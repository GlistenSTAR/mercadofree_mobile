<?php
namespace PublicBundle\Controller;

use AdministracionBundle\Entity\Cliente;
use AdministracionBundle\Entity\Empresa;
use AdministracionBundle\Entity\Pregunta;
use AdministracionBundle\Repository\PreguntaRepository;
use AdministracionBundle\Entity\Usuario;
use AdministracionBundle\Entity\UsuarioObjetivo;
use PublicBundle\Event\FilterUserResponseEvent;
use PublicBundle\Event\GetResponseNullableUserEvent;
use PublicBundle\Event\GetResponseUserEvent;
use PublicBundle\PublicEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\Router;

class MobileApiController extends Controller
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
        $em=$this->getDoctrine()->getManager();
        $data = $request->request->all();
        // $data = json_decode($request->getContent(), true);

        $usuario=$this->get('utilPublic')->getUsuarioLogueado();
            
        $email=$data["emailRegistrarUsuario"];
        
        $rol=$em->getRepository('AdministracionBundle:Rol')->findBy(array("id"=>2),array());
        $estado=$em->getRepository('AdministracionBundle:EstadoUsuario')->findBy(array("slug"=>'publicado'),array());

        if($request->getMethod()=='POST')
        {
            $user=$em->getRepository('AdministracionBundle:Usuario')->findOneBy( array("email"=>$email) );
        }

        if($user!=null)
        {
            return new JsonResponse(array('response'=>false,'message'=>'User already exist.'));

        } else {

            $usuario= new Usuario();
            $usuario->setEmail($email);
            $usuario->setTelefono($data['telephone']);
            $hoy=new \DateTime();
            $usuario->setFechaRegistro($hoy);

            //encoding pass
            $encoder=$this->get('security.encoder_factory')->getEncoder($usuario);
            $usuario->setSalt(md5(time()));
            $passCodifier=$encoder->encodePassword($data['passwordRegistrarUsuario'],$usuario->getSalt());
            
            $usuario->setPassword($passCodifier);
            $usuario->setRolid($rol[0]);
            $usuario->setEstadoUsuarioid($estado[0]);
            $usuario->setPuntos(0);
            $usuario->setNivel(0);

            $cliente= new Cliente();
            $cliente->setNombre($data['nombreRegistrarUsuario']);
            $cliente->setApellidos($data['apellidosRegistrarUsuario']);
            $cliente->setUsuarioid($usuario);

            $em->persist($cliente);
            $em->persist($usuario);
            $em->flush();

            $objetivo=$em->getRepository('AdministracionBundle:Objetivo')->findBy(array('slug'=>'registro'), array())[0];
            
            $objetivoUsuario= new UsuarioObjetivo();
            $objetivoUsuario->setUsuarioid($usuario);
            $objetivoUsuario->setObjetivoid($objetivo);
            $objetivoUsuario->setPuntos($objetivo->getPuntos());
            $objetivoUsuario->setFecha($hoy);
            $em->persist($objetivoUsuario);
            
            $usuario->setPuntos($usuario->getPuntos() + $objetivo->getPuntos());
            $em->flush();
            $usuario->setPuntos(0);

            $token = new UsernamePasswordToken( $usuario, $usuario->getPassword(), 'frontend', $usuario->getRoles() );

            // $this->container->get('security.context')->setToken($token);
            // $body=$this->renderView('PublicBundle:Email:mensaje_email_confirmacion_registro.html.twig',array(
            //     'user_firstname' => $cliente->getNombre()
            // ));
            // $configuracion = $em->getRepository('AdministracionBundle:Configuracion')->find(1);
            // $emailAdmin = $configuracion->getEmailAdministrador();
            // $this->get('utilPublic')->sendMail($emailAdmin,$usuario->getEmail(),'Confirmación de registro',$body,null);
            return new JsonResponse(array('response'=>true, "message" => 'Regisration Successfully.'));
        }

        // return new JsonResponse(array('response'=>true));
        // return $this->render('PublicBundle:Usuario:registrar.html.twig');

    }

    public function loginAction(Request $request)
    {
        $data = $request->request->all();
        // $data = json_decode($request->getContent(), true);
        $usuario = $this->get('utilPublic')->getUsuarioLogueado();

        if($request->getMethod()=='POST')
        {   
            $usuario= new Usuario();
            //encoding pass
            $encoder=$this->get('security.encoder_factory')
                ->getEncoder($usuario);
            // $usuario->setSalt(md5(time()));
            $passCodifier=$encoder->encodePassword(
                $data['_password'],
                $usuario->getSalt()
            );
            
            $email=$data["_username"];
            $user=$this->getDoctrine()->getManager()->getRepository('AdministracionBundle:Usuario')->findOneBy(
                array("email"=>$email),
                array()
            );
            $clentObj = $this->getDoctrine()->getManager()->getRepository('AdministracionBundle:Cliente')->findOneBy(array('usuarioid'=> $user->getId(),));
            
            
            
            $data = array();
            $data['email'] = $user->getEmail();
            $data['telefone'] = $user->getTelefono();
            $data['firstName'] = $clentObj->getNombre();
            $data['lastName'] = $clentObj->getApellidos();
            if($user){
                if (strpos($user->getPassword(), $passCodifier) !== false) {
                   return new JsonResponse(array('response'=>true, 'user'=>$data));
                }else{
                    return new JsonResponse(array('response'=>false,'message'=>'Invalid Credintials.'));
                }
            }else{
                return new JsonResponse(array('response'=>false,'message'=>'Invalid Credintials.'));
            }
        }
    }
    
    /**
     * Request reset user password: submit form and send email.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function sendEmailAction(Request $request)
    {

        $username = $request->request->get('_username');

        $user = $this->getDoctrine()->getManager()->getRepository('AdministracionBundle:Usuario')->findOneBy(array('email' => $username));
        if (!$user){
            return new JsonResponse(array('response'=>false,'username' => $username, 'error' => array('message' => 'Invalid Email Address')) );
        }

        $event = new GetResponseNullableUserEvent($user, $request);
        $this->get('event_dispatcher')->dispatch(PublicEvents::RESETTING_SEND_EMAIL_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        if (null !== $user) {

            $event = new GetResponseUserEvent($user, $request);

            $this->get('event_dispatcher')->dispatch(PublicEvents::RESETTING_RESET_REQUEST, $event);

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->get('util')->generateToken());
            }

            $event = new GetResponseUserEvent($user, $request);
            $this->get('event_dispatcher')->dispatch(PublicEvents::RESETTING_SEND_EMAIL_CONFIRM, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $configuracion = $this->getDoctrine()->getManager()->getRepository('AdministracionBundle:Configuracion')->find(1);

            $emailAdmin = $configuracion->getEmailAdministrador();

            $from = $emailAdmin;
            $to = $user->getEmail();

            $url = $this->get('router')->generate('public_user_resetting_reset', array('token' => $user->getConfirmationToken()), Router::ABSOLUTE_URL);
            $cliente = $user->getEmail();
            if(is_object($user->getClienteid())){
                $c = $user->getClienteid();
                $cliente = $c->getNombre() . " " . $c->getApellidos();
            }

            $body = $this->renderView('@Public/Email/mensaje_email_modificacion_password.html.twig', array(
                'user' => $cliente,
                'confirmationUrl' => $url
            ));

            $this->get('utilpublic')->sendMail($from, $to, "Recuperaci��n de contrase�0�9a", $body);
            $user->setPasswordRequestedAt(new \DateTime());
            $this->get('doctrine.orm.entity_manager')->flush();

            $event = new GetResponseUserEvent($user, $request);
            $this->get('event_dispatcher')->dispatch(PublicEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
        }
        return new JsonResponse(array('response'=>true, 'username' => $username));
    }

    public function changePasswordAction(Request $request)
    {
        $usuario = $this->get('utilPublic')->getUsuarioLogueado();
        $em = $this->getDoctrine()->getManager();
        $username= $request->request->get('emailC');
        
        $usuario = $this->getDoctrine()->getManager()->getRepository('AdministracionBundle:Usuario')->findOneBy(array('email' => $username));
        if(!$usuario){
            return new JsonResponse(array('response'=>false, 'message' => 'Email Does not exist'));

        }else{

            if ($request->getMethod() == 'POST') {
                
                $password = $request->request->get('passwordC');
                //encoding pass
                $encoder=$this->get('security.encoder_factory')
                    ->getEncoder($usuario);
                $usuario->setSalt(md5(time()));
                $passCodifier=$encoder->encodePassword(
                    $password,
                    $usuario->getSalt()
                );

                if ($password != "") {
                    $usuario->setPassword($passCodifier);
                }

                $em->persist($usuario);

                $em->flush();

                return new JsonResponse(array('response'=>true, 'message' => 'Password changed Successfully.'));
            }
        }

    }

}
