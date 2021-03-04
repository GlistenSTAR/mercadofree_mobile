<?php

namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cliente
 * @ORM\Entity()
 * @ORM\Table(name="cliente", indexes={@ORM\Index(name="FKcliente32982", columns={"usuarioid"})})
 */
class Cliente
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;



    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=255, nullable=true)
     */
    private $apellidos;

    /**
     * @var string
     *
     * @ORM\Column(name="dni", type="string", length=255, nullable=true)
     */
    private $dni;

    /**
     * @var Usuario
     *
     * @ORM\OneToOne(targetEntity="Usuario", inversedBy="clienteid")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuarioid", referencedColumnName="id")
     * })
     */
    private $usuarioid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="alert_producto_vendido", type="boolean", nullable=true)
     */
    private $alertProductoVendido;

    /**
     * @var boolean
     *
     * @ORM\Column(name="alert_pregunta_publicacion", type="boolean", nullable=true)
     */
    private $alertPreguntaPublicacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="alert_finalizo_publicacion", type="boolean", nullable=true)
     */
    private $alertFinalizoPublicacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="alert_compra_producto", type="boolean", nullable=true)
     */
    private $alertCompraProducto;

    /**
     * @var boolean
     *
     * @ORM\Column(name="alert_proximo_fin_publicacion", type="boolean", nullable=true)
     */
    private $alertProximoFinPublicacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="alert_login", type="boolean", nullable=true)
     */
    private $alertLogin;

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
     * @return Usuario
     */
    public function getUsuarioid()
    {
        return $this->usuarioid;
    }

    /**
     * @param Usuario $usuarioid
     */
    public function setUsuarioid($usuarioid)
    {
        $this->usuarioid = $usuarioid;
    }

    /**
     * @return string
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * @param string $dni
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
    }

    /**
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @return string
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * @param string $apellidos
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
    }

    /**
     * @return boolean
     */
    public function isAlertProductoVendido()
    {
        return $this->alertProductoVendido;
    }

    /**
     * @param boolean $alertProductoVendido
     */
    public function setAlertProductoVendido($alertProductoVendido)
    {
        $this->alertProductoVendido = $alertProductoVendido;
    }

    /**
     * @return boolean
     */
    public function isAlertPreguntaPublicacion()
    {
        return $this->alertPreguntaPublicacion;
    }

    /**
     * @param boolean $alertPreguntaPublicacion
     */
    public function setAlertPreguntaPublicacion($alertPreguntaPublicacion)
    {
        $this->alertPreguntaPublicacion = $alertPreguntaPublicacion;
    }

    /**
     * @return boolean
     */
    public function isAlertFinalizoPublicacion()
    {
        return $this->alertFinalizoPublicacion;
    }

    /**
     * @param boolean $alertFinalizoPublicacion
     */
    public function setAlertFinalizoPublicacion($alertFinalizoPublicacion)
    {
        $this->alertFinalizoPublicacion = $alertFinalizoPublicacion;
    }

    /**
     * @return boolean
     */
    public function isAlertCompraProducto()
    {
        return $this->alertCompraProducto;
    }

    /**
     * @param boolean $alertCompraProducto
     */
    public function setAlertCompraProducto($alertCompraProducto)
    {
        $this->alertCompraProducto = $alertCompraProducto;
    }

    /**
     * @return boolean
     */
    public function isAlertProximoFinPublicacion()
    {
        return $this->alertProximoFinPublicacion;
    }

    /**
     * @param boolean $alertProximoFinPublicacion
     */
    public function setAlertProximoFinPublicacion($alertProximoFinPublicacion)
    {
        $this->alertProximoFinPublicacion = $alertProximoFinPublicacion;
    }

    /**
     * @return boolean
     */
    public function isAlertLogin()
    {
        return $this->alertLogin;
    }

    /**
     * @param boolean $alertLogin
     */
    public function setAlertLogin($alertLogin)
    {
        $this->alertLogin = $alertLogin;
    }

    public function getFirstLetterName(){
    	return substr($this->nombre,0,2);
    }
    
    public function __toString() {
        return $this->getApellidos().' '.$this->getNombre();
    }


}