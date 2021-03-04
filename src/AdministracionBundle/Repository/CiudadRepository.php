<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 21/03/2018
 * Time: 11:14 AM
 */

namespace AdministracionBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class CiudadRepository extends EntityRepository
{
    public function findByProvincia($idProvincia,$order = 'ASC')
    {
        $em = $this->getEntityManager();

        return $em->createQuery("select c from AdministracionBundle:Ciudad c where c.provinciaid = :idProvincia order by c.ciudadNombre ".$order)
            ->setParameter('idProvincia',$idProvincia)->getResult();
    }

}