<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 06/04/2018
 * Time: 10:48 AM
 */

namespace PublicBundle\Controller;

use AdministracionBundle\Entity\Direccion;
use AdministracionBundle\Entity\UsuarioObjetivo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DireccionController extends Controller
{
    public function adicionarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        foreach ($usuario->getDireccion() as $direc)
        {
            $valor=$direc->getDireccionVenta();
            if ($valor==1)
            {
                $em->remove($direc);

                $em->flush();
            }
        }

        $direccion= new Direccion();

        $direccion->setCalle($request->request->get('calle'));

        $direccion->setUsuarioid($usuario);

        $direccion->setOtrosDatos($request->request->get('datosAdicionales'));

        $ciudad=$em->getRepository('AdministracionBundle:Ciudad')->find($request->request->get('ciudad'));

        $direccion->setCiudad($ciudad);

        $direccion->setCodigoPostal($ciudad->getCodigoPostal());

        $provincia=$em->getRepository('AdministracionBundle:Provincia')->find($request->request->get('provincia'));

        $direccion->setProvincia($provincia);

        $direccion->setDireccionVenta(1);

        $direccion->setEntreCalle($request->request->get('entreCalles'));

        $direccion->setNumero($request->request->get('numero'));

        $direccion->setEntreCalle($request->request->get('entreCalles'));

        $em->persist($direccion);

        $em->flush();

        if($usuario->getDireccionVenta())
        {
            $usuarioObjetivo=$em->getRepository('AdministracionBundle:UsuarioObjetivo')->findBy(array('objetivoid'=>3));

            if(count($usuarioObjetivo)==0)
            {
                $hoy=new \DateTime();

                $objetivoUsuario= new UsuarioObjetivo();

                $objetivoUsuario->setUsuarioid($usuario);

                $objetivo=$em->getRepository('AdministracionBundle:Objetivo')->findBy(array('slug'=>'completar_direccion_de_compra'), array())[0];

                $objetivoUsuario->setObjetivoid($objetivo);

                $objetivoUsuario->setPuntos($objetivo->getPuntos());

                $objetivoUsuario->setFecha($hoy);

                $em->persist($objetivoUsuario);

                $em->flush();

                $usuario->setPuntos($usuario->getPuntos()+$objetivo->getPuntos());
            }
        }

        if ($usuarioVacio=!$this->get('utilPublic')->UsuarioVacio($usuario))
        {
            $usuarioObjetivo=$em->getRepository('AdministracionBundle:UsuarioObjetivo')->findBy(array('objetivoid'=>2));

            if(count($usuarioObjetivo)==0)
            {
                $hoy=new \DateTime();

                $objetivoUsuario= new UsuarioObjetivo();

                $objetivoUsuario->setUsuarioid($usuario);

                $objetivo=$em->getRepository('AdministracionBundle:Objetivo')->findBy(array('slug'=>'completar_datos_personales'), array())[0];

                $objetivoUsuario->setObjetivoid($objetivo);

                $objetivoUsuario->setPuntos($objetivo->getPuntos());

                $objetivoUsuario->setFecha($hoy);

                $em->persist($objetivoUsuario);

                $em->flush();

                $usuario->setPuntos($usuario->getPuntos()+$objetivo->getPuntos());
            }

        }

        $id=$direccion->getId();

        return new Response(json_encode(array('id'=>$id)));
    }

    public function eliminarAction(Request $request)
    {

        $em=$this->getDoctrine()->getManager();

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        $idDireccion=$request->request->get('idDireccion');

        $direccion=$em->getRepository('AdministracionBundle:Direccion')->find($idDireccion);

        $em->remove($direccion);

        $em->flush();

        return new Response(json_encode(true));


    }

    public function modificarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        $idDireccion=$request->request->get('idDireccion');

        $direccion=$em->getRepository('AdministracionBundle:Direccion')->find($idDireccion);

        $direccion->setCalle($request->request->get('calle'));

        $direccion->setOtrosDatos($request->request->get('datosAdicionales'));

        $ciudad=$em->getRepository('AdministracionBundle:Ciudad')->find($request->request->get('ciudad'));

        $direccion->setCiudad($ciudad);

        $direccion->setCodigoPostal($ciudad->getCodigoPostal());

        $provincia=$em->getRepository('AdministracionBundle:Provincia')->find($request->request->get('provincia'));

        $direccion->setProvincia($provincia);

        if($request->request->get('venta')=="on")
        {
            if($direccion->getDireccionVenta()!=1)
            {
                $direccionVenta=$em->getRepository('AdministracionBundle:Direccion')->findBy(array('usuarioid'=>$usuario->getId(),'direccionVenta'=>1));

                if (count($direccionVenta)>0)
                {
                    $em->remove($direccionVenta[0]);

                    $em->flush();
                }

                $direccion->setDireccionVenta(1);
            }
        }
        else
        {
            $direccion->setDireccionVenta(0);
        }

        $direccion->setEntreCalle($request->request->get('entreCalles'));

        $direccion->setNumero($request->request->get('numero'));

        $direccion->setEntreCalle($request->request->get('entreCalles'));

        $em->persist($direccion);

        $em->flush();

        if($usuario->getDireccionVenta())
        {
            $usuarioObjetivo=$em->getRepository('AdministracionBundle:UsuarioObjetivo')->findBy(array('objetivoid'=>3));

            if(count($usuarioObjetivo)==0)
            {
                $hoy=new \DateTime();

                $objetivoUsuario= new UsuarioObjetivo();

                $objetivoUsuario->setUsuarioid($usuario);

                $objetivo=$em->getRepository('AdministracionBundle:Objetivo')->findBy(array('slug'=>'completar_direccion_de_compra'), array())[0];

                $objetivoUsuario->setObjetivoid($objetivo);

                $objetivoUsuario->setPuntos($objetivo->getPuntos());

                $objetivoUsuario->setFecha($hoy);

                $em->persist($objetivoUsuario);

                $em->flush();

                $usuario->setPuntos($usuario->getPuntos()+$objetivo->getPuntos());
            }
        }

        $id=$direccion->getId();

        return new Response(json_encode(array('id'=>$id)));




    }
    public function panelusuarioadicionarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();


       if ($request->request->get('venta')=='on')
       {
           foreach ($usuario->getDireccion() as $direc)
           {
               $valor=$direc->getDireccionVenta();
               if ($valor==1)
               {
                   $em->remove($direc);

                   $em->flush();
               }
           }
       }

        $direccion= new Direccion();

        $direccion->setCalle($request->request->get('calle'));

        $direccion->setUsuarioid($usuario);

        $direccion->setOtrosDatos($request->request->get('datosAdicionales'));

        $ciudad=$em->getRepository('AdministracionBundle:Ciudad')->find($request->request->get('ciudad'));

        $direccion->setCiudad($ciudad);

        $direccion->setCodigoPostal($ciudad->getCodigoPostal());

        $provincia=$em->getRepository('AdministracionBundle:Provincia')->find($request->request->get('provincia'));

        $direccion->setProvincia($provincia);

        if ($request->request->get('venta')=='on')
        {
            $direccion->setDireccionVenta(true);
        }
        else{
        	$direccion->setDireccionCompra(true);
        }

        $direccion->setEntreCalle($request->request->get('entreCalles'));

        $direccion->setNumero($request->request->get('numero'));

        $direccion->setEntreCalle($request->request->get('entreCalles'));

        $em->persist($direccion);

        $em->flush();

        $id=$direccion->getId();

        if($usuario->getDireccionVenta())
        {
            $usuarioObjetivo=$em->getRepository('AdministracionBundle:UsuarioObjetivo')->findBy(array('objetivoid'=>3));

            if(count($usuarioObjetivo)==0)
            {
                $hoy=new \DateTime();

                $objetivoUsuario= new UsuarioObjetivo();

                $objetivoUsuario->setUsuarioid($usuario);

                $objetivo=$em->getRepository('AdministracionBundle:Objetivo')->findBy(array('slug'=>'completar_direccion_de_compra'), array())[0];

                $objetivoUsuario->setObjetivoid($objetivo);

                $objetivoUsuario->setPuntos($objetivo->getPuntos());

                $objetivoUsuario->setFecha($hoy);

                $em->persist($objetivoUsuario);

                $em->flush();

                $usuario->setPuntos($usuario->getPuntos()+$objetivo->getPuntos());
            }
        }

        if ($usuarioVacio=!$this->get('utilPublic')->UsuarioVacio($usuario))
        {
            $usuarioObjetivo=$em->getRepository('AdministracionBundle:UsuarioObjetivo')->findBy(array('objetivoid'=>2));

            if(count($usuarioObjetivo)==0)
            {
                $hoy=new \DateTime();

                $objetivoUsuario= new UsuarioObjetivo();

                $objetivoUsuario->setUsuarioid($usuario);

                $objetivo=$em->getRepository('AdministracionBundle:Objetivo')->findBy(array('slug'=>'completar_datos_personales'), array())[0];

                $objetivoUsuario->setObjetivoid($objetivo);

                $objetivoUsuario->setPuntos($objetivo->getPuntos());

                $objetivoUsuario->setFecha($hoy);

                $em->persist($objetivoUsuario);

                $em->flush();

                $usuario->setPuntos($usuario->getPuntos()+$objetivo->getPuntos());
            }

        }

        return new Response(json_encode(array('id'=>$id)));
    }

    public function obtenerAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        $direccion=$em->getRepository('AdministracionBundle:Direccion')->findBy(array('usuarioid'=>$usuario->getId(), 'direccionVenta'=>1))[0];

        $direccion=$direccion->getCalle()." No. ".$direccion->getNumero()." , entre ".$direccion->getEntreCalle().". ".$direccion->getProvincia()->getNombre().", ".$direccion->getCiudad()->getCiudadNombre().".";

        return new Response(json_encode(array('direccion'=>$direccion)));
    }

    public function obteneridAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        $idDireccion=$request->request->get('idDireccion');

        $direccion=$em->getRepository('AdministracionBundle:Direccion')->find($idDireccion);

        $arrayDireccion=[];

        $arrayDireccion[]=$direccion->getCalle();
        $arrayDireccion[]=$direccion->getCiudad()->getId();
        $arrayDireccion[]=$direccion->getProvincia()->getId();
        $arrayDireccion[]=$direccion->getNumero();
        $arrayDireccion[]=$direccion->getEntreCalle();
        $arrayDireccion[]=$direccion->getOtrosDatos();
        $arrayDireccion[]=$direccion->getDireccionVenta();

        $listaCiudades=[];

        $ciudades=$em->getRepository('AdministracionBundle:Ciudad')->findBy(array('provinciaid'=>$arrayDireccion[2]));

        foreach ($ciudades as $c)
        {
            $arrayCiudad=[];
            $arrayCiudad[]=$c->getId();
            $arrayCiudad[]=$c->getCiudadNombre();

            $listaCiudades[]=$arrayCiudad;
        }

        return new Response(json_encode(array('direccion'=>$arrayDireccion, 'ciudades'=>$listaCiudades)));

    }

}
