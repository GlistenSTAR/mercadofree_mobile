<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OpcionValoracionCalidadProductoPedido
 *
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\OpcionValoracionTiempoEntregaPedidoRepository")
 */
class OpcionValoracionTiempoEntregaPedido {
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="detalle", type="string", length=255, nullable=false)
     */
    private $detalle;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="valor", type="integer", nullable=false)
     */
    private $valor;
    
    function getId() {
        return $this->id;
    }

    function getDetalle() {
        return $this->detalle;
    }

    function getValor() {
        return $this->valor;
    }

    function setDetalle($detalle) {
        $this->detalle = $detalle;
    }

    function setValor($valor){
        $this->valor = $valor;
    }


}
