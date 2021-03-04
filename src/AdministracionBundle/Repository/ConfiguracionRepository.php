<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 20/03/2018
 * Time: 03:03 PM
 */

namespace AdministracionBundle\Repository;
use Doctrine\ORM\EntityRepository;
use AdministracionBundle\Entity\Configuracion;


class ConfiguracionRepository extends EntityRepository
{
    public function getDefaultConfiguracion() {
        return $this->find(Configuracion::CONFIGURACION_DEFAULT_ID);
    }
}