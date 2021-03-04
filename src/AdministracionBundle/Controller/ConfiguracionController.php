<?php


namespace AdministracionBundle\Controller;

use AdministracionBundle\Entity\Coleccion;
use AdministracionBundle\Entity\Configuracion;
use AdministracionBundle\Entity\Contacto;
use AdministracionBundle\Entity\Imagen;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;


class ConfiguracionController extends Controller
{
    public function configuracionAction()
    {
        $em = $this->getDoctrine()->getManager();
        $configuracion = $em->getRepository('AdministracionBundle:Configuracion')->find(1);
        return $this->render('AdministracionBundle:Configuracion:configuracion.html.twig', array('configuracion'=>$configuracion));
    }

    public function guardarAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $configuracion = $em->getRepository('AdministracionBundle:Configuracion')->find(1);

        if ($configuracion==null)
        {
            $configuracion= new Configuracion();
        }

        $configuracion->setEmailAdministrador($request->request->get('email_admin'));
        $configuracion->setTiempoExpiracion($request->request->get('tiempo_expiracion'));
        $configuracion->setIndicePopularidadCat($request->request->get('indice_popularidad_categorias'));
        $configuracion->setCantidadMinimaProductos($request->request->get('cantidad_minima_productos'));
        $configuracion->setMostrarPaquetesPublicidad($request->request->get('mostrarPlanPublicidad'));
        $configuracion->setMaximoIncidenciasContacto($request->request->get('maximo_incidencias_contacto'));
        $configuracion->setAprobarAutomaticamenteRetiros($request->request->get('aprobar_automaticamente_retiros'));
        $configuracion->setLimiteDiasValoracionPedido($request->request->get('dias_valoracion_pedido'));

        $contacto=$configuracion->getContactoConfiguracionId();
        if ($contacto==null)
        {
            $contacto= new Contacto();
        }
        $contacto->setEmail($request->request->get('email_contacto'));
        $contacto->setTelefono($request->request->get('telefono_contacto'));
        $contacto->setFacebook($request->request->get('facebook'));
        $contacto->setInstagram($request->request->get('instagram'));

        $em->persist($contacto);

        $configuracion->setContactoConfiguracionId($contacto);

        //Salvando los datos de la garantia de MercadoFree

        $mostrarGarantia=$request->request->get('mostrarGarantia');

        if($mostrarGarantia!=null && $mostrarGarantia=='1'){
            $configuracion->setMostrarGarantia(true);
        }
        else{
            $configuracion->setMostrarGarantia(false);
        }

        $configuracion->setTextoGarantia($request->request->get('textoGarantia'));

        $em->persist($configuracion);

        $em->flush();

        return new Response(json_encode(true));

    }

    public function configuracionHomeAction(Request $request){

        $em=$this->getDoctrine()->getManager();

        $configuracion=$em->getRepository('AdministracionBundle:Configuracion')->findAll()[0];

        $imagenes=$em->getRepository('AdministracionBundle:Imagen')->findBySliderHome(true);

        if($request->getMethod()=='POST'){
            $opciones_home=$request->request->get('secciones_home');

            if(count($opciones_home)>0 && in_array('ofertas_semana',$opciones_home)){
                $configuracion->setOfertasSemana(true);
            }
            else{
                $configuracion->setOfertasSemana(false);
            }

            if(count($opciones_home)>0 && in_array('publicidad_oferta',$opciones_home)){
                $configuracion->setPublicidadOferta(true);
                $cantOfertasSemana=$request->request->get('cantOfertasSemana');
                $cantOfertasSemana==0?$configuracion->setCantOfertasSemana(5):$configuracion->setCantOfertasSemana($cantOfertasSemana);
            }
            else{
                $configuracion->setPublicidadOferta(false);
            }

            if(count($opciones_home)>0 && in_array('publicidad_producto',$opciones_home)){
                $configuracion->setPublicidadProducto(true);
            }
            else{
                $configuracion->setPublicidadProducto(false);
            }

            if(count($opciones_home)>0 && in_array('historial_visitas_categoria',$opciones_home)){
                $configuracion->setHistorialVisitasCategoria(true);
            }
            else{
                $configuracion->setHistorialVisitasCategoria(false);
            }

            if(count($opciones_home)>0 && in_array('historial_categorias_favoritos',$opciones_home)){
                $configuracion->setHistorialUltimasCategorias(true);
            }
            else{
                $configuracion->setHistorialUltimasCategorias(false);
            }

            /*if(count($opciones_home)>0 && in_array('mostrar_historial',$opciones_home)){
                $configuracion->setMostrarHistorial(true);
            }
            else{
                $configuracion->setMostrarHistorial(false);
            }*/

            if(count($opciones_home)>0 && in_array('colecciones',$opciones_home)){
                $configuracion->setColecciones(true);
            }
            else{
                $configuracion->setColecciones(false);
            }

            //Adicionar imagenes

            if($request->request->get('imagenes')!=null && count($request->request->get('imagenes'))>0){
                foreach($request->request->get('imagenes') as $imagen){

                    //validar que no exista ya esa imagen para no crear duplicados
                    $esta=false;
                    if($imagenes!=null && count($imagenes)>0){
                        foreach($imagenes as $im){
                            if($im->getUrl()==$imagen){
                                $esta=true;
                                break;
                            }
                        }
                    }

                    if(!$esta){
                        //crear el obj imagen

                        $img=new Imagen();

                        $img->setUrl($imagen);
                        $img->setSliderHome(true);

                        $em->persist($img);

                        if(file_exists($this->getParameter('uploads.images.temp').$imagen)){
                            rename($this->getParameter('uploads.images.temp').$imagen, $this->getParameter('uploads.images.slider_home').$imagen);
                        }
                    }
                }
            }

            $em->persist($configuracion);
            $em->flush();

            return new Response(json_encode(true));

        }

        return $this->render('AdministracionBundle:Configuracion:configuracion_home.html.twig',array(
            'configuracion'=>$configuracion,
            'imagenes'=>$imagenes
        ));
    }


}
