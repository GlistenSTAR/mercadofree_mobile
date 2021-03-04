<?php


namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * CaracteristicaCategoria
 *
 * @ORM\Table(name="campanna")
 * @ORM\Entity
 */

class Campanna
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
     * @return int
     */
    public function getPresupuesto()
    {
        return $this->presupuesto;
    }

    /**
     * @param int $presupuesto
     */
    public function setPresupuesto($presupuesto)
    {
        $this->presupuesto = $presupuesto;
    }

    /**
     * @return decimal
     */
    public function getCostoVisita()
    {
        return $this->costoVisita;
    }

    /**
     * @param decimal $costoVisita
     */
    public function setCostoVisita($costoVisita)
    {
        $this->costoVisita = $costoVisita;
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
     * @var integer
     *
     * @ORM\Column(name="presupuesto", type="integer", nullable=true)
     */
    private $presupuesto;


    /**
     * @var decimal
     *
     * @ORM\Column(name="costo_visita", type="decimal", nullable=true)
     */
    private $costoVisita;

    /**
     * @ORM\OneToMany(targetEntity="UsuarioCampanna", mappedBy="campannaid", cascade={"persist"})
     */
    private $usuarioCampanna;

    /**
     * @return mixed
     */
    public function getUsuarioCampanna()
    {
        return $this->usuarioCampanna;
    }

    /**
     * @param mixed $usuarioCampanna
     */
    public function setUsuarioCampanna($usuarioCampanna)
    {
        $this->usuarioCampanna = $usuarioCampanna;
    }

    public function getEstado($idUsuario){
    	foreach ($this->usuarioCampanna as $uc){
    		if($uc->getUsuarioid()->getId() == $idUsuario->getId()){
				return $uc->getEstadoCampannaid();
		    }
	    }

	    return null;
    }
}