ALTER TABLE pedido ADD COLUMN borrado_vendedor tinyint(1) NOT NULL DEFAULT false;
ALTER TABLE pedido ADD COLUMN borrado_comprador tinyint(1) NOT NULL DEFAULT false;

ALTER TABLE producto ADD COLUMN borrado tinyint(1) NOT NULL DEFAULT false;