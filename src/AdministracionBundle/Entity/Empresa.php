<?php


namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Empresa
 *
 * @ORM\Table(name="empresa", indexes={@ORM\Index(name="FKempresa32982", columns={"usuarioid"})})
 * @ORM\Entity
 */
class Empresa
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
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * @param string $cuit
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;
    }

    /**
     * @return string
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * @param string $razonSocial
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;
    }

    /**
     * @return Usuario
     */
    public function getUsuarioid()
    {
        return $this->usuarioid;
    }

    /**
     * @param Usuario $usuarioid
     */
    public function setUsuarioid($usuarioid)
    {
        $this->usuarioid = $usuarioid;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=255, nullable=true)
     */
    private $cuit;



    /**
     * @var string
     *
     * @ORM\Column(name="razon_social", type="string", length=255, nullable=true)
     */
    private $razonSocial;



    /**
     * @var Usuario
     *
     * @ORM\OneToOne(targetEntity="Usuario", inversedBy="empresaid")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuarioid", referencedColumnName="id")
     * })
     */
    private $usuarioid;

    /**
     * @var InformacionFiscal
     *
     * @ORM\OneToOne(targetEntity="AdministracionBundle\Entity\InformacionFiscal", mappedBy="empresaid", cascade={"persist"})
     */
    private $informacionfiscal;

    /**
     * @return InformacionFiscal
     */
    public function getInformacionfiscal()
    {
        return $this->informacionfiscal;
    }

    /**
     * @param InformacionFiscal $informacionfiscal
     */
    public function setInformacionfiscal($informacionfiscal)
    {
        $this->informacionfiscal = $informacionfiscal;
    }


}