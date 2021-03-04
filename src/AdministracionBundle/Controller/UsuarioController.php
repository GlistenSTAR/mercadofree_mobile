<?php
/**
 * Created by PhpStorm.
 * User: HUMBERTO
 * Date: 05/02/2018
 * Time: 09:22 PM
 */

namespace AdministracionBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AdministracionBundle\Entity\MovimientoCuenta;
use AdministracionBundle\Repository\MovimientoCuentaRepository;
use AdministracionBundle\Entity\Usuario;
use AdministracionBundle\Entity\Pedido;
use AdministracionBundle\Repository\PedidoRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;


class UsuarioController extends Controller
{
    public function listarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        if($request->getMethod()=='POST')
        {
            $usuarios=$em->getRepository('AdministracionBundle:Usuario')->findByUsuario($request)->getResult();

            $usuariosTotal=$em->getRepository('AdministracionBundle:Usuario')->findByUsuarioTotal($request)->getResult();

            $listaUsuario=[];

            foreach ($usuarios as $user)
            {
                $userArray=[];
                $userArray[]=$user->getId();
                $userArray[]=$user->getFechaRegistro()!=null?$user->getFechaRegistro()->format('j/n/Y'):"";
                $userArray[]=$user->getEmail()!=null?$user->getEmail():"";
                $userArray[]=$user->getRolid()!=null?$user->getRolid()->getNombre():"";
                $userArray[]=$user->getEstadoUsuarioid()!=null?$user->getEstadoUsuarioid()->getNombre():"";

                $listaUsuario[]=$userArray;
            }

            $json_data=array(
                "draw"=>intval($request->request->get('draw')),
                "recordsTotal"=>intval(count($usuariosTotal)),
                "recordsFiltered"=>intval(count($usuariosTotal)),
                "data"=>$listaUsuario
            );

            return new Response(json_encode($json_data));

        }

        $roles=$em->getRepository('AdministracionBundle:Rol')->findAll();

        return $this->render('AdministracionBundle:Usuario:listado.html.twig',array('roles'=>$roles));
    }

    public function editarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        if ($request->request->get("editarUsuario")=="false")
        {
            $idUsuario=$request->request->get("idUsuario");
            $usuarios=$em->getRepository('AdministracionBundle:Usuario')->findBy(
                array("id"=>$idUsuario),
                array()
            );

            $userArray=[];

                $userArray[]=$usuarios[0]->getId();
                $userArray[]=$usuarios[0]->getFechaRegistro()!=null?$usuarios[0]->getFechaRegistro()->format('j/n/Y'):"";
                $userArray[]=$usuarios[0]->getEmail()!=null?$usuarios[0]->getEmail():"";
                $userArray[]=$usuarios[0]->getRolid()!=null?$usuarios[0]->getRolid()->getNombre():"";
                $userArray[]=$usuarios[0]->getPassword()!=null?$usuarios[0]->getPassword():"";
                $userArray[]=$usuarios[0]->getEstadoUsuarioid()!=null?$usuarios[0]->getEstadoUsuarioid()->getNombre():"";


            $roles=$em->getRepository('AdministracionBundle:Rol')->findAll();

            $rolesArray=[];
            foreach ($roles as $rol)
            {
                $rolArray=[];
                $rolArray[]=$rol->getId();
                $rolArray[]=$rol->getNombre();
                $rolesArray[]=$rolArray;
            }

            $estados=$em->getRepository('AdministracionBundle:EstadoUsuario')->findAll();

            $estadosArray=[];
            foreach ($estados as $estado)
            {
                $estadoArray=[];
                $estadoArray[]=$estado->getId();
                $estadoArray[]=$estado->getNombre();
                $estadosArray[]=$estadoArray;
            }

            return new Response(json_encode(array("usuario"=>$userArray, "roles"=>$rolesArray, 'estados'=>$estadosArray)));
        }
        if ($request->request->get("editarUsuario")=="true")
        {
            $idUsuario=$request->request->get("idUsuarioEditar");
            $usuario=$em->getRepository('AdministracionBundle:Usuario')->findBy(
                    array("id"=>$idUsuario),
                    array()
                    )[0];

            $usuario->setPassword($request->request->get("passwordEditarUsuario"));

            $rol=$em->getRepository('AdministracionBundle:Rol')->findBy(
                array("id"=>$request->request->get("rolEditarUsuario")),
                array()
            );

            $estado=$em->getRepository('AdministracionBundle:EstadoUsuario')->findBy(
                array("id"=>$request->request->get("estadoEditarUsuario")),
                array()
            );

            $usuario->setRolid($rol[0]);
            $usuario->setEstadoUsuarioid($estado[0]);
            $em->persist($usuario);
            $em->flush();

            return new Response(json_encode(true));
        }


    }

    public function adicionarAction(Request $request)
    {
        $modoAdicionar=$request->query->get("modoAdicionar");
        $em=$this->getDoctrine()->getManager();
        if($modoAdicionar==null)
        {

            $email=$request->request->get("emailAdicionarUsuario");
            $user=$this->getDoctrine()->getManager()->getRepository('AdministracionBundle:Usuario')->findOneBy(
                array("email"=>$email),
                array()
            );

            if($user!=null)
            {
                return new Response(json_encode(false));
            }
            else
            {
                $usuario= new Usuario();
                $usuario->setEmail($request->request->get('emailAdicionarUsuario'));
                $hoy=new \DateTime();
                $usuario->setFechaRegistro($hoy);
                $usuario->setPassword($request->request->get('passwordAdicionarUsuario'));
                $rolId=$request->request->get('rolAdicionarUsuario');
                $rol=$em->getRepository('AdministracionBundle:Rol')->findBy(array("id"=>$rolId),array());
                $usuario->setRolid($rol[0]);
                $estado=$em->getRepository('AdministracionBundle:EstadoUsuario')->findBy(array("slug"=>'publicado'),array());
                $usuario->setEstadoUsuarioid($estado[0]);
                $em->persist($usuario);
                $em->flush();

                return new Response(json_encode(true));
            }
        }
        $roles=$em->getRepository('AdministracionBundle:Rol')->findAll();
        return $this->render('AdministracionBundle:Usuario:adicionar.html.twig',array('roles'=>$roles));

    }
    public function eliminarAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $idUsuario=$request->request->get("idUsuarioEliminar");
        $idUsuario=explode(':',$idUsuario);
        for ($i=1;$i<count($idUsuario);$i++)
        {
            $usuario=$em->getRepository('AdministracionBundle:Usuario')->findBy(
                array("id"=>$idUsuario[$i]),
                array()
            )[0];

            $tiendas=$em->getRepository('AdministracionBundle:Tienda')->findBy(
                array("usuarioid"=>$usuario),
                array()
            );

            foreach ($tiendas as $tienda)
            {
                if($tienda!=null)
                {
                    $em->remove($tienda);
                }
            }
            $productos=$em->getRepository('AdministracionBundle:Producto')->findBy(
                array("usuarioid"=>$usuario),
                array()
            );

            foreach ($productos as $producto)
            {
                if($producto!=null)
                {
                    $em->remove($producto);
                }
            }

            $em->remove($usuario);
        }

        $em->flush();
        return new Response(json_encode(true));
    }

    public function detallesAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $idUsuario=$request->query->get("idUsuario");

        /** @var Usuario $usuario **/
        $usuario=$em->getRepository(Usuario::class)->find($idUsuario);

        return $this->render('AdministracionBundle:Usuario:detalles.html.twig',array('usuario'=>$usuario));
    }

    public function tiendaproductodetallesAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $productos=$em->getRepository('AdministracionBundle:Producto')->findByProductoDetalles($request)->getResult();

        $productoTotal=$em->getRepository('AdministracionBundle:Producto')->findByProductoDetallesTotal($request)->getResult();

        $listaProducto=[];

        foreach ($productos as $produc)
        {
            $producArray=[];
            $producArray[]=$produc->getImagenDestacada();
            $producArray[]=$produc->getNombre()!=null?$produc->getNombre():"";
            $producArray[]=$produc->getPrecio()!=null?$produc->getPrecio():"";
            $producArray[]=$produc->getCantidad()!=null?$produc->getCantidad():"";
            $producArray[]=$produc->getCategoriaid()!=null?$produc->getCategoriaid()->getNombre():"";


            $listaProducto[]=$producArray;
        }

        $json_data=array(
            "draw"=>intval($request->request->get('draw')),
            "recordsTotal"=>intval(count($productoTotal)),
            "recordsFiltered"=>intval(count($productoTotal)),
            "data"=>$listaProducto
        );

        return new Response(json_encode($json_data));
    }
    
    function detallePedidosAction(Request $request) {
        $em=$this->getDoctrine()->getManager();

        /** @var PedidoRepository $pedidoRepository */
        $pedidoRepository = $em->getRepository(Pedido::class);
        
        $pedidos= $pedidoRepository->findByProductoDetalles($request)->getResult();

        $pedidosTotal= $pedidoRepository->findByProductoDetallesTotal($request)->getResult();

        $listaPedidos=[];

        foreach ($pedidos as $pedido)
        {
            $pedidoArray=[];
            $pedidoArray[]=$pedido->getImagenDestacada();
            $pedidoArray[]=$pedido->getNombre()!=null?$pedido->getNombre():"";
            $pedidoArray[]=$pedido->getPrecio()!=null?$pedido->getPrecio():"";
            $pedidoArray[]=$pedido->getCantidad()!=null?$pedido->getCantidad():"";
            $pedidoArray[]=$pedido->getCategoriaid()!=null?$pedido->getCategoriaid()->getNombre():"";


            $listaPedidos[]=$pedidoArray;
        }

        $json_data=array(
            "draw"=>intval($request->request->get('draw')),
            "recordsTotal"=>intval(count($pedidosTotal)),
            "recordsFiltered"=>intval(count($pedidosTotal)),
            "data"=>$listaPedidos
        );

        return new Response(json_encode($json_data));
    }
    
    public function finanzasUsuarioAction(Request $request) {
        
        $em = $this->getDoctrine()->getManager();
        
        /** @var MovimientoCuentaRepository $movimientoCuentaRepository */
        $movimientoCuentaRepository = $em->getRepository(MovimientoCuenta::class);
        
        $movimientosCuenta = $movimientoCuentaRepository->findByMovimientoCuenta($request)->getResult();
        
        $movimientosCuentaTotales = $movimientoCuentaRepository->findByMovimientoCuentaTotal($request)->getResult();
        
        $listaMovimientosCuenta=[];
        
        /** @var MovimientoCuenta $movimientoCuenta */
        foreach ($movimientosCuenta as $movimientoCuenta)
        {
            $movimientoCuentaArray=[];

            $movimientoCuentaArray[]=$movimientoCuenta->getReferencia();
            $movimientoCuentaArray[]=$movimientoCuenta->getFecha()->format('j/n/Y H:i');
            $movimientoCuentaArray[]= number_format($movimientoCuenta->getMonto(), 2, '.', ',');
            $movimientoCuentaArray[]=$movimientoCuenta->getTipo();
            $movimientoCuentaArray[]=$movimientoCuenta->getConceptoMovimientoCuentaid()!=null?$movimientoCuenta->getConceptoMovimientoCuentaid()->getNombre():"";
            $movimientoCuentaArray[]=$this->generateUrl('administracion_usuario_movimiento_cuenta',array('id'=>$movimientoCuenta->getId()));

            $listaMovimientosCuenta[]=$movimientoCuentaArray;

        }
        
        $json_data=array(
            "draw"=>intval($request->request->get('draw')),
            "recordsTotal"=>intval(count($movimientosCuentaTotales)),
            "recordsFiltered"=>intval(count($movimientosCuentaTotales)),
            "data"=>$listaMovimientosCuenta
        );

        return new Response(json_encode($json_data));
    }
    
    public function detallesFinanzasUsuarioAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $movimiento = $em
            ->getRepository(MovimientoCuenta::class)
            ->find($id);
        
        $usuario = $this->getUser();

        if (!$movimiento) {
            throw $this->createNotFoundException('Recurso no encontrado');
        }

        return $this->render(
            'AdministracionBundle:Usuario:detallesFinanzaUsuario.html.twig',
            ['movimiento' =>  $movimiento,
             'usuario' => $usuario]
        );
    }
    
    public function comprasUsuarioAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        
        /** @var PedidoRepository $pedidoRepository */
        $pedidoRepository = $em->getRepository(Pedido::class);
        
        $compras = $pedidoRepository->findByPedido($request)->getResult();
        $ComprasTotales = $pedidoRepository->findByPedidoTotal($request)->getResult();
        
        $listaCompras=[];
        
        /** @var Pedido $compra */
        foreach ($compras as $compra)
        {
            $CompraArray=[];

            $CompraArray[]=$compra->getCodigo();
            $CompraArray[]=$compra->getFecha()->format('j/n/Y H:i');
            $CompraArray[]= number_format($compra->getTotal(), 2, '.', ',');
            $CompraArray[]=$compra->getEstado()->getNombre();
            //$links
            //$CompraArray[]='<a href="#" class="btn btn-rw btn-primary" type="button" target="_blank">Ver Detalles</a>';//$this->generateUrl('administracion_usuario_movimiento_cuenta',array('id'=>$compra->getId()));

            $listaCompras[]=$CompraArray;

        }

        $json_data=array(
            "draw"=>intval($request->request->get('draw')),
            "recordsTotal"=>intval(count($ComprasTotales)),
            "recordsFiltered"=>intval(count($ComprasTotales)),
            "data"=>$listaCompras
        );
        
        return new Response(json_encode($json_data));
    }

    public function ventasUsuarioAction(Request  $request)
    {
        $em = $this->getDoctrine()->getManager();

        $idUsuario = $request->request->get('idVendedor');
        $start = $request->request->get('start');
        $offset = $request->request->get('length');

        $compras = $em->getRepository(Pedido::class)->findPedidoByVendedor($idUsuario,$start,$offset);

        /** @var PedidoRepository $pedidoRepository */
        /*$pedidoRepository = $em->getRepository(Pedido::class);

        $compras = $pedidoRepository->findByPedido($request)->getResult();
        $ComprasTotales = $pedidoRepository->findByPedidoTotal($request)->getResult();*/

        $listaCompras=[];

        /** @var Pedido $compra */
        foreach ($compras as $compra)
        {
            $CompraArray=[];

            $CompraArray[]=$compra->getCodigo();
            $CompraArray[]=$compra->getFecha()->format('j/n/Y H:i');
            $CompraArray[]= number_format($compra->getTotal(), 2, '.', ',');
            $CompraArray[]=$compra->getEstado()->getNombre();
            //$links
            //$CompraArray[]='<a href="#" class="btn btn-rw btn-primary" type="button" target="_blank">Ver Detalles</a>';//$this->generateUrl('administracion_usuario_movimiento_cuenta',array('id'=>$compra->getId()));

            $listaCompras[]=$CompraArray;

        }

        $json_data=array(
            "draw"=>intval($request->request->get('draw')),
            "recordsTotal"=>intval(count($compras)),
            "recordsFiltered"=>intval(count($compras)),
            "data"=>$listaCompras
        );

        return new Response(json_encode($json_data));
    }
}
