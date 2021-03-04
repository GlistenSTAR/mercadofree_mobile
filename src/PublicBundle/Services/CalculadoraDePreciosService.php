<?php

namespace PublicBundle\Services;

use AdministracionBundle\Entity\Producto;
use AdministracionBundle\Entity\Pedido;
use AdministracionBundle\Entity\ComisionVenta;

class CalculadoraDePreciosService
{
    protected $container;
    protected $em;

    public function __construct($container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    public function CalcularPrecio(Pedido $pedido)
    {
        $detalle = $pedido->getDetalle();
        $comisionVentaRepositorio = $this->em->getRepository(ComisionVenta::class);
        
        $precio = ($detalle != null) ? $detalle->getPrecio() : $pedido->getSubtotal();
        $categoria = ($detalle != null) ? $detalle->getCategoria() : $pedido->getProducto()->getCategoriaid();
        $cantidad = $pedido->getCant();

        $comisionPorCategoria = $comisionVentaRepositorio->getComisionPorCategoria($categoria);
        $comisionPorRango = $comisionVentaRepositorio->getComisionPorPrecio($precio);
        
        $comision = 1;
        if (!is_null($comisionPorCategoria)) {
            $comision -= ($comisionPorCategoria->getComision() / 100);
        } elseif (!is_null($comisionPorRango)) {
            $comision -= ($comisionPorRango->getComision() / 100);
        }

        return $precio * $comision * $cantidad;
    }
}