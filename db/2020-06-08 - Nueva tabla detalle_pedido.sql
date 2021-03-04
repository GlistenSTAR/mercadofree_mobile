CREATE TABLE detalle_pedido (
  id INT NOT NULL AUTO_INCREMENT,
  pedido_id INT NOT NULL,
  nombre VARCHAR(255) NULL,
  descripcion VARCHAR(255) NULL,
  precio DECIMAL(10,2) NULL,
  cuotas_pagar INT NULL,
  peso DECIMAL(10,2) NULL,
  ancho INT NULL,
  alto INT NULL,
  profundidad INT NULL,
  categoria_id INT NOT NULL,
  garantia LONGTEXT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_detalle_pedido_pedido FOREIGN KEY (pedido_id) REFERENCES pedido (id),
  CONSTRAINT fk_detalle_pedido_categoria FOREIGN KEY (categoria_id) REFERENCES categoria (id)
)