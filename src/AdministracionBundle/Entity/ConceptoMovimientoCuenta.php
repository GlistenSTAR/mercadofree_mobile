<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ConceptoMovimientoCuenta
 *
 * @ORM\Table(name="concepto_movimiento_cuenta")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\ConceptoMovimientoCuentaRepository")
 */
class ConceptoMovimientoCuenta
{
    const PAGO_PAYPAL_SLUG = 'pago-paypal';
    const RETIRO_PAYPAL_SLUG = 'retiro-paypal';
    const RECHAZO_RETIRO_PAYPAL_SLUG = 'rechazo-retiro-paypal';
    const PAGO_SALDO_MERCADOFREE_SLUG = 'pago-saldo-mercadofree';
    const CONFIRMACION_VENTA_PEDIDO_SLUG = 'confirmacion-venta-pedido';
    const COMISION_VENTA_MERCADOFREE_SLUG = 'comision-venta-mercadofree';
    const DEVOLUCION_PEDIDO_SLUG = 'devolucion-pedido';
    
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
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="MovimientoCuenta", mappedBy="concepto_movimiento_cuentaid")
     */
    protected $movimientos;


    public function __construct()
    {
        $this->movimientos = new ArrayCollection();
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
     * Set slug
     *
     * @param string $slug
     * @return ConceptoMovimientoCuenta
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return ConceptoMovimientoCuenta
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Add movimientos
     *
     * @param \AdministracionBundle\Entity\MovimientoCuenta $movimientos
     * @return ConceptoMovimientoCuenta
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
}
