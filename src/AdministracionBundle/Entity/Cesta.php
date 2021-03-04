<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cesta
 *
 * @ORM\Table(name="cesta")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\CestaRepository")
 */
class Cesta
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
     * @var Producto
     *
     * @ORM\ManyToOne(targetEntity="Producto", inversedBy="cestas")
     */
    private $producto;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="cestas")
     */
    private $usuario;

	/**
	 * @var Pedido
	 *
	 * @ORM\ManyToOne(targetEntity="Pedido", inversedBy="cestas")
	 */
	private $pedido;

    /**
     * @var int
     *
     * @ORM\Column(name="cantidad", type="integer")
     */
    private $cantidad;


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
     * Set producto
     *
     * @param Producto $producto
     * @return Cesta
     */
    public function setProducto(Producto $producto)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto
     *
     * @return Producto
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set usuario
     *
     * @param Usuario $usuario
     * @return Cesta
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
     * Set cantidad
     *
     * @param integer $cantidad
     * @return Cesta
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer 
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

	/**
	 * @return Pedido
	 */
	public function getPedido() {
		return $this->pedido;
	}

	/**
	 * @param Pedido $pedido
	 */
	public function setPedido( $pedido ) {
		$this->pedido = $pedido;
	}


}
