<?php


namespace AdministracionBundle\Controller;

use AdministracionBundle\Entity\Coleccion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;


class ColeccionController extends Controller
{
    public function listarAction(Request $request)
    {

            $em=$this->getDoctrine()->getManager();
            if($request->getMethod()=='POST')
            {
                $colecciones=$em->getRepository('AdministracionBundle:Coleccion')->findByColeccion($request)->getResult();

                $coleccionesTotal=$em->getRepository('AdministracionBundle:Coleccion')->findByColeccionTotal($request)->getResult();

                $listaColecciones=[];

                foreach ($colecciones as $colec)
                {
                    $colecArray=[];
                    $colecArray[]=$colec->getId();
                    $colecArray[]=$colec->getImagen();
                    $colecArray[]=$colec->getNombre()!=null?$colec->getNombre():"";


                    $listaColecciones[]=$colecArray;
                }

                $json_data=array(
                    "draw"=>intval($request->request->get('draw')),
                    "recordsTotal"=>intval(count($coleccionesTotal)),
                    "recordsFiltered"=>intval(count($coleccionesTotal)),
                    "data"=>$listaColecciones
                );

                return new Response(json_encode($json_data));

            }
            return $this->render('AdministracionBundle:Coleccion:listado.html.twig');


    }

    public function adicionarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $nombreColeccion=$request->request->get('nombreColeccion');

        $imgColeccion=$request->request->get('imgColeccion');

        $coleccion= new Coleccion();

        $coleccion->setNombre($nombreColeccion);

        $this->get('utilColeccion')->procesarFoto($imgColeccion,$coleccion);

        $coleccion->setSlug($this->get('util')->generateSlug($nombreColeccion));

        $em->persist($coleccion);

        $em->flush();

        return new Response(json_encode(true));
    }

    public function editarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $idColeccion=$request->request->get('idColeccion');

        $editarColeccion=$request->request->get('editarColeccion');

        if ($editarColeccion=="false")
        {
            $coleccion=$em->getRepository('AdministracionBundle:Coleccion')->find($idColeccion);

            $colecArray=[];
            $colecArray[]=$coleccion->getId();
            $colecArray[]=$coleccion->getImagen();
            $colecArray[]=$coleccion->getNombre();

            return new Response(json_encode(array('coleccion'=>$colecArray)));

        }
        else if ($editarColeccion=="true")
        {
            $coleccion=$em->getRepository('AdministracionBundle:Coleccion')->find($idColeccion);

            $nombreColeccionEditar=$request->request->get('nombreColeccionEditar');

            $imgColeccionEditar=$request->request->get('imgColeccionEditar');

            $coleccion->setNombre($nombreColeccionEditar);

            $this->get('utilColeccion')->procesarFoto($imgColeccionEditar,$coleccion);

            $em->persist($coleccion);

            $em->flush();

            return new Response(json_encode(true));
        }

    }

    public function eliminarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $idColeccion=$request->request->get("idColeccionEliminar");
        $idColeccion=explode(':',$idColeccion);
        for ($i=1;$i<count($idColeccion);$i++)
        {
            $coleccion = $em->getRepository('AdministracionBundle:Coleccion')->findBy(
                array("id" => $idColeccion[$i]),
                array()
            )[0];
            $em->remove($coleccion);
        }
        $em->flush();

        return new Response(json_encode(true));
    }
    public function detallesAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $idColeccion=$request->query->get("idColeccion");

        $coleccion = $em->getRepository('AdministracionBundle:Coleccion')->find($idColeccion);
        return $this->render('AdministracionBundle:Coleccion:detalles.html.twig', array('coleccion'=>$coleccion));

    }
    public function coleccionproductodetallesAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $productos=$em->getRepository('AdministracionBundle:Producto')->findByProductoDetalles($request)->getResult();

        $productoTotal=$em->getRepository('AdministracionBundle:Producto')->findByProductoDetallesTotal($request)->getResult();

        $listaProducto=[];

        foreach ($productos as $produc)
        {
            $producArray=[];
            $producArray[]=$produc->getImagenDestacada();
            $producArray[]=$produc->getNombre()!=null?$produc->getNombre():"";
            $producArray[]=$produc->getPrecio()!=null?$produc->getPrecio():"";
            $producArray[]=$produc->getCantidad()!=null?$produc->getCantidad():"";
            $producArray[]=$produc->getCategoriaid()!=null?$produc->getCategoriaid()->getNombre():"";


            $listaProducto[]=$producArray;
        }

        $json_data=array(
            "draw"=>intval($request->request->get('draw')),
            "recordsTotal"=>intval(count($productoTotal)),
            "recordsFiltered"=>intval(count($productoTotal)),
            "data"=>$listaProducto
        );

        return new Response(json_encode($json_data));
    }


}
