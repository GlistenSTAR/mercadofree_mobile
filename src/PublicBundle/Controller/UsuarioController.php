<?php
namespace PublicBundle\Controller;

use AdministracionBundle\Entity\Cliente;
use AdministracionBundle\Entity\Empresa;
use AdministracionBundle\Entity\Pregunta;
use AdministracionBundle\Repository\PreguntaRepository;
use AdministracionBundle\Entity\Usuario;
use AdministracionBundle\Entity\UsuarioObjetivo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UsuarioController extends Controller
{
    private function getEntityManager() {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Registro de nuevo usuario
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|null
     */
    public function registrarAction(Request $request)
    {
        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        if($usuario)
        {
            return $this->redirect($this->generateUrl('public_homepage'));
        }

        $em=$this->getDoctrine()->getManager();
        if($request->getMethod()=='POST')
        {
            $email=$request->request->get("emailRegistrarUsuario");
            $user=$this->getDoctrine()->getManager()->getRepository('AdministracionBundle:Usuario')->findOneBy(
                array("email"=>$email),
                array()
            );

            if($user!=null)
            {
                return $this->render('PublicBundle:Usuario:registrar.html.twig');
            }
            else
            {
                $usuario= new Usuario();
                $usuario->setEmail($request->request->get('emailRegistrarUsuario'));
                $hoy=new \DateTime();
                $usuario->setFechaRegistro($hoy);

                //encoding pass
                $encoder=$this->get('security.encoder_factory')
                    ->getEncoder($usuario);
                $usuario->setSalt(md5(time()));
                $passCodifier=$encoder->encodePassword(
                    $request->request->get('passwordRegistrarUsuario'),
                    $usuario->getSalt()
                );

                $usuario->setPassword($passCodifier);
                $rol=$em->getRepository('AdministracionBundle:Rol')->findBy(array("id"=>2),array());
                $usuario->setRolid($rol[0]);
                $estado=$em->getRepository('AdministracionBundle:EstadoUsuario')->findBy(array("slug"=>'publicado'),array());
                $usuario->setEstadoUsuarioid($estado[0]);
                $usuario->setPuntos(0);
                $usuario->setNivel(0);

                $cliente= new Cliente();
                $cliente->setNombre($request->request->get('nombreRegistrarUsuario'));
                $cliente->setApellidos($request->request->get('apellidosRegistrarUsuario'));
                $cliente->setUsuarioid($usuario);

                $em->persist($cliente);

                $em->persist($usuario);

                $em->flush();

                $objetivoUsuario= new UsuarioObjetivo();

                $objetivoUsuario->setUsuarioid($usuario);

                $objetivo=$em->getRepository('AdministracionBundle:Objetivo')->findBy(array('slug'=>'registro'), array())[0];

                $objetivoUsuario->setObjetivoid($objetivo);

                $objetivoUsuario->setPuntos($objetivo->getPuntos());

                $objetivoUsuario->setFecha($hoy);

                $em->persist($objetivoUsuario);
                $usuario->setPuntos($usuario->getPuntos() + $objetivo->getPuntos());

                $em->flush();

                $usuario->setPuntos(0);

                $token = new UsernamePasswordToken( $usuario, $usuario->getPassword(), 'frontend', $usuario->getRoles() );

                $this->container->get('security.context')->setToken($token);

                $body=$this->renderView('PublicBundle:Email:mensaje_email_confirmacion_registro.html.twig',array(
                    'user_firstname' => $cliente->getNombre()
                ));
                $configuracion = $em->getRepository('AdministracionBundle:Configuracion')->find(1);
                $emailAdmin = $configuracion->getEmailAdministrador();
                $this->get('utilPublic')->sendMail($emailAdmin,$usuario->getEmail(),'Confirmación de registro',$body,null);

                return  $this->redirect($this->generateUrl('public_homepage'));
            }
        }

        return $this->render('PublicBundle:Usuario:registrar.html.twig');

    }

    /**
     * Registro para empresas
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|null
     */
    public function registrar_empresaAction(Request $request)
    {
        $usuario=$this->get('utilPublic')->getUsuarioLogueado();

        if($usuario)
        {
            return $this->redirect($this->generateUrl('public_homepage'));
        }

        $em=$this->getDoctrine()->getManager();
        if($request->getMethod()=='POST')
        {
            $email=$request->request->get("emailRegistrarUsuario");
            $user=$this->getDoctrine()->getManager()->getRepository('AdministracionBundle:Usuario')->findOneBy(
                array("email"=>$email),
                array()
            );

            if($user!=null)
            {
                return $this->render('PublicBundle:Usuario:registrar.html.twig');
            }
            else
            {
                $usuario= new Usuario();
                $usuario->setEmail($request->request->get('emailRegistrarUsuario'));
                $hoy=new \DateTime();
                $usuario->setFechaRegistro($hoy);

                //encoding pass
                $encoder=$this->get('security.encoder_factory')
                    ->getEncoder($usuario);
                $usuario->setSalt(md5(time()));
                $passCodifier=$encoder->encodePassword(
                    $request->request->get('passwordRegistrarUsuario'),
                    $usuario->getSalt()
                );

                $usuario->setPassword($passCodifier);
                $rol=$em->getRepository('AdministracionBundle:Rol')->findBy(array("id"=>2),array());
                $usuario->setRolid($rol[0]);
                $estado=$em->getRepository('AdministracionBundle:EstadoUsuario')->findBy(array("slug"=>'publicado'),array());
                $usuario->setEstadoUsuarioid($estado[0]);

                $empresa= new Empresa();

                $empresa->setCuit($request->request->get('cuitRegistrarEmpresa'));

                $empresa->setRazonSocial($request->request->get('razonSocialRegistrarEmpresa'));

                $empresa->setUsuarioid($usuario);

                $em->persist($empresa);

                $em->persist($usuario);

                $em->flush();

                $objetivoUsuario= new UsuarioObjetivo();

                $objetivoUsuario->setUsuarioid($usuario);

                $objetivo=$em->getRepository('AdministracionBundle:Objetivo')->findBy(array('slug'=>'registro'), array())[0];

                $objetivoUsuario->setObjetivoid($objetivo);

                $objetivoUsuario->setPuntos($objetivo->getPuntos());

                $objetivoUsuario->setFecha($hoy);

                $em->persist($objetivoUsuario);

                $em->flush();

                $usuario->setPuntos($usuario->getPuntos()+$objetivo->getPuntos());

                $token = new UsernamePasswordToken( $usuario, $usuario->getPassword(), 'frontend', $usuario->getRoles() );

                $this->container->get('security.context')->setToken($token);

                $configuracion = $em->getRepository('AdministracionBundle:Configuracion')->find(1);
                $emailAdmin = $configuracion->getEmailAdministrador();
                $body=$this->renderView('PublicBundle:Templates:emailTemplate.html.twig');
                $this->get('utilPublic')->sendMail($emailAdmin,$usuario->getEmail(),'Hola',$body,null);

                return  $this->redirect($this->generateUrl('public_homepage'));
            }
        }

        return $this->render('PublicBundle:Usuario:registrar_empresa.html.twig');

    }

    /**
     * Renderiza el perfil del usuario vendedor indicado en los parámetros
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|null
     */
    public function perfilVendedorAction(Request $request)
    {
        
        $idUsuario=$request->get('id');
        
        $em = $this->getEntityManager();
        
        /** @var Usuario $vendedor */
        $vendedor = $em->getRepository(Usuario::class)->find($idUsuario);
        
        if(!$vendedor) {
            return $this->redirect($this->generateUrl('public_homepage'));
        }
        
        $preguntaRepository = $em->getRepository(Pregunta::class);
        $preguntasRespondidas = $preguntaRepository->preguntasRespondidasPorVendedor($vendedor);        
        
        $direccionVenta = $vendedor->getDireccionVenta();
        
        return $this->render('PublicBundle:Usuario:perfil_vendedor.html.twig',
                            array(
                                'vendedor' => $vendedor,
                                'preguntasRespondidas' => $preguntasRespondidas,
                                'direccionVenta' => $direccionVenta
                                    ));
    }

    /**
     * Verifica que el email especificado no existe ya en la base de datos, función de validación
     * en el registro de usuario
     * @param Request $request
     * @return JsonResponse
     */
    public function checkEmailExistAction(Request $request)
    {
        $email = $request->request->get('email');

        $em = $this->getEntityManager();

        if(is_null($em->getRepository(Usuario::class)->findOneBy(array(
            'email' => $email
        )))){
            return new JsonResponse(array('response'=>false));
        }
        else{
            return new JsonResponse(array('response'=>true));
        }
    }
    
}
