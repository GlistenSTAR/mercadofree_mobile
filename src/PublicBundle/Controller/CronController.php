<?php

namespace PublicBundle\Controller;

use AdministracionBundle\Entity\Producto;
use AdministracionBundle\Entity\EstadoProducto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

class CronController extends Controller
{

    public function validarproductosexpiradosAction(Request $request){

        $em= $this->getDoctrine()->getManager();
        
        $productosExpirados = $em->getRepository(Producto::class)->getProductosExpirados();
        
        $estadoProductoFinalizado = $em->getRepository(EstadoProducto::class)->findOneBySlug(EstadoProducto::ESTADO_FINALIZADO_SLUG);
        
        /** @var Producto $productoExpirado **/
        foreach($productosExpirados as $productoExpirado) {
            $productoExpirado->setEstadoProductoid($estadoProductoFinalizado);
            $em->persist($productoExpirado);
            $em->flush();
        }
        
        return new Response(json_encode(true)); 
    }

}
