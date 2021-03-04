<?php


namespace AdministracionBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AdministracionBundle\Entity\Tienda;
use Symfony\Component\HttpFoundation\Response;


class TiendaController extends Controller
{

    public function listarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        if($request->getMethod()=='POST')
        {
            /*$usuario=$em->getRepository('AdministracionBundle:Usuario')->findBy(
                array("id"=>1),
                array()
            )[0];
            $imagen=$em->getRepository('AdministracionBundle:Imagen')->findBy(
                array("id"=>1),
                array()
            );
            for ($i=16;$i<2000;$i++)
           {
               $tienda=new Tienda();
               $marca="Marca".$i;
               $nombre="Nombre".$i;
               $tienda->setUsuarioid($usuario);
               $tienda->setImagenes($imagen);
               $tienda->setNombre($nombre);
               $tienda->setMarca($marca);
               $em->persist($tienda);
           }
           $em->flush();*/

            $tiendas=$em->getRepository('AdministracionBundle:Tienda')->findByTienda($request)->getResult();

            $tiendaTotal=$em->getRepository('AdministracionBundle:Tienda')->findByTiendaTotal($request)->getResult();

            $listaTienda=[];

            foreach ($tiendas as $tienda)
            {
                $tiendaArray=[];
                $tiendaArray[]=$tienda->getSlug();
                $tiendaArray[]=$tienda->getImagenLogo()!=null?$tienda->getImagenLogo():"";
                $tiendaArray[]=$tienda->getSlogan()!=null?$tienda->getSlogan():"";
                $tiendaArray[]=$tienda->getNombre()!=null?$tienda->getNombre():"";
                $tiendaArray[]=$tienda->getUsuarioid()!=null?$tienda->getUsuarioid()->getEmail():"";
                $tiendaArray[]=$tienda->isVisible() == 1?"Si":"No";



                $listaTienda[]=$tiendaArray;
            }

            $json_data=array(

                "draw"=>intval($request->request->get('draw')),
                "recordsTotal"=>intval(count($tiendaTotal)),
                "recordsFiltered"=>intval(count($tiendaTotal)),
                "data"=>$listaTienda
            );

            return new Response(json_encode($json_data));

        }

        $roles=$em->getRepository('AdministracionBundle:Rol')->findAll();
        return $this->render('AdministracionBundle:Tienda:listado.html.twig',array("roles"=>$roles));
    }
    public function editarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $estadosArray=array(
            array('key'=>1, 'value' =>'Si'),
            array('key'=>0, 'value'=> 'No')
        );
        if ($request->request->get("editarTienda")=="false")
        {
            $idTienda=$request->request->get("idTienda");
            $tienda=$em->getRepository('AdministracionBundle:Tienda')->findBy(
                array("id"=>$idTienda),
                array()
            );

            $tiendaArray=[];

            $tiendaArray[]=$tienda[0]->getId();
            $tiendaArray[]=$tienda[0]->getNombre()!=null?$tienda[0]->getNombre():"";
            $tiendaArray[]=$tienda[0]->getSlogan()!=null?$tienda[0]->getSlogan():"";
            $tiendaArray[]=$tienda[0]->getUsuarioid()!=null?$tienda[0]->getUsuarioid()->getEmail():"";
            $tiendaArray[]=$tienda[0]->isVisible()== 1?"Visible":"No Visible";
            $tiendaArray[]=$tienda[0]->getImagenLogo()!=null?$tienda[0]->getImagenLogo():"";




            return new Response(json_encode(array("tienda"=>$tiendaArray, "estados"=>$estadosArray)));
        }
        if ($request->request->get("editarTienda")=="true")
        {
            $idTienda=$request->request->get("idTienda");
            $tienda=$em->getRepository('AdministracionBundle:Tienda')->findBy(
                array("id"=>$idTienda),
                array()
            )[0];

            $idEstado=$request->request->get("estadoEditar");

            $idEstado = (int)$idEstado;


            $tienda->setVisible($idEstado);

            $em->persist($tienda);
            $em->flush();

            return new Response(json_encode(true));
        }
    }

    public function eliminarAction(Request $request)
    {

        $em=$this->getDoctrine()->getManager();
        $idTienda=$request->request->get("idTiendaEliminar");
        $idTienda=explode(':',$idTienda);
        for ($i=1;$i<count($idTienda);$i++)
        {
            $tienda = $em->getRepository('AdministracionBundle:Tienda')->findBy(
                array("id" => $idTienda[$i]),
                array()
            )[0];
            $em->remove($tienda);
        }
        $em->flush();
        return new Response(json_encode(true));
    }
    public function detallesAction(Request $request, $slug)
    {
        $em=$this->getDoctrine()->getManager();

        $tienda=$em->getRepository('AdministracionBundle:Tienda')->findOneBy(
            array("slug"=>$slug),
            array()
        );
        return $this->render('AdministracionBundle:Tienda:detalles.html.twig',array(
            'tienda'=>$tienda
        ));

    }
    public function tiendaproductodetallesAction(Request $request)
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