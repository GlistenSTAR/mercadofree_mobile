<?php

namespace AdministracionBundle\Controller;

use AdministracionBundle\Entity\EstadoPedido;
use AdministracionBundle\Entity\EstadoProducto;
use AdministracionBundle\Entity\Pedido;
use AdministracionBundle\Entity\Producto;
use AdministracionBundle\Entity\Rol;
use AdministracionBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em=$this->getDoctrine()->getManager();
        $tiendas=$em->getRepository('AdministracionBundle:Tienda')->findByVisible(true);
        $productos = $em->getRepository(Producto::class)->findByStatus(EstadoProducto::ESTADO_PUBLICADO_SLUG);
        $usuarios=$em->getRepository(Usuario::class)->findByRol(Rol::ROL_CLIENT);
        $pedidosEntregados = $em->getRepository(Pedido::class)->findByStatus(EstadoPedido::ESTADO_PEDIDO_RECIBIDO_SLUG);
        $pedidosDevueltos = $em->getRepository(Pedido::class)->findByStatus(EstadoPedido::ESTADO_PEDIDO_DEVUELTO_SLUG);

        return $this->render('AdministracionBundle:Default:index.html.twig',array(
            'tiendas' => (!is_null($tiendas)) ? count($tiendas) : 0,
            'productos' => (!is_null($productos)) ? count($productos) : 0,
            'usuarios' => (!is_null($usuarios)) ? count($usuarios): 0,
            'pedidosEntregados' => (!is_null($pedidosEntregados)) ? count($pedidosEntregados): 0,
            'pedidosDevueltos' => (!is_null($pedidosDevueltos)) ? count($pedidosDevueltos): 0
        ));
    }

    public function loginAction(Request $request)
    {
        $sesion = $request->getSession();
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );
        return $this->render('AdministracionBundle:Default:login.html.twig', array(
            'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
            'error' => $error
        ));
    }

    public function generateSlugAction(Request $request){
        $entity = $request->query->get('entity');
        $field = $request->query->get('field');
        if($entity == '')
            return new JsonResponse("Falta el parametro entity");
        if($field == '')
            return new JsonResponse("Falta el parametro field");
        $em = $this->get('doctrine.orm.entity_manager');
        $entity = "AdministracionBundle:$entity";
        $filas = $em->getRepository($entity)->findAll();

        $servicio = $this->get('util');
        $cantidad = 0;
        foreach ($filas as $fila){
            $data = $fila->getNombre();
            $slug = $servicio->generateUniqueSlug($data, $entity, $field);
            $fila->setSlug($slug);
            $em->flush();
            $cantidad++;
        }
        return new JsonResponse("Cantidad de registros generados: " . $cantidad);
    }


}
