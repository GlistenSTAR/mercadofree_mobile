<?php

namespace AdministracionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Categoria
 *
 * @ORM\Table(name="categoria", indexes={@ORM\Index(name="FKcategoria29957", columns={"categoriaid"}), @ORM\Index(name="FKcategoria811148", columns={"Tiendaid"})})
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\CategoriaRepository")
 */
class Categoria
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
     * @var integer
     *
     * @ORM\Column(name="nivel", type="integer", nullable=true)
     */
    private $nivel;

    /**
     * @var string
     *
     * @ORM\Column(name="icono", type="string", length=50, nullable=false)
     */
    private $icono;

    /**
     * @var integer
     *
     * @ORM\Column(name="tiempo_expiracion_publicaciones", type="integer", nullable=true)
     */
    private $tiempoExpiracion;

    /**
     * @ORM\OneToOne(targetEntity="ComisionVenta", mappedBy="categoria", cascade={"remove"})
     */
    protected $comision_venta;

    /**
     * @var Categoria
     *
     * @ORM\ManyToOne(targetEntity="Categoria", inversedBy="categoriaHijas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoriaid", referencedColumnName="id")
     * })
     */
    private $categoriaid;

    /**
     * @var Tienda
     *
     * @ORM\ManyToOne(targetEntity="Tienda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Tiendaid", referencedColumnName="id")
     * })
     */
    private $tiendaid;

    /**
     * @ORM\OneToMany(targetEntity="CaracteristicaCategoria", mappedBy="categoriaid", cascade={"remove"})
     */
    private $caracteristicas;

    /**
     * @ORM\OneToMany(targetEntity="Categoria", mappedBy="categoriaid")
     */
    private $categoriaHijas;

    /**
     * @ORM\OneToMany(targetEntity="Producto", mappedBy="categoriaid")
     */
    private $productos;

    public function __construct()
    {
        $this->categoriaHijas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productos = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return int
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * @param int $nivel
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;
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
     * @return mixed
     */
    public function getCaracteristicas()
    {
        return $this->caracteristicas;
    }

    /**
     * @param mixed $caracteristicas
     */
    public function setCaracteristicas($caracteristicas)
    {
        $this->caracteristicas = $caracteristicas;
    }

    /**
     * @return mixed
     */
    public function getProductos()
    {
        return $this->productos;
    }

    /**
     * @param mixed $productos
     */
    public function setProductos($productos)
    {
        $this->productos = $productos;
    }

    /**
     * @return mixed
     */
    public function getCategoriaHijas()
    {
        return $this->categoriaHijas;
    }

    /**
     * @param mixed $categoriaHijas
     */
    public function setCategoriaHijas($categoriaHijas)
    {
        $this->categoriaHijas = $categoriaHijas;
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


    public function getCantidadProductosPublicados()
    {
        $productos = $this->getProductos();
        $cantidad = 0;
        if (count($productos) > 0) {
            foreach ($productos as $p) {
                if ($p->getEstadoProductoid()->getSlug() === 'publicado') {
                    $cantidad++;
                }
            }
        }
        return $cantidad;
    }

    public function getPublicaciones()
    {
        $publicaciones = $this->getRecursivePublicaciones($this);
        return $publicaciones;
    }

    public function getRecursivePublicaciones($categoria, $publicaciones = 0)
    {
        if ($categoria->isLeaf())
            return $categoria->getCantidadProductosPublicados();
        if (!$categoria->isLeaf()) {
            $hijas = $categoria->getCategoriaHijas();
            if (count($hijas) > 0) {
                foreach ($hijas as $hija) {
                    $publicaciones += $this->getRecursivePublicaciones($hija, $hija->getCantidadProductosPublicados());
                }
            }
        }
        return $publicaciones;
    }

    public function isLeaf()
    {
        return (bool)count($this->categoriaHijas) == 0;
    }

    public function isRoot()
    {
        return is_null($this->getCategoriaid());
    }

    public function getParent()
    {
        if (!is_null($this->getCategoriaid()))
            return $this->categoriaid;
        return null;
    }


    public function getRoot()
    {
        $categoria = $this;
        while ($categoria->getCategoriaid() !== null) {
            $categoria = $categoria->getCategoriaid();
        }
        return $categoria;
    }


    public function getPath()
    {
        $categoria = $this;
        $result [] = array(
            'id' => $categoria->getId(),
            'nombre' => $categoria->getNombre(),
            'nivel' => $categoria->getNivel(),
            'slug' => $categoria->getSlug()
        );
        while ($categoria->getCategoriaid() !== null) {
            $categoria = $categoria->getCategoriaid();
            $result [] = array(
                'id' => $categoria->getId(),
                'nombre' => $categoria->getNombre(),
                'nivel' => $categoria->getNivel(),
                'slug' => $categoria->getSlug()
            );
        }
        return array_reverse($result, false);
    }


    public function getChildren()
    {
        $hijas = $this->getCategoriaHijas();
        $children = array();
        foreach ($hijas as $categoria) {
            $children [] = array(
                'id' => $categoria->getId(),
                'nombre' => $categoria->getNombre(),
                'nivel' => $categoria->getNivel(),
                'slug' => $categoria->getSlug(),
                'productos' => $categoria->getPublicaciones()
            );
        }
        return $children;
    }

    /**
     * Set comisionVenta
     *
     * @param \AdministracionBundle\Entity\ComisionVenta $comisionVenta
     *
     * @return Categoria
     */
    public function setComisionVenta(\AdministracionBundle\Entity\ComisionVenta $comisionVenta = null)
    {
        $this->comision_venta = $comisionVenta;

        return $this;
    }

    /**
     * Get comisionVenta
     *
     * @return \AdministracionBundle\Entity\ComisionVenta
     */
    public function getComisionVenta()
    {
        return $this->comision_venta;
    }

    /**
     * Add caracteristica
     *
     * @param \AdministracionBundle\Entity\CaracteristicaCategoria $caracteristica
     *
     * @return Categoria
     */
    public function addCaracteristica(\AdministracionBundle\Entity\CaracteristicaCategoria $caracteristica)
    {
        $this->caracteristicas[] = $caracteristica;

        return $this;
    }

    /**
     * Remove caracteristica
     *
     * @param \AdministracionBundle\Entity\CaracteristicaCategoria $caracteristica
     */
    public function removeCaracteristica(\AdministracionBundle\Entity\CaracteristicaCategoria $caracteristica)
    {
        $this->caracteristicas->removeElement($caracteristica);
    }

    /**
     * Add categoriaHija
     *
     * @param \AdministracionBundle\Entity\Categoria $categoriaHija
     *
     * @return Categoria
     */
    public function addCategoriaHija(\AdministracionBundle\Entity\Categoria $categoriaHija)
    {
        $this->categoriaHijas[] = $categoriaHija;

        return $this;
    }

    /**
     * Remove categoriaHija
     *
     * @param \AdministracionBundle\Entity\Categoria $categoriaHija
     */
    public function removeCategoriaHija(\AdministracionBundle\Entity\Categoria $categoriaHija)
    {
        $this->categoriaHijas->removeElement($categoriaHija);
    }

    /**
     * Add producto
     *
     * @param \AdministracionBundle\Entity\Producto $producto
     *
     * @return Categoria
     */
    public function addProducto(\AdministracionBundle\Entity\Producto $producto)
    {
        $this->productos[] = $producto;

        return $this;
    }

    /**
     * Remove producto
     *
     * @param \AdministracionBundle\Entity\Producto $producto
     */
    public function removeProducto(\AdministracionBundle\Entity\Producto $producto)
    {
        $this->productos->removeElement($producto);
    }

    function getTiempoExpiracion() {
        return $this->tiempoExpiracion;
    }

    /**
     * Función para obtener los meses asignados a la categoría
     * o a una de las categorías padres
     *
     * @return int
     */
    function obtenerTiempoExpiracion() {

        $tiempoExpiracion = $this->tiempoExpiracion;

        if(!$tiempoExpiracion) {
            if($this->getCategoriaid()) {
                $tiempoExpiracion = $this->getCategoriaid()->obtenerTiempoExpiracion();
            }
        }

        return $tiempoExpiracion;
    }

    function setTiempoExpiracion($tiempoExpiracion) {
        $this->tiempoExpiracion = $tiempoExpiracion;
    }

}
