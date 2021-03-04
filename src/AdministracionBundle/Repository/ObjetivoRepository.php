<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 12/03/2018
 * Time: 09:43 PM
 */

namespace AdministracionBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class ObjetivoRepository extends EntityRepository
{
    public function findByObjetivo(Request $request)
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
            1=>"nombre",
            2=>"icono",
            3=>"puntos",
            4=>"visible",
            5=>"categoria"
        );


        $valorSearch=$request->request->get('search')['value'];

        if($valorSearch)
        {

            $where=$where." objetivo.nombre like :nombreObjetivo";
            $whereParameters=['nombreObjetivo'=>'%'.$valorSearch.'%'];

            $where=$where." objetivo.icono like :iconoObjetivo";
            $whereParameters=['iconoObjetivo'=>'%'.$valorSearch.'%'];

            $where=$where." objetivo.puntos like :puntosObjetivo";
            $whereParameters=['puntosObjetivo'=>'%'.$valorSearch.'%'];

            $where=$where." objetivo.visible like :visibleObjetivo";
            $whereParameters=['visibleObjetivo'=>'%'.$valorSearch.'%'];

            $where=$where." categoriaObjetivo.nombre like :categoriaObjetivo";
            $whereParameters=['categoriaObjetivo'=>'%'.$valorSearch.'%'];


            $flag=true;

        }
        $columnaOrder=$columnas[$request->request->get("order")[0]["column"]];
        $sentidoOrder=$request->request->get("order")[0]["dir"];


        if ($columnaOrder == "nombre") {
            $orderBy = "ORDER BY objetivo.nombre ".$sentidoOrder;
        }
        if ($columnaOrder == "icono") {
            $orderBy = "ORDER BY objetivo.icono ".$sentidoOrder;
        }
        if ($columnaOrder == "puntos") {
            $orderBy = "ORDER BY objetivo.puntos ".$sentidoOrder;
        }
        if ($columnaOrder == "visible") {
            $orderBy = "ORDER BY objetivo.visible ".$sentidoOrder;
        }
        if ($columnaOrder == "categoria") {
            $orderBy = "ORDER BY categoriaObjetivo.nombre ".$sentidoOrder;
        }

        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  objetivo                  
                  
              from 
              
              AdministracionBundle:Objetivo objetivo 
              LEFT JOIN  objetivo.categoriaobjetivoid categoriaObjetivo
             
             
              ".$where."  ".$orderBy." 
              
             
              
              
             "
        ;

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);
        $dql->setMaxResults($offset);
        $dql->setFirstResult($start);


        return $dql;
    }
    public function findByObjetivoTotal(Request $request)
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

            $where=$where." objetivo.nombre like :nombreObjetivo";
            $whereParameters=['nombreObjetivo'=>'%'.$valorSearch.'%'];

            $where=$where." objetivo.icono like :iconoObjetivo";
            $whereParameters=['iconoObjetivo'=>'%'.$valorSearch.'%'];

            $where=$where." objetivo.puntos like :puntosObjetivo";
            $whereParameters=['puntosObjetivo'=>'%'.$valorSearch.'%'];

            $where=$where." objetivo.visible like :visibleObjetivo";
            $whereParameters=['visibleObjetivo'=>'%'.$valorSearch.'%'];

            $where=$where." categoriaObjetivo.nombre like :categoriaObjetivo";
            $whereParameters=['categoriaObjetivo'=>'%'.$valorSearch.'%'];


            $flag=true;

        }

        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  objetivo                  
                  
              from 
              
              AdministracionBundle:Objetivo objetivo 
              LEFT JOIN  objetivo.categoriaobjetivoid categoriaObjetivo
             
             
              ".$where."  ".$orderBy." 
              
             
              
              
             "
        ;

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }


}
