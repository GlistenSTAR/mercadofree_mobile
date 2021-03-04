<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 20/03/2018
 * Time: 02:46 PM
 */

namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Configuracion
 *
 * @ORM\Table(name="configuracion")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\ConfiguracionRepository")
 */
class Configuracion
{
    const CONFIGURACION_DEFAULT_ID = 1;
    
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
     * @ORM\Column(name="email_admin", type="string", length=255, nullable=false)
     */
    private $emailAdministrador;

    /**
     * @var integer
     *
     * @ORM\Column(name="indice_popularidad_cat", type="integer", nullable=true)
     *
     */
    private $indicePopularidadCat;

    /**
     * @var Contacto
     *
     * @ORM\ManyToOne(targetEntity="Contacto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contacto_configuracionid", referencedColumnName="id")
     * })
     */
    private $contactoConfiguracionId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ofertas_semana", type="boolean",  nullable=true)
     */
    private $ofertasSemana;

    /**
     * @var integer
     *
     * @ORM\Column(name="cant_ofertas_semana", type="integer",  nullable=true)
     */
    private $cantOfertasSemana;


    /**
     * @var boolean
     *
     * @ORM\Column(name="publicidad_oferta", type="boolean",  nullable=true)
     */
    private $publicidadOferta;

    /**
     * @var boolean
     *
     * @ORM\Column(name="publicidad_producto", type="boolean",  nullable=true)
     */
    private $publicidadProducto;

    /**
     * @var boolean
     *
     * @ORM\Column(name="historial_visitas_categoria", type="boolean",  nullable=true)
     */
    private $historialVisitasCategoria;

    /**
     * @var boolean
     *
     * @ORM\Column(name="colecciones", type="boolean",  nullable=true)
     */
    private $colecciones;

    /**
     * @var boolean
     *
     * @ORM\Column(name="historial_ultimas_categorias", type="boolean",  nullable=true)
     */
    private $historialUltimasCategorias;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mostrar_historial", type="boolean",  nullable=true)
     */
    //private $mostrarHistorial;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mostrar_garantia", type="boolean",  nullable=true)
     */
    private $mostrarGarantia;

    /**
     * @var string
     *
     * @ORM\Column(name="texto_garantia", type="string", length=255, nullable=false)
     */
    private $textoGarantia;

    /**
     * @var integer
     * @ORM\Column(name="cantidad_minima_productos", type="integer", nullable=false)
     */
    private $cantidadMinimaProductos;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mostrar_paquetes_publicidad_producto", type="boolean",  nullable=true)
     */
    private $mostrarPaquetesPublicidad;
    
    /**
     * @var integer
     * @ORM\Column(name="tiempo_expiracion_publicaciones", type="integer", nullable=false)
     */
    private $tiempoExpiracion;
    
    /**
     * @var integer
     * @ORM\Column(name="max_incidencias_contacto", type="integer", nullable=false)
     */
    private $maximoIncidenciasContacto;

    /**
     * @ORM\OneToOne(targetEntity="ComisionVenta", mappedBy="configuracion")
     */
    protected $comision_venta;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="aprobar_automaticamente_retiros", type="boolean",  nullable=true)
     */
    protected $aprobarAutomaticamenteRetiros;

	/**
     * @var integer
     * @ORM\Column(name="limite_dias_valoracion_pedido", type="integer", nullable=false)
     */
    protected $limiteDiasValoracionPedido;

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
	 * @return string
	 */
	public function getEmailAdministrador()
	{
		return $this->emailAdministrador;
	}

	/**
	 * @param string $emailAdministrador
	 */
	public function setEmailAdministrador($emailAdministrador)
	{
		$this->emailAdministrador = $emailAdministrador;
	}

	/**
	 * @return int
	 */
	public function getIndicePopularidadCat()
	{
		return $this->indicePopularidadCat;
	}

	/**
	 * @param int $indicePopularidadCat
	 */
	public function setIndicePopularidadCat($indicePopularidadCat)
	{
		$this->indicePopularidadCat = $indicePopularidadCat;
	}

	/**
	 * @return Contacto
	 */
	public function getContactoConfiguracionId()
	{
		return $this->contactoConfiguracionId;
	}

	/**
	 * @param Contacto $contactoConfiguracionId
	 */
	public function setContactoConfiguracionId($contactoConfiguracionId)
	{
		$this->contactoConfiguracionId = $contactoConfiguracionId;
	}

	/**
	 * @return boolean
	 */
	public function isOfertasSemana()
	{
		return $this->ofertasSemana;
	}

	/**
	 * @param boolean $ofertasSemana
	 */
	public function setOfertasSemana($ofertasSemana)
	{
		$this->ofertasSemana = $ofertasSemana;
	}

	/**
	 * @return boolean
	 */
	public function isPublicidadOferta()
	{
		return $this->publicidadOferta;
	}

	/**
	 * @param boolean $publicidadOferta
	 */
	public function setPublicidadOferta($publicidadOferta)
	{
		$this->publicidadOferta = $publicidadOferta;
	}

	/**
	 * @return boolean
	 */
	public function isPublicidadProducto()
	{
		return $this->publicidadProducto;
	}

	/**
	 * @param boolean $publicidadProducto
	 */
	public function setPublicidadProducto($publicidadProducto)
	{
		$this->publicidadProducto = $publicidadProducto;
	}

	/**
	 * @return boolean
	 */
	public function isHistorialVisitasCategoria()
	{
		return $this->historialVisitasCategoria;
	}

	/**
	 * @param boolean $historialVisitasCategoria
	 */
	public function setHistorialVisitasCategoria($historialVisitasCategoria)
	{
		$this->historialVisitasCategoria = $historialVisitasCategoria;
	}

	/**
	 * @return boolean
	 */
	public function isColecciones()
	{
		return $this->colecciones;
	}

	/**
	 * @param boolean $colecciones
	 */
	public function setColecciones($colecciones)
	{
		$this->colecciones = $colecciones;
	}

	/**
	 * @return boolean
	 */
	public function isHistorialUltimasCategorias()
	{
		return $this->historialUltimasCategorias;
	}

	/**
	 * @param boolean $historialUltimasCategorias
	 */
	public function setHistorialUltimasCategorias($historialUltimasCategorias)
	{
		$this->historialUltimasCategorias = $historialUltimasCategorias;
	}

	/**
	 * @return boolean
	 */
//    public function isMostrarHistorial()
//    {
//        return $this->mostrarHistorial;
//    }

	/**
	 * @param boolean $mostrarHistorial
	 */
//    public function setMostrarHistorial($mostrarHistorial)
//    {
//        $this->mostrarHistorial = $mostrarHistorial;
//    }

	/**
	 * @return boolean
	 */
	public function isMostrarGarantia()
	{
		return $this->mostrarGarantia;
	}

	/**
	 * @param boolean $mostrarGarantia
	 */
	public function setMostrarGarantia($mostrarGarantia)
	{
		$this->mostrarGarantia = $mostrarGarantia;
	}

	/**
	 * @return string
	 */
	public function getTextoGarantia()
	{
		return $this->textoGarantia;
	}

	/**
	 * @param string $textoGarantia
	 */
	public function setTextoGarantia($textoGarantia)
	{
		$this->textoGarantia = $textoGarantia;
	}


    /**
     * Get ofertasSemana
     *
     * @return boolean 
     */
    public function getOfertasSemana()
    {
        return $this->ofertasSemana;
    }

    /**
     * Get publicidadOferta
     *
     * @return boolean 
     */
    public function getPublicidadOferta()
    {
        return $this->publicidadOferta;
    }

    /**
     * Get publicidadProducto
     *
     * @return boolean 
     */
    public function getPublicidadProducto()
    {
        return $this->publicidadProducto;
    }

    /**
     * Get historialVisitasCategoria
     *
     * @return boolean 
     */
    public function getHistorialVisitasCategoria()
    {
        return $this->historialVisitasCategoria;
    }

    /**
     * Get colecciones
     *
     * @return boolean 
     */
    public function getColecciones()
    {
        return $this->colecciones;
    }

    /**
     * Get historialUltimasCategorias
     *
     * @return boolean 
     */
    public function getHistorialUltimasCategorias()
    {
        return $this->historialUltimasCategorias;
    }

    /**
     * Get mostrarGarantia
     *
     * @return boolean 
     */
    public function getMostrarGarantia()
    {
        return $this->mostrarGarantia;
    }

    /**
     * Set cantidadMinimaProductos
     *
     * @param integer $cantidadMinimaProductos
     * @return Configuracion
     */
    public function setCantidadMinimaProductos($cantidadMinimaProductos)
    {
        $this->cantidadMinimaProductos = $cantidadMinimaProductos;

        return $this;
    }

    /**
     * Get cantidadMinimaProductos
     *
     * @return integer 
     */
    public function getCantidadMinimaProductos()
    {
        return $this->cantidadMinimaProductos;
    }

	/**
	 * @return int
	 */
	public function getCantOfertasSemana()
	{
		return $this->cantOfertasSemana;
	}

	/**
	 * @param int $cantOfertasSemana
	 */
	public function setCantOfertasSemana($cantOfertasSemana)
	{
		$this->cantOfertasSemana = $cantOfertasSemana;
	}

    /**
     * @return bool
     */
    public function isMostrarPaquetesPublicidad()
    {
        return $this->mostrarPaquetesPublicidad;
    }

    /**
     * @param bool $mostrarPaquetesPublicidad
     */
    public function setMostrarPaquetesPublicidad($mostrarPaquetesPublicidad)
    {
        $this->mostrarPaquetesPublicidad = $mostrarPaquetesPublicidad;
    }

    function getTiempoExpiracion() {
        return $this->tiempoExpiracion;
    }

    /**
    * @param int $tiempoExpiracion
    */
    function setTiempoExpiracion($tiempoExpiracion) {
        $this->tiempoExpiracion = $tiempoExpiracion;
    }
    
    function getMaximoIncidenciasContacto() {
        return $this->maximoIncidenciasContacto;
    }

    /**
    * @param int $maximoIncidenciasContacto
    */
    function setMaximoIncidenciasContacto($maximoIncidenciasContacto) {
        $this->maximoIncidenciasContacto = $maximoIncidenciasContacto;
    }



    /**
     * Get mostrarPaquetesPublicidad
     *
     * @return boolean
     */
    public function getMostrarPaquetesPublicidad()
    {
        return $this->mostrarPaquetesPublicidad;
    }

    /**
     * Set comisionVenta
     *
     * @param \AdministracionBundle\Entity\ComisionVenta $comisionVenta
     *
     * @return Configuracion
     */
    public function setComisionVenta(\AdministracionBundle\Entity\ComisionVenta $comisionVenta = null)
    {
        $this->comision_venta = $comisionVenta;

        return $this;
    }

    /**
     * Get comisionVenta
     *
     * @return \AdministracionBundle\Entity\ComisionVenta
     */
    public function getComisionVenta()
    {
        return $this->comision_venta;
    }
    
    function getAprobarAutomaticamenteRetiros() {
        return $this->aprobarAutomaticamenteRetiros;
    }

    function setAprobarAutomaticamenteRetiros($aprobarAutomaticamenteRetiros) {
        $this->aprobarAutomaticamenteRetiros = $aprobarAutomaticamenteRetiros;
    }

    function getLimiteDiasValoracionPedido() {
        return $this->limiteDiasValoracionPedido;
    }

    function setLimiteDiasValoracionPedido($limiteDiasValoracionPedido) {
        $this->limiteDiasValoracionPedido = $limiteDiasValoracionPedido;
    }

}
