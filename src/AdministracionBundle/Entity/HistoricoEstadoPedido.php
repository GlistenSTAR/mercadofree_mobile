<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistoricoEstadoPedido
 *
 * @ORM\Table(name="historico_estado_pedido")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\HistoricoEstadoPedidoRepository")
 */
class HistoricoEstadoPedido {
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;
    
    /**
     * @var Pedido
     *
     * @ORM\ManyToOne(targetEntity="Pedido")
     * @ORM\JoinColumn(name="pedido_id", referencedColumnName="id")
     */
    private $pedido;
    
    /**
     * @var EstadoPedido
     *
     * @ORM\ManyToOne(targetEntity="EstadoPedido")
     * @ORM\JoinColumn(name="estado_pedido_id", referencedColumnName="id")
     */
    private $estadoPedido;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="modificado_por_admin", type="boolean", nullable=true)
     */
    private $modificadoPorAdmin;
    
    public function __construct(Pedido $pedido, EstadoPedido $estadoPedido, $modificadoPorAdmin = null) {
        $this->fecha = new \DateTime();
        $this->pedido = $pedido;
        $this->modificadoPorAdmin = $modificadoPorAdmin;
        $this->estadoPedido = $estadoPedido;
    }
        
    function getId() {
        return $this->id;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getPedido() {
        return $this->pedido;
    }

    function getEstadoPedido() {
        return $this->estadoPedido;
    }
    
    function getModificadoPorAdmin() {
        return $this->modificadoPorAdmin;
    }
}
