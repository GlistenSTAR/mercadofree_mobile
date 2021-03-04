<?php


namespace PublicBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OfertaController extends Controller
{
    public function ofertasemanaAction(Request $request)
    {

        $em=$this->getDoctrine()->getManager();

        $listaCategoriasFiltro=[];

        $ciudades="";

        $tipoenvio="";

        if ($request->getMethod()=='POST')
        {

            $productos=$em->getRepository('AdministracionBundle:Producto')->findByProductoOfertaSemana($request)->getResult();

            $start=count($productos);

            $productosTotal=$em->getRepository('AdministracionBundle:Producto')->findByProductoOfertaSemanaTotal($request)->getResult();

            $total=count($productosTotal);

            $listaProductos=[];

            $usuario=$this->get('utilPublic')->getUsuarioLogueado();

            foreach ($productos as $producto)
            {
                $productoArray=[];

                $productoArray[]=$producto->getId();
                $productoArray[]=$producto->getNombre()!=null?$producto->getNombre():"";
                $productoArray[]=$producto->getPrecio()!=null?$producto->getPrecio():"";
                $productoArray[]=$producto->getCuotaspagar()!=null?$producto->getCuotaspagar():"";
                $productoArray[]=$producto->getCantidad()!=null?$producto->getCantidad():"";
                $productoArray[]=$producto->getCategoriaid()!=null?$producto->getCategoriaid()->getNombre():"";
                $productoArray[]=$producto->getUsuarioid()!=null?$producto->getUsuarioid()->getEmail():"";
                $productoArray[]=$producto->getEstadoProductoid()!=null?$producto->getEstadoProductoid()->getNombre():"";
                $productoArray[]=$producto->getImagenDestacada()!=null?$producto->getImagenDestacada():"";
                //$productoArray[]=$producto->hasEnvioDomicilio();
                $productoArray[]=$this->generateUrl('public_anuncio_detalles',array('idProducto'=>$producto->getId()));

                $productoArray[]=$producto->getOfertaid()[0]->getPorcientodescuento();
                $productoArray[]=$producto->getPrecio()-(($producto->getPrecio()*$producto->getOfertaid()[0]->getPorcientodescuento())/100);

                $listaProductos[]=$productoArray;
            }

            return new Response(json_encode(array('productos'=>$listaProductos, 'total'=>$total,'start'=>$start)));

        }

        $where="where estadoOferta.id = 3 and estado.id = 3";

        $date=strtotime(date("Y-m-d"));

        $first = strtotime('last Sunday');

        $last = strtotime('next Saturday');

        $first=date("Y-m-d", $first);

        $last=date("Y-m-d", $last);

        $first=explode('-',$first);

        $last=explode('-',$last);

        $first = \DateTime::createFromFormat( "Y-m-d H:i:s", date($first[0]."-".$first[1]."-".$first[2]." "."00:00:00") );

        $last = \DateTime::createFromFormat( "Y-m-d H:i:s", date($last[0]."-".$last[1]."-".$last[2]." "."23:59:59") );

        $where=$where." and (oferta.fechainicio BETWEEN '".$first->format('Y-m-d H:i:s')."' and '".$last->format('Y-m-d H:i:s')."'";

        $where=$where." or oferta.fechafin BETWEEN '".$first->format('Y-m-d H:i:s')."' and '".$last->format('Y-m-d H:i:s')."')";

        $sql2="select 
                  categoria
              from 
              AdministracionBundle:Categoria categoria 
              INNER JOIN  categoria.productos producto
              INNER JOIN  producto.estadoProductoid estado
              INNER JOIN  producto.ofertaid oferta
              INNER JOIN  oferta.estadoProductoid estadoOferta
              INNER JOIN  producto.usuarioid usuario              
              INNER JOIN  producto.condicion condicion
              INNER JOIN  usuario.direccion direccion              
              INNER JOIN  direccion.ciudad ciudad
              ".$where."   
             "
        ;
        $listaCategoriasFiltro=$em->createQuery($sql2)->getResult();

       /* $sql3="select

                  tipoenvio
                  
              from 
              
              AdministracionBundle:TipoEnvio tipoenvio 
              INNER JOIN  tipoenvio.productoid producto
              INNER JOIN  producto.estadoProductoid estado
              INNER JOIN  producto.ofertaid oferta
              INNER JOIN  oferta.estadoProductoid estadoOferta
              INNER JOIN  producto.usuarioid usuario              
              INNER JOIN  producto.condicion condicion
              INNER JOIN  usuario.direccion direccion              
              INNER JOIN  direccion.ciudad ciudad
              
              
              
             
              ".$where."   
              
            
              
              
              
             "
        ;
        $tipoEnvios=$em->createQuery($sql3)->getResult();*/


        $sql3="select 

                  ciudad
                  
              from 
              
              AdministracionBundle:Ciudad ciudad 
              INNER JOIN  ciudad.direcciones direccion
              INNER JOIN  direccion.usuarioid usuario
              INNER JOIN  usuario.productos producto              
              INNER JOIN  producto.estadoProductoid estado
              INNER JOIN  producto.ofertaid oferta
              INNER JOIN  oferta.estadoProductoid estadoOferta                       
              INNER JOIN  producto.condicion condicion                       
              
              
              
              
             
              ".$where."   
              
            
              
              
              
             "
        ;
        $ciudades=$em->createQuery($sql3)->getResult();

        $sql3="select 

                  oferta
                  
              from 
              
              AdministracionBundle:Oferta oferta 
              INNER JOIN  oferta.estadoProductoid estadoOferta                       
                                     
              where estadoOferta.id = 3 
              
              and (oferta.fechainicio BETWEEN '".$first->format('Y-m-d H:i:s')."' and '".$last->format('Y-m-d H:i:s')."'

              or oferta.fechafin BETWEEN '".$first->format('Y-m-d H:i:s')."' and '".$last->format('Y-m-d H:i:s')."')
              
             "
        ;
        $ofertas=$em->createQuery($sql3)->getResult();

        $productos=$em->getRepository('AdministracionBundle:Producto')->findByProductoOfertaSemana($request)->getResult();

        $start=count($productos);

        $productosTotal=$em->getRepository('AdministracionBundle:Producto')->findByProductoOfertaSemanaTotal($request)->getResult();

        $total=count($productosTotal);



        return $this->render('PublicBundle:Oferta:ofertasSemana.html.twig',array('listaCategoriasFiltro'=>$listaCategoriasFiltro,
            "ciudadesProduct"=>$ciudades,'productos'=>$productos, 'productosTotal'=>$productosTotal, 'ofertas'=>$ofertas, 'start'=>$start, 'total'=>$total));

    }
}