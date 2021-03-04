<?php

namespace AdministracionBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AdministracionBundle\Entity\SolicitudRetiroFondos;
use AdministracionBundle\Entity\EstadoSolicitudRetiro;

class EjecutarRetirosPaypalCommand extends ContainerAwareCommand{
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('administracion:solicitud-retiro:ejecutar-retiros')
            ->setDescription('Comando para realizar pagos de retiros solicitados por paypal');
    }
    
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Ejecutando solicitudes de retiros aprobadas');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        
        $solicitudesRetiro = $em->getRepository(SolicitudRetiroFondos::class)->findAprobadas();
        $estadoPagoPaypalPendiente = $em->getRepository(EstadoSolicitudRetiro::class)->findEstadoPagoPaypalPendiente();
        
        /** @var SolicitudRetiroFondos $solicitudRetiro */
        foreach($solicitudesRetiro as $solicitudRetiro) {
            $datosPagoPaypal = json_decode($this->generarPago($solicitudRetiro));
            
            $detallesPago = $this->getDetallesPago($datosPagoPaypal);
            
            $solicitudRetiro->setReferenciaPasarela($detallesPago);
            
            $decodedDetallesPago = json_decode($detallesPago);
            $itemDetallePago = reset($decodedDetallesPago->items);
            $solicitudRetiro->setCodRespuestaPasarela($itemDetallePago->payout_item_id);
            $solicitudRetiro->setEstadoSolicitudRetiro($estadoPagoPaypalPendiente);
            
            $em->persist($solicitudRetiro);
        }
        
        $em->flush();
        $output->writeln('Las solicitudes de retiros fueron enviadas para ser procesadas por paypal.');
        
    }
    
    private function generarPago(SolicitudRetiroFondos $solicitudRetiro) {
        $data = [];

        $time = time();
        //--- Prepare sender batch header
        $sender_batch_header["sender_batch_id"] = $time;
        $sender_batch_header["email_subject"]   = "Pago Recibido";
        $sender_batch_header["email_message"]   = "Usted a recibido un pago, gracias por trabajar con nosotros.";

        //--- First receiver
        $receiver["recipient_type"] = "EMAIL";
        $receiver["note"] = "Gracias por trabajar con nosotros";
        $receiver["sender_item_id"] = $time++;
        $receiver["receiver"] = $solicitudRetiro->getEmailPaypal();
        $receiver["amount"]["value"] = $solicitudRetiro->getMonto();
        $receiver["amount"]["currency"] = "USD";
        $items[] = $receiver;

        $data["sender_batch_header"] = $sender_batch_header;
        $data["items"] = $items;

        //--- Send payout request
        $payout = $this->getContainer()->get('curl')->curlRequest($this->getContainer()->getParameter('paypal_payouts_url'), "POST", $this->getHeaderWithAccessToken(), json_encode($data));
        
        return $payout;
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
    
    private function getDetallesPago($paypalBatchResponse) {
        $detalles = reset($paypalBatchResponse->links);
        $href = $detalles->href;
        
        $detallesPago = $this->getContainer()->get('curl')->curlRequest($href, "GET", $this->getHeaderWithAccessToken());
        
        return $detallesPago;
    }
    
    
}
