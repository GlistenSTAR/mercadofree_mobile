<?php

namespace AdministracionBundle\Services;

use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\Capture;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\PayumBuilder;
use Payum\Core\Payum;
use AdministracionBundle\Entity\Payment;

class PaypalService
{
    private $paymentClass;
    private $container;
    private $payum;
    private $currency;
    private $url_retorno;

    public function __construct($container)
    {
        $this->paymentClass = Payment::class;
        $this->container = $container;
        $this->payum = $this->container->get('payum');
        $this->currency = $this->container->getParameter("paypal.currency");
    }

    public function pagar($params)
    {
        $captureToken = $this->prepare($params);
        $this->url_retorno = $captureToken->getTargetUrl();
        return true;
    }

    public function retirar($params)
    {
        return true;
    }

    private function prepare($params)
    {
        $gatewayName = 'paypal_express_checkout';

        /** @var \Payum\Core\Payum $payum */
        $storage = $this->payum->getStorage($this->paymentClass);
        
        $payment = $storage->create();
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode($this->currency);
        $payment->setTotalAmount(self::prepararMonto($params['monto']));
        $payment->setDescription($params['monto']);
        $payment->setClientId($params['anid']);
        $payment->setClientEmail($params['email']);
       
        $storage->update($payment);
        
        if(isset($params['return_url'])) {
            $returnUrl = $params['return_url'];
        } else {
            $returnUrl = 'public_finanzas_pagopaypaldone';
        }
        
        return $this
            ->payum
            ->getTokenFactory()
            ->createCaptureToken($gatewayName, $payment, $returnUrl);
    }

    // private function capture()
    // {
    //     /** @var \Payum\Core\Payum $payum */
    //     $token = $this->payum->getHttpRequestVerifier()->verify($_REQUEST);
    //     $gateway = $this->payum->getGateway($token->getGatewayName());

    //     /** @var \Payum\Core\GatewayInterface $gateway */
    //     if ($reply = $gateway->execute(new Capture($token), true)) {
    //         if ($reply instanceof HttpRedirect) {
    //             header("Location: ".$reply->getUrl());
    //             die();
    //         }

    //         throw new \LogicException('Unsupported reply', null, $reply);
    //     }

    //     /** @var \Payum\Core\Payum $payum */
    //     $this->payum->getHttpRequestVerifier()->invalidate($token);
    // }

    public function done($request)
    {
        $token = $this->payum->getHttpRequestVerifier()->verify($request);
        $gateway = $this->payum->getGateway($token->getGatewayName());
        
        // You can invalidate the token, so that the URL cannot be requested any more:
        // $this->get('payum')->getHttpRequestVerifier()->invalidate($token);
        
        // Once you have the token, you can get the payment entity from the storage directly. 
        // $identity = $token->getDetails();
        // $payment = $this->get('payum')->getStorage($identity->getClass())->find($identity);
        
        // Or Payum can fetch the entity for you while executing a request (preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        $payment = $status->getFirstModel();
        
        // Now you have order and payment status
        
        return array(
            'status' => $status->getValue(),
            'payment' => array(
                'total_amount' => $payment->getTotalAmount(),
                'currency_code' => $payment->getCurrencyCode(),
                'details' => $payment->getDetails(),
            ),
        );
    }

    private static function prepararMonto($monto)
    {
        return number_format($monto, 2, '', '');
    }

    public function getUrlRetorno()
    {
        return $this->url_retorno;
    }
}