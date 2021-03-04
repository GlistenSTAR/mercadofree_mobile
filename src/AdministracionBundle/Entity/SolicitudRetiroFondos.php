<?php

namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * SolicitudRetiroFondos
 *
 * @ORM\Table(name="solicitud_retiro_fondos")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\SolicitudRetiroFondosRepository")
 */
class SolicitudRetiroFondos {
    
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
     * @ORM\Column(name="email_paypal", type="string", length=255, nullable=false)
     */
    private $emailPaypal;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="monto", type="decimal", nullable=false, scale=2)
     */
    private $monto;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;
    
    /**
     * @var string
     *
     * @ORM\Column(name="observaciones_rechazo", type="string", length=255, nullable=true)
     */
    private $observacionesRechazo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="cod_respuesta_pasarela", type="string", length=15, nullable=true)
     */
    private $codRespuestaPasarela;
    
    /**
     * @var string
     *
     * @ORM\Column(name="mensaje_error_pasarela", type="string", length=255, nullable=true)
     */
    private $mensajeErrorPasarela;
    
    /**
     * @var string
     *
     * @ORM\Column(name="referencia_pasarela", type="text", nullable=true)
     */
    private $referenciaPasarela;
    
    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $usuario;
    
    /**
     * @var EstadoSolicitudRetiro
     *
     * @ORM\ManyToOne(targetEntity="EstadoSolicitudRetiro")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_estado_solicitud_retiro", referencedColumnName="id")
     * })
     */
    private $estadoSolicitudRetiro;
    
    private function __construct($id, $emailPaypal, $monto, $fecha, Usuario $usuario, EstadoSolicitudRetiro $estadoSolicitudRetiro) {
        $this->id = $id;
        $this->emailPaypal = $emailPaypal;
        $this->monto = $monto;
        $this->fecha = $fecha;
        $this->observacionesRechazo = null;
        $this->codRespuestaPasarela = null;
        $this->mensajeErrorPasarela = null;
        $this->referenciaPasarela = null;
        $this->usuario = $usuario;
        $this->estadoSolicitudRetiro = $estadoSolicitudRetiro;
    }
    
    static public function nuevaSolicitudRetiroFondos($emailPaypal, $monto, $fecha, Usuario $usuario, EstadoSolicitudRetiro $estadoSolicitudRetiro) {
        return new self(null, $emailPaypal, $monto, $fecha, $usuario, $estadoSolicitudRetiro);
    }
    
    function getId() {
        return $this->id;
    }

    function getEmailPaypal() {
        return $this->emailPaypal;
    }

    /**
     * @return float
     */
    function getMonto() {
        return $this->monto;
    }

    /**
     * @return \DateTime
     */
    function getFecha() {
        return $this->fecha;
    }

    function getObservacionesRechazo() {
        return $this->observacionesRechazo;
    }
    
    function setObservacionesRechazo($observacionesRechazo){
        $this->observacionesRechazo = $observacionesRechazo;
    }

    function getCodRespuestaPasarela() {
        return $this->codRespuestaPasarela;
    }
    
    function setCodRespuestaPasarela($codRespuestaPasarela) {
        $this->codRespuestaPasarela = $codRespuestaPasarela;
    }

    function getMensajeErrorPasarela() {
        return $this->mensajeErrorPasarela;
    }
    
    function setMensajeErrorPasarela($mensajeErrorPasarela) {
        $this->mensajeErrorPasarela = $mensajeErrorPasarela;
    }

    function getReferenciaPasarela() {
        return $this->referenciaPasarela;
    }
    
    function setReferenciaPasarela($referenciaPasarela) {
        $this->referenciaPasarela = $referenciaPasarela;
    }

    /**
     * @return Usuario
     */
    function getUsuario() {
        return $this->usuario;
    }

    /**
     * @return EstadoSolicitudRetiro
     */
    function getEstadoSolicitudRetiro() {
        return $this->estadoSolicitudRetiro;
    }
    
    /**
     * @param EstadoSolicitudRetiro $estadoSolicitudRetiro
     */
    function setEstadoSolicitudRetiro(EstadoSolicitudRetiro $estadoSolicitudRetiro) {
        $this->estadoSolicitudRetiro = $estadoSolicitudRetiro;
    }

    /**
     * Determina si la solicitud puede gestionarse en el panel de administración
     * (solo las solicitudes pendientes o con pago fallido pueden editarse)
     * 
     * @return boolean
     */
    function requiereGestion() {
        return $this->getEstadoSolicitudRetiro()->requiereGestion();
    }
    
    function puedeReintentarPago() {
        return $this->getEstadoSolicitudRetiro()->puedeReintentarPago();
    }
    
    function getEmailUsuario() {
        return $this->getUsuario()->getEmail();
    }
}
