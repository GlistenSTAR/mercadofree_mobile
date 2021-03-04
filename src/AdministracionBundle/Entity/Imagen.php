<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Imagen
 *
 * @ORM\Table(name="imagen", indexes={@ORM\Index(name="FKimagen984970", columns={"productoid"}), @ORM\Index(name="FKimagen286066", columns={"Tiendaid"})})
 * @ORM\Entity
 */
class Imagen
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
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    private $alt;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="destacada", type="boolean", nullable=true)
     */
    private $destacada;

    /**
     * @var string
     *
     * @ORM\Column(name="portada", type="boolean", nullable=true)
     */
    private $portada;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var Tienda
     *
     * @ORM\ManyToOne(targetEntity="Tienda", inversedBy="imagenes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tiendaid", referencedColumnName="id")
     * })
     */
    private $tiendaid;

    /**
     * @var Producto
     *
     * @ORM\ManyToOne(targetEntity="Producto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="productoid", referencedColumnName="id")
     * })
     */
    private $productoid;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @var boolean
     *
     * @ORM\Column(name="slider_home", type="boolean", nullable=true)
     */
    private $sliderHome;


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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return boolean
     */
    public function getDestacada()
    {
        return $this->destacada;
    }

    /**
     * @param boolean $destacada
     */
    public function setDestacada($destacada)
    {
        $this->destacada = $destacada;
    }

    /**
     * @return boolean
     */
    public function getPortada()
    {
        return $this->portada;
    }

    /**
     * @param boolean $portada
     */
    public function setPortada($portada)
    {
        $this->portada = $portada;
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return Tienda
     */
    public function getTiendaid()
    {
        return $this->tiendaid;
    }

    /**
     * @param Tienda $tiendaid
     */
    public function setTiendaid($tiendaid)
    {
        $this->tiendaid = $tiendaid;
    }

    /**
     * @return Producto
     */
    public function getProductoid()
    {
        return $this->productoid;
    }

    /**
     * @param Producto $productoid
     */
    public function setProductoid($productoid)
    {
        $this->productoid = $productoid;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return boolean
     */
    public function isSliderHome()
    {
        return $this->sliderHome;
    }

    /**
     * @param boolean $sliderHome
     */
    public function setSliderHome($sliderHome)
    {
        $this->sliderHome = $sliderHome;
    }


}
