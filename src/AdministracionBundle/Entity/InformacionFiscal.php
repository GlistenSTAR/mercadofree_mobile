<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 28/03/2018
 * Time: 11:59 AM
 */

namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * InformacionFiscal
 *
 * @ORM\Table(name="informacion_fiscal")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\InformacionFiscalRepository")
 */

class InformacionFiscal
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
    public function getFormInscripcionIngresosBrutos()
    {
        return $this->formInscripcionIngresosBrutos;
    }

    /**
     * @param string $formInscripcionIngresosBrutos
     */
    public function setFormInscripcionIngresosBrutos($formInscripcionIngresosBrutos)
    {
        $this->formInscripcionIngresosBrutos = $formInscripcionIngresosBrutos;
    }

    /**
     * @return string
     */
    public function getCertificadoExclusion()
    {
        return $this->certificadoExclusion;
    }

    /**
     * @param string $certificadoExclusion
     */
    public function setCertificadoExclusion($certificadoExclusion)
    {
        $this->certificadoExclusion = $certificadoExclusion;
    }

    /**
     * @return \DateTime
     */
    public function getFechaIniValidezCert()
    {
        return $this->fechaIniValidezCert;
    }

    /**
     * @param \DateTime $fechaIniValidezCert
     */
    public function setFechaIniValidezCert($fechaIniValidezCert)
    {
        $this->fechaIniValidezCert = $fechaIniValidezCert;
    }

    /**
     * @return \DateTime
     */
    public function getFechaFinValidezCert()
    {
        return $this->fechaFinValidezCert;
    }

    /**
     * @param \DateTime $fechaFinValidezCert
     */
    public function setFechaFinValidezCert($fechaFinValidezCert)
    {
        $this->fechaFinValidezCert = $fechaFinValidezCert;
    }

    /**
     * @return mixed
     */
    public function getTipoContribuyente()
    {
        return $this->tipoContribuyente;
    }

    /**
     * @param mixed $tipoContribuyente
     */
    public function setTipoContribuyente(TipoContribuyente $tipoContribuyente)
    {
        $this->tipoContribuyente = $tipoContribuyente;
    }

    /**
     * @return mixed
     */
    public function getRegimenIngresosBrutos()
    {
        return $this->regimenIngresosBrutos;
    }

    /**
     * @param mixed $regimenIngresosBrutos
     */
    public function setRegimenIngresosBrutos(RegimenIngresosBrutos $regimenIngresosBrutos)
    {
        $this->regimenIngresosBrutos = $regimenIngresosBrutos;
    }


    /**
     * @var string
     *
     * @ORM\Column(name="form_inscripcion_ingresos_brutos", type="string", length=255, nullable=true)
     */
    private $formInscripcionIngresosBrutos;



    /**
     * @var string
     *
     * @ORM\Column(name="certificado_exclusion", type="string", length=255, nullable=true)
     */
    private $certificadoExclusion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaIni_validez_cert", type="date", nullable=true)
     */
    private $fechaIniValidezCert;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaFin_validez_cert", type="date", nullable=true)
     */
    private $fechaFinValidezCert;

    /**
     * @ORM\ManyToOne(targetEntity="TipoContribuyente")
     */
    private $tipoContribuyente;

    /**
     * @ORM\ManyToOne(targetEntity="RegimenIngresosBrutos")
     */
    private $regimenIngresosBrutos;

    /**
     * @var Empresa
     *
     * @ORM\OneToOne(targetEntity="AdministracionBundle\Entity\Empresa", inversedBy="informacionfiscal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="empresaid", referencedColumnName="id")
     * })
     */
    private $empresaid;

    /**
     * @return Usuario
     */
    public function getEmpresaid()
    {
        return $this->empresaid;
    }

    /**
     * @param Usuario $empresaid
     */
    public function setEmpresaid($empresaid)
    {
        $this->empresaid = $empresaid;
    }

}