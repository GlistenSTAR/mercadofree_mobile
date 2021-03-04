<?php


namespace PublicBundle\Controller;

use AdministracionBundle\Entity\Configuracion;
use AdministracionBundle\Entity\EstadoPedido;
use AdministracionBundle\Entity\OpcionValoracionCalidadProductoPedido;
use AdministracionBundle\Entity\OpcionValoracionTiempoEntregaPedido;
use AdministracionBundle\Entity\Pedido;
use AdministracionBundle\Entity\Producto;
use AdministracionBundle\Entity\ProductoCaracteristicaCategoria;
use AdministracionBundle\Entity\Usuario;
use AdministracionBundle\Entity\UsuarioCampanna;
use AdministracionBundle\Entity\UsuarioObjetivo;
use AdministracionBundle\Entity\Visita;
use AdministracionBundle\Services\RegistroMovimientoService;
use PublicBundle\Services\DevolverPagoPedidoService;
use AdministracionBundle\Repository\PedidoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use PublicBundle\DTO\ValoracionPedidoDTO;
use AdministracionBundle\Util\Fpdf;


class PedidoController extends Controller
{

	public function detalleAction($codPedido){
		$em=$this->getDoctrine()->getManager();

		$pedido=$em->getRepository(Pedido::class)->findOneByCodigo($codPedido);

		if($pedido==null){
			throw new ResourceNotFoundException("El pedido especificado no existe");
		}

		return $this->render("PublicBundle:PanelUsuario:detallePedido.html.twig",array(
			'pedido' => $pedido
		));
	}

    public function detalleCompraAction($codPedido){
        $em=$this->getDoctrine()->getManager();

        /** @var Pedido $pedido */
        $pedido=$em->getRepository(Pedido::class)->findOneByCodigo($codPedido);
        
        /** @var Configuracion $configuracion */
        $configuracion = $em->getRepository(Configuracion::class)->getDefaultConfiguracion();
        $opcionesValoracionCalidadProductoPedido = $em->getRepository(OpcionValoracionCalidadProductoPedido::class)->findAll();
        $opcionesValoracionTiempoEntregaPedido = $em->getRepository(OpcionValoracionTiempoEntregaPedido::class)->findAll();
        
        if($pedido==null){
                throw new ResourceNotFoundException("El pedido especificado no existe");
        }

        return $this->render("PublicBundle:PanelUsuario:detallePedidoCompra.html.twig",array(
                'pedido' => $pedido,
                'opcionesValoracionCalidadProductoPedido' => $opcionesValoracionCalidadProductoPedido,
                'opcionesValoracionTiempoEntregaPedido' => $opcionesValoracionTiempoEntregaPedido,
                'configuracion' => $configuracion
        ));
    }
        
    public function valoracionCompraAction(Request $request) {
        $em=$this->getDoctrine()->getManager();
        
        $codPedido = $request->request->get('codPedido');
        $valorTiempoEntrega = (int) $request->request->get('valorTiempoEntrega');
        $valorCalidadProducto = (int) $request->request->get('valorCalidadProducto');
        $aceptaCompra = $request->request->get('aceptaCompra') == 'Si'? true: false;
        $motivoRechazo = $request->request->get('motivoRechazo');
        
        /** @var Pedido $pedido */
        $pedido = $em->getRepository(Pedido::class)->findOneByCodigo($codPedido);
        if($pedido==null){
                throw new ResourceNotFoundException("El pedido especificado no existe");
        }
        
        $opcionValoracionCalidadProductoPedido = $em->getRepository(OpcionValoracionCalidadProductoPedido::class)->find($valorCalidadProducto);
        $opcionValoracionTiempoEntregaPedido = $em->getRepository(OpcionValoracionTiempoEntregaPedido::class)->find($valorTiempoEntrega);
        
        $pedido->valorarPedido($opcionValoracionCalidadProductoPedido, $opcionValoracionTiempoEntregaPedido, $aceptaCompra, $aceptaCompra? null: $motivoRechazo);
        
        $em->persist($pedido);
        $em->flush();
        
        if($aceptaCompra) {
            
            $this->container->get('liberar_pago_pedido_service')->execute($pedido);
            
        } else {
            
            $estadoSolicitadoDevolucion = $em->getRepository(EstadoPedido::class)->findOneBySlug(EstadoPedido::ESTADO_PEDIDO_SOLICITADO_DEVOLUCION_SLUG);
            
            $pedido->setEstado($estadoSolicitadoDevolucion);

            $em->persist($pedido);

            //Envío de mail y notificación al vendedor de solicitud de reembolso

            $this->get('public.pedido')->stateChangedNotification($estadoSolicitadoDevolucion, $pedido);

            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('public_panel_usuario_compras'));
    }

    public function listarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $valorSearch=$request->query->get('valorSearch');

        $categoriaid=$request->query->get('categoriaid');

        $listaCategoriasFiltro=[];

        $ciudades="";

        $tipoenvio="";

        if ($categoriaid)
        {
            $listaCategoriasFiltro=$this->hacerCategoriasFiltro($categoriaid);

            $ciudades=$em->getRepository('AdministracionBundle:Producto')->findByProductoCiudad($request)->getResult();

            $tipoenvio=$em->getRepository('AdministracionBundle:Producto')->findByProductoTipoEnvio($request)->getResult();
        }

        $categoriasBread=[];

        if ($request->getMethod()=='POST')
        {

            $productos=$em->getRepository('AdministracionBundle:Producto')->findByProductoPublic($request)->getResult();

            $start=count($productos);

            $productosTotal=$em->getRepository('AdministracionBundle:Producto')->findByProductoPublicTotal($request)->getResult();

            $total=count($productosTotal);

            $idCategoria="";

            if ($request->request->get('valorSearch')!="" && $request->request->get('categoriaid')=="")
            {
                $listaCategoriasFiltro="";

                $idCategoria="";

                if ($categoriaid==null)
                {
                    $categoriaid=$request->request->get('categoriaid');
                }

               if($categoriaid=="" || ($request->request->get('valorSearch')!="" && $request->request->get('valorSearch')!=null) )
               {
                   $idCategoria=$this->obtenerCategoriaPadre2($productos[0]->getCategoriaid());

                   $categoriaid=$idCategoria;

                   $listaCategoriasFiltro=$this->hacerCategoriasFiltro($idCategoria);

                   $request->request->set('categoriaid',$idCategoria);

                   $request->query->set('categoriaid',$idCategoria);

                   $categoriasBrfead=$this->breadCrumb($em->getRepository('AdministracionBundle:Categoria')->find($idCategoria));

               }

                $ciudades=$em->getRepository('AdministracionBundle:Producto')->findByProductoCiudad($request)->getResult();

                $tipoenvio=$em->getRepository('AdministracionBundle:Producto')->findByProductoTipoEnvio($request)->getResult();
            }

            $listaProductos=[];

            $usuario=$this->get('utilPublic')->getUsuarioLogueado();

            foreach ($productos as $producto)
            {
                $productoArray=[];

                $productoArray[]=$producto->getId();
                $productoArray[]=$producto->getNombre()!=null?$producto->getNombre():"";
                $productoArray[]=$producto->getPrecio()!=null?$producto->getPrecio():"";
                $productoArray[]=$producto->getCuotaspagar()!=null?$producto->getCuotaspagar():"";
                $productoArray[]=$producto->getCantidad()!=null?$producto->getCantidad():"";
                $productoArray[]=$producto->getCategoriaid()!=null?$producto->getCategoriaid()->getNombre():"";
                $productoArray[]=$producto->getUsuarioid()!=null?$producto->getUsuarioid()->getEmail():"";
                $productoArray[]=$producto->getEstadoProductoid()!=null?$producto->getEstadoProductoid()->getNombre():"";
                $productoArray[]=$producto->getImagenDestacada()!=null?$producto->getImagenDestacada():"";
                $productoArray[]=$producto->hasEnvioDomicilio();
                $productoArray[]=$this->generateUrl('public_anuncio_detalles',array('idProducto'=>$producto->getId()));

                if($usuario!=null && $usuario->isProductoFavorito($producto->getId())){
                    $productoArray[]=true;
                }
                else{
                    $productoArray[]=false;
                }

                $listaProductos[]=$productoArray;

            }

            if (count($categoriasBread)>0)
            {
                $listaCategoriaBread=[];
                foreach ($categoriasBread as $caf)
                {
                    $cafArray=[];

                    $cafArray[]=$caf->getId();

                    $cafArray[]=$caf->getNombre();

                    $listaCategoriaBread[]=$cafArray;
                }

                $categoriasBread=$listaCategoriaBread;
            }
            //unset($_COOKIE['categoriasCookie']);
            //eliminar elementos repetidos array_values(array_unique(arraay));

            if(isset($_COOKIE['categoriasCookie']))
            {
                if($categoriaid==null || $categoriaid=="")
                {
                    $categoriaid=$request->request->get('categoriaid');
                }

                $categoriasCookie=json_decode($_COOKIE['categoriasCookie'],true);

                $categoriasCookie[]=$categoriaid;

                $url=$this->generateUrl('public_homepage');

                setcookie('categoriasCookie',json_encode($categoriasCookie),time()+259200*60, $url);
            }
            else
            {
                if($categoriaid==null || $categoriaid=="")
                {
                    $categoriaid=$request->request->get('categoriaid');
                }

                $categoriasCookie[]=$categoriaid;

                $url=$this->generateUrl('public_homepage');

                setcookie('categoriasCookie',json_encode($categoriasCookie),time()+259200*60,$url);
            }

            return new Response(json_encode(array('productos'=>$listaProductos,'listaCategoriasFiltro'=>$listaCategoriasFiltro,
                "ciudadesProduct"=>$ciudades,'tipoenvio'=>$tipoenvio, 'categoriaid'=>$idCategoria, 'categoriasBread'=>$categoriasBread,'total'=>$total,'start'=>$start)));


        }
        if ($categoriaid!="")
        {
            $categoriasBread=$this->breadCrumb($em->getRepository('AdministracionBundle:Categoria')->find($categoriaid));
        }


        return $this->render('PublicBundle:Anuncio:listado.html.twig',array('valorSearch'=>$valorSearch, 'categoriaid'=>$categoriaid, 'listaCategoriasFiltro'=>$listaCategoriasFiltro,
            "ciudadesProduct"=>$ciudades,'tipoenvio'=>$tipoenvio,'categoriasBread'=>$categoriasBread));
    }

    public function construirSelect($producto)
    {
        $categoria=$producto->getCategoriaid();

        $selects="";
        $cadenaId="";
        $hilo="";

        while ($categoria->getNivel()!=1)
        {
            $categoriaHijas=$categoria->getCategoriaid()->getCategoriaHijas();
            $options="";
            $optionsSelected="";
            foreach ($categoriaHijas as $hija)
            {
                if($categoria->getId()==$hija->getId())
                {
                    $options.='<option value="'.$hija->getId().'">'.$hija->getNombre().'</option>';

                    $cadenaId.=$hija->getId().":".$hija->getNivel()."-";

                    $hilo=$hija->getNombre()." > ".$hilo;

                }
                else
                {
                   $options.='<option value="'.$hija->getId().'">'.$hija->getNombre().'</option>';
                }
            }

            $tagSelect='<div class="col-md-3 cajon" id="'.$categoria->getNivel().'" style="margin-top: 8px">'.
            '<select class="form-control  selectCategorias">'.
            $options.
            '</select>'.
            '</div>';

            $selects=$tagSelect."".$selects;

            $categoria=$categoria->getCategoriaid();
        }

        $resultado[]=$selects;
        $cadenaId= substr($cadenaId,0,-1);
        $resultado[]=$cadenaId;
        $resultado[]=$hilo;



        return $resultado;

    }


    public function idsCategoria($categoria,$ids)
    {
        $ids2="";
        if (count($categoria->getCategoriaHijas())!=0)
        {
            $hijas=$categoria->getCategoriaHijas();

            foreach ($hijas as $hija)
            {
                $ids2=$this->idsCategoria($hija,$ids2);
            }

        }
        else if (count($categoria->getProductos())!=0)
        {
            $ids2.=":".$categoria->getId()."-".($categoria->getCategoriaid()!=null?$categoria->getCategoriaid()->getId():1);

        }

        return $ids.=$ids2;
    }

    public function paginarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $start=$request->request->get('start');

        $total=$request->request->get('total');

        if($start<$total)
        {
            $productos=$em->getRepository('AdministracionBundle:Producto')->findByProductoPublic($request)->getResult();

            $productosTotal=$em->getRepository('AdministracionBundle:Producto')->findByProductoPublicTotal($request)->getResult();

            $listaProductos=[];

            foreach ($productos as $producto)
            {
                $productoArray=[];

                $productoArray[]=$producto->getId();
                $productoArray[]=$producto->getNombre()!=null?$producto->getNombre():"";
                $productoArray[]=$producto->getPrecio()!=null?number_format($producto->getPrecio(),2):"";
                $productoArray[]=$producto->getCuotaspagar()!=null?$producto->getCuotaspagar():"";
                $productoArray[]=$producto->getCantidad()!=null?$producto->getCantidad():"";
                $productoArray[]=$producto->getCategoriaid()!=null?$producto->getCategoriaid()->getNombre():"";
                $productoArray[]=$producto->getUsuarioid()!=null?$producto->getUsuarioid()->getEmail():"";
                $productoArray[]=$producto->getEstadoProductoid()!=null?$producto->getEstadoProductoid()->getNombre():"";
                $productoArray[]=$producto->getImagenDestacada()!=null?$producto->getImagenDestacada():"";
                $productoArray[]=count($producto->getVisitas());
                $productoArray[]=$producto->getInversion()==null?0:$producto->getInversion();
                $productoArray[]=$producto->getActivo();
                $productoArray[]=count($producto->getVisitas());



                $listaProductos[]=$productoArray;
            }

            $start+=count($productos);

            return new Response(json_encode(array('productos'=>$listaProductos,'total'=>count($productosTotal),'start'=>$start)));
        }

    }

    public function panelUsuarioPedidoVentasAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $start=$request->request->get('start');
        $total=$request->request->get('total');
        $idUsuario=$request->request->get('usuarioid');
        $calculador = $this->get('calculadoraDePrecios');
        $listaPedidos=[];
        if($start <= $total)
        {
        	$pedidos=$em->getRepository(Pedido::class)->findPedidoByVendedor($idUsuario, $start);
            //$productos=$em->getRepository('AdministracionBundle:Producto')->findByProductoPanelUsuario($request)->getResult();
            /** @var Pedido $pedido */
            foreach ($pedidos as $pedido)
            {
                $precio = $calculador->CalcularPrecio($pedido);
                $precio += $pedido->getCostoEnvio();
				$listaPedidos[] = array(
					'id' => $pedido->getId(),
					'fecha' => date_format($pedido->getFecha(),'d/m/Y'),
					'monto' => number_format($precio, 2,',','.'),
					'estado' => $pedido->getEstado()->getNombre(),
					'codigo' => $pedido->getCodigo(),
					'url' => $this->generateUrl('public_panel_usuario_ventas_detalle',array('codPedido' => $pedido->getCodigo())),
                                        'tiene_valoracion' => (bool) $pedido->getValoracionPedido(),
                                        'valoracion_pedido' => $pedido->getValoracionPedido()? new ValoracionPedidoDTO($pedido->getValoracionPedido()): null,
                                        'puede_imprimir_pdf' => $pedido->puedeImprimirPDF()
				);
            }

            $start+=count($pedidos);
        }
        return new JsonResponse(array('pedidos'=>$listaPedidos,'total'=>$total,'start'=>$start));
    }
    
    public function imprimirEtiquetaAction($codigo,int $notificar) {
        $em=$this->getDoctrine()->getManager();
        
        /** @var PedidoRepository $pedidoRepository */
        $pedidoRepository = $em->getRepository(Pedido::class);
        
        /** @var Pedido $pedido */
        $pedido=$pedidoRepository->findOneByCodigo($codigo);
        
        $vendedor=$this->getUser();
        
        if(!$pedido || $pedido->getVendedor() != $vendedor) {
            return $this->redirect($this->generateUrl('public_pedido_paginadopedidoventas'));
        }
        
        $pdf = $this->generarEtiquetaPedidoPdf($pedido);
        
        $comprador = $pedido->getUsuario();
        
        if($notificar) {//var_dump('notifica'); die();
            //Envío de notificación de pedido listo para envío a comprador
            $emailTemp=$this->render('PublicBundle:Email:mensaje_email_notificacion_pedido_listo_para_envio.html.twig', array(
                                        'pedido' => $pedido
                                ));

            $this->get('email')->sendMail(
                'noreply@mercadofree.com',//From
                $comprador->getEmail(), //To
                "[MercadoFree] Pedido preparado", //Asunto
                $emailTemp->getContent() //Body
            );
        }
        
        $pdf->Output('D','etiqueta pedido - '.$pedido->getCodigo().'.pdf');
                
        return $pdf;
    }
    
    private function generarEtiquetaPedidoPdf(Pedido $pedido) {
        
        $x=10;
        $y=10;
        $lineHeight=5;
        $xDireccionData = 42;
        
        $pdf = new Fpdf();
        $pdf->AddFont('helvetica','','helvetica.php');
        $pdf->AddPage();
        $pdf->SetFont('helvetica','',14);
        /** RECUADRO DE LOGO Y CÓDIGO DE PEDIDO */
        $pdf->SetXY(7, 7);
        $pdf->Cell(190, 25, '', 1);
        /** RECUADRO DE DATOS DE ENVÍO */
        $pdf->SetXY(7, 32);
        $pdf->Cell(190, 28, '', 1);
        /** RECUADRO DE DATOS DEL PRODUCTO */
        $pdf->SetXY(7, 60);
        $pdf->Cell(190, 32, '', 1);
        
        $pdf->Image($this->container->getParameter('directorio_imagenes_public').'logo.png', 10, 10, 60);
        $pdf->Text(80, 15,'Código Pedido: '.$pedido->getCodigo());
        $pdf->SetFont('helvetica','',11);
        
        /** DATOS DE LA DIRECCIÓN DE ENTREGA */
        $direccionEnvio = $pedido->getDireccionEnvio();
        if(!$direccionEnvio) {
            throw new \Exception('El pedido' . $pedido->getCodigo() . ' no tiene definida la dirección de envío');
        }
        
        $stringDireccionEnvio1 = $direccionEnvio->getCalle() . ' ' . $direccionEnvio->getNumero();
        $stringDireccionEnvio2 = $direccionEnvio->getOtrosDatos();
        $stringDireccionEnvio3 = $direccionEnvio->getEntreCalle();
        
        $ciudad = $direccionEnvio->getCiudad();
        $stringDireccionEnvio4 = $ciudad->getCiudadNombre(). ', C.P. ' . $ciudad->getCodigoPostal();
        
        $x = 80;
        $y += 30;
        $pdf->Text($x, $y, 'Dirección de entrega: ');
        $pdf->Text($x+$xDireccionData, $y, $stringDireccionEnvio1);
        if($stringDireccionEnvio2) {
            $y += $lineHeight;
            $pdf->Text($x+$xDireccionData,$y, $stringDireccionEnvio2);
        }
        if($stringDireccionEnvio3) {
            $y += $lineHeight;
            $pdf->Text($x+$xDireccionData,$y, 'entre las calles ' . $stringDireccionEnvio3);
        }
        if($stringDireccionEnvio4) {
            $y += $lineHeight;
            $pdf->Text($x+$xDireccionData,$y, $stringDireccionEnvio4);
        }
        
        /** DATOS DEL COMPRADOR **/
        $x=10;
        $y=40;
        $pdf->Text($x,$y, 'Destinatario: ');
        
        $comprador = $pedido->getUsuario()->getCliente();
        
        $pdf->Text($x+24, $y, $comprador->getApellidos() . ', ' . $comprador->getNombre());
        
        $telefono = $pedido->getUsuario()->getTelefono();
        
        if($telefono) {
            $y += $lineHeight;
            $pdf->Text($x, $y, 'Telefono: ');
            $pdf->Text($x+24, $y, $telefono);
        }
        
        /** DATOS DEL PRODUCTO **/
        $x=10;
        $y=62;
        $producto=$pedido->getProducto();
        $detallePedido=$pedido->getDetalle();
        $imagenDestacadaProducto = $producto->getImagenDestacada();
        if($imagenDestacadaProducto && file_exists($this->container->getParameter('uploads_images_productos').$imagenDestacadaProducto)) {
            $imageSize = getimagesize($this->container->getParameter('uploads_images_productos').$imagenDestacadaProducto);
            
            if($imageSize[1] > $imageSize[0]) {
                $pdf->Image($this->container->getParameter('uploads_images_productos').$imagenDestacadaProducto, $x, $y, 0, 28);
            } else {
                $pdf->Image($this->container->getParameter('uploads_images_productos').$imagenDestacadaProducto, $x, $y, 30);
            }
        }
        $x+=35;
        $y+=5;
        $pdf->Text($x, $y ,'Producto: '.$detallePedido->getNombre());
        $y+=$lineHeight;
        $pdf->Text($x, $y ,'Cantidad: '.$pedido->getCant());
        
        
        //return $pdf->Output('','etiqueta pedido - '.$pedido->getCodigo().'.pdf', true); //PARA ABRIR PDF EN UNA NUEVA VENTANA DEL NAVEGADOR
        return $pdf; //PARA DESCARGAR PDF
    }

    public function panelUsuarioPedidoComprasAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $start=$request->request->get('start');

        $total=$request->request->get('total');

        $idUsuario=$request->request->get('usuarioid');

        $listaPedidos=[];

        if($start<$total)
        {
            $pedidos=$em->getRepository(Pedido::class)->findPedidoByComprador($idUsuario, $start);
            //$productos=$em->getRepository('AdministracionBundle:Producto')->findByProductoPanelUsuario($request)->getResult();

            foreach ($pedidos as $pedido)
            {
                $listaPedidos[]=array(
                    'id' => $pedido->getId(),
                    'fecha' => date_format($pedido->getFecha(),'d/m/Y'),
                    'monto' => number_format($pedido->getTotal(),2,',','.'),
                    'estado' => $pedido->getEstado()->getNombre(),
                    'codigo' => $pedido->getCodigo(),
                    'url' => $this->generateUrl('public_panel_usuario_ventas_detalle',array('codPedido' => $pedido->getCodigo()))
                );
            }

            $start+=count($pedidos);

        }

        return new Response(json_encode(array('pedidos'=>$listaPedidos,'total'=>$total,'start'=>$start)));

    }

    public function cambiarEstadoAction(Request $request)
    {
		$idPedido=$request->get('idPedido');
                $modificadoPorAdmin = $request->get('modificadoPorAdmin');

		$em=$this->getDoctrine()->getManager();

                /** @var Pedido $pedido */
		$pedido=$em->find(Pedido::class,$idPedido);

		if($pedido==null){
			return new JsonResponse(array(false,'El pedido especificado no existe'));
		}

		//obtener estado del pedido y posibles proximos estados
		$estadoActual=array(
			'nombre' => $pedido->getEstado()->getNombre(),
			'slug' => $pedido->getEstado()->getSlug()
		);
		$next=$pedido->getNextState(true);

		$nextStates=array();

		if(count($next)>0){
			foreach ($next as $n){
				$nuevoEstado=$em->getRepository(EstadoPedido::class)->findOneBySlug($n);
				$nextStates[]=array(
					'nombre' => $nuevoEstado->getNombre(),
					'slug' => $nuevoEstado->getSlug()
				);
			}
		}

		if($request->getMethod()=='POST'){
			$nuevoEstado=$request->request->get('nuevoEstadoPedido');
                        /** @var EstadoPedido $nuevoEstado */
			$nuevoEstado=$em->getRepository(EstadoPedido::class)->findOneBySlug($nuevoEstado);
                        
                        if($pedido->devuelto() && $nuevoEstado->estadoCerrado()) {
                            //Se efectua la devolución del monto al cliente
                            /** @var DevolverPagoPedidoService $devolverPagoPedidoService */
                            $devolverPagoPedidoService = $this->container->get('devolver_pago_pedido_service');
                            $devolverPagoPedidoService->execute($pedido);
                        } else {
                            $pedido->setEstado($nuevoEstado, $modificadoPorAdmin);
                        }

			$em->persist($pedido);

            // Enviar notificación en caso de ser necesario
			$this->get('public.pedido')->stateChangedNotification($nuevoEstado, $pedido);

            $em->flush();

			return new JsonResponse(array(
				true,
				'nombre_estado' => $nuevoEstado->getNombre()
			));

		}

		return new JsonResponse(array(
			true,
			'estadoActual' => $estadoActual,
			'next' => $nextStates
		));

    }

    public function eliminarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $idPedido=$request->request->get('idPedido');
        $user = $this->getUser();

        /** @var Pedido $pedido */
        $pedido=$em->find(Pedido::class, $idPedido);

        if($pedido==null){
                return new JsonResponse(array(false,'El pedido especificado no existe'));
        }

        if($pedido->getEstado()->getSlug()!='cerrado'){
                return new JsonResponse(array(false,'El pedido debe estar CERRADO para poder eliminarse'));
        }

        if($pedido->getVendedor() == $user) {
            $pedido->setBorradoVendedor(true);
        } else {
            /** Verificamos que el comprador es el que desea realmente eliminar el productos */
            if($pedido->getUsuario() == $user) {
                $pedido->setBorradoComprador(true);
            }
        }
        
        $em->persist($pedido);
        $em->flush();

        return new JsonResponse(array(true));
    }

}
