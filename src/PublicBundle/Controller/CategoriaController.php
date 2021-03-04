<?php


namespace PublicBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriaController extends Controller
{

   public function obtenerAction(Request $request)
   {
       $em = $this->getDoctrine()->getManager();

       $nivel=$request->request->get('nivel');

       $categorias = $em->getRepository('AdministracionBundle:Categoria')->findBy(
           array("nivel" => $nivel),
           array()
       );

       $listaCategoria = [];
       foreach ($categorias as $categoria)
       {
           $categoriasArray = [];
           $categoriasArray[] = $categoria->getId();
           $categoriasArray[] = $categoria->getNombre();
           $categoriasArray[] = $categoria->getNivel();

           $listaCategoriaHija=[];

           $categoriasHijas=$categoria->getCategoriaHijas();
           foreach ($categoriasHijas as $categoria)
           {
               $categoriasHijaArray = [];
               $categoriasHijaArray[] = $categoria->getId();
               $categoriasHijaArray[] = $categoria->getNombre();

               $listaCategoriaHija[]=$categoriasHijaArray;
           }

           $categoriasArray[]=$listaCategoriaHija;

           $listaCategoria[] = $categoriasArray;

       }

       return new Response(json_encode(array('categorias'=>$listaCategoria)));
   }

    public function tenerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $idCategoria=$request->request->get('idCategoria');

        $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($idCategoria);


        $categoriasArray = [];
        $categoriasArray[] = $categoria->getId();
        $categoriasArray[] = $categoria->getNombre();
        $categoriasArray[] = $categoria->getNivel();

        $listaCategoriaHija=[];

        $categoriasHijas=$categoria->getCategoriaHijas();
        foreach ($categoriasHijas as $categoria)
        {
            $categoriasHijaArray = [];
            $categoriasHijaArray[] = $categoria->getId();
            $categoriasHijaArray[] = $categoria->getNombre();
            $categoriasHijaArray[]=$categoria->getNivel();

            $listaCategoriaHija[]=$categoriasHijaArray;
        }

        $categoriasArray[]=$listaCategoriaHija;



        return new Response(json_encode(array('categorias'=>$categoriasArray)));
    }
}