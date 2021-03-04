<?php
/**
 * Created by PhpStorm.
 * User: neco
 * Date: 1/28/2017
 * Time: 2:43 PM
 */

namespace AppBundle\Services;


use AdministracionBundle\Entity\Notificacion;
use AdministracionBundle\Entity\NotificacionUsuario;
use AdministracionBundle\Entity\Usuario;

class NotificacionService {

    protected $container;
    protected $em;

    public function __construct($container)
    {
        $this->container = $container;
        $this->em=$this->container->get('doctrine')->getManager();
    }

    /**
     * Envía una notificación a un usuario
     *
     * @param Usuario $toUser
     * @param $type
     * @param array $params
     * @return bool
     */
    public function send(Usuario $toUser, $type, $params = array()){
        $notificacion = $this->em->getRepository(Notificacion::class)->findOneBySlug($type);

        if(!is_null($notificacion)){
            $notificacionUsuario = new NotificacionUsuario();
            $notificacionUsuario->setNotificacion($notificacion);
            $notificacionUsuario->setUsuario($toUser);
            $notificacionUsuario->setMensaje($notificacion->getMensaje());

            if(!empty($params)){
                foreach ($params as $paramKey => $paramVal){
                    $notificacionUsuario->setMensaje(str_replace($paramKey, $paramVal, $notificacionUsuario->getMensaje()));
                }
            }

            $this->em->persist($notificacionUsuario);

            return true;
        }
        else{
            return false;
        }

    }



}