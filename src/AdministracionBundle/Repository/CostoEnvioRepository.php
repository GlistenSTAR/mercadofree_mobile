<?php


namespace AdministracionBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;


class CostoEnvioRepository extends EntityRepository
{
    public function findByCostoEnvio(Request $request)
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
            1=>"provincia",
            2=>"ciudad",
            3=>"costo"
        );


        $valorSearch=$request->request->get('search')['value'];

        if($valorSearch)
        {

            $where=$where." provincia.nombre like :nombreProvincia";

            $where=$where." or ciudad.ciudadNombre like :ciudadNombre";

            $where=$where." or costoenvio.costo like :costoEnvio";
            $whereParameters=['nombreProvincia'=>'%'.$valorSearch.'%', 'ciudadNombre'=>'%'.$valorSearch.'%', 'costoEnvio'=>'%'.$valorSearch.'%'];

            

            $flag=true;

        }
        $columnaOrder=$columnas[$request->request->get("order")[0]["column"]];
        $sentidoOrder=$request->request->get("order")[0]["dir"];


        if ($columnaOrder == "provincia") {
            $orderBy = "ORDER BY provincia.nombre ".$sentidoOrder;
        }
        if ($columnaOrder == "ciudad") {
            $orderBy = "ORDER BY ciudad.ciudadNombre ".$sentidoOrder;
        }
        if ($columnaOrder == "costo") {
            $orderBy = "ORDER BY costoenvio.costo ".$sentidoOrder;
        }


        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  costoenvio                  
                  
              from 
              
              AdministracionBundle:CostoEnvio costoenvio 
              INNER JOIN  costoenvio.provinciaid provincia 
              LEFT JOIN  costoenvio.ciudadid ciudad   
             
              ".$where."  ".$orderBy." 
              
             
              
              
             "
        ;

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);
        $dql->setMaxResults($offset);
        $dql->setFirstResult($start);


        return $dql;
    }
    public function findByCostoEnvioTotal(Request $request)
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
            1=>"provincia",
            2=>"ciudad",
            3=>"costo"
        );


        $valorSearch=$request->request->get('search')['value'];

        if($valorSearch)
        {

            $where=$where." provincia.nombre like :nombreProvincia";

            $where=$where." or ciudad.ciudadNombre like :ciudadNombre";

            $where=$where." or costoenvio.costo like :costoEnvio";
            $whereParameters=['nombreProvincia'=>'%'.$valorSearch.'%', 'ciudadNombre'=>'%'.$valorSearch.'%', 'costoEnvio'=>'%'.$valorSearch.'%'];

            $flag=true;

        }

        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  costoenvio                  
                  
              from 
              
              AdministracionBundle:CostoEnvio costoenvio 
              INNER JOIN  costoenvio.provinciaid provincia 
              LEFT JOIN  costoenvio.ciudadid ciudad   
             
              ".$where."  ".$orderBy." 
              
             
              
              
             "
        ;

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }
}