<?php

namespace AdministracionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Usuario
 *
 * @ORM\Table(name="usuario", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})}, indexes={@ORM\Index(name="FKusuario32982", columns={"rolid"})})
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\UsuarioRepository")
 */
class Usuario implements UserInterface
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
     * @ORM\Column(name="confirmation_token", type="string", length=255, nullable=true)
     */
    private $confirmationToken;

    /**
     * @var datetime
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     */
    private $passwordRequestedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="puntos", type="integer", nullable=false)
     */
    private $puntos;


    /**
     * @var integer
     *
     * @ORM\Column(name="nivel", type="integer", nullable=false)
     */
    private $nivel;
    /**
     * @return int
     */

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;


    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="garantia", type="text", nullable=true)
     */
    private $garantia;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mostrarGarantia", type="boolean", nullable=true)
     */
    private $mostrarGarantia;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_registro", type="date", nullable=true)
     */
    private $fechaRegistro;

    /**
     * @var string
     *
     * @ORM\Column(name="iban", type="string", length=20, nullable=true)
     */
    private $iban;

    /**
     * @var Rol
     *
     * @ORM\ManyToOne(targetEntity="Rol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rolid", referencedColumnName="id")
     * })
     */
    private $rolid;


    /**
     * @var EstadoUsuario
     *
     * @ORM\ManyToOne(targetEntity="EstadoUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estado_usuarioid", referencedColumnName="id")
     * })
     */
    private $estadoUsuarioid;


    /**
     * @ORM\OneToMany(targetEntity="UsuarioObjetivo", mappedBy="usuarioid", cascade={"persist"})
     */
    private $objetivoUsuario;

    /**
     * @ORM\OneToMany(targetEntity="Producto", mappedBy="usuarioid", cascade={"persist"})
     */
    private $productos;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Producto", inversedBy="usuarioid")
     * @ORM\JoinTable(name="usuario_productos_favoritos",
     *   joinColumns={
     *     @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="producto_id", referencedColumnName="id")
     *   }
     * )
     */
    private $productosFavoritos;


    /**
     * @ORM\OneToMany(targetEntity="Direccion", mappedBy="usuarioid", cascade={"persist"})
     */
    private $direccion;

    /**
     * @var Cesta
     *
     * @ORM\OneToMany(targetEntity="Cesta", mappedBy="usuario", cascade={"remove"})
     *
     */
    private $cestas;

    /**
     * @var Cliente
     *
     * @ORM\OneToOne(targetEntity="Cliente", mappedBy="usuarioid", cascade={"persist"})
     */
    private $clienteid;

    /**
     * @ORM\OneToMany(targetEntity="UsuarioCampanna", mappedBy="usuarioid", cascade={"persist"})
     */
    private $usuarioCampanna;

    /**
     * @var Empresa
     *
     * @ORM\OneToOne(targetEntity="Empresa", mappedBy="usuarioid")
     */
    private $empresaid;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="TipoEnvio", mappedBy="usuarios")
     */
    private $tipoenvios;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="CostoEnvio", mappedBy="usuarioid")
     */
    private $grupoCostoEnvios;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AdministracionBundle\Entity\Pedido", mappedBy="usuario")
     */
    private $pedidos;

    /**
     * @var integer
     * @ORM\Column(name="incidencias_contacto", type="integer", nullable=true)
     */
    private $incidenciasContacto;

    /**
     * @var string
     *
     * @ORM\Column(name="saldo", type="float", nullable=true)
     */
    private $saldo;

    /**
     * @ORM\OneToMany(targetEntity="MovimientoCuenta", mappedBy="usuarioid")
     */
    protected $movimientos;
    
    /**
     * @var Tienda
     *
     * @ORM\OneToOne(targetEntity="Tienda", mappedBy="usuarioid", cascade={"persist"})
     */
    private $tienda;

    /**
     * @var string
     *
     * @ORM\Column(name="email_paypal", type="string", length=255, nullable=true)
     */
    protected $emailPaypal;

    /**
     * @ORM\OneToMany(targetEntity="AdministracionBundle\Entity\NotificacionUsuario", mappedBy="usuario")
     */
    protected $notificacionesUsuario;

    public function __construct()
    {
        $this->incidenciasContacto = 0;
        $this->productosFavoritos = new ArrayCollection();
        $this->tipoenvios = new ArrayCollection();
        $this->cestas = new ArrayCollection();
        $this->grupoCostoEnvios = new ArrayCollection();
        $this->movimientos = new ArrayCollection();
        $this->saldo = 0;
        $this->notificacionesUsuario = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getGarantia()
    {
        return $this->garantia;
    }

    /**
     * @param string $garantia
     */
    public function setGarantia($garantia)
    {
        $this->garantia = $garantia;
    }

    public function getRoles()
    {

        return array($this->rolid->getNombre());
    }

    /**
     * @return Rol
     */
    public function getRolid()
    {
        return $this->rolid;
    }

    /**
     * @param Rol $rolid
     */
    public function setRolid($rolid)
    {
        $this->rolid = $rolid;
    }

    /**
     * @return EstadoUsuario
     */
    public function getEstadoUsuarioid()
    {
        return $this->estadoUsuarioid;
    }

    /**
     * @param EstadoUsuario $estadoUsuarioid
     */
    public function setEstadoUsuarioid($estadoUsuarioid)
    {
        $this->estadoUsuarioid = $estadoUsuarioid;
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
    public function getProductosFavoritos()
    {
        return $this->productosFavoritos;
    }

    /**
     * @param mixed $productosFavoritos
     */
    public function setProductosFavoritos($productosFavoritos)
    {
        $this->productosFavoritos = $productosFavoritos;
    }

    public function addProductoFavorito(Producto $prod)
    {
        $this->productosFavoritos->add($prod);
    }

    public function isProductoFavorito($idProducto)
    {
        foreach ($this->productosFavoritos as $pf) {
            if ($pf->getId() == $idProducto) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * @param mixed $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    public function getDireccionCompra()
    {
        foreach ($this->direccion as $dir) {
            if ($dir->isDireccionCompra()) {
                return $dir;
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * @param string $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }
    
    public function getCliente() {
        return $this->getClienteid();
    }

    /**
     * @return Cliente
     */
    public function getClienteid()
    {
        return $this->clienteid;
    }

    /**
     * @param Cliente $clienteid
     */
    public function setClienteid($clienteid)
    {
        $this->clienteid = $clienteid;
    }

    /**
     * @return Empresa
     */
    public function getEmpresaid()
    {
        return $this->empresaid;
    }

    /**
     * @param Empresa $empresaid
     */
    public function setEmpresaid($empresaid)
    {
        $this->empresaid = $empresaid;
    }

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

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTipoenvios()
    {
        return $this->tipoenvios;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $tipoenvios
     */
    public function setTipoenvios($tipoenvios)
    {
        $this->tipoenvios = $tipoenvios;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGrupoCostoEnvios()
    {
        return $this->grupoCostoEnvios;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $grupoCostoEnvios
     */
    public function setGrupoCostoEnvios($grupoCostoEnvios)
    {
        $this->grupoCostoEnvios = $grupoCostoEnvios;
    }

    /**
     * @return Cesta
     */
    public function getCestas()
    {
        return $this->cestas;
    }

    /**
     * @param Cesta $cestas
     */
    public function setCestas($cestas)
    {
        $this->cestas = $cestas;
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
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }


    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return \DateTime
     */
    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }

    /**
     * @param \DateTime $fechaRegistro
     */
    public function setFechaRegistro($fechaRegistro)
    {
        $this->fechaRegistro = $fechaRegistro;
    }

    function eraseCredentials()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     */
    public function setIban($iban)
    {
        $this->iban = $iban;
    }

	/**
	 * @return bool
	 */
	public function isMostrarGarantia() {
		return $this->mostrarGarantia;
	}

	/**
	 * @param bool $mostrarGarantia
	 */
	public function setMostrarGarantia( $mostrarGarantia ) {
		$this->mostrarGarantia = $mostrarGarantia;
	}


    public function getDireccionVenta()
    {
        if ($this->direccion != null) {
            foreach ($this->direccion as $dir) {
                if ($dir->getDireccionVenta() == 1) {
                    return $dir;
                }
            }

            return null;
        }

        return null;
    }

    /**
     * Remove productosFavoritos
     *
     * @param \AdministracionBundle\Entity\Producto $productosFavoritos
     */
    public function removeProductoFavorito(\AdministracionBundle\Entity\Producto $prodFavoritos)
    {
        $this->productosFavoritos->removeElement($prodFavoritos);

        //$prodFavoritos->removeUsuarioid($this);
    }

    public function hasTipoEnvio($slug)
    {
        //echo count($this->tipoenvios);die;
        if ($this->tipoenvios != null && count($this->tipoenvios) > 0) {
            foreach ($this->tipoenvios as $te) {
                if ($te->getSlug() == $slug) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getEnvioFijoPais()
    {
        if ($this->grupoCostoEnvios != null) {
            foreach ($this->grupoCostoEnvios as $gC) {
                if ($gC->isTodoElPais() == 1) {
                    return $gC;
                }
            }

        }

        return null;

    }


    /**
     * Set confirmationToken
     *
     * @param string $confirmationToken
     * @return Usuario
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Get confirmationToken
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Set passwordRequestedAt
     *
     * @param \DateTime $passwordRequestedAt
     * @return Usuario
     */
    public function setPasswordRequestedAt($passwordRequestedAt)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    /**
     * Get passwordRequestedAt
     *
     * @return \DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * Add objetivoUsuario
     *
     * @param \AdministracionBundle\Entity\UsuarioObjetivo $objetivoUsuario
     * @return Usuario
     */
    public function addObjetivoUsuario(\AdministracionBundle\Entity\UsuarioObjetivo $objetivoUsuario)
    {
        $this->objetivoUsuario[] = $objetivoUsuario;

        return $this;
    }

    /**
     * Remove objetivoUsuario
     *
     * @param \AdministracionBundle\Entity\UsuarioObjetivo $objetivoUsuario
     */
    public function removeObjetivoUsuario(\AdministracionBundle\Entity\UsuarioObjetivo $objetivoUsuario)
    {
        $this->objetivoUsuario->removeElement($objetivoUsuario);
    }

    /**
     * Add productos
     *
     * @param \AdministracionBundle\Entity\Producto $productos
     * @return Usuario
     */
    public function addProducto(\AdministracionBundle\Entity\Producto $productos)
    {
        $this->productos[] = $productos;

        return $this;
    }

    /**
     * Remove productos
     *
     * @param \AdministracionBundle\Entity\Producto $productos
     */
    public function removeProducto(\AdministracionBundle\Entity\Producto $productos)
    {
        $this->productos->removeElement($productos);
    }

    /**
     * Add productosFavoritos
     *
     * @param \AdministracionBundle\Entity\Producto $productosFavoritos
     * @return Usuario
     */
    public function addProductosFavorito(\AdministracionBundle\Entity\Producto $productosFavoritos)
    {
        $this->productosFavoritos[] = $productosFavoritos;

        return $this;
    }

    /**
     * Remove productosFavoritos
     *
     * @param \AdministracionBundle\Entity\Producto $productosFavoritos
     */
    public function removeProductosFavorito(\AdministracionBundle\Entity\Producto $productosFavoritos)
    {
        $this->productosFavoritos->removeElement($productosFavoritos);
    }

    /**
     * Add direccion
     *
     * @param \AdministracionBundle\Entity\Direccion $direccion
     * @return Usuario
     */
    public function addDireccion(\AdministracionBundle\Entity\Direccion $direccion)
    {
        $this->direccion[] = $direccion;

        return $this;
    }

    /**
     * Remove direccion
     *
     * @param \AdministracionBundle\Entity\Direccion $direccion
     */
    public function removeDireccion(\AdministracionBundle\Entity\Direccion $direccion)
    {
        $this->direccion->removeElement($direccion);
    }

    /**
     * Add cestas
     *
     * @param \AdministracionBundle\Entity\Cesta $cestas
     * @return Usuario
     */
    public function addCesta(\AdministracionBundle\Entity\Cesta $cestas)
    {
        $this->cestas[] = $cestas;

        return $this;
    }

    /**
     * Remove cestas
     *
     * @param \AdministracionBundle\Entity\Cesta $cestas
     */
    public function removeCesta(\AdministracionBundle\Entity\Cesta $cestas)
    {
        $this->cestas->removeElement($cestas);
    }

    /**
     * Add usuarioCampanna
     *
     * @param \AdministracionBundle\Entity\UsuarioCampanna $usuarioCampanna
     * @return Usuario
     */
    public function addUsuarioCampanna(\AdministracionBundle\Entity\UsuarioCampanna $usuarioCampanna)
    {
        $this->usuarioCampanna[] = $usuarioCampanna;

        return $this;
    }

    /**
     * Remove usuarioCampanna
     *
     * @param \AdministracionBundle\Entity\UsuarioCampanna $usuarioCampanna
     */
    public function removeUsuarioCampanna(\AdministracionBundle\Entity\UsuarioCampanna $usuarioCampanna)
    {
        $this->usuarioCampanna->removeElement($usuarioCampanna);
    }

    /**
     * Add tipoenvios
     *
     * @param \AdministracionBundle\Entity\TipoEnvio $tipoenvios
     * @return Usuario
     */
    public function addTipoenvio(\AdministracionBundle\Entity\TipoEnvio $tipoenvios)
    {
        $this->tipoenvios[] = $tipoenvios;

        return $this;
    }

    /**
     * Remove tipoenvios
     *
     * @param \AdministracionBundle\Entity\TipoEnvio $tipoenvios
     */
    public function removeTipoenvio(\AdministracionBundle\Entity\TipoEnvio $tipoenvios)
    {
        $this->tipoenvios->removeElement($tipoenvios);
    }

    /**
     * Add grupoCostoEnvios
     *
     * @param \AdministracionBundle\Entity\CostoEnvio $grupoCostoEnvios
     * @return Usuario
     */
    public function addGrupoCostoEnvio(\AdministracionBundle\Entity\CostoEnvio $grupoCostoEnvios)
    {
        $this->grupoCostoEnvios[] = $grupoCostoEnvios;

        return $this;
    }

    /**
     * Remove grupoCostoEnvios
     *
     * @param \AdministracionBundle\Entity\CostoEnvio $grupoCostoEnvios
     */
    public function removeGrupoCostoEnvio(\AdministracionBundle\Entity\CostoEnvio $grupoCostoEnvios)
    {
        $this->grupoCostoEnvios->removeElement($grupoCostoEnvios);
    }

    /**
     * Add pedidos
     *
     * @param \AdministracionBundle\Entity\Pedido $pedidos
     * @return Usuario
     */
    public function addPedido(\AdministracionBundle\Entity\Pedido $pedidos)
    {
        $this->pedidos[] = $pedidos;

        return $this;
    }

    /**
     * Remove pedidos
     *
     * @param \AdministracionBundle\Entity\Pedido $pedidos
     */
    public function removePedido(\AdministracionBundle\Entity\Pedido $pedidos)
    {
        $this->pedidos->removeElement($pedidos);
    }

    /**
     * Get pedidos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPedidos()
    {
        return $this->pedidos;
    }

    public function isPasswordRequestNonExpired($tokenTtl)
    {
        $dif = 0;
        if($this->getPasswordRequestedAt()!== null){
            $actual = new \DateTime('now');
            $dif = $actual->getTimestamp() - $this->getPasswordRequestedAt()->getTimestamp();
        }
        $flag = ($dif > $tokenTtl);

        return array('expired' => $flag);

    }

    /**
     * Get mostrarGarantia
     *
     * @return boolean
     */
    public function getMostrarGarantia()
    {
        return $this->mostrarGarantia;
    }

    /**
     * Set saldo
     *
     * @param float $saldo
     * @return Usuario
     */
    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Get saldo
     *
     * @return float
     */
    public function getSaldo()
    {
        return $this->saldo;
    }

    /**
     * Add movimientos
     *
     * @param \AdministracionBundle\Entity\MovimientoCuenta $movimientos
     * @return Usuario
     */
    public function addMovimiento(\AdministracionBundle\Entity\MovimientoCuenta $movimientos)
    {
        $this->movimientos[] = $movimientos;

        return $this;
    }

    /**
     * Remove movimientos
     *
     * @param \AdministracionBundle\Entity\MovimientoCuenta $movimientos
     */
    public function removeMovimiento(\AdministracionBundle\Entity\MovimientoCuenta $movimientos)
    {
        $this->movimientos->removeElement($movimientos);
    }

    /**
     * Get movimientos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMovimientos()
    {
        return $this->movimientos;
    }

    public function getIncidenciasContacto() {
        return $this->incidenciasContacto;
    }

    public function addIncidenciaContacto() {
        $this->incidenciasContacto++;
    }

    /**
     * Función para restaurar las incidencias del usuario a cero
     */
    public function resetIncidenciasContacto() {
        $this->incidenciasContacto = 0;
    }
    
    /**
     * @return Pedido
     */
    public function getUltimoPedido() {
        return $this->getPedidos()->last();
                
    }

    public function realizaEnvios() {
        /** @var TipoEnvio $tipoEnvio */
        foreach($this->getTipoenvios() as $tipoEnvio) {
            if($tipoEnvio->esEnvioMercadoFree() || $tipoEnvio->esEnvioGratis() || $tipoEnvio->esEnvioADomicilioPorVendedor()) {
                return true;
            }
        }
        
        return false;
    }
    
    public function getTienda() {
        return $this->tienda;
    }

    function getEmailPaypal() {
        return $this->emailPaypal;
    }

    function setEmailPaypal(string $emailPaypal) {
        $this->emailPaypal = $emailPaypal;

    }

    public function getName()
    {
        if($this->clienteid != null && $this->clienteid->getNombre() != ''){
            return $this->clienteid->getNombre();
        }
        else{
            return $this->email;
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getNotificacionesUsuario()
    {
        return $this->notificacionesUsuario;
    }

    /**
     * @param ArrayCollection $notificacionesUsuario
     */
    public function setNotificacionesUsuario($notificacionesUsuario)
    {
        $this->notificacionesUsuario = $notificacionesUsuario;
    }

    public function getCantNotificacionesNuevas()
    {
        $cant = 0;
        foreach ($this->notificacionesUsuario as $notif){
            if( ! $notif->isLeida()){
                $cant ++;
            }
        }

        return $cant;
    }



}
