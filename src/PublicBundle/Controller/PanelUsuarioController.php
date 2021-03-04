<?php


namespace PublicBundle\Controller;


use AdministracionBundle\Entity\Cliente;
use AdministracionBundle\Entity\Configuracion;
use AdministracionBundle\Entity\CostoEnvio;
use AdministracionBundle\Entity\InformacionFiscal;
use AdministracionBundle\Entity\OpcionValoracionCalidadProductoPedido;
use AdministracionBundle\Entity\OpcionValoracionTiempoEntregaPedido;
use AdministracionBundle\Entity\Pedido;
use AdministracionBundle\Entity\Tienda;
use AdministracionBundle\Entity\Usuario;
use AdministracionBundle\Entity\UsuarioObjetivo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PanelUsuarioController extends Controller
{
    public function resumenAction()
    {
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        $where = "where usuario.id =" . $usuario->getId() . " and (pregunta.respuesta is null or  pregunta.respuesta ='') ";

        $sql = "select 
                  count(pregunta) as cant
              from 
              AdministracionBundle:Pregunta pregunta 
              INNER JOIN  pregunta.productoid producto              
              INNER JOIN  producto.usuarioid usuario             
              " . $where . "   
             ";

        $preguntas = $em->createQuery($sql)->getResult();

        $where2 = "where usuario.id =" . $usuario->getId() . " and estado.id = 3 ";

        $sql2 = "select 
                  producto                
              from               
              AdministracionBundle:Producto producto 
              INNER JOIN  producto.estadoProductoid estado
              LEFT JOIN  producto.ofertaid oferta
              INNER JOIN  producto.categoriaid categoria
              INNER JOIN  producto.usuarioid usuario
              LEFT JOIN  producto.coleccionid coleccion
              INNER JOIN  producto.condicion condicion
              INNER JOIN  usuario.direccion direccion              
              INNER JOIN  direccion.ciudad ciudad             
              LEFT JOIN  producto.campannaid campanna
              " . $where2 . "   
             ";

        $publicacionesActivadas = count($em->createQuery($sql2)->getResult());

        $where3 = "where usuario.id =" . $usuario->getId() . " and estado.id = 4 ";

        $sql3 = "select 
                  producto
              from 
              AdministracionBundle:Producto producto 
              INNER JOIN  producto.estadoProductoid estado
              LEFT JOIN  producto.ofertaid oferta
              INNER JOIN  producto.categoriaid categoria
              INNER JOIN  producto.usuarioid usuario
              LEFT JOIN  producto.coleccionid coleccion
              INNER JOIN  producto.condicion condicion
              INNER JOIN  usuario.direccion direccion              
              INNER JOIN  direccion.ciudad ciudad
              
              LEFT JOIN  producto.campannaid campanna
              " . $where3 . "   
             ";

        $publicacionesPausada = count($em->createQuery($sql3)->getResult());

        $where4 = "where usuario.id =" . $usuario->getId() . " and estado.id = 5 ";

        $sql4 = "select 
                  producto
              from 
              AdministracionBundle:Producto producto 
              INNER JOIN  producto.estadoProductoid estado
              LEFT JOIN  producto.ofertaid oferta
              INNER JOIN  producto.categoriaid categoria
              INNER JOIN  producto.usuarioid usuario
              LEFT JOIN  producto.coleccionid coleccion
              INNER JOIN  producto.condicion condicion
              INNER JOIN  usuario.direccion direccion              
              INNER JOIN  direccion.ciudad ciudad
              LEFT JOIN  producto.campannaid campanna
              " . $where4 . "   
             ";

        $publicacionesFinalizadas = count($em->createQuery($sql4)->getResult());

        $facturaAPagar = null;
        
        $operacionesVendedor = $em->getRepository(Pedido::class)->findPedidoByVendedor($usuario->getId(), null, null, true);
        
        $operacionesPendientes = 0;
        /** @var Pedido $pedido */
        foreach($operacionesVendedor as $pedido) {
            if($pedido->pagado()) {
                $operacionesPendientes++;
            }
        }
        
        $campannas = count($usuario->getUsuarioCampanna());

        return $this->render(
            'PublicBundle:PanelUsuario:resumen.html.twig',
            array(
                'preguntas'   => $preguntas[0]['cant'],
                'pausadas'    => $publicacionesPausada,
                'activas'     => $publicacionesActivadas,
                'finalizadas' => $publicacionesFinalizadas,
                'facturaAPagar' => null,
                'campannas'   => $campannas,
                'operacionesPendientes' => $operacionesPendientes,
            )
        );
    }

    public function publicidadAction()
    {
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        $campannas = $usuario->getUsuarioCampanna();

        $productos = $em->getRepository('AdministracionBundle:Producto')->findBy(array('estadoProductoid' => 3, 'usuarioid' => $usuario->getId()), array());

        $arrayInformacion = [];

        foreach ($campannas as $c) {
            $cont = 0;

            $inversion = 0;

            foreach ($productos as $p) {
                if ($p->getCampannaid()) {
                    if ($p->getCampannaid()->getId() == $c->getCampannaid()->getId()) {
                        $cont++;

                        $inversion = (float)$inversion + (float)$p->getInversion();
                    }
                }
            }

            $d = $c->getEstadoCampannaid()->getId();

            $estado = $em->getRepository('AdministracionBundle:EstadoCampanna')->find($d);

            $informacion = array('nombre' => $c->getCampannaid()->getNombre(), 'slug' => $c->getCampannaid()->getSlug(), 'cantProductos' => $cont, 'inversion' => $inversion, 'estadoCampanna' => $estado->getNombre());

            $arrayInformacion[] = $informacion;
        }


        return $this->render('PublicBundle:PanelUsuario:listadoPublicidad.html.twig', array('campannas' => $campannas, 'productos' => $productos, 'informacion' => $arrayInformacion));
    }

    public function publicidadDetalleAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        $campanna = $em->getRepository('AdministracionBundle:Campanna')->findBy(array('slug' => $slug), array());

        $campannaUsuario = $em->getRepository('AdministracionBundle:UsuarioCampanna')->findBy(array('campannaid' => $campanna[0]->getId(), 'usuarioid' => $usuario->getId()));

        $campannaUsuarioCount = $em->getRepository('AdministracionBundle:UsuarioCampanna')->findBy(array('usuarioid' => $usuario->getId()));

        $campannaUsuarioCount = count($campannaUsuarioCount);

        $productos = $em->getRepository('AdministracionBundle:Producto')->findBy(array('campannaid' => $campanna[0]->getId(), 'usuarioid' => $usuario->getId()));

        /*$start=count($productos);

        $productosTotal=$em->getRepository('AdministracionBundle:Producto')->findByProductoPublicTotal($request)->getResult();

        $total=count($productosTotal);*/

        $inversion = 0;

        $visitas = 0;

        foreach ($productos as $p) {
            $inversion = (float)$inversion + (float)$p->getInversion();

            $visitas = (float)$visitas + (float)count($p->getVisitas());
        }

        $productosSinCampanna = $em->getRepository('AdministracionBundle:Producto')->findBy(array('campannaid' => null, 'usuarioid' => $usuario->getId()), array());

        return $this->render('PublicBundle:PanelUsuario:detallePublicidad.html.twig', array('productos' => $productos,
            'campanna' => $campanna[0],
            'campannaUsuario' => $campannaUsuario[0],
            'inversion' => $inversion,
            'visitas' => $visitas,
            'productosSinCampanna' => $productosSinCampanna,
            'campannaUsuarioCount' => $campannaUsuarioCount));
    }

    public function favoritosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();
        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        if ($request->getMethod() == 'POST') {
            $ids = $request->request->get('idsProducto');

            $ids = explode(',', $ids);

            foreach ($usuario->getProductosFavoritos() as $p) {
                foreach ($ids as $id) {

                    if ($p->getId() == $id) {
                        $usuario->removeProductoFavorito($p);
                    }
                }

            }

            $em->flush();

            return new Response(json_encode(true));
        }

        return $this->render('PublicBundle:PanelUsuario:favoritos.html.twig', array('productos' => $usuario->getProductosFavoritos()));
    }

    public function historialAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        if ($request->getMethod() == 'POST') {
            $visitas = $em->getRepository('AdministracionBundle:Visita')->findBy(array('usuarioid' => $usuario->getId(), 'historial' => null), array(
                'fecha' => 'desc'
            ));
            if ($request->request->get('indicador') == "1") {
                $listaProductos = [];

                foreach ($visitas as $v => $visita) {

                    $producto = $visita->getProductoid();

                    $productoArray = array(
                        $producto->getId(),
                        $producto->getNombre() != null ? $producto->getNombre() : "",
                        $producto->getPrecio() != null ? $producto->getPrecio() : "",
                        $producto->getCuotaspagar() != null ? $producto->getCuotaspagar() : "",
                        $producto->getCantidad() != null ? $producto->getCantidad() : "",
                        $producto->getCategoriaid() != null ? $producto->getCategoriaid()->getNombre() : "",
                        $producto->getUsuarioid() != null ? $producto->getUsuarioid()->getEmail() : "",
                        $producto->getEstadoProductoid() != null ? $producto->getEstadoProductoid()->getNombre() : "",
                        $producto->getImagenDestacada() != null ? $producto->getImagenDestacada() : $producto->getImagenes()[0]->getUrl(),
                        $producto->hasEnvioDomicilio(),
                        $this->generateUrl('public_anuncio_detalles', array('slug' => $producto->getSlug())),
                        $visita->getFecha()->format('d/m/Y')
                    );
                    $listaProductos[$visita->getFecha()->format('m/Y')][] = $productoArray;
                }

                return new Response(json_encode(array('productos' => $listaProductos)));
            } else {
                foreach ($visitas as $v) {
                    $v->setHistorial(1);

                    $em->persist($v);
                }

                $em->flush();

                return new Response(json_encode(true));
            }

        }

        return $this->render('PublicBundle:PanelUsuario:historial.html.twig');
    }

    public function garantiaAction(Request $request){
		if($request->getMethod()=='POST'){
			$mostrarGarantia=$request->request->get('mostrarGarantia');

			$detalleGarantia=$request->request->get('detalleGarantia');

			$usuario=$this->getUser();

			$usuario->setMostrarGarantia($mostrarGarantia);

			$usuario->setGarantia($detalleGarantia);

			$this->getDoctrine()->getManager()->persist($usuario);
			$this->getDoctrine()->getManager()->flush();

			return new JsonResponse(array(true));
		}
		else{
			return $this->render('PublicBundle:PanelUsuario:garantia.html.twig');
		}
    }


    public function preguntasCompradorAction()
    {
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        $query = 'select pregunta from  AdministracionBundle:Pregunta pregunta INNER JOIN pregunta.productoid producto WHERE pregunta.usuarioid = ' . $usuario->getId();


        $productos = array();

        $preguntas = $em->createQuery($query)->getResult();
        foreach ($preguntas as $pregunta){
            $productos [] = $pregunta->getProductoid();
        }
        $productos = array_unique($productos, SORT_REGULAR);

        return $this->render('PublicBundle:PanelUsuario:preguntasComprador.html.twig', array('productos' => $productos));
    }

    public function anunciosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$this->get('security.token_storage')->getToken()->isAuthenticated()) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        $request->request->set('usuarioid', $usuario->getId());

        $productos = $em->getRepository('AdministracionBundle:Producto')->findByProductoPanelUsuarioTotal($request)->getResult();

        $total = count($productos);

        return $this->render('PublicBundle:PanelUsuario:anuncios.html.twig', array('total' => $total));
    }

    public function pedidosVendedorAction()
    {
        $usuario = $this->getUser();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $pedidos = $em->getRepository(Pedido::class)->findPedidoByVendedor($usuario->getId());


        return $this->render('PublicBundle:PanelUsuario:pedidos.html.twig', array('total' => count($pedidos)));

    }

    public function pedidosCompradorAction()
    {
        $usuario = $this->getUser();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $opcionesValoracionCalidadProductoPedido = $em->getRepository(OpcionValoracionCalidadProductoPedido::class)->findAll();
        $opcionesValoracionTiempoEntregaPedido = $em->getRepository(OpcionValoracionTiempoEntregaPedido::class)->findAll();
        
        $configuracion = $em->getRepository(Configuracion::class)->getDefaultConfiguracion();

        $pedidos = $em->getRepository(Pedido::class)->findPedidoByComprador($usuario->getId(), 0, 6, true);

        return $this->render('PublicBundle:PanelUsuario:pedidos-compra.html.twig', array(
            'configuracion' => $configuracion,
            'opcionesValoracionCalidadProductoPedido' => $opcionesValoracionCalidadProductoPedido,
            'opcionesValoracionTiempoEntregaPedido' => $opcionesValoracionTiempoEntregaPedido,
            'pedidos' => $pedidos
        ));

    }

    public function preguntasVendedorAction()
    {
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        $query = 'select producto from  AdministracionBundle:Producto producto INNER JOIN producto.preguntas preguntas WHERE producto.usuarioid = ' . $usuario->getId();

        $productos = $em->createQuery($query)->getResult();

        return $this->render('PublicBundle:PanelUsuario:preguntasVendedor.html.twig', array('productos' => $productos));
    }

    public function editarCuentaAction(Request $request)
    {
        $usuario = $this->getUser();

        $iban = $request->request->get('ibanC');

        $usuario->setIban($iban);

        $this->getDoctrine()->getManager()->persist($usuario);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(array(true));
    }
    
    public function editarCuentaPaypalAction(Request $request)
    {
        $usuario = $this->getUser();

        $cuentaPaypal = $request->request->get('cuentaPaypalC');

        $usuario->setEmailPaypal($cuentaPaypal);

        $this->getDoctrine()->getManager()->persist($usuario);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(array(true));
    }

    public function editarPerfilAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        //$ciudades=$em->getRepository('AdministracionBundle:Ciudad')->findAll();

        if ($request->getMethod() == 'POST') {
            $modificar = $request->request->get('modificar');

            switch ($modificar) {
                case 1: {
                    $usuario->setEmail($request->request->get('emailC'));

                    $password = $request->request->get('passwordC');

                    if ($password != "") {
                        $usuario->setPassword($password);
                    }

                    break;
                }

                case 2: {
                    $usuario->getClienteid()->setNombre($request->request->get('nombreC'));

                    $usuario->getClienteid()->setApellidos($request->request->get('apellidosC'));

                    $usuario->getClienteid()->setDni($request->request->get('dniC'));

                    $usuario->setTelefono($request->request->get('telefonoC'));

                    break;
                }
                case 3: {
                    $usuario->setEmail($request->request->get('emailE'));

                    $password = $request->request->get('passwordE');

                    if ($password != "") {
                        $usuario->setPassword($password);
                    }


                    $usuario->setTelefono($request->request->get('telefonoE'));

                    $usuario->getEmpresaid()->setCuit($request->request->get('cuitE'));

                    $usuario->getEmpresaid()->setRazonSocial($request->request->get('razonsocialE'));

                    break;
                }


            }

            $em->persist($usuario);

            $em->flush();

            if ($usuarioVacio = !$this->get('utilPublic')->UsuarioVacio($usuario)) {
                $usuarioObjetivo = $em->getRepository('AdministracionBundle:UsuarioObjetivo')->findBy(array('objetivoid' => 2));

                if (count($usuarioObjetivo) == 0) {
                    $hoy = new \DateTime();

                    $objetivoUsuario = new UsuarioObjetivo();

                    $objetivoUsuario->setUsuarioid($usuario);

                    $objetivo = $em->getRepository('AdministracionBundle:Objetivo')->findBy(array('slug' => 'completar_datos_personales'), array())[0];

                    $objetivoUsuario->setObjetivoid($objetivo);

                    $objetivoUsuario->setPuntos($objetivo->getPuntos());

                    $objetivoUsuario->setFecha($hoy);

                    $em->persist($objetivoUsuario);

                    $em->flush();

                    $usuario->setPuntos($usuario->getPuntos() + $objetivo->getPuntos());
                }

            }

            return new Response(json_encode(true));
        }

        $provincias = $em->getRepository('AdministracionBundle:Provincia')->findAll();

        $ciudades = null;

        if ($usuario->getClienteid()) {
            return $this->render('PublicBundle:PanelUsuario:editarPerfil.html.twig', array('usuario' => $usuario, 'provincias' => $provincias, 'ciudades' => $ciudades));
        } else {
            return $this->render('PublicBundle:PanelUsuario:editarPerfilEmpresa.html.twig', array('usuario' => $usuario, 'provincias' => $provincias, 'ciudades' => $ciudades));
        }

    }

    public function emailsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }
        $cliente = null;
        if (is_object($usuario->getClienteid())) {
            $cliente = $usuario->getClienteid();
        }else{
            $cliente = new Cliente();
            $cliente->setUsuarioid($usuario);
            $em->persist($cliente);
            $em->flush();
        }

        if ($request->getMethod() == 'POST') {

            if ($request->request->get('1')) {
                $cliente->setAlertProductoVendido(1);
            } else {
                $cliente->setAlertProductoVendido(0);
            }
            if ($request->request->get('2')) {
                $cliente->setAlertPreguntaPublicacion(1);
            } else {
                $cliente->setAlertPreguntaPublicacion(0);
            }
            if ($request->request->get('3')) {
                $cliente->setAlertFinalizoPublicacion(1);
            } else {
                $cliente->setAlertFinalizoPublicacion(0);
            }
            if ($request->request->get('4')) {
                $cliente->setAlertCompraProducto(1);
            } else {
                $cliente->setAlertCompraProducto(0);
            }
            if ($request->request->get('5')) {
                $cliente->setAlertProximoFinPublicacion(1);
            } else {
                $cliente->setAlertProximoFinPublicacion(0);
            }
            if ($request->request->get('6')) {
                $cliente->setAlertLogin(1);
            } else {
                $cliente->setAlertLogin(0);
            }

            $em->persist($cliente);

            $em->flush();


            return new Response(json_encode(true));

        }

        return $this->render('PublicBundle:PanelUsuario:emails.html.twig', array('cliente' => $cliente));
    }

    public function tiendaAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        $tienda = $em->getRepository('AdministracionBundle:Tienda')->findBy(array('usuarioid' => $usuario->getId()));


        if ($request->getMethod() == 'POST') {

            if (count($tienda) > 0) {
                $tienda = $tienda[0];
            } else {
                $tienda = new Tienda();
            }

            $logo = $request->request->get('logo');

            $banner = $request->request->get('banner');

            $visible = ($request->request->has('visible') ? 1 : 0);
            $tienda->setVisible($visible);

            $tienda->setUsuarioid($usuario);

            $tienda->setSlogan($request->request->get('eslogan'));

            $tienda->setNombre($request->request->get('nombre'));

            $slug = $this->get('utilPublic')->generateSlug($request->request->get('nombre'));

            $tienda->setSlug($slug);

            if ($logo) {
                if ($tienda->getImagenLogo() != null) {
                    if ($tienda->getImagenLogo() != $logo) {
                        $em->remove($tienda->getImagenes()[0]);

                        $em->flush();

                        $this->get('utilPublic')->procesarFotoTienda($logo, $tienda, 'logo');
                    }
                } else {
                    $this->get('utilPublic')->procesarFotoTienda($logo, $tienda, 'logo');
                }

            }

            if ($banner) {
                if ($tienda->getImagenPortada() != null) {
                    if ($tienda->getImagenPortada() != $banner) {
                        $em->remove($tienda->getImagenes()[1]);

                        $em->flush();

                        $this->get('utilPublic')->procesarFotoTienda($banner, $tienda, 'banner');
                    }
                } else {
                    $this->get('utilPublic')->procesarFotoTienda($banner, $tienda, 'banner');
                }

            }

            $usuarioObjetivo = $em->getRepository('AdministracionBundle:UsuarioObjetivo')->findBy(array('objetivoid' => 12));

            if (count($usuarioObjetivo) == 0) {
                $hoy = new \DateTime();

                $objetivoUsuario = new UsuarioObjetivo();

                $objetivoUsuario->setUsuarioid($usuario);

                $objetivo = $em->getRepository('AdministracionBundle:Objetivo')->findBy(array('slug' => 'crea_una_tienda_para_agrupar_tus_productos'), array())[0];

                $objetivoUsuario->setObjetivoid($objetivo);

                $objetivoUsuario->setPuntos($objetivo->getPuntos());

                $objetivoUsuario->setFecha($hoy);

                $em->persist($objetivoUsuario);

                $em->flush();

                $usuario->setPuntos($usuario->getPuntos() + $objetivo->getPuntos());
            }

            $em->persist($tienda);

            $em->flush();

            return new Response(json_encode(array('id' => $tienda->getId())));
        }

        count($tienda) > 0 ? $tienda = $tienda[0] : $tienda = null;

        $productos = $em->getRepository('AdministracionBundle:Producto')->findBy(array('usuarioid' => $usuario->getId(), 'estadoProductoid' => 3));

        $cantProductos = count($productos);
        $configuracion = $em->getRepository('AdministracionBundle:Configuracion')->find(1);

        return $this->render('PublicBundle:PanelUsuario:tienda.html.twig', array('tienda' => $tienda, 'cantProductos' => $cantProductos, 'configuracion' => $configuracion));
    }

    public function datosFiscalesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        if ($request->getMethod() == 'POST') {
            if ($usuario->getEmpresaid()->getInformacionfiscal()) {
                $query = 'DELETE FROM  AdministracionBundle:InformacionFiscal if WHERE if.id = ' . $usuario->getEmpresaid()->getInformacionfiscal()->getId();

                $em->createQuery($query)->getResult();
            }

            $informacionFiscal = new InformacionFiscal();

            $tipoContribuyente = $em->getRepository('AdministracionBundle:TipoContribuyente')->find($request->request->get('tipoContribuyente'));

            $informacionFiscal->setTipoContribuyente($tipoContribuyente);

            $informacionFiscal->setEmpresaid($usuario->getEmpresaid());


            if ($request->request->get('tipoContribuyente') == "1" || $request->request->get('tipoContribuyente') == "2") {

                if ($request->request->get('exclusionIVA') == "1") {

                    if ($request->request->get('certificadoExclusionHidden') != $request->request->get('certificadoIVA')) {
                        $name = $this->get('utilPublic')->procesarFichero($request->request->get('certificadoIVA'), $usuario);

                        $informacionFiscal->setCertificadoExclusion($name);
                    } else {
                        $informacionFiscal->setCertificadoExclusion($request->request->get('certificadoIVA'));
                    }

                    $informacionFiscal->setFechaIniValidezCert(new \DateTime($request->request->get('fechaInicio')));

                    $informacionFiscal->setFechaFinValidezCert(new \DateTime($request->request->get('fechaFin')));
                } elseif ($request->request->get('exclusionIVA') == "0") {
                    $regimenIngBru = $em->getRepository('AdministracionBundle:RegimenIngresosBrutos')->find($request->request->get('regimenIngresosBrutos'));

                    if ($request->request->get('formInscripcionIngresosBrutosHidden') != $request->request->get('certificadoIngresosBrutos')) {
                        $name = $this->get('utilPublic')->procesarFichero($request->request->get('certificadoIngresosBrutos'), $usuario);

                        $informacionFiscal->setFormInscripcionIngresosBrutos($name);
                    } else {
                        $informacionFiscal->setFormInscripcionIngresosBrutos($request->request->get('certificadoIngresosBrutos'));
                    }

                    $informacionFiscal->setRegimenIngresosBrutos($regimenIngBru);

                }

            } elseif ($request->request->get('tipoContribuyente') == "3") {
                $name = $this->get('utilPublic')->procesarFichero($request->request->get('certificadoIVA'), $usuario);

                $informacionFiscal->setCertificadoExclusion($name);

                $informacionFiscal->setFechaIniValidezCert(new \DateTime($request->request->get('fechaInicio')));

                $informacionFiscal->setFechaFinValidezCert(new \DateTime($request->request->get('fechaFin')));
            }

            $em->persist($informacionFiscal);

            $em->flush();

            return new Response(json_encode(true));
        }
        $informacionFiscal2 = $usuario->getEmpresaid()->getInformacionfiscal();

        $tipoContribuyente = $em->getRepository('AdministracionBundle:TipoContribuyente')->findAll();

        $regimenIngresoBrutos = $em->getRepository('AdministracionBundle:RegimenIngresosBrutos')->findAll();

        return $this->render('PublicBundle:PanelUsuario:datosFiscales.html.twig', array('tipoContribuyente' => $tipoContribuyente, 'regimenIngresoBrutos' => $regimenIngresoBrutos, 'informacionfiscal' => $informacionFiscal2));
    }

    public function objetivosAction()
    {
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        $objetivos = $em->getRepository('AdministracionBundle:Objetivo')->findBy(array('visible' => 1));

        $objetivosUsuario = $em->getRepository('AdministracionBundle:UsuarioObjetivo')->findBy(array('usuarioid' => $usuario->getId()));

        return $this->render('PublicBundle:PanelUsuario:objetivos.html.twig', array('objetivos' => $objetivos, 'objetivosUsuario' => $objetivosUsuario));
    }

    public function enviosAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        if ($request->getMethod() == "POST") {
            $envioGratisTodoPais = $request->request->get('1');

            $envios = $usuario->getTipoenvios();

            if ($envioGratisTodoPais == "on") {
                $flag = false;
                foreach ($envios as $e) {
                    if ($e->getSlug() == "envio-gratis") {
                        $flag = true;
                    }
                }

                if ($flag == false) {
                    $te = $em->getRepository('AdministracionBundle:TipoEnvio')->findBy(array('slug' => "envio-gratis"))[0];
                    $te->addUsuarioid($usuario);
                }


            } else {
                $flag = false;
                foreach ($envios as $e) {
                    if ($e->getSlug() == "envio-gratis") {
                        $flag = true;
                    }
                }

                if ($flag == true) {
                    $te = $em->getRepository('AdministracionBundle:TipoEnvio')->findBy(array('slug' => "envio-gratis"))[0];
                    $te->removeUsuarioid($usuario);
                }
            }

            $envioMercadoFree = $request->request->get('2');

            if ($envioMercadoFree == "on") {
                $flag = false;
                foreach ($envios as $e) {
                    if ($e->getSlug() == "envio-domicilio-mercadofree") {
                        $flag = true;
                    }
                }

                if ($flag == false) {
                    $te = $em->getRepository('AdministracionBundle:TipoEnvio')->findBy(array('slug' => "envio-domicilio-mercadofree"))[0];
                    $te->addUsuarioid($usuario);
                }


            } else {
                $flag = false;
                foreach ($envios as $e) {
                    if ($e->getSlug() == "envio-domicilio-mercadofree") {
                        $flag = true;
                    }
                }

                if ($flag == true) {
                    $te = $em->getRepository('AdministracionBundle:TipoEnvio')->findBy(array('slug' => "envio-domicilio-mercadofree"))[0];
                    $te->removeUsuarioid($usuario);
                }
            }

            $envioDomicilioVendedor = $request->request->get('3');

            if ($envioDomicilioVendedor == "on") {
                $flag = false;
                foreach ($envios as $e) {
                    if ($e->getSlug() == "envio-domicilio-vendedor") {
                        $flag = true;
                    }
                }

                if ($flag == false) {
                    $te = $em->getRepository('AdministracionBundle:TipoEnvio')->findBy(array('slug' => "envio-domicilio-vendedor"))[0];
                    $te->addUsuarioid($usuario);
                }
                $envioCostoFijo = $request->request->get('4');

                if ($envioCostoFijo == "on") {
                    $d = $usuario->getEnvioFijoPais();
                    if ($d) {
                        $d->setCosto($request->request->get('costoFijo'));
                    } else {
                        $de = new CostoEnvio();

                        $de->setUsuarioid($usuario);

                        $de->setCosto($request->request->get('costoFijo'));

                        $de->setTodoElPais(true);

                        $em->persist($de);
                    }
                }

            } else {
                $flag = false;
                foreach ($envios as $e) {
                    if ($e->getSlug() == "envio-domicilio-vendedor") {
                        $flag = true;
                    }
                }

                if ($flag == true) {
                    $te = $em->getRepository('AdministracionBundle:TipoEnvio')->findBy(array('slug' => "envio-domicilio-vendedor"))[0];
                    $te->removeUsuarioid($usuario);
                }
            }


            $recojidaDomicilioVendedor = $request->request->get('5');

            if ($recojidaDomicilioVendedor == "on") {
                $flag = false;
                foreach ($envios as $e) {
                    if ($e->getSlug() == "recogida-domicilio-vendedor") {
                        $flag = true;
                    }
                }

                if ($flag == false) {
                    $te = $em->getRepository('AdministracionBundle:TipoEnvio')->findBy(array('slug' => "recogida-domicilio-vendedor"))[0];
                    $te->addUsuarioid($usuario);
                }


            } else {
                $flag = false;
                foreach ($envios as $e) {
                    if ($e->getSlug() == "recogida-domicilio-vendedor") {
                        $flag = true;
                    }
                }

                if ($flag == true) {
                    $te = $em->getRepository('AdministracionBundle:TipoEnvio')->findBy(array('slug' => "recogida-domicilio-vendedor"))[0];
                    $te->removeUsuarioid($usuario);
                }
            }

            $em->flush();

            return new Response(json_encode(true));
        }
        $envios = $usuario->getTipoenvios();

        $gruposCostoEnvio = $usuario->getGrupoCostoEnvios();

        $provincias = $em->getRepository('AdministracionBundle:Provincia')->findAll();

        return $this->render('PublicBundle:PanelUsuario:envios.html.twig', array("envios" => $envios, "provincias" => $provincias, 'gruposCostoEnvio' => $gruposCostoEnvio));
    }

}
