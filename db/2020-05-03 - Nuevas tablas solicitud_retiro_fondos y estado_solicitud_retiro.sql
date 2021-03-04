-- Creamos la nueva tabla de estado_solicitud_retiro
CREATE TABLE estado_solicitud_retiro
(
    id INT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL
);

ALTER TABLE estado_solicitud_retiro
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY (slug);

INSERT INTO estado_solicitud_retiro VALUES (1,'PENDIENTE', 'pendiente');
INSERT INTO estado_solicitud_retiro VALUES (2,'APROBADO', 'aprobado');
INSERT INTO estado_solicitud_retiro VALUES (3,'RECHAZADO', 'rechazado');
INSERT INTO estado_solicitud_retiro VALUES (4,'PAGO FALLIDO', 'pago-fallido');
INSERT INTO estado_solicitud_retiro VALUES (5,'PAGO REALIZADO', 'pago-realizado');

-- Creamos la nueva tabla de solicitud_retiro_fondos
CREATE TABLE solicitud_retiro_fondos
(
    id INT NOT NULL,
    email_paypal VARCHAR(255) NOT NULL,
    monto DOUBLE NOT NULL,
    fecha DATETIME NOT NULL,
    observaciones_rechazo VARCHAR(255) NULL,
    cod_respuesta_pasarela VARCHAR(10) NULL,
    mensaje_error_pasarela VARCHAR(255) NULL,
    referencia_pasarela VARCHAR(255) NULL,
    id_usuario INT NOT NULL,
    id_estado_solicitud_retiro INT NOT NULL
);

ALTER TABLE solicitud_retiro_fondos ADD PRIMARY KEY (id);
ALTER TABLE solicitud_retiro_fondos ADD CONSTRAINT FOREIGN KEY (id_usuario) REFERENCES usuario (id);
ALTER TABLE solicitud_retiro_fondos ADD CONSTRAINT FOREIGN KEY (id_estado_solicitud_retiro) REFERENCES estado_solicitud_retiro (id);

ALTER TABLE solicitud_retiro_fondos MODIFY id int NOT NULL AUTO_INCREMENT;

INSERT INTO concepto_movimiento_cuenta VALUES (4, 'rechazo-retiro-paypal', 'Rechazo Retiro Paypal');