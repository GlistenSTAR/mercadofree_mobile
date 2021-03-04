<?php


namespace AdministracionBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;

class ObjetivoController extends Controller
{
    public function listarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        if($request->getMethod()=='POST')
        {
            $objetivos=$em->getRepository('AdministracionBundle:Objetivo')->findByObjetivo($request)->getResult();

            $objetivosTotal=$em->getRepository('AdministracionBundle:Objetivo')->findByObjetivoTotal($request)->getResult();

            $listaObjetivos=[];

            foreach ($objetivos as $objet)
            {
                $objetArray=[];
                $objetArray[]=$objet->getId();
                $objetArray[]=$objet->getNombre()!=null?$objet->getNombre():"";
                $objetArray[]=$objet->getIcono()!=null?$objet->getIcono():"";
                $objetArray[]=$objet->getPuntos()!=null?$objet->getPuntos():"";
                $objetArray[]=$objet->getVisible()!=null?($objet->getVisible()==1?"Sí":"No"):"No";
                $objetArray[]=$objet->getCategoriaobjetivoid()!=null?$objet->getCategoriaobjetivoid()->getNombre():"";
                $objetArray[]=$objet->getDescripcion()!= null ?$objet->getDescripcion():"";


                $listaObjetivos[]=$objetArray;
            }


            $json_data=array(
                "draw"=>intval($request->request->get('draw')),
                "recordsTotal"=>intval(count($objetivosTotal)),
                "recordsFiltered"=>intval(count($objetivosTotal)),
                "data"=>$listaObjetivos
            );

            return new Response(json_encode($json_data));

        }

        return $this->render('AdministracionBundle:Objetivo:listado.html.twig');
    }
    public function editarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        if ($request->request->get("editarObjetivo")=="false")
        {
            $idObjetivo=$request->request->get("idObjetivo");
            $objetivo=$em->getRepository('AdministracionBundle:Objetivo')->findBy(
                array("id"=>$idObjetivo),
                array()
            )[0];

            $objetArray=[];
            $objetArray[]=$objetivo->getId();
            $objetArray[]=$objetivo->getNombre()!=null?$objetivo->getNombre():"";
            $objetArray[]=$objetivo->getIcono()!=null?$objetivo->getIcono():"";
            $objetArray[]=$objetivo->getPuntos()!=null?$objetivo->getPuntos():"";
            $objetArray[]=$objetivo->getVisible()!=null?$objetivo->getVisible():"";
            $objetArray[]=$objetivo->getCategoriaobjetivoid()!=null?$objetivo->getCategoriaobjetivoid()->getNombre():"";
            $objetArray[]=$objetivo->getDescripcion()!=null?$objetivo->getDescripcion():"";

            return new Response(json_encode(array("objetivo"=>$objetArray)));
        }
        if ($request->request->get("editarObjetivo")=="true")
        {
            $idObjetivo=$request->request->get("idObjetivoEditar");
            $objetivo=$em->getRepository('AdministracionBundle:Objetivo')->findBy(
                array("id"=>$idObjetivo),
                array()
            )[0];


            $objetivo->setNombre($request->request->get("nombreObjetivo"));
            $objetivo->setIcono($request->request->get("iconoObjetivo"));
            $objetivo->setVisible($request->request->get("visibleObjetivo"));
            $objetivo->setPuntos($request->request->get("puntosObjetivo"));
            $objetivo->setNombre($request->request->get('nombreObjetivo'));
            $objetivo->setIcono($request->request->get('iconoObjetivo'));
            $objetivo->setDescripcion($request->request->get('descripcionObjetivo'));
            $em->persist($objetivo);
            $em->flush();

            return new Response(json_encode(true));
        }
    }
}
