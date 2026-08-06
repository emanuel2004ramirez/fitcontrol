-- FitControl: procedimientos complementarios del módulo Clientes.
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_clientes_filtrar$$
CREATE PROCEDURE sp_clientes_filtrar(
 IN p_texto VARCHAR(150), IN p_estado_id BIGINT UNSIGNED, IN p_sexo_id BIGINT UNSIGNED,
 IN p_fecha_desde DATE, IN p_fecha_hasta DATE, IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
 IF p_limite IS NULL OR p_limite < 1 OR p_limite > 100 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El límite debe estar entre 1 y 100'; END IF;
 SELECT c.id,c.numero_socio,c.nombre,c.apellido,CONCAT(c.nombre,' ',c.apellido) nombre_completo,
   c.sexo_id,s.nombre sexo,c.estado_cliente_id,e.codigo estado_codigo,e.nombre estado,
   c.tipo_identificacion,c.numero_identificacion,c.telefono,c.correo_electronico,c.direccion,
   c.ciudad,c.pais,c.fecha_nacimiento,c.fecha_registro,c.motivo_estado,c.created_at,c.updated_at
 FROM clientes c
 LEFT JOIN sexos s ON s.id=c.sexo_id
 JOIN estados_cliente e ON e.id=c.estado_cliente_id
 WHERE c.deleted_at IS NULL
 AND (p_texto IS NULL OR p_texto='' OR c.numero_socio LIKE CONCAT('%',p_texto,'%')
   OR c.nombre LIKE CONCAT('%',p_texto,'%') OR c.apellido LIKE CONCAT('%',p_texto,'%')
   OR CONCAT(c.nombre,' ',c.apellido) LIKE CONCAT('%',p_texto,'%')
   OR c.numero_identificacion LIKE CONCAT('%',p_texto,'%') OR c.correo_electronico LIKE CONCAT('%',p_texto,'%')
   OR c.telefono LIKE CONCAT('%',p_texto,'%'))
 AND (p_estado_id IS NULL OR c.estado_cliente_id=p_estado_id)
 AND (p_sexo_id IS NULL OR c.sexo_id=p_sexo_id)
 AND (p_fecha_desde IS NULL OR c.fecha_registro>=p_fecha_desde)
 AND (p_fecha_hasta IS NULL OR c.fecha_registro<=p_fecha_hasta)
 ORDER BY c.apellido,c.nombre,c.id LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_clientes_contar$$
CREATE PROCEDURE sp_clientes_contar(
 IN p_texto VARCHAR(150), IN p_estado_id BIGINT UNSIGNED, IN p_sexo_id BIGINT UNSIGNED,
 IN p_fecha_desde DATE, IN p_fecha_hasta DATE)
BEGIN
 SELECT COUNT(*) total FROM clientes c WHERE c.deleted_at IS NULL
 AND (p_texto IS NULL OR p_texto='' OR c.numero_socio LIKE CONCAT('%',p_texto,'%')
   OR c.nombre LIKE CONCAT('%',p_texto,'%') OR c.apellido LIKE CONCAT('%',p_texto,'%')
   OR CONCAT(c.nombre,' ',c.apellido) LIKE CONCAT('%',p_texto,'%')
   OR c.numero_identificacion LIKE CONCAT('%',p_texto,'%') OR c.correo_electronico LIKE CONCAT('%',p_texto,'%')
   OR c.telefono LIKE CONCAT('%',p_texto,'%'))
 AND (p_estado_id IS NULL OR c.estado_cliente_id=p_estado_id)
 AND (p_sexo_id IS NULL OR c.sexo_id=p_sexo_id)
 AND (p_fecha_desde IS NULL OR c.fecha_registro>=p_fecha_desde)
 AND (p_fecha_hasta IS NULL OR c.fecha_registro<=p_fecha_hasta);
END$$

DROP PROCEDURE IF EXISTS sp_clientes_obtener$$
CREATE PROCEDURE sp_clientes_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
 IF NOT EXISTS(SELECT 1 FROM clientes WHERE id=p_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente no existe o fue retirado'; END IF;
 SELECT c.*,CONCAT(c.nombre,' ',c.apellido) nombre_completo,s.nombre sexo,
   e.codigo estado_codigo,e.nombre estado,e.es_terminal estado_terminal
 FROM clientes c LEFT JOIN sexos s ON s.id=c.sexo_id
 JOIN estados_cliente e ON e.id=c.estado_cliente_id WHERE c.id=p_id AND c.deleted_at IS NULL;
END$$

DROP PROCEDURE IF EXISTS sp_clientes_contactos_emergencia$$
CREATE PROCEDURE sp_clientes_contactos_emergencia(IN p_cliente_id BIGINT UNSIGNED)
BEGIN
 SELECT id,cliente_id,nombre_completo,parentesco,telefono,es_principal,created_at,updated_at
 FROM contactos_emergencia WHERE cliente_id=p_cliente_id ORDER BY es_principal DESC,nombre_completo,id;
END$$

DROP PROCEDURE IF EXISTS sp_clientes_datos_medicos$$
CREATE PROCEDURE sp_clientes_datos_medicos(IN p_cliente_id BIGINT UNSIGNED)
BEGIN
 SELECT id,cliente_id,condiciones_medicas,alergias,medicamentos,restricciones_ejercicio,
   contacto_medico,actualizado_at,actualizado_por,created_at,updated_at
 FROM datos_medicos_cliente WHERE cliente_id=p_cliente_id AND deleted_at IS NULL LIMIT 1;
END$$

DROP PROCEDURE IF EXISTS sp_clientes_consentimientos$$
CREATE PROCEDURE sp_clientes_consentimientos(IN p_cliente_id BIGINT UNSIGNED)
BEGIN
 SELECT id,cliente_id,tipo,version_documento,aceptado,registrado_at,ip,registrado_por,created_at
 FROM consentimientos_cliente WHERE cliente_id=p_cliente_id ORDER BY registrado_at DESC,id DESC;
END$$

DROP PROCEDURE IF EXISTS sp_clientes_historial_estados$$
CREATE PROCEDURE sp_clientes_historial_estados(IN p_cliente_id BIGINT UNSIGNED)
BEGIN
 SELECT h.id,ea.nombre estado_anterior,en.nombre estado_nuevo,h.motivo,h.cambiado_por,h.cambiado_at
 FROM historial_estados_cliente h
 LEFT JOIN estados_cliente ea ON ea.id=h.estado_anterior_id
 JOIN estados_cliente en ON en.id=h.estado_nuevo_id
 WHERE h.cliente_id=p_cliente_id ORDER BY h.cambiado_at DESC,h.id DESC;
END$$

DROP PROCEDURE IF EXISTS sp_contactos_emergencia_guardar$$
CREATE PROCEDURE sp_contactos_emergencia_guardar(
 IN p_id BIGINT UNSIGNED,IN p_cliente_id BIGINT UNSIGNED,IN p_nombre VARCHAR(150),
 IN p_parentesco VARCHAR(60),IN p_telefono VARCHAR(25),IN p_principal BOOLEAN)
BEGIN
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 IF NOT EXISTS(SELECT 1 FROM clientes WHERE id=p_cliente_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente no existe o fue retirado'; END IF;
 IF p_id IS NOT NULL AND NOT EXISTS(SELECT 1 FROM contactos_emergencia WHERE id=p_id AND cliente_id=p_cliente_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El contacto no pertenece al cliente'; END IF;
 START TRANSACTION;
 IF COALESCE(p_principal,0)=1 THEN UPDATE contactos_emergencia SET es_principal=0,updated_at=CURRENT_TIMESTAMP WHERE cliente_id=p_cliente_id; END IF;
 IF p_id IS NULL THEN
  INSERT INTO contactos_emergencia(cliente_id,nombre_completo,parentesco,telefono,es_principal,created_at,updated_at)
  VALUES(p_cliente_id,TRIM(p_nombre),TRIM(p_parentesco),TRIM(p_telefono),COALESCE(p_principal,0),CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
  SET p_id=LAST_INSERT_ID();
 ELSE
  UPDATE contactos_emergencia SET nombre_completo=TRIM(p_nombre),parentesco=TRIM(p_parentesco),telefono=TRIM(p_telefono),es_principal=COALESCE(p_principal,0),updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND cliente_id=p_cliente_id;
 END IF;
 COMMIT;
 SELECT * FROM contactos_emergencia WHERE id=p_id;
END$$

DROP PROCEDURE IF EXISTS sp_contactos_emergencia_eliminar$$
CREATE PROCEDURE sp_contactos_emergencia_eliminar(IN p_cliente_id BIGINT UNSIGNED, IN p_id BIGINT UNSIGNED)
BEGIN
 DELETE FROM contactos_emergencia WHERE id=p_id AND cliente_id=p_cliente_id;
 IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El contacto no existe para este cliente'; END IF;
END$$

DROP PROCEDURE IF EXISTS sp_clientes_crear$$
CREATE PROCEDURE sp_clientes_crear(
 IN p_numero_socio VARCHAR(30), IN p_sexo_id BIGINT UNSIGNED, IN p_estado_id BIGINT UNSIGNED,
 IN p_nombre VARCHAR(100), IN p_apellido VARCHAR(100), IN p_tipo_identificacion VARCHAR(30),
 IN p_numero_identificacion VARCHAR(60), IN p_telefono VARCHAR(25), IN p_correo VARCHAR(150),
 IN p_direccion VARCHAR(200), IN p_ciudad VARCHAR(100), IN p_pais CHAR(2), IN p_fecha_nacimiento DATE,
 IN p_creado_por BIGINT UNSIGNED)
BEGIN
 DECLARE v_id BIGINT UNSIGNED;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 IF p_nombre IS NULL OR TRIM(p_nombre)='' OR p_apellido IS NULL OR TRIM(p_apellido)='' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Nombre y apellido son obligatorios'; END IF;
 IF p_correo IS NULL OR TRIM(p_correo)='' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El correo electrónico es obligatorio'; END IF;
 IF p_fecha_nacimiento IS NOT NULL AND p_fecha_nacimiento>CURRENT_DATE THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La fecha de nacimiento no puede ser futura'; END IF;
 IF NOT EXISTS(SELECT 1 FROM estados_cliente WHERE id=p_estado_id AND activo=1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El estado del cliente no es válido'; END IF;
 IF p_sexo_id IS NOT NULL AND NOT EXISTS(SELECT 1 FROM sexos WHERE id=p_sexo_id AND activo=1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El sexo seleccionado no es válido'; END IF;
 IF p_numero_identificacion IS NOT NULL AND TRIM(p_numero_identificacion)<>'' AND EXISTS(SELECT 1 FROM clientes WHERE tipo_identificacion=TRIM(p_tipo_identificacion) AND numero_identificacion=TRIM(p_numero_identificacion)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El número de identificación ya pertenece a otro cliente'; END IF;
 IF p_correo IS NOT NULL AND TRIM(p_correo)<>'' AND EXISTS(SELECT 1 FROM clientes WHERE correo_electronico=LOWER(TRIM(p_correo))) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El correo electrónico ya está registrado'; END IF;
 START TRANSACTION;
 INSERT INTO clientes(numero_socio,sexo_id,estado_cliente_id,nombre,apellido,tipo_identificacion,numero_identificacion,telefono,correo_electronico,direccion,ciudad,pais,fecha_nacimiento,fecha_registro,created_at,updated_at)
 VALUES(CONCAT('TMP-',UUID_SHORT()),p_sexo_id,p_estado_id,TRIM(p_nombre),TRIM(p_apellido),NULLIF(TRIM(p_tipo_identificacion),''),NULLIF(TRIM(p_numero_identificacion),''),NULLIF(TRIM(p_telefono),''),NULLIF(LOWER(TRIM(p_correo)),''),NULLIF(TRIM(p_direccion),''),NULLIF(TRIM(p_ciudad),''),UPPER(p_pais),p_fecha_nacimiento,CURRENT_DATE,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SET v_id=LAST_INSERT_ID();
 UPDATE clientes SET numero_socio=CONCAT('CLI-',LPAD(v_id,6,'0')) WHERE id=v_id;
 INSERT INTO historial_estados_cliente(cliente_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(v_id,p_estado_id,'Alta de cliente',p_creado_por,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
 SELECT * FROM clientes WHERE id=v_id;
END$$

DROP PROCEDURE IF EXISTS sp_clientes_actualizar$$
CREATE PROCEDURE sp_clientes_actualizar(
 IN p_id BIGINT UNSIGNED, IN p_sexo_id BIGINT UNSIGNED, IN p_nombre VARCHAR(100), IN p_apellido VARCHAR(100),
 IN p_tipo_identificacion VARCHAR(30), IN p_numero_identificacion VARCHAR(60), IN p_telefono VARCHAR(25),
 IN p_correo VARCHAR(150), IN p_direccion VARCHAR(200), IN p_ciudad VARCHAR(100), IN p_pais CHAR(2), IN p_fecha_nacimiento DATE)
BEGIN
 IF NOT EXISTS(SELECT 1 FROM clientes WHERE id=p_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Cliente no encontrado'; END IF;
 IF p_correo IS NULL OR TRIM(p_correo)='' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El correo electrónico es obligatorio'; END IF;
 IF p_fecha_nacimiento IS NOT NULL AND p_fecha_nacimiento>CURRENT_DATE THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La fecha de nacimiento no puede ser futura'; END IF;
 IF p_numero_identificacion IS NOT NULL AND TRIM(p_numero_identificacion)<>'' AND EXISTS(SELECT 1 FROM clientes WHERE tipo_identificacion=TRIM(p_tipo_identificacion) AND numero_identificacion=TRIM(p_numero_identificacion) AND id<>p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El número de identificación ya pertenece a otro cliente'; END IF;
 IF p_correo IS NOT NULL AND TRIM(p_correo)<>'' AND EXISTS(SELECT 1 FROM clientes WHERE correo_electronico=LOWER(TRIM(p_correo)) AND id<>p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El correo electrónico ya está registrado'; END IF;
 UPDATE clientes SET sexo_id=p_sexo_id,nombre=TRIM(p_nombre),apellido=TRIM(p_apellido),tipo_identificacion=NULLIF(TRIM(p_tipo_identificacion),''),numero_identificacion=NULLIF(TRIM(p_numero_identificacion),''),telefono=NULLIF(TRIM(p_telefono),''),correo_electronico=NULLIF(LOWER(TRIM(p_correo)),''),direccion=NULLIF(TRIM(p_direccion),''),ciudad=NULLIF(TRIM(p_ciudad),''),pais=UPPER(p_pais),fecha_nacimiento=p_fecha_nacimiento,updated_at=CURRENT_TIMESTAMP WHERE id=p_id;
 SELECT * FROM clientes WHERE id=p_id;
END$$

DROP PROCEDURE IF EXISTS sp_eventos_cliente_registrar$$
CREATE PROCEDURE sp_eventos_cliente_registrar(IN p_cliente_id BIGINT UNSIGNED,IN p_tipo VARCHAR(60),IN p_titulo VARCHAR(150),IN p_descripcion TEXT,IN p_entidad VARCHAR(80),IN p_entidad_id BIGINT UNSIGNED,IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 INSERT INTO eventos_cliente(cliente_id,tipo,titulo,descripcion,entidad,entidad_id,usuario_id,ocurrido_at,created_at,updated_at) VALUES(p_cliente_id,p_tipo,p_titulo,p_descripcion,p_entidad,p_entidad_id,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
END$$

DROP PROCEDURE IF EXISTS sp_clientes_crear_expediente$$
CREATE PROCEDURE sp_clientes_crear_expediente(IN p_datos JSON,IN p_usuario_id BIGINT UNSIGNED,IN p_ip VARCHAR(45))
BEGIN
 DECLARE v_id BIGINT UNSIGNED; DECLARE v_estado BIGINT UNSIGNED; DECLARE v_correo VARCHAR(150); DECLARE v_nombre VARCHAR(100); DECLARE v_apellido VARCHAR(100);
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 SET v_nombre=TRIM(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.nombre'))); SET v_apellido=TRIM(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.apellido'))); SET v_correo=LOWER(TRIM(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.correo_electronico')))); SET v_estado=JSON_EXTRACT(p_datos,'$.estado_cliente_id');
 IF v_nombre IS NULL OR v_nombre='' OR v_apellido IS NULL OR v_apellido='' OR v_correo IS NULL OR v_correo='' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Nombre, apellido y correo son obligatorios'; END IF;
 IF EXISTS(SELECT 1 FROM clientes WHERE correo_electronico=v_correo) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El correo electrónico ya está registrado'; END IF;
 IF JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.contacto.nombre_completo')) IS NULL OR JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.contacto.telefono')) IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El contacto de emergencia es obligatorio'; END IF;
 IF COALESCE(JSON_EXTRACT(p_datos,'$.consentimiento.aceptado'),0)<>1 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Debe aceptar el consentimiento de privacidad'; END IF;
 START TRANSACTION;
 INSERT INTO clientes(numero_socio,sexo_id,estado_cliente_id,nombre,apellido,tipo_identificacion,numero_identificacion,telefono,correo_electronico,direccion,ciudad,fecha_nacimiento,fecha_registro,created_at,updated_at)
 VALUES(CONCAT('TMP-',UUID_SHORT()),JSON_EXTRACT(p_datos,'$.sexo_id'),v_estado,v_nombre,v_apellido,NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.tipo_identificacion')),''),NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.numero_identificacion')),''),NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.telefono')),''),v_correo,NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.direccion')),''),NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.ciudad')),''),JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.fecha_nacimiento')),CURRENT_DATE,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SET v_id=LAST_INSERT_ID(); UPDATE clientes SET numero_socio=CONCAT('CLI-',LPAD(v_id,6,'0')) WHERE id=v_id;
 INSERT INTO contactos_emergencia(cliente_id,nombre_completo,parentesco,telefono,es_principal,created_at,updated_at) VALUES(v_id,TRIM(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.contacto.nombre_completo'))),TRIM(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.contacto.parentesco'))),TRIM(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.contacto.telefono'))),1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 INSERT INTO datos_medicos_cliente(cliente_id,condiciones_medicas,alergias,medicamentos,restricciones_ejercicio,contacto_medico,es_confidencial,actualizado_at,actualizado_por,created_at,updated_at) VALUES(v_id,NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.medico.condiciones_medicas')),''),NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.medico.alergias')),''),NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.medico.medicamentos')),''),NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.medico.restricciones_ejercicio')),''),NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.medico.contacto_medico')),''),1,CURRENT_TIMESTAMP,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 INSERT INTO consentimientos_cliente(cliente_id,tipo,version_documento,aceptado,registrado_at,ip,registrado_por,created_at,updated_at) VALUES(v_id,'PRIVACIDAD_DATOS','1.0',1,CURRENT_TIMESTAMP,p_ip,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 INSERT INTO historial_estados_cliente(cliente_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(v_id,v_estado,'Alta de cliente',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 INSERT INTO eventos_cliente(cliente_id,tipo,titulo,descripcion,entidad,entidad_id,usuario_id,ocurrido_at,created_at,updated_at) VALUES(v_id,'CLIENTE_REGISTRADO','Cliente registrado','Expediente inicial completado','clientes',v_id,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),(v_id,'CONTACTO_AGREGADO','Contacto de emergencia agregado',JSON_UNQUOTE(JSON_EXTRACT(p_datos,'$.contacto.nombre_completo')),'contactos_emergencia',LAST_INSERT_ID(),p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),(v_id,'CONSENTIMIENTO_ACEPTADO','Consentimiento de privacidad aceptado','Versión 1.0','consentimientos_cliente',NULL,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT; SELECT * FROM clientes WHERE id=v_id;
END$$

DROP PROCEDURE IF EXISTS sp_clientes_linea_tiempo$$
CREATE PROCEDURE sp_clientes_linea_tiempo(IN p_cliente_id BIGINT UNSIGNED)
BEGIN SELECT id,tipo,titulo,descripcion,entidad,entidad_id,usuario_id,ocurrido_at FROM eventos_cliente WHERE cliente_id=p_cliente_id ORDER BY ocurrido_at DESC,id DESC LIMIT 200; END$$
DELIMITER ;
