-- FitControl: procedimientos del módulo Membresías.
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_membresias_filtrar$$
CREATE PROCEDURE sp_membresias_filtrar(IN p_texto VARCHAR(150),IN p_estado_id BIGINT UNSIGNED,IN p_tipo_id BIGINT UNSIGNED,IN p_desde DATE,IN p_hasta DATE,IN p_limite INT UNSIGNED,IN p_offset INT UNSIGNED)
BEGIN
 IF p_limite IS NULL OR p_limite<1 OR p_limite>100 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El límite debe estar entre 1 y 100'; END IF;
 SELECT m.id,m.cliente_id,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente,m.tipo_membresia_id,t.nombre tipo,
 m.estado_membresia_id,e.codigo estado_codigo,e.nombre estado,e.permite_acceso,m.fecha_inicio,m.fecha_fin,
 m.precio_contratado,m.moneda,m.bloqueo_activa,m.origen,m.membresia_anterior_id,m.cancelada_at,m.created_at
 FROM membresias m JOIN clientes c ON c.id=m.cliente_id JOIN tipos_membresia t ON t.id=m.tipo_membresia_id
 JOIN estados_membresia e ON e.id=m.estado_membresia_id WHERE m.deleted_at IS NULL
 AND (p_texto IS NULL OR p_texto='' OR c.numero_socio LIKE CONCAT('%',p_texto,'%') OR c.nombre LIKE CONCAT('%',p_texto,'%') OR c.apellido LIKE CONCAT('%',p_texto,'%'))
 AND (p_estado_id IS NULL OR m.estado_membresia_id=p_estado_id) AND (p_tipo_id IS NULL OR m.tipo_membresia_id=p_tipo_id)
 AND (p_desde IS NULL OR m.fecha_fin>=p_desde) AND (p_hasta IS NULL OR m.fecha_inicio<=p_hasta)
 ORDER BY m.fecha_inicio DESC,m.id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_contar$$
CREATE PROCEDURE sp_membresias_contar(IN p_texto VARCHAR(150),IN p_estado_id BIGINT UNSIGNED,IN p_tipo_id BIGINT UNSIGNED,IN p_desde DATE,IN p_hasta DATE)
BEGIN
 SELECT COUNT(*) total FROM membresias m JOIN clientes c ON c.id=m.cliente_id WHERE m.deleted_at IS NULL
 AND (p_texto IS NULL OR p_texto='' OR c.numero_socio LIKE CONCAT('%',p_texto,'%') OR c.nombre LIKE CONCAT('%',p_texto,'%') OR c.apellido LIKE CONCAT('%',p_texto,'%'))
 AND (p_estado_id IS NULL OR m.estado_membresia_id=p_estado_id) AND (p_tipo_id IS NULL OR m.tipo_membresia_id=p_tipo_id)
 AND (p_desde IS NULL OR m.fecha_fin>=p_desde) AND (p_hasta IS NULL OR m.fecha_inicio<=p_hasta);
END$$

DROP PROCEDURE IF EXISTS sp_membresias_obtener$$
CREATE PROCEDURE sp_membresias_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
 IF NOT EXISTS(SELECT 1 FROM membresias WHERE id=p_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La membresía no existe'; END IF;
 SELECT m.*,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente,t.nombre tipo,t.duracion_dias,
 e.codigo estado_codigo,e.nombre estado,e.permite_acceso,e.es_terminal
 FROM membresias m JOIN clientes c ON c.id=m.cliente_id JOIN tipos_membresia t ON t.id=m.tipo_membresia_id
 JOIN estados_membresia e ON e.id=m.estado_membresia_id WHERE m.id=p_id AND m.deleted_at IS NULL;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_historial$$
CREATE PROCEDURE sp_membresias_historial(IN p_id BIGINT UNSIGNED)
BEGIN
 SELECT h.id,ea.nombre estado_anterior,en.nombre estado_nuevo,h.motivo,h.cambiado_por,h.cambiado_at
 FROM historial_estados_membresia h LEFT JOIN estados_membresia ea ON ea.id=h.estado_anterior_id
 JOIN estados_membresia en ON en.id=h.estado_nuevo_id WHERE h.membresia_id=p_id ORDER BY h.cambiado_at DESC,h.id DESC;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_suspensiones$$
CREATE PROCEDURE sp_membresias_suspensiones(IN p_id BIGINT UNSIGNED)
BEGIN SELECT * FROM suspensiones_membresia WHERE membresia_id=p_id ORDER BY fecha_inicio DESC,id DESC; END$$

DROP PROCEDURE IF EXISTS sp_membresias_clientes_disponibles$$
CREATE PROCEDURE sp_membresias_clientes_disponibles(IN p_texto VARCHAR(150))
BEGIN SELECT c.id,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) nombre FROM clientes c
 WHERE c.deleted_at IS NULL AND NOT EXISTS(SELECT 1 FROM membresias m WHERE m.cliente_id=c.id AND m.bloqueo_activa=1 AND m.deleted_at IS NULL)
 AND (p_texto IS NULL OR p_texto='' OR c.numero_socio LIKE CONCAT('%',p_texto,'%') OR c.nombre LIKE CONCAT('%',p_texto,'%') OR c.apellido LIKE CONCAT('%',p_texto,'%')) ORDER BY c.apellido,c.nombre LIMIT 200; END$$

DROP PROCEDURE IF EXISTS sp_membresias_precios_disponibles$$
CREATE PROCEDURE sp_membresias_precios_disponibles(IN p_fecha DATE)
BEGIN SELECT p.id,p.tipo_membresia_id,t.nombre tipo,t.duracion_dias,p.precio,p.moneda,p.vigente_desde,p.vigente_hasta
 FROM precios_membresia p JOIN tipos_membresia t ON t.id=p.tipo_membresia_id
 WHERE t.activo=1 AND p.vigente_desde<=COALESCE(p_fecha,CURRENT_DATE) AND (p.vigente_hasta IS NULL OR p.vigente_hasta>=COALESCE(p_fecha,CURRENT_DATE)) ORDER BY t.nombre,p.moneda; END$$

DROP PROCEDURE IF EXISTS sp_membresias_renovar$$
CREATE PROCEDURE sp_membresias_renovar(IN p_anterior_id BIGINT UNSIGNED,IN p_tipo_id BIGINT UNSIGNED,IN p_precio_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_inicio DATE,IN p_fin DATE,IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_cliente BIGINT UNSIGNED; DECLARE v_lock BIGINT UNSIGNED; DECLARE v_precio DECIMAL(12,2); DECLARE v_moneda CHAR(3); DECLARE v_id BIGINT UNSIGNED;
 DECLARE EXIT HANDLER FOR 1062 BEGIN ROLLBACK; SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente ya posee otra membresía activa'; END;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 IF p_fin<p_inicio THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El periodo de renovación no es válido'; END IF;
 SELECT cliente_id INTO v_cliente FROM membresias WHERE id=p_anterior_id AND deleted_at IS NULL;
 IF v_cliente IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La membresía anterior no existe'; END IF;
 SELECT precio,moneda INTO v_precio,v_moneda FROM precios_membresia WHERE id=p_precio_id AND tipo_membresia_id=p_tipo_id AND vigente_desde<=p_inicio AND (vigente_hasta IS NULL OR vigente_hasta>=p_inicio);
 IF v_precio IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El precio no está vigente para la fecha de renovación'; END IF;
 START TRANSACTION;
 SELECT id INTO v_lock FROM clientes WHERE id=v_cliente FOR UPDATE;
 UPDATE membresias SET bloqueo_activa=NULL,updated_at=CURRENT_TIMESTAMP WHERE id=p_anterior_id;
 INSERT INTO membresias(cliente_id,tipo_membresia_id,precio_membresia_id,estado_membresia_id,membresia_anterior_id,fecha_inicio,fecha_fin,precio_contratado,moneda,bloqueo_activa,origen,creada_por,created_at,updated_at)
 VALUES(v_cliente,p_tipo_id,p_precio_id,p_estado_id,p_anterior_id,p_inicio,p_fin,v_precio,v_moneda,1,'RENOVACION',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SET v_id=LAST_INSERT_ID();
 INSERT INTO historial_estados_membresia(membresia_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at)
 VALUES(v_id,p_estado_id,'Renovación de membresía',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT; CALL sp_membresias_obtener(v_id);
END$$

DROP PROCEDURE IF EXISTS sp_membresias_congelar$$
CREATE PROCEDURE sp_membresias_congelar(IN p_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_inicio DATE,IN p_fin DATE,IN p_dias_extension SMALLINT UNSIGNED,IN p_motivo VARCHAR(255),IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_anterior BIGINT UNSIGNED; DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 IF p_fin IS NOT NULL AND p_fin<p_inicio THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El periodo de congelamiento no es válido'; END IF;
 IF EXISTS(SELECT 1 FROM suspensiones_membresia WHERE membresia_id=p_id AND (fecha_fin IS NULL OR fecha_fin>=p_inicio) AND (p_fin IS NULL OR fecha_inicio<=p_fin)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El congelamiento se superpone con otro'; END IF;
 START TRANSACTION; SELECT estado_membresia_id INTO v_anterior FROM membresias WHERE id=p_id AND deleted_at IS NULL AND bloqueo_activa=1 FOR UPDATE;
 IF v_anterior IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La membresía no está activa'; END IF;
 IF NOT EXISTS(SELECT 1 FROM estados_membresia WHERE id=p_estado_id AND es_terminal=0) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Estado de congelamiento no válido'; END IF;
 INSERT INTO suspensiones_membresia(membresia_id,fecha_inicio,fecha_fin,dias_extension,motivo,autorizada_por,created_at,updated_at) VALUES(p_id,p_inicio,p_fin,COALESCE(p_dias_extension,0),p_motivo,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 UPDATE membresias SET estado_membresia_id=p_estado_id,fecha_fin=DATE_ADD(fecha_fin,INTERVAL COALESCE(p_dias_extension,0) DAY),updated_at=CURRENT_TIMESTAMP WHERE id=p_id;
 INSERT INTO historial_estados_membresia(membresia_id,estado_anterior_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(p_id,v_anterior,p_estado_id,p_motivo,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_reactivar$$
CREATE PROCEDURE sp_membresias_reactivar(IN p_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_fecha DATE,IN p_motivo VARCHAR(255),IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_anterior BIGINT UNSIGNED; DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 START TRANSACTION; SELECT estado_membresia_id INTO v_anterior FROM membresias WHERE id=p_id AND deleted_at IS NULL AND bloqueo_activa=1 FOR UPDATE;
 IF v_anterior IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La membresía no puede reactivarse'; END IF;
 IF NOT EXISTS(SELECT 1 FROM suspensiones_membresia WHERE membresia_id=p_id AND fecha_inicio<=p_fecha AND (fecha_fin IS NULL OR fecha_fin>=p_fecha)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No existe un congelamiento vigente para esa fecha'; END IF;
 IF NOT EXISTS(SELECT 1 FROM estados_membresia WHERE id=p_estado_id AND permite_acceso=1 AND es_terminal=0) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El estado de reactivación no permite acceso'; END IF;
 UPDATE suspensiones_membresia SET fecha_fin=p_fecha,updated_at=CURRENT_TIMESTAMP WHERE membresia_id=p_id AND fecha_inicio<=p_fecha AND (fecha_fin IS NULL OR fecha_fin>=p_fecha);
 UPDATE membresias SET estado_membresia_id=p_estado_id,updated_at=CURRENT_TIMESTAMP WHERE id=p_id;
 INSERT INTO historial_estados_membresia(membresia_id,estado_anterior_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(p_id,v_anterior,p_estado_id,p_motivo,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_cancelar$$
CREATE PROCEDURE sp_membresias_cancelar(IN p_id BIGINT UNSIGNED,IN p_estado_cancelada_id BIGINT UNSIGNED,IN p_motivo VARCHAR(255),IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_anterior BIGINT UNSIGNED; DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 IF p_motivo IS NULL OR TRIM(p_motivo)='' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El motivo de cancelación es obligatorio'; END IF;
 IF NOT EXISTS(SELECT 1 FROM estados_membresia WHERE id=p_estado_cancelada_id AND es_terminal=1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El estado seleccionado no es terminal'; END IF;
 START TRANSACTION; SELECT estado_membresia_id INTO v_anterior FROM membresias WHERE id=p_id AND deleted_at IS NULL AND cancelada_at IS NULL FOR UPDATE;
 IF v_anterior IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La membresía no existe o ya fue cancelada'; END IF;
 UPDATE membresias SET estado_membresia_id=p_estado_cancelada_id,bloqueo_activa=NULL,cancelada_at=CURRENT_TIMESTAMP,motivo_cancelacion=p_motivo,cancelada_por=p_usuario_id,updated_at=CURRENT_TIMESTAMP WHERE id=p_id;
 INSERT INTO historial_estados_membresia(membresia_id,estado_anterior_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(p_id,v_anterior,p_estado_cancelada_id,p_motivo,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_crear$$
CREATE PROCEDURE sp_membresias_crear(IN p_cliente_id BIGINT UNSIGNED,IN p_tipo_id BIGINT UNSIGNED,IN p_precio_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_fecha_inicio DATE,IN p_fecha_fin DATE,IN p_origen VARCHAR(30),IN p_anterior_id BIGINT UNSIGNED,IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_precio DECIMAL(12,2); DECLARE v_moneda CHAR(3); DECLARE v_dias SMALLINT UNSIGNED; DECLARE v_fin DATE; DECLARE v_id BIGINT UNSIGNED; DECLARE v_lock BIGINT UNSIGNED;
 DECLARE EXIT HANDLER FOR 1062 BEGIN ROLLBACK; SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente ya posee una membresía activa'; END;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 IF p_fecha_inicio IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La fecha de inicio es obligatoria'; END IF;
 IF NOT EXISTS(SELECT 1 FROM clientes WHERE id=p_cliente_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Cliente no encontrado'; END IF;
 SELECT p.precio,p.moneda,t.duracion_dias INTO v_precio,v_moneda,v_dias FROM precios_membresia p JOIN tipos_membresia t ON t.id=p.tipo_membresia_id WHERE p.id=p_precio_id AND p.tipo_membresia_id=p_tipo_id AND p.vigente_desde<=p_fecha_inicio AND (p.vigente_hasta IS NULL OR p.vigente_hasta>=p_fecha_inicio) AND t.activo=1;
 IF v_precio IS NULL OR v_dias IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Precio no vigente o incompatible con el plan'; END IF;
 SET v_fin=DATE_ADD(p_fecha_inicio,INTERVAL v_dias-1 DAY);
 START TRANSACTION;
 SELECT id INTO v_lock FROM clientes WHERE id=p_cliente_id FOR UPDATE;
 IF EXISTS(SELECT 1 FROM membresias WHERE cliente_id=p_cliente_id AND bloqueo_activa=1 AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente ya posee una membresía activa'; END IF;
 INSERT INTO membresias(cliente_id,tipo_membresia_id,precio_membresia_id,estado_membresia_id,membresia_anterior_id,fecha_inicio,fecha_fin,precio_contratado,moneda,bloqueo_activa,origen,creada_por,created_at,updated_at) VALUES(p_cliente_id,p_tipo_id,p_precio_id,p_estado_id,p_anterior_id,p_fecha_inicio,v_fin,v_precio,v_moneda,1,COALESCE(p_origen,'NUEVA'),p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SET v_id=LAST_INSERT_ID();
 INSERT INTO historial_estados_membresia(membresia_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(v_id,p_estado_id,'Alta de membresía',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
 SELECT * FROM membresias WHERE id=v_id;
END$$

DELIMITER ;
