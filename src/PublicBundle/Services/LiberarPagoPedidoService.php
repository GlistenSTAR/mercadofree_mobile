<?php

namespace PublicBundle\Services;

use AdministracionBundle\Entity\Notificacion;
use AppBundle\Services\NotificacionService;
use Doctrine\ORM\EntityManager;
use AppBundle\Services\EmailService;
use AdministracionBundle\Services\RegistroMovimientoService;

use AdministracionBundle\Entity\EstadoPedido;
use AdministracionBundle\Entity\Pedido;

/**
 * Description of LiberarPagoPedidoService
 *
 * @author Vadino
 */
class LiberarPagoPedidoService {
    
    private $em;
    private $emailService;
    private $templatingService;
    private $registroMovimientoService;
    private $pedidoService;
    private $notificationService;
    
    public function __construct(
                EntityManager $em,
                EmailService $emailService,
                $templatingService,
                RegistroMovimientoService $registroMovimientoService,
                PublicPedidoService $pedidoService,
                NotificacionService $notificationService
            ) {
        $this->em = $em;
        $this->emailService = $emailService;
        $this->templatingService = $templatingService;
        $this->registroMovimientoService = $registroMovimientoService;
        $this->pedidoService = $pedidoService;
        $this->notificationService = $notificationService;
    }
    
    public function execute(Pedido $pedido, $modificadoPorAdmin = false) {
        
        $em = $this->em;
            
        $vendedor = $pedido->getVendedor();

        $movimiento = $this->registroMovimientoService->registrarCobroPedidoMercadofree($vendedor, $pedido);

        $estadoFinalizado = $em->getRepository(EstadoPedido::class)->findOneBySlug(EstadoPedido::ESTADO_PEDIDO_CERRADO_SLUG);

        // Envio de notificacion al vendedor de pedido calificado

        $this->pedidoService->stateChangedNotification($estadoFinalizado, $pedido);

        // Envio de notificación al vendedor con el crédito en su cuenta.

        $this->notificationService->send(
            $vendedor,
            Notificacion::NOTIFICATION_TYPE_NEW_ACCOUNT_CREDIT,
            array(
                Notificacion::NOTIFICATION_PARAM_CREDIT_AMOUNT => $movimiento->getMonto()
            )
        );

        $pedido->setEstado($estadoFinalizado, $modificadoPorAdmin);
        $em->persist($pedido);
        $em->flush();
    }
}
