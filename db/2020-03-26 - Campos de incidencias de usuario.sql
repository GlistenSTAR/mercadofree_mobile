ALTER TABLE configuracion ADD COLUMN max_incidencias_contacto INT(11) NULL;
UPDATE configuracion SET max_incidencias_contacto = 1;
ALTER TABLE configuracion MODIFY max_incidencias_contacto INT(11) NOT NULL;

ALTER TABLE usuario ADD COLUMN incidencias_contacto INT(11) NULL;