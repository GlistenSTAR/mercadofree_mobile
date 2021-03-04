<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 21/03/2018
 * Time: 11:15 AM
 */

namespace AdministracionBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CiudadController extends Controller
{
      public function obtenerCiudadesAction(Request $request)
      {
          $em=$this->getDoctrine()->getManager();

          $idProvincia=$request->request->get('idProvincia');

          $ciudades=$em->getRepository('AdministracionBundle:Ciudad')->findBy(
              array('provinciaid'=>$idProvincia),
              array()
          );

          $ciudadesArray=[];

          foreach ($ciudades as $ciudad)
          {
              $ciudadArray=[];
              $ciudadArray[]=$ciudad->getId();
              $ciudadArray[]=$ciudad->getCiudadNombre();

              $ciudadesArray[]=$ciudadArray;
          }

          return new Response(json_encode(array('ciudades'=>$ciudadesArray)));
      }
}