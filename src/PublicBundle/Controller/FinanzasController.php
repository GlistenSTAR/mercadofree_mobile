<?php

namespace PublicBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use AdministracionBundle\Entity\MovimientoCuenta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Email;
use PublicBundle\Form\AgregarFondoType;
use AdministracionBundle\Entity\Configuracion;
use AdministracionBundle\Entity\EstadoSolicitudRetiro;
use AdministracionBundle\Entity\Usuario;
use AdministracionBundle\Entity\SolicitudRetiroFondos;

class FinanzasController extends Controller
{
    const ITEMS_PER_PAGE = 10;
    const CONCEPTO_RETIRO = 0;

    public function indexAction()
    {
        $usuario = $this->getUser();
        $saldo = $usuario->getSaldo();
        return $this->render(
            'PublicBundle:Finanzas:index.html.twig',
            array('saldo' => $saldo)
        );
    }

    public function detallesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $movimiento = $em
            ->getRepository(MovimientoCuenta::class)
            ->find($id);
        
        $usuario = $this->getUser();

        if (!$movimiento) {
            throw $this->createNotFoundException('Recurso no encontrado');
        }

        return $this->render(
            'PublicBundle:Finanzas:detalles.html.twig',
            ['movimiento' =>  $movimiento,
             'usuario' => $usuario]
        );
    }

    public function asyncGetDataAction(Request $request)
    {
        $usuario = $this->getUser();
        if (empty($usuario)) {
            return $this->redirect(
                $this->generateUrl('public_login')
            );
        }

        $page = $request->request->get('page') ?? 0;
        $em = $this->getDoctrine()->getManager();
        $registros = $em
            ->getRepository(MovimientoCuenta::class)
            ->findMovimentosByUsuario($usuario->getId(), $page);

        $registros = array_map( function($e) {
            $e['fecha'] = $e['fecha']->format('d-m-Y');
            $e['urlDetalle'] = $this->generateUrl('public_finanzas_detalles', ['id' => $e['id']]);
            return $e;
        }, $registros);

        return new JsonResponse([
            'data' => $registros,
            'end'  => count($registros) < 10 ? 1 : 0,
        ]);
    }

    public function retirarFondosAction(Request $request)
    {
        $usuario = $this->getUser();

        $valor = $usuario->getSaldo();
        $valor = is_null($valor) ? 0 : $valor;
        $defaultData = [
            'email' => $usuario->getEmailPaypal(),
            'monto' => $valor,
        ];

        $form = $this->createFormBuilder($defaultData)
        ->add(
            'email',
            EmailType::class,
            [
                'label' => 'Cuenta PayPal (Email de tu cuenta PayPal)',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Email es requerido']),
                    new Email(['message' => 'Formato de email invalido']),
                ],
            ])
        ->add(
            'monto',
            NumberType::class,
            [
                'label' => 'Monto a retirar',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Monto es requerido']),
                    new GreaterThan(array('value' => 0, 'message' => 'Monto es invalido')),
                    new LessThanOrEqual(array('value' => $valor, 'message' => 'Monto excedido')),
                ],
            ])
        ->add('enviar', SubmitType::class)
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['anid'] = "25011988445";

            $this->registrarRetiroDeFondosPaypal($usuario, $data);
            
            $this->addFlash('success',  'Su solicitud se ha registrado con éxito, ' . 
                            'en un plazo de 24 a 72 horas laborales, recibirá ' . 
                            'sus fondos.' );

            return $this->redirectToRoute('public_finanzas_index');
        }
        
        $this->addFlash('danger',  'Transacción fallida, intente nuevamente más tarde o contáctenos.' );
        
        return $this->render(
            'PublicBundle:Finanzas:retirar.html.twig',
            ['form' => $form->createView(),
             'usuario' => $usuario
            ]
        );
    }
    
    private function registrarRetiroDeFondosPaypal(Usuario $usuario, $data) {
        $registrarMovServcie = $this->container->get('RegistroMivimento');
        
        $registrarMovServcie->registrarRetiroPaypal($usuario, [
                'referencia' => 'REF1',
                'monto'      => $data['monto'],
                'refext'     => 'OUTRAREF',
            ]);
        
        $emailService = $this->container->get('email');
        
        $em = $this->getDoctrine()->getManager();
        
        /** @var Configuracion $configuracion */
        $configuracion = $em->getRepository(Configuracion::class)->getDefaultConfiguracion();
        $emailAdministrador = $configuracion->getEmailAdministrador();
        
        //Según la configuración que el administrador halla elegido
        //los retiros se aprueban automaticamente, sin notificar al administrador
        if($configuracion->getAprobarAutomaticamenteRetiros()) {
            /** @var EstadoSolicitudRetiro $estadoSolicitudRetiro */
            $estadoSolicitudRetiro = $em->getRepository(EstadoSolicitudRetiro::class)->findEstadoAprobado();
        } else {
            /** @var EstadoSolicitudRetiro $estadoSolicitudRetiro */
            $estadoSolicitudRetiro = $em->getRepository(EstadoSolicitudRetiro::class)->findEstadoPendiente();
        }
        
        $solicitudRetiroFondos = SolicitudRetiroFondos::nuevaSolicitudRetiroFondos($data['email'], $data['monto'], new \DateTime(), $usuario, $estadoSolicitudRetiro);
        
        $em->persist($solicitudRetiroFondos);
        $em->flush();
        
        if(!$configuracion->getAprobarAutomaticamenteRetiros()) {
            $emailBody = $this->render('PublicBundle:Email:mensaje_email_solicitud_retiro_fondos.html.twig',array(
                                    'usuario' => $usuario,
                                    'solicitudRetiroFondos' => $solicitudRetiroFondos
                    ));

            $emailService->sendMail('noreply@mercadofree.com', 
                                    $emailAdministrador, 
                                    'Nueva solicitud de retiro de fondos', 
                                    $emailBody->getContent());
        }
    }

    public function agregarFondosAction(Request $request)
    {
        $usuario = $this->getUser();

        $defaultData = ['email' => $usuario->getEmailPayPal()];
        $form = $this->createForm(AgregarFondoType::class, $defaultData);

        $form->handleRequest($request);
        $form->isValid();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $paypalService = $this->container->get('paypal');
            $data['anid'] = "25011988445";
            if ($paypalService->pagar($data)) {
                $url = $paypalService->getUrlRetorno();

                return $this->redirect($url);
            }

            $request->getSession()
                ->getFlashBag()
                ->add('danger', 'Transacción fallida, inténtelo nuevamente más tarde o contáctenos.');
        }

        return $this->render(
            'PublicBundle:Finanzas:agregar.html.twig',
            ['form' => $form->createView()]
        );
    }

    public function pagopaypaldoneAction(Request $request)
    {
        $paypalService = $this->container->get('paypal');
        $response = $paypalService->done($request);
        $registrarMovServcie = $this->container->get('RegistroMivimento');
        $usuario = $this->getUser();

        try {
            if ($response['status'] ==  'ok' || $response['status'] ==  'captured') {
                $registrarMovServcie->registrarPagoComPaypal($usuario, [
                    'referencia' => 'REF2',
                    'monto'      => $response['payment']['total_amount'],
                    'refext'     => 'OUTRAREF',
                    'identificador_pago' => $response['payment']['details']['INVNUM']
                ]);

                $this->addFlash('success',  'Se han agregado $' . 
                                            $response['payment']['total_amount'] . 
                                            ' a sus fondos con exito.' );
                return $this->redirectToRoute('public_finanzas_index');
            } else {
                $logger = $this->get('logger');
                $logger->error("Error guardando pago. ". json_encode($response));
                $this->addFlash('danger', 'No se ha podido realizar el pago.');
                return $this->redirectToRoute('public_finanzas_agregar');
            }
        } catch(\Exception $ex) {
            $logger = $this->get('logger');
            $logger->error("Error guardando pago. ". $ex->getMessage());
            $this->addFlash('danger', 'No se ha podido realizar el pago.');
            return $this->redirectToRoute('public_finanzas_agregar');
        }
        
    }
    
    public function retiropaypaldoneAction(Request $request) {
        $paypalService = $this->container->get('paypal');
        $response = $paypalService->done($request);
        $registrarMovServcie = $this->container->get('RegistroMivimento');
        $usuario = $this->getUser();
    }
}
