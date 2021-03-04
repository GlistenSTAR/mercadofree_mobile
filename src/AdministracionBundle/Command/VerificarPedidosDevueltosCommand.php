<?php

namespace AdministracionBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AdministracionBundle\Entity\Configuracion;
use AdministracionBundle\Entity\Pedido;
use AdministracionBundle\Repository\PedidoRepository;
use PublicBundle\Services\LiberarPagoPedidoService;

/**
 * Description of VerificarPedidosDevueltosCommand
 *
 * @author Vadino
 */
class VerificarPedidosDevueltosCommand extends ContainerAwareCommand {
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('administracion:verificar-pedidos-devueltos')
            ->setDescription('Comando para verificar pedidos devueltos que deban ser cerrados por caducidad');
    }
    
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Verificando pedidos devueltos');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        
        /** @var Configuracion $configuracion */
        $configuracion = $em->getRepository(Configuracion::class)->getDefaultConfiguracion();
        
        $limiteDiasValoracionPedido = $configuracion->getLimiteDiasValoracionPedido();
        
        if($limiteDiasValoracionPedido) {
            $fechaLimiteValoracion = (new \DateTime())->sub(new \DateInterval('P'.$limiteDiasValoracionPedido.'D'));

            /** @var PedidoRepository $pedidoRepository */
            $pedidoRepository = $em->getRepository(Pedido::class);
            $pedidosRecibidos = $pedidoRepository->findPedidosDevueltosACerrar($fechaLimiteValoracion);
            
            /** @var Pedido $pedido */
            foreach($pedidosRecibidos as $pedido) {

                if($pedido->puedeCambiarEstadoPorInactividad($fechaLimiteValoracion))
                {
                    /** @var LiberarPagoPedidoService $liberarPagoPedidoService */
                    $liberarPagoPedidoService = $this->getContainer()->get('devolver_pago_pedido_service');
                    $liberarPagoPedidoService->execute($pedido);

                    // Enviar email de notificación al vendedor
                    $this->getContainer()->get('email')->sendMail(
                        null, //From
                        $pedido->getVendedor()->getEmail(),
                        "Pedido cerrado automáticamente",
                        $this->getContainer()->get('templating')
                            ->render('@Public/Email/mensaje_email_notificacion_pedido_autocalificado_vendedor.html.twig',array(
                                'user_firstname' => $pedido->getVendedor()->getName(),
                                'pedido' => $pedido
                            ))
                    );

                    $em->persist($pedido);
                    $em->flush();
                }
            }
        }
        
        $output->writeln('Fin de la verificación de pedidos devueltos.');
    }
    
    private function getConfirmacionPago($codigoRespuestaPasarela) {
        
        $detallesPago = $this->getContainer()->get('curl')->curlRequest($this->getContainer()->getParameter('paypal_payouts_item_url') . '/' . $codigoRespuestaPasarela, "GET", $this->getHeaderWithAccessToken());
        
        return $detallesPago;
    }
    
    private function getAccessToken() {
        //--- Headers for our token request
        $headers[] = "Accept: application/json";
        $headers[] = "Content-Type: application/x-www-form-urlencoded";

        //--- Data field for our token request
        $data = "grant_type=client_credentials";

        //--- Pass client id & client secrent for authorization
        $curl_options[CURLOPT_USERPWD] = $this->getContainer()->getParameter('paypal_client_id') . ":" . $this->getContainer()->getParameter('paypal_client_secret');

        $token_request = $this->getContainer()->get('curl')->curlRequest($this->getContainer()->getParameter('paypal_token_url'), "POST", $headers, $data, $curl_options);
        $tokenRequest = json_decode($token_request);
        if(isset($token_request->error)){
            throw new \Exception("Paypal Token Error: ". $token_request->error_description);
        }
        
        return $tokenRequest;
    }
    
    private function getHeaderWithAccessToken() {
        $token_request = $this->getAccessToken();
        
        $headers = [];
        
        //--- Headers for payout request
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: Bearer $token_request->access_token";
        
        return $headers;
    }
}
