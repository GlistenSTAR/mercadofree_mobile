<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 17/04/2018
 * Time: 05:23 PM
 */

namespace AdministracionBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class ValoracionRepository extends EntityRepository
{
    public function findByValoracion(Request $request)
    {
        $em=$this->getEntityManager();

        $where="where";
        $having="";
        $orderBy="";

        $flag=false;

        $whereParameters=array();

        $start=$request->request->get('start');


        $idProducto=$request->request->get('idProducto');

        $where=$where." producto.id = :idProducto";
        $whereParameters+=['idProducto'=>$idProducto];

        $sql="SELECT
                 valoracion
                FROM
                
               AdministracionBundle:Valoracion valoracion
               INNER JOIN valoracion.productoid producto
               
               ".$where."";

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);

        $dql->setMaxResults(5);

        $dql->setFirstResult($start);


        return $dql;
    }

    public function findByValoracionTotal(Request $request)
    {
        $em=$this->getEntityManager();

        $where="where";
        $having="";
        $orderBy="";

        $flag=false;

        $whereParameters=array();

        $start=$request->request->get('start');


        $idProducto=$request->request->get('idProducto');

        $where=$where." producto.id = :idProducto";
        $whereParameters+=['idProducto'=>$idProducto];

        $sql="SELECT
                 valoracion
                FROM
                
               AdministracionBundle:Valoracion valoracion
               INNER JOIN valoracion.productoid producto
               
               ".$where."";

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }

}