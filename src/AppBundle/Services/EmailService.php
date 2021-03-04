<?php
/**
 * Created by PhpStorm.
 * User: neco
 * Date: 1/28/2017
 * Time: 2:43 PM
 */

namespace AppBundle\Services;


use AdministracionBundle\Entity\Configuracion;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class EmailService {
    protected $container;
    protected $em;
    public function __construct($container)
    {
        $this->container = $container;
        $this->em=$this->container->get('doctrine')->getManager();
    }

    public function sendMail($from, $to, $subject, $body, $attachments = array()){
        if(is_null($from)){
            $conf = $this->em->getRepository(Configuracion::class)->find(1);
            $from = $conf->getEmailAdmin();
        }

        $mail = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from, '')
            ->setTo($to)
            ->addPart($body, 'text/html');

        if(! is_null($attachments) && count($attachments) > 0){
            foreach ($attachments as $attach){
                $mail = $mail->attach(
                    \Swift_Attachment::fromPath($attach->getPathName())
                        ->setFilename($attach->getClientOriginalName())
                );
            }
        }

        try{
            $this->container->get('mailer')->send($mail);
        }
        catch(Exception $e){
            return false;
        }

        return true;
    }

}