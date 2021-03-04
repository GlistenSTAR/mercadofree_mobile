<?php

namespace AdministracionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TipoEnvio
 *
 * @ORM\Table(name="tipo_envio")
 * @ORM\Entity
 */
class TipoEnvio
{
    
    const ENVIO_MERCADOFREE_ID = 3;
    const ENVIO_RECOGIDA_EN_TIENDA_ID = 4;
    const ENVIO_GRATIS_ID = 5;
    const ENVIO_A_DOMICILIO_POR_VENDEDOR_ID = 6;
    const TIPO_ENVIO_GRATIS_SLUG = 'envio-gratis';
    const TIPO_ENVIO_DOMICILIO_VENDEDOR = 'envio-domicilio-vendedor';
    const TIPO_ENVIO_DOMICILIO_MERCADOFREE = 'envio-domicilio-mercadofree';
    
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
     * @return int
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * @param int $precio
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;
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
     * @var integer
     *
     * @ORM\Column(name="precio", type="integer", nullable=true)
     */
    private $precio;



    /**
     * Add usuarioid
     *
     * @param \AdministracionBundle\Entity\Usuario $usuarioid
     * @return TipoEnvio
     */
    public function addUsuarioid(\AdministracionBundle\Entity\Usuario $usuarioid)
    {
        $this->usuarios[] = $usuarioid;

        return $this;
    }

    /**
     * Remove usuarioid
     *
     * @param \AdministracionBundle\Entity\Usuario $usuarioid
     * @return TipoEnvio
     */
    public function removeUsuarioid(\AdministracionBundle\Entity\Usuario $usuarioid)
    {
        $this->usuarios->removeElement($usuarioid);

        return $this;
    }




    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $usuarios
     */
    public function setUsuarios($usuarios)
    {
        $this->usuarios = $usuarios;
    }






    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Usuario", inversedBy="tipoenvios")
     * @ORM\JoinTable(name="usuario_tipoenvio",
     *   joinColumns={
     *     @ORM\JoinColumn(name="tipoenvioid", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="usuarioid", referencedColumnName="id")
     *   }
     * )
     */
    private $usuarios;

    /**
     * Constructor
     */
    public function __construct()
    {
        //$this->productoid = new \Doctrine\Common\Collections\ArrayCollection();
        $this->usuarios = new ArrayCollection();
    }

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AdministracionBundle\Entity\Producto", mappedBy="tipoenvioid")
     */
    private $productos;
    
    public function esGratis() {
        return $this->getSlug() == TipoEnvio::TIPO_ENVIO_GRATIS_SLUG;
    }
    
    public function esDomicilioVendedor() {
        return $this->getSlug() == TipoEnvio::TIPO_ENVIO_DOMICILIO_VENDEDOR;
    }

    function esEnvioMercadoFree() {
        return $this->getId() == TipoEnvio::ENVIO_MERCADOFREE_ID;
    }
    
    function esRecogidaEnTienda() {
        return $this->getId() == TipoEnvio::ENVIO_RECOGIDA_EN_TIENDA_ID;
    }
    
    function esEnvioGratis() {
        return $this->getId() == TipoEnvio::ENVIO_GRATIS_ID;
    }
    
    function esEnvioADomicilioPorVendedor() {
        return $this->getId() == TipoEnvio::ENVIO_A_DOMICILIO_POR_VENDEDOR_ID;
    }

}
