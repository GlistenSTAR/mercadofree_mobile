<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 16/04/2018
 * Time: 06:14 PM
 */

namespace PublicBundle\Controller;
use AdministracionBundle\Entity\Notificacion;
use AdministracionBundle\Entity\Pregunta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class PreguntaController extends Controller
{
    public function adicionarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        $preguntaTexto=$request->request->get('pregunta');

        $idProducto=$request->request->get('idProducto');
        
        $validarInputContactosNoPermitidosService = $this->get('validar_input_contactos_no_permitidos_service');
        
        /** Validamos que los datos ingresados no contengan datos de contacto **/
        if( !$validarInputContactosNoPermitidosService->execute($usuario, [$preguntaTexto]) ) {
            return new JsonResponse(['error' => true, 'mensaje' => 'Hemos detectado que ha entrado datos de contacto o enlaces externos a nuestra web, en los datos del formulario, le rogamos corrija esta información para poder publicar su comentario, ya que está incumpliendo nuestras Políticas de Uso.']);
        }

        $pregunta= new Pregunta();

        if($usuario)
        {
            $pregunta->setUsuarioid($usuario);
        }

        $pregunta->setPregunta($preguntaTexto);

        $producto= $em->getRepository('AdministracionBundle:Producto')->find($idProducto);

        $pregunta->setProductoid($producto);

        $hoy=new \DateTime();

        $pregunta->setFecha($hoy);

        $em->persist($pregunta);

        // Enviar notificación al vendedor

        $this->get('notification')->send(
            $producto->getUsuarioid(),
            Notificacion::NOTIFICATION_TYPE_PRODUCT_QUESTION,
            array(
                Notificacion::NOTIFICATION_PARAM_PROD_NAME => $producto->getNombre()
            )
        );

        $em->flush();

        return new Response(json_encode(true));
    }



    public function eliminarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $pregunta= $em->getRepository('AdministracionBundle:Pregunta')->find($request->request->get('idPregunta'));

        $cantidadPreguntas=count($pregunta->getProductoid()->getPreguntas())-1;

        $idProducto=$pregunta->getProductoid()->getId();

        $em->remove($pregunta);

        $em->flush();

        return new Response(json_encode(array('cantPre'=>$cantidadPreguntas,'idProducto'=>$idProducto)));

    }

    public function obtenerAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        $producto=$em->getRepository('AdministracionBundle:Producto')->find($request->request->get('idProducto'));

        $preguntas=$producto->getPreguntas();

        $pregunta=$preguntas[count($preguntas)-1];

        $arrayPregunta=[];

        $arrayPregunta[]=$pregunta->getId();
        $arrayPregunta[]=$pregunta->getPregunta();
        $arrayPregunta[]=$pregunta->getRespuesta();

        return new Response(json_encode(array('pregunta'=>$arrayPregunta)));

    }

    public function responderAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $idPregunta=$request->request->get('idPregunta');

        $texto=$request->request->get('texto');

        $pregunta=$em->getRepository('AdministracionBundle:Pregunta')->find($idPregunta);

        $pregunta->setRespuesta($texto);

        $em->persist($pregunta);

        // Enviar notificación al usuario de que se ha respondido su pregunta

        $this->get('notification')->send(
            $pregunta->getUsuarioid(),
            Notificacion::NOTIFICATION_TYPE_PRODUCT_RESPONSE,
            array(
                Notificacion::NOTIFICATION_PARAM_PROD_NAME => $pregunta->getProductoid()->getNombre()
            )
        );

        $em->flush();

        return new Response(json_encode(true));
    }
}