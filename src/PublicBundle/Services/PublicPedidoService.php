<?php

namespace PublicBundle\Services;


use AdministracionBundle\Entity\EstadoPedido;
use AdministracionBundle\Entity\Notificacion;
use AdministracionBundle\Entity\Pedido;
use AdministracionBundle\Entity\Usuario;
use AppBundle\Services\NotificacionService;
use Symfony\Component\DependencyInjection\ContainerInterface;


class PublicPedidoService
{
    protected $container;
    protected $em;
    protected $utilPublicService;
    protected $notificationService;

    public function __construct(ContainerInterface $container, UtilsPublicService $utilPublicService, NotificacionService $notificationService)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->utilPublicService = $utilPublicService;
        $this->notificationService = $notificationService;
    }

    /**
     * Envia un email de notificacion cuyos parametros varían según el estado del pedido
     *
     * @param EstadoPedido $state
     * @param Pedido $pedido
     */
    public function stateChangedNotification(EstadoPedido $state, Pedido $pedido)
    {
        $emailTemp = '';
        $toUser = null;
        $configuracion = $this->em->getRepository('AdministracionBundle:Configuracion')->find(1);
        $emailFrom = $configuracion->getEmailAdministrador();
        $subject = '';
        switch ($state->getSlug()){
            case EstadoPedido::ESTADO_PEDIDO_ENVIADO_SLUG:

                // Enviar email al comprador

                $toUser = $pedido->getUsuario();
                $subject = '[MercadoFree] Pedido enviado';
                $emailTemp = $this->container->get('templating')
                    ->render('PublicBundle:Email:mensaje_email_notificacion_pedido_enviado.html.twig',array(
                        'user_firstname' => $toUser->getName(),
                        'pedido' => $pedido
                    )) ;

                // Enviar también notificación del sistema al comprador

                $this->notificationService->send(
                    $toUser,
                    Notificacion::NOTIFICATION_TYPE_ORDER_SENT,
                    array(
                        Notificacion::NOTIFICATION_PARAM_ORDER_CODE => $pedido->getCodigo()
                    )
                );

                break;
            case EstadoPedido::ESTADO_PEDIDO_RECIBIDO_SLUG:
                // Enviar email al comprador para que califique el pedido
                $toUser = $pedido->getUsuario();
                $subject = '[MercadoFree] Califica tu pedido';
                $emailTemp = $this->container->get('templating')
                    ->render('PublicBundle:Email:mensaje_email_notificacion_pedido_recibido.html.twig',array(
                        'user_firstname' => $toUser->getName(),
                        'pedido' => $pedido
                    )) ;

                // Enviar también notificación del sistema

                $this->notificationService->send(
                    $toUser,
                    Notificacion::NOTIFICATION_TYPE_ORDER_DELIVERED,
                    array(
                        Notificacion::NOTIFICATION_PARAM_ORDER_CODE => $pedido->getCodigo()
                    )
                );

                break;
            case EstadoPedido::ESTADO_PEDIDO_SOLICITADO_DEVOLUCION_SLUG:
                // Enviar email al vendedor con la solicitud de devolución
                $toUser = $pedido->getVendedor()->getEmail();
                $subject = '[MercadoFree] Solicitud de devolución de pedido';
                $emailTemp = $this->container->get('templating')
                    ->render('PublicBundle:Email:mensaje_email_notificacion_pedido_solicitado_devolucion.html.twig', array(
                        'pedido' => $pedido
                    )) ;

                // Enviar también notificación del sistema

                $this->notificationService->send(
                    $pedido->getVendedor(),
                    Notificacion::NOTIFICATION_TYPE_REIMBURSEMENT_REQUEST,
                    array(
                        Notificacion::NOTIFICATION_PARAM_ORDER_CODE => $pedido->getCodigo()
                    )
                );

                break;
            case EstadoPedido::ESTADO_PEDIDO_CERRADO_SLUG:
                // Enviar email al vendedor
                $toUser = $pedido->getVendedor()->getEmail();
                $subject = '[MercadoFree] Uno de tus pedidos ha sido calificado';
                $emailTemp = $this->container->get('templating')
                    ->render('PublicBundle:Email:mensaje_email_notificacion_pedido_cerrado.html.twig', array(
                        'pedido' => $pedido
                    )) ;

                // Enviar también notificación del sistema

                $this->notificationService->send(
                    $pedido->getVendedor(),
                    Notificacion::NOTIFICATION_TYPE_QUALIFIED_ORDER,
                    array(
                        Notificacion::NOTIFICATION_PARAM_ORDER_CODE => $pedido->getCodigo()
                    )
                );

                break;
            default:
                $emailTemp = '';

        }

        if($emailTemp != ''){
            $this->utilPublicService->sendMail(
                $emailFrom,
                $toUser->getEmail(),
                $subject,
                $emailTemp
            );
        }
    }




}