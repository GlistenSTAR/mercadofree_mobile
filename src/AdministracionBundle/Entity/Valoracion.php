<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Valoracion
 *
 * @ORM\Table(name="valoracion", indexes={@ORM\Index(name="FKvaloracion61872", columns={"productoid"}), @ORM\Index(name="FKvaloracion803339", columns={"usuarioid"})})
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\ValoracionRepository")
 */
class Valoracion
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
     * @var integer
     *
     * @ORM\Column(name="asunto", type="integer", nullable=true)
     */
    private $asunto;

    /**
     * @var string
     *
     * @ORM\Column(name="opinion", type="string", length=255, nullable=true)
     */
    private $opinion;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

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
     * @var integer
     *
     * @ORM\Column(name="puntuacion", type="integer", nullable=true)
     */
    private $puntuacion;

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
     * @return int
     */
    public function getAsunto()
    {
        return $this->asunto;
    }

    /**
     * @param int $asunto
     */
    public function setAsunto($asunto)
    {
        $this->asunto = $asunto;
    }

    /**
     * @return string
     */
    public function getOpinion()
    {
        return $this->opinion;
    }

    /**
     * @param string $opinion
     */
    public function setOpinion($opinion)
    {
        $this->opinion = $opinion;
    }

    /**
     * @return int
     */
    public function getPuntuacion()
    {
        return $this->puntuacion;
    }

    /**
     * @param int $puntuacion
     */
    public function setPuntuacion($puntuacion)
    {
        $this->puntuacion = $puntuacion;
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
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

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



}
