<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 01/03/2018
 * Time: 02:53 PM
 */

namespace AdministracionBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ColeccionRepository extends EntityRepository
{

    public function findByColeccion(Request $request)
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
            0,"",
            1,"",
            2=>"nombre"
        );


        $valorSearch=$request->request->get('search')['value'];

        if($valorSearch)
        {

            $where=$where." producto.nombre like :nombreColeccion";
            $whereParameters=['nombreColeccion'=>'%'.$valorSearch.'%'];

            $flag=true;

        }
        $columnaOrder=$columnas[$request->request->get("order")[0]["column"]];
        $sentidoOrder=$request->request->get("order")[0]["dir"];


        if ($columnaOrder == "nombre")
        {
            $orderBy = "ORDER BY coleccion.nombre ".$sentidoOrder;
        }
        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  coleccion                  
                  
              from 
              
              AdministracionBundle:Coleccion coleccion                            
              ".$where."  ".$orderBy." 
              
             
              
              
             "
        ;

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);
        $dql->setMaxResults($offset);
        $dql->setFirstResult($start);


        return $dql;
    }
    public function findByColeccionTotal(Request $request)
    {

        $em=$this->getEntityManager();

        $where="where";
        $having="";
        $orderBy="";

        $flag=false;

        $whereParameters=array();

        $valorSearch=$request->request->get('search')['value'];

        if($valorSearch)
        {
            $where=$where." producto.nombre like :nombreColeccion";
            $whereParameters=['nombreColeccion'=>'%'.$valorSearch.'%'];

            $flag=true;

        }

        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  coleccion                  
                  
              from 
              
              AdministracionBundle:Coleccion coleccion                            
              ".$where."  ".$orderBy." 
              
             
              
              
             "
        ;

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }

}