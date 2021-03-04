<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * DetallePedido
 *
 * @ORM\Table(name="detalle_pedido")
 * @ORM\Entity()
 */
class DetallePedido
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
     * @var Pedido
     *
     * @ORM\OneToOne(targetEntity="Pedido", inversedBy="detalle")
     * @ORM\JoinColumn(name="pedido_id", referencedColumnName="id")
     */
    private $pedido;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;
    
    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var float
     *
     * @ORM\Column(name="precio", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $precio;

    /**
     * @var integer
     *
     * @ORM\Column(name="cuotas_pagar", type="integer")
     */
    private $cuotasPagar;

    /**
     * @var decimal
     *
     * @ORM\Column(name="peso", type="decimal", precision=10, scale=2, nullable=true)
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
     * @var Categoria
     *
     * @ORM\ManyToOne(targetEntity="Categoria")
     * @ORM\JoinColumn(name="categoria_id", referencedColumnName="id")
     */
    private $categoria;
    
    /**
    * @var string
    *
    * @ORM\Column(name="garantia", type="text", nullable=true)
    */
    private $garantia;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
        $producto = $pedido->getProducto();
        if($producto) {
            $this->nombre = $producto->getNombre();
            $this->descripcion = $producto->getDescripcion();
            $this->precio = $producto->getPrecio();
            $this->cuotasPagar = $producto->getCuotaspagar();
            $this->peso = $producto->getPeso();
            $this->ancho = $producto->getAncho();
            $this->alto = $producto->getAlto();
            $this->profundidad = $producto->getProfundidad();
            $this->categoria = $producto->getCategoriaid();
            $this->garantia = $producto->getGarantia();
        }
    }
    
    public static function detalleDesdePedido(Pedido $pedido) {
        $detallePedido = new DetallePedido($pedido);
        return $detallePedido;
    }
    
    function getId() {
        return $this->id;
    }

    function getPedido() {
        return $this->pedido;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getPrecio() {
        return (float) $this->precio;
    }

    function getCuotasPagar() {
        return $this->cuotasPagar;
    }

    function getPeso() {
        return $this->peso;
    }

    function getAncho() {
        return $this->ancho;
    }

    function getAlto() {
        return $this->alto;
    }

    function getProfundidad() {
        return $this->profundidad;
    }

    function getCategoria() {
        return $this->categoria;
    }

    function getGarantia() {
        return $this->garantia;
    }

    function setPedido(Pedido $pedido) {
        $this->pedido = $pedido;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setPrecio($precio) {
        $this->precio = $precio;
    }

    function setCuotasPagar($cuotasPagar) {
        $this->cuotasPagar = $cuotasPagar;
    }

    function setPeso($peso) {
        $this->peso = $peso;
    }

    function setAncho($ancho) {
        $this->ancho = $ancho;
    }

    function setAlto($alto) {
        $this->alto = $alto;
    }

    function setProfundidad($profundidad) {
        $this->profundidad = $profundidad;
    }

    function setCategoria(Categoria $categoria) {
        $this->categoria = $categoria;
    }

    function setGarantia($garantia) {
        $this->garantia = $garantia;
    }

}
