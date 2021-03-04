<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 05/04/2018
 * Time: 02:59 PM
 */

namespace PublicBundle\Controller;
use AdministracionBundle\Entity\Imagen;
use AdministracionBundle\Entity\Producto;
use AdministracionBundle\Services\SimpleUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImagenController extends Controller
{
    public function adicionarAction(Request $request)
    {
        $directorioImages=$this->container->getParameter('uploads.images.temp');

        $idProducto = $request->query->get('idProducto');

        if(!file_exists($directorioImages))
            mkdir($directorioImages);

        $uploader = new SimpleUploadService('uploadfile2');
        $result = $uploader->handleUpload($directorioImages);

        $producto = $this->get('doctrine.orm.entity_manager')->getRepository('AdministracionBundle:Producto')->find($idProducto);

        $imagen = $this->get('utilPublic')->procesarFotoProductoInServer($result[1], $producto);

        if (!$result[0]) {
            return new Response(json_encode(array('success' => false, 'msg' => $uploader->getErrorMsg())));
        }
        return new Response(json_encode(array('success' => true,'file'=>$imagen['url'], 'id' => $imagen['id'])));
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

    public function eliminarImagenAction(Request $request){
        $urlImagen=$request->request->get('urlImagen');
        $tipoImagen = $request->request->get('tipo');
        $em=$this->getDoctrine()->getManager();
        $msg = "";
        $success = false;
        $dirctorio = "";
        $filesystem = null;
        switch ($tipoImagen){
            case 'productos':
                $filesystem = $this->getParameter('uploads.images.productos');
                break;
            case 'temp':
                $filesystem = $this->getParameter('uploads.images.temp');
                break;
            case 'slider_home':
                $filesystem = $this->getParameter('uploads.images.slider_home');
                break;
            case 'colecciones':
                $filesystem = $this->getParameter('uploads.images.colecciones');
                break;
            case 'tiendas':
                $filesystem = $this->getParameter('uploads.images.tiendas');
                break;
            case 'usuarios':
                $filesystem = $this->getParameter('uploads.images.usuarios');
                break;
        }
        $fichero = $filesystem.$urlImagen;

            //buscar primero en los temporales

        if($urlImagen!=null && $urlImagen!=""){
            $imagen=$em->getRepository('AdministracionBundle:Imagen')->findOneByUrl($urlImagen);
            if (file_exists($fichero)) {
                //Eliminar y return
                if ($imagen != null) {

                    $producto = $imagen->getProductoid();
                    $imagenes = $producto->getImagenes();

                    if(!$imagen->getDestacada()){
                        $em->remove($imagen);
                    }else  if(count($imagenes) > 1){
                        $em->remove($imagen);
                        $imagenes[0]->setDestacada(true);
                    }
                    else{
                        $msg = "El producto solo tiene una imagen, no se puede eliminar.";

                        return new JsonResponse(array('success' => false, 'msg' => $msg));
                    }
                }

                $em->flush();
                unlink($fichero);
                $success = true;
                $msg = "La imagen ha sido eliminda satisfactoriamente.";
                //Eliminar fichero fisico

            } else {
                $msg = "La imagen que desea eliminar no existe.";
            }

        }else{
            $msg = "No se ha seleccionado ninguna imagen para eliminar.";
        }
        return new Response(json_encode(array('success' => $success, 'msg' => $msg)));
    }

}