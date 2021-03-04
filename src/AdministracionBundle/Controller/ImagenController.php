<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 01/03/2018
 * Time: 09:34 PM
 */

namespace AdministracionBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use AdministracionBundle\Services\SimpleUploadService;

class ImagenController extends Controller
{
    public function adicionarAction(Request $request)
    {
        $directorioImages=$this->container->getParameter('uploads.images.temp');


        if(!file_exists($directorioImages))
            mkdir($directorioImages);

        $uploader = new SimpleUploadService('uploadfile2');
        $result = $uploader->handleUpload($directorioImages);



        if (!$result[0]) {
            return new Response(json_encode(array('success' => false, 'msg' => $uploader->getErrorMsg())));
        }

        return new Response(json_encode(array('success' => true,'file'=>$result[1])));
    }

    public function eliminarAction(Request $request){
        $urlImagen=$request->request->get('urlImagen');

        $em=$this->getDoctrine()->getManager();

        if($urlImagen!=null && $urlImagen!=""){
            //buscar primero en los temporales

            if(file_exists($this->getParameter('uploads.images.temp').$urlImagen)){
                //Eliminar y return

                unlink($this->getParameter('uploads.images.temp').$urlImagen);

                return new Response(json_encode(array(true)));
            }

            if(file_exists($this->getParameter('uploads.images.productos').$urlImagen)){
                //Eliminar y return
                $imagen=$em->getRepository('AdministracionBundle:Imagen')->findOneByUrl($urlImagen);

                if($imagen!=null){
                    $em->remove($imagen);
                }

                $em->flush();
                unlink($this->getParameter('uploads.images.productos').$urlImagen);

                return new Response(json_encode(array(true)));
            }

            //Buscar en la carpeta de imagenes del slider home
            else if(file_exists($this->getParameter('uploads.images.slider_home').$urlImagen)){
                //eliminamos primero de la BD

                $imagen=$em->getRepository('AdministracionBundle:Imagen')->findOneByUrl($urlImagen);

                if($imagen!=null){
                    $em->remove($imagen);
                }

                $em->flush();

                //Eliminar fichero fisico

                unlink($this->getParameter('uploads.images.slider_home').$urlImagen);

                return new Response(json_encode(array(true)));
            }
        }
    }


}