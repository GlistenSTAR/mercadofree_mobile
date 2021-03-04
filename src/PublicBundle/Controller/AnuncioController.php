<?php


namespace PublicBundle\Controller;


use AdministracionBundle\Entity\Categoria;
use AdministracionBundle\Entity\Configuracion;
use AdministracionBundle\Entity\EstadoProducto;
use AdministracionBundle\Entity\Pedido;
use AdministracionBundle\Entity\Producto;
use AdministracionBundle\Entity\ProductoCaracteristicaCategoria;
use AdministracionBundle\Entity\Usuario;
use AdministracionBundle\Entity\UsuarioCampanna;
use AdministracionBundle\Entity\UsuarioObjetivo;
use AdministracionBundle\Entity\Visita;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\DateTime;


class AnuncioController extends Controller
{
    public function publicarAction(Request $request, $step)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->getMethod() == 'POST') {
            $step = $request->request->get('step');
        }
        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        $producto = $em->getRepository('AdministracionBundle:Producto')->findBy(
            array('usuarioid' => $usuario->getId(),
                'estadoProductoid' => 1),
            array()
        );

        $nav = $request->request->get('nav');

        if ($nav == null) {
            $nav = $request->query->get('nav');
        }

        if (count($producto) == 0) {
            $producto = null;
        } else {
            $producto = $producto[0];

            if ($nav != "1" && $request->request->get('idCategoria') == null) {
                $step = $producto->getStep();
            }

        }

        //Conocer si el usuario tiene llenados los datos personales y de contacto para activar o no el paso 4

        $usuarioVacio = !$this->get('utilPublic')->UsuarioVacio($usuario);
        switch ($step) {
            case 1: { // PASO 1, SELECCIONAR CATEGORIA
                if ($request->getMethod() == 'POST') {
                    $idCategoria = $request->request->get('idCategoria');

                    $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($idCategoria);

                    $estado = $em->getRepository('AdministracionBundle:EstadoProducto')->find(1);

                    if ($producto == null) {
                        $producto = new Producto();
                    }

                    $producto->setUsuarioid($usuario);

                    $producto->setCategoriaid($categoria);

                    $producto->setEstadoProductoid($estado);

                    $producto->setStep(2);

                    $em->persist($producto);

                    $em->flush();

                    return new Response(json_encode(true));


                }

                $categorias = $em->getRepository('AdministracionBundle:Categoria')->findBy(array('nivel' => 1), array());

                $cadenaId = "";

                $selects = "";

                $hilo = "";

                if ($producto != null) {
                    $resultado = $this->construirSelect($producto);

                    $selects = $resultado[0];

                    $cadenaId = $resultado[1];

                    $hilo = $resultado[2];

                    $idCategoria1 = explode('-', $cadenaId);

                    $idCategoria1 = explode(':', $idCategoria1[count($idCategoria1) - 1]);

                    $categoriaHilo = $em->getRepository('AdministracionBundle:Categoria')->find($idCategoria1[0]);

                    $hilo = $categoriaHilo->getCategoriaid()->getNombre() . " > " . $hilo;

                    $hilo = substr($hilo, 0, -2);
                }

                return $this->render('PublicBundle:Anuncio:publicar.html.twig', array('categorias' => $categorias, 'producto' => $producto, 'selects' => $selects, 'cadenaId' => $cadenaId, 'hilo' => $hilo, 'usuarioVacio' => $usuarioVacio));
            }

            case 2: { // PASO 2, IMAGENES, CARACTERISTICAS Y DESCRIPCION DEL PRODUCTO
                if ($request->getMethod() == 'POST') {
                    $producto = $em->getRepository('AdministracionBundle:Producto')->find($request->request->get('idProducto'));

                    $producto->setDescripcion($request->request->get('descripcion'));

                    $producto->setNombre($request->request->get('titulo'));

                    $producto->setStep($request->request->get('step'));

                    // Obtener campos de peso y dimensiones si se han introducido

	                $producto->setPeso($request->request->get('peso'));

	                $producto->setAlto($request->request->get('alto'));

	                $producto->setAncho($request->request->get('ancho'));

	                $producto->setProfundidad($request->request->get('profundidad'));

                    $producto->setSlug($this->get('utilpublic')->generateSlug($request->request->get('titulo')));

                    $condicion = $em->getRepository('AdministracionBundle:CondicionProducto')->find($request->request->get('condicion'));

                    $producto->setCondicion($condicion);

                    $caracteristicas = $request->request->get('caracteristica');

                    $imagenes = $request->request->get('imagenes');

                    $caracteristicasCategoria = $producto->getCategoriaid()->getCaracteristicas();

                    $caraacteristicas = $em->getRepository('AdministracionBundle:ProductoCaracteristicaCategoria')->findBy(array('productoid' => $producto->getId()));

                    //$em->persist($producto);

                    foreach ($caraacteristicas as $carac) {
                        $em->remove($carac);
                    }
                    $em->flush();

                    $cont = 0;
                    foreach ($caracteristicasCategoria as $carac) {
                        $productoCaracteristica = new ProductoCaracteristicaCategoria();
                        $productoCaracteristica->setCaracteristicaCategoriaid($carac);
                        $productoCaracteristica->setProductoid($producto);
                        $productoCaracteristica->setValor($caracteristicas[$cont]);

                        $em->persist($productoCaracteristica);

                        $cont++;
                    }

                    $imgs = $producto->getImagenes();

                    foreach ($imgs as $i) {
                        $i->setDestacada(false);
                        $em->flush();
                    }
                    if ($request->request->has('fotoPerfil')) {
                        $imagen = $em->getRepository('AdministracionBundle:Imagen')->find($request->request->get('fotoPerfil'));

                        $imagen->setDestacada(true);
                    }
                    else if($imgs!=null && count($imgs)>0){
                    	$imgs[0]->setDestacada(true);
                    	$em->persist($imgs[0]);
                    }
                    $producto->setStep(3);

                    $em->persist($producto);

                    $em->flush();

                    return new Response(json_encode(true));

                }
                $caraacteristicas = null;
                if ($producto != null) {
                    $caraacteristicas = $em->getRepository('AdministracionBundle:ProductoCaracteristicaCategoria')->findBy(array('productoid' => $producto->getId()));
                }

                $condiciones = $em->getRepository('AdministracionBundle:CondicionProducto')->findAll();

                return $this->render('PublicBundle:Anuncio:publicar_2.html.twig', array('producto' => $producto, 'caraacteristicas' => $caraacteristicas, 'condiciones' => $condiciones, 'usuarioVacio' => $usuarioVacio));
            }


            case 3: {
                $tipoenvio = $em->getRepository('AdministracionBundle:TipoEnvio')->findAll();

                $usuarioFlag = $this->get('utilPublic')->UsuarioVacio($usuario);

                $garantiaUsuario = $usuario->getGarantia();

                if ($request->getMethod() == 'POST') {
                    $producto->setStep($request->request->get('step'));

                    $producto->setCantidad($request->request->get('cantidad'));

                    $producto->setPrecio($request->request->get('precio'));

                    //$enviosEliminar=$producto->getTipoenvioid();

                    $garantiaProducto = $request->request->get('garantiaProducto');

                    if ($garantiaProducto != "") {
                        $producto->setGarantia($garantiaProducto);
                    }

//                    foreach ($enviosEliminar as $te2)
//                    {
//                        $te2->removeProductoid($producto);
//                    }

                    $em->flush();

                    //$tipoenvioArray=[];

                    $productoArray[] = $producto;
//                    foreach ($tipoenvio as $te)
//                    {
//                       $teU= $request->request->get($te->getSlug());
//
//                       if($teU)
//                       {
//                           $tipoenvioArray[]=$te;
//
//                           $te->addProductoid($producto);
//
//                           //$em->persist($te);
//                       }
//                    }

                    //$producto->setTipoenvioid($tipoenvioArray);

                    if ($usuarioFlag) {
                        $estadoProducto = $em->getRepository('AdministracionBundle:EstadoProducto')->find(3);

                        $producto->setEstadoProductoid($estadoProducto);

                        $producto->setStep(5);
                    }

                    $producto->setStep(4);

                    $em->persist($producto);

                    /////////////////////////Objetivo usuario

//                    $hoy=new \DateTime();
//
//                    $objetivoUsuario= new UsuarioObjetivo();
//
//                    $objetivoUsuario->setUsuarioid($producto->getUsuarioid());
//
//                    $objetivo=$em->getRepository('AdministracionBundle:Objetivo')->findBy(array('slug'=>'registro'), array())[0];
//
//                    $objetivoUsuario->setObjetivoid($objetivo);
//
//                    $objetivoUsuario->setPuntos($objetivo->getPuntos());
//
//                    $objetivoUsuario->setFecha($hoy);
//
//                    $em->persist($objetivoUsuario);

                    //////////////////////////


                    $em->flush();

                    return new Response(json_encode(true));

                }

                return $this->render('PublicBundle:Anuncio:publicar_3.html.twig', array(
                    'tipoenvio' => $tipoenvio,
                    'producto' => $producto,
                    'usuarioFlag' => $usuarioFlag,
                    'usuarioVacio' => $usuarioVacio,
                    'garantiaUsuario' => ($garantiaUsuario != null) ? $garantiaUsuario : ''
                ));
            }


            case 4: {
                if ($request->getMethod() == 'POST') {
                    $usuario->setTelefono($request->request->get('telefono'));

                    if ($usuario->getRolid()->getId() == 2) {
                        $usuario->getClienteid()->setNombre($request->request->get('nombre'));

                        $usuario->getClienteid()->setApellidos($request->request->get('apellidos'));

                        $usuario->getClienteid()->setDni($request->request->get('dni'));
                    }
                    if ($usuario->getRoles() == 'ROLE_EMPRESA') {
                        $usuario->getEmpresaid()->setCuit($request->request->get('nombre'));

                        $usuario->getEmpresaid()->setRazonSocial($request->request->get('apellidos'));
                    }

                    $estadoProducto = $em->getRepository('AdministracionBundle:EstadoProducto')->find(3);

                    $producto->setEstadoProductoid($estadoProducto);

                    $producto->setStep(5);

                    $em->persist($producto);

                    $em->persist($usuario);

                    $em->flush();

                    return new Response(json_encode(true));
                }
                $provincia = $em->getRepository('AdministracionBundle:Provincia')->findAll();

                $ciudadTemp = null;

                if ($usuario->getDireccionVenta() != null) {
                    $ciudadTemp = $em->getRepository('AdministracionBundle:Ciudad')->findBy(array('provinciaid' => $usuario->getDireccionVenta()->getProvincia()->getId()), array());
                }
                return $this->render('PublicBundle:Anuncio:publicar_4.html.twig', array('usuario' => $usuario, 'provincias' => $provincia, 'usuarioVacio' => $usuarioVacio, 'ciudades' => $ciudadTemp, 'producto' => $producto));

            }
            case 5: {
                $idProducto = $request->query->get('idProducto');
                
                // Obtener parametro de configuracion para conocer si se muestran los planes de publicidad o no
                /** @var Configuracion $conf */
                $conf = $em->getRepository(Configuracion::class)->findAll()[0];
                
                if ($idProducto) {
                    /** @var Producto $producto */
                    $producto = $em->getRepository('AdministracionBundle:Producto')->find($idProducto);
                    
                    /** Calculo de la fecha de expiración de la publicacion */
                    $categoria = $producto->getCategoriaid();
                    $tiempoExpiracion = $categoria->obtenerTiempoExpiracion();
                    
                    if(!$tiempoExpiracion) {
                        $tiempoExpiracion = $conf->getTiempoExpiracion();
                    }
                    
                    $fechaAuxiliarExpiracion = new \DateTime();
                    $dia = $fechaAuxiliarExpiracion->format('d');
                    
                    $fechaAuxiliarExpiracion->modify('first day of this month');
                    $fechaAuxiliarExpiracion->add(new \DateInterval('P'.$tiempoExpiracion.'M'));
                    $fechaAuxiliarExpiracion->modify('last day of this month');

                    $anio = $fechaAuxiliarExpiracion->format('Y');
                    $mes = $fechaAuxiliarExpiracion->format('m');
                    
                    if ( (int) $fechaAuxiliarExpiracion->format('d') < (int) $dia) {
                        $dia = $fechaAuxiliarExpiracion->format('d');
                    } 

                    $fechaAuxiliarExpiracion->setDate($anio, $mes, $dia);
        
                    $producto->setFechaExpiracion($fechaAuxiliarExpiracion);

                    $em->persist($producto);
                    $em->flush();
                }

                if($conf->isMostrarPaquetesPublicidad()){
                    if ($producto) {
                        $campannaUser = $em->getRepository('AdministracionBundle:UsuarioCampanna')->findBy(
                            array('usuarioid' => $producto->getUsuarioid()->getId()), array());
                    }


                    if ($request->getMethod() == 'POST') {

                        $campannaUser = $em->getRepository('AdministracionBundle:UsuarioCampanna')->findBy(
                            array('campannaid' => $request->request->get('idPlan'),
                                'usuarioid' => $producto->getUsuarioid()->getId()), array());

                        $campanna = $em->getRepository('AdministracionBundle:Campanna')->find($request->request->get('idPlan'));

                        if (count($campannaUser) == 0) {
                            $estadoCampanna = $em->getRepository('AdministracionBundle:EstadoCampanna')->find(1);

                            $usuarioCampanna = new UsuarioCampanna();

                            $usuarioCampanna->setCampannaid($campanna);

                            $usuarioCampanna->setUsuarioid($usuario);

                            $usuarioCampanna->setEstadoCampannaid($estadoCampanna);

                            $hoy = new \DateTime();

                            $usuarioCampanna->setFecha($hoy);

                            $em->persist($usuarioCampanna);
                        }

                        $producto->setCampannaid($campanna);

                        $producto->setActivo(1);

                        $producto->setRanking($campanna->getId());

                        $em->persist($producto);

                        $em->flush();


                        $usuarioObjetivo = $em->getRepository('AdministracionBundle:UsuarioObjetivo')->findBy(array('objetivoid' => 10));

                        if (count($usuarioObjetivo) == 0) {
                            $hoy = new \DateTime();

                            $objetivoUsuario = new UsuarioObjetivo();

                            $objetivoUsuario->setUsuarioid($usuario);

                            $objetivo = $em->getRepository('AdministracionBundle:Objetivo')->findBy(array('slug' => 'crea_una_campaña_de_publicidad_para_uno_de_tus_productos'), array())[0];

                            $objetivoUsuario->setObjetivoid($objetivo);

                            $objetivoUsuario->setPuntos($objetivo->getPuntos());

                            $objetivoUsuario->setFecha($hoy);

                            $em->persist($objetivoUsuario);

                            $em->flush();

                            $usuario->setPuntos($usuario->getPuntos() + $objetivo->getPuntos());


                        }


                        return new Response(json_encode(true));

                    }

                    $uno = 0;

                    $dos = 0;

                    $tres = 0;

                    foreach ($campannaUser as $cu) {
                        switch ($cu->getCampannaid()->getId()) {
                            case 1:
                                $uno = 1;
                                break;
                            case 2:
                                $dos = 1;
                                break;
                            case 3:
                                $tres = 1;
                                break;

                        }

                    }
                    $producto = $em->getRepository('AdministracionBundle:Producto')->find($request->query->get('idProducto'));

                    $planes = $em->getRepository('AdministracionBundle:Campanna')->findAll();

                    return $this->render('PublicBundle:Publicidad:planes.html.twig', array('idProducto' => $producto->getId(), 'planes' => $planes, 'uno' => $uno, 'dos' => $dos, 'tres' => $tres));
                }
                else{
                    return $this->redirect($this->generateUrl('public_anuncio_detalles', array('slug' => $producto->getSlug())));
                }
            }

            case 6: {
                $producto = $em->getRepository('AdministracionBundle:Producto')->find($request->query->get('idProducto'));

                if ($producto->getCampannaid() == null) {
                    $producto->setActivo(0);

                    $producto->setRanking(0);
                }

                $em->persist($producto);

                $em->flush();


                return $this->redirect($this->generateUrl('public_anuncio_detalles', array('slug' => $producto->getSlug())));
            }

        }


    }

    public function vendedorTieneDireccionVentaAction(){
    	$usuario=$this->getUser();

    	$direcciones=$usuario->getDireccion();

    	if($direcciones!=null && count($direcciones)>0){
			foreach ($direcciones as $dir){
				if($dir->getDireccionVenta()){
					return new JsonResponse(array(true));
				}
			}

		    return new JsonResponse(array(false));
	    }
    	else{
    		return new JsonResponse(array(false));;
	    }
    }
    
    /**
     * Función general para realizar el get de la propiedad enviada en el request
     * 
     * @param Request $request
     * @param String $fieldValue
     * @return any
     */
    private function getRequestValue(Request $request, $fieldValue) {
        if ($request->getMethod() == 'POST') {
            return $request->get($fieldValue);
        } else {
            return $request->query->get($fieldValue);
        }
    }
    
    /**
     * Función para obtener el repositorio de una clase
     * 
     * @param String $class
     * @return Repository
     */
    private function getRepository($class) {
        return $this->getDoctrine()->getManager()->getRepository($class);
    }
    
    public function busquedageneralAction(Request $request) {
        
        $busqueda = $this->getRequestValue($request, 'valorSearch');
        $maxResults = 5; //máximo de anuncios a mostrar en el buscador
        
        $filters = 
        [
            'estados_slug' => [EstadoProducto::ESTADO_PUBLICADO_SLUG],
            'nombre_o_descripcion' => $busqueda
        ];
        
        $productos = $this->getRepository(Producto::class)->findByFilters($filters, $maxResults);
        
        $productosArray= [];
        
        /** @var Producto $producto **/
        foreach($productos as $producto) {
            
            $imagenDestacada = $producto->getImagenDestacada();
            
            $assetManager = $this->get('templating.helper.assets');
            
            $datosImagenDestacada = $imagenDestacada? $assetManager->getUrl($this->getParameter('uploads_images_productos').$producto->getImagenDestacada()): null;
            
            $datosProducto = [
                'id' => $producto->getId(),
                'nombre' => $producto->getNombre(),
                'descripcion' => $producto->getDescripcion(),
                'imagen_destacada' => $datosImagenDestacada,
                'slug' => $producto->getSlug(),
                'detalle_url' => $this->generateUrl('public_anuncio_detalles', ['slug' => $producto->getSlug() ])
            ];
            
            $productosArray[] = $datosProducto;
            
        } 
        
        return new JsonResponse(['productos' => $productosArray]);
        
    }

    public function buscarAction(Request $request)
    {
        $busqueda = $this->getRequestValue($request, 'valorSearch');
        $total = 0;

        $preciosOferta = [];
        $productos = $this->getRepository(Producto::class)->findBySearchParam($busqueda);
        $total = count($productos);
        $productosOrdenados = array();
        $uso = [];
        $usoTemp = array();
        if (!empty($productos)) {

            //Poner primero los productos que estan en campanna
            foreach ($productos as $prod) {
                if ($prod->getCampannaid() != null && ($prod->getCampannaid()->getEstado($prod->getUsuarioid()) != null && $prod->getCampannaid()->getEstado($prod->getUsuarioid()) == 'publicado')) {
                    $productosOrdenados[] = $prod;
                }
//                    $prod = new Producto();
                if ($prod->getOfertaActiva())
                    $preciosOferta [] = $prod->getPrecioOferta();

                if (is_object($prod->getCondicion()))
                    $uso [] = $prod->getCondicion()->getId();
            }
            $usoTemp = array_unique($usoTemp);
            foreach ($usoTemp as $temp) {
                $us = $this->getDoctrine()->getManager()->getRepository("AdministracionBundle:CondicionProducto")->find($temp);
                $uso [] = array(
                    'id' => $us->getId(),
                    'nombre' => $us->getNombre(),
                    'slug' => $us->getSlug()
                );
            }
            //Agregar los productos restantes

            foreach ($productos as $prod) {
                if ($prod->getCampannaid() == null || ($prod->getCampannaid() != null && $prod->getCampannaid()->getEstado($prod->getUsuarioid()) == 'pausado')) {
                    $productosOrdenados[] = $prod;
                }
            }
        }

        return $this->render('PublicBundle:Anuncio:listado.html.twig', array(
            'productos' => $productosOrdenados,
            'valorSearch' => $busqueda, 'categoriaid' => null, 'listaCategoriasFiltro' => array(),
            "ciudadesProduct" => array(), 'categoriasBread' => array(), 'preciosOferta' => $preciosOferta, 'totalProductos' => $total, 'uso' => $uso));
//        } else {
//            throw new AccessDeniedException("Peticion Incorrecta");
//        }
    }

    public function modificarAction(Request $request, $step)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        if ($request->getMethod() == 'POST') {
            $step = $request->request->get('step');
        }
        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        /*$producto=$em->getRepository('AdministracionBundle:Producto')->findBy(
            array('usuarioid'=>$usuario->getId(),
                'estadoProductoid'=>1),
            array()
        );*/

        $idProducto = $request->query->get('idProducto');

        if ($idProducto == null) {
            $idProducto = $request->request->get('idProducto');
        }

        $producto = $em->getRepository('AdministracionBundle:Producto')->find($idProducto);


        $nav = $request->request->get('nav');


        /* if ($nav==null)
         {
             $nav=$request->query->get('nav');
         }

         if (count($producto)==0)
         {
             $producto=null;
         }
         else
         {
             $producto=$producto[0];

             if($nav!="1" && $request->request->get('idCategoria')==null)
             {
                 $step=$producto->getStep();
             }

         }*/

        //Conocer si el usuario tiene llenados los datos personales y de contacto para activar o no el paso 4

        $usuarioVacio = !$this->get('utilPublic')->UsuarioVacio($usuario);


        switch ($step) {
            case 1: {
                if ($request->getMethod() == 'POST') {
                    $idCategoria = $request->request->get('idCategoria');

                    $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($idCategoria);


                    $producto->setCategoriaid($categoria);

                    $em->persist($producto);

                    $em->flush();

                    return new Response(json_encode(true));
                }

                $categorias = $em->getRepository('AdministracionBundle:Categoria')->findBy(array('nivel' => 1), array());

                $cadenaId = "";

                $selects = "";

                $hilo = "";

                if ($producto != null) {
                    $resultado = $this->construirSelect($producto);

                    $selects = $resultado[0];

                    $cadenaId = $resultado[1];

                    $hilo = $resultado[2];

                    $idCategoria1 = explode('-', $cadenaId);

                    $idCategoria1 = explode(':', $idCategoria1[count($idCategoria1) - 1]);

                    $categoriaHilo = $em->getRepository('AdministracionBundle:Categoria')->find($idCategoria1[0]);

                    $hilo = $categoriaHilo->getCategoriaid()->getNombre() . " > " . $hilo;

                    $hilo = substr($hilo, 0, -2);
                }

                return $this->render('PublicBundle:Anuncio:modificar.html.twig', array('categorias' => $categorias, 'producto' => $producto, 'selects' => $selects, 'cadenaId' => $cadenaId, 'hilo' => $hilo, 'usuarioVacio' => $usuarioVacio));
            }

            case 2: {
                if ($request->getMethod() == 'POST') {

                    //$producto=$em->getRepository('AdministracionBundle:Producto')->find($request->request->get('idProducto'));

                    $producto->setDescripcion($request->request->get('descripcion'));

                    $producto->setNombre($request->request->get('titulo'));

                    $producto->setStep($request->request->get('step'));

                    $condicion = $em->getRepository('AdministracionBundle:CondicionProducto')->find($request->request->get('condicion'));

                    $producto->setCondicion($condicion);

                    $caracteristicas = $request->request->get('caracteristica');

                    $imagenes = $request->request->get('imagenes');

                    $caracteristicasCategoria = $producto->getCategoriaid()->getCaracteristicas();

                    $caraacteristicas = $em->getRepository('AdministracionBundle:ProductoCaracteristicaCategoria')->findBy(array('productoid' => $producto->getId()));

                    //$em->persist($producto);

                    foreach ($caraacteristicas as $carac) {
                        $em->remove($carac);
                    }
                    $em->flush();

                    $cont = 0;
                    foreach ($caracteristicasCategoria as $carac) {
                        $productoCaracteristica = new ProductoCaracteristicaCategoria();
                        $productoCaracteristica->setCaracteristicaCategoriaid($carac);
                        $productoCaracteristica->setProductoid($producto);
                        $productoCaracteristica->setValor($caracteristicas[$cont]);

                        $em->persist($productoCaracteristica);

                        $cont++;
                    }

                    $imgs = $producto->getImagenes();

                    foreach ($imgs as $i) {
                        $i->setDestacada(false);
                        $em->flush();
                    }
                    if ($request->request->has('fotoPerfil')) {
                        $imagen = $em->getRepository('AdministracionBundle:Imagen')->find($request->request->get('fotoPerfil'));

                        $imagen->setDestacada(true);
                    }

                    $em->persist($producto);

                    $em->flush();

                    return new Response(json_encode(true));

                }
                $caraacteristicas = null;
                if ($producto != null) {
                    $caraacteristicas = $em->getRepository('AdministracionBundle:ProductoCaracteristicaCategoria')->findBy(array('productoid' => $producto->getId()));
                }

                $condiciones = $em->getRepository('AdministracionBundle:CondicionProducto')->findAll();

                return $this->render('PublicBundle:Anuncio:modificar_2.html.twig', array('producto' => $producto, 'caraacteristicas' => $caraacteristicas, 'condiciones' => $condiciones, 'usuarioVacio' => $usuarioVacio));
            }


            case 3: {
                $tipoenvio = $em->getRepository('AdministracionBundle:TipoEnvio')->findAll();

                $usuarioFlag = $this->get('utilPublic')->UsuarioVacio($usuario);

                $garantiaUsuario = $usuario->getGarantia();

                if ($request->getMethod() == 'POST') {

                    $producto->setCantidad($request->request->get('cantidad'));

                    $producto->setPrecio($request->request->get('precio'));

                    $enviosEliminar = $producto->getTipoenvioid();


                    if (!empty($enviosEliminar)) {
                        foreach ($enviosEliminar as $te2) {
                            //$te2->removeProductoid($producto);
                            $producto->removeTipoenvioid($te2);
                        }
                    }

                    $em->flush();

                    $tipoenvioArray = [];
                    $seleccionados = $request->request->get('tipoenvio');;


                    $productoArray[] = $producto;

                    foreach ($seleccionados as $te) {
                        $teObject = $em->getRepository('AdministracionBundle:TipoEnvio')->find($te);
                        $producto->addTipoenvioid($teObject);

                    }

                    //$producto->setTipoenvioid($tipoenvioArray);

                    if ($usuarioFlag) {
                        $estadoProducto = $em->getRepository('AdministracionBundle:EstadoProducto')->find(3);

                        $producto->setEstadoProductoid($estadoProducto);
                    }

                    $em->persist($producto);

                    /////////////////////////Objetivo usuario

//                    $hoy=new \DateTime();
//
//                    $objetivoUsuario= new UsuarioObjetivo();
//
//                    $objetivoUsuario->setUsuarioid($producto->getUsuarioid());
//
//                    $objetivo=$em->getRepository('AdministracionBundle:Objetivo')->findBy(array('slug'=>'registro'), array())[0];
//
//                    $objetivoUsuario->setObjetivoid($objetivo);
//
//                    $objetivoUsuario->setPuntos($objetivo->getPuntos());
//
//                    $objetivoUsuario->setFecha($hoy);
//
//                    $em->persist($objetivoUsuario);

                    //////////////////////////


                    $em->flush();

                    return new Response(json_encode(true));
                }


                return $this->render('PublicBundle:Anuncio:modificar_3.html.twig', array(
                    'tipoenvio' => $tipoenvio,
                    'producto' => $producto,
                    'usuarioFlag' => $usuarioFlag,
                    'usuarioVacio' => $usuarioVacio,
                    'garantiaUsuario' => ($garantiaUsuario != null) ? $garantiaUsuario : ''
                ));
            }


            case 4: {
                if ($request->getMethod() == 'POST') {
                    $usuario->setTelefono($request->request->get('telefono'));

                    if ($usuario->getRolid()->getId() == 2) {
                        $usuario->getClienteid()->setNombre($request->request->get('nombre'));

                        $usuario->getClienteid()->setApellidos($request->request->get('apellidos'));

                        $usuario->getClienteid()->setDni($request->request->get('dni'));
                    }
                    if ($usuario->getRoles() == 'ROLE_EMPRESA') {
                        $usuario->getEmpresaid()->setCuit($request->request->get('nombre'));

                        $usuario->getEmpresaid()->setRazonSocial($request->request->get('apellidos'));
                    }

                    /*$estadoProducto=$em->getRepository('AdministracionBundle:EstadoProducto')->find(3);

                    $producto->setEstadoProductoid($estadoProducto);

                    //$producto->setStep(5);

                    $em->persist($producto);*/

                    $em->persist($usuario);

                    $em->flush();

                    return new Response(json_encode(true));
                }
                $provincia = $em->getRepository('AdministracionBundle:Provincia')->findAll();

                $ciudadTemp = null;

                if ($usuario->getDireccionVenta() != null) {
                    $ciudadTemp = $em->getRepository('AdministracionBundle:Ciudad')->findBy(array('provinciaid' => $usuario->getDireccionVenta()->getProvincia()->getId()), array());
                }
                return $this->render('PublicBundle:Anuncio:modificar_4.html.twig', array('usuario' => $usuario, 'provincias' => $provincia, 'usuarioVacio' => $usuarioVacio, 'ciudades' => $ciudadTemp, 'producto' => $producto));

            }
            /*case 5:
            {
                $idProducto=$request->query->get('idProducto');
                if ($idProducto)
                {
                    $producto=$em->getRepository('AdministracionBundle:Producto')->find($idProducto);
                }


                if ($producto)
                {
                    $campannaUser=$em->getRepository('AdministracionBundle:UsuarioCampanna')->findBy(
                        array('usuarioid'=>$producto->getUsuarioid()->getId()), array());
                }


                if ($request->getMethod()=='POST')
                {
                    $producto=$em->getRepository('AdministracionBundle:Producto')->find($request->request->get('idProducto'));

                    $campannaUser=$em->getRepository('AdministracionBundle:UsuarioCampanna')->findBy(
                        array('campannaid'=>$request->request->get('idPlan'),
                            'usuarioid'=>$producto->getUsuarioid()->getId()), array());

                    $campanna=$em->getRepository('AdministracionBundle:Campanna')->find($request->request->get('idPlan'));

                    if(count($campannaUser)==0)
                    {
                        $estadoCampanna=$em->getRepository('AdministracionBundle:EstadoCampanna')->find(1);

                        $usuarioCampanna= new UsuarioCampanna();

                        $usuarioCampanna->setCampannaid($campanna);

                        $usuarioCampanna->setUsuarioid($usuario);

                        $usuarioCampanna->setEstadoCampannaid($estadoCampanna);

                        $hoy=new \DateTime();

                        $usuarioCampanna->setFecha($hoy);

                        $em->persist($usuarioCampanna);
                    }

                    $producto->setCampannaid($campanna);

                    $producto->setActivo(1);

                    $producto->setRanking($campanna->getId());

                    $em->persist($producto);

                    $em->flush();

                    return new Response(json_encode(true));

                }

                $uno =0;

                $dos =0;

                $tres=0;

                foreach ($campannaUser as $cu)
                {
                    switch ($cu->getCampannaid()->getId())
                    {
                        case 1:
                            $uno=1;
                            break;
                        case 2:
                            $dos=1;
                            break;
                        case 3:
                            $tres=1;
                            break;

                    }

                }
                $producto=$em->getRepository('AdministracionBundle:Producto')->find($request->query->get('idProducto'));

                $planes=$em->getRepository('AdministracionBundle:Campanna')->findAll();

                return $this->render('PublicBundle:Publicidad:planes.html.twig',array('idProducto'=>$producto->getId(),'planes'=>$planes ,'uno'=>$uno,'dos'=>$dos,'tres'=>$tres));
            }

            case 6:
            {
                $producto=$em->getRepository('AdministracionBundle:Producto')->find($request->query->get('idProducto'));

                if($producto->getCampannaid()==null)
                {
                    $producto->setActivo(0);

                    $producto->setRanking(0);
                }

                $em->persist($producto);

                $em->flush();


                return $this->redirect($this->generateUrl('public_anuncio_detalles',array('idProducto'=>$producto->getId())));
            }*/

        }


    }

    public function detallesAction(Request $request, $slug)
    {


        $em = $this->getDoctrine()->getManager();
        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        /** @var Producto $producto */
        $producto = $em->getRepository('AdministracionBundle:Producto')->findOneBy(array('slug' => $slug));
        
        if(!$producto || (!$producto->publicado() && $producto->getUsuarioid() != $usuario)) {
            return $this->redirect($this->generateUrl('public_anuncio_listar'));
        }
        
        $usuarioP = $producto->getUsuarioid();

        $preguntas = $em->getRepository('AdministracionBundle:Pregunta')->findBy(array(
            'productoid' => $producto->getId()
        ), array()
        );


        $visita = new Visita();

        $visita->setProductoid($producto);

        $hoy = new \DateTime();

        $visita->setFecha($hoy);

        if ($usuario != null) {
            $visita->setUsuarioid($usuario);
        }

        if (isset($_COOKIE['usuarioCookie'])) {
            $usuarioCookie = $_COOKIE['usuarioCookie'];

            $visita->setUsuarioCookie($usuarioCookie);
        } else {
            $max = $em->createQuery('SELECT MAX(visita.usuarioCookie) as id from AdministracionBundle:Visita visita')->getResult()[0]['id'];

            $url = $this->generateUrl('public_homepage');

            if ($max == null)
                $max = 1;
            else
                $max++;

            setcookie('usuarioCookie', $max, time() + 259200 * 60, $url);

            $visita->setUsuarioCookie($max);

        }

        $em->persist($visita);

        $em->flush();

        if ($producto->getCampannaid()) {
            $campannaUser = $em->getRepository('AdministracionBundle:UsuarioCampanna')->findBy(
                array('campannaid' => $producto->getCampannaid()->getId(),
                    'usuarioid' => $producto->getUsuarioid()->getId()), array());

            if ($campannaUser[0]->getEstadoCampannaid()->getId() == 1) {
                if ($producto->getActivo() == 1) {

                    if ($usuario == null || $usuario->getId() != $producto->getUsuarioid()->getId()) {
                        $inversion = (float)$producto->getInversion() + (float)$producto->getCampannaid()->getCostoVisita();

                        $producto->setInversion($inversion);

                        $productosUsuario = $em->getRepository('AdministracionBundle:Producto')->findBy(array(
                            'usuarioid' => $producto->getUsuarioid()->getId(),
                            'campannaid' => $producto->getCampannaid()->getId()),
                            array()
                        );

                        $inversionTotal = 0;

                        foreach ($productosUsuario as $pu) {
                            $inversionTotal = (float)$inversionTotal + (float)$pu->getInversion();
                        }

                        if ($inversionTotal >= $producto->getCampannaid()->getPresupuesto()) {
                            $estadoCampanna = $em->getRepository('AdministracionBundle:EstadoCampanna')->find(2);

                            foreach ($productosUsuario as $pu) {
                                $pu->setActivo(0);

                                $pu->setRanking(0);

                                $em->persist($pu);
                            }

                            $campannaUser[0]->setEstadoCampannaid($estadoCampanna);

                        }

                        $em->persist($producto);

                        $em->flush();

                    }
                }
            }
        }

        $estadoProductoPublicado = $em->getRepository(EstadoProducto::class)->findOneBySlug(EstadoProducto::ESTADO_PUBLICADO_SLUG);
        
        $productos = $em->getRepository('AdministracionBundle:Producto')->findBy(
            array('usuarioid' => $producto->getUsuarioid()->getId(), 'estadoProductoid' => $estadoProductoPublicado->getId())
        );
        $imagenes = $em->getRepository('AdministracionBundle:Imagen')->findBy(
            array('productoid' => $producto),
            array('destacada' => 'DESC'));

        $categoriasBread = $this->breadCrumb($em->getRepository('AdministracionBundle:Categoria')->find($producto->getCategoriaid()));

        $request = $this->get('request');
        $request->request->set('idProducto', $producto->getId());

        $request->request->set('start', 0);

        $comentarios = $em->getRepository('AdministracionBundle:Valoracion')->findByValoracion($request)->getResult();

        $start = count($comentarios);

        $total = count($em->getRepository('AdministracionBundle:Valoracion')->findByValoracionTotal($request)->getResult());

        $caraacteristicas = $em->getRepository('AdministracionBundle:ProductoCaracteristicaCategoria')->findBy(array('productoid' => $producto->getId()));

        // Verificar si tiene envio por MercadoFree y si es asi, calcular costo envio segun la direccion del usuario

        $costoEnvio = 0;

        if ($producto->getUsuarioid()->hasTipoEnvio('envio-domicilio-vendedor')) {
            $costoEnvio = $this->get('utilPublic')->getCostoEnvio($usuario, $producto->getId());
        }

        //Obtener configuracion

	    $conf=$em->getRepository(Configuracion::class)->findAll()[0];

        //Obtener cantidad de ventas del usuario

	    $pedidos=$em->getRepository(Pedido::class)->findPedidoByVendedor($producto->getUsuarioid()->getId());

	    $pedidosUltimos=array();

	    $fecha = new \DateTime('-3 month');

	    foreach ($pedidos as $ped){
	    	if($ped->getEstado()->getSlug()=='cerrado' && $ped->getFecha() > $fecha){
	    		$pedidosUltimos[]=$ped;
		    }
	    }

        return $this->render('PublicBundle:Anuncio:detalles.html.twig', array(
            'producto' => $producto,
            'caraacteristicas' => $caraacteristicas,
            'productos' => $productos,
            'preguntas' => $preguntas,
            'user' => $usuario,
            'comentarios' => $comentarios,
            'start' => $start,
            'total' => $total,
            'usuarioP' => $usuarioP,
            'categoriasBread' => $categoriasBread,
            'costoEnvio' => $costoEnvio,
            'imagenes' => $imagenes,
	        'conf' => $conf,
	        'cantPedidos' => count($pedidosUltimos)
        ));

    }

    public function listarAction(Request $request)
    {
        $categoriaParam = null;
        $tipo = null;
        $valorSearch = "";
        $listaCategoriasFiltro = array();
        $categoriaid = null;
        $ciudades = array();
        $categoriasBread = array();
        $preciosOferta = array();
        $totalProductos = 0;
        $total = 0;
        $listaProductos = array();
        $categoriasArray = array();

        $em = $this->get('doctrine.orm.entity_manager');
        if ($request->getMethod() == 'POST') {
            $categoriaParam = $request->request->get('slug');
            $tipo = $request->request->get('tipo');
            $valorSearch = $request->request->get('valorSearch', "");

            $categoriaid = $request->request->get('categoriaid', null);
            $start = $request->request->get('start') + $request->request->get('offset');

            $productos = $em->getRepository('AdministracionBundle:Producto')->findByProductoPublic($request)->getResult();


            $productosTotal = $em->getRepository('AdministracionBundle:Producto')->findByProductoPublic($request, false)->getResult();
            $precioMin = (double)$request->request->get('precioMin');

            $precioMax = (double)$request->request->get('precioMax');
            if ($precioMin > 0 || $precioMax > 0) {
                $oferta = $em->getRepository('AdministracionBundle:Producto')->findProductosEnOferta($request, false)->getResult();

                $productos = array_merge($productos, $oferta);

                $productos = array_unique($productos, SORT_REGULAR);

                $productosTotal = array_merge($productosTotal, $oferta);
                $productosTotal = array_unique($productosTotal, SORT_REGULAR);
            }

            $totalProductos = count($productos);
            $total = count($productosTotal);

            $start = $request->request->get('start') + $request->request->get('offset');
            $usuario = $this->get('utilPublic')->getUsuarioLogueado();

            foreach ($productos as $producto) {
                if ($producto->getOfertaActiva() && $producto->getPrecioOferta() < $precioMin && $producto->getPrecioOferta() > $precioMax) {
                    continue;
                }
                $productoArray = [];
                $productoArray[]['id'] = $producto->getId();
                $productoArray['slug'] = $producto->getSlug();
                $productoArray['descripcion'] = $producto->getDescripcion();
                $productoArray['nombre'] = $producto->getNombre() != null ? $producto->getNombre() : "";
                $productoArray['precio'] = $producto->getPrecio() != null ? number_format($producto->getPrecio(),2,',','.') : "";
                $productoArray['cuota_pagar'] = $producto->getCuotaspagar() != null ? $producto->getCuotaspagar() : "";
                $productoArray['cantidad'] = $producto->getCantidad() != null ? $producto->getCantidad() : "";
                $productoArray['categoria'] = $producto->getCategoriaid() != null ? $producto->getCategoriaid()->getNombre() : "";
                $productoArray['usuario'] = $producto->getUsuarioid() != null ? $producto->getUsuarioid()->getEmail() : "";
                $productoArray['estado_producto'] = $producto->getEstadoProductoid() != null ? $producto->getEstadoProductoid()->getNombre() : "";
                $productoArray['imagen'] = $producto->getImagenDestacada() != null ? $producto->getImagenDestacada() : $producto->getImagenes()[0]->geturl();
                $productoArray['precio_oferta'] = (is_object($producto->getOfertaActiva()) ? number_format($producto->getPrecioOferta(),2,',','.') : null);
                $productoArray['en_oferta'] = (is_object($producto->getOfertaActiva()) ? true : false);
                $productoArray['descuento'] = (is_object($producto->getOfertaActiva()) ? $producto->getOfertaActiva()->getPorcientoDescuento() : null);
                $productoArray['envio_domicilio'] = $producto->hasEnvioDomicilio();
                $productoArray['condicion'] = (is_object($producto->getCondicion()) ? $producto->getCondicion()->getNombre() : "");
                $productoArray['url_detalle'] = $this->generateUrl('public_anuncio_detalles', array('slug' => $producto->getSlug()));

                if ($usuario != null && $usuario->isProductoFavorito($producto->getId())) {
                    $productoArray['favorito'] = true;
                } else {
                    $productoArray['favorito'] = false;
                }

                $listaProductos[$producto->getId()] = $productoArray;
                $padre = $this->getCategoriasPadre($producto->getCategoriaid(), $valorSearch);
                $categoriasArray [$padre['id']] = $padre;
            }
            $listaProductos = array_values($listaProductos);

            /***** BREADCRUMB ********/
            if ($categoriaid !== "") {
                $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($categoriaid);

                $catBread = $this->breadCrumb($categoria);

                foreach ($catBread as $catB) {
                    $categoriasBread [] = array(
                        'id' => $catB->getId(),
                        'nombre' => $catB->getNombre(),
                        'slug' => $catB->getSlug(),
                        'url' => $this->get('router')->generate('public_anuncio_listar', array('valorSearch' => $valorSearch, 'slug' => $catB->getSlug(), 'tipo' => 'f'))
                    );
                }

//                $listaCategoriasFiltro = $this->hacerCategoriasFiltro($categoria->getId());
                $children = $categoria->getChildren();

                foreach ($children as $child) {
                    if ($child['productos'] > 0) {
                        $child['url'] = $this->get('router')->generate('public_anuncio_listar', array('slug' => $child['slug'], 'tipo' => 'f', 'valorSearch' => $valorSearch));
                        $listaCategoriasFiltro [] = $child;
                    }
                }
            } else {

                $listaCategoriasFiltro = array_values($categoriasArray);
            }


            /***** CIUDADES *****/

            $ciudades = $em->getRepository('AdministracionBundle:Producto')->findByProductoCiudad($request)->getResult();
            /*******************/
            $ciudadid = $request->request->get('ciudadid', "");
            if ($ciudadid != "") {
                $temp = explode(",", $ciudadid);
                $temp = array_unique($temp);
                $ciudadid = implode(",", $temp);
            }

            $precioOferta = $request->request->get('precioOferta', "");

            return new Response(json_encode(array('tipo' => $tipo, 'precioOferta' => $precioOferta, 'ciudadid' => $ciudadid, 'valorSearch' => $valorSearch, 'productos' => $listaProductos, 'listaCategoriasFiltro' => $listaCategoriasFiltro,
                "ciudadesProduct" => $ciudades, 'categoriaid' => $categoriaid, 'categoriasBread' => $categoriasBread, 'total' => $total, 'totalProductos' => $totalProductos, 'start' => $start)));
        } else {
            $valorSearch = $request->query->get('valorSearch', "");
            $categoriaParam = $request->query->get('slug');
            $categoria = $em->getRepository('AdministracionBundle:Categoria')->findOneBy(array('slug' => $categoriaParam));
            $categoriaid = (is_object($categoria) ? $categoria->getId() : null);
            $tipo = $request->query->get('tipo');
            $categoriasBread = $this->breadCrumb($categoria);
        }
        return $this->render('PublicBundle:Anuncio:listado.html.twig', array('tipo' => $tipo, 'ciudadid' => "", 'valorSearch' => $valorSearch, 'categoriaid' => $categoriaid, 'listaCategoriasFiltro' => $listaCategoriasFiltro,
            "ciudadesProduct" => $ciudades, 'categoriasBread' => $categoriasBread, 'totalProductos' => $totalProductos, 'total' => $total));
    }

    public function construirSelect($producto)
    {
        $categoria = $producto->getCategoriaid();

        $selects = "";
        $cadenaId = "";
        $hilo = "";

        while ($categoria->getNivel() != 1) {
            $categoriaHijas = $categoria->getCategoriaid()->getCategoriaHijas();
            $options = "";
            $optionsSelected = "";
            foreach ($categoriaHijas as $hija) {
                if ($categoria->getId() == $hija->getId()) {
                    $options .= '<option value="' . $hija->getId() . '">' . $hija->getNombre() . '</option>';

                    $cadenaId .= $hija->getId() . ":" . $hija->getNivel() . "-";

                    $hilo = $hija->getNombre() . " > " . $hilo;

                } else {
                    $options .= '<option value="' . $hija->getId() . '">' . $hija->getNombre() . '</option>';
                }
            }

            $tagSelect = '<div class="col-md-3 cajon" id="' . $categoria->getNivel() . '" style="margin-top: 8px">' .
                '<select class="form-control  selectCategorias">' .
                $options .
                '</select>' .
                '</div>';

            $selects = $tagSelect . "" . $selects;

            $categoria = $categoria->getCategoriaid();
        }

        $resultado[] = $selects;
        $cadenaId = substr($cadenaId, 0, -1);
        $resultado[] = $cadenaId;
        $resultado[] = $hilo;


        return $resultado;

    }


    public function idsCategoria($categoria, $ids)
    {

        $ids2 = "";
        if (is_object($categoria)) {

            if (count($categoria->getCategoriaHijas()) != 0) {
                $hijas = $categoria->getCategoriaHijas();

                foreach ($hijas as $hija) {
                    if ($hija->getCantidadProductosPublicados() > 0) {
                        $ids2 = $this->idsCategoria($hija, $ids2);
                    }
                }

            } else if (count($categoria->getProductos()) != 0) {
                $ids2 .= ":" . $categoria->getId() . "-" . ($categoria->getCategoriaid() != null ? $categoria->getCategoriaid()->getId() : 1);

            }
        }

        return $ids .= $ids2;
    }

    public function hacerCategoriasFiltro2($categoriaid)
    {
        $em = $this->getDoctrine()->getManager();
        $listaCategoriasFiltro = [];

        $categoriaFiltro = $em->getRepository('AdministracionBundle:Categoria')->find($categoriaid);
        if ($categoriaFiltro->getCantidadProductosPublicados() > 0) {
            $listaCategoriasFiltro[] = array(
                'id' => $categoriaFiltro->getId(),
                'nivel' => $categoriaFiltro->getNivel(),
                'nombre' => $categoriaFiltro->getNombre(),
                'slug' => $categoriaFiltro->getSlug(),
                'publicados' => $categoriaFiltro->getCantidadProductosPublicados(),
                'url' => $this->get('router')->generate('public_anuncio_listar', array('slug' => $categoriaFiltro->getSlug(), 'tipo' => 'c'))
            );
        }
        foreach ($categoriaFiltro->getCategoriaHijas() as $catH) {
            if ($catH->getCantidadProductosPublicados() > 0) {
                $listaCategoriasFiltro[] = array(
                    'id' => $catH->getId(),
                    'nivel' => $catH->getNivel(),
                    'nombre' => $catH->getNombre(),
                    'slug' => $catH->getSlug(),
                    'publicados' => $catH->getCantidadProductosPublicados(),
                    'url' => $this->get('router')->generate('public_anuncio_listar', array('slug' => $catH->getSlug(), 'tipo' => 'f'))
                );
            }
        }
        return $listaCategoriasFiltro;
    }

    public function hacerCategoriasFiltro($categoriaid, $search = "")
    {
        $em = $this->getDoctrine()->getManager();

        $listaCategoriasFiltro = array();

        $categoriaFiltro = $em->getRepository('AdministracionBundle:Categoria')->find($categoriaid);
        $ids = null;
        foreach ($categoriaFiltro->getCategoriaHijas() as $catH) {
//            if ($catH->getCantidadProductosPublicados() > 0) {
            $ids = $this->idsCategoria($catH, "");
//            }


            if ($ids) {
                $ids = explode(':', $ids);

                $categoriaArray = array();

                foreach ($ids as $id) {
                    if ($id) {
                        $id = explode('-', $id);

                        $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($id[0]);
//                        if ($categoria->getCantidadProductosPublicados() > 0) {
                        $params = array('slug' => $categoria->getSlug(), 'tipo' => 'f');

                        if ($search === "")
                            $params['valorSearch'] = $search;

                        $categoriaArray = array(
                            'id' => $categoria->getId(),
                            'nombre' => $categoria->getNombre(),
                            'nivel' => $categoria->getNivel(),
                            'slug' => $categoria->getSlug(),
                            'url' => $this->get('router')->generate('public_anuncio_listar', $params),
                            'cantidad' => $categoria->getCantidadProductosPublicados()
                        );
                        $listaCategoriasFiltro[] = $categoriaArray;
//                        }
                    }

                }
            }

        }
        return $listaCategoriasFiltro;
    }

    function getCategoriasPadre($categoria, $valorSearch = "")
    {

        while ($categoria->getCategoriaid() != null) {
            $categoria = $categoria->getCategoriaid();
        }
//        return $categoria;
        $params = array('slug' => $categoria->getSlug(), 'tipo' => 'f');
        if ($valorSearch !== "")
            $params['valorSearch'] = $valorSearch;
        return array(
            'id' => $categoria->getId(),
            'nombre' => $categoria->getNombre(),
            'slug' => $categoria->getSlug(),
            'url' => $this->get('router')->generate('public_anuncio_listar', $params));
    }

//    public function getCategoriasPadre($categoria){
//            $nivel = $categoria->getNivel();
//            if($nivel === 1){
//               return $categoria->getId();
//            }
//            else {
//                $padre = $categoria->getCategoriaid();
//                if (is_object($padre)) {
//                    $this->getCategoriasPadre($padre);
//                }
//            }
//        return $result;

//    }

    public function getCaminoCategoria2($categoria, $camino = array(), $cantidadProductos = 0)
    {

        if (is_object($categoria)) {
            $nivel = $categoria->getNivel();

            if ($nivel === 1) {
                $cantidadProductos = $categoria->getCantidadProductosPublicados();
                $camino [] = array('categoria' => $categoria, 'productos' => $cantidadProductos);
                return $camino;
            } else {
                $padre = $categoria->getCategoriaid();
                if (is_object($padre)) {
                    $cantidadProductos += $padre->getCantidadProductosPublicados();
                    $camino[] = array('categoria' => $padre, 'productos' => $cantidadProductos);
                    $camino[] = $this->getCaminoCategoria2($padre, $camino, $cantidadProductos);
                }
            }
            return $camino;
        }
        return array();
    }

    public function getCaminoCategoria($categoria, $camino = array(), $cantidadProductos = 0)
    {

        if (is_object($categoria)) {
            $nivel = $categoria->getNivel();
            $hijos = $categoria->getCategoriaHijas();
            if ($nivel == 1) {
                $cantidadProductos += $categoria->getCantidadProductosPublicados();
                $camino = array(
                    'id' => $categoria->getId(),
                    'nivel' => $nivel,
                    'nombre' => $categoria->getNombre(),
                    'slug' => $categoria->getSlug(),
                    'url' => $this->get('router')->generate('public_anuncio_listar', array('slug' => $categoria->getSlug(), 'tipo' => 'f')),
                    'productos' => $cantidadProductos
                );
                return $camino;
            } else {
                foreach ($hijos as $hijo) {
                    $cantidadProductos += $hijo->getCantidadProductosPublicados();
                    $camino = array(
                        'id' => $hijo->getId(),
                        'nivel' => $hijo->getNivel(),
                        'nombre' => $hijo->getNombre(),
                        'slug' => $hijo->getSlug(),
                        'url' => $this->get('router')->generate('public_anuncio_listar', array('slug' => $categoria->getSlug(), 'tipo' => 'f')),
                        'productos' => $cantidadProductos
                    );
                    $camino = $this->getCaminoCategoria($hijo, $camino, $cantidadProductos);
                }
            }
        }
        return $camino;
    }

    public function obtenerCategoriaPadre2($categoria)
    {

        $catNivel = "";
        if (is_object($categoria)) {
            $categoriaPadre = $categoria->getCategoriaid();

            if (is_object($categoriaPadre)) {
                $nivel = $categoriaPadre->getNivel();

                if ($nivel == 1) {
                    return $categoriaPadre->getCategoriaid()->getId();
                } else {
                    if (is_object($categoriaPadre->getCategoriaid())) {

                        $catNivel[] = $this->obtenerCategoriaPadre2($categoriaPadre->getCategoriaid());

                    }
                }
            }
        }
        return $catNivel;
    }

    public function breadCrumb($categoria)
    {
        $categoriaArray = [];
        if (is_object($categoria)) {

            $categoriaTemp = $categoria;
            while ($categoriaTemp->getNivel() != 1) {
                $categoriaArray[] = $categoriaTemp;

                $categoriaTemp = $categoriaTemp->getCategoriaid();
            }

            $categoriaArray[] = $categoriaTemp;
            $categoriaArrayTemp = [];
            for ($i = (count($categoriaArray) - 1); $i >= 0; $i--) {
                $categoriaArrayTemp[] = $categoriaArray[$i];
            }

            $categoriaArray = $categoriaArrayTemp;
        }

        return $categoriaArray;

    }

    public function breadCrumb2($categoria)
    {
        $categoriaArray = [];
        if (is_object($categoria)) {

            $categoriaTemp = $categoria;
            while ($categoriaTemp->getNivel() > 1) {
                $categoriaArray[] = $categoriaTemp;

                $categoriaTemp = $categoriaTemp->getCategoriaid();
            }

            $categoriaArray[] = $categoriaTemp;
            $categoriaArrayTemp = [];
            for ($i = (count($categoriaArray) - 1); $i >= 0; $i--) {
                $categoriaArrayTemp[] = $categoriaArray[$i];
            }

            $categoriaArray = $categoriaArrayTemp;
        }

        return $categoriaArray;

    }

    public function paginarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $total = $request->request->get('total');
        $start = $request->request->get('start');
        if ($start < $total) {
            $productos = $em->getRepository('AdministracionBundle:Producto')->findByProductoPublic($request)->getResult();

            $productosTotal = $em->getRepository('AdministracionBundle:Producto')->findByProductoPublicTotal($request)->getResult();

            $listaProductos = [];
            $usuario = $this->get('utilPublic')->getUsuarioLogueado();
            foreach ($productos as $producto) {
                $productoArray = [];

                $productoArray['id'] = $producto->getId();
                $productoArray['slug'] = $producto->getSlug();
                $productoArray['nombre'] = $producto->getNombre() != null ? $producto->getNombre() : "";
                $productoArray['precio'] = $producto->getPrecio() != null ? number_format($producto->getPrecio(), 2,',','.') : "";
                $productoArray['cuota_pagar'] = $producto->getCuotaspagar() != null ? $producto->getCuotaspagar() : "";
                $productoArray['cantidad'] = $producto->getCantidad() != null ? $producto->getCantidad() : "";
                $productoArray['categoria'] = $producto->getCategoriaid() != null ? $producto->getCategoriaid()->getNombre() : "";
                $productoArray['usuario'] = $producto->getUsuarioid() != null ? $producto->getUsuarioid()->getEmail() : "";
                $productoArray['estado'] = $producto->getEstadoProductoid() != null ? $producto->getEstadoProductoid()->getNombre() : "";
                $productoArray['imagen'] = $producto->getImagenDestacada() != null ? $producto->getImagenDestacada() : "";
                $productoArray['visitas'] = count($producto->getVisitas());
                $productoArray['inversion'] = $producto->getInversion() == null ? 0 : $producto->getInversion();
                $productoArray['activo'] = $producto->getActivo();
                $productoArray['estado_producto'] = $producto->getEstadoProductoid() != null ? $producto->getEstadoProductoid()->getNombre() : "";
                $productoArray['imagen'] = $producto->getImagenDestacada() != null ? $producto->getImagenDestacada() : "";
                $productoArray['precio_oferta'] = (is_object($producto->getOfertaActiva()) ? number_format($producto->getPrecioOferta(),2,',','.') : null);
                $productoArray['en_oferta'] = (is_object($producto->getOfertaActiva()) ? true : false);
                $productoArray['descuento'] = (is_object($producto->getOfertaActiva()) ? $producto->getOfertaActiva()->getPorcientoDescuento() : null);
                $productoArray['envio_domicilio'] = $producto->hasEnvioDomicilio();
                $productoArray['url_detalle'] = $this->generateUrl('public_anuncio_detalles', array('slug' => $producto->getSlug()));

                if ($usuario != null && $usuario->isProductoFavorito($producto->getId())) {
                    $productoArray['favorito'] = true;
                } else {
                    $productoArray['favorito'] = false;
                }


                $listaProductos[] = $productoArray;
            }

            $start += count($productos);

            return new Response(json_encode(array('productos' => $listaProductos, 'total' => count($productosTotal), 'start' => $start)));
        }

    }

    public function panelusuarioanuncioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $start = $request->request->get('start');

        $total = $request->request->get('total');

        if ($start < $total) {
            $productos = $em->getRepository('AdministracionBundle:Producto')->findByProductoPanelUsuario($request)->getResult();

            $listaProductos = [];

            /** @var Producto $producto */
            foreach ($productos as $producto) {
                $productoArray = [];

                $productoArray[] = $producto->getId();
                $productoArray[] = $producto->getNombre() != null ? $producto->getNombre() : "";
                //$productoArray[] = $producto->getPrecio() != null ? number_format($producto->getPrecio(), 2) : "";
                $productoArray[] = $producto->getPrecio() != null ? $producto->getPrecio() : "";
                $productoArray[] = $producto->getCuotaspagar() != null ? $producto->getCuotaspagar() : "";
                $productoArray[] = $producto->getCantidad() != null ? $producto->getCantidad() : "";
                $productoArray[] = $producto->getCategoriaid() != null ? $producto->getCategoriaid()->getNombre() : "";
                $productoArray[] = $producto->getUsuarioid() != null ? $producto->getUsuarioid()->getEmail() : "";
                $productoArray[] = $producto->getEstadoProductoid() != null ? $producto->getEstadoProductoid()->getNombre() : "";
                $productoArray[] = $producto->getImagenDestacada() != null ? $producto->getImagenDestacada() : $producto->getImagenes()[0]->getUrl();
                $productoArray[] = count($producto->getVisitas());
                $productoArray[] = $producto->getInversion() == null ? 0 : $producto->getInversion();
                $productoArray[] = $producto->getActivo();
                $productoArray[] = count($producto->getVisitas());
                $productoArray[] = $producto->getSlug();
                
                $fechaExpiracion = $producto->getFechaExpiracion();
                if($fechaExpiracion && !$producto->estadoFinalizado()) {
                    $productoArray[] = "Finaliza el " . $fechaExpiracion->format('d/m/Y');
                } else {
                    $productoArray[] = "";
                }


                $listaProductos[] = $productoArray;
            }

            $start += count($productos);

            return new Response(json_encode(array('productos' => $listaProductos, 'total' => $total, 'start' => $start)));
        }

    }

    public function favoritoAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $slug = $request->request->get('idProducto');

            if ($slug != null && $slug != "") {
                $em = $this->getDoctrine()->getManager();

                $producto = $em->getRepository('AdministracionBundle:Producto')->findOneBy(array('slug' => $slug));

                if ($producto != null) {
                    $usuario = $this->get('utilPublic')->getUsuarioLogueado();
                    $idProducto = $producto->getId();

                    if ($usuario == null) {
                        return new Response(json_encode(array(false, 'Para añadir un producto como favorito debe estar logueado')));
                    } else if ($usuario->isProductoFavorito($idProducto)) {
                        return new Response(json_encode(array(false, 'Ya tienes ese producto como favorito')));
                    } else {
                        $usuario->addProductoFavorito($producto);
                        $em->persist($usuario);
                        $em->flush();

                        if (count($usuario->getProductosFavoritos()) >= 3) {
                            $usuarioObjetivo = $em->getRepository('AdministracionBundle:UsuarioObjetivo')->findBy(array('objetivoid' => 4));

                            if (count($usuarioObjetivo) == 0) {
                                $hoy = new \DateTime();

                                $objetivoUsuario = new UsuarioObjetivo();

                                $objetivoUsuario->setUsuarioid($usuario);

                                $objetivo = $em->getRepository('AdministracionBundle:Objetivo')->findBy(array('slug' => 'marcar_al_menos_tres_productos_como_favoritos'), array())[0];

                                $objetivoUsuario->setObjetivoid($objetivo);

                                $objetivoUsuario->setPuntos($objetivo->getPuntos());

                                $objetivoUsuario->setFecha($hoy);

                                $em->persist($objetivoUsuario);

                                $em->flush();

                                $usuario->setPuntos($usuario->getPuntos() + $objetivo->getPuntos());
                            }

                        }

                        return new Response(json_encode(array(true)));
                    }
                } else {
                    return new Response(json_encode(array(false, 'El producto especificado no existe')));
                }
            } else {
                return new Response(json_encode(array(false, 'Debe especificar un producto para añadir a favoritos')));
            }

        } else {
            return new Response(json_encode(array(false, 'Petición incorrecta')));
        }
    }

    public function pausarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ids = $request->request->get('idsProducto');

        $estado = $request->request->get('estado');

        $procedencia = $request->request->get('procedencia');

        if ($procedencia) {
            $ids = substr($ids, 1, strlen($ids));
        }

        $query = 'UPDATE AdministracionBundle:Producto producto SET producto.activo = ' . $estado . ' WHERE producto.id IN (' . $ids . ')';

        $max = $em->createQuery($query)->getResult();

        return new Response(json_encode(true));
    }

    public function cambiarestadoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->request->get('idProducto');

        $producto = $em->getRepository('AdministracionBundle:Producto')->find($id);

        $estadoAnterior = $producto->getEstadoProductoid()->getId();

        $estado = null;

        if ($estadoAnterior == 3) {
            $estado = $em->getRepository('AdministracionBundle:EstadoProducto')->find(4);
        } else if ($estadoAnterior == 4) {
            $estado = $em->getRepository('AdministracionBundle:EstadoProducto')->find(3);
        }

        $producto->setEstadoProductoid($estado);

        $em->persist($producto);

        $em->flush();

        return new Response(json_encode($estadoAnterior));

    }

    public function obtenerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ids = $request->request->get('idsProducto');

        $campannaid = $request->request->get('campannaid');

        $ids = substr($ids, 1, strlen($ids));

        $campanna = $em->getRepository('AdministracionBundle:Campanna')->find($campannaid);

        $query = 'select producto from  AdministracionBundle:Producto producto WHERE producto.id IN (' . $ids . ')';

        $productos = $em->createQuery($query)->getResult();

        foreach ($productos as $producto) {
            $producto->setCampannaid($campanna);

            $producto->setActivo(1);

            $em->persist($producto);
        }

        $em->flush();

        $listaProductos = [];

        foreach ($productos as $producto) {
            $productoArray = [];

            $productoArray[] = $producto->getId();
            $productoArray[] = $producto->getNombre() != null ? $producto->getNombre() : "";
            $productoArray[] = $producto->getPrecio() != null ? number_format($producto->getPrecio(), 2,',','.') : "";
            $productoArray[] = $producto->getCuotaspagar() != null ? $producto->getCuotaspagar() : "";
            $productoArray[] = $producto->getCantidad() != null ? $producto->getCantidad() : "";
            $productoArray[] = $producto->getCategoriaid() != null ? $producto->getCategoriaid()->getNombre() : "";
            $productoArray[] = $producto->getUsuarioid() != null ? $producto->getUsuarioid()->getEmail() : "";
            $productoArray[] = $producto->getEstadoProductoid() != null ? $producto->getEstadoProductoid()->getNombre() : "";
            $productoArray[] = $producto->getImagenDestacada() != null ? $producto->getImagenDestacada() : "";
            $productoArray[] = count($producto->getVisitas());
            $productoArray[] = $producto->getInversion() == null ? 0 : $producto->getInversion();
            $productoArray[] = $producto->getActivo();

            $listaProductos[] = $productoArray;
        }

        return new Response(json_encode(array('productos' => $listaProductos)));

    }

    public function eliminarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ids = $request->request->get('idsProducto');

        $ids = substr($ids, 1, strlen($ids));

        $query = 'select producto from  AdministracionBundle:Producto producto WHERE producto.id IN (' . $ids . ')';

        $productos = $em->createQuery($query)->getResult();

        //Aki es donde genero el pago de los productos seleccionados

        $query = 'UPDATE AdministracionBundle:Producto producto SET producto.campannaid = NULL, producto.inversion = 0, producto.ranking = 0, producto.activo = 0 WHERE producto.id IN (' . $ids . ')';

        $em->createQuery($query)->getResult();

        return new Response(json_encode(true));

    }

    public function panelusuarioeliminarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ids = $request->request->get('idProducto');

        $indicador = $request->request->get('indicador');
        
        $usuario = $this->getUser();

        if ($indicador == 1) {
            $ids = substr($ids, 1, strlen($ids));
        }
        $query = 'SELECT producto FROM  AdministracionBundle:Producto producto WHERE producto.id IN (' . $ids . ')';

        $productos = $em->createQuery($query)->getResult();
        
        /** @var Producto $p */
        foreach ($productos as $p) {
            
            if($p->getUsuarioid() == $usuario && !$p->publicado()) {
                $p->setBorrado(true);
                $em->persist($p);
            }
        }

        $em->flush();

        return new Response(json_encode(true));

    }

    public function nocampannaAction()
    {
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if (!$usuario) {
            return $this->redirect($this->generateUrl('public_login'));
        }

        $listaProductos = [];

        $productos = $em->getRepository('AdministracionBundle:Producto')->findBy(array('campannaid' => null, 'usuarioid' => $usuario->getId()), array());

        foreach ($productos as $producto) {
            $productoArray = [];

            $productoArray[] = $producto->getId();
            $productoArray[] = $producto->getNombre() != null ? $producto->getNombre() : "";
            $productoArray[] = $producto->getPrecio() != null ? number_format($producto->getPrecio(), 2) : "";
            $productoArray[] = $producto->getCuotaspagar() != null ? $producto->getCuotaspagar() : "";
            $productoArray[] = $producto->getCantidad() != null ? $producto->getCantidad() : "";
            $productoArray[] = $producto->getCategoriaid() != null ? $producto->getCategoriaid()->getNombre() : "";
            $productoArray[] = $producto->getUsuarioid() != null ? $producto->getUsuarioid()->getEmail() : "";
            $productoArray[] = $producto->getEstadoProductoid() != null ? $producto->getEstadoProductoid()->getNombre() : "";
            $productoArray[] = $producto->getImagenDestacada() != null ? $producto->getImagenDestacada() : "";
            $productoArray[] = count($producto->getVisitas());
            $productoArray[] = $producto->getInversion() == null ? 0 : $producto->getInversion();
            $productoArray[] = $producto->getActivo();

            $listaProductos[] = $productoArray;
        }

        return new Response(json_encode(array('productos' => $listaProductos)));

    }

}
