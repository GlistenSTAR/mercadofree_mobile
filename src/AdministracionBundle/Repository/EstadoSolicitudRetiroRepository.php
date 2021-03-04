<?php
namespace AdministracionBundle\Repository;
use Doctrine\ORM\EntityRepository;

use AdministracionBundle\Entity\EstadoSolicitudRetiro;

class EstadoSolicitudRetiroRepository extends EntityRepository
{
    public function findEstadoPendiente() {
        return $this->findOneBySlug(EstadoSolicitudRetiro::ESTADO_SOLICITUD_PENDIENTE_SLUG);
    }
    
    public function findEstadoAprobado() {
        return $this->findOneBySlug(EstadoSolicitudRetiro::ESTADO_SOLICITUD_APROBADO_SLUG);
    }
    
    public function findEstadoRechazado() {
        return $this->findOneBySlug(EstadoSolicitudRetiro::ESTADO_SOLICITUD_RECHAZADO_SLUG);
    }
    
    public function findEstadoPagoPaypalPendiente() {
        return $this->findOneBySlug(EstadoSolicitudRetiro::ESTADO_SOLICITUD_PAGO_PAYPAL_PENDIENTE_SLUG);
    }
    
    public function findEstadoPagoRealizado() {
        return $this->findOneBySlug(EstadoSolicitudRetiro::ESTADO_SOLICITUD_PAGO_REALIZADO_SLUG);
    }
    
    public function findEstadoPagoFallido() {
        return $this->findOneBySlug(EstadoSolicitudRetiro::ESTADO_SOLICITUD_PAGO_FALLIDO_SLUG);
    }
    
}
