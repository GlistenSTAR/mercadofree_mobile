<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 29/04/2018
 * Time: 12:29 PM
 */

namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * UsuarioCampanna
 *
 * @ORM\Table(name="usuario_campanna", indexes={@ORM\Index(name="FKusuario_c197259", columns={"usuarioid"}), @ORM\Index(name="FKusuario_c490229", columns={"campannaid"}), @ORM\Index(name="FKusuario_c978173", columns={"estado_campannaid"})})
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\ProductoRepository")
 */

class UsuarioCampanna
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="usuarioCampanna", cascade={"persist"})
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="usuarioid", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $usuarioid;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Campanna", inversedBy="usuarioCampanna", cascade={"persist"})
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="campannaid", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $campannaid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;


    /**
     * @var \EstadoCampanna
     *
     * @ORM\ManyToOne(targetEntity="EstadoCampanna")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estado_campannaid", referencedColumnName="id")
     * })
     */
    private $estadoCampannaid;

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
	 * @return \EstadoCampanna
	 */
	public function getEstadoCampannaid()
	{
		return $this->estadoCampannaid;
	}

	/**
	 * @param \EstadoCampanna $estadoCampannaid
	 */
	public function setEstadoCampannaid($estadoCampannaid)
	{
		$this->estadoCampannaid = $estadoCampannaid;
	}

	/**
	 * @return mixed
	 */
	public function getCampannaid()
	{
		return $this->campannaid;
	}

	/**
	 * @param mixed $campannaid
	 */
	public function setCampannaid($campannaid)
	{
		$this->campannaid = $campannaid;
	}

}