<?php


namespace AdministracionBundle\Controller;

use AdministracionBundle\Entity\EstadoProducto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductoController extends Controller
{

    public function listarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        if($request->getMethod()=='POST')
        {
            /*for ($i=16;$i<2000;$i++)
            {
                $usuario=new Usuario();
                $email="email".$i."@gmail.com";
                $usuario->setEmail($email);
                $u="Usuario".$i;
                $usuario->setNombre($u);
                $em->persist($usuario);
            }
            $em->flush();*/


            $productos=$em->getRepository('AdministracionBundle:Producto')->findByProducto($request)->getResult();



            $productoTotal=$em->getRepository('AdministracionBundle:Producto')->findByProductoTotal($request)->getResult();

            $listaProducto=[];

            foreach ($productos as $produc)
            {
                $producArray=[];
                $producArray[]=$produc->getId();
                $producArray[]=$produc->getImagenDestacada()!=null?$produc->getImagenDestacada():$produc->getImagenes()[0]->getUrl();
                $producArray[]=$produc->getNombre()!=null?$produc->getNombre():"";
                $producArray[]=$produc->getPrecio()!=null?$produc->getPrecio():"";
                $producArray[]=$produc->getCuotaspagar()!=null?$produc->getCuotaspagar():"";
                $producArray[]=$produc->getCantidad()!=null?$produc->getCantidad():"";
                $producArray[]=$produc->getCategoriaid()!=null?$produc->getCategoriaid()->getNombre():"";
                $producArray[]=$produc->getUsuarioid()!=null?$produc->getUsuarioid()->getEmail():"";
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

        $roles=$em->getRepository('AdministracionBundle:Rol')->findAll();


        return $this->render('AdministracionBundle:Producto:listado.html.twig',array("roles"=>$roles));
    }

    public function editarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        if ($request->request->get("editarProducto")=="false")
        {
            $idProducto=$request->request->get("idProducto");
            $producto=$em->getRepository('AdministracionBundle:Producto')->findBy(
                array("id"=>$idProducto),
                array()
            );

            $productoArray=[];

            $productoArray[]=$producto[0]->getSlug();
            $productoArray[]=$producto[0]->getNombre()!=null?$producto[0]->getNombre():"";
            $productoArray[]=$producto[0]->getDescripcion()!=null?$producto[0]->getDescripcion():"";
            $productoArray[]=$producto[0]->getPrecio()!=null?$producto[0]->getPrecio():"";
            $productoArray[]=$producto[0]->getCuotaspagar()!=null?$producto[0]->getCuotaspagar():"";
            $productoArray[]=$producto[0]->getCantidad()!=null?$producto[0]->getCantidad():"";
            $productoArray[]=$producto[0]->getCategoriaid()!=null?$producto[0]->getCategoriaid()->getNombre():"";
            $productoArray[]=$producto[0]->getUsuarioid()!=null?$producto[0]->getUsuarioid()->getEmail():"";
            $productoArray[]=$producto[0]->getEstadoProductoid()!=null?$producto[0]->getEstadoProductoid()->getNombre():"";
            $productoArray[]=$producto[0]->getImagenDestacada()!=null?$producto[0]->getImagenDestacada():$producto[0]->getImagenes()[0]->getUrl();

            $estados=$em->getRepository('AdministracionBundle:EstadoProducto')->findAll();

            $estadosArray=[];
            foreach ($estados as $estado)
            {
                $estadoArray=[];
                $estadoArray[]=$estado->getId();
                $estadoArray[]=$estado->getNombre();
                $estadosArray[]=$estadoArray;
            }


            return new Response(json_encode(array("producto"=>$productoArray, "estados"=>$estadosArray)));
        }
        if ($request->request->get("editarProducto")=="true")
        {
            $idProducto=$request->request->get("idProducto");
            $producto=$em->getRepository('AdministracionBundle:Producto')->findBy(
                array("id"=>$idProducto),
                array()
            )[0];

            $idEstado=$request->request->get("estadoEditar");
            $estado=$em->getRepository('AdministracionBundle:EstadoProducto')->findBy(
                array("id"=>$idEstado),
                array()
            )[0];

            $producto->setEstadoProductoid($estado);

            $em->persist($producto);
            $em->flush();

            return new Response(json_encode(true));
        }
    }

    public function eliminarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $idProducto=$request->request->get("idProductoEliminar");
        $idProducto=explode(':',$idProducto);
        for ($i=1;$i<count($idProducto);$i++)
        {
            $producto = $em->getRepository('AdministracionBundle:Producto')->findBy(
                array("id" => $idProducto[$i]),
                array()
            )[0];
            $em->remove($producto);
        }
        $em->flush();
        return new Response(json_encode(true));
    }

    public function detallesAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $idProducto=$request->query->get("idProducto");

        $producto = $em->getRepository('AdministracionBundle:Producto')->find($idProducto);

        $caraacteristicas = $em->getRepository('AdministracionBundle:ProductoCaracteristicaCategoria')->findBy(array('productoid'=>$idProducto));

        return $this->render('AdministracionBundle:Producto:detalles.html.twig',array('producto'=>$producto, 'caracteristicas'=>$caraacteristicas));
    }

}
