<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 29/04/2018
 * Time: 12:12 PM
 */

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Visita
 *
 * @ORM\Table(name="visita", indexes={@ORM\Index(name="FKvisita393903", columns={"categoriaid"}), @ORM\Index(name="FKvisita978173", columns={"productoid"}), @ORM\Index(name="FKvisita610978", columns={"usuarioid"})})
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\VisitaRepository")
 */
class Visita
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
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param \DateTime $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * @return \Categoria
     */
    public function getCategoriaid()
    {
        return $this->categoriaid;
    }

    /**
     * @param \Categoria $categoriaid
     */
    public function setCategoriaid($categoriaid)
    {
        $this->categoriaid = $categoriaid;
    }

    /**
     * @return \Producto
     */
    public function getProductoid()
    {
        return $this->productoid;
    }

    /**
     * @param \Producto $productoid
     */
    public function setProductoid($productoid)
    {
        $this->productoid = $productoid;
    }

    /**
     * @return \Usuario
     */
    public function getUsuarioid()
    {
        return $this->usuarioid;
    }

    /**
     * @param \Usuario $usuarioid
     */
    public function setUsuarioid($usuarioid)
    {
        $this->usuarioid = $usuarioid;
    }

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @var \Categoria
     *
     * @ORM\ManyToOne(targetEntity="Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoriaid", referencedColumnName="id")
     * })
     */
    private $categoriaid;

    /**
     * @var \Producto
     *
     * @ORM\ManyToOne(targetEntity="Producto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="productoid", referencedColumnName="id")
     * })
     */
    private $productoid;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuarioid", referencedColumnName="id")
     * })
     */
    private $usuarioid;

    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_cookie", type="integer", nullable=false)
     */

    private $usuarioCookie;

    /**
     * @var integer
     *
     * @ORM\Column(name="historial", type="integer", nullable=true)
     */

    private $historial;

    /**
     * @return int
     */
    public function getHistorial()
    {
        return $this->historial;
    }

    /**
     * @param int $historial
     */
    public function setHistorial($historial)
    {
        $this->historial = $historial;
    }

    /**
     * @return int
     */
    public function getUsuarioCookie()
    {
        return $this->usuarioCookie;
    }

    /**
     * @param int $usuarioCookie
     */
    public function setUsuarioCookie($usuarioCookie)
    {
        $this->usuarioCookie = $usuarioCookie;
    }


}