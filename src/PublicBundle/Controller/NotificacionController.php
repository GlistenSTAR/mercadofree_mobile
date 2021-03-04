<?php

namespace PublicBundle\Controller;

use Proxies\__CG__\AdministracionBundle\Entity\NotificacionUsuario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificacionController extends Controller
{
    /**
     * Renderiza la vista del listado de notificaciones
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function renderListadoNotificacionesAction()
    {
        $usuario = $this->getUser();
        return $this->render('PublicBundle:Templates:listadoNotificaciones.html.twig', array(
            'notificaciones' => $usuario->getNotificacionesUsuario(),
            'noLeidas' => $usuario->getCantNotificacionesNuevas()
        ));
    }

    /**
     * Marca como leidas todas las notificaciones del usuario que aun están sin leer
     * @return JsonResponse
     */
    public function setLeidasAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $notificaciones = $em->getRepository(NotificacionUsuario::class)->findBy(array(
            'leida' => false,
            'usuario' => $user->getId()
        ));

        if(! is_null($notificaciones) ){
            foreach ($notificaciones as $notif){
                $notif->setLeida(true);
                $em->persist($notif);
            }

            $em->flush();
        }

        return new JsonResponse(array('success' => true));

    }

    public function deleteAllAction()
    {
        $notificaciones = $this->getUser()->getNotificacionesUsuario();
        $em = $this->getDoctrine()->getManager();

        foreach ($notificaciones as $notif){
            $em->remove($notif);
        }

        $em->flush();

        return new JsonResponse(array('success' => true));
    }
}
