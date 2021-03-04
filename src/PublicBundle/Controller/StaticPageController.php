<?php


namespace PublicBundle\Controller;


use AdministracionBundle\Entity\Configuracion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StaticPageController extends Controller
{
	public function contactarAction(Request $request){

		if($request->getMethod()=='POST'){
			$em=$this->getDoctrine()->getManager();

			$conf=$em->getRepository(Configuracion::class)->findAll()[0];

			//Obtener datos del form

			$nombre=$request->request->get('nombre');
			$apellidos=$request->request->get('apellidos');
			$email=$request->request->get('email');
			$message=$request->request->get('message');
			$files = $request->files->get("files");

			$emailTemp=$this->render('PublicBundle:Email:mensaje_email_contacto.html.twig',array(
				'nombre' => $nombre,
				'apellidos' => $apellidos,
				'email' => $email,
				'message' => $message
			));

			//enviar email

			$enviado=$this->get('email')->sendMail(
				$email,//From
				$conf->getContactoConfiguracionId()->getEmail(), //To
				"[MercadoFree] Mensaje de contacto", //Asunto
				$emailTemp->getContent(), //Body
                $files
			);

			if($enviado){
				$this->get('session')->getFlashBag()->add('info','Gracias por contactarnos, te reponderemos lo antes posible');
			}
			else{
				$this->get('session')->getFlashBag()->add('error','Ha ocurrido un error en el sistema y no hemos podido enviar tu mensaje, intentalo de nuevo mas tarde');
			}

			return $this->redirect($this->generateUrl('public_static_contactar'));
		}

		return $this->render('PublicBundle:StaticPage:contactar.html.twig');
	}

	public function politicasAction(){
		return $this->render('PublicBundle:StaticPage:politica_privacidad.html.twig');
	}

	public function terminosAction(){
		return $this->render('PublicBundle:StaticPage:terminos_condiciones.html.twig');
	}

	public function aboutAction(){
		return $this->render('PublicBundle:StaticPage:about.html.twig');
	}
}