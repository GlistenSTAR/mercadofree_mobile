<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 16/02/2018
 * Time: 11:34 AM
 */

namespace AdministracionBundle\Controller;

use AdministracionBundle\Entity\CaracteristicaCategoria;
use AdministracionBundle\Entity\Categoria;
use AdministracionBundle\Entity\DetallePedido;
use AdministracionBundle\Entity\Pedido;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriaController extends Controller
{

    public function listarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $roles = $em->getRepository('AdministracionBundle:Rol')->findAll();
        if ($request->getMethod() == 'POST') {
            $nivel = $request->request->get("nivel");

            if ($nivel == 1) {
                $categorias = $em->getRepository('AdministracionBundle:Categoria')->findBy(
                    array("nivel" => $nivel),
                    array()
                );
            } else {
                $idCategoriaPadre = $request->request->get("idCategoriaPadre");
                $categorias = $em->getRepository('AdministracionBundle:Categoria')->findBy(
                    array(
                        "nivel" => $nivel,
                        "categoriaid" => $idCategoriaPadre
                    ),
                    array()
                );
            }

            $listaCategoria = array();
            $categoriasArray = array();
            foreach ($categorias as $categoria) {

                $categoriasArray['nombre'] = $categoria->getNombre();
                $categoriasArray['nivel'] = $categoria->getNivel();
                $categoriasArray['id'] = $categoria->getId();
                $categoriasArray['icono'] = $categoria->getIcono();
                $categoriasArray['idPadre'] = (is_object($categoria->getCategoriaid()) ? $categoria->getCategoriaid()->getId() : null);

                $listaCategoria[] = $categoriasArray;

            }
            //categoria padre

            $idCategoriaPadre = $request->request->get("idCategoriaPadre");
            $categoriaPadreArray = [];

            if ($idCategoriaPadre != null && $idCategoriaPadre != 0) {
                $categoriaPadre = $em->getRepository('AdministracionBundle:Categoria')->find($idCategoriaPadre);

                if (is_object($categoriaPadre)) {
                    $categoriaPadreArray['nombre'] = $categoriaPadre->getNombre();
                    $categoriaPadreArray['nivel'] = $categoriaPadre->getNivel();
                    $categoriaPadreArray['id'] = $categoriaPadre->getId();
                    $categoriaPadreArray['icono'] = $categoriaPadre->getIcono();
                    $categoriaPadreArray['idPadre'] = (is_object($categoriaPadre->getCategoriaid()) ? $categoriaPadre->getCategoriaid()->getId() : null);

                    $caracteristicaArray = [];
                    $caracteristicas = $categoriaPadre->getCaracteristicas();
                    if (count($caracteristicas) != 0) {
                        foreach ($caracteristicas as $caracteristica) {
                            $caracteristicaArray[] = $caracteristica->getNombre();
                        }
                    }


                    $categoriaPadreArray[] = $caracteristicaArray;
                }
            }

            return new Response(json_encode(array("categorias" => $listaCategoria, "categoriaPadre" => $categoriaPadreArray)));

        }
        return $this->render('AdministracionBundle:Categoria:listado.html.twig', array("roles" => $roles));
    }


    public function adicionarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $modoAdicionar = $request->request->get('modoAdicionar');

        if ($modoAdicionar == null) {
            $modoAdicionar = $request->query->get('modoAdicionar');
        }

        if ($modoAdicionar == 1) {
            $nivel = $request->request->get('nivel');

            $idCategoria = $request->request->get('idCategoria');

            $nombreCategoria = $request->request->get('nombreCategoria');

            $iconoCategoria = $request->request->get('iconoCategoria');

            $categorisTodas = $em->getRepository('AdministracionBundle:Categoria')->findAll();

            foreach ($categorisTodas as $categoriaT) {
                if ($categoriaT->getNombre() == $nombreCategoria) {
                    return new Response(json_encode(false));
                }
            }

            $categoria = new Categoria();

            $categoria->setNombre($nombreCategoria);

            $categoria->setIcono($iconoCategoria);

            $categoria->setSlug($this->get('util')->generateSlug($nombreCategoria));

            $categoriaPadre = $em->getRepository('AdministracionBundle:Categoria')->find($idCategoria);

            if ($nivel == 1 && $idCategoria == 0) {
                $categoria->setNivel($nivel);
            } else {
                $categoria->setCategoriaid($categoriaPadre);

                $nivel++;

                $categoria->setNivel($nivel);
                if (count($categoriaPadre->getCaracteristicas()) > 0) {
                    $caracteristicasArray = [];
                    foreach ($categoriaPadre->getCaracteristicas() as $carac) {
                        $caracteristicasObj = new CaracteristicaCategoria();
                        $caracteristicasObj->setNombre($carac->getNombre());
                        $caracteristicasObj->setCategoriaid($categoria);

                        $em->persist($caracteristicasObj);
                        $caracteristicasArray[] = $caracteristicasObj;
                    }
                    $categoria->setCaracteristicas($caracteristicasArray);
                }


            }
            $em->persist($categoria);
            $em->flush();

            return new Response(json_encode(true));
        } else if ($modoAdicionar == 2) {
            $nivelObtener = $request->request->get('nivel');

            if ($nivelObtener == 1) {
                $nivelMax = $em->getRepository('AdministracionBundle:Categoria')->findMaxLevel()->getResult()[0][1];

                return new Response(json_encode($nivelMax));
            }

            return $this->render('AdministracionBundle:Categoria:adicionar.html.twig', array("roles" => ""));
        } else if ($modoAdicionar == 3) {
            $nivel = $request->request->get('nivelCategoriaAdicionar');

            $idCategoriaPadre = $request->request->get('nombreCategoriaPadreAdicionar');

            $nombreCategoria = $request->request->get('nombreCategoriaAdicionar');

            $iconoCategoria = $request->request->get('iconoCategoriaAdicionar');
            
            $tiempoExpiracionCategoria = $request->request->get('tiempoExpiracionCategoriaAdicionar');

            $caracteristicasAdicionar = $request->request->get('caracteristicasAdicionar');

            $caracteristicasPadre = $request->request->get('caracteristicasPadre');

            $categoria = new Categoria();

            $categoria->setNombre($nombreCategoria);

            $categoria->setIcono($iconoCategoria);
            
            $categoria->setTiempoExpiracion($tiempoExpiracionCategoria);

            $categoria->setSlug($this->get('util')->generateSlug($nombreCategoria));

            $categoria->setNivel($nivel);


            if ($nivel != 1) {
                $categoriaPadre = $em->getRepository('AdministracionBundle:Categoria')->find($idCategoriaPadre);

                $categoria->setCategoriaid($categoriaPadre);
            }

            if ($caracteristicasAdicionar != null && count($caracteristicasAdicionar) > 0) {
                $caracteristicasArray = [];
                foreach ($caracteristicasAdicionar as $carac) {
                    $caracteristicasObj = new CaracteristicaCategoria();
                    $caracteristicasObj->setNombre($carac);
                    $caracteristicasObj->setCategoriaid($categoria);

                    $em->persist($caracteristicasObj);
                    $caracteristicasArray[] = $caracteristicasObj;
                }

                foreach ($caracteristicasPadre as $carac) {
                    $caracteristicasObj = new CaracteristicaCategoria();
                    $caracteristicasObj->setNombre($carac);
                    $caracteristicasObj->setCategoriaid($categoria);

                    $em->persist($caracteristicasObj);
                    $caracteristicasArray[] = $caracteristicasObj;
                }

                $categoria->setCaracteristicas($caracteristicasArray);

                $em->persist($categoria);
            }


            $em->persist($categoria);
            $em->flush();

            return new Response(json_encode(true));

        }


    }

    public function addcaracAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ultimaCategoria = $request->request->get('ultimaCategoriaSelected');

        $caracteristicas = $request->request->get('caracteristicas');

        $ultimaCategoria = explode(":", $ultimaCategoria);

        $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($ultimaCategoria[0]);

        foreach ($categoria->getCaracteristicas() as $caract) {
            $em->remove($caract);
        }
        $em->flush();

        $categoriaHijas = $em->getRepository('AdministracionBundle:Categoria')->findBy(array('categoriaid' => $categoria));

        foreach ($categoriaHijas as $cateHijas) {
            foreach ($caracteristicas as $carac) {
                $flag = false;
                foreach ($cateHijas->getCaracteristicas() as $carac2) {
                    if ($carac == $carac2->getNombre()) {
                        $flag = true;
                    }
                }

                if ($flag == false) {
                    $caracteristicasObj = new CaracteristicaCategoria();
                    $caracteristicasObj->setNombre($carac);
                    $caracteristicasObj->setCategoriaid($cateHijas);

                    $em->persist($caracteristicasObj);
                }
            }
            $em->persist($cateHijas);

        }
        $em->flush();

        $caracteristicasArray = [];
        foreach ($caracteristicas as $carac) {
            $caracteristicasObj = new CaracteristicaCategoria();
            $caracteristicasObj->setNombre($carac);
            $caracteristicasObj->setSlug($this->get('util')->generateSlug($carac));
            $caracteristicasObj->setCategoriaid($categoria);

            $em->persist($caracteristicasObj);
            $caracteristicasArray[] = $caracteristicasObj;
        }

        $categoria->setCaracteristicas($caracteristicasArray);

        $em->persist($categoria);
        $em->flush();

        return new Response(json_encode(true));


    }

    public function editarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $editarCategoria = $request->request->get('editarCategoria');

        if ($editarCategoria == "false") {
            $nivelCategoria = $request->request->get('nivelCategoria');

            $idCategoria = $request->request->get('idCategoria');

            $nivelMax = $em->getRepository('AdministracionBundle:Categoria')->findMaxLevel()->getResult()[0][1];

            /** @var Categoria $categoria */
            $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($idCategoria);

            $categoriaArray = [];
            $categoriaArray['nombre'] = $categoria->getNombre();
            $categoriaArray['nivel'] = $categoria->getNivel();
            $categoriaArray['id'] = $categoria->getId();
            $categoriaArray['icono'] = $categoria->getIcono();
            $categoriaArray['tiempoExpiracion'] = $categoria->getTiempoExpiracion();
            $categoriaArray['idPadre'] = (is_object($categoria->getCategoriaid()) ? $categoria->getCategoriaid()->getId() : null);

            $caracteristicaArray = [];
            $caracteristicas = $categoria->getCaracteristicas();
            if (count($caracteristicas) != 0) {

                foreach ($caracteristicas as $caracteristica) {
                    $caracteristicaArray[] = $caracteristica->getNombre();
                }
            }
            $categoriaArray[] = $caracteristicaArray;

            $categoriaArray[] = $categoria->getIcono();
            //categoria padre

            $listaCategoriaPadre = [];

            if ($categoria->getCategoriaid() != null) {
                $nivelCategoria--;
                $categoriasPadre = $em->getRepository('AdministracionBundle:Categoria')->findBy(
                    array('nivel' => ($nivelCategoria)),
                    array()
                );

                foreach ($categoriasPadre as $categoriaP) {
                    $categoriaPadreArray = [];
                    $categoriaPadreArray['nombre'] = $categoriaP->getNombre();
                    $categoriaPadreArray['nivel'] = $categoriaP->getNivel();
                    $categoriaPadreArray['id'] = $categoriaP->getId();
                    $categoriaPadreArray['icono'] = $categoriaP->getIcono();
                    $categoriaPadreArray['idPadre'] = (is_object($categoriaP->getCategoriaid()) ? $categoriaP->getCategoriaid()->getId() : null);

                    $caracteristicaArray = [];
                    $caracteristicas = $categoriaP->getCaracteristicas();
                    if (count($caracteristicas) != 0) {
                        foreach ($caracteristicas as $caracteristica) {
                            $caracteristicaArray[] = $caracteristica->getNombre();
                        }
                    }
                    $categoriaPadreArray[] = $caracteristicaArray;
                    $listaCategoriaPadre[] = $categoriaPadreArray;
                }


            }

            return new Response(json_encode(array('categoria' => $categoriaArray, 'idCategoriaPadre' => $categoria->getCategoriaid() != null ? $categoria->getCategoriaid()->getId() : "0", 'nivelCategoriaPadre' => $categoria->getCategoriaid() != null ? $categoria->getCategoriaid()->getNivel() : "0", 'categoriasPadre' => $listaCategoriaPadre, 'nivel' => $nivelMax)));


        } else if ($editarCategoria == "true") {

            $idCategoria = $request->request->get('idCategoria');

            $caracteristicasCategoria = $request->request->get('caracteristicasEditar');

            $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($idCategoria);

            $nivelCategoria = $request->request->get('nivelCategoriaEditar');

            $idCategoriaPadre = $request->request->get('idCategoriaPadre');

            $nombreCategoriaPadre = $request->request->get('nombreCategoriaPadre');


            foreach ($categoria->getCaracteristicas() as $caracteristica) {
                $em->remove($caracteristica);
            }

            $em->flush();

            if ($idCategoriaPadre != $nombreCategoriaPadre && $nivelCategoria != 1 && $nombreCategoriaPadre != 0) {

                $categoriaPadre = $em->getRepository('AdministracionBundle:Categoria')->find($nombreCategoriaPadre);

                foreach ($categoriaPadre->getCaracteristicas() as $caracP) {
                    $caracteristicasCategoria[] = $caracP->getNombre();
                }

                $categoria->setCategoriaid($categoriaPadre);

                $em->persist($categoria);
            }


            $categoriaHijas = $em->getRepository('AdministracionBundle:Categoria')->findBy(array('categoriaid' => $categoria));

            if ($caracteristicasCategoria) {
                foreach ($categoriaHijas as $cateHijas) {
                    foreach ($caracteristicasCategoria as $carac) {
                        $flag = false;
                        foreach ($cateHijas->getCaracteristicas() as $carac2) {
                            if ($carac == $carac2->getNombre()) {
                                $flag = true;
                            }
                        }

                        if ($flag == false) {
                            $caracteristicasObj = new CaracteristicaCategoria();
                            $caracteristicasObj->setNombre($carac);
                            $caracteristicasObj->setCategoriaid($cateHijas);

                            $em->persist($caracteristicasObj);
                        }
                    }
                    $em->persist($cateHijas);

                }
                $em->flush();
            }


            if ($caracteristicasCategoria != null) {
                $caracteristicasArray = [];
                foreach ($caracteristicasCategoria as $carac) {
                    $caracteristicasObj = new CaracteristicaCategoria();
                    $caracteristicasObj->setNombre($carac);
                    $caracteristicasObj->setCategoriaid($categoria);

                    $em->persist($caracteristicasObj);
                    $caracteristicasArray[] = $caracteristicasObj;
                }

                $categoria->setCaracteristicas($caracteristicasArray);
            }


            $em->persist($categoria);

            $categoria->setNombre($request->request->get('nombreCategoriaEditar'));

            $categoria->setIcono($request->request->get('iconoCategoriaEditar'));
            
            $categoria->setTiempoExpiracion($request->request->get('tiempoExpiracionEditar'));

            $categoria->setNivel($nivelCategoria);

            if ($nivelCategoria == 1) {
                $nombreCategoriaHija = $request->request->get('nombreCategoriaHija');
                if ($nombreCategoriaHija != "0") {
                    $nivelCategoriaHija = $request->request->get('nivelCategoriaHija');

                    $categoriaHija = $em->getRepository('AdministracionBundle:Categoria')->find($nombreCategoriaHija);

                    $categoriaHija->setCategoriaid($categoria);

                    $em->persist($categoriaHija);
                }
                $categoria->setCategoriaid(null);

            }


            $em->flush();

            $categoriaArray = [];
            $categoriaArray['nombre'] = $categoria->getNombre();
            $categoriaArray['nivel'] = $categoria->getNivel();
            $categoriaArray['id'] = $categoria->getId();
            $categoriaArray['icono'] = $categoria->getIcono();
            $categoriaArray['idPadre'] = (is_object($categoria->getCategoriaid()) ? $categoria->getCategoriaid()->getId() : null);

            return new Response(json_encode(array('categoria' => $categoriaArray)));

        }

    }

    public function obtenerAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $nivelCategoria = $request->request->get('nivel');

        $listaCategoriaPadre = [];

        $categoriasPadre = $em->getRepository('AdministracionBundle:Categoria')->findBy(
            array('nivel' => ($nivelCategoria)),
            array()
        );

        foreach ($categoriasPadre as $categoriaP) {
            $categoriaPadreArray = [];
            $categoriaPadreArray['nombre'] = $categoriaP->getNombre();
            $categoriaPadreArray['nivel'] = $categoriaP->getNivel();
            $categoriaPadreArray['id'] = $categoriaP->getId();
            $categoriaPadreArray['icono'] = $categoriaP->getIcono();
            $categoriaPadreArray['idPadre'] = (is_object($categoriaP->getCategoriaid()) ? $categoriaP->getCategoriaid()->getId() : null);

            $caracteristicaArray = [];
            $caracteristicas = $categoriaP->getCaracteristicas();
            if (count($caracteristicas) != 0) {
                foreach ($caracteristicas as $caracteristica) {
                    $caracteristicaArray[] = $caracteristica->getNombre();
                }
            }
            $categoriaPadreArray[] = $caracteristicaArray;
            $listaCategoriaPadre[] = $categoriaPadreArray;
        }

        return new Response(json_encode(array('categorias' => $listaCategoriaPadre)));

    }

    public function obtenercarateristicasAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $idCategoria = $request->request->get('idCategoria');

        $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($idCategoria);

        $caracteristicaArray = [];
        if (is_object($categoria)) {
            $caracteristicas = $categoria->getCaracteristicas();
            if (count($caracteristicas) != 0) {
                foreach ($caracteristicas as $caracteristica) {
                    $caracteristicaArray[] = $caracteristica->getNombre();
                }
            }
        }
        return new Response(json_encode(array('caracteristicas' => $caracteristicaArray)));
    }

    public function eliminarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $eliminarCategoria = $request->request->get('eliminarCategoria');

        if ($eliminarCategoria == "false") {
            $idCategoria = $request->request->get('idCategoriaEliminar');

            $nivel = $request->request->get('nivel');

            $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($idCategoria);

            $categoriaArray = [];
            $categoriaArray['nombre'] = $categoria->getNombre();
            $categoriaArray['nivel'] = $categoria->getNivel();
            $categoriaArray['id'] = $categoria->getId();
            $categoriaArray['icono'] = $categoria->getId();
            $categoriaArray['idPadre'] = (is_object($categoria->getCategoriaid()) ? $categoria->getCategoriaid()->getId() : null);

            $categoriaNivel = $em->getRepository('AdministracionBundle:Categoria')->findBy(
                array('nivel' => $nivel)
            );

            $listaCategoriaNivel = [];
            foreach ($categoriaNivel as $categoriaN) {
                $categoriaNivelArray = [];
                $categoriaNivelArray['nombre'] = $categoriaN->getNombre();
                $categoriaNivelArray['nivel'] = $categoriaN->getNivel();
                $categoriaNivelArray['id'] = $categoriaN->getId();
                $categoriaNivelArray['icono'] = $categoriaN->getIcono();
                $categoriaNivelArray['idPadre'] = (is_object($categoriaN->getCategoriaid()) ? $categoriaN->getCategoriaid()->getId() : null);

                $listaCategoriaNivel[] = $categoriaNivelArray;

            }
            return new Response(json_encode(array('categoria' => $categoriaArray, 'categoriaNivel' => $listaCategoriaNivel)));
        } else if ($eliminarCategoria == "true") {
            $idCategoria = $request->request->get('idCategoriaEliminar');

            $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($idCategoria);

            $productos = array();
            $categoriaHijas = array();
            $pedidos = array();

            if(! is_null($categoria)){
                $productos = $em->getRepository('AdministracionBundle:Producto')->findBy(
                    array('categoriaid' => $categoria->getId()),
                    array()
                );

                $categoriaHijas = $em->getRepository('AdministracionBundle:Categoria')->findBy(
                    array("categoriaid" => $categoria->getId()),
                    array()
                );

                $pedidos = $em->getRepository(DetallePedido::class)->findBy(array(
                    'categoria' => $categoria->getId()
                ));
            }
            else{
                return new JsonResponse(array(
                    'success' => false,
                    'message' => "La categoría que intenta eliminar no existe"
                ));
            }

            $idCategoriaVecina = (int) $request->request->get('nombreCategoriaVecino');
            $categoriaVecina = null;

            if($idCategoriaVecina != null && $idCategoriaVecina != 0){
                $categoriaVecina = $em->getRepository('AdministracionBundle:Categoria')->find($idCategoriaVecina);
            }
            if (!is_null($categoriaVecina)) {

                if(!is_null($categoriaHijas) && count($categoriaHijas) > 0){
                    foreach ($categoriaHijas as $ch) {
                        $ch->setCategoriaid($categoriaVecina);
                        $em->persist($ch);
                    }
                }

                if(!is_null($productos) && count($productos) > 0){
                    foreach ($productos as $producto) {
                        $producto->setCategoriaid($categoriaVecina);
                        $em->persist($producto);
                    }
                }

                if(!is_null($pedidos) && count($pedidos) > 0){
                    foreach ($pedidos as $ped){
                        $ped->setCategoria($categoriaVecina);
                        $em->persist($ped);
                    }
                }

            } else if ($idCategoriaVecina == 0) {
                if(($categoria->getCategoriaid() == null && $productos != null && count($productos) > 0)
                    || $categoria->getCategoriaid() == null && $categoriaHijas != null && count($categoriaHijas) > 0
                    || $categoria->getCategoriaid() == null && $pedidos != null && count($pedidos) > 0
                ){
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => "Debe seleccionar una categoría para asignar los productos, y/o categorías dependientes de esta que intentas eliminar"
                    ));
                }
                else if($categoria->getCategoriaid() != null){
                    if ($productos != null) {
                        foreach ($productos as $producto) {
                            $producto->setCategoriaid($categoria->getCategoriaid());
                            $em->persist($producto);
                        }
                    }

                    if($categoriaHijas != null){
                        foreach ($categoriaHijas as $catHija){
                            $catHija->setCategoriaid($categoria->getCategoriaid());
                            $catHija->setNivel($categoria->getNivel());
                            $em->persist($catHija);
                        }
                    }

                    if(!is_null($pedidos)){
                        foreach ($pedidos as $ped){
                            $ped->setCategoria($categoria->getCategoriaid());
                            $em->persist($ped);
                        }
                    }
                }

                //$this->EliminarCategoria($categoria);

            }

            $em->remove($categoria);
            $em->flush();

            return new JsonResponse(array(
                'success' => true
            ));

        }


    }

    public function EliminarCategoria($categoria)
    {
        $em = $this->getDoctrine()->getManager();

        $categoriaTemp = $em->getRepository('AdministracionBundle:Categoria')->findBy(
            array('categoriaid' => $categoria->getId()),
            array()
        );

        if ($categoriaTemp != null) {

            for ($i = 0; $i < count($categoriaTemp); $i++) {
                $this->EliminarCategoria($categoriaTemp[$i]);
            }

        } else {
            $productos = $em->getRepository('AdministracionBundle:Producto')->findBy(
                array('categoriaid' => $categoria->getId()),
                array()
            );

            if ($productos != null) {
                foreach ($productos as $producto) {
                    $em->remove($producto);
                }
                $em->flush();

            }


        }

        $em->remove($categoria);
        $em->flush();

        return 0;


    }


}
