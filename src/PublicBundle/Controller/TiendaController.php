<?php


namespace PublicBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;


class TiendaController extends Controller
{
    public function detalleAction($slug)
    {
        $em=$this->getDoctrine()->getManager();

        $tienda=$em->getRepository('AdministracionBundle:Tienda')->findOneBy(array('slug'=>$slug));

        $usuario=$tienda->getUsuarioid();

       // $productosTotal=$em->getRepository('AdministracionBundle:Producto')->findBy(array('usuarioid'=>$usuario->getId(),'estadoProductoid'=>3));

        $where="where usuario.id =".$usuario->getId()." and estado.id = 3";

        $sql="select 

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
              INNER JOIN  producto.tipoenvioid tipoenvio
              LEFT JOIN  producto.campannaid campanna
              
             
              ".$where."   
              
            
              
              GROUP BY producto.id
              
             "
        ;

        $productosTotal=$em->createQuery($sql)->getResult();

        $sql2="select 

                  categoria
                  
              from 
              
              AdministracionBundle:Categoria categoria 
              INNER JOIN  categoria.productos producto
              INNER JOIN  producto.estadoProductoid estado
              INNER JOIN  producto.usuarioid usuario              
              INNER JOIN  producto.condicion condicion
              INNER JOIN  usuario.direccion direccion              
              INNER JOIN  direccion.ciudad ciudad
              INNER JOIN  producto.tipoenvioid tipoenvio
              
              
             
              ".$where."   
              
            
              
              
              
             "
        ;
        $categorias=$em->createQuery($sql2)->getResult();

        $sql3="select 

                  tipoenvio
                  
              from 
              
              AdministracionBundle:TipoEnvio tipoenvio 
              INNER JOIN  tipoenvio.productos producto
              INNER JOIN  producto.estadoProductoid estado
              INNER JOIN  producto.usuarioid usuario              
              INNER JOIN  producto.condicion condicion
              INNER JOIN  usuario.direccion direccion              
              INNER JOIN  direccion.ciudad ciudad
              
              
              
             
              ".$where."   
              
            
              
              
              
             "
        ;
        $tipoEnvios=$em->createQuery($sql3)->getResult();
        return $this->render('PublicBundle:Tienda:detalle.html.twig',array('tienda'=>$tienda,'total'=>count($productosTotal),'categorias'=>$categorias, 'tiposEnvios'=>$tipoEnvios));
    }

    public function listarAction()
    {
        $em=$this->getDoctrine()->getManager();

        $tiendas=$em->getRepository('AdministracionBundle:Tienda')->findBy(array('visible'=>1));

        return $this->render('PublicBundle:Tienda:listado.html.twig', array('tiendas'=>$tiendas));
    }

}
