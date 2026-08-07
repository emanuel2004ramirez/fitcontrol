-- FitControl: procedimientos del módulo Evaluaciones Físicas.
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_evaluaciones_filtrar$$
CREATE PROCEDURE sp_evaluaciones_filtrar(IN p_texto VARCHAR(150),IN p_cliente_id BIGINT UNSIGNED,IN p_evaluador_id BIGINT UNSIGNED,IN p_desde DATE,IN p_hasta DATE,IN p_limite INT UNSIGNED,IN p_offset INT UNSIGNED) BEGIN SELECT e.id,e.cliente_id,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente,e.evaluador_id,CONCAT(p.nombre,' ',p.apellido) evaluador,e.evaluada_at,e.metodo,e.observaciones,(SELECT COUNT(*) FROM detalles_evaluacion d WHERE d.evaluacion_fisica_id=e.id) medidas FROM evaluaciones_fisicas e JOIN clientes c ON c.id=e.cliente_id JOIN personal p ON p.id=e.evaluador_id WHERE e.deleted_at IS NULL AND (p_texto IS NULL OR p_texto='' OR c.numero_socio LIKE CONCAT('%',p_texto,'%') OR c.nombre LIKE CONCAT('%',p_texto,'%') OR c.apellido LIKE CONCAT('%',p_texto,'%') OR e.metodo LIKE CONCAT('%',p_texto,'%')) AND (p_cliente_id IS NULL OR e.cliente_id=p_cliente_id) AND (p_evaluador_id IS NULL OR e.evaluador_id=p_evaluador_id) AND (p_desde IS NULL OR DATE(e.evaluada_at)>=p_desde) AND (p_hasta IS NULL OR DATE(e.evaluada_at)<=p_hasta) ORDER BY e.evaluada_at DESC,e.id DESC LIMIT p_limite OFFSET p_offset; END$$
DROP PROCEDURE IF EXISTS sp_evaluaciones_contar$$
CREATE PROCEDURE sp_evaluaciones_contar(IN p_texto VARCHAR(150),IN p_cliente_id BIGINT UNSIGNED,IN p_evaluador_id BIGINT UNSIGNED,IN p_desde DATE,IN p_hasta DATE) BEGIN SELECT COUNT(*) total FROM evaluaciones_fisicas e JOIN clientes c ON c.id=e.cliente_id WHERE e.deleted_at IS NULL AND (p_texto IS NULL OR p_texto='' OR c.numero_socio LIKE CONCAT('%',p_texto,'%') OR c.nombre LIKE CONCAT('%',p_texto,'%') OR c.apellido LIKE CONCAT('%',p_texto,'%') OR e.metodo LIKE CONCAT('%',p_texto,'%')) AND (p_cliente_id IS NULL OR e.cliente_id=p_cliente_id) AND (p_evaluador_id IS NULL OR e.evaluador_id=p_evaluador_id) AND (p_desde IS NULL OR DATE(e.evaluada_at)>=p_desde) AND (p_hasta IS NULL OR DATE(e.evaluada_at)<=p_hasta); END$$
DROP PROCEDURE IF EXISTS sp_evaluaciones_fisicas_obtener$$
CREATE PROCEDURE sp_evaluaciones_fisicas_obtener(IN p_id BIGINT UNSIGNED) BEGIN IF NOT EXISTS(SELECT 1 FROM evaluaciones_fisicas WHERE id=p_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La evaluación no existe';END IF;SELECT e.*,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente,CONCAT(p.nombre,' ',p.apellido) evaluador FROM evaluaciones_fisicas e JOIN clientes c ON c.id=e.cliente_id JOIN personal p ON p.id=e.evaluador_id WHERE e.id=p_id;END$$
DROP PROCEDURE IF EXISTS sp_evaluaciones_medidas$$
CREATE PROCEDURE sp_evaluaciones_medidas(IN p_id BIGINT UNSIGNED) BEGIN SELECT d.id,d.tipo_medida_id,t.codigo,t.nombre,d.valor,d.unidad_snapshot,t.decimales,d.instrumento,d.observaciones,d.created_at FROM detalles_evaluacion d JOIN tipos_medida t ON t.id=d.tipo_medida_id WHERE d.evaluacion_fisica_id=p_id ORDER BY t.nombre; END$$
DROP PROCEDURE IF EXISTS sp_evaluaciones_historial_cliente$$
CREATE PROCEDURE sp_evaluaciones_historial_cliente(IN p_cliente_id BIGINT UNSIGNED) BEGIN SELECT e.id,e.evaluada_at,e.metodo,CONCAT(p.nombre,' ',p.apellido) evaluador,COUNT(d.id) medidas FROM evaluaciones_fisicas e JOIN personal p ON p.id=e.evaluador_id LEFT JOIN detalles_evaluacion d ON d.evaluacion_fisica_id=e.id WHERE e.cliente_id=p_cliente_id AND e.deleted_at IS NULL GROUP BY e.id,p.nombre,p.apellido ORDER BY e.evaluada_at DESC; END$$
DROP PROCEDURE IF EXISTS sp_evaluaciones_comparar$$
CREATE PROCEDURE sp_evaluaciones_comparar(IN p_evaluacion_base BIGINT UNSIGNED,IN p_evaluacion_comparada BIGINT UNSIGNED)
BEGIN
 DECLARE v_cliente1 BIGINT UNSIGNED; DECLARE v_cliente2 BIGINT UNSIGNED;
 DECLARE v_fecha1 DATETIME; DECLARE v_fecha2 DATETIME;
 DECLARE v_anterior BIGINT UNSIGNED; DECLARE v_actual BIGINT UNSIGNED;
 SELECT cliente_id,evaluada_at INTO v_cliente1,v_fecha1 FROM evaluaciones_fisicas WHERE id=p_evaluacion_base AND deleted_at IS NULL;
 SELECT cliente_id,evaluada_at INTO v_cliente2,v_fecha2 FROM evaluaciones_fisicas WHERE id=p_evaluacion_comparada AND deleted_at IS NULL;
 IF v_cliente1 IS NULL OR v_cliente2 IS NULL OR v_cliente1<>v_cliente2 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Las evaluaciones deben existir y pertenecer al mismo cliente'; END IF;
 IF v_fecha1<v_fecha2 OR (v_fecha1=v_fecha2 AND p_evaluacion_base<p_evaluacion_comparada) THEN SET v_anterior=p_evaluacion_base; SET v_actual=p_evaluacion_comparada; ELSE SET v_anterior=p_evaluacion_comparada; SET v_actual=p_evaluacion_base; END IF;
 SELECT t.id tipo_medida_id,t.nombre,t.decimales,da.valor valor_anterior,dn.valor valor_actual,
   dn.valor-da.valor diferencia,
   CASE WHEN da.valor=0 THEN NULL ELSE ROUND(((dn.valor-da.valor)/ABS(da.valor))*100,2) END variacion_porcentual,
   da.unidad_snapshot unidad,ea.evaluada_at fecha_anterior,en.evaluada_at fecha_actual
 FROM detalles_evaluacion da
 JOIN detalles_evaluacion dn ON dn.tipo_medida_id=da.tipo_medida_id AND dn.evaluacion_fisica_id=v_actual
 JOIN tipos_medida t ON t.id=da.tipo_medida_id
 JOIN evaluaciones_fisicas ea ON ea.id=v_anterior
 JOIN evaluaciones_fisicas en ON en.id=v_actual
 WHERE da.evaluacion_fisica_id=v_anterior ORDER BY t.nombre;
END$$
DROP PROCEDURE IF EXISTS sp_evaluaciones_agregar_medida$$
CREATE PROCEDURE sp_evaluaciones_agregar_medida(IN p_evaluacion_id BIGINT UNSIGNED,IN p_tipo_id BIGINT UNSIGNED,IN p_valor DECIMAL(12,4),IN p_instrumento VARCHAR(100),IN p_observaciones TEXT) BEGIN DECLARE v_unidad VARCHAR(20);DECLARE v_min DECIMAL(12,4);DECLARE v_max DECIMAL(12,4);IF NOT EXISTS(SELECT 1 FROM evaluaciones_fisicas WHERE id=p_evaluacion_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La evaluación no existe';END IF;SELECT unidad,valor_minimo,valor_maximo INTO v_unidad,v_min,v_max FROM tipos_medida WHERE id=p_tipo_id AND activo=1;IF v_unidad IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El tipo de medida no está disponible';END IF;IF (v_min IS NOT NULL AND p_valor<v_min) OR (v_max IS NOT NULL AND p_valor>v_max) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El valor está fuera del rango permitido';END IF;INSERT INTO detalles_evaluacion(evaluacion_fisica_id,tipo_medida_id,valor,unidad_snapshot,instrumento,observaciones,created_at,updated_at) VALUES(p_evaluacion_id,p_tipo_id,p_valor,v_unidad,p_instrumento,p_observaciones,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE valor=VALUES(valor),unidad_snapshot=VALUES(unidad_snapshot),instrumento=VALUES(instrumento),observaciones=VALUES(observaciones),updated_at=CURRENT_TIMESTAMP;END$$
DROP PROCEDURE IF EXISTS sp_evaluaciones_clientes$$
CREATE PROCEDURE sp_evaluaciones_clientes() BEGIN SELECT DISTINCT c.id,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) nombre FROM clientes c JOIN membresias m ON m.cliente_id=c.id AND m.deleted_at IS NULL AND m.bloqueo_activa=1 JOIN estados_membresia em ON em.id=m.estado_membresia_id AND em.permite_acceso=1 WHERE c.deleted_at IS NULL AND CURRENT_DATE BETWEEN m.fecha_inicio AND m.fecha_fin AND EXISTS(SELECT 1 FROM cargos_cobro cc WHERE cc.membresia_id=m.id) AND NOT EXISTS(SELECT 1 FROM cargos_cobro cc WHERE cc.membresia_id=m.id AND COALESCE((SELECT SUM(ap.monto_aplicado) FROM aplicaciones_pago ap WHERE ap.cargo_cobro_id=cc.id),0)<cc.total) ORDER BY nombre LIMIT 500;END$$
DROP PROCEDURE IF EXISTS sp_evaluaciones_evaluadores$$
CREATE PROCEDURE sp_evaluaciones_evaluadores() BEGIN SELECT id,codigo_empleado,CONCAT(nombre,' ',apellido) nombre FROM personal WHERE deleted_at IS NULL ORDER BY apellido,nombre LIMIT 500;END$$

DROP PROCEDURE IF EXISTS sp_evaluaciones_crear_completa$$
CREATE PROCEDURE sp_evaluaciones_crear_completa(IN p_cliente_id BIGINT UNSIGNED,IN p_evaluador_id BIGINT UNSIGNED,IN p_fecha DATETIME,IN p_metodo VARCHAR(100),IN p_observaciones TEXT,IN p_usuario_id BIGINT UNSIGNED,IN p_medidas JSON)
BEGIN
 DECLARE v_id BIGINT UNSIGNED;
 DECLARE v_index INT DEFAULT 0;
 DECLARE v_len INT DEFAULT 0;
 DECLARE v_tipo_id BIGINT UNSIGNED;
 DECLARE v_valor DECIMAL(12,4);
 DECLARE v_instrumento VARCHAR(100);
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK;RESIGNAL;END;
 IF NOT EXISTS(SELECT 1 FROM clientes WHERE id=p_cliente_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente no existe';END IF;
 IF NOT EXISTS(SELECT 1 FROM personal WHERE id=p_evaluador_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El evaluador no está disponible';END IF;
 IF p_fecha>CURRENT_TIMESTAMP THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La fecha de evaluación no puede ser futura';END IF;
 IF p_metodo IS NULL OR TRIM(p_metodo)='' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El método de evaluación es obligatorio';END IF;
 IF p_medidas IS NULL OR JSON_LENGTH(p_medidas)=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Debe registrar al menos una medida';END IF;
 DROP TEMPORARY TABLE IF EXISTS tmp_evaluaciones_medidas;
 CREATE TEMPORARY TABLE tmp_evaluaciones_medidas (
   tipo_id BIGINT UNSIGNED NOT NULL,
   valor DECIMAL(12,4) NOT NULL,
   instrumento VARCHAR(100) NULL,
   pos INT UNSIGNED NOT NULL,
   PRIMARY KEY (pos)
 ) ENGINE=Memory;
 SET v_len = JSON_LENGTH(p_medidas);
 WHILE v_index < v_len DO
   SET v_tipo_id = CAST(JSON_EXTRACT(JSON_EXTRACT(p_medidas, CONCAT('$[', v_index, ']')), '$.tipo_medida_id') AS UNSIGNED);
   SET v_valor = CAST(JSON_EXTRACT(JSON_EXTRACT(p_medidas, CONCAT('$[', v_index, ']')), '$.valor') AS DECIMAL(12,4));
   SET v_instrumento = NULLIF(TRIM(CAST(JSON_EXTRACT(JSON_EXTRACT(p_medidas, CONCAT('$[', v_index, ']')), '$.instrumento') AS CHAR)), '');
   INSERT INTO tmp_evaluaciones_medidas (tipo_id, valor, instrumento, pos) VALUES (v_tipo_id, v_valor, v_instrumento, v_index + 1);
   SET v_index = v_index + 1;
 END WHILE;
 IF EXISTS(SELECT 1 FROM tmp_evaluaciones_medidas j LEFT JOIN tipos_medida t ON t.id=j.tipo_id AND t.activo=1 WHERE t.id IS NULL OR (t.valor_minimo IS NOT NULL AND j.valor<t.valor_minimo) OR (t.valor_maximo IS NOT NULL AND j.valor>t.valor_maximo)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Una o más medidas son inválidas o están fuera del rango permitido';END IF;
 START TRANSACTION;
 INSERT INTO evaluaciones_fisicas(cliente_id,evaluador_id,evaluada_at,metodo,observaciones,creada_por,created_at,updated_at) VALUES(p_cliente_id,p_evaluador_id,p_fecha,TRIM(p_metodo),p_observaciones,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SET v_id=LAST_INSERT_ID();
 INSERT INTO detalles_evaluacion(evaluacion_fisica_id,tipo_medida_id,valor,unidad_snapshot,instrumento,created_at,updated_at)
 SELECT v_id,j.tipo_id,j.valor,t.unidad,j.instrumento,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP FROM tmp_evaluaciones_medidas j JOIN tipos_medida t ON t.id=j.tipo_id AND t.activo=1;
 COMMIT;
 SELECT * FROM evaluaciones_fisicas WHERE id=v_id;
END$$
DELIMITER ;


DROP PROCEDURE IF EXISTS sp_tipos_medida_listar;
CREATE PROCEDURE sp_tipos_medida_listar(IN p_buscar VARCHAR(100), IN p_limite INT, IN p_offset INT)
BEGIN
    SELECT id, codigo, nombre, unidad, valor_minimo, valor_maximo, decimales, activo
    FROM tipos_medida
    WHERE p_buscar = '' OR codigo LIKE CONCAT('%', p_buscar, '%') OR nombre LIKE CONCAT('%', p_buscar, '%') OR unidad LIKE CONCAT('%', p_buscar, '%')
    ORDER BY id DESC
    LIMIT p_limite OFFSET p_offset;
END;

DROP PROCEDURE IF EXISTS sp_tipos_medida_crear;
CREATE PROCEDURE sp_tipos_medida_crear(
    IN p_codigo VARCHAR(30), 
    IN p_nombre VARCHAR(50), 
    IN p_unidad VARCHAR(20), 
    IN p_valor_minimo DECIMAL(10,4), 
    IN p_valor_maximo DECIMAL(10,4), 
    IN p_decimales TINYINT, 
    IN p_activo TINYINT
)
BEGIN
    INSERT INTO tipos_medida (codigo, nombre, unidad, valor_minimo, valor_maximo, decimales, activo, created_at, updated_at) 
    VALUES (p_codigo, p_nombre, p_unidad, p_valor_minimo, p_valor_maximo, p_decimales, p_activo, NOW(), NOW());
END;

DROP PROCEDURE IF EXISTS sp_tipos_medida_actualizar;
CREATE PROCEDURE sp_tipos_medida_actualizar(
    IN p_id BIGINT, 
    IN p_codigo VARCHAR(30), 
    IN p_nombre VARCHAR(50), 
    IN p_unidad VARCHAR(20), 
    IN p_valor_minimo DECIMAL(10,4), 
    IN p_valor_maximo DECIMAL(10,4), 
    IN p_decimales TINYINT, 
    IN p_activo TINYINT
)
BEGIN
    UPDATE tipos_medida 
    SET codigo = p_codigo, 
        nombre = p_nombre, 
        unidad = p_unidad, 
        valor_minimo = p_valor_minimo, 
        valor_maximo = p_valor_maximo, 
        decimales = p_decimales, 
        activo = p_activo, 
        updated_at = NOW() 
    WHERE id = p_id;
END;

DROP PROCEDURE IF EXISTS sp_tipos_medida_eliminar;
CREATE PROCEDURE sp_tipos_medida_eliminar(IN p_id BIGINT)
BEGIN
    DELETE FROM tipos_medida WHERE id = p_id;
END;
