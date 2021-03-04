<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Direccion
 *
 * @ORM\Table(name="direccion", indexes={@ORM\Index(name="FKdireccion132439", columns={"Tiendaid"}), @ORM\Index(name="FKdireccion831343", columns={"productoid"}), @ORM\Index(name="FKdireccion275035", columns={"usuarioid"})})
 * @ORM\Entity
 */
class Direccion
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
     * @var boolean
     *
     * @ORM\Column(name="direccion_venta", type="boolean", nullable=true)
     */
    private $direccionVenta;

    /**
     * @var boolean
     *
     * @ORM\Column(name="direccion_compra", type="boolean", nullable=true)
     */
    private $direccionCompra;



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
     * @return int
     */
    public function getDireccionVenta()
    {
        return $this->direccionVenta;
    }

    /**
     * @param int $direccionVenta
     */
    public function setDireccionVenta($direccionVenta)
    {
        $this->direccionVenta = $direccionVenta;
    }

    /**
     * @return bool
     */
    public function isDireccionCompra()
    {
        return $this->direccionCompra;
    }

    /**
     * @param bool $direccionCompra
     */
    public function setDireccionCompra($direccionCompra)
    {
        $this->direccionCompra = $direccionCompra;
    }



    /**
     * @return string
     */
    public function getCodigoPostal()
    {
        return $this->codigoPostal;
    }

    /**
     * @param string $codigoPostal
     */
    public function setCodigoPostal($codigoPostal)
    {
        $this->codigoPostal = $codigoPostal;
    }

    /**
     * @return Ciudad
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * @param Ciudad
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;
    }

    /**
     * @return Provincia
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * @param Provincia
     */
    public function setProvincia($provincia)
    {
        $this->provincia = $provincia;
    }

    /**
     * @return string
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * @param string $pais
     */
    public function setPais($pais)
    {
        $this->pais = $pais;
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
     * @return Producto
     */
    public function getProductoid()
    {
        return $this->productoid;
    }

    /**
     * @param Producto $productoid
     */
    public function setProductoid($productoid)
    {
        $this->productoid = $productoid;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_postal", type="string", length=255, nullable=true)
     */
    private $codigoPostal;

    /**
     * @var Ciudad
     *
     * @ORM\ManyToOne(targetEntity="Ciudad", inversedBy="direcciones")
     */
    private $ciudad;

    /**
     * @var Provincia
     *
     * @ORM\ManyToOne(targetEntity="Provincia")
     */
    private $provincia;

    /**
     * @var string
     *
     * @ORM\Column(name="calle", type="string", length=255, nullable=true)
     */
    private $calle;

    /**
     * @return string
     */
    public function getCalle()
    {
        return $this->calle;
    }

    /**
     * @param string $calle
     */
    public function setCalle($calle)
    {
        $this->calle = $calle;
    }

    /**
     * @return string
     */
    public function getEntreCalle()
    {
        return $this->entreCalle;
    }

    /**
     * @param string $entreCalle
     */
    public function setEntreCalle($entreCalle)
    {
        $this->entreCalle = $entreCalle;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="entre_calle", type="string", length=255, nullable=true)
     */
    private $entreCalle;

    /**
     * @var string
     *
     * @ORM\Column(name="pais", type="string", length=255, nullable=true)
     */
    private $pais;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=255, nullable=true)
     */
    private $numero;
    /**
     * @var string
     *
     * @ORM\Column(name="otros_datos", type="string", length=255, nullable=true)
     */
    private $otrosDatos;

    /**
     * @return string
     */
    public function getOtrosDatos()
    {
        return $this->otrosDatos;
    }

    /**
     * @param string $otrosDatos
     */
    public function setOtrosDatos($otrosDatos)
    {
        $this->otrosDatos = $otrosDatos;
    }

    /**
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param string $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * @var \Tienda
     *
     * @ORM\ManyToOne(targetEntity="Tienda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Tiendaid", referencedColumnName="id")
     * })
     */
    private $tiendaid;



    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuarioid", referencedColumnName="id")
     * })
     */
    private $usuarioid;

    /**
     * @var \Producto
     *
     * @ORM\ManyToOne(targetEntity="Producto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="productoid", referencedColumnName="id")
     * })
     */
    private $productoid;

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
