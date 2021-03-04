<?php


namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Objetivo
 *
 * @ORM\Table(name="objetivo")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\ObjetivoRepository")
 */

class Objetivo
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
     * @var string
     *
     * @ORM\Column(name="icono", type="string", length=255, nullable=true)
     */
    private $icono;

    /**
     * @var integer
     *
     * @ORM\Column(name="puntos", type="integer", nullable=false)
     *
     */
    private $puntos;

    /**
     * @var integer
     *
     * @ORM\Column(name="visible", type="integer", length=1, nullable=false)
     *
     */
    private $visible;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @var CategoriaObjetivo
     *
     * @ORM\ManyToOne(targetEntity="CategoriaObjetivo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoriaobjetivoid", referencedColumnName="id")
     * })
     */
    private $categoriaobjetivoid;

    /**
     * @ORM\Column(name="nuevo", type="boolean")
     */
    private $nuevo = true;

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
     * @return string
     */
    public function getIcono()
    {
        return $this->icono;
    }

    /**
     * @param string $icono
     */
    public function setIcono($icono)
    {
        $this->icono = $icono;
    }

    /**
     * @return int
     */
    public function getPuntos()
    {
        return $this->puntos;
    }

    /**
     * @param int $puntos
     */
    public function setPuntos($puntos)
    {
        $this->puntos = $puntos;
    }

    /**
     * @return int
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param int $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    /**
     * @return CategoriaObjetivo
     */
    public function getCategoriaobjetivoid()
    {
        return $this->categoriaobjetivoid;
    }

    /**
     * @param CategoriaObjetivo $categoriaobjetivoid
     */
    public function setCategoriaobjetivoid($categoriaobjetivoid)
    {
        $this->categoriaobjetivoid = $categoriaobjetivoid;
    }

    /**
     * @return mixed
     */
    public function getObjetivoUsuario()
    {
        return $this->objetivoUsuario;
    }

    /**
     * @param mixed $objetivoUsuario
     */
    public function setObjetivoUsuario($objetivoUsuario)
    {
        $this->objetivoUsuario = $objetivoUsuario;
    }

    /**
     * @ORM\OneToMany(targetEntity="UsuarioObjetivo", mappedBy="objetivoid", cascade={"persist"})
     */
    private $objetivoUsuario;

    /**
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }


    /**
     * @return boolean
     */
    public function getNuevo()
    {
        return $this->nuevo;
    }

    /**
     * @param boolean $nuevo
     */
    public function setNuevo($nuevo)
    {
        $this->nuevo = $nuevo;
    }



}