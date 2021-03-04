<?php


namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Ciudad
 *
 * @ORM\Table(name="ciudad", indexes={@ORM\Index(name="FKciudad393904", columns={"provincia_id"})})
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\CiudadRepository")
 */

class Ciudad
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
    public function getCiudadNombre()
    {
        return $this->ciudadNombre;
    }

    /**
     * @param string $ciudadNombre
     */
    public function setCiudadNombre($ciudadNombre)
    {
        $this->ciudadNombre = $ciudadNombre;
    }

    /**
     * @return int
     */
    public function getCodigoPostal()
    {
        return $this->codigoPostal;
    }

    /**
     * @param int $codigoPostal
     */
    public function setCodigoPostal($codigoPostal)
    {
        $this->codigoPostal = $codigoPostal;
    }

    /**
     * @return int
     */
    public function getProvinciaid()
    {
        return $this->provinciaid;
    }

    /**
     * @param int $provinciaid
     */
    public function setProvinciaid($provinciaid)
    {
        $this->provinciaid = $provinciaid;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="ciudad_nombre", type="string", length=255, nullable=true)
     */
    private $ciudadNombre;

    /**
     * @var integer
     *
     * @ORM\Column(name="cp", type="integer", nullable=true)
     */
    private $codigoPostal;


    /**
     * @var Provincia
     *
     * @ORM\ManyToOne(targetEntity="Provincia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="provincia_id", referencedColumnName="id")
     * })
     */
    private $provinciaid;

    /**
     * @ORM\OneToMany(targetEntity="AdministracionBundle\Entity\Direccion", mappedBy="ciudad", cascade={"remove"})
     */
    private $direcciones;

    function __toString() {
        return $this->getCiudadNombre();
    }
}