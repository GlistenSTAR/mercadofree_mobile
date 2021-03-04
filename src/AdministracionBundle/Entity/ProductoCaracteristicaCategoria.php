<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 07/03/2018
 * Time: 11:13 AM
 */

namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProductoCaracteristicaCategoria
 *
 * @ORM\Table(name="producto_caracteristica_categoria", indexes={@ORM\Index(name="FKproducto_c197259", columns={"caracteristica_categoriaid"}), @ORM\Index(name="FKproducto_c490229", columns={"productoid"})})
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\ProductoRepository")
 */

class ProductoCaracteristicaCategoria
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CaracteristicaCategoria", inversedBy="productoCaracteristicaCategoria", cascade={"persist"})
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="caracteristica_categoriaid", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $caracteristicaCategoriaid;

    /**
     * @return mixed
     */
    public function getCaracteristicaCategoriaid()
    {
        return $this->caracteristicaCategoriaid;
    }

    /**
     * @param mixed $caracteristicaCategoriaid
     */
    public function setCaracteristicaCategoriaid($caracteristicaCategoriaid)
    {
        $this->caracteristicaCategoriaid = $caracteristicaCategoriaid;
    }

    /**
     * @return mixed
     */
    public function getProductoid()
    {
        return $this->productoid;
    }

    /**
     * @param mixed $productoid
     */
    public function setProductoid($productoid)
    {
        $this->productoid = $productoid;
    }

    /**
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @param string $valor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Producto", inversedBy="productoCaracteristicaCategoria", cascade={"persist"})
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="productoid", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $productoid;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="string", length=255, nullable=true)
     */
    private $valor;
}