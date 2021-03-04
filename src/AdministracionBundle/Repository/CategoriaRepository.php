<?php


namespace AdministracionBundle\Repository;
use Doctrine\ORM\EntityRepository;

class CategoriaRepository extends EntityRepository
{

    public function findMaxLevel()
    {
        $em=$this->getEntityManager();
        $sql="select 

                  MAX (categoria.nivel)
                  
              from 
              
              AdministracionBundle:Categoria categoria   
              
             "
        ;

        $dql=$em->createQuery($sql);

        return $dql;
    }


}