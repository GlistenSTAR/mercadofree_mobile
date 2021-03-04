<?php

namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Exception\InvalidArgumentException;


/**
 * ComisionVenta
 *
 * @ORM\Table(name="comision_venta")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\ComisionVentaRepository")
 */
class ComisionVenta
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
     * @var float
     *
     * @ORM\Column(name="precio_minimo", type="float")
     */
    private $precioMinimo;

    /**
     * @var float
     *
     * @ORM\Column(name="precio_maximo", type="float")
     */
    private $precioMaximo;

    /**
     * @var int
     *
     * @ORM\Column(name="comision", type="integer")
     */
    private $comision;

    /**
     * @var Configuracion
     *
     * @ORM\OneToOne(targetEntity="Configuracion", inversedBy="comision_venta")
     * @ORM\JoinColumn(name="id_configuracion", referencedColumnName="id")
     */
    private $configuracion;

    /**
     * @var Categoria
     * 
     * @ORM\OneToOne(targetEntity="Categoria", inversedBy="comision_venta")
     * @ORM\JoinColumn(name="id_categoria", referencedColumnName="id")
     */
    private $categoria;

    protected $errors = [];
    protected $hasError = false;
    protected $tipo = null;

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
     * Set precioMinimo
     *
     * @param float $precioMinimo
     *
     * @return ComisionVenta
     */
    public function setPrecioMinimo($precioMinimo)
    {
        $this->precioMinimo = $precioMinimo;

        return $this;
    }

    /**
     * Get precioMinimo
     *
     * @return float
     */
    public function getPrecioMinimo()
    {
        return $this->precioMinimo;
    }

    /**
     * Set precioMaximo
     *
     * @param float $precioMaximo
     *
     * @return ComisionVenta
     */
    public function setPrecioMaximo($precioMaximo)
    {
        $this->precioMaximo = $precioMaximo;

        return $this;
    }

    /**
     * Get precioMaximo
     *
     * @return float
     */
    public function getPrecioMaximo()
    {
        return $this->precioMaximo;
    }

    /**
     * Set comision
     *
     * @param integer $comision
     *
     * @return ComisionVenta
     */
    public function setComision($comision)
    {
        $this->comision = $comision;

        return $this;
    }

    /**
     * Get comision
     *
     * @return integer
     */
    public function getComision()
    {
        return $this->comision;
    }

    /**
     * Set configuracion
     *
     * @param \AdministracionBundle\Entity\Configuracion $configuracion
     *
     * @return ComisionVenta
     */
    public function setConfiguracion(\AdministracionBundle\Entity\Configuracion $configuracion = null)
    {
        $this->configuracion = $configuracion;

        return $this;
    }

    /**
     * Get configuracion
     *
     * @return \AdministracionBundle\Entity\Configuracion
     */
    public function getConfiguracion()
    {
        return $this->configuracion;
    }

    /**
     * Set categoria
     *
     * @param \AdministracionBundle\Entity\Categoria $categoria
     *
     * @return ComisionVenta
     */
    public function setCategoria(\AdministracionBundle\Entity\Categoria $categoria = null)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return \AdministracionBundle\Entity\Categoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Retorna un array clave => valor, donde cada poscicion es el nombre del atributo con su respectivo valor
     */
    public function getAttributes()
    {
        return [
            'id' => $this->id,
            'tipo' => $this->getTipo(),
            'categoria' => isset($this->categoria) ? $this->categoria->getId() : null,
            'precio_minimo' => $this->precioMinimo,
            'precio_maximo' => $this->precioMaximo,
            'comision' => $this->comision,
        ];
    }

    /** 
     * Retorna tipo de comision
     *   1: por rango
     *   2: por categoria
     */
    public function getTipo()
    {
        if (empty($this->tipo)) {
            $this->tipo = empty($this->categoria) ? 1 : 2;
        }
        return $this->tipo;
    }

    public function setTipo($tipo)
    {
        if (($tipo != 1) && ($tipo != 2)) {
            throw new InvalidArgumentException('tipo invalido');
        }
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Retorna un array clave => valor, donde cada poscicion es el nombre del atributo con su respectivo valor
     */
    public function setAttributes($params)
    {
        $this->categoria    = $params['categoria'] ?? null;
        $this->precioMinimo = (float) $params['precio_minimo'] ?? null;
        $this->precioMaximo = (float) $params['precio_maximo'] ?? null;
        $this->comision     = (integer) $params['comision'] ?? null;
        $this->tipo         = (integer) $params['tipo'] ?? null;
    }

    public function isValid()
    {
        $this->hasError = false;
        $this->errors = [];
        $tipo = $this->getTipo();
        if ($tipo == 1) {
            if (!is_numeric($this->precioMinimo)) {
                $this->errors['precio_minimo'] = ['précio mínimo no valido'];
            } elseif (!is_numeric($this->precioMaximo)) {
                $this->errors['precio_maximo'] = ['précio maximo no valido'];
            } elseif (($this->precioMinimo < 0) || ($this->precioMinimo >= $this->precioMaximo) ){
                $this->errors['precio_maximo'] = ['précio minimo no valido'];
            } elseif ($this->precioMaximo <= $this->precioMinimo) {
                $this->errors['precio_maximo'] = ['précio maximo no valido'];
            }
        } else if (($tipo == 2)) {
            if (!($this->categoria instanceof Categoria)) {
                $this->errors['categoria'] = ['Categoria Invalida'];
            }
        } else {
            $this->errors['tipo'] = ['Tipo de comisión Invalido'];
        }

        if (!is_numeric($this->comision)) {
            $this->errors['comision'] = ['Formato Invalido']; 
        } elseif ($this->comision < 0) {
            $this->errors['comision'] = ['Formato Invalido'];
        } elseif ($this->comision > 100) {
            $this->errors['comision'] = ['Formato Invalido'];
        }

        $this->hasError = !empty($this->errors);
        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
