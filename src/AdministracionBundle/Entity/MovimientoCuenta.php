<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MovimientoCuenta
 *
 * @ORM\Table(name="movimiento_cuenta")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\MovimientoCuentaRepository")
 */
class MovimientoCuenta
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
     * @var string
     *
     * @ORM\Column(name="referencia", type="string", length=9, nullable=false)
     */
    private $referencia;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;

    /**
     * @var float
     *
     * @ORM\Column(name="monto", type="float", nullable=false)
     */
    private $monto;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=10)
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="ref_externa", type="string", length=255)
     */
    private $ref_externa;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuarioid", referencedColumnName="id")
     * })
     */
    private $usuarioid;

    /**
     * @var Pedido
     *
     * @ORM\ManyToOne(targetEntity="ConceptoMovimientoCuenta", inversedBy="movimientos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="concepto_movimiento_cuentaid", referencedColumnName="id")
     * })
     */
    private $concepto_movimiento_cuentaid;

    /**
     * @var Pedido
     *
     * @ORM\ManyToOne(targetEntity="Pedido")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pedidoid", referencedColumnName="id")
     * })
     */
    private $pedidoid;

    /**
     * @var int
     *
     * @ORM\Column(name="descuento_mercadofree", type="integer", length=10)
     */
    private $descuento_mercadofree;
    
    /**
     * @var Payment
     * 
     * @ORM\OneToOne(targetEntity="Payment")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

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
     * Set referencia
     *
     * @param string $referencia
     * @return MovimientoCuenta
     */
    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;

        return $this;
    }

    /**
     * Get referencia
     *
     * @return string 
     */
    public function getReferencia()
    {
        return $this->referencia;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return MovimientoCuenta
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
     * Set monto
     *
     * @param float $monto
     * @return MovimientoCuenta
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return float 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     * @return MovimientoCuenta
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set ref_externa
     *
     * @param string $refExterna
     * @return MovimientoCuenta
     */
    public function setRefExterna($refExterna)
    {
        $this->ref_externa = $refExterna;

        return $this;
    }

    /**
     * Get ref_externa
     *
     * @return string 
     */
    public function getRefExterna()
    {
        return $this->ref_externa;
    }

    /**
     * Set descuento_mercadofree
     *
     * @param integer $descuentoMercadofree
     * @return MovimientoCuenta
     */
    public function setDescuentoMercadofree($descuentoMercadofree)
    {
        $this->descuento_mercadofree = $descuentoMercadofree;

        return $this;
    }

    /**
     * Get descuento_mercadofree
     *
     * @return integer 
     */
    public function getDescuentoMercadofree()
    {
        return $this->descuento_mercadofree;
    }

    /**
     * Set usuarioid
     *
     * @param \AdministracionBundle\Entity\Usuario $usuarioid
     * @return MovimientoCuenta
     */
    public function setUsuarioid(\AdministracionBundle\Entity\Usuario $usuarioid = null)
    {
        $this->usuarioid = $usuarioid;

        return $this;
    }

    /**
     * Get usuarioid
     *
     * @return \AdministracionBundle\Entity\Usuario 
     */
    public function getUsuarioid()
    {
        return $this->usuarioid;
    }

    /**
     * Set concepto_movimiento_cuentaid
     *
     * @param \AdministracionBundle\Entity\ConceptoMovimientoCuenta $conceptoMovimientoCuentaid
     * @return MovimientoCuenta
     */
    public function setConceptoMovimientoCuentaid(\AdministracionBundle\Entity\ConceptoMovimientoCuenta $conceptoMovimientoCuentaid = null)
    {
        $this->concepto_movimiento_cuentaid = $conceptoMovimientoCuentaid;

        return $this;
    }

    /**
     * Get concepto_movimiento_cuentaid
     *
     * @return \AdministracionBundle\Entity\ConceptoMovimientoCuenta 
     */
    public function getConceptoMovimientoCuentaid()
    {
        return $this->concepto_movimiento_cuentaid;
    }

    /**
     * Set pedidoid
     *
     * @param \AdministracionBundle\Entity\Pedido $pedidoid
     * @return MovimientoCuenta
     */
    public function setPedidoid(\AdministracionBundle\Entity\Pedido $pedidoid = null)
    {
        $this->pedidoid = $pedidoid;

        return $this;
    }

    /**
     * Get pedidoid
     *
     * @return \AdministracionBundle\Entity\Pedido 
     */
    public function getPedidoid()
    {
        return $this->pedidoid;
    }
    
    /**
     * @return Payment
     */
    public function getPayment() {
        return $this->payment;
    }
    
    /**
     * @param Payment $payment
     */
    public function setPayment(Payment $payment) {
        $this->payment = $payment;
    }
}
