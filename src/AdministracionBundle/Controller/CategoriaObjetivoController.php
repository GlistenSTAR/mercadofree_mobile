<?php


namespace AdministracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriaObjetivoController extends Controller
{
    public function listarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        if($request->getMethod()=='POST')
        {
            $categoriaObjetivos=$em->getRepository('AdministracionBundle:CategoriaObjetivo')->findByCategoriaObjetivo($request)->getResult();

            $categoriaObjetivosTotal=$em->getRepository('AdministracionBundle:CategoriaObjetivo')->findByCategoriaObjetivoTotal($request)->getResult();

            $listaCategoriaObjetivos=[];

            foreach ($categoriaObjetivos as $catObjet)
            {
                $catObjetArray=[];
                $catObjetArray[]=$catObjet->getId();
                $catObjetArray[]=$catObjet->getNombre()!=null?$catObjet->getNombre():"";

                $listaCategoriaObjetivos[]=$catObjetArray;
            }

            $json_data=array(
                "draw"=>intval($request->request->get('draw')),
                "recordsTotal"=>intval(count($categoriaObjetivosTotal)),
                "recordsFiltered"=>intval(count($categoriaObjetivosTotal)),
                "data"=>$listaCategoriaObjetivos
            );

            return new Response(json_encode($json_data));

        }

        return $this->render('AdministracionBundle:CategoriaObjetivo:listado.html.twig');
    }
    public function editarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        if ($request->request->get("editarCategoriaObjetivo")=="false")
        {
            $idCategoriaObjetivo=$request->request->get("idCategoriaObjetivo");
            $categoriaObjetivo=$em->getRepository('AdministracionBundle:CategoriaObjetivo')->findBy(
                array("id"=>$idCategoriaObjetivo),
                array()
            )[0];

            $objetArray=[];
            $objetArray[]=$categoriaObjetivo->getId();
            $objetArray[]=$categoriaObjetivo->getNombre()!=null?$categoriaObjetivo->getNombre():"";


            return new Response(json_encode(array("categoriaObjetivo"=>$objetArray)));
        }
        if ($request->request->get("editarCategoriaObjetivo")=="true")
        {
            $idCategoriaObjetivo=$request->request->get("idCategoriaObjetivo");
            $categoriaObjetivo=$em->getRepository('AdministracionBundle:CategoriaObjetivo')->findBy(
                array("id"=>$idCategoriaObjetivo),
                array()
            )[0];

            $categoriaObjetivo->setNombre($request->request->get("nombreEditarCategoriaObjetivo"));

            $em->persist($categoriaObjetivo);
            $em->flush();

            return new Response(json_encode(true));
        }
    }
}