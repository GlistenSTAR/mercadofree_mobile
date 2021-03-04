<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pedido
 *
 * @ORM\Table(name="pedido")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\PedidoRepository")
 */
class Pedido
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
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="pedidos")
     */
    private $usuario;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var EstadoPedido
     *
     * @ORM\ManyToOne(targetEntity="EstadoPedido")
     */
    private $estado;

    /**
     * @var TipoEnvio
     *
     * @ORM\ManyToOne(targetEntity="TipoEnvio")
     */
    private $tipoEnvio;

    /**
     * @var decimal
     *
     * @ORM\Column(name="costoEnvio", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $costoEnvio;

    /**
     * @var decimal
     *
     * @ORM\Column(name="subtotal", type="decimal", precision=10, scale=2)
     */
    private $subtotal;

    /**
     * @var DireccionEnvio
     *
     * @ORM\OneToOne(targetEntity="DireccionEnvio")
     */
    private $direccionEnvio;

    /**
     * @var MetodoPago
     *
     * @ORM\ManyToOne(targetEntity="MetodoPago")
     */
    private $metodoPago;

    /**
     * @var Factura
     *
     * @ORM\OneToOne(targetEntity="Factura", cascade={"persist","remove"})
     */
    private $factura;

    /**
     * @var Pago
     *
     * @ORM\OneToOne(targetEntity="Pago")
     */
    private $pago;

	/**
	 * @var Cesta
	 *
	 * @ORM\OneToMany(targetEntity="Cesta", mappedBy="pedido", cascade={"remove"})
	 *
	 */
	private $cestas;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="codigo", type="string", length=9)
	 */
	private $codigo;

	/**
	 * @var Producto
	 *
	 * @ORM\ManyToOne(targetEntity="Producto")
	 */
	private $producto;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="cant", type="integer", nullable=true)
	 */
	private $cant;

    /**
     * @ORM\OneToMany(targetEntity="MovimientoCuenta", mappedBy="pedidoid")
     */
    protected $movimientos;
    
    /**
     * @var DetallePedido
     * 
     * @ORM\OneToOne(targetEntity="DetallePedido", mappedBy="pedido", cascade={"persist"})
     */
    protected $detalle;
    
    /**
     * @var ValoracionPedido
     *
     * @ORM\OneToOne(targetEntity="ValoracionPedido", inversedBy="pedido", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="valoracion_pedido_id", referencedColumnName="id")
     * })
     */
    protected $valoracionPedido;
    
    /**
     * @ORM\OneToMany(targetEntity="HistoricoEstadoPedido", mappedBy="pedido", cascade={"persist"})
     */
    protected $historicosEstadoPedido;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="borrado_vendedor", type="boolean", nullable=false)
     */
    protected $borradoVendedor;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="borrado_comprador", type="boolean", nullable=false)
     */
    protected $borradoComprador;

    public function __construct()
    {
        $this->movimientos = new ArrayCollection();
        $this->historicosEstadoPedido = new ArrayCollection();
        $this->borradoVendedor = false;
        $this->borradoComprador = false;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set usuario
     *
     * @param Usuario $usuario
     * @return Pedido
     */
    public function setUsuario(Usuario $usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Pedido
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
     * Set estado
     *
     * @param EstadoPedido $estado
     * @param boolean $modificadoPorAdmin
     * @return Pedido
     */
    public function setEstado(EstadoPedido $estado, $modificadoPorAdmin = null)
    {
        $this->estado = $estado;
        $this->getHistoricosEstadoPedido()->add(new HistoricoEstadoPedido($this, $this->estado, $modificadoPorAdmin? true: false));

        return $this;
    }

    /**
     * Get estado
     *
     * @return EstadoPedido
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set tipoEnvio
     *
     * @param TipoEnvio $tipoEnvio
     * @return Pedido
     */
    public function setTipoEnvio(TipoEnvio $tipoEnvio)
    {
        $this->tipoEnvio = $tipoEnvio;

        return $this;
    }

    /**
     * Get tipoEnvio
     *
     * @return TipoEnvio
     */
    public function getTipoEnvio()
    {
        return $this->tipoEnvio;
    }

    /**
     * Set costoEnvio
     *
     * @param decimal $costoEnvio
     * @return Pedido
     */
    public function setCostoEnvio($costoEnvio)
    {
        $this->costoEnvio = $costoEnvio;

        return $this;
    }

    /**
     * Get costoEnvio
     *
     * @return decimal
     */
    public function getCostoEnvio()
    {
        return empty($this->costoEnvio) ? 0 : $this->costoEnvio;
    }

    /**
     * Set subtotal
     *
     * @param decimal $subtotal
     * @return Pedido
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return decimal
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set direccionEnvio
     *
     * @param DireccionEnvio $direccionEnvio
     * @return Pedido
     */
    public function setDireccionEnvio(DireccionEnvio $direccionEnvio)
    {
        $this->direccionEnvio = $direccionEnvio;

        return $this;
    }

    /**
     * Get direccionEnvio
     *
     * @return DireccionEnvio
     */
    public function getDireccionEnvio()
    {
        return $this->direccionEnvio;
    }

    /**
     * Set metodoPago
     *
     * @param MetodoPago $metodoPago
     * @return Pedido
     */
    public function setMetodoPago(MetodoPago $metodoPago)
    {
        $this->metodoPago = $metodoPago;

        return $this;
    }

    /**
     * Get metodoPago
     *
     * @return MetodoPago
     */
    public function getMetodoPago()
    {
        return $this->metodoPago;
    }

    /**
     * Set factura
     *
     * @param Factura $factura
     * @return Pedido
     */
    public function setFactura(Factura $factura)
    {
        $this->factura = $factura;

        return $this;
    }

    /**
     * Get factura
     *
     * @return Factura
     */
    public function getFactura()
    {
        return $this->factura;
    }

    /**
     * Set pago
     *
     * @param Pago $pago
     * @return Pedido
     */
    public function setPago(Pago $pago)
    {
        $this->pago = $pago;

        return $this;
    }

    /**
     * Get pago
     *
     * @return Pago
     */
    public function getPago()
    {
        return $this->pago;
    }

	/**
	 * @return Cesta
	 */
	public function getCestas() {
		return $this->cestas;
	}

	/**
	 * @param Cesta $cestas
	 */
	public function setCestas( $cestas ) {
		$this->cestas = $cestas;
	}

	/**
	 * @return string
	 */
	public function getCodigo() {
		return $this->codigo;
	}

	/**
	 * @param string $codigo
	 */
	public function setCodigo( $codigo ) {
		$this->codigo = $codigo;
	}

	/**
	 * @return Producto
	 */
	public function getProducto() {
		return $this->producto;
	}

	/**
	 * @param Producto $producto
	 */
	public function setProducto( $producto ) {
		$this->producto = $producto;
	}

	/**
	 * @return int
	 */
	public function getCant() {
		return $this->cant;
	}

	/**
	 * @param int $cant
	 */
	public function setCant( $cant ) {
		$this->cant = $cant;
	}

    public function getTotal(){
    	if($this->costoEnvio!=null && $this->costoEnvio>0){
    		return $this->subtotal+$this->costoEnvio;
	    }
	    else{
    		return $this->subtotal;
	    }
    }

    public function getNextState($fromSeller = false){
        return $this->getEstado()->getNextState($fromSeller);
    }

    /**
     * Add cestas
     *
     * @param \AdministracionBundle\Entity\Cesta $cestas
     * @return Pedido
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
     * Add movimientos
     *
     * @param \AdministracionBundle\Entity\MovimientoCuenta $movimientos
     * @return Pedido
     */
    public function addMovimiento(\AdministracionBundle\Entity\MovimientoCuenta $movimientos)
    {
        $this->movimientos[] = $movimientos;

        return $this;
    }

    /**
     * Remove movimientos
     *
     * @param \AdministracionBundle\Entity\MovimientoCuenta $movimientos
     */
    public function removeMovimiento(\AdministracionBundle\Entity\MovimientoCuenta $movimientos)
    {
        $this->movimientos->removeElement($movimientos);
    }

    /**
     * Get movimientos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMovimientos()
    {
        return $this->movimientos;
    }
    
    /**
     * @return DetallePedido
     */
    function getDetalle() {
        return $this->detalle;
    }

    function setDetalle(DetallePedido $detalle) {
        $this->detalle = $detalle;
    }
    
    function generarDetalleCompra() {
        $this->setDetalle(DetallePedido::detalleDesdePedido($this));
    }
    
    /**
     * Un pedido puede valorarse si se encuentra en estado Enviado
     * y si esta dentro de los días determinados por la configuración
     * 
     * @param \AdministracionBundle\Entity\Configuracion $configuracion
     * @return boolean
     */
    function puedeValorarse(Configuracion $configuracion = null) {
        $puedeValorarse = false;

        $fechaLimiteParaValorar = $this->getFechaEntrega();

        if(is_null($fechaLimiteParaValorar)){
            return false;
        }
        
        if($configuracion && !$this->valoracionPedido) {
            
            $limiteDiasValoracionPedido = $configuracion->getLimiteDiasValoracionPedido();
            
            if($limiteDiasValoracionPedido) {
                $fechaActual = new \DateTime();
                //$fechaLimiteParaValorar = $this->getFecha();
                $fechaLimiteParaValorar->add(new \DateInterval('P'.$limiteDiasValoracionPedido.'D'));
                
                if($fechaActual < $fechaLimiteParaValorar && $this->pedidoEntregado()) {
                    $puedeValorarse = true;
                }
            }
        }
        
        return $puedeValorarse;
    }
    
    /**
     * @return boolean
     */
    function pedidoEntregado() {
        return $this->getEstado()->estadoEntregado();
    }

    function valorarPedido(
            OpcionValoracionCalidadProductoPedido $opcionValoracionCalidadProductoPedido, 
            OpcionValoracionTiempoEntregaPedido $opcionValoracionTiempoEntregaPedido, 
            $compraAceptada,
            $motivoRechazo) {
        
        $this->valoracionPedido = ValoracionPedido::generar($opcionValoracionCalidadProductoPedido, $opcionValoracionTiempoEntregaPedido, $compraAceptada, $motivoRechazo);
        
    }

    public function getFechaEntrega()
    {
        $fechaEntrega = null;
        foreach ($this->getHistoricosEstadoPedido() as $he){
            if($he->getEstadoPedido()->estadoEntregado()){
                $fechaEntrega = $he->getFecha();
            }
        }

        return $fechaEntrega;
    }
    
    /**
     * @return Usuario
     */
    function getVendedor() {
        return $this->getProducto()->getUsuarioid();
    }
    
    function getValoracionPedido() {
        return $this->valoracionPedido;
    }
    
    function getHistoricosEstadoPedido() {
        return $this->historicosEstadoPedido;
    }
    
    /**
     * @return boolean
     */
    function puedeLiberarFondos() {
        
        return !$this->getEstado()->estadoCerrado();
    }
    
    function puedeImprimirPDF() {
        return $this->getEstado()->estadoPagado();
    }
    
    function devuelto() {
        return $this->getEstado()->estadoDevuelto();
    }
    
    function puedeCambiarEstadoPorInactividad(\DateTime $fechaLimite) {
        
        /** @var HistoricoEstadoPedido $ultimoEstadoHistorico */
        $ultimoEstadoHistorico = $this->getHistoricosEstadoPedido()->last();
        if($ultimoEstadoHistorico->getFecha() < $fechaLimite) {
            return true;
        }
            
        return false;
    }
    
    function getBorradoVendedor() {
        return $this->borradoVendedor;
    }

    function setBorradoVendedor($borrado) {
        $this->borradoVendedor = $borrado;
    }
    
    function getBorradoComprador() {
        return $this->borradoComprador;
    }
    
    function setBorradoComprador($borrado) {
        $this->borradoComprador = $borrado;
    }
    
    function cerrado() {
        return $this->getEstado()->estadoCerrado();
    }
    
    function pendiente() {
        return $this->getEstado()->estadoPendiente();
    }
    
    function seRecogeEnTienda() {
        return $this->getTipoEnvio()->esRecogidaEnTienda();
    }

    function pagado(){
        return $this->getEstado()->estadoPagado();
    }
}
