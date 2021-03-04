CREATE TABLE historico_estado_pedido (
  id INT NOT NULL AUTO_INCREMENT,
  fecha DATETIME NOT NULL,
  pedido_id INT NOT NULL,
  estado_pedido_id INT NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_historico_estado_pedido_pedido
    FOREIGN KEY (pedido_id)
    REFERENCES pedido (id),
  CONSTRAINT fk_historico_estado_pedido_estado_pedido
    FOREIGN KEY (estado_pedido_id)
    REFERENCES estado_pedido (id)
);