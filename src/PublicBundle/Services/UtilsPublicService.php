<?php

namespace PublicBundle\Services;

use AdministracionBundle\Entity\Configuracion;
use AdministracionBundle\Entity\Usuario;
use Proxies\__CG__\AdministracionBundle\Entity\Imagen;
use Symfony\Component\HttpFoundation\JsonResponse;

class UtilsPublicService
{
    protected $container;
    protected $em;

    public function __construct($container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    public function getUsuarioLogueado()
    {
        $usuario = $this->container->get('security.context')->getToken()->getUser();
//        echo $usuario;die;

        if (is_object($usuario)) {
            return $this->em->getRepository('AdministracionBundle:Usuario')->find($usuario->getId());
        } else {
            return null;
        }

    }

    public function sendMail($from, $to, $subject, $body, $type = null)
    {
        $mail = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from, '')
            ->setTo($to)
            //->setContentType('MIME-Version: 1.0;Content-type: text/plain; charset= iso-8859-1')
            ->addPart($body, 'text/html')
        ;
//        $headers = $mail->getHeaders()->addTextHeader('')
        /*setBody(
                $this->render('PublicBundle:Templates:emailTemplate.html.twig', array('name'=>'Pedro')),'text/html'
            );

            /**/

        if ($type != null && $type == 'instant') {
            try {
                $this->container->get('instant_mailer')->send($mail);
            } catch (Exception $e) {
                return false;
            }

            return true;
        } else {
            try {
                $this->container->get('mailer')->send($mail);
            } catch (Exception $e) {
                return false;
            }
            return true;
        }


    }

    public function procesarFotoProductoInServer($foto, $producto){
        $newName = "";
        if($foto !== null){
            $newName = $producto->getId() . md5(time() + rand(0, 1000)) . '.jpg';
            $archivoSubido = $this->container->getParameter('uploads.images.temp') . $foto;
            if (file_exists($archivoSubido)) {
                $renamed = $this->container->getParameter('uploads.images.productos') . $newName;

                rename($archivoSubido, $renamed);

                $imagen = new Imagen();

                $imagen->setUrl($newName);

                $imagen->setProductoid($producto);

                $this->em->persist($imagen);
                $this->em->flush();
                return array('success' => true, 'url'=>$imagen->getUrl(), 'id' => $imagen->getId());
            }
        }else{
            return array('success' => false);
        }
    }

    public function procesarFotoProducto($foto, $producto, $destacada)
    {

        $newName = "";
        if ($foto != null) {


            $newName = $producto->getId() . md5(time() + rand(0, 1000)) . '.jpg';
            $archivoSubido = $this->container->getParameter('uploads.images.temp') . $foto;

            if (file_exists($archivoSubido)) {

                $renamed = $this->container->getParameter('uploads.images.productos') . $newName;

                rename($archivoSubido, $renamed);


                //crear el obj imagen

                $imagen = new Imagen();

                $imagen->setUrl($newName);

                $imagen->setProductoid($producto);



                if ($destacada !== '') {
                    $fotos = $producto->getImagenes();
                    foreach ($fotos as $f) {
                        $f->setDestacada(0);
                        if($f->getUrl() === $destacada){
                            $f->setDestacada(1);
                        }
                    }
                    if($destacada === $archivoSubido){
                        $imagen->setDestacada(1);
                    }
                }
                $this->em->persist($imagen);
                $this->em->flush();
            }
        }
    }

    public function procesarFotoTienda($foto, $tienda, $indicador)
    {
        $newName = "";

        if ($foto != null) {
            $newName = $tienda->getId() . md5(time() + rand(0, 1000)) . '.jpg';

            if (file_exists($this->container->getParameter('uploads.images.temp') . $foto)) {
                rename($this->container->getParameter('uploads.images.temp') . $foto, $this->container->getParameter('uploads.images.tiendas') . $newName);

                //crear el obj imagen

                $imagen = new Imagen();

                $imagen->setUrl($newName);

                $imagen->setTiendaid($tienda);

                if ($indicador == 'logo') {
                    $imagen->setLogo(1);
                } else {
                    $imagen->setPortada(1);
                }

                $this->em->persist($imagen);

            }
        }
    }

    public function procesarFichero($fichero, $usuario)
    {
        $newName = "";

        if ($fichero != null) {
            $extension = explode(".", $fichero);
            if ($extension[1] == "jpg") {
                $newName = $usuario->getId() . md5(time() + rand(0, 1000)) . '.jpg';
            } else
                $newName = $usuario->getId() . md5(time() + rand(0, 1000)) . '.pdf';


            if (file_exists($this->container->getParameter('uploads.images.temp') . $fichero)) {
                rename($this->container->getParameter('uploads.images.temp') . $fichero, $this->container->getParameter('uploads.images.usuarios') . $newName);

                //crear el obj imagen

                $imagen = new Imagen();

            }

        }

        return $newName;
    }

    public function UsuarioVacio(Usuario $usuario)
    {
        $usuarioFlag = true;

        if ($usuario) {
            if ($usuario->getTelefono() == "") {
                $usuarioFlag = false;
            }
            if ($usuarioFlag == true) {
                foreach ($usuario->getDireccion() as $direc) {
                    $valor = $direc->getDireccionVenta();
                    if ($valor == 1) {
                        $usuarioFlag = true;
                    } else {
                        $usuarioFlag = false;
                    }
                }
                if ($usuario->getDireccion()[0] == null) {
                    $usuarioFlag = false;
                }

            }
            if ($usuarioFlag == true) {
                if ($usuario->getEmpresaid()) {
                    if ($usuario->getEmpresaid()->getCuit() == "" || $usuario->getEmpresaid()->getRazonSocial() == "") {
                        $usuarioFlag = false;
                    }

                }
            }
            if ($usuarioFlag == true) {
                if ($usuario->getClienteid()) {
                    if ($usuario->getClienteid()->getDni() == "" || $usuario->getClienteid()->getNombre() == "" || $usuario->getClienteid()->getApellidos() == "") {
                        $usuarioFlag = false;
                    }
                }
            }

        }

        return $usuarioFlag;
    }

    public function generateSlug($texto)
    {
        $slug = strtolower($texto);
        $slug = str_replace(" ", "-", $slug);
        $slug = str_replace("á", "a", $slug);
        $slug = str_replace("é", "e", $slug);
        $slug = str_replace("í", "i", $slug);
        $slug = str_replace("ó", "o", $slug);
        $slug = str_replace("ú", "u", $slug);
        $slug = str_replace("ñ", "nn", $slug);
        $slug = str_replace("/", "-", $slug);

        return $slug;
    }

    public function getCostoEnvio(Usuario $usuario = null, $idProducto)
    {
        $producto = $this->em->getRepository('AdministracionBundle:Producto')->find($idProducto);

	    $costoSelected = null;

        if($producto != null){
        	// Obtener costos de envio para envio a domicilio

			$vendedor = $producto->getUsuarioid();

	        $confCosto = array(
	            'direccion' => false,
		        'dimensiones' => false
	        );

	        $direccionCompra = ($usuario!=null)?$usuario->getDireccionCompra():null;

	        $costosEnvio = $this->em->getRepository('AdministracionBundle:CostoEnvio')->findBy(array(
	        	'usuarioid' => $vendedor->getId(),
	        ));

	        if($costosEnvio != null){
				/* Recorrer todos los costos de envio e ir aplicando filtros de costos de acuerdo a
		         direccion del cliente, o dimensiones del producto, o ambos */

		        foreach ( $costosEnvio as $ce ) {
			        // Primero verificamos por la direccionn de envio del cliente

					if(!$ce->isTodoElPais()){

						// Comparamos para ver si aplica

						if($direccionCompra != null && $ce->getProvinciaid() != null && $ce->getCiudadid() != null){

							if($direccionCompra->getProvincia()!=null && $ce->getProvinciaid()->getId() == $direccionCompra->getProvincia()->getId() &&
							   $direccionCompra->getCiudad()!=null && $ce->getCiudadid()->getId() == $direccionCompra->getCiudad()->getId()){

								// Ha pasado el filtro de la direccion

								$confCosto['direccion']=true;
							}

						}
						else if($direccionCompra != null && $ce->getProvinciaid() != null && $ce->getCiudadid() == null){

							if($direccionCompra->getProvincia()!=null && $ce->getProvinciaid()->getId() == $direccionCompra->getProvincia()->getId()){

								// Ha pasado el filtro de la direccion

								$confCosto['direccion']=true;
							}

						}
						else if($direccionCompra != null && $ce->getProvinciaid() == null && $ce->getCiudadid() == null){
							// Marcamos como fltro de direccion pasado, porque la conf de costo actual, no tiene filtros por direccion

							$confCosto['direccion']=true;
						}
						else if($direccionCompra == null && $ce->getProvinciaid() == null && $ce->getCiudadid() == null){
							// Marcamos como fltro de direccion pasado, porque la conf de costo actual, no tiene filtros por direccion

							$confCosto['direccion']=true;
						}


						if($confCosto['direccion']){
							// Verificamos segun filtros de dimensiones

							if($ce->getPeso()!=null && $ce->getPeso()>0 && $ce->getPeso() >= $producto->getPeso()){
								$confCosto['peso']=true;
							}
							else if($ce->getPeso()!=null && $ce->getPeso()>0){
								$confCosto['peso']=false;
							}
							else{
								$confCosto['peso']=true;
							}

							if($ce->getAlto()!=null && $ce->getAlto()>0 && $ce->getAlto() >= $producto->getAlto()){
								$confCosto['alto']=true;
							}
							else if($ce->getAlto()!=null && $ce->getAlto()>0){
								$confCosto['alto']=false;
							}
							else{
								$confCosto['alto']=true;
							}

							if($ce->getAncho()!=null && $ce->getAncho()>0 && $ce->getAncho() >= $producto->getAncho()){
								$confCosto['ancho']=true;
							}
							else if($ce->getAncho()!=null && $ce->getAncho()>0){
								$confCosto['ancho']=false;
							}
							else{
								$confCosto['ancho']=true;
							}

							if($ce->getProfundidad()!=null && $ce->getProfundidad()>0 && $ce->getProfundidad() >= $producto->getProfundidad()){
								$confCosto['profundidad']=true;
							}
							else if($ce->getProfundidad()!=null && $ce->getProfundidad()>0){
								$confCosto['profundidad']=false;
							}
							else{
								$confCosto['profundidad']=true;
							}

							if($confCosto['peso'] && $confCosto['alto'] && $confCosto['ancho'] && $confCosto['profundidad']){
								$confCosto['dimensiones'] = true;
							}
						}

						if($confCosto['direccion'] && $confCosto['dimensiones']){
							$costoSelected = $ce;
							break;
						}

					}
				}
	        }

	        /* En caso que no tenga restricciones de costo de envio, o que los parametros e la compra no cumplan
	         con dichas restricciones.
	        */

	        if($costoSelected == null || $costosEnvio == null){
	        	// Buscar si tiene envio para all the country

		        $envioAllCountry = $this->em->getRepository('AdministracionBundle:CostoEnvio')->findOneBy(array(
			        'usuarioid' => $vendedor->getId(),
			        'todoElPais' => true
		        ));

		        $costoSelected = $envioAllCountry;
	        }


        }

        return ($costoSelected!=null) ? $costoSelected->getCosto() : -1;
    }

    public function getEmailAdmin()
    {
        $conf = $this->em->getRepository(Configuracion::class)->findAll();
        if( ! is_null($conf) ){
            $conf = $conf[0];

            return ($conf->getEmailAdministrador() != null && $conf->getEmailAdministrador() != '') ?
                $conf->getEmailAdministrador() : 'noreply@mercadofree.com.ar';
        }
        else{
            return 'noreply@mercadofree.com.ar';
        }

    }


}