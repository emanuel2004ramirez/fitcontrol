-- FitControl: procedimientos del módulo Cargos por Cobrar.
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_cargos_cobro_filtrar$$
CREATE PROCEDURE sp_cargos_cobro_filtrar(IN p_texto VARCHAR(150),IN p_cliente_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_vencidos BOOLEAN,IN p_desde DATE,IN p_hasta DATE,IN p_limite INT UNSIGNED,IN p_offset INT UNSIGNED)
BEGIN
 IF p_limite IS NULL OR p_limite<1 OR p_limite>100 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El límite debe estar entre 1 y 100'; END IF;
 SELECT c.id,c.numero_cargo,c.cliente_id,cl.numero_socio,CONCAT(cl.nombre,' ',cl.apellido) cliente,c.membresia_id,
 c.estado_cargo_cobro_id,e.codigo estado_codigo,e.nombre estado,e.es_terminal,c.concepto,c.descripcion,c.subtotal,c.descuento,c.impuesto,c.total,c.moneda,c.fecha_emision,c.fecha_vencimiento,
 COALESCE(SUM(a.monto_aplicado),0) monto_pagado,c.total-COALESCE(SUM(a.monto_aplicado),0) saldo,
 (c.fecha_vencimiento<CURRENT_DATE AND c.total>COALESCE(SUM(a.monto_aplicado),0)) vencido
 FROM cargos_cobro c JOIN clientes cl ON cl.id=c.cliente_id JOIN estados_cargo_cobro e ON e.id=c.estado_cargo_cobro_id
 LEFT JOIN aplicaciones_pago a ON a.cargo_cobro_id=c.id WHERE c.deleted_at IS NULL
 AND (p_texto IS NULL OR p_texto='' OR c.numero_cargo LIKE CONCAT('%',p_texto,'%') OR c.concepto LIKE CONCAT('%',p_texto,'%') OR cl.numero_socio LIKE CONCAT('%',p_texto,'%') OR cl.nombre LIKE CONCAT('%',p_texto,'%') OR cl.apellido LIKE CONCAT('%',p_texto,'%'))
 AND (p_cliente_id IS NULL OR c.cliente_id=p_cliente_id) AND (p_estado_id IS NULL OR c.estado_cargo_cobro_id=p_estado_id)
 AND (p_desde IS NULL OR c.fecha_emision>=p_desde) AND (p_hasta IS NULL OR c.fecha_emision<=p_hasta)
 GROUP BY c.id,cl.numero_socio,cl.nombre,cl.apellido,e.codigo,e.nombre,e.es_terminal
 HAVING (COALESCE(p_vencidos,0)=0 OR vencido=1) ORDER BY c.fecha_emision DESC,c.id DESC LIMIT p_limite OFFSET p_offset;
END$$
DROP PROCEDURE IF EXISTS sp_cargos_cobro_contar$$
CREATE PROCEDURE sp_cargos_cobro_contar(IN p_texto VARCHAR(150),IN p_cliente_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_vencidos BOOLEAN,IN p_desde DATE,IN p_hasta DATE)
BEGIN SELECT COUNT(*) total FROM (SELECT c.id,c.fecha_vencimiento,c.total,COALESCE(SUM(a.monto_aplicado),0) pagado FROM cargos_cobro c JOIN clientes cl ON cl.id=c.cliente_id LEFT JOIN aplicaciones_pago a ON a.cargo_cobro_id=c.id WHERE c.deleted_at IS NULL
 AND (p_texto IS NULL OR p_texto='' OR c.numero_cargo LIKE CONCAT('%',p_texto,'%') OR c.concepto LIKE CONCAT('%',p_texto,'%') OR cl.numero_socio LIKE CONCAT('%',p_texto,'%') OR cl.nombre LIKE CONCAT('%',p_texto,'%') OR cl.apellido LIKE CONCAT('%',p_texto,'%'))
 AND (p_cliente_id IS NULL OR c.cliente_id=p_cliente_id) AND (p_estado_id IS NULL OR c.estado_cargo_cobro_id=p_estado_id) AND (p_desde IS NULL OR c.fecha_emision>=p_desde) AND (p_hasta IS NULL OR c.fecha_emision<=p_hasta) GROUP BY c.id HAVING COALESCE(p_vencidos,0)=0 OR (fecha_vencimiento<CURRENT_DATE AND total>pagado)) x; END$$
DROP PROCEDURE IF EXISTS sp_cargos_cobro_obtener$$
CREATE PROCEDURE sp_cargos_cobro_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
 IF NOT EXISTS(SELECT 1 FROM cargos_cobro WHERE id=p_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cargo no existe'; END IF;
 SELECT c.*,cl.numero_socio,CONCAT(cl.nombre,' ',cl.apellido) cliente,e.codigo estado_codigo,e.nombre estado,e.es_terminal,
 COALESCE(SUM(a.monto_aplicado),0) monto_pagado,c.total-COALESCE(SUM(a.monto_aplicado),0) saldo
 FROM cargos_cobro c JOIN clientes cl ON cl.id=c.cliente_id JOIN estados_cargo_cobro e ON e.id=c.estado_cargo_cobro_id LEFT JOIN aplicaciones_pago a ON a.cargo_cobro_id=c.id WHERE c.id=p_id AND c.deleted_at IS NULL GROUP BY c.id,cl.numero_socio,cl.nombre,cl.apellido,e.codigo,e.nombre,e.es_terminal;
END$$
DROP PROCEDURE IF EXISTS sp_cargos_cobro_crear$$
CREATE PROCEDURE sp_cargos_cobro_crear(IN p_numero VARCHAR(40),IN p_cliente_id BIGINT UNSIGNED,IN p_membresia_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_concepto VARCHAR(150),IN p_descripcion TEXT,IN p_subtotal DECIMAL(12,2),IN p_descuento DECIMAL(12,2),IN p_impuesto DECIMAL(12,2),IN p_moneda CHAR(3),IN p_vencimiento DATE,IN p_usuario_id BIGINT UNSIGNED)
BEGIN DECLARE v_id BIGINT UNSIGNED; DECLARE v_total DECIMAL(12,2); DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 SET v_total=p_subtotal-COALESCE(p_descuento,0)+COALESCE(p_impuesto,0); IF v_total<0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El total del cargo no puede ser negativo'; END IF;
 IF EXISTS(SELECT 1 FROM cargos_cobro WHERE numero_cargo=TRIM(p_numero)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El número de cargo ya existe'; END IF;
 IF NOT EXISTS(SELECT 1 FROM clientes WHERE id=p_cliente_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente no existe'; END IF;
 IF p_membresia_id IS NOT NULL AND NOT EXISTS(SELECT 1 FROM membresias WHERE id=p_membresia_id AND cliente_id=p_cliente_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La membresía no pertenece al cliente'; END IF;
 START TRANSACTION; INSERT INTO cargos_cobro(numero_cargo,cliente_id,membresia_id,estado_cargo_cobro_id,concepto,descripcion,subtotal,descuento,impuesto,total,moneda,fecha_emision,fecha_vencimiento,creado_por,created_at,updated_at) VALUES(TRIM(p_numero),p_cliente_id,p_membresia_id,p_estado_id,TRIM(p_concepto),p_descripcion,p_subtotal,COALESCE(p_descuento,0),COALESCE(p_impuesto,0),v_total,UPPER(p_moneda),CURRENT_DATE,p_vencimiento,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP); SET v_id=LAST_INSERT_ID();
 INSERT INTO historial_estados_cargo_cobro(cargo_cobro_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(v_id,p_estado_id,'Creación del cargo',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP); COMMIT; CALL sp_cargos_cobro_obtener(v_id); END$$
DROP PROCEDURE IF EXISTS sp_cargos_cobro_actualizar$$
CREATE PROCEDURE sp_cargos_cobro_actualizar(IN p_id BIGINT UNSIGNED,IN p_concepto VARCHAR(150),IN p_descripcion TEXT,IN p_subtotal DECIMAL(12,2),IN p_descuento DECIMAL(12,2),IN p_impuesto DECIMAL(12,2),IN p_vencimiento DATE)
BEGIN DECLARE v_total DECIMAL(12,2); SET v_total=p_subtotal-COALESCE(p_descuento,0)+COALESCE(p_impuesto,0);
 IF NOT EXISTS(SELECT 1 FROM cargos_cobro WHERE id=p_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cargo no existe'; END IF;
 IF EXISTS(SELECT 1 FROM aplicaciones_pago WHERE cargo_cobro_id=p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No puede modificar importes de un cargo con pagos aplicados'; END IF;
 IF v_total<0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El total no puede ser negativo'; END IF;
 UPDATE cargos_cobro SET concepto=TRIM(p_concepto),descripcion=p_descripcion,subtotal=p_subtotal,descuento=COALESCE(p_descuento,0),impuesto=COALESCE(p_impuesto,0),total=v_total,fecha_vencimiento=p_vencimiento,updated_at=CURRENT_TIMESTAMP WHERE id=p_id; CALL sp_cargos_cobro_obtener(p_id); END$$
DROP PROCEDURE IF EXISTS sp_cargos_cobro_cambiar_estado$$
CREATE PROCEDURE sp_cargos_cobro_cambiar_estado(IN p_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_motivo VARCHAR(255),IN p_usuario_id BIGINT UNSIGNED)
BEGIN DECLARE v_anterior BIGINT UNSIGNED; DECLARE v_terminal BOOLEAN; DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 START TRANSACTION; SELECT c.estado_cargo_cobro_id,e.es_terminal INTO v_anterior,v_terminal FROM cargos_cobro c JOIN estados_cargo_cobro e ON e.id=c.estado_cargo_cobro_id WHERE c.id=p_id AND c.deleted_at IS NULL FOR UPDATE;
 IF v_anterior IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cargo no existe'; END IF; IF v_anterior=p_estado_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cargo ya tiene ese estado'; END IF; IF v_terminal=1 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No puede cambiarse un cargo en estado terminal'; END IF;
 UPDATE cargos_cobro SET estado_cargo_cobro_id=p_estado_id,updated_at=CURRENT_TIMESTAMP WHERE id=p_id; INSERT INTO historial_estados_cargo_cobro(cargo_cobro_id,estado_anterior_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(p_id,v_anterior,p_estado_id,p_motivo,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP); COMMIT; END$$
DROP PROCEDURE IF EXISTS sp_cargos_cobro_eliminar$$
CREATE PROCEDURE sp_cargos_cobro_eliminar(IN p_id BIGINT UNSIGNED,IN p_motivo VARCHAR(255),IN p_usuario_id BIGINT UNSIGNED)
BEGIN IF EXISTS(SELECT 1 FROM aplicaciones_pago WHERE cargo_cobro_id=p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No puede retirarse un cargo con pagos aplicados'; END IF; UPDATE cargos_cobro SET deleted_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND deleted_at IS NULL; IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cargo no existe'; END IF; END$$
DROP PROCEDURE IF EXISTS sp_cargos_cobro_historial$$
CREATE PROCEDURE sp_cargos_cobro_historial(IN p_id BIGINT UNSIGNED) BEGIN SELECT h.id,ea.nombre estado_anterior,en.nombre estado_nuevo,h.motivo,h.cambiado_por,h.cambiado_at FROM historial_estados_cargo_cobro h LEFT JOIN estados_cargo_cobro ea ON ea.id=h.estado_anterior_id JOIN estados_cargo_cobro en ON en.id=h.estado_nuevo_id WHERE h.cargo_cobro_id=p_id ORDER BY h.cambiado_at DESC,h.id DESC; END$$
DROP PROCEDURE IF EXISTS sp_cargos_cobro_aplicaciones$$
CREATE PROCEDURE sp_cargos_cobro_aplicaciones(IN p_id BIGINT UNSIGNED) BEGIN SELECT a.id,a.pago_id,p.numero_recibo,p.pagado_at,a.monto_aplicado,p.moneda,a.created_at FROM aplicaciones_pago a JOIN pagos p ON p.id=a.pago_id WHERE a.cargo_cobro_id=p_id ORDER BY a.created_at DESC; END$$
DROP PROCEDURE IF EXISTS sp_cargos_cobro_resumen$$
CREATE PROCEDURE sp_cargos_cobro_resumen() BEGIN SELECT COUNT(*) cargos,COALESCE(SUM(total),0) total_facturado,COALESCE(SUM(pagado),0) total_pagado,COALESCE(SUM(total-pagado),0) saldo_pendiente,COALESCE(SUM(fecha_vencimiento<CURRENT_DATE AND total>pagado),0) vencidos FROM (SELECT c.id,c.total,c.fecha_vencimiento,COALESCE(SUM(a.monto_aplicado),0) pagado FROM cargos_cobro c LEFT JOIN aplicaciones_pago a ON a.cargo_cobro_id=c.id WHERE c.deleted_at IS NULL GROUP BY c.id) x; END$$
DROP PROCEDURE IF EXISTS sp_cargos_cobro_clientes$$
CREATE PROCEDURE sp_cargos_cobro_clientes() BEGIN SELECT id,numero_socio,CONCAT(nombre,' ',apellido) nombre FROM clientes WHERE deleted_at IS NULL ORDER BY apellido,nombre LIMIT 500; END$$
DROP PROCEDURE IF EXISTS sp_cargos_cobro_membresias$$
CREATE PROCEDURE sp_cargos_cobro_membresias(IN p_cliente_id BIGINT UNSIGNED) BEGIN SELECT m.id,m.cliente_id,t.nombre tipo,m.fecha_inicio,m.fecha_fin FROM membresias m JOIN tipos_membresia t ON t.id=m.tipo_membresia_id WHERE m.deleted_at IS NULL AND (p_cliente_id IS NULL OR m.cliente_id=p_cliente_id) ORDER BY m.fecha_inicio DESC LIMIT 500; END$$
DELIMITER ;


DROP PROCEDURE IF EXISTS sp_cargos_listar;
CREATE PROCEDURE sp_cargos_listar(IN p_buscar VARCHAR(100), IN p_limite INT, IN p_offset INT)
BEGIN
    SELECT id, codigo, nombre, descripcion, activo
    FROM cargos
    WHERE p_buscar = '' OR codigo LIKE CONCAT('%', p_buscar, '%') OR nombre LIKE CONCAT('%', p_buscar, '%')
    ORDER BY id DESC
    LIMIT p_limite OFFSET p_offset;
END;

DROP PROCEDURE IF EXISTS sp_cargos_crear;
CREATE PROCEDURE sp_cargos_crear(IN p_codigo VARCHAR(30), IN p_nombre VARCHAR(50), IN p_descripcion TEXT, IN p_activo TINYINT)
BEGIN
    INSERT INTO cargos (codigo, nombre, descripcion, activo, created_at, updated_at) 
    VALUES (p_codigo, p_nombre, p_descripcion, p_activo, NOW(), NOW());
END;

DROP PROCEDURE IF EXISTS sp_cargos_actualizar;
CREATE PROCEDURE sp_cargos_actualizar(IN p_id BIGINT, IN p_codigo VARCHAR(30), IN p_nombre VARCHAR(50), IN p_descripcion TEXT, IN p_activo TINYINT)
BEGIN
    UPDATE cargos 
    SET codigo = p_codigo, 
        nombre = p_nombre, 
        descripcion = p_descripcion, 
        activo = p_activo, 
        updated_at = NOW() 
    WHERE id = p_id;
END;

DROP PROCEDURE IF EXISTS sp_cargos_eliminar;
CREATE PROCEDURE sp_cargos_eliminar(IN p_id BIGINT)
BEGIN
    DELETE FROM cargos WHERE id = p_id;
END;
