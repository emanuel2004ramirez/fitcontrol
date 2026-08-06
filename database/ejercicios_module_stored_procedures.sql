-- FitControl: procedimientos del catálogo de ejercicios.
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_ejercicios_filtrar$$
CREATE PROCEDURE sp_ejercicios_filtrar(IN p_texto VARCHAR(150),IN p_estado_id BIGINT UNSIGNED,IN p_grupo_id BIGINT UNSIGNED,IN p_patron VARCHAR(80),IN p_equipamiento VARCHAR(120),IN p_limite INT UNSIGNED,IN p_offset INT UNSIGNED)
BEGIN IF p_limite IS NULL OR p_limite<1 OR p_limite>100 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El límite debe estar entre 1 y 100'; END IF;
 SELECT e.id,e.codigo,e.nombre,e.estado_ejercicio_id,ee.codigo estado_codigo,ee.nombre estado,e.patron_movimiento,e.equipamiento,e.descripcion,e.video_url,GROUP_CONCAT(DISTINCT gm.nombre ORDER BY egm.es_principal DESC,gm.nombre SEPARATOR ', ') grupos_musculares
 FROM ejercicios e JOIN estados_ejercicio ee ON ee.id=e.estado_ejercicio_id LEFT JOIN ejercicio_grupo_muscular egm ON egm.ejercicio_id=e.id LEFT JOIN grupos_musculares gm ON gm.id=egm.grupo_muscular_id
 WHERE e.deleted_at IS NULL AND (p_texto IS NULL OR p_texto='' OR e.codigo LIKE CONCAT('%',p_texto,'%') OR e.nombre LIKE CONCAT('%',p_texto,'%') OR e.descripcion LIKE CONCAT('%',p_texto,'%')) AND (p_estado_id IS NULL OR e.estado_ejercicio_id=p_estado_id) AND (p_patron IS NULL OR p_patron='' OR e.patron_movimiento=p_patron) AND (p_equipamiento IS NULL OR p_equipamiento='' OR e.equipamiento LIKE CONCAT('%',p_equipamiento,'%')) AND (p_grupo_id IS NULL OR EXISTS(SELECT 1 FROM ejercicio_grupo_muscular x WHERE x.ejercicio_id=e.id AND x.grupo_muscular_id=p_grupo_id))
 GROUP BY e.id,ee.codigo,ee.nombre ORDER BY e.nombre,e.id LIMIT p_limite OFFSET p_offset; END$$
DROP PROCEDURE IF EXISTS sp_ejercicios_contar$$
CREATE PROCEDURE sp_ejercicios_contar(IN p_texto VARCHAR(150),IN p_estado_id BIGINT UNSIGNED,IN p_grupo_id BIGINT UNSIGNED,IN p_patron VARCHAR(80),IN p_equipamiento VARCHAR(120))
BEGIN SELECT COUNT(*) total FROM ejercicios e WHERE e.deleted_at IS NULL AND (p_texto IS NULL OR p_texto='' OR e.codigo LIKE CONCAT('%',p_texto,'%') OR e.nombre LIKE CONCAT('%',p_texto,'%') OR e.descripcion LIKE CONCAT('%',p_texto,'%')) AND (p_estado_id IS NULL OR e.estado_ejercicio_id=p_estado_id) AND (p_patron IS NULL OR p_patron='' OR e.patron_movimiento=p_patron) AND (p_equipamiento IS NULL OR p_equipamiento='' OR e.equipamiento LIKE CONCAT('%',p_equipamiento,'%')) AND (p_grupo_id IS NULL OR EXISTS(SELECT 1 FROM ejercicio_grupo_muscular x WHERE x.ejercicio_id=e.id AND x.grupo_muscular_id=p_grupo_id)); END$$
DROP PROCEDURE IF EXISTS sp_ejercicios_obtener$$
CREATE PROCEDURE sp_ejercicios_obtener(IN p_id BIGINT UNSIGNED) BEGIN IF NOT EXISTS(SELECT 1 FROM ejercicios WHERE id=p_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El ejercicio no existe'; END IF; SELECT e.*,ee.codigo estado_codigo,ee.nombre estado FROM ejercicios e JOIN estados_ejercicio ee ON ee.id=e.estado_ejercicio_id WHERE e.id=p_id AND e.deleted_at IS NULL; END$$
DROP PROCEDURE IF EXISTS sp_ejercicios_grupos$$
CREATE PROCEDURE sp_ejercicios_grupos(IN p_id BIGINT UNSIGNED) BEGIN SELECT gm.id,gm.codigo,gm.nombre,egm.es_principal FROM ejercicio_grupo_muscular egm JOIN grupos_musculares gm ON gm.id=egm.grupo_muscular_id WHERE egm.ejercicio_id=p_id ORDER BY egm.es_principal DESC,gm.nombre; END$$
DROP PROCEDURE IF EXISTS sp_ejercicios_asignar_grupo$$
CREATE PROCEDURE sp_ejercicios_asignar_grupo(IN p_ejercicio_id BIGINT UNSIGNED,IN p_grupo_id BIGINT UNSIGNED,IN p_principal BOOLEAN)
BEGIN DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END; IF NOT EXISTS(SELECT 1 FROM ejercicios WHERE id=p_ejercicio_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El ejercicio no existe'; END IF; IF NOT EXISTS(SELECT 1 FROM grupos_musculares WHERE id=p_grupo_id AND activo=1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El grupo muscular no está disponible'; END IF; START TRANSACTION; IF COALESCE(p_principal,0)=1 THEN UPDATE ejercicio_grupo_muscular SET es_principal=0,updated_at=CURRENT_TIMESTAMP WHERE ejercicio_id=p_ejercicio_id; END IF; INSERT INTO ejercicio_grupo_muscular(ejercicio_id,grupo_muscular_id,es_principal,created_at,updated_at) VALUES(p_ejercicio_id,p_grupo_id,COALESCE(p_principal,0),CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE es_principal=VALUES(es_principal),updated_at=CURRENT_TIMESTAMP; COMMIT; END$$
DROP PROCEDURE IF EXISTS sp_ejercicios_retirar_grupo$$
CREATE PROCEDURE sp_ejercicios_retirar_grupo(IN p_ejercicio_id BIGINT UNSIGNED,IN p_grupo_id BIGINT UNSIGNED) BEGIN DELETE FROM ejercicio_grupo_muscular WHERE ejercicio_id=p_ejercicio_id AND grupo_muscular_id=p_grupo_id; IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El grupo no está asignado al ejercicio'; END IF; END$$
DROP PROCEDURE IF EXISTS sp_ejercicios_cambiar_estado$$
CREATE PROCEDURE sp_ejercicios_cambiar_estado(IN p_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED) BEGIN IF NOT EXISTS(SELECT 1 FROM estados_ejercicio WHERE id=p_estado_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El estado no existe'; END IF; UPDATE ejercicios SET estado_ejercicio_id=p_estado_id,updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND deleted_at IS NULL; IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El ejercicio no existe o ya tiene ese estado'; END IF; END$$
DROP PROCEDURE IF EXISTS sp_ejercicios_patrones$$
CREATE PROCEDURE sp_ejercicios_patrones() BEGIN SELECT DISTINCT patron_movimiento nombre FROM ejercicios WHERE deleted_at IS NULL AND patron_movimiento IS NOT NULL ORDER BY patron_movimiento; END$$
DROP PROCEDURE IF EXISTS sp_ejercicios_crear$$
CREATE PROCEDURE sp_ejercicios_crear(IN p_codigo VARCHAR(40),IN p_estado_id BIGINT UNSIGNED,IN p_nombre VARCHAR(120),IN p_patron VARCHAR(80),IN p_equipamiento VARCHAR(120),IN p_descripcion TEXT,IN p_instrucciones TEXT,IN p_video VARCHAR(500))
BEGIN
 DECLARE v_id BIGINT UNSIGNED;
 IF p_nombre IS NULL OR TRIM(p_nombre)='' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El nombre del ejercicio es obligatorio'; END IF;
 IF NOT EXISTS(SELECT 1 FROM estados_ejercicio WHERE id=p_estado_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El estado seleccionado no existe'; END IF;
 INSERT INTO ejercicios(codigo,estado_ejercicio_id,nombre,patron_movimiento,equipamiento,descripcion,instrucciones,video_url,created_at,updated_at) VALUES(CONCAT('TMP-',UUID_SHORT()),p_estado_id,TRIM(p_nombre),NULLIF(TRIM(p_patron),''),NULLIF(TRIM(p_equipamiento),''),NULLIF(TRIM(p_descripcion),''),NULLIF(TRIM(p_instrucciones),''),NULLIF(TRIM(p_video),''),CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SET v_id=LAST_INSERT_ID();
 UPDATE ejercicios SET codigo=CONCAT('EJ-',LPAD(v_id,6,'0')) WHERE id=v_id;
 SELECT * FROM ejercicios WHERE id=v_id;
END$$
DELIMITER ;


DROP PROCEDURE IF EXISTS sp_grupos_musculares_listar;
CREATE PROCEDURE sp_grupos_musculares_listar(IN p_buscar VARCHAR(100), IN p_limite INT, IN p_offset INT)
BEGIN
    SELECT id, codigo, nombre, activo
    FROM grupos_musculares
    WHERE p_buscar = '' OR codigo LIKE CONCAT('%', p_buscar, '%') OR nombre LIKE CONCAT('%', p_buscar, '%')
    ORDER BY id DESC
    LIMIT p_limite OFFSET p_offset;
END;

DROP PROCEDURE IF EXISTS sp_grupos_musculares_crear;
CREATE PROCEDURE sp_grupos_musculares_crear(IN p_codigo VARCHAR(30), IN p_nombre VARCHAR(50), IN p_activo TINYINT)
BEGIN
    INSERT INTO grupos_musculares (codigo, nombre, activo, created_at, updated_at) 
    VALUES (p_codigo, p_nombre, p_activo, NOW(), NOW());
END;

DROP PROCEDURE IF EXISTS sp_grupos_musculares_actualizar;
CREATE PROCEDURE sp_grupos_musculares_actualizar(IN p_id BIGINT, IN p_codigo VARCHAR(30), IN p_nombre VARCHAR(50), IN p_activo TINYINT)
BEGIN
    UPDATE grupos_musculares 
    SET codigo = p_codigo, 
        nombre = p_nombre, 
        activo = p_activo, 
        updated_at = NOW() 
    WHERE id = p_id;
END;

DROP PROCEDURE IF EXISTS sp_grupos_musculares_eliminar;
CREATE PROCEDURE sp_grupos_musculares_eliminar(IN p_id BIGINT)
BEGIN
    DELETE FROM grupos_musculares WHERE id = p_id;
END;
