<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoProducto
 *
 * @ORM\Table(name="estado_producto")
 * @ORM\Entity
 */
class EstadoProducto
{
    const ESTADO_PUBLICADO_SLUG = 'publicado';
    const ESTADO_FINALIZADO_SLUG = 'finalizado';
    
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
     * 
     * @return bool
     */
    public function estadoFinalizado() {
        return $this->getSlug() == EstadoProducto::ESTADO_FINALIZADO_SLUG;
    }
    
    public function estadoPublicado() {
        return $this->getSlug() == EstadoProducto::ESTADO_PUBLICADO_SLUG;
    }


}
