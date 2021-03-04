<?php

namespace PublicBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $configuracion = $em->getRepository('AdministracionBundle:Configuracion')->findAll();

        if (count($configuracion) > 0) {
            $configuracion = $configuracion[0];
        }

        $imagenesHome = $em->getRepository('AdministracionBundle:Imagen')->findBy(
            array('sliderHome' => 1),
            array()
        );
        $productosOfertas = [];

        $coleccion = [];

        if ($configuracion != null && $configuracion->isColecciones() == 1) {
            $coleccion = $em->getRepository('AdministracionBundle:Coleccion')->findAll();

            if (count($coleccion) > 0) {
                $productoTemp = $coleccion[0]->getProductos();

                $productoTempRanking3 = [];

                $productoTempRankingDemas = [];

                for ($i = 0; $i < count($productoTemp); $i++) {
                    if ($i < 8) {
                        if ($productoTemp[$i]->getRanking() == 3) {
                            $productoTempRanking3[] = $productoTemp[$i];
                        } else {
                            $productoTempRankingDemas[] = $productoTemp[$i];
                        }
                    } else {
                        break;
                    }


                }

                $coleccion[0]->setProductos($productoTemp);
            }

        }

        if ($configuracion != null && $configuracion->isOfertasSemana() == 1) {
            $date = strtotime(date("Y-m-d"));

            $first = strtotime('last Sunday');

            $last = strtotime('next Saturday');

            $first = date("Y-m-d", $first);

            $last = date("Y-m-d", $last);

            $productosOfertas = $em->getRepository('AdministracionBundle:Oferta')->findByProductoOfertaSemana($first, $last, 0)->getResult();

            $prodOfertaRanking2 = [];

            $prodOfertaRanking3 = [];

            $prodOfertaRanking = [];

            foreach ($productosOfertas as $po) {
                switch ($po->getRanking()) {
                    case 2:
                        $prodOfertaRanking2[] = $po;
                        break;
                    case 3:
                        $prodOfertaRanking3[] = $po;
                        break;
                    default:
                        $prodOfertaRanking[] = $po;
                        break;
                }
            }

            $productosOfertas = [];

            $productosOfertas += $prodOfertaRanking3;

            foreach ($prodOfertaRanking2 as $pr) {
                $productosOfertas[] = $pr;
            }
            foreach ($prodOfertaRanking as $pr) {
                $productosOfertas[] = $pr;
            }

        }
        $productosRanking3 = $em->getRepository('AdministracionBundle:Producto')->findBy(array('ranking' => 3));

        $productoBanner = null;

        $productoInteres = [];
        if ($configuracion != null) {

            if (count($productosRanking3) > 0) {
                $count = count($productosRanking3) - 1;

                if ($configuracion->isPublicidadOferta() == 1) {
                    $interes1 = random_int(0, $count);

                    $productoInteres[] = $productosRanking3[$interes1];

                    $interes2 = random_int(0, $count);

                    $productoInteres[] = $productosRanking3[$interes2];

                }

                if ($configuracion->isPublicidadProducto() == 1) {
                    $banner = random_int(0, $count);

                    $productoBanner = $productosRanking3[$banner];
                }


            }
        }

        //ultimas categorias visitadas

        $historial = [];


        if ($configuracion != null && $configuracion->isHistorialVisitasCategoria() == 1) {
            if (isset($_COOKIE['categoriasCookie'])) {
                $categoriasCookie = json_decode($_COOKIE['categoriasCookie'], true);

                $categoriasCookie = array_values(array_unique($categoriasCookie));

                $categoriasCookieTemp = $categoriasCookie;

                $arrayCategoria = [];

                $result = $this->EliminarPadres($categoriasCookieTemp, $arrayCategoria, -1);


                //modifico las cookies

                $result = array_reverse($result);

                $cont = 1;

                $arrayId = [];

                foreach ($result as $ca) {
                    if ($ca) {
                        $arrayId[] = $ca->getId();

                        if ($cont <= 3) {
                            $productos = $em->getRepository('AdministracionBundle:Producto')->findByProductoHomePage($ca)->getResult();

                            $arrayCat = [];

                            $arrayCat[] = $ca->getNombre();

                            $arrayCat[] = $productos;

                            $historial[] = $arrayCat;
                        }
                        $cont++;
                    }

                }
                $arrayId = array_reverse($arrayId);

                setcookie('categoriasCookie', json_encode($arrayId), time() + 365 * 24 * 60, $this->generateUrl('public_homepage'));
            }

        }


//        return $this->render('PublicBundle:Default:index.html.twig',
//            array('imagenesSlider'=>$imagenesHome,
//                   'colecciones'=>$coleccion,
//                   'coleccion'=>$coleccion,
//                   'productosOfertas'=>$productosOfertas,
//                   'productoBanner'=>$productoBanner,
//                   'productoInteres'=>$productoInteres,
//                   'historial'=>$historial
//
//                ));

        return $this->render('PublicBundle:Default:static-home.html.twig',
            array(
                'imagenesSlider' => $imagenesHome
            ));
    }

    public function EliminarPadres($categoriasCookies, $categoriaArray, $pos)
    {
        $pos++;
        $em = $this->getDoctrine()->getManager();

        $flag = false;
        $categoria = null;
        if (count($categoriasCookies) > 1) {
            if ($categoriasCookies[$pos] !== null || $categoriasCookies[$pos] != "") {

                $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($categoriasCookies[$pos]);

                if (is_object($categoria)) {
                    if (count($categoria->getCategoriaHijas()) > 0) {
                        $catHijas = $categoria->getCategoriaHijas();

                        for ($j = 0; $j < count($catHijas); $j++) {
                            if ($catHijas[$j]->getId() == $categoriasCookies[$pos + 1]) {
                                unset($categoriasCookies[$pos]);
                                //$pos++;
                                $categoriaArray += $this->EliminarPadres($categoriasCookies, $categoriaArray, $pos);
                                $flag = true;
                                break;
                            }

                        }
                    }
                }
            }
            if ($flag == false) {
                unset($categoriasCookies[$pos]);

                $categoriaArray[] = $categoria;

                $categoriaArray += $this->EliminarPadres($categoriasCookies, $categoriaArray, $pos);
            }

        } else if (count($categoriasCookies) == 1 && $categoriasCookies[$pos] != null) {
            $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($categoriasCookies[$pos]);

            unset($categoriasCookies[$pos]);

            $categoriaArray[] = $categoria;

            return $categoriaArray;
        }


        return $categoriaArray;


    }


    public function loginAction(Request $request)
    {
        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if ($usuario) {
            return $this->redirect($this->generateUrl('public_homepage'));
        }

        $sesion = $request->getSession();
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );
        return $this->render('PublicBundle:Default:login.html.twig', array(
            'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
            'error' => $error
        ));

    }

    public function menuAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categorias = $em->getRepository('AdministracionBundle:Categoria')->findBy(
            array("nivel" => 1),
            array()
        );

        return $this->render('PublicBundle:Templates:menuCategoria.html.twig', array(
            'categorias' => $categorias
        ));

    }

    public function requestResetPasswordAction(){
        return $this->render('@Public/Default/recuperar_password.html.twig');
    }

}
