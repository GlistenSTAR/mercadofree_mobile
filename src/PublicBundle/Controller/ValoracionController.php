<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 16/04/2018
 * Time: 09:26 PM
 */

namespace PublicBundle\Controller;
use AdministracionBundle\Entity\Valoracion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ValoracionController extends Controller
{

    public function adicionarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $idProducto = $request->request->get('idProducto');

        $producto=$em->getRepository('AdministracionBundle:Producto')->find($idProducto);

        $comentarioTexto = $request->request->get('comentario');

        $puntuacion = $request->request->get('puntuacion');

        $tema = $request->request->get('tema');

        $nombre = $request->request->get('nombre');

        $usuario = $this->get('utilPublic')->getUsuarioLogueado();
        
        $validarInputContactosNoPermitidosService = $this->get('validar_input_contactos_no_permitidos_service');
        
        /** Validamos que los datos ingresados no contengan datos de contacto **/
        if( !$validarInputContactosNoPermitidosService->execute($usuario, [$comentarioTexto, $tema, $nombre]) ) {
            return new JsonResponse(['error' => true, 'mensaje' => 'Hemos detectado que ha entrado datos de contacto o enlaces externos a nuestra web, en los datos del formulario, le rogamos corrija esta información para poder publicar su valoración, ya que está incumpliendo nuestras Políticas de Uso.']);
        }
        
        $comentario=new Valoracion();

        $comentario->setAsunto($tema);

        $comentario->setOpinion($comentarioTexto) ;

        $comentario->setUsuarioid($usuario);

        $comentario->setPuntuacion($puntuacion);

        $comentario->setProductoid($producto);

        $comentario->setNombre($nombre);

        $comentario->setFecha(new \DateTime('today'));

        $em->persist($comentario);

        $em->flush();

        return new Response(json_encode(true));

    }

    public function obtenerAction(Request $request,$pag)
    {
        $em=$this->getDoctrine()->getManager();

        $start=$request->request->get('start');

        $total=$request->request->get('total');

        $valoraciones=$em->getRepository('AdministracionBundle:Valoracion')->findByValoracion($request)->getResult();

        $start+=count($valoraciones);

        $total=count($em->getRepository('AdministracionBundle:Valoracion')->findByValoracionTotal($request)->getResult());

        $listValoracion=[];

            foreach($valoraciones as $val)
            {
                $valArray=[];
                $valArray[]=$val->getOpinion();
                $valArray[]=$val->getPuntuacion();

                $listValoracion[]=$valArray;
            }

        return new Response(json_encode(array('start'=>$start, 'total'=>$total, 'valoraciones'=>$listValoracion)));


    }
}