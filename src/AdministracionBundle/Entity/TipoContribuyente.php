<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 28/03/2018
 * Time: 12:07 PM
 */

namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * TipoContribuyente
 *
 * @ORM\Table(name="tipo_contribuyente")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\TipoContribuyenteRepository")
 */
class TipoContribuyente
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



}