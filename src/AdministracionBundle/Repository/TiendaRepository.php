<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 12/02/2018
 * Time: 09:22 PM
 */

namespace AdministracionBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;


class TiendaRepository extends EntityRepository
{
    public function findByTienda(Request $request)
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
            2=>"marca",
            3=>"nombre",
            4=>"usuario"
        );


        $valorSearch=$request->request->get('search')['value'];

        if($valorSearch)
        {

            $where=$where." tienda.nombre like :nombreTienda";
            $whereParameters=['nombreTienda'=>'%'.$valorSearch.'%'];

            $where=$where." or tienda.marca like :marcaTienda";
            $whereParameters+=['marcaTienda'=>'%'.$valorSearch.'%'];

            $where=$where." or usuario.email like :usuarioTienda";
            $whereParameters+=['usuarioTienda'=>'%'.$valorSearch.'%'];

            $flag=true;

        }


        if ($request->request->get("order")[0]["column"]>1)
        {
            $columnaOrder=$columnas[$request->request->get("order")[0]["column"]];
            $sentidoOrder=$request->request->get("order")[0]["dir"];


            if ($columnaOrder == "nombre") {
                $orderBy = "ORDER BY tienda.nombre ".$sentidoOrder;
            }
            if ($columnaOrder == "marca") {
                $orderBy = "ORDER BY tienda.marca ".$sentidoOrder;
            }
            if ($columnaOrder == "usuario") {
                $orderBy = "ORDER BY usuario.email ".$sentidoOrder;
            }
        }


        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  tienda                  
                  
              from 
              
              AdministracionBundle:Tienda tienda               
              LEFT JOIN  tienda.usuarioid usuario
             
              ".$where."  ".$orderBy." 
              
             
              
              
             "
        ;

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);
        $dql->setMaxResults($offset);
        $dql->setFirstResult($start);


        return $dql;
    }
    public function findByTiendaTotal(Request $request)
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

            $where=$where." tienda.nombre like :nombreTienda";
            $whereParameters=['nombreTienda'=>'%'.$valorSearch.'%'];

            $where=$where." or tienda.marca like :marcaTienda";
            $whereParameters+=['marcaTienda'=>'%'.$valorSearch.'%'];

            $where=$where." or usuario.email like :usuarioTienda";
            $whereParameters+=['usuarioTienda'=>'%'.$valorSearch.'%'];

            $flag=true;

        }


        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  tienda                  
                  
              from 
              
              AdministracionBundle:Tienda tienda               
              LEFT JOIN  tienda.usuarioid usuario
             
              ".$where." 
              
             
              
              
             "
        ;
        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }


}