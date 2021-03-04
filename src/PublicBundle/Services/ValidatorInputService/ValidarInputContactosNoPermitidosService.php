<?php

namespace PublicBundle\Services\ValidatorInputService;

use PublicBundle\Services\ValidatorInputService\ValidatorInputService;
use AppBundle\Services\EmailService;
use AdministracionBundle\Repository\ConfiguracionRepository;
use AdministracionBundle\Repository\UsuarioRepository;
use AdministracionBundle\Entity\Cliente;
use AdministracionBundle\Entity\Configuracion;
use AdministracionBundle\Entity\Usuario;

/**
 * Clase para validar que un input no tiene datos de contacto externos a la plataforma
 * 
 * Se valida que dentro de los datos enviados no se manden datos como ser:
 * - mail
 * - telefono
 * - web
 *
 * @author Vadino
 */
class ValidarInputContactosNoPermitidosService {
    
    private $validatorInputService;
    private $configuracionRepository;
    private $usuarioRepository;
    private $emailService;
    private $templatingService;
    
    public function __construct(
            ValidatorInputService $validatorInputService, 
            ConfiguracionRepository $configuracionRepository,
            UsuarioRepository $usuarioRepository,
            EmailService $emailService,
            $templatingService
            ) {
        $this->validatorInputService = $validatorInputService;
        $this->configuracionRepository = $configuracionRepository;
        $this->usuarioRepository = $usuarioRepository;
        $this->emailService = $emailService;
        $this->templatingService = $templatingService;
    }
    
    public function execute(Usuario $usuario, $datos) {
        
        $datosValidos = $this->validarDatosIngresados($datos);
        
        if(!$datosValidos) {
            
            //sumar una incidencia al usuario
            $usuario->addIncidenciaContacto();
            $incidenciasUsuario = $usuario->getIncidenciasContacto();
            
            /** @var Configuracion $configuracion */
            $configuracion = $this->configuracionRepository->getDefaultConfiguracion();
            
            //validar que el usuario no haya alcanzado la cantidad maxima de incidencias
            //sino, enviar mail de notificación al administrador
            if( $incidenciasUsuario >= $configuracion->getMaximoIncidenciasContacto()) {
                
                /** @var Cliente $cliente */
                $cliente = $usuario->getClienteid();
                
                $nombreUsuario = $cliente? $cliente->getNombre()." " .$cliente->getApellidos(): "(no cliente)";
                $emailUsuario = $usuario->getEmail();
                
                $emailAdministrador = $configuracion->getEmailAdministrador();
                        
                $body = $this->templatingService->render('PublicBundle:Email:mensaje_email_incidencias_contacto.html.twig',array(
				'nombreUsuario' => $nombreUsuario,
				'emailUsuario' => $emailUsuario,
				'incidenciasUsuario' => $incidenciasUsuario
                ));
                
                //envio mail
                $this->emailService->sendMail('noreply@mercadofree.com', $emailAdministrador, 'Incidencias contacto', $body);
                
                $usuario->resetIncidenciasContacto();
            }
            
            $this->usuarioRepository->persistAndFlush($usuario);
        }
        
        return $datosValidos;
        
    }
    
    private function validarDatosIngresados($datos) {
        $datosValidos = true;
        
        foreach($datos as $string) {
            if(!$this->validatorInputService->validar($string, ValidatorInputService::VALIDATE_EMAIL)) {
                $datosValidos =  false;
                break;
            }

            if(!$this->validatorInputService->validar($string, ValidatorInputService::VALIDATE_PHONE)) {
                $datosValidos = false;
                break;
            }

            if(!$this->validatorInputService->validar($string, ValidatorInputService::VALIDATE_WEB)) {
                $datosValidos = false;
                break;
            }
        }
        
        return $datosValidos;
    }
}
