<?php


namespace AdministracionBundle\Controller;
use AdministracionBundle\Entity\Configuracion;
use AdministracionBundle\Entity\CostoEnvio;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;

class CostoEnvioController extends Controller
{
    public function listarAction(Request $request)
    {
        if($request->getMethod()=='POST')
        {
            $em=$this->getDoctrine()->getManager();

            $costoEnvio=$em->getRepository('AdministracionBundle:CostoEnvio')->findByCostoEnvio($request)->getResult();

            $costoEnvioTotal=$em->getRepository('AdministracionBundle:CostoEnvio')->findByCostoEnvioTotal($request)->getResult();

            $listaCostoEnvio=[];

            foreach ($costoEnvio as $ce)
            {
                $ceArray=[];
                $ceArray[]=$ce->getId();
                $ceArray[]=$ce->getProvinciaid()!=null?$ce->getProvinciaid()->getNombre():"";
                $ceArray[]=$ce->getCiudadid()!=null?$ce->getCiudadid()->getCiudadNombre():"";
                $ceArray[]=$ce->getCosto()!=null?$ce->getCosto():"";

                $listaCostoEnvio[]=$ceArray;
            }

            $json_data=array(
                "draw"=>intval($request->request->get('draw')),
                "recordsTotal"=>intval(count($costoEnvioTotal)),
                "recordsFiltered"=>intval(count($costoEnvioTotal)),
                "data"=>$listaCostoEnvio
            );

            return new Response(json_encode($json_data));

        }
        return $this->render('AdministracionBundle:CostoEnvio:listado.html.twig');
    }
    public function adicionarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $provincia=$em->getRepository('AdministracionBundle:Provincia')->findAll();

        $modoAdicionar=$request->request->get('modoAdicionar');

        $costoEnvio= new CostoEnvio();

        if ($modoAdicionar==1)
        {
            $costoEnvio->setCosto($request->request->get('nombreCostoEnvioAdicionar'));

            $provincia=$em->getRepository('AdministracionBundle:Provincia')->find($request->request->get('nombreProvinciaAdicionar'));

            $costoEnvio->setProvinciaid($provincia);

            $idCiudad=$request->request->get('nombreCiudadAdicionar');
            if ($idCiudad!="")
            {
                $ciudad=$em->getRepository('AdministracionBundle:Ciudad')->find($idCiudad);

                $costoEnvio->setCiudadid($ciudad);
            }

            $em->persist($costoEnvio);

            $em->flush();

            return new Response(json_encode(true));
        }
        if ($modoAdicionar=="3")
        {
            $provincia=$em->getRepository('AdministracionBundle:Provincia')->findAll();

            $listaProvincia=[];

            foreach ($provincia as $ce)
            {
                $ceArray=[];
                $ceArray[]=$ce->getId();
                $ceArray[]=$ce->getNombre()!=null?$ce->getNombre():"";

                $listaProvincia[]=$ceArray;
            }

            return new Response(json_encode(array('provincias'=>$listaProvincia)));
        }
        if ($modoAdicionar=="2")
        {
            $costoEnvio->setCosto($request->request->get('nombreCostoEnvioAdicionarModal'));

            $provincia=$em->getRepository('AdministracionBundle:Provincia')->find($request->request->get('nombreProvinciaAdicionarModal'));

            $costoEnvio->setProvinciaid($provincia);

            $idCiudad=$request->request->get('nombreCiudadAdicionarModal');
            if ($idCiudad!="")
            {
                $ciudad=$em->getRepository('AdministracionBundle:Ciudad')->find($idCiudad);

                $costoEnvio->setCiudadid($ciudad);
            }

            $em->persist($costoEnvio);

            $em->flush();

            return new Response(json_encode(true));
        }


        return $this->render('AdministracionBundle:CostoEnvio:adicionar.html.twig', array('provincias'=>$provincia));
    }

    public function editarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $editarCostoEnvio=$request->request->get('editarCostoEnvio');

        $idCostoEnvio=$request->request->get('idCostoEnvio');

        $costoEnvio=$em->getRepository('AdministracionBundle:CostoEnvio')->find($idCostoEnvio);

        if ($editarCostoEnvio=="false")
        {
            $provincia=$em->getRepository('AdministracionBundle:Provincia')->findAll();

            $listaProvincia=[];

            foreach ($provincia as $ce)
            {
                $ceArray=[];
                $ceArray[]=$ce->getId();
                $ceArray[]=$ce->getNombre()!=null?$ce->getNombre():"";

                $listaProvincia[]=$ceArray;
            }

            $ciudad=$em->getRepository('AdministracionBundle:Ciudad')->findBy(
                array('provinciaid'=>$costoEnvio->getProvinciaid()->getId()),
                array()
            );

            $listaCiudad=[];

            foreach ($ciudad as $ce)
            {
                $ceArray=[];
                $ceArray[]=$ce->getId();
                $ceArray[]=$ce->getCiudadNombre()!=null?$ce->getCiudadNombre():"";

                $listaCiudad[]=$ceArray;
            }

            $ceArray=[];
            $ceArray[]=$costoEnvio->getId();
            $ceArray[]=$costoEnvio->getProvinciaid()!=null?$costoEnvio->getProvinciaid()->getId():"";
            $ceArray[]=$costoEnvio->getProvinciaid()!=null?$costoEnvio->getProvinciaid()->getNombre():"";
            $ceArray[]=$costoEnvio->getCiudadid()!=null?$costoEnvio->getCiudadid()->getId():"";
            $ceArray[]=$costoEnvio->getCiudadid()!=null?$costoEnvio->getCiudadid()->getCiudadNombre():"";
            $ceArray[]=$costoEnvio->getCosto()!=null?$costoEnvio->getCosto():"";


            return new Response(json_encode(array('provincias'=>$listaProvincia, 'ciudades'=>$listaCiudad, 'costoEnvio'=>$ceArray)));



        }
        if ($editarCostoEnvio=="true")
        {
            $provincia=$em->getRepository('AdministracionBundle:Provincia')->find($request->request->get('nombreProvinciaEditar'));

            $costoEnvio->setProvinciaid($provincia);

            $ciudad=$em->getRepository('AdministracionBundle:Ciudad')->find($request->request->get('nombreCiudadEditar'));

            $costoEnvio->setCiudadid($ciudad);

            $costoEnvio->setCosto($request->request->get('nombreCostoEnvioEditar'));

            $em->persist($costoEnvio);

            $em->flush();

            return new Response(json_encode(true));
        }
    }

    public function eliminarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $idCostoEnvio=$request->request->get("idCostoEnvioEliminar");
        $idCostoEnvio=explode(':',$idCostoEnvio);
        for ($i=1;$i<count($idCostoEnvio);$i++)
        {
            $costoEnvio = $em->getRepository('AdministracionBundle:CostoEnvio')->find($idCostoEnvio[$i]);
            $em->remove($costoEnvio);
        }
        $em->flush();

        return new Response(json_encode(true));

    }
}