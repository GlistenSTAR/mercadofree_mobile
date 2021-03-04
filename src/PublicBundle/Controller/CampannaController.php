<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 14/05/2018
 * Time: 03:32 PM
 */

namespace PublicBundle\Controller;



use AdministracionBundle\Entity\UsuarioCampanna;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CampannaController extends Controller
{


    public function modificarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $campannaid=$request->request->get('campannaid');

        $usuarioid=$request->request->get('usuarioid');

        $campanna=$em->getRepository('AdministracionBundle:UsuarioCampanna')->findBy(array('campannaid'=>$campannaid,'usuarioid'=>$usuarioid),array());

        $campanna=$campanna[0];

        $flag=0;

        if($campanna->getEstadoCampannaid()->getSlug()=='publicado')
        {
            $estado=$em->getRepository('AdministracionBundle:EstadoCampanna')->findBy(array('slug'=>'pausado'),array());

            $campanna->setEstadoCampannaid($estado[0]);

            $query='UPDATE AdministracionBundle:Producto producto SET producto.ranking = 0 WHERE producto.campannaid = '.$campannaid.' and producto.usuarioid = '.$usuarioid;

            $em->createQuery($query)->getResult();

            $flag=2;
        }
        else
        {
            $estado=$em->getRepository('AdministracionBundle:EstadoCampanna')->findBy(array('slug'=>'publicado'),array());

            $campanna->setEstadoCampannaid($estado[0]);

            $query='UPDATE AdministracionBundle:Producto producto SET producto.ranking = '.$campannaid.' WHERE producto.campannaid = '.$campannaid.' and producto.usuarioid = '.$usuarioid;

            $em->createQuery($query)->getResult();


            $flag=1;
        }

        $em->persist($campanna);

        $em->flush($campanna);

        return new Response(json_encode(array('estado'=>$flag)));
    }


    public function cambiarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        if(!$usuario)
        {
            return $this->redirect($this->generateUrl('public_login'));
        }

        if ($request->getMethod()=='POST')
        {
            $campannaidAnterior=$request->request->get('campannaidAnterior');

            $campanna=$em->getRepository('AdministracionBundle:Campanna')->find($request->request->get('campannaid'));

            $estadoCampanna=$em->getRepository('AdministracionBundle:EstadoCampanna')->find(1);

            $usuarioCampanna= new UsuarioCampanna();

            $usuarioCampanna->setCampannaid($campanna);

            $usuarioCampanna->setUsuarioid($usuario);

            $usuarioCampanna->setEstadoCampannaid($estadoCampanna);

            $hoy=new \DateTime();

            $usuarioCampanna->setFecha($hoy);

            $em->persist($usuarioCampanna);

            $productos=$em->getRepository('AdministracionBundle:Producto')->findBy(array('campannaid'=>$campannaidAnterior,'usuarioid'=>$usuario->getId()));

            foreach ($productos as $p)
            {
                $p->setCampannaid($campanna);

                if($p->getRanking()>0)
                {
                    $p->setRanking($campanna->getId());
                }

                $em->persist($p);
            }

            $em->flush();

            return new Response(json_encode(true));

        }

        $idCampanna=$request->query->get('idCampanna');

        $campanna=$em->getRepository('AdministracionBundle:Campanna')->find($idCampanna);

        $campannaUser=$em->getRepository('AdministracionBundle:UsuarioCampanna')->findBy(
            array('usuarioid'=>$usuario->getId()), array());


        $uno =0;

        $dos =0;

        $tres=0;

        foreach ($campannaUser as $cu)
        {
            switch ($cu->getCampannaid()->getId())
            {
                case 1:
                    $uno=1;
                    break;
                case 2:
                    $dos=1;
                    break;
                case 3:
                    $tres=1;
                    break;

            }

        }

        $planes=$em->getRepository('AdministracionBundle:Campanna')->findAll();

        return $this->render('PublicBundle:Publicidad:planesCambio.html.twig',array('idCampanna'=>$idCampanna,'planes'=>$planes ,'uno'=>$uno,'dos'=>$dos,'tres'=>$tres, 'campanna'=>$campanna));

    }
}