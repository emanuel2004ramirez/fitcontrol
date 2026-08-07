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
 SELECT m.*,c.numero_socio,c.correo_electronico,CONCAT(c.nombre,' ',c.apellido) cliente,t.codigo tipo_codigo,t.nombre tipo,t.duracion_dias,
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
 WHERE c.deleted_at IS NULL AND NOT EXISTS(SELECT 1 FROM membresias m JOIN estados_membresia e ON e.id=m.estado_membresia_id WHERE m.cliente_id=c.id AND m.deleted_at IS NULL AND e.es_terminal=0)
 AND NOT EXISTS(SELECT 1 FROM beneficiarios_membresia b JOIN membresias_familiares f ON f.id=b.membresia_familiar_id JOIN membresias m ON m.id=f.membresia_id JOIN estados_membresia e ON e.id=m.estado_membresia_id WHERE b.cliente_id=c.id AND b.estado='ACTIVO' AND m.bloqueo_activa=1 AND m.deleted_at IS NULL AND e.permite_acceso=1 AND CURRENT_DATE BETWEEN m.fecha_inicio AND m.fecha_fin)
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
 IF EXISTS(SELECT 1 FROM membresias m JOIN estados_membresia e ON e.id=m.estado_membresia_id WHERE m.cliente_id=p_cliente_id AND m.deleted_at IS NULL AND e.es_terminal=0) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente ya posee una membresía vigente, pendiente o programada'; END IF;
 INSERT INTO membresias(cliente_id,tipo_membresia_id,precio_membresia_id,estado_membresia_id,membresia_anterior_id,fecha_inicio,fecha_fin,precio_contratado,moneda,bloqueo_activa,origen,creada_por,created_at,updated_at) VALUES(p_cliente_id,p_tipo_id,p_precio_id,p_estado_id,p_anterior_id,p_fecha_inicio,v_fin,v_precio,v_moneda,1,COALESCE(p_origen,'NUEVA'),p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SET v_id=LAST_INSERT_ID();
 INSERT INTO historial_estados_membresia(membresia_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(v_id,p_estado_id,'Alta de membresía',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
 SELECT * FROM membresias WHERE id=v_id;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_crear_contrato$$
CREATE PROCEDURE sp_membresias_crear_contrato(IN p_datos JSON,IN p_usuario_id BIGINT UNSIGNED,IN p_ip VARCHAR(45))
BEGIN
 DECLARE v_cliente BIGINT UNSIGNED; DECLARE v_tipo BIGINT UNSIGNED; DECLARE v_precio_id BIGINT UNSIGNED; DECLARE v_estado BIGINT UNSIGNED; DECLARE v_precio DECIMAL(12,2); DECLARE v_moneda CHAR(3); DECLARE v_dias SMALLINT UNSIGNED; DECLARE v_inicio DATE; DECLARE v_fin DATE; DECLARE v_id BIGINT UNSIGNED; DECLARE v_nombre VARCHAR(220); DECLARE v_identificacion VARCHAR(100); DECLARE v_plan VARCHAR(120);
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 SET v_cliente=JSON_EXTRACT(p_datos,'$.cliente_id'); SET v_tipo=JSON_EXTRACT(p_datos,'$.tipo_membresia_id'); SET v_precio_id=JSON_EXTRACT(p_datos,'$.precio_membresia_id'); SET v_inicio=JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.fecha_inicio'));
 IF COALESCE(CAST(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.acepta_contrato')) AS UNSIGNED),0)<>1 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Debe aceptar el contrato de membresía'; END IF;
 SELECT id INTO v_estado FROM estados_membresia WHERE codigo='PENDIENTE' LIMIT 1;
 SELECT p.precio,p.moneda,t.duracion_dias,t.nombre INTO v_precio,v_moneda,v_dias,v_plan FROM precios_membresia p JOIN tipos_membresia t ON t.id=p.tipo_membresia_id WHERE p.id=v_precio_id AND p.tipo_membresia_id=v_tipo AND p.vigente_desde<=v_inicio AND (p.vigente_hasta IS NULL OR p.vigente_hasta>=v_inicio) AND t.activo=1;
 SELECT CONCAT(nombre,' ',apellido),CONCAT_WS(' ',tipo_identificacion,numero_identificacion) INTO v_nombre,v_identificacion FROM clientes WHERE id=v_cliente AND deleted_at IS NULL;
 IF v_estado IS NULL OR v_precio IS NULL OR v_nombre IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Cliente, plan o precio no válido'; END IF;
 IF EXISTS(SELECT 1 FROM membresias m JOIN estados_membresia e ON e.id=m.estado_membresia_id WHERE m.cliente_id=v_cliente AND m.deleted_at IS NULL AND e.es_terminal=0) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente ya tiene una membresía vigente, pendiente, programada o congelada'; END IF;
 SET v_fin=DATE_ADD(v_inicio,INTERVAL v_dias-1 DAY);
 START TRANSACTION;
 INSERT INTO membresias(cliente_id,tipo_membresia_id,precio_membresia_id,estado_membresia_id,fecha_inicio,fecha_fin,precio_contratado,moneda,bloqueo_activa,origen,creada_por,created_at,updated_at) VALUES(v_cliente,v_tipo,v_precio_id,v_estado,v_inicio,v_fin,v_precio,v_moneda,NULL,'NUEVA',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP); SET v_id=LAST_INSERT_ID();
 INSERT INTO contratos_membresia(membresia_id,numero_contrato,version_documento,cliente_nombre,cliente_identificacion,plan_nombre,precio,moneda,fecha_inicio,fecha_fin,condiciones,politica_congelacion,politica_cancelacion,firma_nombre,aceptado,aceptado_at,ip_aceptacion,registrado_por,created_at,updated_at) VALUES(v_id,CONCAT('CTR-',UUID_SHORT()),'1.0',v_nombre,NULLIF(v_identificacion,''),v_plan,v_precio,v_moneda,v_inicio,v_fin,'El cliente se compromete a cumplir el reglamento interno del gimnasio y utilizar responsablemente las instalaciones.','Las congelaciones requieren solicitud, fechas y motivo. Los días de extensión autorizados se suman al final de la vigencia.','La cancelación requiere categoría, motivo y fecha efectiva. Los pagos e historial ya registrados se conservan.',v_nombre,1,CURRENT_TIMESTAMP,p_ip,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 INSERT INTO historial_estados_membresia(membresia_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(v_id,v_estado,'Membresía creada pendiente de pago',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 INSERT INTO eventos_cliente(cliente_id,tipo,titulo,descripcion,entidad,entidad_id,usuario_id,ocurrido_at,created_at,updated_at) VALUES(v_cliente,'MEMBRESIA_CREADA','Membresía creada',CONCAT(v_plan,' · ',v_precio,' ',v_moneda),'membresias',v_id,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),(v_cliente,'CONTRATO_ACEPTADO','Contrato de membresía aceptado','Versión 1.0','contratos_membresia',LAST_INSERT_ID(),p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT; CALL sp_membresias_obtener(v_id);
END$$

DROP PROCEDURE IF EXISTS sp_membresias_renovar_rapida$$
CREATE PROCEDURE sp_membresias_renovar_rapida(IN p_anterior_id BIGINT UNSIGNED,IN p_tipo_id BIGINT UNSIGNED,IN p_precio_id BIGINT UNSIGNED,IN p_inicio DATE,IN p_usuario_id BIGINT UNSIGNED,IN p_ip VARCHAR(45))
BEGIN
 DECLARE v_cliente BIGINT UNSIGNED; DECLARE v_estado BIGINT UNSIGNED; DECLARE v_precio DECIMAL(12,2); DECLARE v_moneda CHAR(3); DECLARE v_dias SMALLINT UNSIGNED; DECLARE v_plan VARCHAR(120); DECLARE v_nombre VARCHAR(220); DECLARE v_fin DATE; DECLARE v_id BIGINT UNSIGNED;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 SELECT cliente_id INTO v_cliente FROM membresias WHERE id=p_anterior_id AND deleted_at IS NULL; SELECT id INTO v_estado FROM estados_membresia WHERE codigo='PENDIENTE' LIMIT 1;
 SELECT p.precio,p.moneda,t.duracion_dias,t.nombre INTO v_precio,v_moneda,v_dias,v_plan FROM precios_membresia p JOIN tipos_membresia t ON t.id=p.tipo_membresia_id WHERE p.id=p_precio_id AND p.tipo_membresia_id=p_tipo_id AND p.vigente_desde<=p_inicio AND (p.vigente_hasta IS NULL OR p.vigente_hasta>=p_inicio);
 SELECT CONCAT(nombre,' ',apellido) INTO v_nombre FROM clientes WHERE id=v_cliente;
 IF v_cliente IS NULL OR v_precio IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No se puede renovar con el plan seleccionado'; END IF; IF EXISTS(SELECT 1 FROM membresias WHERE membresia_anterior_id=p_anterior_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Esta membresía ya tiene una renovación'; END IF;
 SET v_fin=DATE_ADD(p_inicio,INTERVAL v_dias-1 DAY); START TRANSACTION;
 INSERT INTO membresias(cliente_id,tipo_membresia_id,precio_membresia_id,estado_membresia_id,membresia_anterior_id,fecha_inicio,fecha_fin,precio_contratado,moneda,bloqueo_activa,origen,creada_por,created_at,updated_at) VALUES(v_cliente,p_tipo_id,p_precio_id,v_estado,p_anterior_id,p_inicio,v_fin,v_precio,v_moneda,NULL,'RENOVACION',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP); SET v_id=LAST_INSERT_ID();
 INSERT INTO contratos_membresia(membresia_id,numero_contrato,version_documento,cliente_nombre,plan_nombre,precio,moneda,fecha_inicio,fecha_fin,condiciones,politica_congelacion,politica_cancelacion,firma_nombre,aceptado,aceptado_at,ip_aceptacion,registrado_por,created_at,updated_at) VALUES(v_id,CONCAT('CTR-',UUID_SHORT()),'1.0',v_nombre,v_plan,v_precio,v_moneda,p_inicio,v_fin,'Renovación de membresía conforme al reglamento vigente.','Las congelaciones requieren autorización.','La cancelación conserva todo el historial.',v_nombre,1,CURRENT_TIMESTAMP,p_ip,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 INSERT INTO historial_estados_membresia(membresia_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(v_id,v_estado,'Renovación pendiente de pago',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 INSERT INTO eventos_cliente(cliente_id,tipo,titulo,descripcion,entidad,entidad_id,usuario_id,ocurrido_at,created_at,updated_at) VALUES(v_cliente,'MEMBRESIA_RENOVADA','Membresía renovada',CONCAT('Nuevo plan: ',v_plan),'membresias',v_id,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT; CALL sp_membresias_obtener(v_id);
END$$

DROP PROCEDURE IF EXISTS sp_membresias_contrato$$
CREATE PROCEDURE sp_membresias_contrato(IN p_membresia_id BIGINT UNSIGNED)
BEGIN
 SELECT c.*,u.name registrado_por_nombre
 FROM contratos_membresia c LEFT JOIN users u ON u.id=c.registrado_por
 WHERE c.membresia_id=p_membresia_id;
END$$
DROP PROCEDURE IF EXISTS sp_membresias_categorias_cancelacion$$
CREATE PROCEDURE sp_membresias_categorias_cancelacion() BEGIN SELECT id,codigo,nombre FROM categorias_cancelacion_membresia WHERE activo=1 ORDER BY orden,nombre; END$$

DROP PROCEDURE IF EXISTS sp_membresias_cancelar_profesional$$
CREATE PROCEDURE sp_membresias_cancelar_profesional(IN p_id BIGINT UNSIGNED,IN p_categoria_id BIGINT UNSIGNED,IN p_fecha_efectiva DATE,IN p_motivo VARCHAR(255),IN p_observaciones TEXT,IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_cliente BIGINT UNSIGNED; DECLARE v_anterior BIGINT UNSIGNED; DECLARE v_cancelada BIGINT UNSIGNED; DECLARE v_categoria VARCHAR(100); DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 IF p_motivo IS NULL OR TRIM(p_motivo)='' OR p_fecha_efectiva IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Categoría, motivo y fecha efectiva son obligatorios'; END IF;
 SELECT nombre INTO v_categoria FROM categorias_cancelacion_membresia WHERE id=p_categoria_id AND activo=1; SELECT id INTO v_cancelada FROM estados_membresia WHERE codigo='CANCELADA';
 START TRANSACTION; SELECT cliente_id,estado_membresia_id INTO v_cliente,v_anterior FROM membresias WHERE id=p_id AND deleted_at IS NULL AND cancelada_at IS NULL FOR UPDATE; IF v_cliente IS NULL OR v_categoria IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Membresía o categoría no válida'; END IF;
 UPDATE membresias SET estado_membresia_id=v_cancelada,bloqueo_activa=NULL,cancelada_at=CURRENT_TIMESTAMP,motivo_cancelacion=p_motivo,cancelada_por=p_usuario_id,updated_at=CURRENT_TIMESTAMP WHERE id=p_id;
 INSERT INTO cancelaciones_membresia(membresia_id,categoria_id,fecha_efectiva,motivo,observaciones,cancelada_por,cancelada_at,created_at,updated_at) VALUES(p_id,p_categoria_id,p_fecha_efectiva,TRIM(p_motivo),NULLIF(TRIM(p_observaciones),''),p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 INSERT INTO historial_estados_membresia(membresia_id,estado_anterior_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(p_id,v_anterior,v_cancelada,CONCAT(v_categoria,': ',p_motivo),p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 INSERT INTO eventos_cliente(cliente_id,tipo,titulo,descripcion,entidad,entidad_id,usuario_id,ocurrido_at,created_at,updated_at) VALUES(v_cliente,'MEMBRESIA_CANCELADA','Membresía cancelada',CONCAT(v_categoria,': ',p_motivo),'membresias',p_id,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_familia_obtener$$
CREATE PROCEDURE sp_membresias_familia_obtener(IN p_membresia_id BIGINT UNSIGNED) BEGIN SELECT f.*,CONCAT(t.nombre,' ',t.apellido) titular,CONCAT(r.nombre,' ',r.apellido) responsable_pago FROM membresias_familiares f JOIN clientes t ON t.id=f.titular_cliente_id JOIN clientes r ON r.id=f.responsable_pago_cliente_id WHERE f.membresia_id=p_membresia_id; END$$
DROP PROCEDURE IF EXISTS sp_membresias_familia_beneficiarios$$
CREATE PROCEDURE sp_membresias_familia_beneficiarios(IN p_membresia_id BIGINT UNSIGNED) BEGIN SELECT b.*,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente FROM beneficiarios_membresia b JOIN membresias_familiares f ON f.id=b.membresia_familiar_id JOIN clientes c ON c.id=b.cliente_id WHERE f.membresia_id=p_membresia_id ORDER BY b.fecha_incorporacion,b.id; END$$
DROP PROCEDURE IF EXISTS sp_membresias_familia_configurar$$
CREATE PROCEDURE sp_membresias_familia_configurar(IN p_membresia_id BIGINT UNSIGNED,IN p_titular BIGINT UNSIGNED,IN p_responsable BIGINT UNSIGNED,IN p_limite SMALLINT UNSIGNED)
BEGIN
 DECLARE v_titular BIGINT UNSIGNED; DECLARE v_tipo VARCHAR(40); DECLARE v_limite SMALLINT UNSIGNED;
 SELECT m.cliente_id,t.codigo INTO v_titular,v_tipo FROM membresias m JOIN tipos_membresia t ON t.id=m.tipo_membresia_id WHERE m.id=p_membresia_id AND m.deleted_at IS NULL;
 IF v_titular IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La membresía no existe'; END IF;
 IF v_tipo NOT IN('PAREJA','FAMILIAR') THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El plan seleccionado no admite beneficiarios'; END IF;
 IF p_titular<>v_titular THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El titular debe ser el propietario de la membresía'; END IF;
 IF NOT EXISTS(SELECT 1 FROM clientes WHERE id=p_responsable AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El responsable del pago no existe'; END IF;
 SET v_limite=IF(v_tipo='PAREJA',1,3);
 INSERT INTO membresias_familiares(membresia_id,titular_cliente_id,responsable_pago_cliente_id,limite_beneficiarios,created_at,updated_at)
 VALUES(p_membresia_id,v_titular,p_responsable,v_limite,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)
 ON DUPLICATE KEY UPDATE titular_cliente_id=v_titular,responsable_pago_cliente_id=p_responsable,limite_beneficiarios=v_limite,updated_at=CURRENT_TIMESTAMP;
END$$
DROP PROCEDURE IF EXISTS sp_membresias_familia_agregar$$
CREATE PROCEDURE sp_membresias_familia_agregar(IN p_membresia_id BIGINT UNSIGNED,IN p_cliente_id BIGINT UNSIGNED,IN p_parentesco VARCHAR(60),IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_familia BIGINT UNSIGNED; DECLARE v_limite INT; DECLARE v_cliente_titular BIGINT UNSIGNED;
 SELECT id,limite_beneficiarios,titular_cliente_id INTO v_familia,v_limite,v_cliente_titular FROM membresias_familiares WHERE membresia_id=p_membresia_id;
 IF v_familia IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Primero configure el grupo de la membresía'; END IF;
 IF NOT EXISTS(SELECT 1 FROM clientes WHERE id=p_cliente_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El beneficiario seleccionado no existe'; END IF;
 IF p_cliente_id=v_cliente_titular THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El titular no debe repetirse como beneficiario'; END IF;
 IF EXISTS(SELECT 1 FROM membresias m JOIN estados_membresia e ON e.id=m.estado_membresia_id WHERE m.cliente_id=p_cliente_id AND m.bloqueo_activa=1 AND m.deleted_at IS NULL AND e.permite_acceso=1 AND CURRENT_DATE BETWEEN m.fecha_inicio AND m.fecha_fin) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente ya posee una membresía individual vigente'; END IF;
 IF EXISTS(SELECT 1 FROM beneficiarios_membresia b JOIN membresias_familiares f ON f.id=b.membresia_familiar_id JOIN membresias m ON m.id=f.membresia_id JOIN estados_membresia e ON e.id=m.estado_membresia_id WHERE b.cliente_id=p_cliente_id AND b.estado='ACTIVO' AND f.id<>v_familia AND m.bloqueo_activa=1 AND m.deleted_at IS NULL AND e.permite_acceso=1 AND CURRENT_DATE BETWEEN m.fecha_inicio AND m.fecha_fin) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente ya pertenece a otra membresía grupal vigente'; END IF;
 IF (SELECT COUNT(*) FROM beneficiarios_membresia WHERE membresia_familiar_id=v_familia AND estado='ACTIVO')>=v_limite THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Se alcanzó el límite de beneficiarios del plan'; END IF;
 INSERT INTO beneficiarios_membresia(membresia_familiar_id,cliente_id,parentesco,estado,fecha_incorporacion,fecha_retiro,motivo_retiro,registrado_por,created_at,updated_at)
 VALUES(v_familia,p_cliente_id,TRIM(p_parentesco),'ACTIVO',CURRENT_DATE,NULL,NULL,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)
 ON DUPLICATE KEY UPDATE parentesco=VALUES(parentesco),estado='ACTIVO',fecha_incorporacion=CURRENT_DATE,fecha_retiro=NULL,motivo_retiro=NULL,registrado_por=p_usuario_id,updated_at=CURRENT_TIMESTAMP;
END$$
DROP PROCEDURE IF EXISTS sp_membresias_familia_retirar$$
CREATE PROCEDURE sp_membresias_familia_retirar(IN p_beneficiario_id BIGINT UNSIGNED,IN p_motivo VARCHAR(255)) BEGIN UPDATE beneficiarios_membresia SET estado='RETIRADO',fecha_retiro=CURRENT_DATE,motivo_retiro=p_motivo,updated_at=CURRENT_TIMESTAMP WHERE id=p_beneficiario_id AND estado='ACTIVO'; IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El beneficiario no está activo'; END IF; END$$

DROP PROCEDURE IF EXISTS sp_membresias_por_vencer_3_dias$$
CREATE PROCEDURE sp_membresias_por_vencer_3_dias() BEGIN SELECT m.id membresia_id,m.cliente_id,c.nombre,c.apellido,c.correo_electronico,t.nombre plan,m.fecha_fin FROM membresias m JOIN clientes c ON c.id=m.cliente_id JOIN tipos_membresia t ON t.id=m.tipo_membresia_id JOIN estados_membresia e ON e.id=m.estado_membresia_id WHERE m.deleted_at IS NULL AND e.codigo='ACTIVA' AND m.fecha_fin=DATE_ADD(CURRENT_DATE,INTERVAL 3 DAY) AND c.correo_electronico IS NOT NULL AND NOT EXISTS(SELECT 1 FROM notificaciones_membresia n WHERE n.membresia_id=m.id AND n.tipo='VENCE_3_DIAS' AND n.estado='ENVIADA'); END$$
DROP PROCEDURE IF EXISTS sp_membresias_notificacion_resultado$$
CREATE PROCEDURE sp_membresias_notificacion_resultado(IN p_membresia_id BIGINT UNSIGNED,IN p_tipo VARCHAR(40),IN p_destinatario VARCHAR(150),IN p_exito BOOLEAN,IN p_error TEXT) BEGIN INSERT INTO notificaciones_membresia(membresia_id,tipo,destinatario,estado,enviado_at,error,intentos,created_at,updated_at) VALUES(p_membresia_id,p_tipo,p_destinatario,IF(p_exito,'ENVIADA','FALLIDA'),IF(p_exito,CURRENT_TIMESTAMP,NULL),p_error,1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE estado=VALUES(estado),enviado_at=VALUES(enviado_at),error=VALUES(error),intentos=intentos+1,updated_at=CURRENT_TIMESTAMP; END$$
DROP PROCEDURE IF EXISTS sp_membresias_vencer$$
CREATE PROCEDURE sp_membresias_vencer(IN p_usuario_id BIGINT UNSIGNED)
BEGIN DECLARE v_estado BIGINT UNSIGNED; SELECT id INTO v_estado FROM estados_membresia WHERE codigo='VENCIDA'; INSERT INTO historial_estados_membresia(membresia_id,estado_anterior_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) SELECT m.id,m.estado_membresia_id,v_estado,'Vencimiento automático',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP FROM membresias m JOIN estados_membresia e ON e.id=m.estado_membresia_id WHERE m.deleted_at IS NULL AND m.fecha_fin<CURRENT_DATE AND e.codigo='ACTIVA'; INSERT INTO eventos_cliente(cliente_id,tipo,titulo,descripcion,entidad,entidad_id,usuario_id,ocurrido_at,created_at,updated_at) SELECT m.cliente_id,'MEMBRESIA_VENCIDA','Membresía vencida',CONCAT('Finalizó el ',DATE_FORMAT(m.fecha_fin,'%d/%m/%Y')),'membresias',m.id,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP FROM membresias m JOIN estados_membresia e ON e.id=m.estado_membresia_id WHERE m.deleted_at IS NULL AND m.fecha_fin<CURRENT_DATE AND e.codigo='ACTIVA'; UPDATE membresias m JOIN estados_membresia e ON e.id=m.estado_membresia_id SET m.estado_membresia_id=v_estado,m.bloqueo_activa=NULL,m.updated_at=CURRENT_TIMESTAMP WHERE m.deleted_at IS NULL AND m.fecha_fin<CURRENT_DATE AND e.codigo='ACTIVA'; SELECT ROW_COUNT() actualizadas; END$$
DROP PROCEDURE IF EXISTS sp_membresias_congelar$$
CREATE PROCEDURE sp_membresias_congelar(IN p_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_inicio DATE,IN p_fin DATE,IN p_dias_extension SMALLINT UNSIGNED,IN p_motivo VARCHAR(255),IN p_usuario_id BIGINT UNSIGNED)
BEGIN DECLARE v_cliente BIGINT UNSIGNED; IF p_fin IS NOT NULL AND p_fin<p_inicio THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El periodo de congelamiento no es válido'; END IF; IF EXISTS(SELECT 1 FROM suspensiones_membresia WHERE membresia_id=p_id AND (fecha_fin IS NULL OR fecha_fin>=p_inicio) AND (p_fin IS NULL OR fecha_inicio<=p_fin)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El congelamiento se superpone con otro'; END IF; SELECT cliente_id INTO v_cliente FROM membresias WHERE id=p_id AND deleted_at IS NULL; IF v_cliente IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La membresía no existe'; END IF; INSERT INTO suspensiones_membresia(membresia_id,fecha_inicio,fecha_fin,dias_extension,motivo,autorizada_por,created_at,updated_at) VALUES(p_id,p_inicio,p_fin,COALESCE(p_dias_extension,0),p_motivo,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP); UPDATE membresias SET fecha_fin=DATE_ADD(fecha_fin,INTERVAL COALESCE(p_dias_extension,0) DAY),updated_at=CURRENT_TIMESTAMP WHERE id=p_id; INSERT INTO eventos_cliente(cliente_id,tipo,titulo,descripcion,entidad,entidad_id,usuario_id,ocurrido_at,created_at,updated_at) VALUES(v_cliente,'MEMBRESIA_CONGELADA','Congelación registrada',p_motivo,'membresias',p_id,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP); END$$
DROP PROCEDURE IF EXISTS sp_membresias_reactivar$$
CREATE PROCEDURE sp_membresias_reactivar(IN p_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_fecha DATE,IN p_motivo VARCHAR(255),IN p_usuario_id BIGINT UNSIGNED)
BEGIN DECLARE v_cliente BIGINT UNSIGNED; SELECT cliente_id INTO v_cliente FROM membresias WHERE id=p_id AND deleted_at IS NULL; IF v_cliente IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La membresía no existe'; END IF; UPDATE suspensiones_membresia SET fecha_fin=p_fecha,updated_at=CURRENT_TIMESTAMP WHERE membresia_id=p_id AND fecha_inicio<=p_fecha AND (fecha_fin IS NULL OR fecha_fin>=p_fecha); IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No existe congelación vigente'; END IF; INSERT INTO eventos_cliente(cliente_id,tipo,titulo,descripcion,entidad,entidad_id,usuario_id,ocurrido_at,created_at,updated_at) VALUES(v_cliente,'MEMBRESIA_REACTIVADA','Membresía reactivada',p_motivo,'membresias',p_id,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP); END$$
DELIMITER ;


DROP PROCEDURE IF EXISTS sp_tipos_membresia_listar;
CREATE PROCEDURE sp_tipos_membresia_listar(IN p_buscar VARCHAR(100), IN p_limite INT, IN p_offset INT)
BEGIN
    SELECT 
        tm.id, tm.codigo, tm.nombre, tm.descripcion, tm.duracion_dias, tm.activo,
        pm.precio, pm.moneda
    FROM tipos_membresia tm
    -- Traemos solo el precio actual (el que no tiene fecha de vencimiento)
    LEFT JOIN precios_membresia pm ON tm.id = pm.tipo_membresia_id AND pm.vigente_hasta IS NULL
    WHERE p_buscar = '' 
       OR tm.codigo LIKE CONCAT('%', p_buscar, '%') 
       OR tm.nombre LIKE CONCAT('%', p_buscar, '%')
    ORDER BY tm.id DESC
    LIMIT p_limite OFFSET p_offset;
END;

DROP PROCEDURE IF EXISTS sp_tipos_membresia_crear;
CREATE PROCEDURE sp_tipos_membresia_crear(
    IN p_codigo VARCHAR(30), IN p_nombre VARCHAR(50), IN p_descripcion TEXT, 
    IN p_duracion_dias INT, IN p_activo TINYINT,
    IN p_precio DECIMAL(10,2), IN p_moneda VARCHAR(10)
)
BEGIN
    DECLARE v_tipo_id BIGINT;
    
    -- 1. Insertamos el tipo de membresía
    INSERT INTO tipos_membresia (codigo, nombre, descripcion, duracion_dias, activo, created_at, updated_at) 
    VALUES (p_codigo, p_nombre, p_descripcion, p_duracion_dias, p_activo, NOW(), NOW());
    
    SET v_tipo_id = LAST_INSERT_ID();
    
    -- 2. Insertamos el precio inicial
    INSERT INTO precios_membresia (tipo_membresia_id, precio, moneda, vigente_desde, motivo_cambio, created_at, updated_at)
    VALUES (v_tipo_id, p_precio, p_moneda, CURDATE(), 'Precio inicial', NOW(), NOW());
END;

DROP PROCEDURE IF EXISTS sp_tipos_membresia_actualizar;
CREATE PROCEDURE sp_tipos_membresia_actualizar(
    IN p_id BIGINT, IN p_codigo VARCHAR(30), IN p_nombre VARCHAR(50), 
    IN p_descripcion TEXT, IN p_duracion_dias INT, IN p_activo TINYINT,
    IN p_precio DECIMAL(10,2), IN p_moneda VARCHAR(10)
)
BEGIN
    DECLARE v_precio_actual DECIMAL(10,2);
    DECLARE v_moneda_actual VARCHAR(10);
    
    -- 1. Actualizar los datos base
    UPDATE tipos_membresia 
    SET codigo = p_codigo, nombre = p_nombre, descripcion = p_descripcion, 
        duracion_dias = p_duracion_dias, activo = p_activo, updated_at = NOW() 
    WHERE id = p_id;
    
    -- 2. Obtener el precio actual
    SELECT precio, moneda INTO v_precio_actual, v_moneda_actual
    FROM precios_membresia 
    WHERE tipo_membresia_id = p_id AND vigente_hasta IS NULL 
    ORDER BY id DESC LIMIT 1;
    
    -- 3. Si cambiaron el precio en el formulario, guardamos el historial
    IF v_precio_actual IS NULL OR v_precio_actual != p_precio OR v_moneda_actual != p_moneda THEN
        -- Caducar precio anterior
        UPDATE precios_membresia 
        SET vigente_hasta = CURDATE(), updated_at = NOW() 
        WHERE tipo_membresia_id = p_id AND vigente_hasta IS NULL;
        
        -- Insertar nuevo precio
        INSERT INTO precios_membresia (tipo_membresia_id, precio, moneda, vigente_desde, motivo_cambio, created_at, updated_at)
        VALUES (p_id, p_precio, p_moneda, CURDATE(), 'Actualización de precio', NOW(), NOW());
    END IF;
END;

DROP PROCEDURE IF EXISTS sp_tipos_membresia_eliminar;
CREATE PROCEDURE sp_tipos_membresia_eliminar(IN p_id BIGINT)
BEGIN
    -- Borramos en cascada manual: primero los precios, luego la membresía
    DELETE FROM precios_membresia WHERE tipo_membresia_id = p_id;
    DELETE FROM tipos_membresia WHERE id = p_id;
END;


DROP PROCEDURE IF EXISTS sp_estados_membresia_listar;
CREATE PROCEDURE sp_estados_membresia_listar(IN p_buscar VARCHAR(100), IN p_limite INT, IN p_offset INT)
BEGIN
    SELECT id, codigo, nombre, permite_acceso, es_terminal, orden
    FROM estados_membresia
    WHERE p_buscar = '' OR codigo LIKE CONCAT('%', p_buscar, '%') OR nombre LIKE CONCAT('%', p_buscar, '%')
    ORDER BY orden ASC, id DESC
    LIMIT p_limite OFFSET p_offset;
END;

DROP PROCEDURE IF EXISTS sp_estados_membresia_crear;
CREATE PROCEDURE sp_estados_membresia_crear(IN p_codigo VARCHAR(30), IN p_nombre VARCHAR(50), IN p_permite_acceso TINYINT, IN p_es_terminal TINYINT, IN p_orden SMALLINT)
BEGIN
    INSERT INTO estados_membresia (codigo, nombre, permite_acceso, es_terminal, orden, created_at, updated_at) 
    VALUES (p_codigo, p_nombre, p_permite_acceso, p_es_terminal, p_orden, NOW(), NOW());
END;

DROP PROCEDURE IF EXISTS sp_estados_membresia_actualizar;
CREATE PROCEDURE sp_estados_membresia_actualizar(IN p_id BIGINT, IN p_codigo VARCHAR(30), IN p_nombre VARCHAR(50), IN p_permite_acceso TINYINT, IN p_es_terminal TINYINT, IN p_orden SMALLINT)
BEGIN
    UPDATE estados_membresia SET codigo = p_codigo, nombre = p_nombre, permite_acceso = p_permite_acceso, es_terminal = p_es_terminal, orden = p_orden, updated_at = NOW() WHERE id = p_id;
END;

DROP PROCEDURE IF EXISTS sp_estados_membresia_eliminar;
CREATE PROCEDURE sp_estados_membresia_eliminar(IN p_id BIGINT)
BEGIN
    DELETE FROM estados_membresia WHERE id = p_id;
END;
