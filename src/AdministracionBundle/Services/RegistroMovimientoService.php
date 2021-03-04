<?php

namespace AdministracionBundle\Services;

use AdministracionBundle\Entity\ComisionVenta;
use AdministracionBundle\Entity\Payment;
use AdministracionBundle\Entity\MovimientoCuenta;
use AdministracionBundle\Entity\ConceptoMovimientoCuenta;
use AdministracionBundle\Entity\Pedido;
use AdministracionBundle\Entity\Configuracion;
use PublicBundle\Services\CalculadoraDePreciosService;

class RegistroMovimientoService
{
    const TIPO_RECARGA_FONDOS_PAYPAL = 'RECFONPP';
    const TIPO_RETIRO_FONDOS_PAYPAL = 'RETFONPP';
    const TIPO_PAGO_FONDOS_MERCADOFREE = 'PAGCOMSAL';
    const TIPO_DEBITO = 'DEBITO';
    const TIPO_CREDITO = 'CREDITO';
    
    private $em;
    private $calculadoraDePreciosService;

    public function __construct($entityManager, 
                                CalculadoraDePreciosService $calculadoraDePreciosService)
    {
        $this->em = $entityManager;
        $this->calculadoraDePreciosService = $calculadoraDePreciosService;
    }

    public function registrarPagoComPaypal($usuario, $params)
    {
        $tipo = $this
            ->em
            ->getRepository(ConceptoMovimientoCuenta::class)
            ->findOneBy(['slug' => ConceptoMovimientoCuenta::PAGO_PAYPAL_SLUG]);

        $this->validarMovimientoNuevo($usuario, $params);
        
        $payment = $this->getPayment($params['identificador_pago']);
        
        $monto = ((float) $params['monto']) / 100; //Paypal nos envia el valor del monto con dos flotantes incluidos
        
        $movimiento = new MovimientoCuenta();
        $movimiento
            ->setReferencia($this->generarReferenciaMovimiento('CRED'))
            ->setFecha(new \DateTime('now'))
            ->setMonto($monto) 
            ->setTipo(self::TIPO_CREDITO)
            ->setConceptoMovimientoCuentaid($tipo)
            ->setDescuentoMercadofree(0)
            ->setRefExterna($params['identificador_pago'])
            ->setUsuarioid($usuario)
            ->setPayment($payment);

        $saldo = $usuario->getSaldo();
        $usuario->setSaldo($saldo + $monto);

        $this->em->persist($movimiento);
        $this->em->persist($usuario);
        $this->em->flush();
        return $movimiento;
    }

    public function registrarPagoComSaldoMercadofree($usuario, Pedido $pedido)
    {
        $tipo = $this
            ->em
            ->getRepository(ConceptoMovimientoCuenta::class)
            ->findOneBy(['slug' => ConceptoMovimientoCuenta::PAGO_SALDO_MERCADOFREE_SLUG]);

        $saldo = $usuario->getSaldo();
        if ($saldo < $pedido->getTotal()) {
            return false;
        }

        $movimiento = new MovimientoCuenta();
        $movimiento
            ->setReferencia('DEB'.str_pad(mt_rand(0,9999), 4, '0', STR_PAD_LEFT))
            ->setFecha(new \DateTime('now'))
            ->setMonto($pedido->getTotal())
            ->setTipo(self::TIPO_DEBITO)
            ->setConceptoMovimientoCuentaid($tipo)
            ->setDescuentoMercadofree(0)
            ->setRefExterna('')
            ->setPedidoid($pedido)
            ->setUsuarioid($usuario);

        $usuario->setSaldo($saldo - $pedido->getTotal());

        $this->em->persist($movimiento);
        $this->em->persist($usuario);
        $this->em->flush();
        return true;
    }

    public function registrarRetiroPaypal($usuario, $params)
    {
        $tipo = $this
            ->em
            ->getRepository(ConceptoMovimientoCuenta::class)
            ->findOneBy(['slug' => ConceptoMovimientoCuenta::RETIRO_PAYPAL_SLUG]);

        $movimiento = new MovimientoCuenta();
        $movimiento
            ->setReferencia($this->generarReferenciaMovimiento())
            ->setFecha(new \DateTime('now'))
            ->setMonto($params['monto'])
            ->setTipo(self::TIPO_DEBITO)
            ->setConceptoMovimientoCuentaid($tipo)
            ->setDescuentoMercadofree(0)
            ->setRefExterna('')
            ->setUsuarioid($usuario);

        $saldo = $usuario->getSaldo();
        $usuario->setSaldo($saldo - $params['monto']);

        $this->em->persist($movimiento);
        $this->em->persist($usuario);
        $this->em->flush();
        return true;
    }
    
    /**
     * Función para registrar movimientos de fondos de rechazos de retiro de paypal
     * 
     * @param Usuario $usuario
     * @param array $params
     * @return boolean
     */
    public function registrarRechazoRetiroPaypal($usuario, $params)
    {
        $tipo = $this
            ->em
            ->getRepository(ConceptoMovimientoCuenta::class)
            ->findOneBy(['slug' => ConceptoMovimientoCuenta::RECHAZO_RETIRO_PAYPAL_SLUG]);

        $movimiento = new MovimientoCuenta();
        $movimiento
            ->setReferencia($this->generarReferenciaMovimiento('CRED'))
            ->setFecha(new \DateTime('now'))
            ->setMonto($params['monto'])
            ->setTipo(self::TIPO_CREDITO)
            ->setConceptoMovimientoCuentaid($tipo)
            ->setDescuentoMercadofree(0)
            ->setRefExterna('')
            ->setUsuarioid($usuario);

        $saldo = $usuario->getSaldo();
        $usuario->setSaldo($saldo + $params['monto']);

        $this->em->persist($movimiento);
        $this->em->persist($usuario);
        $this->em->flush();
        return true;
    }
    
    /**
     * Función para validar que el movimiento que se intenta realizar no se haya
     * registrado previamente.
     * Se utiliza para validar que una llamada desde la url de paypal no se 
     * realice mas de 1 vez para asignar o retirar fondos.
     * 
     * @param type $usuario
     * @param type $params
     */
    private function validarMovimientoNuevo($usuario, $params) {
        
        //Validación de pago ya registrado
        /** @var Payment $payment */
        $payment = $this->getPayment($params['identificador_pago']);
        
        if($payment) {
            $movimientoCuenta = $this
                                    ->em
                                    ->getRepository(MovimientoCuenta::class)
                                    ->findOneBy(['payment' => $payment]);
            
            if($movimientoCuenta) {
                throw new \Exception('pago ya registrado');
            }
        }
    }
    
    /**
     * @param string $paymentNumber
     * @return Payment
     */
    private function getPayment($paymentNumber) {
        return $this
                    ->em
                    ->getRepository(Payment::class)
                    ->findOneBy(['number' => $paymentNumber]);
    }
    
    public function registrarCobroPedidoMercadofree($usuario, Pedido $pedido)
    {
        $tipo = $this
            ->em
            ->getRepository(ConceptoMovimientoCuenta::class)
            ->findOneBy(['slug' => ConceptoMovimientoCuenta::CONFIRMACION_VENTA_PEDIDO_SLUG]);

        $saldo = $usuario->getSaldo();
        
        $saldoACobrarVendedor = $this->calculadoraDePreciosService->CalcularPrecio($pedido);

        $movimiento = new MovimientoCuenta();
        $movimiento
            ->setReferencia($this->generarReferenciaMovimiento('CRED'))
            ->setFecha(new \DateTime('now'))
            ->setMonto($saldoACobrarVendedor)
            ->setTipo(self::TIPO_CREDITO)
            ->setConceptoMovimientoCuentaid($tipo)
            ->setDescuentoMercadofree(0)
            ->setRefExterna('')
            ->setPedidoid($pedido)
            ->setUsuarioid($usuario);

        // Registrar porcentaje descontado, si aplica

        $detalle = $pedido->getDetalle();
        $comisionVentaRepositorio = $this->em->getRepository(ComisionVenta::class);

        $comision = $comisionVentaRepositorio->getComisionPorCategoria($detalle->getCategoria());

        if(is_null($comision)){
            $comision = $comisionVentaRepositorio->getComisionPorPrecio($detalle->getPrecio());
        }

        if(!is_null($comision)){
            $movimiento->setDescuentoMercadofree($comision->getComision());

            // Registrar el movimiento correspondiente al descuento de la comisión MercadoFree.
            $this->registrarDescuentoComision($usuario, $pedido);
        }

        $usuario->setSaldo($saldo + $saldoACobrarVendedor);

        $this->em->persist($movimiento);
        $this->em->persist($usuario);
        $this->em->flush();
        return $movimiento;
    }
    
    public function registrarDevolucionPedidoMercadofree(Pedido $pedido)
    {
        $tipo = $this
            ->em
            ->getRepository(ConceptoMovimientoCuenta::class)
            ->findOneBy(['slug' => ConceptoMovimientoCuenta::DEVOLUCION_PEDIDO_SLUG]);

        $usuario = $pedido->getUsuario();
        
        $movimiento = new MovimientoCuenta();
        $movimiento
            ->setReferencia($this->generarReferenciaMovimiento('CRED'))
            ->setFecha(new \DateTime('now'))
            ->setMonto($pedido->getTotal())
            ->setTipo(self::TIPO_CREDITO)
            ->setConceptoMovimientoCuentaid($tipo)
            ->setDescuentoMercadofree(0)
            ->setRefExterna('')
            ->setPedidoid($pedido)
            ->setUsuarioid($usuario);
        
        $saldo = $usuario->getSaldo();
        $usuario->setSaldo($saldo + $pedido->getTotal());

        $this->em->persist($movimiento);
        $this->em->persist($usuario);
        $this->em->flush();
        return $movimiento;
    }

    public function registrarDescuentoComision($usuario, Pedido $pedido)
    {
        $tipo = $this
            ->em
            ->getRepository(ConceptoMovimientoCuenta::class)
            ->findOneBy(['slug' => ConceptoMovimientoCuenta::COMISION_VENTA_MERCADOFREE_SLUG]);

        $saldoACobrarVendedor = $this->calculadoraDePreciosService->CalcularPrecio($pedido);

        $movimiento = new MovimientoCuenta();
        $movimiento
            ->setReferencia($this->generarReferenciaMovimiento())
            ->setFecha(new \DateTime('now'))
            ->setMonto($pedido->getSubtotal() - $saldoACobrarVendedor)
            ->setTipo(self::TIPO_DEBITO)
            ->setConceptoMovimientoCuentaid($tipo)
            ->setDescuentoMercadofree(0)
            ->setRefExterna('')
            ->setPedidoid($pedido)
            ->setUsuarioid($usuario);

        $this->em->persist($movimiento);

        $this->em->flush();
    }

    public function generarReferenciaMovimiento($type = 'DEB')
    {
        return $type.str_pad(mt_rand(0,9999), 4, '0', STR_PAD_LEFT);
    }
}