ALTER TABLE configuracion ADD COLUMN tiempo_expiracion_publicaciones INT(11) NULL;
UPDATE configuracion SET tiempo_expiracion_publicaciones = 30;
ALTER TABLE configuracion MODIFY tiempo_expiracion_publicaciones INT(11) NOT NULL;

ALTER TABLE categoria ADD COLUMN tiempo_expiracion_publicaciones INT(11) NULL;

ALTER TABLE producto ADD COLUMN fecha_expiracion date;