ALTER TABLE configuracion ADD COLUMN aprobar_automaticamente_retiros TINYINT(1) NULL;
ALTER TABLE solicitud_retiro_fondos MODIFY COLUMN referencia_pasarela TEXT NULL;
ALTER TABLE solicitud_retiro_fondos MODIFY COLUMN cod_respuesta_pasarela VARCHAR(15) NULL;

INSERT INTO estado_solicitud_retiro VALUES (6,'PAGO PAYPAL PENDIENTE', 'pago-paypal-pendiente');