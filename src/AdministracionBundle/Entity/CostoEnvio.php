<?php


namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * CostoEnvio
 *
 * @ORM\Table(name="costo_envio", indexes={@ORM\Index(name="FKcostoenvio393904", columns={"provincia_id"}), @ORM\Index(name="FKcostoenvio978173", columns={"usuario_id"}), @ORM\Index(name="FKcostoenvio610978", columns={"ciudad_id"})})
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\CostoEnvioRepository")
 */
class CostoEnvio
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="costo", type="integer", nullable=true)
     */
    private $costo;

    /**
     * @var \Provincia
     *
     * @ORM\ManyToOne(targetEntity="Provincia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="provincia_id", referencedColumnName="id")
     * })
     */
    private $provinciaid;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuarioid;


    /**
     * @var \Ciudad
     *
     * @ORM\ManyToOne(targetEntity="Ciudad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ciudad_id", referencedColumnName="id")
     * })
     */
    private $ciudadid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="gratis", type="boolean", nullable=true)
     */
    private $gratis;

    /**
     * @var boolean
     *
     * @ORM\Column(name="todo_el_pais", type="boolean", nullable=true)
     */
    private $todoElPais;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mercadofree", type="boolean", nullable=true)
     */
    private $mercadofree;

	/**
	 * @var decimal
	 *
	 * @ORM\Column(name="peso", type="decimal", nullable=true)
	 */
	private $peso;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="ancho", type="integer", nullable=true)
	 */
	private $ancho;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="alto", type="integer", nullable=true)
	 */
	private $alto;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="profundidad", type="integer", nullable=true)
	 */
	private $profundidad;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getCosto()
	{
		return $this->costo;
	}

	/**
	 * @param int $costo
	 */
	public function setCosto($costo)
	{
		$this->costo = $costo;
	}

	/**
	 * @return \Provincia
	 */
	public function getProvinciaid()
	{
		return $this->provinciaid;
	}

	/**
	 * @param \Provincia $provinciaid
	 */
	public function setProvinciaid($provinciaid)
	{
		$this->provinciaid = $provinciaid;
	}

	/**
	 * @return \Usuario
	 */
	public function getUsuarioid()
	{
		return $this->usuarioid;
	}

	/**
	 * @param \Usuario $usuarioid
	 */
	public function setUsuarioid($usuarioid)
	{
		$this->usuarioid = $usuarioid;
	}

	/**
	 * @return \Ciudad
	 */
	public function getCiudadid()
	{
		return $this->ciudadid;
	}

	/**
	 * @param \Ciudad $ciudadid
	 */
	public function setCiudadid($ciudadid)
	{
		$this->ciudadid = $ciudadid;
	}

	/**
	 * @return bool
	 */
	public function isGratis()
	{
		return $this->gratis;
	}

	/**
	 * @param bool $gratis
	 */
	public function setGratis($gratis)
	{
		$this->gratis = $gratis;
	}

	/**
	 * @return bool
	 */
	public function isTodoElPais()
	{
		return $this->todoElPais;
	}

	/**
	 * @param bool $todoElPais
	 */
	public function setTodoElPais($todoElPais)
	{
		$this->todoElPais = $todoElPais;
	}

	/**
	 * @return bool
	 */
	public function isMercadofree()
	{
		return $this->mercadofree;
	}

	/**
	 * @param bool $mercadofree
	 */
	public function setMercadofree($mercadofree)
	{
		$this->mercadofree = $mercadofree;
	}

	/**
	 * @return decimal
	 */
	public function getPeso() {
		return $this->peso;
	}

	/**
	 * @param decimal $peso
	 */
	public function setPeso( $peso ) {
		$this->peso = $peso;
	}

	/**
	 * @return int
	 */
	public function getAncho() {
		return $this->ancho;
	}

	/**
	 * @param int $ancho
	 */
	public function setAncho( $ancho ) {
		$this->ancho = $ancho;
	}

	/**
	 * @return int
	 */
	public function getAlto() {
		return $this->alto;
	}

	/**
	 * @param int $alto
	 */
	public function setAlto( $alto ) {
		$this->alto = $alto;
	}

	/**
	 * @return int
	 */
	public function getProfundidad() {
		return $this->profundidad;
	}

	/**
	 * @param int $profundidad
	 */
	public function setProfundidad( $profundidad ) {
		$this->profundidad = $profundidad;
	}

	public function __toString() {

		return '$'.$this->costo;

	}


}