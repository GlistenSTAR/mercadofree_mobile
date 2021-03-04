<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DireccionEnvio
 *
 * @ORM\Table(name="direccion_envio")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\DireccionEnvioRepository")
 */
class DireccionEnvio
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="pais", type="string", length=255, nullable=true)
     */
    private $pais;

    /**
     * @var Provincia
     *
     * @ORM\ManyToOne(targetEntity="Provincia")
     */
    private $provincia;

    /**
     * @var Ciudad
     *
     * @ORM\ManyToOne(targetEntity="Ciudad")
     */
    private $ciudad;

    /**
     * @var string
     *
     * @ORM\Column(name="calle", type="string", length=255, nullable=true)
     */
    private $calle;

    /**
     * @var string
     *
     * @ORM\Column(name="entreCalle", type="string", length=255, nullable=true)
     */
    private $entreCalle;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=20, nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="otrosDatos", type="string", length=255, nullable=true)
     */
    private $otrosDatos;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pais
     *
     * @param string $pais
     * @return DireccionEnvio
     */
    public function setPais($pais)
    {
        $this->pais = $pais;

        return $this;
    }

    /**
     * Get pais
     *
     * @return string 
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * Set provincia
     *
     * @param Provincia $provincia
     * @return DireccionEnvio
     */
    public function setProvincia(Provincia $provincia)
    {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * Get provincia
     *
     * @return Provincia
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * Set ciudad
     *
     * @param Ciudad $ciudad
     * @return DireccionEnvio
     */
    public function setCiudad(Ciudad $ciudad)
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    /**
     * Get ciudad
     *
     * @return Ciudad
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set calle
     *
     * @param string $calle
     * @return DireccionEnvio
     */
    public function setCalle($calle)
    {
        $this->calle = $calle;

        return $this;
    }

    /**
     * Get calle
     *
     * @return string 
     */
    public function getCalle()
    {
        return $this->calle;
    }

    /**
     * Set entreCalle
     *
     * @param string $entreCalle
     * @return DireccionEnvio
     */
    public function setEntreCalle($entreCalle)
    {
        $this->entreCalle = $entreCalle;

        return $this;
    }

    /**
     * Get entreCalle
     *
     * @return string 
     */
    public function getEntreCalle()
    {
        return $this->entreCalle;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return DireccionEnvio
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set otrosDatos
     *
     * @param string $otrosDatos
     * @return DireccionEnvio
     */
    public function setOtrosDatos($otrosDatos)
    {
        $this->otrosDatos = $otrosDatos;

        return $this;
    }

    /**
     * Get otrosDatos
     *
     * @return string 
     */
    public function getOtrosDatos()
    {
        return $this->otrosDatos;
    }

	public function getFormatedDir($format){
		$dir="";
		switch ($format){
			case 'direccion-compra-envio':
				if($this->calle!=null && $this->calle!=""){
					$dir.="<b>".$this->calle."</b>";
					if($this->numero!=null && $this->numero!=""){
						$dir.=" #".$this->numero;
					}
				}

				if($this->otrosDatos!=null && $this->otrosDatos!=""){
					$dir.=", ".$this->otrosDatos;
				}

				if($this->entreCalle!=null && $this->entreCalle!=""){
					$dir.=", entre ".$this->entreCalle.". ";
				}

				if($this->ciudad!=null){
					$dir.=$this->ciudad->getCiudadNombre();
					$dir.=" CP: ".$this->ciudad->getCodigoPostal().". ";
				}

				if($this->provincia!=null){
					$dir.=$this->provincia->getNombre();
				}


		}

		return $dir;
	}
}
