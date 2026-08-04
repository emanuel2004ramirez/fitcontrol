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

DELIMITER ;
