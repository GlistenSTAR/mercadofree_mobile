<?php

namespace AdministracionBundle\Repository;

use Doctrine\ORM\EntityRepository;

use AdministracionBundle\Entity\Usuario;

class PreguntaRepository extends EntityRepository
{
    public function preguntasRespondidasPorVendedor(Usuario $vendedor) {
        
        $qb = $this->createQueryBuilder('preg');
        
        $qb ->join('preg.productoid','prod')
            ->join('prod.usuarioid', 'u')
            ->where('preg.respuesta is not null')
            ->andWhere('u = :vendedor')
            ->setParameter('vendedor', $vendedor);
        
        return $qb->getQuery()->getResult();
    }
}
