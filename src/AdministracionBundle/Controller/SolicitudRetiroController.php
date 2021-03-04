<?php

namespace AdministracionBundle\Controller;

namespace AdministracionBundle\Controller;
use AdministracionBundle\Entity\EstadoSolicitudRetiro;
use AdministracionBundle\Entity\SolicitudRetiroFondos;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of SolicitudRetiroController
 *
 * @author Vadino
 */
class SolicitudRetiroController extends Controller{
    
    public function listarAction(Request $request)
    {
        if($request->getMethod()=='POST')
        {
            $em=$this->getDoctrine()->getManager();

            $solicitudesRetiro=$em->getRepository(SolicitudRetiroFondos::class)->findBySolicitudRetiro($request)->getResult();
            $solicitudesRetiroTotales=$em->getRepository(SolicitudRetiroFondos::class)->findBySolicitudRetiroTotal($request)->getResult();

            $listaSolicitudRetiro=[];

            /** @var SolicitudRetiroFondos $sr */
            foreach ($solicitudesRetiro as $sr)
            {
                $srArray=[];
                $srArray[]=$sr->getId();
                $srArray[]=$sr->getFecha()->format('j/n/Y H:i');
                $srArray[]=$sr->getEmailPaypal();
                $srArray[]=$sr->getMonto();
                $srArray[]=$sr->getEstadoSolicitudRetiro()->getNombre();
                $srArray['puede_reintentar_pago']=$sr->puedeReintentarPago();
                $srArray['requiere_gestion']=$sr->requiereGestion();

                $listaSolicitudRetiro[]=$srArray;
            }

            $json_data=array(
                "draw"=>intval($request->request->get('draw')),
                "recordsTotal"=>intval(count($solicitudesRetiroTotales)),
                "recordsFiltered"=>intval(count($solicitudesRetiroTotales)),
                "data"=>$listaSolicitudRetiro
            );

            return new Response(json_encode($json_data));

        }
        return $this->render('AdministracionBundle:SolicitudRetiro:listado.html.twig');
    }
    
    public function aprobarAction(Request $request) {
        $solicitudesSeleccionadas = $request->request->get('solicitudesSeleccionadas');
        
        $em=$this->getDoctrine()->getManager();
        
        $estadoSolicitudAprobada = $em->getRepository(EstadoSolicitudRetiro::class)->findEstadoAprobado();
        
        foreach($solicitudesSeleccionadas as $idSolicitud) {
            /** @var SolicitudRetiroFondos $solicitudRetiro */
            $solicitudRetiro = $em->getRepository(SolicitudRetiroFondos::class)->find($idSolicitud);
            
            if($solicitudRetiro->requiereGestion()) {
                $solicitudRetiro->setEstadoSolicitudRetiro($estadoSolicitudAprobada);
            }
            
            $em->persist($solicitudRetiro);
        }
        
        $em->flush();
        
        return new Response(json_encode([
            'solicitudes' => $solicitudesSeleccionadas
        ]));
    }
    
    public function rechazarAction(Request $request) {
        $solicitudesSeleccionadas = $request->request->get('solicitudesSeleccionadas');
        $observacionesRechazo = $request->request->get('motivo');
        
        $em=$this->getDoctrine()->getManager();
        
        $datosSolicitudesRechazadas = []; //Array con datos para envío de mails
        
        $estadoSolicitudRechazada = $em->getRepository(EstadoSolicitudRetiro::class)->findEstadoRechazado();
        
        foreach($solicitudesSeleccionadas as $idSolicitud) {
            /** @var SolicitudRetiroFondos $solicitudRetiro */
            $solicitudRetiro = $em->getRepository(SolicitudRetiroFondos::class)->find($idSolicitud);
            
            if($solicitudRetiro->requiereGestion()) {
                $solicitudRetiro->setEstadoSolicitudRetiro($estadoSolicitudRechazada);
                $solicitudRetiro->setObservacionesRechazo($observacionesRechazo);
            }
            
            $em->persist($solicitudRetiro);
            
            //Registro de movimiento de cuenta del usuario
            $registrarMovService = $this->container->get('RegistroMivimento');
            
            $registrarMovService->registrarRechazoRetiroPaypal($solicitudRetiro->getUsuario(), [
                    'referencia' => 'REF2',
                    'monto'      => $solicitudRetiro->getMonto(),
                    'refext'     => 'OUTRAREF'
                ]);
            
            //Registro datos para enviar mail
            $datosSolicitudesRechazadas[] = [
                'solicitudRetiro' => $solicitudRetiro,
                'motivo' => $observacionesRechazo
            ];
        }
        
        $em->flush();
        
        $this->notificarRechazos($datosSolicitudesRechazadas);
        
        return new Response(json_encode([
            'solicitudes' => $solicitudesSeleccionadas
        ]));
    }
    
    private function notificarRechazos($datosSolicitudesRechazadas) {
        foreach($datosSolicitudesRechazadas as $datosSolicitudRechazada) {
            
            /** @var SolicitudRetiroFondos $solicitudRetiro */
            $solicitudRetiro = $datosSolicitudRechazada['solicitudRetiro'];
            $motivo = $datosSolicitudRechazada['motivo'];
            
            //Envío de mail motivo rechazo
            $emailTemp=$this->render('AdministracionBundle:Email:mensaje_rechazo_solicitud_retiro_fondos.html.twig', array(
                                'solicitudRetiro' => $solicitudRetiro,
                                'usuario' => $solicitudRetiro->getUsuario(),
                                'motivo' => $motivo,
                        ));

            $this->get('email')->sendMail(
                                'noreply@mercadofree.com',//From
                                $solicitudRetiro->getEmailUsuario(), //To
                                "[MercadoFree] Rechazo de retiro de fondos", //Asunto
                                $emailTemp->getContent() //Body
            );
        }
    }
    
    public function detalleAction(Request $request) {
        $idSolicitud = $request->request->get('idSolicitud');
        
        $em=$this->getDoctrine()->getManager();
        
        /** @var SolicitudRetiroFondos $solicitudRetiro */
        $solicitudRetiro = $em->getRepository(SolicitudRetiroFondos::class)->find($idSolicitud);
        
        return new Response(json_encode([
            'usuario' => $solicitudRetiro->getUsuario()->getUsername(),
            'emailPaypal' => $solicitudRetiro->getEmailPaypal(),
            'monto' => $solicitudRetiro->getMonto(),
            'fecha' => $solicitudRetiro->getFecha()->format('j/n/Y'),
            'codigoRespuestaPasarela' => $solicitudRetiro->getCodRespuestaPasarela(),
            'mensajeErrorPasarela' => $solicitudRetiro->getMensajeErrorPasarela(),
            'referenciaPasarela' => $solicitudRetiro->getReferenciaPasarela()
        ]));
    }
    
    public function reintentarPagoAction(Request $request) {
        $solicitudesSeleccionadas = $request->request->get('solicitudesSeleccionadas');
        
        $em=$this->getDoctrine()->getManager();
        
        $estadoSolicitudAprobada = $em->getRepository(EstadoSolicitudRetiro::class)->findEstadoAprobado();
        
        foreach($solicitudesSeleccionadas as $idSolicitud) {
            /** @var SolicitudRetiroFondos $solicitudRetiro */
            $solicitudRetiro = $em->getRepository(SolicitudRetiroFondos::class)->find($idSolicitud);
            
            if($solicitudRetiro->puedeReintentarPago()) {
                $solicitudRetiro->setEstadoSolicitudRetiro($estadoSolicitudAprobada);
            }
            
            $em->persist($solicitudRetiro);
        }
        
        $em->flush();
        
        return new Response(json_encode([
            'solicitudes' => $solicitudesSeleccionadas
        ]));
    }
}
