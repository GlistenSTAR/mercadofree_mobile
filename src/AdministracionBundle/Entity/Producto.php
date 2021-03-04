<?php

namespace AdministracionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Producto
 *
 * @ORM\Table(name="producto", indexes={@ORM\Index(name="FKproducto393903", columns={"categoriaid"}), @ORM\Index(name="FKproducto978173", columns={"estado_productoid"}), @ORM\Index(name="FKproducto610978", columns={"usuarioid"})})
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\ProductoRepository")
 */
class Producto
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
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @var decimal
     *
     * @ORM\Column(name="precio", type="decimal", nullable=true)
     */
    private $precio;

    /**
     * @var integer
     *
     * @ORM\Column(name="cuotasPagar", type="integer", nullable=true)
     */
    private $cuotaspagar;

    /**
     * @var integer
     *
     * @ORM\Column(name="step", type="integer", nullable=true)
     */
    private $step;

    /**
     * @var integer
     *
     * @ORM\Column(name="activo", type="integer", nullable=true)
     */
    private $activo;

	/**
	 * @var decimal
	 *
	 * @ORM\Column(name="inversion", type="decimal", nullable=true)
	 */
	private $inversion;

	/**
	 * @var CondicionProducto
	 *
	 * @ORM\ManyToOne(targetEntity="CondicionProducto")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="condicion_productoid", referencedColumnName="id")
	 * })
	 */
	private $condicion;

	/**
	 * @var Cesta
	 *
	 * @ORM\OneToMany(targetEntity="Cesta", mappedBy="producto", cascade={"remove"})
	 *
	 */
	private $cestas;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="cantidad", type="integer", nullable=true)
	 */
	private $cantidad;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="garantia", type="text", nullable=true)
	 */
	private $garantia;

	/**
	 * @var Categoria
	 *
	 * @ORM\ManyToOne(targetEntity="Categoria", inversedBy="productos")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="categoriaid", referencedColumnName="id")
	 * })
	 */
	private $categoriaid;

	/**
	 * @var Coleccion
	 *
	 * @ORM\ManyToOne(targetEntity="Coleccion", inversedBy="productos")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="coleccionid", referencedColumnName="id")
	 * })
	 */
	private $coleccionid;

	/**
	 * @var Usuario
	 *
	 * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="productos")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="usuarioid", referencedColumnName="id")
	 * })
	 */
	private $usuarioid;

	/**
	 * @var Usuario
	 *
	 * @ORM\ManyToMany(targetEntity="Usuario", mappedBy="productosFavoritos")
	 *
	 */
	private $usuarios;

	/**
	 * @var EstadoProducto
	 *
	 * @ORM\ManyToOne(targetEntity="EstadoProducto")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="estado_productoid", referencedColumnName="id")
	 * })
	 */
	private $estadoProductoid;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 *
	 * @ORM\ManyToMany(targetEntity="Oferta", mappedBy="productoid", cascade={"remove"})
	 */
	private $ofertaid;

	/**
	 * @ORM\OneToMany(targetEntity="ProductoCaracteristicaCategoria", mappedBy="productoid", cascade={"persist"}, cascade={"remove"})
	 */
	private $productoCaracteristicaCategoria;

	/**
	 * @ORM\OneToMany(targetEntity="AdministracionBundle\Entity\Valoracion", mappedBy="productoid", cascade={"persist"}, cascade={"remove"})
	 */
	private $valoraciones;

	/**
	 * @ORM\OneToMany(targetEntity="Pregunta", mappedBy="productoid", cascade={"persist"}, cascade={"remove"})
	 */
	private $preguntas;

	/**
	 * Many Productos have Many TipoEnvio.
	 * @ORM\ManyToMany(targetEntity="AdministracionBundle\Entity\TipoEnvio", inversedBy="productos")
	 * @ORM\JoinTable(name="producto_tipoenvio",
	 *      joinColumns={@ORM\JoinColumn(name="productoid", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="tipoenvioid", referencedColumnName="id")}
	 *      )
	 */
	private $tipoenvioid;

	/**
	 * @ORM\OneToMany(targetEntity="Imagen", mappedBy="productoid", cascade={"remove"})
	 */
	private $imagenes;

	/**
	 * @var Categoria
	 *
	 * @ORM\ManyToOne(targetEntity="Campanna")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="campannaid", referencedColumnName="id")
	 * })
	 */
	private $campannaid;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="ranking", type="integer", nullable=true)
	 */
	private $ranking;

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
        * @var \DateTime
        *
        * @ORM\Column(name="fecha_expiracion", type="date", nullable=true)
        */
	private $fechaExpiracion;
        
        /**
        * @var boolean
        *
        * @ORM\Column(name="borrado", type="boolean", nullable=false)
        */
        private $borrado;

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
	public function getDescripcion()
	{
		return $this->descripcion;
	}

	/**
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion)
	{
		$this->descripcion = $descripcion;
	}

	/**
	 * @return decimal
	 */
	public function getPrecio()
	{
		return $this->precio ;
	}

	/**
	 * @param decimal $precio
	 */
	public function setPrecio($precio)
	{
		$this->precio = $precio;
	}

	/**
	 * @return int
	 */
	public function getCuotaspagar()
	{
		return $this->cuotaspagar;
	}

	/**
	 * @param int $cuotaspagar
	 */
	public function setCuotaspagar($cuotaspagar)
	{
		$this->cuotaspagar = $cuotaspagar;
	}

	/**
	 * @return int
	 */
	public function getCantidad()
	{
		return $this->cantidad;
	}

	/**
	 * @param int $cantidad
	 */
	public function setCantidad($cantidad)
	{
		$this->cantidad = $cantidad;
	}

	/**
	 * @return Categoria
	 */
	public function getCategoriaid()
	{
		return $this->categoriaid;
	}

	/**
	 * @param Categoria $categoriaid
	 */
	public function setCategoriaid($categoriaid)
	{
		$this->categoriaid = $categoriaid;
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
	 * @return EstadoProducto
	 */
	public function getEstadoProductoid()
	{
		return $this->estadoProductoid;
	}

	/**
	 * @param EstadoProducto $estadoProductoid
	 */
	public function setEstadoProductoid($estadoProductoid)
	{
		$this->estadoProductoid = $estadoProductoid;
	}

	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getOfertaid()
	{
		return $this->ofertaid;
	}

	/**
	 * @param \Doctrine\Common\Collections\Collection $ofertaid
	 */
	public function setOfertaid($ofertaid)
	{
		$this->ofertaid = $ofertaid;
	}

	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getCaracteristicaCategoriaid()
	{
		return $this->caracteristicaCategoriaid;
	}

	/**
	 * @param \Doctrine\Common\Collections\Collection $caracteristicaCategoriaid
	 */
	public function setCaracteristicaCategoriaid($caracteristicaCategoriaid)
	{
		$this->caracteristicaCategoriaid = $caracteristicaCategoriaid;
	}

	/**
	 * @return mixed
	 */
	public function getImagenes()
	{
		return $this->imagenes;
	}

	/**
	 * @param mixed $imagenes
	 */
	public function setImagenes($imagenes)
	{
		$this->imagenes = $imagenes;
	}

    /**
     * @return int
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * @param int $activo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    }

    /**
     * @return decimal
     */
    public function getInversion()
    {
        return $this->inversion;
    }

    /**
     * @param decimal $inversion
     */
    public function setInversion($inversion)
    {
        $this->inversion = $inversion;
    }



    /**
     * @return CondicionProducto
     */
    public function getCondicion()
    {
        return $this->condicion;
    }

    /**
     * @param CondicionProducto $condicion
     */
    public function setCondicion($condicion)
    {
        $this->condicion = $condicion;
    }



    /**
     * @return int
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @param int $step
     */
    public function setStep($step)
    {
        $this->step = $step;
    }



    /**
     * @return string
     */
    public function getGarantia()
    {
        return $this->garantia;
    }

    /**
     * @param string $garantia
     */
    public function setGarantia($garantia)
    {
        $this->garantia = $garantia;
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


    /**
     * Remove usuarios
     *
     * @param \AdministracionBundle\Entity\Usuario $usuarios
     */
    public function removeUsuarioid(\AdministracionBundle\Entity\Usuario $userFavoritos)
    {
        $this->usuarios->removeElement($userFavoritos);

        //$userFavoritos->removeProductoFavorito($this);
    }

    /**
     * @return Usuario
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    /**
     * @param Usuario $usuarios
     */
    public function setUsuarios($usuarios)
    {
        $this->usuarios = $usuarios;
    }


    /**
     * @return mixed
     */
    public function getPreguntas()
    {
        return $this->preguntas;
    }

    /**
     * @param mixed $preguntas
     */
    public function setPreguntas($preguntas)
    {
        $this->preguntas = $preguntas;
    }

    /**
     * @ORM\OneToMany(targetEntity="Visita", mappedBy="productoid", cascade={"persist"}, cascade={"remove"})
     */
    private $visitas;

    /**
     * @return mixed
     */
    public function getVisitas()
    {
        return $this->visitas;
    }

    /**
     * @param mixed $visitas
     */
    public function setVisitas($visitas)
    {
        $this->visitas = $visitas;
    }



    /**
     * @return Coleccion
     */
    public function getColeccionid()
    {
        return $this->coleccionid;
    }

    /**
     * @param Coleccion $coleccionid
     */
    public function setColeccionid($coleccionid)
    {
        $this->coleccionid = $coleccionid;
    }

    /**
     * @return mixed
     */
    public function getProductoCaracteristicaCategoria()
    {
        return $this->productoCaracteristicaCategoria;
    }

    /**
     * @param mixed $productoCaracteristicaCategoria
     */
    public function setProductoCaracteristicaCategoria($productoCaracteristicaCategoria)
    {
        $this->productoCaracteristicaCategoria = $productoCaracteristicaCategoria;
    }

    /**
     * @return int
     */
    public function getRanking()
    {
        return $this->ranking;
    }

    /**
     * @param int $ranking
     */
    public function setRanking($ranking)
    {
        $this->ranking = $ranking;
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

    /**
     * @return ArrayCollection
     */
    public function getCestas()
    {
        return $this->cestas;
    }

    /**
     * @param Cesta $cestas
     */
    public function setCestas($cestas)
    {
        $this->cestas = $cestas;
    }

    /**
     * @return mixed
     */
    public function getValoraciones()
    {
        return $this->valoraciones;
    }

    /**
     * @param mixed $valoraciones
     */
    public function setValoraciones($valoraciones)
    {
        $this->valoraciones = $valoraciones;
    }

    /**
     * @var string
     * @ORM\Column(name="slug", length=255, type="string", nullable=true)
     */
    private $slug;


    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ofertaid = new \Doctrine\Common\Collections\ArrayCollection();
        $this->caracteristicaCategoriaid = new \Doctrine\Common\Collections\ArrayCollection();
        //$this->tipoenvioid = new \Doctrine\Common\Collections\ArrayCollection();
        $this->usuarios=new ArrayCollection();
        $this->preguntas=new ArrayCollection();
        $this->cestas = new ArrayCollection();
        $this->borrado = false;
    }

    public function hasEnvioDomicilio(){
        foreach($this->tipoenvioid as $tipoEnvio){
            if($tipoEnvio->getSlug()=='envio-domicilio-vendedor' || $tipoEnvio->getSlug()=='envio-domicilio-mercadofree'){
                return true;
            }
        }

        return false;
    }

    public function getValoracionTotal(){
        $sum=0;
        foreach ($this->valoraciones as $valoracion){
            $sum += $valoracion->getPuntuacion();
        }

        if(count($this->valoraciones) > 0){
            return $sum / count($this->valoraciones);
        }
        else{
            return 0;
        }
    }



    public function getImagenDestacada()
    {
        if($this->imagenes!=null)
        {
            foreach($this->imagenes as $img)
            {
                if($img->getDestacada())
                {
                    return $img->getUrl();
                }
            }
        }


        return null;
    }

    public function getOfertaActiva(){
        $today=new \DateTime();

        foreach ($this->ofertaid as $oferta){
            if($oferta->getEstadoProductoid()->getSlug()=='publicado' && $oferta->getFechaInicio()<$today && $oferta->getFechaFin()>$today){
                return $oferta;
            }
        }
        return null;
    }

    public function getPrecioOferta(){
        if($this->getOfertaActiva()!=null){
            $ofertaActiva=$this->getOfertaActiva();
            $newPrice = ($this->precio-($this->precio*($ofertaActiva->getPorcientoDescuento()/100)));
            return $newPrice;
        }
        else{
            return $this->precio;
        }
    }

    public function getPorcientoOferta(){
        $today=new \DateTime();
        if($this->getOfertaActiva()!=null){
            foreach ($this->ofertaid as $oferta){
                if($oferta->getEstadoProductoid()->getSlug()=='publicado' && $oferta->getFechaInicio()<$today && $oferta->getFechaFin()>$today){
                    return $oferta->getPorcientoDescuento();
                }
            }
        }
        else{
            return 0;
        }
    }



    /**
     * Add cestas
     *
     * @param \AdministracionBundle\Entity\Cesta $cestas
     * @return Producto
     */
    public function addCesta(\AdministracionBundle\Entity\Cesta $cestas)
    {
        $this->cestas[] = $cestas;

        return $this;
    }

    /**
     * Remove cestas
     *
     * @param \AdministracionBundle\Entity\Cesta $cestas
     */
    public function removeCesta(\AdministracionBundle\Entity\Cesta $cestas)
    {
        $this->cestas->removeElement($cestas);
    }

    /**
     * Add usuarios
     *
     * @param \AdministracionBundle\Entity\Usuario $usuarios
     * @return Producto
     */
    public function addUsuario(\AdministracionBundle\Entity\Usuario $usuarios)
    {
        $this->usuarios[] = $usuarios;

        return $this;
    }

    /**
     * Remove usuarios
     *
     * @param \AdministracionBundle\Entity\Usuario $usuarios
     */
    public function removeUsuario(\AdministracionBundle\Entity\Usuario $usuarios)
    {
        $this->usuarios->removeElement($usuarios);
    }

    /**
     * Add ofertaid
     *
     * @param \AdministracionBundle\Entity\Oferta $ofertaid
     * @return Producto
     */
    public function addOfertaid(\AdministracionBundle\Entity\Oferta $ofertaid)
    {
        $this->ofertaid[] = $ofertaid;

        return $this;
    }

    /**
     * Remove ofertaid
     *
     * @param \AdministracionBundle\Entity\Oferta $ofertaid
     */
    public function removeOfertaid(\AdministracionBundle\Entity\Oferta $ofertaid)
    {
        $this->ofertaid->removeElement($ofertaid);
    }

    /**
     * Add productoCaracteristicaCategoria
     *
     * @param \AdministracionBundle\Entity\ProductoCaracteristicaCategoria $productoCaracteristicaCategoria
     * @return Producto
     */
    public function addProductoCaracteristicaCategorium(\AdministracionBundle\Entity\ProductoCaracteristicaCategoria $productoCaracteristicaCategoria)
    {
        $this->productoCaracteristicaCategoria[] = $productoCaracteristicaCategoria;

        return $this;
    }

    /**
     * Remove productoCaracteristicaCategoria
     *
     * @param \AdministracionBundle\Entity\ProductoCaracteristicaCategoria $productoCaracteristicaCategoria
     */
    public function removeProductoCaracteristicaCategorium(\AdministracionBundle\Entity\ProductoCaracteristicaCategoria $productoCaracteristicaCategoria)
    {
        $this->productoCaracteristicaCategoria->removeElement($productoCaracteristicaCategoria);
    }

    /**
     * Add valoraciones
     *
     * @param \AdministracionBundle\Entity\Valoracion $valoraciones
     * @return Producto
     */
    public function addValoracione(\AdministracionBundle\Entity\Valoracion $valoraciones)
    {
        $this->valoraciones[] = $valoraciones;

        return $this;
    }

    /**
     * Remove valoraciones
     *
     * @param \AdministracionBundle\Entity\Valoracion $valoraciones
     */
    public function removeValoracione(\AdministracionBundle\Entity\Valoracion $valoraciones)
    {
        $this->valoraciones->removeElement($valoraciones);
    }

    /**
     * Add preguntas
     *
     * @param \AdministracionBundle\Entity\Pregunta $preguntas
     * @return Producto
     */
    public function addPregunta(\AdministracionBundle\Entity\Pregunta $preguntas)
    {
        $this->preguntas[] = $preguntas;

        return $this;
    }

    /**
     * Remove preguntas
     *
     * @param \AdministracionBundle\Entity\Pregunta $preguntas
     */
    public function removePregunta(\AdministracionBundle\Entity\Pregunta $preguntas)
    {
        $this->preguntas->removeElement($preguntas);
    }


    /**
     * Add visitas
     *
     * @param \AdministracionBundle\Entity\Visita $visitas
     * @return Producto
     */
    public function addVisita(\AdministracionBundle\Entity\Visita $visitas)
    {
        $this->visitas[] = $visitas;

        return $this;
    }

    /**
     * Remove visitas
     *
     * @param \AdministracionBundle\Entity\Visita $visitas
     */
    public function removeVisita(\AdministracionBundle\Entity\Visita $visitas)
    {
        $this->visitas->removeElement($visitas);
    }

    /**
     * Add imagenes
     *
     * @param \AdministracionBundle\Entity\Imagen $imagenes
     * @return Producto
     */
    public function addImagene(\AdministracionBundle\Entity\Imagen $imagenes)
    {
        $this->imagenes[] = $imagenes;

        return $this;
    }

    /**
     * Remove imagenes
     *
     * @param \AdministracionBundle\Entity\Imagen $imagenes
     */
    public function removeImagene(\AdministracionBundle\Entity\Imagen $imagenes)
    {
        $this->imagenes->removeElement($imagenes);
    }

    /**
     * Add tipoenvioid
     *
     * @param \AdministracionBundle\Entity\TipoEnvio $tipoenvioid
     * @return Producto
     */
    public function addTipoenvioid(\AdministracionBundle\Entity\TipoEnvio $tipoenvioid)
    {

        $this->tipoenvioid[] = $tipoenvioid;

        return $this;
    }

    /**
     * Remove tipoenvioid
     *
     * @param \AdministracionBundle\Entity\TipoEnvio $tipoenvioid
     */
    public function removeTipoenvioid(\AdministracionBundle\Entity\TipoEnvio $tipoenvioid)
    {
        $this->tipoenvioid->removeElement($tipoenvioid);
    }

    /**
     * Get tipoenvioid
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTipoenvioid()
    {
        return $this->tipoenvioid;
    }
    
    /**
     * 
     * @return \DateTime
     */
    function getFechaExpiracion() {
        return $this->fechaExpiracion;
    }

    /**
     * 
     * @param \DateTime $fechaExpiracion
     */
    function setFechaExpiracion(\DateTime $fechaExpiracion) {
        $this->fechaExpiracion = $fechaExpiracion;
    }
    
    /**
     * Función para saber si la publicación del producto se encuentra finalizada
     * 
     * @return bool
     */
    function estadoFinalizado() {
        return $this->getEstadoProductoid()->estadoFinalizado();
    }
    
    function setBorrado($borrado) {
        $this->borrado = $borrado;
    }
    
    /**
     * @return boolean
     */
    function getBorrado() {
        return $this->borrado;
    }

    function publicado() {
        return $this->getEstadoProductoid()->estadoPublicado();
    }
}
