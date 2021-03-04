ALTER TABLE configuracion ADD COLUMN limite_dias_valoracion_pedido INT NULL;

CREATE TABLE opcion_valoracion_tiempo_entrega_pedido (
  id INT NOT NULL AUTO_INCREMENT,
  detalle VARCHAR(255) NOT NULL,
  valor INT NOT NULL,
  PRIMARY KEY (id));

INSERT INTO opcion_valoracion_tiempo_entrega_pedido (id, detalle, valor) VALUES (1, 'Ha demorado mucho', 1);
INSERT INTO opcion_valoracion_tiempo_entrega_pedido (id, detalle, valor) VALUES (2, 'Aceptable', 2);
INSERT INTO opcion_valoracion_tiempo_entrega_pedido (id, detalle, valor) VALUES (3, 'Rápido', 3);
INSERT INTO opcion_valoracion_tiempo_entrega_pedido (id, detalle, valor) VALUES (4, 'Muy Rápido (24Hrs o menos)', 4);

CREATE TABLE opcion_valoracion_calidad_producto_pedido (
  id INT NOT NULL AUTO_INCREMENT,
  detalle VARCHAR(255) NOT NULL,
  valoracion INT NOT NULL,
  PRIMARY KEY (id));

CREATE TABLE valoracion_pedido (
  id INT NOT NULL AUTO_INCREMENT,
  opcion_valoracion_tiempo_entrega_pedido_id INT NOT NULL,
  opcion_valoracion_calidad_producto_pedido_id INT NOT NULL,
  compra_aceptada TINYINT NOT NULL,
  motivo_rechazo VARCHAR(255) NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_valoracion_pedido_opcion_valoracion_tiempo_entrega_pedido
    FOREIGN KEY (opcion_valoracion_tiempo_entrega_pedido_id)
    REFERENCES opcion_valoracion_tiempo_entrega_pedido (id),
  CONSTRAINT fk_valoracion_pedido_opcion_valoracion_calidad_producto_pedido
    FOREIGN KEY (opcion_valoracion_calidad_producto_pedido_id)
    REFERENCES opcion_valoracion_calidad_producto_pedido (id));

INSERT INTO opcion_valoracion_calidad_producto_pedido (id, detalle, valoracion) VALUES (1, 'Muy Malo', 1);
INSERT INTO opcion_valoracion_calidad_producto_pedido (id, detalle, valoracion) VALUES (2, 'Malo', 2);
INSERT INTO opcion_valoracion_calidad_producto_pedido (id, detalle, valoracion) VALUES (3, 'Regular', 3);
INSERT INTO opcion_valoracion_calidad_producto_pedido (id, detalle, valoracion) VALUES (4, 'Bueno', 4);
INSERT INTO opcion_valoracion_calidad_producto_pedido (id, detalle, valoracion) VALUES (5, 'Muy Bueno', 5);

ALTER TABLE pedido ADD COLUMN valoracion_pedido_id INT NULL;
ALTER TABLE pedido ADD CONSTRAINT fk_pedido_valoracion_pedido FOREIGN KEY (valoracion_pedido_id) REFERENCES valoracion_pedido (id);

INSERT INTO concepto_movimiento_cuenta (slug, nombre) VALUES ('confirmacion-venta-pedido', 'Confirmación de venta realizada');
INSERT INTO concepto_movimiento_cuenta (slug, nombre) VALUES ('comision-venta-mercadofree', 'Comisión sobre venta de Mercadofree');

INSERT INTO estado_pedido (nombre, slug) VALUES ('SOLICITADO DEVOLUCIÓN', 'solicitado-devolucion');