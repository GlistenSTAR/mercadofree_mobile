<?php

namespace AdministracionBundle\Repository;

/**
 * ComisionVentaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ComisionVentaRepository extends \Doctrine\ORM\EntityRepository
{
    public function duplicadoPorCategoria($comision)
    {
        $idCategoria = $comision->getCategoria()->getId();
        $idComision = $comision->getId();
        $q = $this->createQueryBuilder('cv')
            ->select('cv.id')
            ->where('cv.categoria = :categoria')
            ->setParameter('categoria', $idCategoria);
        
        if (!empty($idComision)) {
            $q->andWhere('cv.id <> :id')
                ->setParameter('id', $comision->getId());
        }

        $result = $q->getQuery()->getResult();
        return !empty($result);
    }

    public function duplicadoPorRango($comision) 
    {
        $id = $comision->getId();
        $min = $comision->getPrecioMinimo();
        $max = $comision->getPrecioMaximo();
        $q = $this->createQueryBuilder('cv')
            ->select('cv.id')
            ->where('(cv.precioMinimo >= :min AND cv.precioMinimo <= :max) OR (cv.precioMaximo >= :min AND cv.precioMaximo <= :max) OR (cv.precioMinimo <= :min AND cv.precioMaximo >= :max)')
            ->setParameter('min', $min)
            ->setParameter('max', $max);
            
        if (!empty($id)) {
            $q->andWhere('cv.id <> :id')
                ->setParameter('id', $id);
        }

        $result = $q->getQuery()->getResult();
        return !empty($result);
    }

    public function getComisionPorCategoria($categoriaId)
    {
        $q = $this->createQueryBuilder('cv')
            ->innerJoin('cv.categoria', 'c')
            ->where('c.id = :categoria')
            ->setParameter('categoria', $categoriaId)
            ->getQuery();

        try {
            $comision = $q->getSingleResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $comision = null;
        }

        return $comision;
    }

    public function getComisionPorPrecio($precio)
    {
        $q = $this->createQueryBuilder('cv')
            ->where('cv.precioMinimo <= :price')
            ->andWhere('cv.precioMaximo >= :price')
            ->setParameter('price', $precio)
            ->getQuery();

        try {
            $comision = $q->getSingleResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $comision = null;
        }

        return $comision;
    }
}

