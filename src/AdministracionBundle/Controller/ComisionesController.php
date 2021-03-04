<?php


namespace AdministracionBundle\Controller;

use AdministracionBundle\Entity\Coleccion;
use AdministracionBundle\Entity\ComisionVenta;
use AdministracionBundle\Entity\Categoria;
use AdministracionBundle\Entity\Configuracion;
use AdministracionBundle\Entity\Contacto;
use AdministracionBundle\Entity\Imagen;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Email;

use Symfony\Component\HttpFoundation\Response;


class ComisionesController extends Controller
{
    /**
     * Despliega la vista y ejecuta el codigo javascript necesario para las funcionalidades 
     */
    public function indexAction(Request $request)
    {
        return $this->render('AdministracionBundle:Comision:index.html.twig');
    }

    /**
     * Datos necesarios para incializar la vista
     */
    public function controlAction(Request $request)
    {
        $response = [];
        $em = $this->getDoctrine()->getManager();
        $comisiones = $em->getRepository(ComisionVenta::class)->findAll();
        $categorias = $em->getRepository(Categoria::class)->findAll();

        $response['list'] = array_map(
            function ($e) {
                $response = $e->getAttributes();
                $response['url_editar'] = $this->generateUrl('administracion_comisiones_guardar', ['id' => $e->getId()]);
                return $response;
            },
            $comisiones
        );
        $response['categorias'] =  array_map(
            function ($e) {
                return [
                    'id' => $e->getId(),
                    'nombre' => $e->getNombre(),
                ];
            },
            $categorias
        );
        return new JsonResponse($response);
    }

    /**
     * Crea/Edita una comision de ventas
     */
    public function guardarAction(Request $request, $id = null)
    {
        $response = [];
        $em = $this->getDoctrine()->getManager();
        $content = $request->getContent();
        $parameters = json_decode($content, true);
        $comision = new ComisionVenta;
        $parameters['categoria'] = ($parameters['tipo'] == 2) ?
            $em->getRepository(Categoria::class)->find($parameters['categoria']) :
            null;
        if (!$parameters) {
            $response = ['msj' => 'Parametros Incorrectos'];
            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
        }
        

        $comisionVentaRepositorio = $em->getRepository(ComisionVenta::class);
        if (isset($id)) {
            $comision = $comisionVentaRepositorio->find($id);
            if (!$comision) {
                $response = ['msj' => 'Recurso no encontrado'];
                return new JsonResponse($response, Response::HTTP_NOT_FOUND);
            }
        }

        $comision->setAttributes($parameters);
        if (!$comision->isValid()) {
            $response = $comision->getErrors();
            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
        }

        if ($parameters['tipo'] == 2) {
            if ($comisionVentaRepositorio->duplicadoPorCategoria($comision)) {
                return new JsonResponse(
                    ['categoria' => ['Existe una comisión registrada en esa misma categoria']],
                    Response::HTTP_BAD_REQUEST
                );
            }
        } else {
            if ($comisionVentaRepositorio->duplicadoPorRango($comision)) {
                return new JsonResponse(
                    [
                        'precio_minimo' => ['Existe una comisión registrada en este rango de precios'],
                        'precio_maximo' => ['Existe una comisión registrada en este rango de precios'],
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }
        
        $em->persist($comision);
        $em->flush();
        $response = $comision->getAttributes();
        $response['url_editar'] = $this->generateUrl('administracion_comisiones_guardar', ['id' => $comision->getId()]);
        $response['url_eliminar'] = $this->generateUrl('administracion_comisiones_eliminar', ['id' => $comision->getId()]);
        return new JsonResponse($response);
    }

    /**
     * Elimina una comision de venta
     */
    public function eliminarAction(Request $request)
    {
        $response = [];
        $em = $this->getDoctrine()->getManager();
        $content = $request->getContent();
        $parameters = json_decode($content, true);
        $comisiones = $em->getRepository(ComisionVenta::class)->findById($parameters['ids']);

        if (!empty($comisiones)) {
            foreach ($comisiones as $comision) {
                $em->remove($comision);                
            }
            $em->flush();
        }

        return new JsonResponse([
            'msj' =>  'ok'
        ]);
    }
}
