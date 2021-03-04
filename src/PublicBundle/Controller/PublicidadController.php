<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 06/04/2018
 * Time: 01:36 PM
 */

namespace PublicBundle\Controller;

use AdministracionBundle\Entity\UsuarioCampanna;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PublicidadController extends Controller
{
    public function planesAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        if(!$usuario)
        {
            return $this->redirect($this->generateUrl('public_login'));
        }

        if($request->getMethod()=='POST')
        {
            $campanna=$em->getRepository('AdministracionBundle:Campanna')->find($request->request->get('idCampanna'));

            $estadoCampanna=$em->getRepository('AdministracionBundle:EstadoCampanna')->find(1);

            $usuarioCampanna= new UsuarioCampanna();

            $usuarioCampanna->setCampannaid($campanna);

            $usuarioCampanna->setUsuarioid($usuario);

            $usuarioCampanna->setEstadoCampannaid($estadoCampanna);

            $hoy=new \DateTime();

            $usuarioCampanna->setFecha($hoy);

            $em->persist($usuarioCampanna);

            $em->flush();

            return new Response(json_encode(true));


        }

        $planes=$em->getRepository('AdministracionBundle:Campanna')->findAll();

        $campannaUser=$em->getRepository('AdministracionBundle:UsuarioCampanna')->findBy(
            array('usuarioid'=>$usuario->getId()), array());


        $uno =0;

        $dos =0;

        $tres=0;

        foreach ($campannaUser as $cu)
        {
            switch ($cu->getCampannaid()->getId())
            {
                case 1:
                    $uno=1;
                    break;
                case 2:
                    $dos=1;
                    break;
                case 3:
                    $tres=1;
                    break;

            }

        }


        return $this->render('PublicBundle:Publicidad:planesAdicionar.html.twig', array('planes'=>$planes,'uno'=>$uno,'dos'=>$dos,'tres'=>$tres));
    }

}