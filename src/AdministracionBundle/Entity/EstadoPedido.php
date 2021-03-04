<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoPedido
 *
 * @ORM\Table(name="estado_pedido")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\EstadoPedidoRepository")
 */
class EstadoPedido
{
    const ESTADO_PEDIDO_PENDIENTE_SLUG = 'pendiente';
    const ESTADO_PEDIDO_PENDIENTE_PAGO_SLUG = 'pendiente-pago';
    const ESTADO_PEDIDO_PAGADO_SLUG = 'pagado';
    const ESTADO_PEDIDO_ENVIADO_SLUG = 'enviado';
    const ESTADO_PEDIDO_RECIBIDO_SLUG = 'recibido';
    const ESTADO_PEDIDO_CERRADO_SLUG = 'cerrado';
    const ESTADO_PEDIDO_SOLICITADO_DEVOLUCION_SLUG = 'solicitado-devolucion';
    const ESTADO_PEDIDO_DEVUELTO_SLUG = 'devuelto';
    
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
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;


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
     * Set nombre
     *
     * @param string $nombre
     * @return EstadoPedido
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
     * Set slug
     *
     * @param string $slug
     * @return EstadoPedido
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
    
    function estadoPendiente() {
        return $this->getSlug() == EstadoPedido::ESTADO_PEDIDO_PENDIENTE_SLUG;
    }
    
    /**
     * @return boolean
     */
    public function estadoEntregado() {
        return $this->getSlug() == EstadoPedido::ESTADO_PEDIDO_RECIBIDO_SLUG;
    }
    
    /**
     * @return boolean
     */
    public function estadoCerrado() {
        return $this->getSlug() == EstadoPedido::ESTADO_PEDIDO_CERRADO_SLUG;
    }
    
    /**
     * @return boolean
     */
    public function estadoPagado() {
        return $this->getSlug() == EstadoPedido::ESTADO_PEDIDO_PAGADO_SLUG;
    }
    
    public function estadoDevuelto() {
        return $this->getSlug() == EstadoPedido::ESTADO_PEDIDO_DEVUELTO_SLUG;
    }
    
    public function getNextState($fromSeller = false) {
        $next=array();
    	switch ($this->getSlug()){
            case 'pendiente':
                $next=array("pendiente-pago","pagado","cancelado");
                break;

            case 'pendiente-pago':
                    $next=array("pagado","cancelado");
                    break;

            case 'cancelado':
                $next=array("cerrado");
                break;

            case 'pagado':
                $next=array("enviado","recibido");
                break;

            case self::ESTADO_PEDIDO_ENVIADO_SLUG:
                $next=array("recibido","devuelto");
                break;

            case 'recibido':
                $next = ($fromSeller) ? array() : array("cerrado");
                break;
            
            case self::ESTADO_PEDIDO_SOLICITADO_DEVOLUCION_SLUG:
                $next=array(self::ESTADO_PEDIDO_DEVUELTO_SLUG);
                break;

            case self::ESTADO_PEDIDO_DEVUELTO_SLUG:
                $next=array(self::ESTADO_PEDIDO_CERRADO_SLUG, self::ESTADO_PEDIDO_ENVIADO_SLUG);
                break;
        }

        return $next;
    }
    
    function __toString() {
        return $this->getNombre();
    }
}
