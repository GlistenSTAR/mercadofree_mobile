<?php

namespace PublicBundle\Controller;

use AdministracionBundle\Entity\Usuario;
use PublicBundle\Event\FilterUserResponseEvent;
use PublicBundle\Event\GetResponseNullableUserEvent;
use PublicBundle\Event\GetResponseUserEvent;
use PublicBundle\PublicEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;

/**
 * Controller managing the resetting of the password.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ResettingController extends Controller
{

    private $retryTtl;

    public function __construct($retryTtl = 86400)
    {
        $this->retryTtl = $retryTtl;
    }

    /**
     * Request reset user password: show form.
     */
    public function requestAction()
    {
        return $this->render('@Public/Resetting/request.html.twig');
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

        $user = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AdministracionBundle:Usuario')->findOneBy(array('email' => $username));
        if (!$user)
            return $this->render('@Public/Resetting/request.html.twig', array(
                    'error' => array('message' => 'La dirección de correo es inválida'), 'username' => $username)
            );
        //$passwordRequestExpired = $user->isPasswordRequestNonExpired($this->retryTtl);
        // if ($passwordRequestExpired['expired']) {
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

            $configuracion = $this->get('doctrine.orm.entity_manager')->getRepository('AdministracionBundle:Configuracion')->find(1);

            $emailAdmin = $configuracion->getEmailAdministrador();

            $from = $emailAdmin;
            $to = $user->getEmail();
            //$template = '@Public/Resetting/email.txt.twig';

            $url = $this->get('router')->generate('public_user_resetting_reset', array('token' => $user->getConfirmationToken()), Router::ABSOLUTE_URL);
//            $url = '<a href="'.$url.'">'.$url.'</a>';
            $cliente = $user->getEmail();
            if(is_object($user->getClienteid())){
                $c = $user->getClienteid();
                $cliente = $c->getNombre() . " " . $c->getApellidos();
            }

            $body = $this->renderView('@Public/Email/mensaje_email_modificacion_password.html.twig', array(
                'user' => $cliente,
                'confirmationUrl' => $url
            ));

            $this->get('utilpublic')->sendMail($from, $to, "Recuperación de contraseña", $body);
            $user->setPasswordRequestedAt(new \DateTime());
            $this->get('doctrine.orm.entity_manager')->flush();

            $event = new GetResponseUserEvent($user, $request);
            $this->get('event_dispatcher')->dispatch(PublicEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
        }
        return new RedirectResponse($this->generateUrl('public_user_resetting_check_email', array('username' => $username)));
    }

    /**
     * Tell the user to check his email provider.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function checkEmailAction(Request $request)
    {
        $username = $request->query->get('username');
        $requestExpired = $request->query->get('requestExpired');
        if (empty($username)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->generateUrl('public_user_resetting_request'));
        }

        return $this->render('@Public/Resetting/checkEmail.html.twig', array(
            'tokenLifetime' => ceil($this->retryTtl / 3600)
        ));
    }

    /**
     * Reset user password.
     *
     * @param Request $request
     * @param string $token
     *
     * @return Response
     */
    public function resetAction(Request $request, $token)
    {
        $user = $this->get('doctrine.orm.entity_manager')->getRepository('AdministracionBundle:Usuario')
            ->findOneBy(array('confirmationToken' => $token));
        if ($request->getMethod() == 'POST') {


            if (null === $user) {
                return new RedirectResponse($this->container->get('router')->generate('public_login'));
            }

            $event = new GetResponseUserEvent($user, $request);
            $this->get('event_dispatcher')->dispatch(PublicEvents::RESETTING_RESET_INITIALIZE, $event);
            $password = $request->request->get('_password');
            $confirmpassword = $request->request->get('_second_password');
            if ($password === $confirmpassword) {
                $encodedPassword = $this->get('security.password_encoder')->encodePassword($user, $password);
                $this->get('event_dispatcher')->dispatch(PublicEvents::RESETTING_RESET_SUCCESS, $event);
                $user->setPassword($encodedPassword);
                $this->get('doctrine.orm.entity_manager')->flush();
                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('public_homepage');
                    $response = new RedirectResponse($url);
                }

                $this->get('event_dispatcher')->dispatch(
                    PublicEvents::RESETTING_RESET_COMPLETED,
                    new FilterUserResponseEvent($user, $request, $response)
                );

                return $this->render('@Public/Resetting/success.html.twig');
            } else {
                return $this->render('@Public/Resetting/reset.html.twig', array(
                    'token' => $token,
                    'error' => array('message' => 'Las contraseñas no coinciden.')
                ));
            }


        }
        return $this->render('@Public/Resetting/reset.html.twig', array(
            'token' => $token
        ));


    }


}

