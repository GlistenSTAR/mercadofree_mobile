<?php

namespace AdministracionBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AdministracionBundle\Entity\Configuracion;
use AdministracionBundle\Entity\SolicitudRetiroFondos;
use AdministracionBundle\Entity\EstadoSolicitudRetiro;

/**
 * Description of ValidarRetirosPaypalCommand
 *
 * @author Vadino
 */
class ValidarRetirosPaypalCommand extends ContainerAwareCommand {
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('administracion:solicitud-retiro:validar-retiros')
            ->setDescription('Comando para realizar la consulta de estados de pagos de retiros solicitados a paypal');
    }
    
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Validando solicitudes de retiros aprobadas');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var Configuracion $configuracion */
        $configuracion = $em->getRepository(Configuracion::class)->getDefaultConfiguracion();
        
        $solicitudesRetiro = $em->getRepository(SolicitudRetiroFondos::class)->findPagoPaypalPendientes();
        
        $estadoPagoRealizado = $em->getRepository(EstadoSolicitudRetiro::class)->findEstadoPagoRealizado();
        $estadoPagoFallido = $em->getRepository(EstadoSolicitudRetiro::class)->findEstadoPagoFallido();
        
        /** @var SolicitudRetiroFondos $solicitudRetiro */
        foreach($solicitudesRetiro as $solicitudRetiro) {
            
            $codigoRespuestaPasarela = $solicitudRetiro->getCodRespuestaPasarela();
            $datosDePago = json_decode($this->getConfirmacionPago($codigoRespuestaPasarela));
            
            if(isset($datosDePago->transaction_status)) { //Revizamos que realmente recibamos el estado de la transacción, de lo contrario significa que hubo un error con el Id del item del pago
                if($datosDePago->transaction_status == "SUCCESS") {
                    $solicitudRetiro->setEstadoSolicitudRetiro($estadoPagoRealizado);
                    
                    //Envío de mail motivo rechazo
                    $emailTemp=$this->getContainer()->get('templating')->render('AdministracionBundle:Email:mensaje_confirmacion_solicitud_retiro_fondos.html.twig', array(
                                        'solicitudRetiro' => $solicitudRetiro,
                                        'usuario' => $solicitudRetiro->getUsuario(),
                                ));

                    $this->sendMail(
                                    $solicitudRetiro->getEmailUsuario(),
                                    "[MercadoFree] Confirmación de retiro de fondos",
                                    $emailTemp
                        );
                } else {
                    if(in_array($datosDePago->transaction_status, array("FAILED", "RETURNED", "REFUNDED"))) {
                        $solicitudRetiro->setEstadoSolicitudRetiro($estadoPagoFallido);
                        $solicitudRetiro->setMensajeErrorPasarela($datosDePago->transaction_status);
                        
                        //Envío de mail motivo rechazo
                        $emailTemp=$this->getContainer()->get('templating')->render('AdministracionBundle:Email:mensaje_falla_solicitud_retiro_fondos.html.twig', array(
                                            'solicitudRetiro' => $solicitudRetiro,
                                            'usuario' => $solicitudRetiro->getUsuario(),
                                    ));

                        $this->sendMail(
                                    $configuracion->getEmailAdministrador(),
                                    "[MercadoFree] Fallo en retiro de fondos",
                                    $emailTemp
                                );
                    }
                }
            } else {
                $solicitudRetiro->setEstadoSolicitudRetiro($estadoPagoFallido);
                $solicitudRetiro->setMensajeErrorPasarela($datosDePago->name);
                
                //Envío de mail motivo rechazo
                $emailTemp=$this->getContainer()->get('templating')->render('AdministracionBundle:Email:mensaje_falla_solicitud_retiro_fondos.html.twig', array(
                                    'solicitudRetiro' => $solicitudRetiro,
                                    'usuario' => $solicitudRetiro->getUsuario(),
                            ));

                $this->sendMail($configuracion->getEmailAdministrador(),
                                "[MercadoFree] Fallo en retiro de fondos",
                                $emailTemp);
            }
            
            $em->persist($solicitudRetiro);
            $em->flush();
        }
        
        $output->writeln('Se ha actualizado el estado de las solicitudes de retiros según paypal.');
    }
    
    private function getConfirmacionPago($codigoRespuestaPasarela) {
        
        $detallesPago = $this->getContainer()->get('curl')->curlRequest($this->getContainer()->getParameter('paypal_payouts_item_url') . '/' . $codigoRespuestaPasarela, "GET", $this->getHeaderWithAccessToken());
        
        return $detallesPago;
    }
    
    private function sendMail($to, $about, $body) {
        $this->getContainer()->get('email')->sendMail(
            'noreply@mercadofree.com', //From
            $to,
            $about,
            $body
        );
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
