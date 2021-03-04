<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificacionUsuario
 *
 * @ORM\Table(name="notificacion_usuario")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\NotificacionUsuarioRepository")
 */
class NotificacionUsuario
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne (targetEntity="AdministracionBundle\Entity\Notificacion")
     */
    private $notificacion;

    /**
     *
     * @ORM\ManyToOne (targetEntity="AdministracionBundle\Entity\Usuario", inversedBy="notificacionesUsuario")
     */
    private $usuario;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var text
     *
     * @ORM\Column(name="mensaje", type="text")
     */
    private $mensaje;

    /**
     * @var bool
     *
     * @ORM\Column(name="leida", type="boolean")
     */
    private $leida;

    public function __construct()
    {
        $this->fecha = new \DateTime('now');
        $this->leida = false;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNotificacion()
    {
        return $this->notificacion;
    }

    /**
     * @param mixed $notificacion
     */
    public function setNotificacion($notificacion)
    {
        $this->notificacion = $notificacion;
    }

    /**
     * @return mixed
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param mixed $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }


    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return NotificacionUsuario
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set leida
     *
     * @param boolean $leida
     *
     * @return NotificacionUsuario
     */
    public function setLeida($leida)
    {
        $this->leida = $leida;

        return $this;
    }

    /**
     * Get leida
     *
     * @return bool
     */
    public function isLeida()
    {
        return $this->leida;
    }

    /**
     * @return text
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }

    /**
     * @param text $mensaje
     */
    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
    }


}

