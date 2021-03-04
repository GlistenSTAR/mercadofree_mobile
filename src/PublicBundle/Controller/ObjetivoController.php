<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 21/07/2018
 * Time: 04:43 PM
 */

namespace PublicBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ObjetivoController extends Controller
{

    public function barsideAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();
        $objetivos=$em->getRepository('AdministracionBundle:Objetivo')->findBy(array('visible' => 1));

        $puntosTotal=0;

        foreach ($objetivos as $item)
        {
            $puntosTotal+=$item->getPuntos();
        }

        $objetivosUsuario=$em->getRepository('AdministracionBundle:UsuarioObjetivo')->findBy(array('usuarioid'=>$usuario->getId()));

        $puntosUsuario=0;

        foreach ($objetivosUsuario as $item)
        {
            $puntosUsuario+=$item->getPuntos();
        }

        $porcientoUsuario=(int)(($puntosUsuario*100)/$puntosTotal);

        $nivel=0;

        if($porcientoUsuario<=20)
        {
            $nivel=1;
        }
        elseif($porcientoUsuario<=40)
        {
            $nivel=2;
        }
        elseif($porcientoUsuario<=60)
        {
            $nivel=3;
        }
        elseif($porcientoUsuario<=80)
        {
            $nivel=4;
        }
        else
        {
            $nivel=5;
        }

        $puntosNivel=$puntosTotal/5;
        $puntosNivelD=$puntosUsuario%$puntosNivel;
        $porcientoNivel=(int)(($puntosNivelD*100)/$puntosNivel);

        $categoriaObjetivo=$em->getRepository('AdministracionBundle:CategoriaObjetivo')->findAll();

        $porcientoCategorias=[];


        foreach ($categoriaObjetivo as $item)
        {
            $where="where cat.id =".$item->getId();

            $sql="select 

                  objetivo
                  
              from 
              
              AdministracionBundle:Objetivo objetivo 
              INNER JOIN objetivo.categoriaobjetivoid cat
              
             
              ".$where."   
              
             "
            ;

            $objetivos2=$em->createQuery($sql)->getResult();

            $where2="where cat.id =".$item->getId()." and user.id=".$usuario->getId();

            $sql2="select 

                  userobjetivo
                  
              from 
              
              AdministracionBundle:UsuarioObjetivo userobjetivo 
              INNER JOIN userobjetivo.objetivoid objetivo
              INNER JOIN userobjetivo.usuarioid user
              INNER JOIN objetivo.categoriaobjetivoid cat
              
             
              ".$where2."   
              
             "
            ;

            $objetivos3=$em->createQuery($sql2)->getResult();

            $porCat=(int)((count($objetivos3)*100)/count($objetivos2));

            $porcientoCategorias[]=$porCat;

        }

        return $this->render(
            "PublicBundle:Objetivo:panelUsuarioObjetivo.html.twig",
            array(
                'nivel'               => $nivel,
                'porcientoNivel'      => $porcientoNivel,
                'categoriaObjetivo'   => $categoriaObjetivo,
                'porcientoCategorias' => $porcientoCategorias,
                'porcientoUsuario'    => $porcientoUsuario,
                'saldo'               => $usuario->getSaldo(),
            )
        );
    }
}