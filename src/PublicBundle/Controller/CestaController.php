<?php

namespace PublicBundle\Controller;

use AdministracionBundle\Entity\Cesta;
use AdministracionBundle\Entity\DireccionEnvio;
use AdministracionBundle\Entity\EstadoPedido;
use AdministracionBundle\Entity\Factura;
use AdministracionBundle\Entity\MetodoPago;
use AdministracionBundle\Entity\MovimientoCuenta;
use AdministracionBundle\Entity\Notificacion;
use AdministracionBundle\Entity\Pedido;
use AdministracionBundle\Entity\Producto;
use AdministracionBundle\Entity\TipoEnvio;
use AdministracionBundle\Entity\Usuario;
use PublicBundle\Form\FacturaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

class CestaController extends Controller
{

    public function listarAction(Request $request){

	    $em=$this->getDoctrine()->getManager();

        //Obtener el usuario logueado y verificar si tiene productos en la cesta

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        $total=$this->getTotalCesta();

        if($request->getMethod()=='POST' && $usuario!=null){
        	//Crear pedido

	        $idPedido=$request->request->get('idPedido');

	        $pedido=null;
	        if($idPedido!=null && $idPedido!=""){
	        	$pedido=$em->find(Pedido::class,$idPedido);
	        }

	        if($pedido==null){
		        $pedido=new Pedido();

		        $pedido->setUsuario($usuario);
		        $pedido->setEstado($em->getRepository(EstadoPedido::class)->findOneBySlug('pendiente'));
		        $pedido->setFecha(new \DateTime());
		        $pedido->setSubtotal($total);
		        $pedido->setCodigo('P'.rand(10000000,99999999));

		        try{
			        $em->persist($pedido);
			        $em->flush();
		        }
		        catch (\Exception $e){
			        return new JsonResponse(array(false,$e->getMessage()));
		        }
	        }
	        else{
	        	//editando un pedido

		        if($pedido->getSubtotal()!=$total){
		        	$pedido->setSubtotal($total);

		        	$em->persist($pedido);

		        	$em->flush();
		        }
	        }

	        return new JsonResponse(array(true));

        }
        else{
	        if($usuario!=null && count($usuario->getCestas())>0){

	        	//Verificar si tiene pedido pendiente

				$pedido=$em->getRepository(Pedido::class)->findOneBy(array(
					'usuario' => $usuario->getId(),
					'estado' => 1
				));

		        return $this->render('PublicBundle:Cesta:listado.html.twig',array(
			        'cestas' => $usuario->getCestas(),
			        'total' => $total,
			        'idPedido' => ($pedido!=null?$pedido->getId():null)
		        ));
	        }
	        else{
		        //No tiene productos, mostrar notificacion

		        return $this->render('PublicBundle:Templates:notificaciones.html.twig',array(
			        'titulo' => 'No tienes productos en tu cesta',
			        'texto' => 'Al parecer estás intentando acceder a tu carrito de compras pero no tienes productos, agrega primero
                un producto y luego vuelve por aquí.',
			        'icon' => 'ion-alert-circled',
			        'type' => 'error',
			        'button' => $this->generateUrl('public_homepage')
		        ));
	        }
        }
    }

    public function eliminarProductoAction(Request $request){
        if($request->getMethod()=='POST'){
            $idCesta=$request->request->get('idCesta');

            $result=$this->validacionesCesta($idCesta);

            if($result[0]){
                $em=$this->getDoctrine()->getManager();

                $cesta=$em->getRepository('AdministracionBundle:Cesta')->find($idCesta);

                $usuario=$this->get('utilPublic')->getUsuarioLogueado();

                //Eliminar item del carrito

                //salvar el id del producto antes de eliminar para luego tenerlo como referencia para actualizar el preview

                $idProd=$cesta->getProducto()->getId();

                $em->remove($cesta);

                $em->flush();

                //recalcular total

                $total=$this->getTotalCesta();

                return new Response(json_encode(array(
                    true, //response status
                    $total, //monto total actualizado
                    count($usuario->getCestas()), //cantidad de prod cesta actualizado
                    $idProd //id producto eliminado
                )));
            }
            else{
                return new Response(json_encode($result));
            }
        }
        else{
            return new Response(json_encode(array(false,'Peticion Incorrecta')));
        }
    }

    public function modificarCantidadAction(Request $request){
        if($request->getMethod()=='POST'){
            $idCesta=$request->request->get('idCesta');

            $result=$this->validacionesCesta($idCesta);

            if($result[0]){
                $em=$this->getDoctrine()->getManager();

                $cesta=$em->getRepository('AdministracionBundle:Cesta')->find($idCesta);

                $cesta->setCantidad($request->request->get('cant'));

                $em->persist($cesta);
                $em->flush();

                //calcular nuevo precio

                $newPrice=$cesta->getProducto()->getPrecioOferta()*$cesta->getCantidad();

                //recalcular total

                $total=$this->getTotalCesta();

                return new Response(json_encode(array(
                    true, //Status response
                    $newPrice, //Nuevo precio de la linea del prod segun la nueva cantidad
                    $total, //Nuevo precio total
                    $cesta->getProducto()->getId() //Id producto
                )));
            }
            else{
                return new Response(json_encode($result));
            }
        }
        else{
            return new Response(json_encode(array(false,'Peticion Incorrecta')));
        }
    }

    private function validacionesCesta($idCesta){
        if($idCesta==null || $idCesta==''){
            return array(false,'Debe especificar un producto para eliminar de su carrito');
        }

        $em=$this->getDoctrine()->getManager();

        $cesta=$em->getRepository('AdministracionBundle:Cesta')->find($idCesta);

        if($cesta==null){
            return array(false,'El producto especificado no existe en su carrito');
        }

        //Verificar que la cesta pertenece al usuario logueado

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        if($usuario!=null && $cesta->getUsuario()->getId()!=$usuario->getId()){
            return array(false,'El producto especificado no pertenece a su carrito');
        }

        return array(true);
    }

    private function getTotalCesta(){
        /** @var Usuario $usuario */
        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        $total=0;

        if($usuario!=null && count($usuario->getCestas())>0) {

            //obtener total
            foreach ($usuario->getCestas() as $cesta) {
                $total = $total + ($cesta->getProducto()->getPrecioOferta() * $cesta->getCantidad());
            }
        }

        return $total;
    }

    public function configurarEnvioAction(Request $request){

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        $pedido=null;

		$buyNow=false;
		$saldo = $usuario->getSaldo();

        $tipoEnvios=array();

        if($request->get('buyNow')!=null && $request->get('idProducto')!=null){
	        $pedido=$this->getPedido($request->get('idProducto'));
	        $buyNow=true;
        }
        else{
	        $pedido=$this->getPedido();
        }

        if($pedido!=null){
	        $enviosGratis=false;
	        $costoEnvio=0;

	        if(!$buyNow){
		        $cesta=$usuario->getCestas();
		        foreach ($cesta as $cest){
			        $tipoEnvios=$cest->getProducto()->getUsuarioid()->getTipoEnvios();

			        foreach ($tipoEnvios as $te){
				        if($te->getSlug()=='envio-gratis'){
					        $enviosGratis++;
				        }
			        }
		        }

		        if($enviosGratis==count($cesta)){
			        $enviosGratis=true;
		        }
		        else{
			        $enviosGratis=false;
			        $costoEnvio=$this->get('utilPublic')->getCostoEnvio($usuario, $request->get('idProducto'));
		        }
	        }
	        else{
		        $tipoEnvios=$pedido->getProducto()->getUsuarioid()->getTipoEnvios();
		        foreach ($tipoEnvios as $te){
			        if($te->getSlug()=='envio-gratis'){
				        $enviosGratis=true;
				        break;
			        }
		        }

		        if(count($tipoEnvios)==0){
		        	$costoEnvio=0;
		        }
		        else if(!$enviosGratis){
			        $costoEnvio=$this->get('utilPublic')->getCostoEnvio($usuario, $request->get('idProducto'));
		        }
	        }

	        $em=$this->getDoctrine()->getManager();

	        if($request->getMethod()=='POST'){
	        	//Actualizar pedido

		        $envioSelected=$request->request->get('envioSelected');

		        //Crear direccion de envio y asociar a pedido

		        $direccionCompra=$usuario->getDireccionCompra();

		        $direccionEnvio=null;

		        if($pedido->getDireccionEnvio()!=null){
		        	$direccionEnvio=$pedido->getDireccionEnvio();
		        }
		        else{
			        $direccionEnvio=new DireccionEnvio();
		        }


		        $direccionEnvio->setCalle($direccionCompra->getCalle());
		        $direccionEnvio->setCiudad($direccionCompra->getCiudad());
		        $direccionEnvio->setEntreCalle($direccionCompra->getEntreCalle());
		        $direccionEnvio->setNumero($direccionCompra->getNumero());
		        $direccionEnvio->setProvincia($direccionCompra->getProvincia());
		        $direccionEnvio->setOtrosDatos($direccionCompra->getOtrosDatos());

		        $errorMessage="";

		        try{
			        $em->persist($direccionEnvio);
			        $em->flush();
		        }
		        catch (\Exception $e){
		        	$errorMessage=$e->getMessage();
		        }


		        if($enviosGratis){
			        $pedido->setTipoEnvio($em->getRepository(TipoEnvio::class)->findOneBySlug('envio-gratis'));
		        }
		        else if($envioSelected!=null && $envioSelected!=''){ //Obtener tipo de envio seleccionado
					$tipoEnvioSelected=$em->getRepository(TipoEnvio::class)->findOneBySlug($envioSelected);

					if($tipoEnvioSelected!=null){
						$pedido->setTipoEnvio($tipoEnvioSelected);

						if($envioSelected=='recogida-domicilio-vendedor'){
							$costoEnvio=0;
						}
					}
					else{
						$pedido->setTipoEnvio($em->getRepository(TipoEnvio::class)->findOneBySlug('recogida-domicilio-vendedor'));
						$costoEnvio=0;
					}
		        }
		        else{
			        $pedido->setTipoEnvio($em->getRepository(TipoEnvio::class)->findOneBySlug('recogida-domicilio-vendedor'));
			        $costoEnvio=0;
		        }

		        $pedido->setCostoEnvio($costoEnvio);

		        $pedido->setDireccionEnvio($direccionEnvio);

		        try{
			        $em->persist($pedido);

			        $em->flush();
		        }
		        catch (\Exception $e){
			        $errorMessage=$e->getMessage();
		        }

		        if($errorMessage!=""){
		        	return new JsonResponse(array(false,$errorMessage));
		        }
		        else{
			        return new JsonResponse(array(true));
		        }

	        }

	        $provincias = $em->getRepository('AdministracionBundle:Provincia')->findAll();



	        return $this->render('PublicBundle:Cesta:envio.html.twig',array(
		        'envioGratis' => $enviosGratis,
		        'costoEnvio' => $costoEnvio,
		        'subtotal' => $pedido->getSubtotal(),
		        'buyNow' => $buyNow,
		        'pedido' => $pedido,
		        'tipoEnvios' => $tipoEnvios,
				'provincias' => $provincias
	        ));
        }
        else{
        	//redireccionar para la pagina de notificacion de error
	        return $this->render('PublicBundle:Templates:notificaciones.html.twig',array(
		        'titulo' => 'No tienes un pedido PENDIENTE',
		        'texto' => 'Solo puedes acceder a esta sección si tienes un pedido PENDIENTE, pero al parecer no lo tienes, empieza agregando productos a tu carrito para crear un nuevo pedido.',
		        'icon' => 'ion-alert-circled',
		        'type' => 'error',
		        'button' => $this->generateUrl('public_homepage')
	        ));
        }
    }

    public function metodoPagoAction(Request $request){

    	$usuario=$this->getUser();
        $buyNow=$request->get('buyNow');
        $idProducto=$request->get('idProducto');
        $saldo = $usuario->getSaldo();
        $pedido=null;

        if($idProducto!=null && $idProducto!=""){
            $pedido=$this->getPedido($idProducto);
        }
        else{
                $pedido=$this->getPedido();
        }

        $total = $pedido->getTotal();
//    	$factura=$pedido->getFactura();
//
//
//
//		if($factura==null){
//			$factura=new Factura();
//
//			//Rellenar factura con datos que ya se tienen
//
//			$factura->setNombre($usuario->getClienteid()->getNombre());
//			$factura->setApellidos($usuario->getClienteid()->getApellidos());
//			$factura->setCalle($pedido->getDireccionEnvio()->getCalle());
//			$factura->setNumero($pedido->getDireccionEnvio()->getNumero());
//			$factura->setCodPostal($pedido->getDireccionEnvio()->getCiudad()->getCodigoPostal());
//			$factura->setLocalidad($pedido->getDireccionEnvio()->getCiudad()->getCiudadNombre());
//		}
//
//    	$formFactura=$this->createForm(new FacturaType(), $factura);
//
//    	if($factura->getId()==null){
////		    $formFactura->get('metodoPago')->setData('tarjeta_credito_mercado_pago');
//		    $formFactura->get('metodoPago')->setData('transferencia_bancaria');
//	    }

    if($request->getMethod()=='POST'){
        //$formFactura->handleRequest($request);

//    		if($formFactura->isSubmitted() && $formFactura->isValid()){
//    			$option=$formFactura->get('metodoPago')->getData();
//
//    			$em=$this->getDoctrine()->getManager();
//    			$metodoPago=$em->getRepository(MetodoPago::class)->findOneBySlug($option);
//
//    			$pedido->setMetodoPago($metodoPago);
//
//    			if($option=='rapipago' || $option=='pago_facil'){
//    				$factura->generateRef($usuario->getClienteid()->getFirstLetterName());
//    				$factura->setFecha(new \DateTime());
//    				$pedido->setFactura($factura);
//			    }
//
//			    try{
//				    $em->persist($pedido);
//				    $em->flush();
//			    }
//			    catch (\Exception $e){
//    			    return new JsonResponse(array(false,$e->getMessage()));
//			    }
//
//			    return new JsonResponse(array(true));
//		    }
//		    else{
//			    return new JsonResponse(array(false,'Ocurrió un error de validación en su formulario'));
//		    }

            $metodoPagoSelected = $request->request->get('metodoPagoSelected');

            $em=$this->getDoctrine()->getManager();

            if($metodoPagoSelected!=null && $metodoPagoSelected!=''){
                try {
                    $pedido->setMetodoPago($em->getRepository(MetodoPago::class)->findOneBySlug($metodoPagoSelected));
                } catch (\Exception $e) {
                    $pedido->setMetodoPago($em->getRepository(MetodoPago::class)->findOneBySlug('pago-entrega'));
                }
                $em->persist($pedido);
                $em->flush();

            }

            return new JsonResponse(array(true));
        }


        return $this->render('PublicBundle:Cesta:metodoPago.html.twig',array(
        	//'formFactura' => $formFactura->createView(),
	        'metodoPago' => ($pedido->getMetodoPago()!=null?$pedido->getMetodoPago()->getSlug():''),
	        'buyNow' => $buyNow,
	        'idProducto' => $idProducto,
	        'codPedido' => $pedido->getCodigo(),
            'pedido' => $pedido,
            'saldo' => $saldo,
            'saldoConta' => ($saldo >= $total),
            'vendedor' => (!is_null($pedido->getProducto())) ? $pedido->getProducto()->getUsuarioid() : null
        ));
    }

    public function resumenAction(Request $request){
    	$usuario=$this->getUser();

        $buyNow=$request->get('buyNow');

        $idProducto=$request->get('idProducto');

        $pedido=null;

        $iban="";

        if($idProducto!=null && $idProducto!=""){
                $pedido=$this->getPedido($idProducto);

                $iban=$pedido->getProducto()->getUsuarioid()->getIban();
        }
        else{
                $pedido=$this->getPedido();
        }

    	if($pedido==null){
            return $this->render('PublicBundle:Templates:notificaciones.html.twig',array(
                    'titulo' => 'No tienes un pedido PENDIENTE',
                    'texto' => 'No tienes ningún pedido PENDIENTE que podamos mostrarte en esta página',
                    'icon' => 'ion-alert-circled',
                    'type' => 'error',
                    'button' => $this->generateUrl('public_homepage')
            ));
        }

        return $this->render('PublicBundle:Cesta:resumen.html.twig',array(
        	'pedido' => $pedido,
                'usuario' => $usuario,
	        'cestas' => $usuario->getCestas(),
	        'buyNow' => $buyNow,
	        'idProducto' => $idProducto,
	        'iban' => $iban
        ));
    }

    public function comprarAhoraAction(Request $request){

    	$idProducto=$request->request->get('idProducto');

    	$cant=$request->request->get('cant');

		//buscar si existe algun pedido para este producto

	    $pedido=null;

	    $pedido=$this->getPedido($idProducto);

	    $em=$this->getDoctrine()->getManager();

	    $producto=$em->find(Producto::class, $idProducto);

	    $subtotal=$cant*$producto->getPrecio();

	    if($pedido==null){
	    	//crear pedido

		    $pedido=new Pedido();

		    $pedido->setFecha(new \DateTime());
		    $pedido->setEstado($em->getRepository(EstadoPedido::class)->findOneBySlug('pendiente'));
		    $pedido->setUsuario($this->getUser());
		    $pedido->setCant($cant);
		    $pedido->setCodigo('P'.rand(10000000,99999999));
		    $pedido->setSubtotal($subtotal);
		    $pedido->setProducto($producto);

		    $em->persist($pedido);
	    }

	    else{
	    	//incrementar la cantidad anterior a la cantidad actual, y recalcular subtotal

		    $cantAnterior=$pedido->getCant();

		    $pedido->setCant($cantAnterior+$cant);

		    $subtotal=$producto->getPrecio()*($cantAnterior+$cant);

		    $pedido->setSubtotal($subtotal);

		    $em->persist($pedido);
	    }

	    $em->flush();

	    return new JsonResponse(array(true, $pedido->getId()));

    }

    public function adicionarProductoAction(Request $request){
        if($request->getMethod()=='POST'){
            $idProducto=$request->request->get('idProducto');
            if($idProducto==null || $idProducto==""){
                return new Response(json_encode(array(false,'Debes seleccionar un producto para añadirlo a la cesta')));
            }

            //verificar si el usuario ya tiene ese producto en la cesta

            $usuario=$this->get('utilPublic')->getUsuarioLogueado();

            if($usuario!=null && count($usuario->getCestas())>0){
                foreach ($usuario->getCestas() as $cesta){
                    if($cesta->getProducto()->getId()==$idProducto){
                        return new Response(json_encode(array(false,'Ya ese producto se encuentra en el carrito')));
                    }
                }
            }

            //obtener el producto y añadirlo a la cesta

            $em=$this->getDoctrine()->getManager();

            $producto=$em->getRepository('AdministracionBundle:Producto')->find($idProducto);

            if($producto==null){
                return new Response(json_encode(array(false,'El producto indicado no existe')));
            }
            else{
                //crear obj cesta

                $cesta=new Cesta();

                $cesta->setProducto($producto);
                $cesta->setUsuario($usuario);
                $cesta->setCantidad($request->request->get('cantidad'));

                $em->persist($cesta);
                $em->flush();

                //Crear array con datos del producto para el modal de confirmacion

                //Verificar si ese producto esta en oferta actualmente

                $precioOferta=0;
                if($producto->getOfertaActiva()!=null){
                    $precioOferta=$producto->getPrecioOferta();
                }


                $prodview=array(
                    'imagen' => $producto->getImagenDestacada(),
                    'titulo' => $producto->getNombre(),
                    'precio' => $producto->getPrecio(),
                    'cantidad' => $cesta->getCantidad(),
                    'precioOferta' => $precioOferta
                );

                $cestas=$em->getRepository('AdministracionBundle:Cesta')->findBy(array(
                    'usuario' => $usuario->getId()
                ));

                return new Response(json_encode(array(true,$prodview, ($cestas!=null?count($cestas):0) )));
            }
        }
        else{
            return new Response(json_encode(array(false,'Peticion Incorrecta')));
        }
    }

    public function previewAction(){
        //obtener usuario logueado

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        $productosView=array();

        //verificar si tiene productos en la cesta

        if(count($usuario->getCestas())>0){
            // crear array para la vista

            foreach ($usuario->getCestas() as $cesta){
                $productosView[]=array(
                    'cantidad' => $cesta->getCantidad(),
                    'nombre' => $cesta->getProducto()->getNombre(),
                    'precio' => $cesta->getProducto()->getPrecioOferta(),
                    'imagen' => $cesta->getProducto()->getImagenDestacada(),
                    'id' => $cesta->getProducto()->getId()
                );
            }
        }

        return $this->render('PublicBundle:Templates:previewCesta.html.twig',array(
            'productosView' => $productosView
        ));
    }

    public function confirmarPedidoAction(Request $request){
    	$usuario=$this->getUser();

        $buyNow=$request->get('buyNow');

        $idProducto=$request->get('idProducto');

        $pedido=null;

        if($idProducto!=null && $idProducto!=""){
                $pedido=$this->getPedido($idProducto);
        }
        else{
                $pedido=$this->getPedido();
        }

    	if($pedido!=null){
            
            /** genera detalle del producto */
            $pedido->generarDetalleCompra();
            
            $em=$this->getDoctrine()->getManager();

            $configuracion = $em->getRepository('AdministracionBundle:Configuracion')->find(1);

            $emailAdmin = $configuracion->getEmailAdministrador();

            $vendedor = $pedido->getVendedor();

            $from = $emailAdmin;

            
            // Enviar email al vendedor con notificacion y detalles del pedido

            $emailTpl = $this->renderView('@Public/Email/mensaje_email_notificacion_pedido.html.twig',array(
                'pedido' => $pedido,
                'user_firstname' => ($vendedor->getCliente() != null) ?
                    $vendedor->getCliente()->getNombre() : $vendedor->getEmail()
            ));

            $to = $vendedor->getEmail();

            $this->get('utilpublic')->sendMail($from, $to, "Tienes un nuevo Pedido", $emailTpl);

            // Enviar email al comprador con detalles del pedido

            $emailTpl = $this->renderView('@Public/Email/mensaje_email_notificacion_pedido_comprador.html.twig',array(
                'pedido' => $pedido,
                'user_firstname' => ($usuario->getCliente() != null) ?
                    $usuario->getCliente()->getNombre() : $usuario->getEmail()
            ));

            $to = $usuario->getEmail();

            $this->get('utilpublic')->sendMail($from, $to, "Pedido realizado", $emailTpl);

            // Enviar notificación al vendedor del nuevo pedido

            $this->get('notification')->send(
                $vendedor,
                Notificacion::NOTIFICATION_TYPE_PRODUCT_SALE,
                array(
                    Notificacion::NOTIFICATION_PARAM_ORDER_CODE => $pedido->getCodigo(),
                    Notificacion::NOTIFICATION_PARAM_PROD_NAME => $pedido->getProducto()->getNombre()
                )
            );

            //eliminar cestas del usuario y asociarlas al pedido

            if(!$buyNow){

                $cestas=$em->getRepository(Cesta::class)->findBy(array('usuario' => $usuario->getId()));

                foreach ($cestas as $cesta){
                        $cest=new Cesta();
                        $cest->setCantidad($cesta->getCantidad());
                        $cest->setProducto($cesta->getProducto());
                        $cest->setPedido($pedido);

                        $em->persist($cest);

                        $em->remove($cesta);
                }
            }

            $em->flush();

            //Verificar metodo de pago

            if($pedido->getMetodoPago()->getSlug()==MetodoPago::RAPIPAGO || $pedido->getMetodoPago()->getSlug()==MetodoPago::PAGOFACIL){
                $this->registrarEstadoPedidoPendientePago($pedido);
                //TODO: Enviar factura a punto de pago

                return $this->render('PublicBundle:Cesta:referenciaFactura.html.twig',array(
                        'pedido' => $pedido
                ));
            } else if($pedido->getMetodoPago()->getSlug()==MetodoPago::TRANSFERENCIA_BANCARIA) {
                $this->registrarEstadoPedidoPendientePago($pedido);
                return $this->render('PublicBundle:Cesta:confirmacionTransferenciaBancaria.html.twig',array(
                        'pedido' => $pedido
                ));
            } else if($pedido->getMetodoPago()->getSlug()==MetodoPago::PAGO_ENTREGA) {
                $this->registrarEstadoPedidoPendientePago($pedido);
                return $this->render('PublicBundle:Cesta:datosVendedorEntrega.html.twig',array(
                        'pedido' => $pedido
                ));
            } else if($pedido->getMetodoPago()->getSlug()==MetodoPago::PAYPAL) {
                $this->registrarEstadoPedidoPendientePago($pedido);
                $paypalService = $this->container->get('paypal');
                $data = [
                    'email' => $usuario->getEmailPaypal(),
                    'monto' => $pedido->getTotal(),
                    'anid' => "25011988445", //TODO: buscar que es esto
                    'return_url' => 'public_cesta_pago_paypal'
                ];
                if ($paypalService->pagar($data)) {
                    $url = $paypalService->getUrlRetorno();

                    return $this->redirect($url);
                }

                $request->getSession()
                        ->getFlashBag()
                        ->add('danger', 'Transacción fallida, intente nuevamente mas tarde o entre en contacto con nosotros.');
            } else if($pedido->getMetodoPago()->getSlug()=='pago-saldo') {
                return $this->registrarPagoSaldo($usuario, $pedido, $request);
            }
        }
        else{
                return $this->render('PublicBundle:Templates:notificaciones.html.twig',array(
                        'titulo' => 'No tienes un pedido PENDIENTE',
                        'texto' => 'No tienes ningún pedido PENDIENTE que podamos mostrarte en esta página',
                        'icon' => 'ion-alert-circled',
                        'type' => 'error',
                        'button' => $this->generateUrl('public_homepage')
                ));
        }
    }
    
    private function registrarEstadoPedidoPendientePago(Pedido $pedido) {
        $em=$this->getDoctrine()->getManager();

        $pedido->setEstado($em->getRepository(EstadoPedido::class)->findOneBySlug(EstadoPedido::ESTADO_PEDIDO_PENDIENTE_PAGO_SLUG));

        $em->persist($pedido);
        $em->flush();
    }
    
    private function registrarEstadoPedidoPagado(Pedido $pedido) {
        $em=$this->getDoctrine()->getManager();

        $pedido->setEstado($em->getRepository(EstadoPedido::class)->findOneBySlug(EstadoPedido::ESTADO_PEDIDO_PAGADO_SLUG));

        $em->persist($pedido);
        $em->flush();
    }
    
    public function pagopedidopaypalAction(Request $request)
    {
        //Registro del movimiento de ingreso de dinero a la cuenta
        $paypalService = $this->container->get('paypal');
        $response = $paypalService->done($request);
        $registrarMovServcie = $this->container->get('RegistroMivimento');
        /** @var Usuario $usuario */
        $usuario = $this->getUser();

        //tomo el último pedido asignado
        $pedido = $usuario->getUltimoPedido();

        $totalPedido = $pedido->getTotal();
        
        try {
            if ($response['status'] ==  'ok' || $response['status'] ==  'captured') {
                /** @var MovimientoCuenta $movimiento */
                $movimiento = $registrarMovServcie->registrarPagoComPaypal($usuario, [
                    'referencia' => 'REF2',
                    'monto'      => $response['payment']['total_amount'],
                    'refext'     => 'OUTRAREF',
                    'identificador_pago' => $response['payment']['details']['INVNUM']
                ]);
                
                $montoPagado = $movimiento->getMonto();
                
                //Validación del monto pagado
                //Se evalua el monto pagado dentro de un rango para evitar
                //posibles redondeos erroneos
                if($montoPagado && ($montoPagado < $totalPedido - 1 && $montoPagado > $totalPedido + 1)) {
                    throw new \Exception('El monto abonado no corresponde con el total del ultimo pedido.');
                }
                
                return $this->registrarPagoSaldo($usuario, $pedido, $request);
            } else {
                $logger = $this->get('logger');
                $logger->error("Error guardando pago. ". json_encode($response));
                $this->addFlash('danger', 'No se ha podido realizar el pago.');
                return $this->redirectToRoute('public_finanzas_agregar');
            }
        } catch(\Exception $ex) {
            $logger = $this->get('logger');
            $logger->error("Error guardando pago. ". $ex->getMessage());
            $this->addFlash('danger', 'No se ha podido realizar el pago.');
            return $this->redirectToRoute('public_finanzas_agregar');
        }
        
    }
    
    /**
     * 
     * @param Usuario $usuario
     * @param Pedido $pedido
     * @param Request $request
     */
    private function registrarPagoSaldo(Usuario $usuario, Pedido $pedido, Request $request) {
        $registrarMovServcie = $this->container->get('RegistroMivimento');
        if ($registrarMovServcie->registrarPagoComSaldoMercadofree($usuario, $pedido)) {
            $this->registrarEstadoPedidoPagado($pedido);
            return $this->render('PublicBundle:Cesta:datosVendedorEntrega.html.twig',array(
                    'pedido' => $pedido
            ));
        } else {
        
        $buyNow=$request->get('buyNow');

        $idProducto=$request->get('idProducto');
        
        $request->getSession()
            ->getFlashBag()
            ->add('danger', 'Transacción fallida, intente nuevamente más tarde o contáctenos.');
        
            return $this->redirectToRoute('public_cesta_resumen', ['buyNow' => $buyNow, 'idProducto' => $idProducto]);
        }
    }

    public function calcularCostoEnvioAction(Request $request){
    	if($request->getMethod() == 'POST'){
    		$idPedido = $request->request->get('idPedido');

		    $em = $this->getDoctrine()->getManager();

		    $usuario = $this->getUser();

		    if($idPedido!=null){
			    $pedido = $em->getRepository(Pedido::class)->find($idPedido);

			    $tipoEnvio = null;

			    if($pedido != null){

				    $costoEnvio=$this->get('utilPublic')->getCostoEnvio($usuario, $pedido->getProducto()->getId());

				    return new JsonResponse(array(
				        true,
					    'costoEnvio' => $costoEnvio
				    ));
			    }
			    else{
				    return new JsonResponse(array(false,'Pedido no existe'));
			    }

		    }
		    else{
			    return new JsonResponse(array(false,'ID pedido no valido'));
		    }

	    }
    	else{
		    return new JsonResponse(array(false,'Peticion incorrecta'));
	    }
    }

    private function getPedido($idProducto=null){
    	$usuario=$this->getUser();

    	if($usuario!=null){
    		$pedido=null;

    		if($idProducto==null) {

			    $pedido = $this->getDoctrine()->getManager()->getRepository( Pedido::class )->findOneBy( array(
				    'usuario' => $usuario->getId(),
				    'estado'  => 1,
				    'producto' => null
			    ) );

		    }
		    else{
			    $pedido = $this->getDoctrine()->getManager()->getRepository( Pedido::class )->findOneBy( array(
				    'usuario' => $usuario->getId(),
				    'estado'  => 1,
				    'producto' => $idProducto
			    ) );
		    }

    		if($pedido!=null){
    			return $pedido;
		    }
	    }

	    return null;
    }

}
