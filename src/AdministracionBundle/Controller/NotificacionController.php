<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 07/03/2018
 * Time: 10:05 PM
 */

namespace AdministracionBundle\Controller;
use AdministracionBundle\Entity\Notificacion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Tests\Writer\NonBackupDumper;


class NotificacionController extends Controller
{
    public function listarAction(Request $request)
    {

        $em=$this->getDoctrine()->getManager();
        if($request->getMethod()=='POST')
        {
            $notificaciones=$em->getRepository('AdministracionBundle:Notificacion')->findByNotificacion($request)->getResult();

            $notificacionesTotal=$em->getRepository('AdministracionBundle:Notificacion')->findByNotificacionTotal($request)->getResult();

            $listaNotificaciones=[];

            foreach ($notificaciones as $note)
            {
                $noteArray=[];
                $noteArray[]=$note->getId();
                $noteArray[]=$note->getIcono();
                $noteArray[]=$note->getTitulo();
                $noteArray[]=$note->getMensaje();
                $noteArray[]=($note->isEnabled()) ? 'SI' : 'NO';


                $listaNotificaciones[]=$noteArray;
            }

            $json_data=array(
                "draw"=>intval($request->request->get('draw')),
                "recordsTotal"=>intval(count($notificacionesTotal)),
                "recordsFiltered"=>intval(count($notificacionesTotal)),
                "data"=>$listaNotificaciones
            );

            return new Response(json_encode($json_data));

        }
        return $this->render('AdministracionBundle:Notificacion:listado.html.twig');

    }

    public function adicionarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $modoAdicionar=$request->request->get('modoAdicionar');

        if ($modoAdicionar=="1")
        {
           $icono=$request->request->get('iconoNotificacion');

            $nombre=$request->request->get('nombreNotificacion');

            $notificacion= new Notificacion();

            $notificacion->setIcono($icono);

            $notificacion->setNombre($nombre);

            $em->persist($notificacion);

            $em->flush();

            return new Response(json_encode(true));
        }
        return $this->render('AdministracionBundle:Notificacion:adicionar.html.twig');

    }

    public function eliminarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $idNotificion=$request->request->get("idNotificacionEliminar");
        $idNotificion=explode(':',$idNotificion);
        for ($i=1;$i<count($idNotificion);$i++)
        {
            $notificacion = $em->getRepository('AdministracionBundle:Notificacion')->findBy(
                array("id" => $idNotificion[$i]),
                array()
            )[0];
            $em->remove($notificacion);
        }
        $em->flush();
        return new Response(json_encode(true));
    }

    public function editarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $idNotificacion=$request->request->get('idNotificacion');

        $notificacion=$em->getRepository('AdministracionBundle:Notificacion')->find($idNotificacion);

        $editarNotificacion=$request->request->get("editarNotificacion");

        if($editarNotificacion=="false")
        {
            $noteArray=[];
            $noteArray['id']=$notificacion->getId();
            $noteArray['titulo']= $notificacion->getTitulo();
            $noteArray['icono']=$notificacion->getIcono();
            $noteArray['mensaje']=$notificacion->getMensaje();
            $noteArray['enabled']=$notificacion->isEnabled();

            return new Response(json_encode(array('notificacion'=>$noteArray)));
        }
        if($editarNotificacion=="true")
        {
            $notificacion->setTitulo($request->request->get("nombreNotificacionEditar"));
            $notificacion->setIcono($request->request->get("iconoNotificacionEditar"));
            $notificacion->setMensaje($request->request->get("mensajeNotificacionEditar"));

            $em->persist($notificacion);

            $em->flush();

            return new Response(json_encode(true));
        }
        return new Response(json_encode(true));
    }

}