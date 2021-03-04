<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CaracteristicaCategoria
 *
 * @ORM\Table(name="caracteristica_categoria", indexes={@ORM\Index(name="FKcaracteris342193", columns={"categoriaid"})})
 * @ORM\Entity
 */
class CaracteristicaCategoria
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return Categoria
     */
    public function getCategoriaid()
    {
        return $this->categoriaid;
    }

    /**
     * @param Categoria $categoriaid
     */
    public function setCategoriaid($categoriaid)
    {
        $this->categoriaid = $categoriaid;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductoid()
    {
        return $this->productoid;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $productoid
     */
    public function setProductoid($productoid)
    {
        $this->productoid = $productoid;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @var Categoria
     *
     * @ORM\ManyToOne(targetEntity="Categoria", inversedBy="caracteristicas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoriaid", referencedColumnName="id")
     * })
     */
    private $categoriaid;

    /**
     * @ORM\OneToMany(targetEntity="ProductoCaracteristicaCategoria", mappedBy="caracteristicaCategoriaid", cascade={"persist","remove"})
     */
    private $productoCaracteristicaCategoria;

    /**
     * @return mixed
     */
    public function getProductoCaracteristicaCategoria()
    {
        return $this->productoCaracteristicaCategoria;
    }

    /**
     * @param mixed $productoCaracteristicaCategoria
     */
    public function setProductoCaracteristicaCategoria($productoCaracteristicaCategoria)
    {
        $this->productoCaracteristicaCategoria = $productoCaracteristicaCategoria;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productoid = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
