<?php

namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoSolicitudRetiro
 *
 * @ORM\Table(name="estado_solicitud_retiro", uniqueConstraints={@ORM\UniqueConstraint(name="slug", columns={"slug"})})
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\EstadoSolicitudRetiroRepository")
 */
class EstadoSolicitudRetiro {
    
    const ESTADO_SOLICITUD_PENDIENTE_SLUG = 'pendiente';
    const ESTADO_SOLICITUD_APROBADO_SLUG = 'aprobado';
    const ESTADO_SOLICITUD_RECHAZADO_SLUG = 'rechazado';
    const ESTADO_SOLICITUD_PAGO_REALIZADO_SLUG = 'pago-realizado';
    const ESTADO_SOLICITUD_PAGO_FALLIDO_SLUG = 'pago-fallido';
    const ESTADO_SOLICITUD_PAGO_PAYPAL_PENDIENTE_SLUG = 'pago-paypal-pendiente';
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;
    
    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;
    
    public function getNombre() {
        return $this->nombre;
    }
    
    public function getSlug() {
        return $this->slug;
    }
    
    public function __toString() {
        return $this->getNombre();
    }
    
    /**
     * Determina si el estado de la solicitud permite gestionar solicitudes de
     * retiro en el panel de administración
     * (solo las solicitudes pendientes o con pago fallido pueden editarse)
     * 
     * @return boolean
     */
    public function requiereGestion() {
        return 
            $this->getSlug() == EstadoSolicitudRetiro::ESTADO_SOLICITUD_PENDIENTE_SLUG ||
            $this->getSlug() == EstadoSolicitudRetiro::ESTADO_SOLICITUD_PAGO_FALLIDO_SLUG;
    }
    
    function puedeReintentarPago() {
        return $this->getSlug() == EstadoSolicitudRetiro::ESTADO_SOLICITUD_PAGO_FALLIDO_SLUG;
    }
    
}
