<?php


namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * UsuarioObjetivo
 *
 * @ORM\Table(name="usuario_objetivo", indexes={@ORM\Index(name="FKobjetivo_c197259", columns={"objetivoid"}), @ORM\Index(name="FKusuario_c490229", columns={"usuarioid"})})
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\UsuarioObjetivoRepository")
 */
class UsuarioObjetivo
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Objetivo", inversedBy="objetivoUsuario", cascade={"persist"})
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="objetivoid", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $objetivoid;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="objetivoUsuario", cascade={"persist"})
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="usuarioid", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $usuarioid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaFin", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var integer
     *
     * @ORM\Column(name="puntos", type="integer", nullable=true)
     *
     */
    private $puntos;

    /**
     * @return mixed
     */
    public function getObjetivoid()
    {
        return $this->objetivoid;
    }

    /**
     * @param mixed $objetivoid
     */
    public function setObjetivoid($objetivoid)
    {
        $this->objetivoid = $objetivoid;
    }

    /**
     * @return mixed
     */
    public function getUsuarioid()
    {
        return $this->usuarioid;
    }

    /**
     * @param mixed $usuarioid
     */
    public function setUsuarioid($usuarioid)
    {
        $this->usuarioid = $usuarioid;
    }

    /**
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param \DateTime $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * @return int
     */
    public function getPuntos()
    {
        return $this->puntos;
    }

    /**
     * @param int $puntos
     */
    public function setPuntos($puntos)
    {
        $this->puntos = $puntos;
    }
}