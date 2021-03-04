<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Oferta
 *
 * @ORM\Table(name="oferta")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\OfertaRepository")
 */
class Oferta
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
     * @return \DateTime
     */
    public function getFechainicio()
    {
        return $this->fechainicio;
    }

    /**
     * @param \DateTime $fechainicio
     */
    public function setFechainicio($fechainicio)
    {
        $this->fechainicio = $fechainicio;
    }

    /**
     * @return \DateTime
     */
    public function getFechafin()
    {
        return $this->fechafin;
    }

    /**
     * @param \DateTime $fechafin
     */
    public function setFechafin($fechafin)
    {
        $this->fechafin = $fechafin;
    }

    /**
     * @return int
     */
    public function getPorcientodescuento()
    {
        return $this->porcientodescuento;
    }

    /**
     * @param int $porcientodescuento
     */
    public function setPorcientodescuento($porcientodescuento)
    {
        $this->porcientodescuento = $porcientodescuento;
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
     * @var \DateTime
     *
     * @ORM\Column(name="fechaInicio", type="date", nullable=true)
     */
    private $fechainicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaFin", type="date", nullable=true)
     */
    private $fechafin;

    /**
     * @var integer
     *
     * @ORM\Column(name="porcientoDescuento", type="integer", nullable=true)
     */
    private $porcientodescuento;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Producto", inversedBy="ofertaid")
     * @ORM\JoinTable(name="oferta_producto",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ofertaid", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="productoid", referencedColumnName="id")
     *   }
     * )
     */
    private $productoid;

    /**
     * @var EstadoProducto
     *
     * @ORM\ManyToOne(targetEntity="EstadoProducto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estado_productoid", referencedColumnName="id")
     * })
     */
    private $estadoProductoid;

    /**
     * @return EstadoProducto
     */
    public function getEstadoProductoid()
    {
        return $this->estadoProductoid;
    }

    /**
     * @param EstadoProducto $estadoProductoid
     */
    public function setEstadoProductoid($estadoProductoid)
    {
        $this->estadoProductoid = $estadoProductoid;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productoid = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
