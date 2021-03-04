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

class VisitaRepository extends EntityRepository
{

    public function findAllGroupByFecha($usuario, $historial = null)
    {
        $qb = $this->createQueryBuilder('v');
        $qb->select('v', 'p', 'c', 'up', 'ep')
            ->join('v.productoid', 'p')
            ->join('p.categoriaid', 'c')
            ->join('p.usuarioid', 'up')
            ->join('p.estadoProductoid', 'ep')
            ->where('v.usuarioid = :user')
            ->setParameter('user', $usuario);
        if($historial == null){
            $qb->andWhere('v.historial is null');
        }
        $result = $qb->getQuery()->getScalarResult();
        return $result;
    }

}
