<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 15/02/2018
 * Time: 09:01 PM
 */

namespace AdministracionBundle\Repository;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\HttpFoundation\Request;

class OfertaRepository extends EntityRepository
{
    public function findByOferta(Request $request)
    {

        $em=$this->getEntityManager();

        $where="where";
        $having="";
        $orderBy="";

        $flag=false;

        $whereParameters=array();

        $start=$request->request->get('start');
        $offset=$request->request->get('length');

        $columnas=array(
            1=>"nombre",
            2=>"descuento",
            3=>"fechainicio",
            4=>"fechafin"
        );


        $valorSearch=$request->request->get('search')['value'];

         if($valorSearch)
         {

             $where=$where." oferta.nombre like :nombreOferta";
             $whereParameters=['nombreOferta'=>'%'.$valorSearch.'%'];

             $where=$where." or oferta.porcientodescuento like :porcientodescuentoOferta";
             $whereParameters+=['porcientodescuentoOferta'=>'%'.$valorSearch.'%'];

             $where=$where." or oferta.fechainicio like :fechainicioOferta";
             $whereParameters+=['fechainicioOferta'=>'%'.$valorSearch.'%'];

             $where=$where." or oferta.fechafin like :fechafinOferta";
             $whereParameters+=['fechafinOferta'=>'%'.$valorSearch.'%'];


             $flag=true;

         }
         if ($request->request->get("order")[0]["column"]!=0)
         {
             $columnaOrder=$columnas[$request->request->get("order")[0]["column"]];
             $sentidoOrder=$request->request->get("order")[0]["dir"];


             if ($columnaOrder == "nombre") {
                 $orderBy = "ORDER BY oferta.nombre ".$sentidoOrder;
             }
             if ($columnaOrder == "descuento") {
                 $orderBy = "ORDER BY oferta.porcientodescuento ".$sentidoOrder;
             }
             if ($columnaOrder == "fechainicio") {
                 $orderBy = "ORDER BY oferta.fechainicio ".$sentidoOrder;
             }
            if ($columnaOrder == "fechafin") {
                $orderBy = "ORDER BY oferta.fechafin ".$sentidoOrder;
            }
         }


        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  oferta                  
                  
              from 
              
              AdministracionBundle:Oferta oferta               
              LEFT JOIN  oferta.productoid producto
             
              ".$where." ".$orderBy." 
              
             
              
              
             "
        ;

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);
        $dql->setMaxResults($offset);
        $dql->setFirstResult($start);


        return $dql;
    }
    public function findByOfertaTotal(Request $request)
    {

        $em=$this->getEntityManager();

        $where="where estadoOferta.id = 3 and estado.id = 3";
        $having="";
        $orderBy="";

        $flag=false;

        $whereParameters=array();

        $start=$request->request->get('start');
        $offset=$request->request->get('length');

        $valorSearch=$request->request->get('search')['value'];

        if($valorSearch)
        {

            $where=$where." oferta.nombre like :nombreOferta";
            $whereParameters=['nombreOferta'=>'%'.$valorSearch.'%'];

            $where=$where." or oferta.porcientodescuento like :porcientodescuentoOferta";
            $whereParameters+=['porcientodescuentoOferta'=>'%'.$valorSearch.'%'];

            $where=$where." or oferta.fechainicio like :fechainicioOferta";
            $whereParameters+=['fechainicioOferta'=>'%'.$valorSearch.'%'];

            $where=$where." or oferta.fechafin like :fechafinOferta";
            $whereParameters+=['fechafinOferta'=>'%'.$valorSearch.'%'];


            $flag=true;

        }

        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  oferta                  
                  
              from 
              
              AdministracionBundle:Oferta oferta     
              INNER JOIN  oferta.estadoProductoid estadoOferta
              INNER JOIN  oferta.productoid producto
              INNER JOIN  producto.estadoProductoid estado
              
             
              ".$where." 
              
             
              
              
             "
        ;
        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }

    public function findByProductoOfertaSemana($first,$last,$start)
    {
        $em=$this->getEntityManager();

        $where="where estadoOferta.id = 3 and estado.id = 3";
        $whereParameters=array();
        $first=explode('-',$first);
        $last=explode('-',$last);
        $first = \DateTime::createFromFormat( "Y-m-d H:i:s", date($first[0]."-".$first[1]."-".$first[2]." "."00:00:00") );
        $last = \DateTime::createFromFormat( "Y-m-d H:i:s", date($last[0]."-".$last[1]."-".$last[2]." "."23:59:59") );


        $where=$where." and (oferta.fechainicio BETWEEN :fechainicioOferta and :fechafinOferta";

        $where=$where." or oferta.fechafin BETWEEN :fechainicioOferta and :fechafinOferta)";

        $whereParameters+=['fechainicioOferta'=>$first];
        $whereParameters+=['fechafinOferta'=>$last];

        $sql="select 

                  producto                  
                  
              from 
              
              AdministracionBundle:Producto producto               
              INNER JOIN  producto.ofertaid oferta   
              INNER JOIN  producto.categoriaid categoria
              INNER JOIN  producto.estadoProductoid estado             
              INNER JOIN  oferta.estadoProductoid estadoOferta
              INNER JOIN  producto.usuarioid usuario              
              INNER JOIN  producto.condicion condicion
              INNER JOIN  usuario.direccion direccion              
              INNER JOIN  direccion.ciudad ciudad
              
              
              ".$where." 
              
             
              
              
             "
        ;
        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);
        $dql->setMaxResults(15);
        $dql->setFirstResult($start);

        return $dql;

    }


}
