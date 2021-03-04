<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Valoracion
 *
 * @ORM\Table(name="valoracion_pedido")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\ValoracionPedidoRepository")
 */
class ValoracionPedido
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
     * @var boolean
     *
     * @ORM\Column(name="compra_aceptada", type="boolean", nullable=true)
     */
    private $compraAceptada;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo_rechazo", type="string", length=255, nullable=true)
     */
    private $motivoRechazo;

    /**
     * @var OpcionValoracionCalidadProductoPedido
     *
     * @ORM\OneToOne(targetEntity="OpcionValoracionCalidadProductoPedido")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="opcion_valoracion_calidad_producto_pedido_id", referencedColumnName="id")
     * })
     */
    private $opcionValoracionCalidadProductoPedido;
    
    /**
     * @var OpcionValoracionTiempoEntregaPedido
     *
     * @ORM\OneToOne(targetEntity="OpcionValoracionTiempoEntregaPedido")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="opcion_valoracion_tiempo_entrega_pedido_id", referencedColumnName="id")
     * })
     */
    private $opcionValoracionTiempoEntregaPedido;
    
    /**
     * @var Pedido
     * 
     * @ORM\OneToOne(targetEntity="Pedido", mappedBy="valoracionPedido")
     * 
     */
    protected $pedido;

    public static function generar(
            OpcionValoracionCalidadProductoPedido $opcionValoracionCalidadProductoPedido, 
            OpcionValoracionTiempoEntregaPedido $opcionValoracionTiempoEntregaPedido, 
            $compraAceptada,
            $motivoRechazo) {
        $valoracionPedido = new self();
        $valoracionPedido->opcionValoracionCalidadProductoPedido = $opcionValoracionCalidadProductoPedido;
        $valoracionPedido->opcionValoracionTiempoEntregaPedido = $opcionValoracionTiempoEntregaPedido;
        $valoracionPedido->compraAceptada = $compraAceptada;
        $valoracionPedido->motivoRechazo = $motivoRechazo;
        
        return $valoracionPedido;
    }
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    function getCompraAceptada() {
        return $this->compraAceptada;
    }

    function getMotivoRechazo() {
        return $this->motivoRechazo;
    }

    function getOpcionValoracionCalidadProductoPedido() {
        return $this->opcionValoracionCalidadProductoPedido;
    }

    function getOpcionValoracionTiempoEntregaPedido() {
        return $this->opcionValoracionTiempoEntregaPedido;
    }

    function setCompraAceptada($compraAceptada) {
        $this->compraAceptada = $compraAceptada;
    }

    function setMotivoRechazo($motivoRechazo) {
        $this->motivoRechazo = $motivoRechazo;
    }

    function setOpcionValoracionCalidadProductoPedido(OpcionValoracionCalidadProductoPedido $opcionValoracionCalidadProductoPedido) {
        $this->opcionValoracionCalidadProductoPedido = $opcionValoracionCalidadProductoPedido;
    }

    function setOpcionValoracionTiempoEntregaPedido(OpcionValoracionTiempoEntregaPedido $opcionValoracionTiempoEntregaPedido) {
        $this->opcionValoracionTiempoEntregaPedido = $opcionValoracionTiempoEntregaPedido;
    }

    function getPedido() {
        return $this->pedido;
    }

    function setPedido(Pedido $pedido) {
        $this->pedido = $pedido;
    }
    
    function getDetalleCalidadProducto() {
        return $this->getOpcionValoracionCalidadProductoPedido()->getDetalle();
    }
    
    function getDetalleTiempoEntrega() {
        return $this->getOpcionValoracionTiempoEntregaPedido()->getDetalle();
    }

}
