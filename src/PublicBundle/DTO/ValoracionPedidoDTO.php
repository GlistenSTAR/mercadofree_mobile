<?php

namespace PublicBundle\DTO;

use AdministracionBundle\Entity\ValoracionPedido;

/**
 * Description of ValoracionProductoDTO
 *
 * @author Vadino
 */
class ValoracionPedidoDTO {
    
    public $detalle_calidad_producto;
    
    public $detalle_tiempo_entrega;
    
    public $compra_aceptada;
    
    public $motivo_rechazo;
    
    public function __construct(ValoracionPedido $valoracionPedido) {
        
        if($valoracionPedido) {
            $this->detalle_calidad_producto = $valoracionPedido->getDetalleCalidadProducto();
            $this->detalle_tiempo_entrega = $valoracionPedido->getDetalleTiempoEntrega();
            $this->compra_aceptada = $valoracionPedido->getCompraAceptada();
            $this->motivo_rechazo = $valoracionPedido->getMotivoRechazo();
        }
    }
}
