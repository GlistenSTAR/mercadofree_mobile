<?php


namespace PublicBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ColeccionController extends Controller
{

    public function obtenerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $idColeccion=$request->request->get('idColeccion');

        $coleccion = $em->getRepository('AdministracionBundle:Coleccion')->find($idColeccion);

        $productoTemp=$coleccion->getProductos();

        $productoTempRanking3=[];

        $productoTempRankingDemas=[];

        for ($i=0;$i<count($productoTemp);$i++)
        {
            if ($i<8)
            {
                if($productoTemp[$i]->getRanking()==3)
                {
                    $productoTempRanking3[]=$productoTemp[$i];
                }
                else
                {
                    $productoTempRankingDemas[]=$productoTemp[$i];
                }
            }
            else
            {
                break;
            }


        }

        $productoTempRanking3=array_merge($productoTempRanking3,$productoTempRankingDemas);

        $coleccionesArray = [];
        $coleccionesArray[] = $coleccion->getId();
        $coleccionesArray[] = $coleccion->getNombre();
        $coleccionesArray[] = $coleccion->getImagen();

        $listaProducto=[];

        foreach ($productoTempRanking3 as $producto)
        {
            $productoArray = [];
            $productoArray[] = $producto->getId();
            $productoArray[] = $producto->getNombre();
            $productoArray[] = $producto->getImagenDestacada();

            $listaProducto[]=$productoArray;
        }

        $coleccionesArray[]=$listaProducto;


        return new Response(json_encode(array('colecciones'=>$coleccionesArray)));
    }
}
