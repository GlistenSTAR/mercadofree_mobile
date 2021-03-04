<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 09/08/2018
 * Time: 12:08 PM
 */

namespace PublicBundle\Controller;
use AdministracionBundle\Entity\CostoEnvio;
use AdministracionBundle\Repository\CostoEnvioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CostoEnvioController extends Controller
{
    public function adicionarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        // Obtener parametros

        $idProvincia=$request->request->get('provincia');

        $idCiudad=$request->request->get('ciudad');

        $costo=$request->request->get('costoEnvio');

        $esGratis=$request->request->get('costoGratis');

        $peso = $request->request->get('peso');

	    $ancho = $request->request->get('ancho');

	    $alto = $request->request->get('alto');

	    $profundidad = $request->request->get('profundidad');

        if(($costo==null || $costo == '') && !$esGratis){
            return new Response(json_encode(array(false,"Debes establecer un precio para este envío. Si es gratis, marca la casilla de envío gratuito para indicarlo")));
        }

        // TODO: Cambiar la validacion por provincia, ahora puede ser null si se han metido valores en las dimensiones y peso

	    if( $idProvincia==null && ($peso == null && $ancho == null && $alto == null && $profundidad == null) ){
		    return new Response(json_encode(array(false,"Debes seleccionar al menos una provincia para establecer un costo de envío para esa región")));
	    }


        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        $costoEnvio= new CostoEnvio();

        $vista=array();

        //foreach ($grupos as $grupo){
            $provincia=null;
            $ciudad=null;
            $itemVista=array(
                'provincia'=>"",
                'ciudad'=>"",
                'peso'=>"",
                'ancho'=>"",
                'alto'=>"",
                'profundidad'=>"",
                'costo'=>0,
                'gratis'=>false,
                'id'=>""
            );

//            $idProvincia=(int)explode(',',$grupo)[0];
//            $idCiudad=(int)explode(',',$grupo)[1];

            if($idProvincia!=null && $idProvincia!=""){
                $provincia=$em->getRepository('AdministracionBundle:Provincia')->find($idProvincia);
            }

            if($idCiudad!=null && $idCiudad!=""){
                $ciudad=$em->getRepository('AdministracionBundle:Ciudad')->find($idCiudad);
            }

            //Verificar que no exista ya una configuracion de costo de envio para esa combinacion de provincia y ciudad

            if($provincia!=null && $ciudad!=null){
                $costoEnvio=$em->getRepository('AdministracionBundle:CostoEnvio')->findOneBy(array(
                    'usuarioid'=>$usuario->getId(),
                    'provinciaid'=>$provincia->getId(),
                    'ciudadid'=>$ciudad->getId()
                ));
                $itemVista['provincia']=$provincia->getNombre();
                $itemVista['ciudad']=$ciudad->getCiudadNombre();

            }
            else if($provincia!=null){
                $costoEnvio=$em->getRepository('AdministracionBundle:CostoEnvio')->findOneBy(array(
                    'usuarioid'=>$usuario->getId(),
                    'provinciaid'=>$provincia->getId(),
                ));
                $itemVista['provincia']=$provincia->getNombre();
            }


            //Si existe una configuracion, se edita el costo, o se marca como gratuita, ademas, si se han entrado restricciones de medidas, se registran tambien.

            if($costoEnvio!=null && $costoEnvio->getId()!=null){
                if($esGratis){
                    $costoEnvio->setGratis(true);
                    $costoEnvio->setCosto(0);
                    $itemVista['gratis']=true;
                }
                else{
                    $costoEnvio->setGratis(false);
                    $costoEnvio->setCosto($costo);
                    $itemVista['costo']=$costo;
                }
            }
            else{
                //No existe esa conf, se crea nueva y se inserta
                $costoEnvio=new CostoEnvio();

                $costoEnvio->setUsuarioid($usuario);
                $costoEnvio->setProvinciaid($provincia);
                $costoEnvio->setCiudadid($ciudad);
                if($esGratis){
                    $costoEnvio->setGratis(true);
                    $costoEnvio->setCosto(0);
                    $itemVista['gratis']=true;
                }
                else{
                    $costoEnvio->setGratis(false);
                    $costoEnvio->setCosto($costo);
                    $itemVista['costo']=$costo;
                }
            }

            // Si se han entrado valores en el peso y dimensiones, tambien asignarlos

	        $costoEnvio->setPeso(($peso!=null) ? $peso : 0);
	        $costoEnvio->setAncho(($ancho!=null) ? $ancho : 0);
	        $costoEnvio->setAlto(($alto!=null) ? $alto : 0);
	        $costoEnvio->setProfundidad(($profundidad!=null) ? $profundidad : 0);

	        $itemVista['peso']=(($peso!=null) ? $peso : '');
	        $itemVista['ancho']=(($ancho!=null) ? $ancho : '');
	        $itemVista['alto']=(($alto!=null) ? $alto : '');
	        $itemVista['profundidad']=(($profundidad!=null) ? $profundidad : '');

            $em->persist($costoEnvio);

            $itemVista['id']=$costoEnvio->getId();

            $vista[]=$itemVista;
        //}
        $em->flush();

        return new Response(json_encode(array(true,$vista)));


    }

    public function eliminarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $idCosto=$request->request->get('idCosto');

        $costoenvio=$em->getRepository('AdministracionBundle:CostoEnvio')->find($idCosto);

        $em->remove($costoenvio);

        $em->flush();

        return new Response("true");
    }


}