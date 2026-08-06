-- FitControl: procedimientos del módulo Pagos.
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_pagos_filtrar$$
CREATE PROCEDURE sp_pagos_filtrar(IN p_texto VARCHAR(150),IN p_cliente_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_metodo_id BIGINT UNSIGNED,IN p_desde DATE,IN p_hasta DATE,IN p_limite INT UNSIGNED,IN p_offset INT UNSIGNED)
BEGIN
 IF p_limite IS NULL OR p_limite<1 OR p_limite>100 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El límite debe estar entre 1 y 100'; END IF;
 SELECT p.id,p.numero_recibo,p.cliente_id,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente,p.metodo_pago_id,mp.nombre metodo,mp.requiere_referencia,p.estado_pago_id,e.codigo estado_codigo,e.nombre estado,e.es_terminal,p.monto,p.moneda,p.referencia,p.referencia_externa,p.pagado_at,p.observaciones,p.created_at,
 COALESCE(SUM(a.monto_aplicado),0) monto_aplicado,p.monto-COALESCE(SUM(a.monto_aplicado),0) saldo_disponible
 FROM pagos p JOIN clientes c ON c.id=p.cliente_id JOIN metodos_pago mp ON mp.id=p.metodo_pago_id JOIN estados_pago e ON e.id=p.estado_pago_id LEFT JOIN aplicaciones_pago a ON a.pago_id=p.id
 WHERE (p_texto IS NULL OR p_texto='' OR p.numero_recibo LIKE CONCAT('%',p_texto,'%') OR c.numero_socio LIKE CONCAT('%',p_texto,'%') OR c.nombre LIKE CONCAT('%',p_texto,'%') OR c.apellido LIKE CONCAT('%',p_texto,'%') OR p.referencia LIKE CONCAT('%',p_texto,'%'))
 AND (p_cliente_id IS NULL OR p.cliente_id=p_cliente_id) AND (p_estado_id IS NULL OR p.estado_pago_id=p_estado_id) AND (p_metodo_id IS NULL OR p.metodo_pago_id=p_metodo_id)
 AND (p_desde IS NULL OR DATE(COALESCE(p.pagado_at,p.created_at))>=p_desde) AND (p_hasta IS NULL OR DATE(COALESCE(p.pagado_at,p.created_at))<=p_hasta)
 GROUP BY p.id,c.numero_socio,c.nombre,c.apellido,mp.nombre,mp.requiere_referencia,e.codigo,e.nombre,e.es_terminal ORDER BY COALESCE(p.pagado_at,p.created_at) DESC,p.id DESC LIMIT p_limite OFFSET p_offset;
END$$
DROP PROCEDURE IF EXISTS sp_pagos_contar$$
CREATE PROCEDURE sp_pagos_contar(IN p_texto VARCHAR(150),IN p_cliente_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_metodo_id BIGINT UNSIGNED,IN p_desde DATE,IN p_hasta DATE)
BEGIN SELECT COUNT(*) total FROM pagos p JOIN clientes c ON c.id=p.cliente_id WHERE (p_texto IS NULL OR p_texto='' OR p.numero_recibo LIKE CONCAT('%',p_texto,'%') OR c.numero_socio LIKE CONCAT('%',p_texto,'%') OR c.nombre LIKE CONCAT('%',p_texto,'%') OR c.apellido LIKE CONCAT('%',p_texto,'%') OR p.referencia LIKE CONCAT('%',p_texto,'%')) AND (p_cliente_id IS NULL OR p.cliente_id=p_cliente_id) AND (p_estado_id IS NULL OR p.estado_pago_id=p_estado_id) AND (p_metodo_id IS NULL OR p.metodo_pago_id=p_metodo_id) AND (p_desde IS NULL OR DATE(COALESCE(p.pagado_at,p.created_at))>=p_desde) AND (p_hasta IS NULL OR DATE(COALESCE(p.pagado_at,p.created_at))<=p_hasta); END$$
DROP PROCEDURE IF EXISTS sp_pagos_obtener$$
CREATE PROCEDURE sp_pagos_obtener(IN p_id BIGINT UNSIGNED)
BEGIN IF NOT EXISTS(SELECT 1 FROM pagos WHERE id=p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El pago no existe'; END IF;
 SELECT p.*,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente,mp.nombre metodo,mp.requiere_referencia,e.codigo estado_codigo,e.nombre estado,e.es_terminal,COALESCE(SUM(a.monto_aplicado),0) monto_aplicado,p.monto-COALESCE(SUM(a.monto_aplicado),0) saldo_disponible FROM pagos p JOIN clientes c ON c.id=p.cliente_id JOIN metodos_pago mp ON mp.id=p.metodo_pago_id JOIN estados_pago e ON e.id=p.estado_pago_id LEFT JOIN aplicaciones_pago a ON a.pago_id=p.id WHERE p.id=p_id GROUP BY p.id,c.numero_socio,c.nombre,c.apellido,mp.nombre,mp.requiere_referencia,e.codigo,e.nombre,e.es_terminal; END$$
DROP PROCEDURE IF EXISTS sp_pagos_historial$$
CREATE PROCEDURE sp_pagos_historial(IN p_id BIGINT UNSIGNED) BEGIN SELECT h.id,ea.nombre estado_anterior,en.nombre estado_nuevo,h.motivo,h.cambiado_por,h.cambiado_at FROM historial_estados_pago h LEFT JOIN estados_pago ea ON ea.id=h.estado_anterior_id JOIN estados_pago en ON en.id=h.estado_nuevo_id WHERE h.pago_id=p_id ORDER BY h.cambiado_at DESC,h.id DESC; END$$
DROP PROCEDURE IF EXISTS sp_pagos_aplicaciones$$
CREATE PROCEDURE sp_pagos_aplicaciones(IN p_id BIGINT UNSIGNED) BEGIN SELECT a.id,a.cargo_cobro_id,c.numero_cargo,c.concepto,c.total,a.monto_aplicado,a.created_at FROM aplicaciones_pago a JOIN cargos_cobro c ON c.id=a.cargo_cobro_id WHERE a.pago_id=p_id ORDER BY a.created_at DESC; END$$
DROP PROCEDURE IF EXISTS sp_pagos_cargos_disponibles$$
CREATE PROCEDURE sp_pagos_cargos_disponibles(IN p_pago_id BIGINT UNSIGNED)
BEGIN DECLARE v_cliente BIGINT UNSIGNED; DECLARE v_moneda CHAR(3); SELECT cliente_id,moneda INTO v_cliente,v_moneda FROM pagos WHERE id=p_pago_id; IF v_cliente IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El pago no existe'; END IF;
 SELECT c.id,c.numero_cargo,c.concepto,c.total,c.moneda,COALESCE(SUM(a.monto_aplicado),0) pagado,c.total-COALESCE(SUM(a.monto_aplicado),0) saldo FROM cargos_cobro c LEFT JOIN aplicaciones_pago a ON a.cargo_cobro_id=c.id WHERE c.cliente_id=v_cliente AND c.moneda=v_moneda AND c.deleted_at IS NULL GROUP BY c.id HAVING saldo>0 ORDER BY c.fecha_vencimiento,c.id; END$$
DROP PROCEDURE IF EXISTS sp_pagos_aplicar$$
CREATE PROCEDURE sp_pagos_aplicar(IN p_pago_id BIGINT UNSIGNED,IN p_cargo_id BIGINT UNSIGNED,IN p_monto DECIMAL(12,2))
BEGIN DECLARE v_pago_total DECIMAL(12,2); DECLARE v_pago_usado DECIMAL(12,2); DECLARE v_cargo_total DECIMAL(12,2); DECLARE v_cargo_pagado DECIMAL(12,2); DECLARE v_moneda_pago CHAR(3); DECLARE v_moneda_cargo CHAR(3); DECLARE v_cliente_pago BIGINT UNSIGNED; DECLARE v_cliente_cargo BIGINT UNSIGNED; DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 START TRANSACTION; SELECT monto,moneda,cliente_id INTO v_pago_total,v_moneda_pago,v_cliente_pago FROM pagos WHERE id=p_pago_id FOR UPDATE; SELECT total,moneda,cliente_id INTO v_cargo_total,v_moneda_cargo,v_cliente_cargo FROM cargos_cobro WHERE id=p_cargo_id AND deleted_at IS NULL FOR UPDATE;
 IF v_pago_total IS NULL OR v_cargo_total IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Pago o cargo inexistente'; END IF; IF v_cliente_pago<>v_cliente_cargo THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cargo no pertenece al cliente del pago'; END IF; IF v_moneda_pago<>v_moneda_cargo THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Las monedas no coinciden'; END IF;
 SELECT COALESCE(SUM(monto_aplicado),0) INTO v_pago_usado FROM aplicaciones_pago WHERE pago_id=p_pago_id; SELECT COALESCE(SUM(monto_aplicado),0) INTO v_cargo_pagado FROM aplicaciones_pago WHERE cargo_cobro_id=p_cargo_id;
 IF p_monto<=0 OR v_pago_usado+p_monto>v_pago_total THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La aplicación excede el saldo disponible del pago'; END IF; IF v_cargo_pagado+p_monto>v_cargo_total THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La aplicación excede el saldo del cargo'; END IF;
 INSERT INTO aplicaciones_pago(pago_id,cargo_cobro_id,monto_aplicado,created_at,updated_at) VALUES(p_pago_id,p_cargo_id,p_monto,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE monto_aplicado=monto_aplicado+p_monto,updated_at=CURRENT_TIMESTAMP; COMMIT; END$$
DROP PROCEDURE IF EXISTS sp_pagos_cambiar_estado$$
CREATE PROCEDURE sp_pagos_cambiar_estado(IN p_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_motivo VARCHAR(255),IN p_usuario_id BIGINT UNSIGNED)
BEGIN DECLARE v_anterior BIGINT UNSIGNED; DECLARE v_terminal BOOLEAN; DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END; START TRANSACTION; SELECT p.estado_pago_id,e.es_terminal INTO v_anterior,v_terminal FROM pagos p JOIN estados_pago e ON e.id=p.estado_pago_id WHERE p.id=p_id FOR UPDATE; IF v_anterior IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El pago no existe'; END IF; IF v_anterior=p_estado_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El pago ya tiene ese estado'; END IF; IF v_terminal=1 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No puede cambiarse un pago en estado terminal'; END IF; UPDATE pagos SET estado_pago_id=p_estado_id,updated_at=CURRENT_TIMESTAMP WHERE id=p_id; INSERT INTO historial_estados_pago(pago_id,estado_anterior_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(p_id,v_anterior,p_estado_id,p_motivo,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP); COMMIT; END$$
DROP PROCEDURE IF EXISTS sp_pagos_resumen$$
CREATE PROCEDURE sp_pagos_resumen() BEGIN SELECT COUNT(*) pagos,COALESCE(SUM(monto),0) total_recibido,COALESCE(SUM(aplicado),0) total_aplicado,COALESCE(SUM(monto-aplicado),0) saldo_sin_aplicar FROM (SELECT p.id,p.monto,COALESCE(SUM(a.monto_aplicado),0) aplicado FROM pagos p LEFT JOIN aplicaciones_pago a ON a.pago_id=p.id GROUP BY p.id) x; END$$
DROP PROCEDURE IF EXISTS sp_pagos_clientes$$
CREATE PROCEDURE sp_pagos_clientes() BEGIN SELECT id,numero_socio,CONCAT(nombre,' ',apellido) nombre FROM clientes WHERE deleted_at IS NULL ORDER BY apellido,nombre LIMIT 500; END$$
DROP PROCEDURE IF EXISTS sp_pagos_membresias_pendientes$$
CREATE PROCEDURE sp_pagos_membresias_pendientes()
BEGIN
 SELECT m.id,m.cliente_id,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente,t.nombre tipo,m.precio_contratado total,m.moneda,m.fecha_inicio,m.fecha_fin
 FROM membresias m JOIN clientes c ON c.id=m.cliente_id JOIN tipos_membresia t ON t.id=m.tipo_membresia_id
 WHERE m.deleted_at IS NULL AND c.deleted_at IS NULL
 AND NOT EXISTS(SELECT 1 FROM cargos_cobro cc WHERE cc.membresia_id=m.id AND COALESCE((SELECT SUM(a.monto_aplicado) FROM aplicaciones_pago a WHERE a.cargo_cobro_id=cc.id),0)>=cc.total)
 ORDER BY c.apellido,c.nombre,m.fecha_inicio DESC LIMIT 500;
END$$

DROP PROCEDURE IF EXISTS sp_pagos_registrar_completo$$
CREATE PROCEDURE sp_pagos_registrar_completo(IN p_idempotency CHAR(36),IN p_membresia_id BIGINT UNSIGNED,IN p_metodo_id BIGINT UNSIGNED,IN p_referencia VARCHAR(120),IN p_usuario_id BIGINT UNSIGNED,IN p_observaciones TEXT)
BEGIN
 DECLARE v_cliente BIGINT UNSIGNED; DECLARE v_total DECIMAL(12,2); DECLARE v_moneda CHAR(3); DECLARE v_tipo VARCHAR(100); DECLARE v_cargo BIGINT UNSIGNED; DECLARE v_pago BIGINT UNSIGNED; DECLARE v_estado_pago BIGINT UNSIGNED; DECLARE v_estado_cargo BIGINT UNSIGNED; DECLARE v_estado_activa BIGINT UNSIGNED; DECLARE v_estado_anterior BIGINT UNSIGNED; DECLARE v_pagado DECIMAL(12,2) DEFAULT 0;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 IF EXISTS(SELECT 1 FROM pagos WHERE idempotency_key=p_idempotency) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El pago ya fue procesado'; END IF;
 IF EXISTS(SELECT 1 FROM metodos_pago WHERE id=p_metodo_id AND requiere_referencia=1) AND (p_referencia IS NULL OR TRIM(p_referencia)='') THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El método de pago seleccionado requiere referencia'; END IF;
 SELECT m.cliente_id,m.precio_contratado,m.moneda,t.nombre,m.estado_membresia_id INTO v_cliente,v_total,v_moneda,v_tipo,v_estado_anterior FROM membresias m JOIN tipos_membresia t ON t.id=m.tipo_membresia_id WHERE m.id=p_membresia_id AND m.deleted_at IS NULL;
 IF v_cliente IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La membresía no existe'; END IF;
 SELECT id INTO v_estado_pago FROM estados_pago WHERE codigo='APLICADO' LIMIT 1;
 SELECT id INTO v_estado_cargo FROM estados_cargo_cobro WHERE codigo='PAGADO' LIMIT 1;
 SELECT id INTO v_estado_activa FROM estados_membresia WHERE codigo='ACTIVA' LIMIT 1;
 IF v_estado_pago IS NULL OR v_estado_cargo IS NULL OR v_estado_activa IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Los estados financieros no están configurados'; END IF;
 START TRANSACTION;
 SELECT id INTO v_cargo FROM cargos_cobro WHERE membresia_id=p_membresia_id AND deleted_at IS NULL ORDER BY id DESC LIMIT 1 FOR UPDATE;
 IF v_cargo IS NULL THEN
   INSERT INTO cargos_cobro(numero_cargo,cliente_id,membresia_id,estado_cargo_cobro_id,concepto,subtotal,descuento,impuesto,total,moneda,fecha_emision,fecha_vencimiento,creado_por,created_at,updated_at) VALUES(CONCAT('CG-',UUID_SHORT()),v_cliente,p_membresia_id,v_estado_cargo,CONCAT('Pago de membresía ',v_tipo),v_total,0,0,v_total,v_moneda,CURRENT_DATE,CURRENT_DATE,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
   SET v_cargo=LAST_INSERT_ID();
 ELSE
   SELECT COALESCE(SUM(monto_aplicado),0) INTO v_pagado FROM aplicaciones_pago WHERE cargo_cobro_id=v_cargo;
   IF v_pagado>0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La membresía ya tiene un pago registrado'; END IF;
   IF (SELECT total FROM cargos_cobro WHERE id=v_cargo)<>v_total THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El total interno no coincide con el precio de la membresía'; END IF;
   UPDATE cargos_cobro SET estado_cargo_cobro_id=v_estado_cargo,updated_at=CURRENT_TIMESTAMP WHERE id=v_cargo;
 END IF;
 INSERT INTO pagos(idempotency_key,numero_recibo,cliente_id,metodo_pago_id,estado_pago_id,monto,moneda,referencia,pagado_at,procesado_por,observaciones,created_at,updated_at) VALUES(p_idempotency,CONCAT('REC-',UUID_SHORT()),v_cliente,p_metodo_id,v_estado_pago,v_total,v_moneda,NULLIF(TRIM(p_referencia),''),CURRENT_TIMESTAMP,p_usuario_id,NULLIF(TRIM(p_observaciones),''),CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SET v_pago=LAST_INSERT_ID();
 INSERT INTO aplicaciones_pago(pago_id,cargo_cobro_id,monto_aplicado,created_at,updated_at) VALUES(v_pago,v_cargo,v_total,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 INSERT INTO historial_estados_pago(pago_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(v_pago,v_estado_pago,'Pago completo de membresía',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 IF v_estado_anterior<>v_estado_activa THEN UPDATE membresias SET bloqueo_activa=NULL,updated_at=CURRENT_TIMESTAMP WHERE cliente_id=v_cliente AND id<>p_membresia_id AND bloqueo_activa=1; UPDATE membresias SET estado_membresia_id=v_estado_activa,bloqueo_activa=1,updated_at=CURRENT_TIMESTAMP WHERE id=p_membresia_id; INSERT INTO historial_estados_membresia(membresia_id,estado_anterior_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(p_membresia_id,v_estado_anterior,v_estado_activa,'Activación por pago completo',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP); END IF;
 INSERT INTO eventos_cliente(cliente_id,tipo,titulo,descripcion,entidad,entidad_id,usuario_id,ocurrido_at,created_at,updated_at) VALUES(v_cliente,'PAGO_COMPLETADO','Pago completado',CONCAT(v_total,' ',v_moneda),'pagos',v_pago,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),(v_cliente,'MEMBRESIA_ACTIVADA','Membresía activada','Activación por pago completo','membresias',p_membresia_id,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
 CALL sp_pagos_obtener(v_pago);
END$$

DROP PROCEDURE IF EXISTS sp_pagos_reconciliar_completo$$
CREATE PROCEDURE sp_pagos_reconciliar_completo(IN p_pago_id BIGINT UNSIGNED,IN p_membresia_id BIGINT UNSIGNED,IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_cliente BIGINT UNSIGNED; DECLARE v_cliente_m BIGINT UNSIGNED; DECLARE v_monto DECIMAL(12,2); DECLARE v_total DECIMAL(12,2); DECLARE v_moneda CHAR(3); DECLARE v_cargo BIGINT UNSIGNED; DECLARE v_estado_cargo BIGINT UNSIGNED;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 SELECT cliente_id,monto INTO v_cliente,v_monto FROM pagos WHERE id=p_pago_id;
 SELECT cliente_id,precio_contratado,moneda INTO v_cliente_m,v_total,v_moneda FROM membresias WHERE id=p_membresia_id AND deleted_at IS NULL;
 IF v_cliente IS NULL OR v_cliente<>v_cliente_m OR v_monto<>v_total THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El pago no corresponde al total de la membresía'; END IF;
 IF EXISTS(SELECT 1 FROM aplicaciones_pago WHERE pago_id=p_pago_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El pago ya está aplicado'; END IF;
 SELECT id INTO v_estado_cargo FROM estados_cargo_cobro WHERE codigo='PAGADO' LIMIT 1;
 START TRANSACTION;
 INSERT INTO cargos_cobro(numero_cargo,cliente_id,membresia_id,estado_cargo_cobro_id,concepto,subtotal,descuento,impuesto,total,moneda,fecha_emision,fecha_vencimiento,creado_por,created_at,updated_at) VALUES(CONCAT('CG-',UUID_SHORT()),v_cliente,p_membresia_id,v_estado_cargo,'Pago de membresía',v_total,0,0,v_total,v_moneda,CURRENT_DATE,CURRENT_DATE,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SET v_cargo=LAST_INSERT_ID();
 INSERT INTO aplicaciones_pago(pago_id,cargo_cobro_id,monto_aplicado,created_at,updated_at) VALUES(p_pago_id,v_cargo,v_total,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_pagos_aplicar$$
CREATE PROCEDURE sp_pagos_aplicar(IN p_pago_id BIGINT UNSIGNED,IN p_cargo_id BIGINT UNSIGNED,IN p_monto DECIMAL(12,2))
BEGIN
 DECLARE v_disponible DECIMAL(12,2); DECLARE v_saldo DECIMAL(12,2);
 SELECT p.monto-COALESCE((SELECT SUM(a.monto_aplicado) FROM aplicaciones_pago a WHERE a.pago_id=p.id),0) INTO v_disponible FROM pagos p WHERE p.id=p_pago_id;
 SELECT c.total-COALESCE((SELECT SUM(a.monto_aplicado) FROM aplicaciones_pago a WHERE a.cargo_cobro_id=c.id),0) INTO v_saldo FROM cargos_cobro c WHERE c.id=p_cargo_id AND c.deleted_at IS NULL;
 IF v_disponible IS NULL OR v_saldo IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Pago o registro financiero inexistente'; END IF;
 IF p_monto<>v_disponible OR p_monto<>v_saldo THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No se permiten pagos parciales; debe pagarse el total'; END IF;
 INSERT INTO aplicaciones_pago(pago_id,cargo_cobro_id,monto_aplicado,created_at,updated_at) VALUES(p_pago_id,p_cargo_id,p_monto,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
END$$

DELIMITER ;
