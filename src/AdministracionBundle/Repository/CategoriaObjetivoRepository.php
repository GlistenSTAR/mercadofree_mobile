<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 14/03/2018
 * Time: 08:43 AM
 */

namespace AdministracionBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class CategoriaObjetivoRepository extends EntityRepository
{
    public function findByCategoriaObjetivo(Request $request)
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
            0=>"nombre",
            1=>"nombre"
        );


        $valorSearch=$request->request->get('search')['value'];

        if($valorSearch)
        {

             $where=$where." categoriaObjetivo.nombre like :nombreCategoriaObjetivo";
             $whereParameters=['nombreCategoriaObjetivo'=>'%'.$valorSearch.'%'];

             $flag=true;

        }
        $columnaOrder=$columnas[$request->request->get("order")[0]["column"]];
        $sentidoOrder=$request->request->get("order")[0]["dir"];


         if ($columnaOrder == "nombre") {
             $orderBy = "ORDER BY categoriaObjetivo.nombre ".$sentidoOrder;
         }



        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  categoriaObjetivo                  
                  
              from 
              
              AdministracionBundle:CategoriaObjetivo categoriaObjetivo             
             
             
              ".$where."  ".$orderBy." 
              
             
              
              
             "
        ;

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);
        $dql->setMaxResults($offset);
        $dql->setFirstResult($start);


        return $dql;
    }
    public function findByCategoriaObjetivoTotal(Request $request)
    {

        $em=$this->getEntityManager();

        $where="where";
        $having="";
        $orderBy="";

        $flag=false;

        $whereParameters=array();

        $start=$request->request->get('start');
        $offset=$request->request->get('length');

        $valorSearch=$request->request->get('search')['value'];

        if($valorSearch)
        {

            $where=$where." categoriaObjetivo.nombre like :nombreCategoriaObjetivo";
            $whereParameters=['nombreCategoriaObjetivo'=>'%'.$valorSearch.'%'];

            $flag=true;

        }

        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  categoriaObjetivo                  
                  
              from 
              
              AdministracionBundle:CategoriaObjetivo categoriaObjetivo             
             
             
              ".$where."  ".$orderBy." 
              
             
              
              
             "
        ;
        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }


}