<?php

namespace PublicBundle\Services;

use Doctrine\ORM\EntityManager;
use AppBundle\Services\EmailService;
use AdministracionBundle\Services\RegistroMovimientoService;

use AdministracionBundle\Entity\EstadoPedido;
use AdministracionBundle\Entity\Pedido;

/**
 * Description of DevolverPagoPedidoService
 *
 * @author Vadino
 */
class DevolverPagoPedidoService {
    
    private $em;
    private $emailService;
    private $templatingService;
    private $registroMovimientoService;
    
    public function __construct(
                EntityManager $em,
                EmailService $emailService,
                $templatingService,
                RegistroMovimientoService $registroMovimientoService
            ) {
        $this->em = $em;
        $this->emailService = $emailService;
        $this->templatingService = $templatingService;
        $this->registroMovimientoService = $registroMovimientoService;
    }
    
    public function execute(Pedido $pedido, $modificadoPorAdmin = false) {
        
        $em = $this->em;
            
        $vendedor = $pedido->getVendedor();

        $this->registroMovimientoService->registrarDevolucionPedidoMercadofree($pedido);

        $estadoFinalizado = $em->getRepository(EstadoPedido::class)->findOneBySlug(EstadoPedido::ESTADO_PEDIDO_CERRADO_SLUG);

        $pedido->setEstado($estadoFinalizado, $modificadoPorAdmin);

        //Envío de mail pedido cerrado
        $emailTemp=$this->templatingService->render('PublicBundle:Email:mensaje_email_notificacion_pedido_devolucion.html.twig', array(
                                    'pedido' => $pedido
                            ));

        $this->emailService->sendMail(
            'noreply@mercadofree.com',//From
            $pedido->getUsuario()->getEmail(), //To
            "[MercadoFree] Devolución de pedido " . $pedido->getCodigo(), //Asunto
            $emailTemp //Body
        );
        
        $em->persist($pedido);
        $em->flush();
    }
}
