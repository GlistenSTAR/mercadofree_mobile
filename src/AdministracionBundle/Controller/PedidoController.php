<?php

namespace AdministracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AdministracionBundle\Entity\Cliente;
use AdministracionBundle\Entity\Pedido;
use AdministracionBundle\Entity\HistoricoEstadoPedido;
use AdministracionBundle\Repository\PedidoRepository;

/**
 * Description of PedidoController
 *
 * @author Vadino
 */
class PedidoController  extends Controller {
    
    public function listarAction(Request $request)
    {
        if($request->getMethod()=='POST')
        {
            $em=$this->getDoctrine()->getManager();
            
            

            /** @var PedidoRepository $pedidoRepository */
            $pedidoRepository = $em->getRepository(Pedido::class);
            $pedidos = $pedidoRepository->findByPedido($request)->getResult();
            $pedidosTotales=$pedidoRepository->findByPedidoTotal($request)->getResult();

            $listaPedido=[];

            /** @var Pedido $pedido */
            foreach ($pedidos as $pedido)
            {
                $pedidoArray=[];
                $pedidoArray[]=$pedido->getId();
                $pedidoArray[]=$pedido->getCodigo();
                $pedidoArray[]=$pedido->getFecha()->format('j/n/Y H:i');
                $pedidoArray[]=$pedido->getUsuario()->getCliente()->getApellidos().', '.$pedido->getUsuario()->getCliente()->getNombre();
                $pedidoArray[]= number_format($pedido->getTotal(), 2);
                $pedidoArray[]=$pedido->getEstado()->getNombre();

                $listaPedido[]=$pedidoArray;
            }

            $json_data=array(
                "draw"=>intval($request->request->get('draw')),
                "recordsTotal"=>intval(count($pedidosTotales)),
                "recordsFiltered"=>intval(count($pedidosTotales)),
                "data"=>$listaPedido
            );

            return new Response(json_encode($json_data));

        }
        return $this->render('AdministracionBundle:Pedido:listado.html.twig');
    }
    
    public function detalleAction(Request $request) {
        $idPedido = $request->get('idPedido');
        
        $em=$this->getDoctrine()->getManager();
        
        /** @var Pedido $pedido */
        $pedido = $em->getRepository(Pedido::class)->find((int) $idPedido);
        
        $usuarioComprador = $pedido->getUsuario();
        /** @var Cliente $comprador */
        $comprador = $usuarioComprador->getCliente()? $usuarioComprador->getCliente(): null;
        
        $usuarioVendedor = $pedido->getVendedor();
        /** @var Cliente $vendedor */
        $vendedor = $usuarioVendedor->getCliente()? $usuarioVendedor->getCliente(): null;
        $metodoPago = $pedido->getMetodoPago();
        
        $htmlDireccionEnvio = $this->getHTMLDireccionEnvio($pedido);
        $tableBodyProducto = $this->getHtmlTableBodyProducto($pedido);
        $tableBodySeguimiento = $this->getHtmlTableBodySeguimiento($pedido);
        $tableValoracion = $this->getHtmlTableValoracion($pedido);
        
        return new Response(json_encode([
            'codigo' => $pedido->getCodigo(),
            'estado' => $pedido->getEstado()->getNombre(),
            'comprador' => $comprador? $comprador->getApellidos().', '.$comprador->getNombre().' ('. $usuarioComprador->getEmail().')': '',
            'fecha' => $pedido->getFecha()->format('j/n/Y'),
            'vendedor' => $vendedor? $vendedor->getApellidos().', '.$vendedor->getNombre().' ('. $usuarioVendedor->getEmail().')': '',
            'metodoPago' => $metodoPago? $metodoPago->getNombre(): '',
            'monto' => number_format($pedido->getTotal(), 2),
            'htmlDireccionEnvio' => $htmlDireccionEnvio,
            'tableBodyProducto' => $tableBodyProducto,
            'tableBodySeguimiento' => $tableBodySeguimiento,
            'tableValoracion' => $tableValoracion,
        ]));
    }
    
    private function getHTMLDireccionEnvio(Pedido $pedido) {
        $htmlDireccionEnvio = '';
        
        $tipoEnvio = $pedido->getTipoEnvio();
        
        if($pedido->getDireccionEnvio()) {
        $htmlDireccionEnvio = '<div class="col-xs-12" >
                        <div class="heading mb30">
                            <h5>Dirección de Envío</h5>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="content-box content-box-bordered" style="padding: 20px 10px;">
                            <div class="row">
                                <div class="col-xs-12" style="text-align: left">
                                    <p>' . $pedido->getDireccionEnvio()->getFormatedDir('direccion-compra-envio') . '</p>
                                </div>
                            </div>
                        </div>
                    </div>';
        }
                
        return $htmlDireccionEnvio;
    }
    
    private function getHtmlTableBodyProducto(Pedido $pedido) {
        
        $assetsManager = $this->get('templating.helper.assets');
        
        $tableBodyProducto = '
                <tr>
                    <td width="55%">
                        <table>
                            <tr class="visible-xs">
                                <td width="100%" style="padding-right: 5px;padding-bottom: 10px;"><img class="img-responsive" src="' . $assetsManager->getUrl('uploads/images/productos/'.trim($pedido->getProducto()->getImagenDestacada())) . '"></td>
                            </tr>
                            <tr>
                                <td class="hidden-xs" width="17%" style="padding-right: 5px"><img class="img-responsive" src="' . $assetsManager->getUrl('uploads/images/productos/'.trim($pedido->getProducto()->getImagenDestacada())) . '"></td>
                                <td>
                                    <a href="#">' . $pedido->getDetalle()->getNombre() . '</a>
                                    <p>$ ' . number_format($pedido->getDetalle()->getPrecio(), 2,',','.') . '</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>$ ' . number_format($pedido->getDetalle()->getPrecio(), 2,',','.') . '</td>
                    <td>' . $pedido->getCant() . '</td>
                    <td>
                        $ ' . number_format($pedido->getDetalle()->getPrecio() * $pedido->getCant(), 2,',','.') . '
                    </td>
                </tr>';
        
        
        $tableBodyProducto .= '
            <tr>
                <td colspan="3"><b>Subtotal</b></td>
                <td>$ ' . number_format($pedido->getDetalle()->getPrecio(), 2, ',', '.') . '</td>
            </tr>';
            
        if($pedido->getCostoEnvio()) {
            $tableBodyProducto .= '
                <tr>
                    <td colspan="3"><b>' . $pedido->getTipoEnvio()->getNombre() . '</b></td>
                    <td>$ ' . number_format($pedido->getCostoEnvio(), 2, ',', '.') . '</td>
                </tr>';
        }
        
        $tableBodyProducto .= '
            <tr>
                <td colspan="3"><b>Total</b></td>
                <td><b>$ ' . number_format($pedido->getTotal(), 2, ',', '.') . '</b></td>
            </tr>';
        
        return $tableBodyProducto;
    }
    
    private function getHtmlTableBodySeguimiento(Pedido $pedido) {

        $tableBodySeguimiento = '';
        
        /** @var HistoricoEstadoPedido $historicoEstadoPedido */
        foreach($pedido->getHistoricosEstadoPedido() as $historicoEstadoPedido) {
            
            $stringModificadoPorAdmin = $historicoEstadoPedido->getModificadoPorAdmin()? 'Si': 'No';
            
            $tableBodySeguimiento .= '
                <tr>
                    <td>' . $historicoEstadoPedido->getFecha()->format('d/m/Y H:i') . '</td>
                    <td>' . $historicoEstadoPedido->getEstadoPedido()->getNombre() . '</td>
                    <td>' . $stringModificadoPorAdmin . '</td>
                </tr>';
        }
        
        return $tableBodySeguimiento;
    }
    
    private function getHtmlTableValoracion(Pedido $pedido) {
        
        $valoracionPedido = $pedido->getValoracionPedido();
        
        if($valoracionPedido) {
            
            $tableValoracion = '<p><h6>';
            $tableValoracion.= $valoracionPedido->getCompraAceptada()? 'La compra fue aceptada': 'La compra fue rechazada';
            $tableValoracion.= '</h6></p>';
            
            $tableValoracion.= '<p></p><table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tiempo de Entrega</th>
                                            <th>Calidad del producto</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
            
            $tableValoracion.= '<tr><td>'.$valoracionPedido->getDetalleTiempoEntrega().'</td>';
            $tableValoracion.= '<td>'.$valoracionPedido->getDetalleCalidadProducto().'</td</tr>';
            
            $tableValoracion .= '</tbody></table>';
            
            if(!$valoracionPedido->getCompraAceptada()) {
                $tableValoracion .= '<br/><b>Motivo del rechazo</b>: ' . $valoracionPedido->getMotivoRechazo();
            }
        } else {
            $tableValoracion = '<p><h6>El pedido no tiene valoración</h6></p>';
        }
        
        return $tableValoracion;
    }
    
    public function liberarFondosAction(Request $request) {
        $pedidosSeleccionados = $request->request->get('pedidosSeleccionados');
        
        $em=$this->getDoctrine()->getManager();
        
        $pedidosFondosLiberados = [];
        
        foreach ($pedidosSeleccionados as $idPedidoSeleccionado) {
            
            /** @var Pedido $pedido */
            $pedido = $em->getRepository(Pedido::class)->find($idPedidoSeleccionado);
            
            if($pedido->puedeLiberarFondos()) {
                /** procedimiento para liberar los fondos del pedido y cerrar el pedido */
                $this->container->get('liberar_pago_pedido_service')->execute($pedido, true);
                
                $pedidosFondosLiberados[] = $pedido->getId();
            }
        }
        
        return new Response(json_encode([
            'pedidosFondosLiberados' => $pedidosFondosLiberados
        ]));
    }
    
}
