<?php
namespace AdministracionBundle\Controller;

use AdministracionBundle\Entity\Producto;
use AdministracionBundle\Entity\Oferta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;

class OfertaController extends Controller
{

    public function listarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        if($request->getMethod()=='POST')
        {
            /*for ($i=5;$i<2000;$i++)
            {
                $oferta=new Oferta();
                $nombre="nombre".$i;
                $oferta->setNombre($nombre);
                $oferta->setPorcientodescuento($i);
                $hoy=new \DateTime();
                $oferta->setFechainicio($hoy);
                $oferta->setFechafin($hoy);

                $em->persist($oferta);
            }
            $em->flush();*/


            $ofertas=$em->getRepository('AdministracionBundle:Oferta')->findByOferta($request)->getResult();

            $ofertasTotal=$em->getRepository('AdministracionBundle:Oferta')->findByOfertaTotal($request)->getResult();

            $listaOferta=[];

            foreach ($ofertas as $oferta)
            {
                $ofertaArray=[];
                $ofertaArray[]=$oferta->getId();
                $ofertaArray[]=$oferta->getNombre()!=null?$oferta->getNombre():"";
                $ofertaArray[]=$oferta->getPorcientodescuento()!=null?$oferta->getPorcientodescuento():"";
                $ofertaArray[]=$oferta->getFechainicio()!=null?$oferta->getFechainicio()->format('j/n/Y'):"";
                $ofertaArray[]=$oferta->getFechafin()!=null?$oferta->getFechafin()->format('j/n/Y'):"";
                $ofertaArray[]=$oferta->getEstadoProductoid()!=null?$oferta->getEstadoProductoid()->getNombre():"";


                $listaOferta[]=$ofertaArray;
            }

            $json_data=array(
                "draw"=>intval($request->request->get('draw')),
                "recordsTotal"=>intval(count($ofertasTotal)),
                "recordsFiltered"=>intval(count($ofertasTotal)),
                "data"=>$listaOferta
            );

            return new Response(json_encode($json_data));

        }

        $roles=$em->getRepository('AdministracionBundle:Rol')->findAll();
        return $this->render('AdministracionBundle:Oferta:listado.html.twig',array("roles"=>$roles));
    }

    public function eliminarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $idOferta=$request->request->get("idOfertaEliminar");
        $idOferta=explode(':',$idOferta);
        for ($i=1;$i<count($idOferta);$i++)
        {
            $oferta=$em->getRepository('AdministracionBundle:Oferta')->findBy(
                array("id"=>$idOferta[$i]),
                array()
            )[0];

           /* $tiendas=$em->getRepository('AdministracionBundle:Tienda')->findBy(
                array("usuarioid"=>$oferta),
                array()
            );

            foreach ($tiendas as $tienda)
            {
                if($tienda!=null)
                {
                    $em->remove($tienda);
                }
            }
            $productos=$em->getRepository('AdministracionBundle:Producto')->findBy(
                array("usuarioid"=>$oferta),
                array()
            );

            foreach ($productos as $producto)
            {
                if($producto!=null)
                {
                    $em->remove($producto);
                }
            }*/

            $em->remove($oferta);
        }

        $em->flush();
        return new Response(json_encode(true));
    }
    public function bloquearAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $idOferta=$request->request->get("idOferta");

        $oferta=$em->getRepository('AdministracionBundle:Oferta')->findBy(
            array("id"=>$idOferta),
            array()
        )[0];

        $bloquearOferta=$request->request->get("bloquearOferta");

        if ($bloquearOferta=="false")
        {
            $estados=$em->getRepository('AdministracionBundle:EstadoProducto')->findAll();

            $estadosArray=[];
            foreach ($estados as $estado)
            {
                $estadoArray=[];
                $estadoArray[]=$estado->getId();
                $estadoArray[]=$estado->getNombre();
                $estadosArray[]=$estadoArray;
            }



            $ofertaArray=[];
            $ofertaArray[]= $oferta->getNombre();
            $ofertaArray[]= $oferta->getPorcientodescuento();
            $ofertaArray[]= $oferta->getEstadoProductoid()!=null?$oferta->getEstadoProductoid()->getNombre():"";
            $ofertaArray[]= $oferta->getFechainicio()!=null?$oferta->getFechainicio()->format('j/n/Y'):"";
            $ofertaArray[]= $oferta->getFechafin()!=null?$oferta->getFechafin()->format('j/n/Y'):"";




            return new Response(json_encode(array('oferta'=>$ofertaArray, 'estados'=>$estadosArray)));
        }
        else if($bloquearOferta=="true")
        {
            $idEstado=$request->request->get("estadoOfertaDetalles");
            $estado=$em->getRepository('AdministracionBundle:EstadoProducto')->find($idEstado);
            $oferta->setEstadoProductoid($estado);
            $em->persist($oferta);
            $em->flush();

            return new Response(json_encode(true));
        }



    }
    public function detallesAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $idOferta=$request->query->get("idOferta");

        $oferta=$em->getRepository('AdministracionBundle:Oferta')->findBy(
            array("id"=>$idOferta),
            array()
        )[0];

        $estados=$em->getRepository('AdministracionBundle:EstadoProducto')->findAll();

        $estadosArray=[];
        foreach ($estados as $estado)
        {
            $estadoArray=[];
            $estadoArray[]=$estado->getId();
            $estadoArray[]=$estado->getNombre();
            $estadosArray[]=$estadoArray;
        }


        /*$ofertaArray=[];
        $ofertaArray[]= $oferta->getNombre();
        $ofertaArray[]= $oferta->getPorcientodescuento();
        $ofertaArray[]= $oferta->getEstadoProductoid()->getNombre();
        $ofertaArray[]= $oferta->getFechainicio()->format('j/n/Y');
        $ofertaArray[]= $oferta->getFechafin()->format('j/n/Y');

        $listaProducto=[];

        foreach ($oferta->getProductoid() as $produc)
        {
            $producArray=[];
            $producArray[]=$produc->getId();
            $producArray[]=$produc->getImagenDestacada();
            $producArray[]=$produc->getNombre()!=null?$produc->getNombre():"";
            $producArray[]=$produc->getPrecio()!=null?$produc->getPrecio():"";
            $producArray[]=$produc->getCantidad()!=null?$produc->getCantidad():"";
            $producArray[]=$produc->getCategoriaid()!=null?$produc->getCategoriaid()->getNombre():"";


            $listaProducto[]=$producArray;
        }

        $estados=$em->getRepository('AdministracionBundle:EstadoProducto')->findAll();

        $estadosArray=[];
        foreach ($estados as $estado)
        {
            $estadoArray=[];
            $estadoArray[]=$estado->getId();
            $estadoArray[]=$estado->getNombre();
            $estadosArray[]=$estadoArray;
        }


        return new Response(json_encode(array('oferta'=>$ofertaArray,'productos'=>$listaProducto,'estados'=>$estadosArray)));*/
        return $this->render('AdministracionBundle:Oferta:detalles.html.twig',array(
            'oferta'=>$oferta,
            'estados'=>$estados
        ));

    }

    public function ofertaproductodetallesAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $productos=$em->getRepository('AdministracionBundle:Producto')->findByProductoDetalles($request)->getResult();

        $productoTotal=$em->getRepository('AdministracionBundle:Producto')->findByProductoDetalles($request)->getResult();

        $listaProducto=[];

        /** @var Producto $produc */
        foreach ($productos as $produc)
        {
            $producArray=[];
            $producArray[]=$produc->getImagenDestacada();
            $producArray[]=$produc->getNombre()!=null?$produc->getNombre():"";
            $producArray[]=$produc->getPrecio()!=null?$produc->getPrecio():"";
            $producArray[]=$produc->getCantidad()!=null?$produc->getCantidad():"";
            $producArray[]=$produc->getCategoriaid()!=null?$produc->getCategoriaid()->getNombre():"";
            $producArray[]=$produc->getEstadoProductoid()!=null?$produc->getEstadoProductoid()->getNombre():"";

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