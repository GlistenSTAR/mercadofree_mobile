<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 01/03/2018
 * Time: 02:28 PM
 */

namespace AdministracionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Notificacion
 *
 * @ORM\Table(name="notificacion")
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\NotificacionRepository")
 */
class Notificacion
{

    public const NOTIFICATION_TYPE_PRODUCT_SALE = "product_sale";
    public const NOTIFICATION_TYPE_PRODUCT_QUESTION = "product_question";
    public const NOTIFICATION_TYPE_PRODUCT_RESPONSE = "product_response";
    public const NOTIFICATION_TYPE_PRODUCT_PUBLICATION_EXPIRED = "product_publication_expired";
    public const NOTIFICATION_TYPE_QUALIFIED_ORDER = "qualified_order";
    public const NOTIFICATION_TYPE_NEW_ACCOUNT_CREDIT = "new_account_credit";
    public const NOTIFICATION_TYPE_REIMBURSEMENT_REQUEST = "reimbursement_request";
    public const NOTIFICATION_TYPE_ORDER_SENT = "order_sent";
    public const NOTIFICATION_TYPE_ORDER_DELIVERED = "order_delivered";

    public const NOTIFICATION_PARAM_PROD_NAME= "{prodName}";
    public const NOTIFICATION_PARAM_ORDER_CODE= "{codPedido}";
    public const NOTIFICATION_PARAM_CREDIT_AMOUNT= "{creditAmount}";

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
     * @ORM\Column(name="titulo", type="string", length=255)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="icono", type="string", length=255, nullable=true)
     */
    private $icono;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje", type="text", nullable=true)
     */
    private $mensaje;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    public function __construct(){
        $this->enabled = true;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * @param string $titulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    /**
     * @return string
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }

    /**
     * @param string $mensaje
     */
    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

}