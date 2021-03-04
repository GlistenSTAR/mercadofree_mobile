<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 07/03/2018
 * Time: 10:06 PM
 */

namespace AdministracionBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificacionRepository extends EntityRepository
{
    public function findByNotificacion(Request $request)
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
            2=>"titulo"
        );


        $valorSearch=$request->request->get('search')['value'];

        if($valorSearch)
        {

            $where=$where." producto.titulo like :nombreColeccion";
            $whereParameters=['nombreColeccion'=>'%'.$valorSearch.'%'];

            $flag=true;

        }
        $columnaOrder=$columnas[$request->request->get("order")[0]["column"]];
        $sentidoOrder=$request->request->get("order")[0]["dir"];


        if ($columnaOrder == "titulo")
        {
            $orderBy = "ORDER BY notificacion.titulo ".$sentidoOrder;
        }
        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  notificacion                  
                  
              from 
              
              AdministracionBundle:Notificacion notificacion                            
              ".$where."  ".$orderBy." 
              
             
              
              
             "
        ;

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);
        $dql->setMaxResults($offset);
        $dql->setFirstResult($start);


        return $dql;
    }
    public function findByNotificacionTotal(Request $request)
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
            $where=$where." nombre.nombre like :nombreNotificacion";
            $whereParameters=['nombreNotificacion'=>'%'.$valorSearch.'%'];

            $flag=true;

        }

        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  notificacion                  
                  
              from 
              
              AdministracionBundle:Notificacion notificacion                            
              ".$where."  ".$orderBy." 
              
             
              
              
             "
        ;


        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }

}