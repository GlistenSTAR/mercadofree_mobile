<?php
namespace AdministracionBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

use AdministracionBundle\Entity\EstadoSolicitudRetiro;

class SolicitudRetiroFondosRepository extends EntityRepository
{
    public function findBySolicitudRetiro(Request $request)
    {
        $start=$request->request->get('start');
        $offset=$request->request->get('length');
        
        $dql = $this->findBySolicitudRetiroTotal($request);
        $dql->setMaxResults($offset);
        $dql->setFirstResult($start);
        
        return $dql;
    }
    
    public function findBySolicitudRetiroTotal(Request $request)
    {
        $em=$this->getEntityManager();

        $where="";
        $having="";
        $orderBy="";

        $flag=false;

        $whereParameters=array();

        $columnas=array(
            0=>"id",
            1=>"fecha",
            2=>"emailPaypal",
            3=>"monto",
            4=>"estado"
        );

        $valorSearch=$request->request->get('search')['value'];

        if($valorSearch)
        {
            $where="where";
            $where=$where." solicitudretirofondos.emailPaypal like :emailPaypal";

            $where=$where." or estadosolicitudretiro.nombre like :estado";
            $whereParameters=['emailPaypal'=>'%'.$valorSearch.'%', 'estado'=>'%'.$valorSearch.'%'];
//
//            $where=$where." costoenvio.costo like :costoEnvio";
//            $whereParameters=['costoEnvio'=>'%'.$valorSearch.'%'];

        }
        $columnaOrder=$columnas[$request->request->get("order")[0]["column"]];
        $sentidoOrder=$request->request->get("order")[0]["dir"];

        if ($columnaOrder == "id") {
            $orderBy = "ORDER BY solicitudretirofondos.id ".$sentidoOrder;
        }
        if ($columnaOrder == "fecha") {
            $orderBy = "ORDER BY solicitudretirofondos.fecha ".$sentidoOrder;
        }
        if ($columnaOrder == "emailPaypal") {
            $orderBy = "ORDER BY solicitudretirofondos.emailPaypal ".$sentidoOrder;
        }
        if ($columnaOrder == "monto") {
            $orderBy = "ORDER BY solicitudretirofondos.monto ".$sentidoOrder;
        }
        if ($columnaOrder == "estado") {
            $orderBy = "ORDER BY estadosolicitudretiro.nombre ".$sentidoOrder;
        }

        $sql="select 

                  solicitudretirofondos
                  
              from 
              
              AdministracionBundle:SolicitudRetiroFondos solicitudretirofondos 
              JOIN  solicitudretirofondos.estadoSolicitudRetiro estadosolicitudretiro
             
              ".$where."  ".$orderBy." 
              
             
              
              
             "
        ;

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }
    public function findAprobadas() {
        $qb = $this->createQueryBuilder('srf')
                    ->join('srf.estadoSolicitudRetiro', 'esr');
        
        $qb->andWhere($qb->expr()->like('esr.slug', ':estado_aprobado_slug'));
        $qb->setParameter('estado_aprobado_slug', EstadoSolicitudRetiro::ESTADO_SOLICITUD_APROBADO_SLUG);
        
        $q = $qb->getQuery();
        
        return $q->execute();
    }
    
    public function findPagoPaypalPendientes() {
        $qb = $this->createQueryBuilder('srf')
                    ->join('srf.estadoSolicitudRetiro', 'esr');
        
        $qb->andWhere($qb->expr()->like('esr.slug', ':estado_aprobado_slug'));
        $qb->setParameter('estado_aprobado_slug', EstadoSolicitudRetiro::ESTADO_SOLICITUD_PAGO_PAYPAL_PENDIENTE_SLUG);
        
        $q = $qb->getQuery();
        
        return $q->execute();
    }
}
