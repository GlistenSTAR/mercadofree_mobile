INSERT 
INTO detalle_pedido (pedido_id, nombre, descripcion, precio, cuotas_pagar, peso, ancho, alto, profundidad, categoria_id, garantia) 
    SELECT ped.id as pedido_id, pro.nombre as nombre, pro.descripcion as descripcion, pro.precio as precio, pro.cuotasPagar as cuotas_pagar, pro.peso as peso, pro.ancho as ancho, pro.alto as alto, pro.profundidad as profundidad, pro.categoriaId as categoria_id, pro.garantia as garantia FROM pedido ped join producto pro ON ped.producto_id = pro.id where ped.id not in (SELECT pedido_id FROM detalle_pedido)