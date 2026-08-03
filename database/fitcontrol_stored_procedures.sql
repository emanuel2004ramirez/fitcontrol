-- FitControl - Stored Procedures
-- MySQL 8.x
-- Generado a partir de las migraciones 2026_08_02.
-- Ejecutar sobre una base con el esquema ya migrado.

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_sexos_listar$$
CREATE PROCEDURE sp_sexos_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `sexos`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_sexos_obtener$$
CREATE PROCEDURE sp_sexos_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `sexos` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `sexos` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_sexos_crear$$
CREATE PROCEDURE sp_sexos_crear(
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `sexos` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `sexos` (`codigo`, `nombre`, `activo`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_activo, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `sexos` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_sexos_actualizar$$
CREATE PROCEDURE sp_sexos_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `sexos` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `sexos` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `sexos` SET `codigo` = p_codigo, `nombre` = p_nombre, `activo` = p_activo, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `sexos` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_sexos_eliminar$$
CREATE PROCEDURE sp_sexos_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `sexos` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    UPDATE `sexos` SET activo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_cliente_listar$$
CREATE PROCEDURE sp_estados_cliente_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `estados_cliente`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_estados_cliente_obtener$$
CREATE PROCEDURE sp_estados_cliente_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `estados_cliente` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `estados_cliente` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_cliente_crear$$
CREATE PROCEDURE sp_estados_cliente_crear(
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_activo BOOLEAN,
    IN p_es_terminal BOOLEAN,
    IN p_orden SMALLINT UNSIGNED
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `estados_cliente` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `estados_cliente` (`codigo`, `nombre`, `activo`, `es_terminal`, `orden`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_activo, p_es_terminal, p_orden, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `estados_cliente` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_estados_cliente_actualizar$$
CREATE PROCEDURE sp_estados_cliente_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_activo BOOLEAN,
    IN p_es_terminal BOOLEAN,
    IN p_orden SMALLINT UNSIGNED
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `estados_cliente` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `estados_cliente` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `estados_cliente` SET `codigo` = p_codigo, `nombre` = p_nombre, `activo` = p_activo, `es_terminal` = p_es_terminal, `orden` = p_orden, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `estados_cliente` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_cliente_eliminar$$
CREATE PROCEDURE sp_estados_cliente_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `estados_cliente` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    UPDATE `estados_cliente` SET activo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_personal_listar$$
CREATE PROCEDURE sp_estados_personal_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `estados_personal`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_estados_personal_obtener$$
CREATE PROCEDURE sp_estados_personal_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `estados_personal` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `estados_personal` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_personal_crear$$
CREATE PROCEDURE sp_estados_personal_crear(
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_activo BOOLEAN,
    IN p_es_terminal BOOLEAN,
    IN p_orden SMALLINT UNSIGNED
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `estados_personal` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `estados_personal` (`codigo`, `nombre`, `activo`, `es_terminal`, `orden`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_activo, p_es_terminal, p_orden, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `estados_personal` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_estados_personal_actualizar$$
CREATE PROCEDURE sp_estados_personal_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_activo BOOLEAN,
    IN p_es_terminal BOOLEAN,
    IN p_orden SMALLINT UNSIGNED
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `estados_personal` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `estados_personal` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `estados_personal` SET `codigo` = p_codigo, `nombre` = p_nombre, `activo` = p_activo, `es_terminal` = p_es_terminal, `orden` = p_orden, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `estados_personal` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_personal_eliminar$$
CREATE PROCEDURE sp_estados_personal_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `estados_personal` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    UPDATE `estados_personal` SET activo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_cargos_listar$$
CREATE PROCEDURE sp_cargos_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `cargos`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_cargos_obtener$$
CREATE PROCEDURE sp_cargos_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `cargos` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `cargos` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_cargos_crear$$
CREATE PROCEDURE sp_cargos_crear(
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `cargos` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `cargos` (`codigo`, `nombre`, `descripcion`, `activo`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_descripcion, p_activo, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `cargos` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_cargos_actualizar$$
CREATE PROCEDURE sp_cargos_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `cargos` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `cargos` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `cargos` SET `codigo` = p_codigo, `nombre` = p_nombre, `descripcion` = p_descripcion, `activo` = p_activo, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `cargos` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_cargos_eliminar$$
CREATE PROCEDURE sp_cargos_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `cargos` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    UPDATE `cargos` SET activo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_roles_listar$$
CREATE PROCEDURE sp_roles_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `roles`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_roles_obtener$$
CREATE PROCEDURE sp_roles_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `roles` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `roles` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_roles_crear$$
CREATE PROCEDURE sp_roles_crear(
    IN p_codigo VARCHAR(50),
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `roles` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `roles` (`codigo`, `nombre`, `descripcion`, `activo`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_descripcion, p_activo, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `roles` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_roles_actualizar$$
CREATE PROCEDURE sp_roles_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(50),
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `roles` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `roles` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `roles` SET `codigo` = p_codigo, `nombre` = p_nombre, `descripcion` = p_descripcion, `activo` = p_activo, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `roles` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_roles_eliminar$$
CREATE PROCEDURE sp_roles_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `roles` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    UPDATE `roles` SET activo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_permisos_listar$$
CREATE PROCEDURE sp_permisos_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `permisos`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_permisos_obtener$$
CREATE PROCEDURE sp_permisos_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `permisos` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `permisos` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_permisos_crear$$
CREATE PROCEDURE sp_permisos_crear(
    IN p_codigo VARCHAR(100),
    IN p_nombre VARCHAR(120),
    IN p_modulo VARCHAR(60),
    IN p_descripcion TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `permisos` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `permisos` (`codigo`, `nombre`, `modulo`, `descripcion`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_modulo, p_descripcion, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `permisos` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_permisos_actualizar$$
CREATE PROCEDURE sp_permisos_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(100),
    IN p_nombre VARCHAR(120),
    IN p_modulo VARCHAR(60),
    IN p_descripcion TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `permisos` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `permisos` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `permisos` SET `codigo` = p_codigo, `nombre` = p_nombre, `modulo` = p_modulo, `descripcion` = p_descripcion, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `permisos` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_permisos_eliminar$$
CREATE PROCEDURE sp_permisos_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    DECLARE EXIT HANDLER FOR 1451 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No puede eliminarse porque tiene registros relacionados';
    IF NOT EXISTS (SELECT 1 FROM `permisos` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    DELETE FROM `permisos` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_membresia_listar$$
CREATE PROCEDURE sp_estados_membresia_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `estados_membresia`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_estados_membresia_obtener$$
CREATE PROCEDURE sp_estados_membresia_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `estados_membresia` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `estados_membresia` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_membresia_crear$$
CREATE PROCEDURE sp_estados_membresia_crear(
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_permite_acceso BOOLEAN,
    IN p_es_terminal BOOLEAN,
    IN p_orden SMALLINT UNSIGNED
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `estados_membresia` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `estados_membresia` (`codigo`, `nombre`, `permite_acceso`, `es_terminal`, `orden`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_permite_acceso, p_es_terminal, p_orden, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `estados_membresia` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_estados_membresia_actualizar$$
CREATE PROCEDURE sp_estados_membresia_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_permite_acceso BOOLEAN,
    IN p_es_terminal BOOLEAN,
    IN p_orden SMALLINT UNSIGNED
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `estados_membresia` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `estados_membresia` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `estados_membresia` SET `codigo` = p_codigo, `nombre` = p_nombre, `permite_acceso` = p_permite_acceso, `es_terminal` = p_es_terminal, `orden` = p_orden, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `estados_membresia` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_membresia_eliminar$$
CREATE PROCEDURE sp_estados_membresia_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    DECLARE EXIT HANDLER FOR 1451 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No puede eliminarse porque tiene registros relacionados';
    IF NOT EXISTS (SELECT 1 FROM `estados_membresia` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    DELETE FROM `estados_membresia` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_tipos_membresia_listar$$
CREATE PROCEDURE sp_tipos_membresia_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `tipos_membresia`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_tipos_membresia_obtener$$
CREATE PROCEDURE sp_tipos_membresia_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `tipos_membresia` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `tipos_membresia` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_tipos_membresia_crear$$
CREATE PROCEDURE sp_tipos_membresia_crear(
    IN p_codigo VARCHAR(40),
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_duracion_dias SMALLINT UNSIGNED,
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `tipos_membresia` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `tipos_membresia` (`codigo`, `nombre`, `descripcion`, `duracion_dias`, `activo`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_descripcion, p_duracion_dias, p_activo, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `tipos_membresia` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_tipos_membresia_actualizar$$
CREATE PROCEDURE sp_tipos_membresia_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(40),
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_duracion_dias SMALLINT UNSIGNED,
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `tipos_membresia` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `tipos_membresia` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `tipos_membresia` SET `codigo` = p_codigo, `nombre` = p_nombre, `descripcion` = p_descripcion, `duracion_dias` = p_duracion_dias, `activo` = p_activo, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `tipos_membresia` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_tipos_membresia_eliminar$$
CREATE PROCEDURE sp_tipos_membresia_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `tipos_membresia` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    UPDATE `tipos_membresia` SET activo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_pago_listar$$
CREATE PROCEDURE sp_estados_pago_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `estados_pago`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_estados_pago_obtener$$
CREATE PROCEDURE sp_estados_pago_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `estados_pago` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `estados_pago` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_pago_crear$$
CREATE PROCEDURE sp_estados_pago_crear(
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_es_terminal BOOLEAN,
    IN p_orden SMALLINT UNSIGNED
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `estados_pago` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `estados_pago` (`codigo`, `nombre`, `es_terminal`, `orden`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_es_terminal, p_orden, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `estados_pago` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_estados_pago_actualizar$$
CREATE PROCEDURE sp_estados_pago_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_es_terminal BOOLEAN,
    IN p_orden SMALLINT UNSIGNED
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `estados_pago` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `estados_pago` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `estados_pago` SET `codigo` = p_codigo, `nombre` = p_nombre, `es_terminal` = p_es_terminal, `orden` = p_orden, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `estados_pago` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_pago_eliminar$$
CREATE PROCEDURE sp_estados_pago_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    DECLARE EXIT HANDLER FOR 1451 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No puede eliminarse porque tiene registros relacionados';
    IF NOT EXISTS (SELECT 1 FROM `estados_pago` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    DELETE FROM `estados_pago` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_metodos_pago_listar$$
CREATE PROCEDURE sp_metodos_pago_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `metodos_pago`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_metodos_pago_obtener$$
CREATE PROCEDURE sp_metodos_pago_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `metodos_pago` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `metodos_pago` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_metodos_pago_crear$$
CREATE PROCEDURE sp_metodos_pago_crear(
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(100),
    IN p_requiere_referencia BOOLEAN,
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `metodos_pago` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `metodos_pago` (`codigo`, `nombre`, `requiere_referencia`, `activo`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_requiere_referencia, p_activo, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `metodos_pago` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_metodos_pago_actualizar$$
CREATE PROCEDURE sp_metodos_pago_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(100),
    IN p_requiere_referencia BOOLEAN,
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `metodos_pago` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `metodos_pago` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `metodos_pago` SET `codigo` = p_codigo, `nombre` = p_nombre, `requiere_referencia` = p_requiere_referencia, `activo` = p_activo, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `metodos_pago` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_metodos_pago_eliminar$$
CREATE PROCEDURE sp_metodos_pago_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `metodos_pago` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    UPDATE `metodos_pago` SET activo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_objetivos_listar$$
CREATE PROCEDURE sp_objetivos_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `objetivos`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_objetivos_obtener$$
CREATE PROCEDURE sp_objetivos_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `objetivos` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `objetivos` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_objetivos_crear$$
CREATE PROCEDURE sp_objetivos_crear(
    IN p_codigo VARCHAR(40),
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `objetivos` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `objetivos` (`codigo`, `nombre`, `descripcion`, `activo`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_descripcion, p_activo, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `objetivos` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_objetivos_actualizar$$
CREATE PROCEDURE sp_objetivos_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(40),
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `objetivos` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `objetivos` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `objetivos` SET `codigo` = p_codigo, `nombre` = p_nombre, `descripcion` = p_descripcion, `activo` = p_activo, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `objetivos` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_objetivos_eliminar$$
CREATE PROCEDURE sp_objetivos_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `objetivos` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    UPDATE `objetivos` SET activo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_tipos_medida_listar$$
CREATE PROCEDURE sp_tipos_medida_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `tipos_medida`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_tipos_medida_obtener$$
CREATE PROCEDURE sp_tipos_medida_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `tipos_medida` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `tipos_medida` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_tipos_medida_crear$$
CREATE PROCEDURE sp_tipos_medida_crear(
    IN p_codigo VARCHAR(40),
    IN p_nombre VARCHAR(100),
    IN p_unidad VARCHAR(20),
    IN p_valor_minimo DECIMAL(12,4),
    IN p_valor_maximo DECIMAL(12,4),
    IN p_decimales TINYINT UNSIGNED,
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `tipos_medida` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `tipos_medida` (`codigo`, `nombre`, `unidad`, `valor_minimo`, `valor_maximo`, `decimales`, `activo`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_unidad, p_valor_minimo, p_valor_maximo, p_decimales, p_activo, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `tipos_medida` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_tipos_medida_actualizar$$
CREATE PROCEDURE sp_tipos_medida_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(40),
    IN p_nombre VARCHAR(100),
    IN p_unidad VARCHAR(20),
    IN p_valor_minimo DECIMAL(12,4),
    IN p_valor_maximo DECIMAL(12,4),
    IN p_decimales TINYINT UNSIGNED,
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `tipos_medida` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `tipos_medida` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `tipos_medida` SET `codigo` = p_codigo, `nombre` = p_nombre, `unidad` = p_unidad, `valor_minimo` = p_valor_minimo, `valor_maximo` = p_valor_maximo, `decimales` = p_decimales, `activo` = p_activo, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `tipos_medida` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_tipos_medida_eliminar$$
CREATE PROCEDURE sp_tipos_medida_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `tipos_medida` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    UPDATE `tipos_medida` SET activo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_grupos_musculares_listar$$
CREATE PROCEDURE sp_grupos_musculares_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `grupos_musculares`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_grupos_musculares_obtener$$
CREATE PROCEDURE sp_grupos_musculares_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `grupos_musculares` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `grupos_musculares` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_grupos_musculares_crear$$
CREATE PROCEDURE sp_grupos_musculares_crear(
    IN p_codigo VARCHAR(40),
    IN p_nombre VARCHAR(100),
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `grupos_musculares` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `grupos_musculares` (`codigo`, `nombre`, `activo`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_activo, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `grupos_musculares` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_grupos_musculares_actualizar$$
CREATE PROCEDURE sp_grupos_musculares_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(40),
    IN p_nombre VARCHAR(100),
    IN p_activo BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `grupos_musculares` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `grupos_musculares` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `grupos_musculares` SET `codigo` = p_codigo, `nombre` = p_nombre, `activo` = p_activo, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `grupos_musculares` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_grupos_musculares_eliminar$$
CREATE PROCEDURE sp_grupos_musculares_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `grupos_musculares` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    UPDATE `grupos_musculares` SET activo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_ejercicio_listar$$
CREATE PROCEDURE sp_estados_ejercicio_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `estados_ejercicio`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_estados_ejercicio_obtener$$
CREATE PROCEDURE sp_estados_ejercicio_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `estados_ejercicio` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `estados_ejercicio` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_ejercicio_crear$$
CREATE PROCEDURE sp_estados_ejercicio_crear(
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `estados_ejercicio` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `estados_ejercicio` (`codigo`, `nombre`, created_at, updated_at) VALUES (p_codigo, p_nombre, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `estados_ejercicio` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_estados_ejercicio_actualizar$$
CREATE PROCEDURE sp_estados_ejercicio_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `estados_ejercicio` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `estados_ejercicio` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `estados_ejercicio` SET `codigo` = p_codigo, `nombre` = p_nombre, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `estados_ejercicio` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_ejercicio_eliminar$$
CREATE PROCEDURE sp_estados_ejercicio_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    DECLARE EXIT HANDLER FOR 1451 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No puede eliminarse porque tiene registros relacionados';
    IF NOT EXISTS (SELECT 1 FROM `estados_ejercicio` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    DELETE FROM `estados_ejercicio` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_rutina_listar$$
CREATE PROCEDURE sp_estados_rutina_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `estados_rutina`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_estados_rutina_obtener$$
CREATE PROCEDURE sp_estados_rutina_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `estados_rutina` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `estados_rutina` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_rutina_crear$$
CREATE PROCEDURE sp_estados_rutina_crear(
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_es_terminal BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `estados_rutina` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `estados_rutina` (`codigo`, `nombre`, `es_terminal`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_es_terminal, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `estados_rutina` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_estados_rutina_actualizar$$
CREATE PROCEDURE sp_estados_rutina_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_es_terminal BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `estados_rutina` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `estados_rutina` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `estados_rutina` SET `codigo` = p_codigo, `nombre` = p_nombre, `es_terminal` = p_es_terminal, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `estados_rutina` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_rutina_eliminar$$
CREATE PROCEDURE sp_estados_rutina_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    DECLARE EXIT HANDLER FOR 1451 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No puede eliminarse porque tiene registros relacionados';
    IF NOT EXISTS (SELECT 1 FROM `estados_rutina` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    DELETE FROM `estados_rutina` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_cargo_cobro_listar$$
CREATE PROCEDURE sp_estados_cargo_cobro_listar(IN p_busqueda VARCHAR(150), IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `estados_cargo_cobro`
    WHERE p_busqueda IS NULL OR p_busqueda = '' OR codigo LIKE CONCAT('%', p_busqueda, '%') OR nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY nombre LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_estados_cargo_cobro_obtener$$
CREATE PROCEDURE sp_estados_cargo_cobro_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `estados_cargo_cobro` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `estados_cargo_cobro` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_cargo_cobro_crear$$
CREATE PROCEDURE sp_estados_cargo_cobro_crear(
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_es_terminal BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' OR p_nombre IS NULL OR TRIM(p_nombre) = '' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código y nombre son obligatorios'; END IF;
    IF EXISTS (SELECT 1 FROM `estados_cargo_cobro` WHERE codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    INSERT INTO `estados_cargo_cobro` (`codigo`, `nombre`, `es_terminal`, created_at, updated_at) VALUES (p_codigo, p_nombre, p_es_terminal, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    SELECT * FROM `estados_cargo_cobro` WHERE id = LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_estados_cargo_cobro_actualizar$$
CREATE PROCEDURE sp_estados_cargo_cobro_actualizar(
    IN p_id BIGINT UNSIGNED,
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_es_terminal BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM `estados_cargo_cobro` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    IF EXISTS (SELECT 1 FROM `estados_cargo_cobro` WHERE (codigo = TRIM(p_codigo) OR nombre = TRIM(p_nombre)) AND id <> p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Código o nombre duplicado'; END IF;
    START TRANSACTION;
    UPDATE `estados_cargo_cobro` SET `codigo` = p_codigo, `nombre` = p_nombre, `es_terminal` = p_es_terminal, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    COMMIT;
    SELECT * FROM `estados_cargo_cobro` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_estados_cargo_cobro_eliminar$$
CREATE PROCEDURE sp_estados_cargo_cobro_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    DECLARE EXIT HANDLER FOR 1451 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No puede eliminarse porque tiene registros relacionados';
    IF NOT EXISTS (SELECT 1 FROM `estados_cargo_cobro` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    DELETE FROM `estados_cargo_cobro` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_precios_membresia_listar$$
CREATE PROCEDURE sp_precios_membresia_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `precios_membresia` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_precios_membresia_obtener$$
CREATE PROCEDURE sp_precios_membresia_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `precios_membresia` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `precios_membresia` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_clientes_listar$$
CREATE PROCEDURE sp_clientes_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `clientes` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_clientes_obtener$$
CREATE PROCEDURE sp_clientes_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `clientes` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `clientes` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_personal_listar$$
CREATE PROCEDURE sp_personal_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `personal` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_personal_obtener$$
CREATE PROCEDURE sp_personal_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `personal` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `personal` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_users_listar$$
CREATE PROCEDURE sp_users_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT id,personal_id,name,username,email,email_verified_at,activo,debe_cambiar_password,intentos_fallidos,bloqueado_hasta,password_changed_at,ultimo_acceso_at,created_at,updated_at,deleted_at FROM users ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_users_obtener$$
CREATE PROCEDURE sp_users_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `users` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT id,personal_id,name,username,email,email_verified_at,activo,debe_cambiar_password,intentos_fallidos,bloqueado_hasta,password_changed_at,ultimo_acceso_at,created_at,updated_at,deleted_at FROM users WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_contactos_emergencia_listar$$
CREATE PROCEDURE sp_contactos_emergencia_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `contactos_emergencia` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_contactos_emergencia_obtener$$
CREATE PROCEDURE sp_contactos_emergencia_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `contactos_emergencia` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `contactos_emergencia` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_consentimientos_cliente_listar$$
CREATE PROCEDURE sp_consentimientos_cliente_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `consentimientos_cliente` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_consentimientos_cliente_obtener$$
CREATE PROCEDURE sp_consentimientos_cliente_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `consentimientos_cliente` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `consentimientos_cliente` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_historial_estados_cliente_listar$$
CREATE PROCEDURE sp_historial_estados_cliente_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `historial_estados_cliente` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_historial_estados_cliente_obtener$$
CREATE PROCEDURE sp_historial_estados_cliente_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `historial_estados_cliente` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `historial_estados_cliente` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_historial_cargos_personal_listar$$
CREATE PROCEDURE sp_historial_cargos_personal_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `historial_cargos_personal` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_historial_cargos_personal_obtener$$
CREATE PROCEDURE sp_historial_cargos_personal_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `historial_cargos_personal` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `historial_cargos_personal` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_horarios_personal_listar$$
CREATE PROCEDURE sp_horarios_personal_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `horarios_personal` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_horarios_personal_obtener$$
CREATE PROCEDURE sp_horarios_personal_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `horarios_personal` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `horarios_personal` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_listar$$
CREATE PROCEDURE sp_membresias_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `membresias` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_obtener$$
CREATE PROCEDURE sp_membresias_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `membresias` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `membresias` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_historial_estados_membresia_listar$$
CREATE PROCEDURE sp_historial_estados_membresia_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `historial_estados_membresia` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_historial_estados_membresia_obtener$$
CREATE PROCEDURE sp_historial_estados_membresia_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `historial_estados_membresia` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `historial_estados_membresia` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_suspensiones_membresia_listar$$
CREATE PROCEDURE sp_suspensiones_membresia_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `suspensiones_membresia` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_suspensiones_membresia_obtener$$
CREATE PROCEDURE sp_suspensiones_membresia_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `suspensiones_membresia` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `suspensiones_membresia` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_cargos_cobro_listar$$
CREATE PROCEDURE sp_cargos_cobro_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `cargos_cobro` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_cargos_cobro_obtener$$
CREATE PROCEDURE sp_cargos_cobro_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `cargos_cobro` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `cargos_cobro` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_pagos_listar$$
CREATE PROCEDURE sp_pagos_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `pagos` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_pagos_obtener$$
CREATE PROCEDURE sp_pagos_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `pagos` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `pagos` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_aplicaciones_pago_listar$$
CREATE PROCEDURE sp_aplicaciones_pago_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `aplicaciones_pago` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_aplicaciones_pago_obtener$$
CREATE PROCEDURE sp_aplicaciones_pago_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `aplicaciones_pago` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `aplicaciones_pago` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_reembolsos_listar$$
CREATE PROCEDURE sp_reembolsos_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `reembolsos` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_reembolsos_obtener$$
CREATE PROCEDURE sp_reembolsos_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `reembolsos` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `reembolsos` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_historial_estados_pago_listar$$
CREATE PROCEDURE sp_historial_estados_pago_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `historial_estados_pago` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_historial_estados_pago_obtener$$
CREATE PROCEDURE sp_historial_estados_pago_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `historial_estados_pago` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `historial_estados_pago` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_asistencias_listar$$
CREATE PROCEDURE sp_asistencias_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `asistencias` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_asistencias_obtener$$
CREATE PROCEDURE sp_asistencias_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `asistencias` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `asistencias` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_ejercicios_listar$$
CREATE PROCEDURE sp_ejercicios_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `ejercicios` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_ejercicios_obtener$$
CREATE PROCEDURE sp_ejercicios_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `ejercicios` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `ejercicios` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_rutinas_listar$$
CREATE PROCEDURE sp_rutinas_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `rutinas` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_rutinas_obtener$$
CREATE PROCEDURE sp_rutinas_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `rutinas` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `rutinas` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_versiones_rutina_listar$$
CREATE PROCEDURE sp_versiones_rutina_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `versiones_rutina` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_versiones_rutina_obtener$$
CREATE PROCEDURE sp_versiones_rutina_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `versiones_rutina` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `versiones_rutina` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_sesiones_rutina_listar$$
CREATE PROCEDURE sp_sesiones_rutina_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `sesiones_rutina` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_sesiones_rutina_obtener$$
CREATE PROCEDURE sp_sesiones_rutina_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `sesiones_rutina` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `sesiones_rutina` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_ejercicios_rutina_listar$$
CREATE PROCEDURE sp_ejercicios_rutina_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `ejercicios_rutina` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_ejercicios_rutina_obtener$$
CREATE PROCEDURE sp_ejercicios_rutina_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `ejercicios_rutina` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `ejercicios_rutina` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_evaluaciones_fisicas_listar$$
CREATE PROCEDURE sp_evaluaciones_fisicas_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `evaluaciones_fisicas` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_evaluaciones_fisicas_obtener$$
CREATE PROCEDURE sp_evaluaciones_fisicas_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `evaluaciones_fisicas` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `evaluaciones_fisicas` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_detalles_evaluacion_listar$$
CREATE PROCEDURE sp_detalles_evaluacion_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `detalles_evaluacion` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_detalles_evaluacion_obtener$$
CREATE PROCEDURE sp_detalles_evaluacion_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `detalles_evaluacion` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `detalles_evaluacion` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_auditoria_listar$$
CREATE PROCEDURE sp_auditoria_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `auditoria` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_auditoria_obtener$$
CREATE PROCEDURE sp_auditoria_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `auditoria` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `auditoria` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_historial_estados_personal_listar$$
CREATE PROCEDURE sp_historial_estados_personal_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `historial_estados_personal` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_historial_estados_personal_obtener$$
CREATE PROCEDURE sp_historial_estados_personal_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `historial_estados_personal` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `historial_estados_personal` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_datos_medicos_cliente_listar$$
CREATE PROCEDURE sp_datos_medicos_cliente_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `datos_medicos_cliente` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_datos_medicos_cliente_obtener$$
CREATE PROCEDURE sp_datos_medicos_cliente_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `datos_medicos_cliente` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `datos_medicos_cliente` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_entrenamientos_realizados_listar$$
CREATE PROCEDURE sp_entrenamientos_realizados_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `entrenamientos_realizados` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_entrenamientos_realizados_obtener$$
CREATE PROCEDURE sp_entrenamientos_realizados_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `entrenamientos_realizados` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `entrenamientos_realizados` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_series_realizadas_listar$$
CREATE PROCEDURE sp_series_realizadas_listar(IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED)
BEGIN
    IF p_limite IS NULL OR p_limite = 0 OR p_limite > 500 THEN SET p_limite = 100; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;
    SELECT * FROM `series_realizadas` ORDER BY id DESC LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_series_realizadas_obtener$$
CREATE PROCEDURE sp_series_realizadas_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `series_realizadas` WHERE id = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro no encontrado'; END IF;
    SELECT * FROM `series_realizadas` WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_permiso_rol_listar$$
CREATE PROCEDURE sp_permiso_rol_listar() BEGIN SELECT * FROM permiso_rol ORDER BY rol_id, permiso_id; END$$

DROP PROCEDURE IF EXISTS sp_role_user_listar$$
CREATE PROCEDURE sp_role_user_listar() BEGIN SELECT * FROM role_user ORDER BY user_id, role_id; END$$

DROP PROCEDURE IF EXISTS sp_objetivo_rutina_listar$$
CREATE PROCEDURE sp_objetivo_rutina_listar() BEGIN SELECT * FROM objetivo_rutina ORDER BY rutina_id, objetivo_id; END$$

DROP PROCEDURE IF EXISTS sp_ejercicio_grupo_muscular_listar$$
CREATE PROCEDURE sp_ejercicio_grupo_muscular_listar() BEGIN SELECT * FROM ejercicio_grupo_muscular ORDER BY ejercicio_id, grupo_muscular_id; END$$



-- =========================================================
-- CLIENTES, PERSONAL Y SEGURIDAD
-- =========================================================
DROP PROCEDURE IF EXISTS sp_clientes_buscar$$
CREATE PROCEDURE sp_clientes_buscar(IN p_texto VARCHAR(150), IN p_estado_id BIGINT UNSIGNED)
BEGIN
    SELECT c.*, CONCAT(c.nombre, ' ', c.apellido) AS nombre_completo, ec.codigo AS estado_codigo
    FROM clientes c JOIN estados_cliente ec ON ec.id = c.estado_cliente_id
    WHERE c.deleted_at IS NULL
      AND (p_estado_id IS NULL OR c.estado_cliente_id = p_estado_id)
      AND (p_texto IS NULL OR p_texto = '' OR c.numero_socio LIKE CONCAT('%',p_texto,'%')
           OR c.nombre LIKE CONCAT('%',p_texto,'%') OR c.apellido LIKE CONCAT('%',p_texto,'%')
           OR c.correo_electronico LIKE CONCAT('%',p_texto,'%') OR c.numero_identificacion LIKE CONCAT('%',p_texto,'%'))
    ORDER BY c.apellido,c.nombre;
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
 IF p_numero_socio IS NULL OR TRIM(p_numero_socio)='' OR p_nombre IS NULL OR TRIM(p_nombre)='' OR p_apellido IS NULL OR TRIM(p_apellido)='' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Número de socio, nombre y apellido son obligatorios'; END IF;
 IF p_fecha_nacimiento IS NOT NULL AND p_fecha_nacimiento>CURRENT_DATE THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Fecha de nacimiento inválida'; END IF;
 IF NOT EXISTS(SELECT 1 FROM estados_cliente WHERE id=p_estado_id AND activo=1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Estado de cliente inválido'; END IF;
 IF EXISTS(SELECT 1 FROM clientes WHERE numero_socio=TRIM(p_numero_socio)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Número de socio duplicado'; END IF;
 IF p_correo IS NOT NULL AND EXISTS(SELECT 1 FROM clientes WHERE correo_electronico=LOWER(TRIM(p_correo))) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Correo duplicado'; END IF;
 START TRANSACTION;
 INSERT INTO clientes(numero_socio,sexo_id,estado_cliente_id,nombre,apellido,tipo_identificacion,numero_identificacion,telefono,correo_electronico,direccion,ciudad,pais,fecha_nacimiento,fecha_registro,created_at,updated_at)
 VALUES(TRIM(p_numero_socio),p_sexo_id,p_estado_id,TRIM(p_nombre),TRIM(p_apellido),p_tipo_identificacion,p_numero_identificacion,p_telefono,LOWER(TRIM(p_correo)),p_direccion,p_ciudad,UPPER(p_pais),p_fecha_nacimiento,CURRENT_DATE,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SET v_id=LAST_INSERT_ID();
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
 IF p_fecha_nacimiento IS NOT NULL AND p_fecha_nacimiento>CURRENT_DATE THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Fecha de nacimiento inválida'; END IF;
 IF p_correo IS NOT NULL AND EXISTS(SELECT 1 FROM clientes WHERE correo_electronico=LOWER(TRIM(p_correo)) AND id<>p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Correo duplicado'; END IF;
 UPDATE clientes SET sexo_id=p_sexo_id,nombre=TRIM(p_nombre),apellido=TRIM(p_apellido),tipo_identificacion=p_tipo_identificacion,numero_identificacion=p_numero_identificacion,telefono=p_telefono,correo_electronico=LOWER(TRIM(p_correo)),direccion=p_direccion,ciudad=p_ciudad,pais=UPPER(p_pais),fecha_nacimiento=p_fecha_nacimiento,updated_at=CURRENT_TIMESTAMP WHERE id=p_id;
 SELECT * FROM clientes WHERE id=p_id;
END$$

DROP PROCEDURE IF EXISTS sp_clientes_cambiar_estado$$
CREATE PROCEDURE sp_clientes_cambiar_estado(IN p_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_motivo VARCHAR(255),IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_anterior BIGINT UNSIGNED;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 START TRANSACTION;
 SELECT estado_cliente_id INTO v_anterior FROM clientes WHERE id=p_id AND deleted_at IS NULL FOR UPDATE;
 IF v_anterior IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Cliente no encontrado'; END IF;
 IF NOT EXISTS(SELECT 1 FROM estados_cliente WHERE id=p_estado_id AND activo=1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Estado inválido'; END IF;
 IF v_anterior=p_estado_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente ya tiene ese estado'; END IF;
 UPDATE clientes SET estado_cliente_id=p_estado_id,motivo_estado=p_motivo,updated_at=CURRENT_TIMESTAMP WHERE id=p_id;
 INSERT INTO historial_estados_cliente(cliente_id,estado_anterior_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(p_id,v_anterior,p_estado_id,p_motivo,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_clientes_eliminar$$
CREATE PROCEDURE sp_clientes_eliminar(IN p_id BIGINT UNSIGNED,IN p_usuario_id BIGINT UNSIGNED,IN p_motivo VARCHAR(255))
BEGIN
 IF NOT EXISTS(SELECT 1 FROM clientes WHERE id=p_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Cliente no encontrado'; END IF;
 IF EXISTS(SELECT 1 FROM membresias WHERE cliente_id=p_id AND bloqueo_activa=1 AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No puede retirarse un cliente con membresía activa'; END IF;
 UPDATE clientes SET deleted_at=CURRENT_TIMESTAMP,motivo_estado=p_motivo,updated_at=CURRENT_TIMESTAMP WHERE id=p_id;
END$$

DROP PROCEDURE IF EXISTS sp_personal_buscar$$
CREATE PROCEDURE sp_personal_buscar(IN p_texto VARCHAR(150),IN p_estado_id BIGINT UNSIGNED)
BEGIN
 SELECT p.*,c.nombre cargo,ep.nombre estado FROM personal p JOIN cargos c ON c.id=p.cargo_id JOIN estados_personal ep ON ep.id=p.estado_personal_id
 WHERE p.deleted_at IS NULL AND (p_estado_id IS NULL OR p.estado_personal_id=p_estado_id)
 AND (p_texto IS NULL OR p_texto='' OR p.codigo_empleado LIKE CONCAT('%',p_texto,'%') OR p.nombre LIKE CONCAT('%',p_texto,'%') OR p.apellido LIKE CONCAT('%',p_texto,'%'))
 ORDER BY p.apellido,p.nombre;
END$$

DROP PROCEDURE IF EXISTS sp_personal_crear$$
CREATE PROCEDURE sp_personal_crear(IN p_codigo VARCHAR(30),IN p_cargo_id BIGINT UNSIGNED,IN p_sexo_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_nombre VARCHAR(100),IN p_apellido VARCHAR(100),IN p_tipo_id VARCHAR(30),IN p_numero_id VARCHAR(60),IN p_telefono VARCHAR(25),IN p_correo VARCHAR(150),IN p_fecha_contratacion DATE)
BEGIN
 IF EXISTS(SELECT 1 FROM personal WHERE codigo_empleado=p_codigo) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Código de empleado duplicado'; END IF;
 IF NOT EXISTS(SELECT 1 FROM cargos WHERE id=p_cargo_id AND activo=1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Cargo inválido'; END IF;
 INSERT INTO personal(codigo_empleado,cargo_id,sexo_id,estado_personal_id,nombre,apellido,tipo_identificacion,numero_identificacion,telefono,correo_electronico,fecha_contratacion,created_at,updated_at)
 VALUES(p_codigo,p_cargo_id,p_sexo_id,p_estado_id,TRIM(p_nombre),TRIM(p_apellido),p_tipo_id,p_numero_id,p_telefono,LOWER(TRIM(p_correo)),p_fecha_contratacion,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SELECT * FROM personal WHERE id=LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_personal_actualizar$$
CREATE PROCEDURE sp_personal_actualizar(IN p_id BIGINT UNSIGNED,IN p_cargo_id BIGINT UNSIGNED,IN p_sexo_id BIGINT UNSIGNED,IN p_nombre VARCHAR(100),IN p_apellido VARCHAR(100),IN p_telefono VARCHAR(25),IN p_correo VARCHAR(150))
BEGIN
 IF NOT EXISTS(SELECT 1 FROM personal WHERE id=p_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Personal no encontrado'; END IF;
 UPDATE personal SET cargo_id=p_cargo_id,sexo_id=p_sexo_id,nombre=TRIM(p_nombre),apellido=TRIM(p_apellido),telefono=p_telefono,correo_electronico=LOWER(TRIM(p_correo)),updated_at=CURRENT_TIMESTAMP WHERE id=p_id;
 SELECT * FROM personal WHERE id=p_id;
END$$

DROP PROCEDURE IF EXISTS sp_personal_cambiar_estado$$
CREATE PROCEDURE sp_personal_cambiar_estado(IN p_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_motivo VARCHAR(255),IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_anterior BIGINT UNSIGNED; DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 START TRANSACTION;
 SELECT estado_personal_id INTO v_anterior FROM personal WHERE id=p_id AND deleted_at IS NULL FOR UPDATE;
 IF v_anterior IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Personal no encontrado'; END IF;
 UPDATE personal SET estado_personal_id=p_estado_id,updated_at=CURRENT_TIMESTAMP WHERE id=p_id;
 INSERT INTO historial_estados_personal(personal_id,estado_anterior_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(p_id,v_anterior,p_estado_id,p_motivo,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_personal_eliminar$$
CREATE PROCEDURE sp_personal_eliminar(IN p_id BIGINT UNSIGNED,IN p_fecha_terminacion DATE,IN p_motivo VARCHAR(255))
BEGIN
 IF NOT EXISTS(SELECT 1 FROM personal WHERE id=p_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Personal no encontrado'; END IF;
 UPDATE personal SET fecha_terminacion=COALESCE(p_fecha_terminacion,CURRENT_DATE),motivo_terminacion=p_motivo,deleted_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP WHERE id=p_id;
 UPDATE users SET activo=0,deleted_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP WHERE personal_id=p_id AND deleted_at IS NULL;
END$$

DROP PROCEDURE IF EXISTS sp_users_crear$$
CREATE PROCEDURE sp_users_crear(IN p_personal_id BIGINT UNSIGNED,IN p_name VARCHAR(255),IN p_username VARCHAR(60),IN p_email VARCHAR(150),IN p_password_hash VARCHAR(255),IN p_debe_cambiar BOOLEAN)
BEGIN
 IF p_password_hash IS NULL OR CHAR_LENGTH(p_password_hash)<50 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Debe enviarse un hash seguro, nunca una contraseña en texto claro'; END IF;
 IF EXISTS(SELECT 1 FROM users WHERE username=LOWER(TRIM(p_username)) OR (p_email IS NOT NULL AND email=LOWER(TRIM(p_email)))) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuario o correo duplicado'; END IF;
 IF p_personal_id IS NOT NULL AND EXISTS(SELECT 1 FROM users WHERE personal_id=p_personal_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El empleado ya tiene usuario'; END IF;
 INSERT INTO users(personal_id,name,username,email,password,activo,debe_cambiar_password,created_at,updated_at) VALUES(p_personal_id,p_name,LOWER(TRIM(p_username)),LOWER(TRIM(p_email)),p_password_hash,1,p_debe_cambiar,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SELECT id,personal_id,name,username,email,activo,debe_cambiar_password,created_at FROM users WHERE id=LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_users_obtener_credenciales$$
CREATE PROCEDURE sp_users_obtener_credenciales(IN p_login VARCHAR(150))
BEGIN
 SELECT id,personal_id,name,username,email,password,activo,debe_cambiar_password,intentos_fallidos,bloqueado_hasta,deleted_at
 FROM users WHERE deleted_at IS NULL AND (username=LOWER(TRIM(p_login)) OR email=LOWER(TRIM(p_login))) LIMIT 1;
END$$

DROP PROCEDURE IF EXISTS sp_password_reset_tokens_guardar$$
CREATE PROCEDURE sp_password_reset_tokens_guardar(IN p_email VARCHAR(255),IN p_token VARCHAR(255))
BEGIN
 INSERT INTO password_reset_tokens(email,token,created_at) VALUES(LOWER(TRIM(p_email)),p_token,CURRENT_TIMESTAMP)
 ON DUPLICATE KEY UPDATE token=p_token,created_at=CURRENT_TIMESTAMP;
END$$

DROP PROCEDURE IF EXISTS sp_password_reset_tokens_obtener$$
CREATE PROCEDURE sp_password_reset_tokens_obtener(IN p_email VARCHAR(255))
BEGIN SELECT email,token,created_at FROM password_reset_tokens WHERE email=LOWER(TRIM(p_email)); END$$

DROP PROCEDURE IF EXISTS sp_password_reset_tokens_eliminar$$
CREATE PROCEDURE sp_password_reset_tokens_eliminar(IN p_email VARCHAR(255))
BEGIN DELETE FROM password_reset_tokens WHERE email=LOWER(TRIM(p_email)); END$$

DROP PROCEDURE IF EXISTS sp_users_actualizar$$
CREATE PROCEDURE sp_users_actualizar(IN p_id BIGINT UNSIGNED,IN p_name VARCHAR(255),IN p_username VARCHAR(60),IN p_email VARCHAR(150),IN p_activo BOOLEAN)
BEGIN
 IF NOT EXISTS(SELECT 1 FROM users WHERE id=p_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuario no encontrado'; END IF;
 IF EXISTS(SELECT 1 FROM users WHERE id<>p_id AND (username=LOWER(TRIM(p_username)) OR (p_email IS NOT NULL AND email=LOWER(TRIM(p_email))))) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuario o correo duplicado'; END IF;
 UPDATE users SET name=p_name,username=LOWER(TRIM(p_username)),email=LOWER(TRIM(p_email)),activo=p_activo,updated_at=CURRENT_TIMESTAMP WHERE id=p_id;
END$$

DROP PROCEDURE IF EXISTS sp_users_cambiar_password$$
CREATE PROCEDURE sp_users_cambiar_password(IN p_id BIGINT UNSIGNED,IN p_password_hash VARCHAR(255),IN p_debe_cambiar BOOLEAN)
BEGIN
 IF CHAR_LENGTH(p_password_hash)<50 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Hash de contraseña inválido'; END IF;
 UPDATE users SET password=p_password_hash,debe_cambiar_password=p_debe_cambiar,password_changed_at=CURRENT_TIMESTAMP,intentos_fallidos=0,bloqueado_hasta=NULL,updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND deleted_at IS NULL;
 IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuario no encontrado'; END IF;
END$$

DROP PROCEDURE IF EXISTS sp_users_asignar_rol$$
CREATE PROCEDURE sp_users_asignar_rol(IN p_user_id BIGINT UNSIGNED,IN p_role_id BIGINT UNSIGNED)
BEGIN
 IF NOT EXISTS(SELECT 1 FROM users WHERE id=p_user_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuario no encontrado'; END IF;
 IF NOT EXISTS(SELECT 1 FROM roles WHERE id=p_role_id AND activo=1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Rol inválido'; END IF;
 INSERT IGNORE INTO role_user(role_id,user_id,created_at,updated_at) VALUES(p_role_id,p_user_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
END$$

DROP PROCEDURE IF EXISTS sp_users_retirar_rol$$
CREATE PROCEDURE sp_users_retirar_rol(IN p_user_id BIGINT UNSIGNED,IN p_role_id BIGINT UNSIGNED)
BEGIN DELETE FROM role_user WHERE user_id=p_user_id AND role_id=p_role_id; END$$

DROP PROCEDURE IF EXISTS sp_roles_asignar_permiso$$
CREATE PROCEDURE sp_roles_asignar_permiso(IN p_role_id BIGINT UNSIGNED,IN p_permiso_id BIGINT UNSIGNED)
BEGIN
 IF NOT EXISTS(SELECT 1 FROM roles WHERE id=p_role_id AND activo=1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Rol inválido'; END IF;
 IF NOT EXISTS(SELECT 1 FROM permisos WHERE id=p_permiso_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Permiso inválido'; END IF;
 INSERT IGNORE INTO permiso_rol(permiso_id,role_id,created_at,updated_at) VALUES(p_permiso_id,p_role_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
END$$

DROP PROCEDURE IF EXISTS sp_roles_retirar_permiso$$
CREATE PROCEDURE sp_roles_retirar_permiso(IN p_role_id BIGINT UNSIGNED,IN p_permiso_id BIGINT UNSIGNED)
BEGIN DELETE FROM permiso_rol WHERE role_id=p_role_id AND permiso_id=p_permiso_id; END$$

DROP PROCEDURE IF EXISTS sp_users_eliminar$$
CREATE PROCEDURE sp_users_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN UPDATE users SET activo=0,deleted_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND deleted_at IS NULL; IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuario no encontrado'; END IF; END$$

DROP PROCEDURE IF EXISTS sp_contactos_emergencia_guardar$$
CREATE PROCEDURE sp_contactos_emergencia_guardar(IN p_id BIGINT UNSIGNED,IN p_cliente_id BIGINT UNSIGNED,IN p_nombre VARCHAR(150),IN p_parentesco VARCHAR(60),IN p_telefono VARCHAR(25),IN p_principal BOOLEAN)
BEGIN
 IF p_principal=1 THEN UPDATE contactos_emergencia SET es_principal=0,updated_at=CURRENT_TIMESTAMP WHERE cliente_id=p_cliente_id; END IF;
 IF p_id IS NULL THEN INSERT INTO contactos_emergencia(cliente_id,nombre_completo,parentesco,telefono,es_principal,created_at,updated_at) VALUES(p_cliente_id,p_nombre,p_parentesco,p_telefono,p_principal,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP); SET p_id=LAST_INSERT_ID();
 ELSE UPDATE contactos_emergencia SET nombre_completo=p_nombre,parentesco=p_parentesco,telefono=p_telefono,es_principal=p_principal,updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND cliente_id=p_cliente_id; END IF;
 SELECT * FROM contactos_emergencia WHERE id=p_id;
END$$

DROP PROCEDURE IF EXISTS sp_contactos_emergencia_eliminar$$
CREATE PROCEDURE sp_contactos_emergencia_eliminar(IN p_id BIGINT UNSIGNED) BEGIN DELETE FROM contactos_emergencia WHERE id=p_id; IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Contacto no encontrado'; END IF; END$$

DROP PROCEDURE IF EXISTS sp_consentimientos_cliente_registrar$$
CREATE PROCEDURE sp_consentimientos_cliente_registrar(IN p_cliente_id BIGINT UNSIGNED,IN p_tipo VARCHAR(60),IN p_version VARCHAR(30),IN p_aceptado BOOLEAN,IN p_ip VARCHAR(45),IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 IF EXISTS(SELECT 1 FROM consentimientos_cliente WHERE cliente_id=p_cliente_id AND tipo=p_tipo AND version_documento=p_version) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Consentimiento ya registrado para esta versión'; END IF;
 INSERT INTO consentimientos_cliente(cliente_id,tipo,version_documento,aceptado,registrado_at,ip,registrado_por,created_at,updated_at) VALUES(p_cliente_id,p_tipo,p_version,p_aceptado,CURRENT_TIMESTAMP,p_ip,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
END$$

DROP PROCEDURE IF EXISTS sp_datos_medicos_cliente_guardar$$
CREATE PROCEDURE sp_datos_medicos_cliente_guardar(IN p_cliente_id BIGINT UNSIGNED,IN p_condiciones TEXT,IN p_alergias TEXT,IN p_medicamentos TEXT,IN p_restricciones TEXT,IN p_contacto_medico VARCHAR(150),IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 INSERT INTO datos_medicos_cliente(cliente_id,condiciones_medicas,alergias,medicamentos,restricciones_ejercicio,contacto_medico,actualizado_at,actualizado_por,created_at,updated_at)
 VALUES(p_cliente_id,p_condiciones,p_alergias,p_medicamentos,p_restricciones,p_contacto_medico,CURRENT_TIMESTAMP,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)
 ON DUPLICATE KEY UPDATE condiciones_medicas=p_condiciones,alergias=p_alergias,medicamentos=p_medicamentos,restricciones_ejercicio=p_restricciones,contacto_medico=p_contacto_medico,actualizado_at=CURRENT_TIMESTAMP,actualizado_por=p_usuario_id,updated_at=CURRENT_TIMESTAMP,deleted_at=NULL;
END$$

DROP PROCEDURE IF EXISTS sp_horarios_personal_guardar$$
CREATE PROCEDURE sp_horarios_personal_guardar(IN p_id BIGINT UNSIGNED,IN p_personal_id BIGINT UNSIGNED,IN p_dia TINYINT UNSIGNED,IN p_inicio TIME,IN p_fin TIME,IN p_desde DATE,IN p_hasta DATE)
BEGIN
 IF p_dia NOT BETWEEN 1 AND 7 OR p_fin<=p_inicio OR (p_hasta IS NOT NULL AND p_hasta<p_desde) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Horario inválido'; END IF;
 IF EXISTS(SELECT 1 FROM horarios_personal WHERE personal_id=p_personal_id AND dia_semana=p_dia AND id<>COALESCE(p_id,0) AND hora_inicio<p_fin AND hora_fin>p_inicio AND (vigente_hasta IS NULL OR vigente_hasta>=p_desde) AND (p_hasta IS NULL OR vigente_desde<=p_hasta)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El horario se superpone con otro vigente'; END IF;
 IF p_id IS NULL THEN INSERT INTO horarios_personal(personal_id,dia_semana,hora_inicio,hora_fin,vigente_desde,vigente_hasta,created_at,updated_at) VALUES(p_personal_id,p_dia,p_inicio,p_fin,p_desde,p_hasta,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP); SET p_id=LAST_INSERT_ID();
 ELSE UPDATE horarios_personal SET dia_semana=p_dia,hora_inicio=p_inicio,hora_fin=p_fin,vigente_desde=p_desde,vigente_hasta=p_hasta,updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND personal_id=p_personal_id; END IF;
 SELECT * FROM horarios_personal WHERE id=p_id;
END$$

DROP PROCEDURE IF EXISTS sp_horarios_personal_eliminar$$
CREATE PROCEDURE sp_horarios_personal_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN DELETE FROM horarios_personal WHERE id=p_id; IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Horario no encontrado'; END IF; END$$


-- =========================================================
-- MEMBRESÍAS, PRECIOS, CARGOS Y PAGOS
-- =========================================================
DROP PROCEDURE IF EXISTS sp_precios_membresia_crear$$
CREATE PROCEDURE sp_precios_membresia_crear(IN p_tipo_id BIGINT UNSIGNED,IN p_precio DECIMAL(12,2),IN p_moneda CHAR(3),IN p_desde DATE,IN p_motivo VARCHAR(255))
BEGIN
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 IF p_precio<=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El precio debe ser mayor que cero'; END IF;
 IF NOT EXISTS(SELECT 1 FROM tipos_membresia WHERE id=p_tipo_id AND activo=1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Tipo de membresía inválido'; END IF;
 IF EXISTS(SELECT 1 FROM precios_membresia WHERE tipo_membresia_id=p_tipo_id AND moneda=UPPER(p_moneda) AND vigente_desde=p_desde) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Ya existe un precio con esa fecha inicial'; END IF;
 START TRANSACTION;
 UPDATE precios_membresia SET vigente_hasta=DATE_SUB(p_desde,INTERVAL 1 DAY),updated_at=CURRENT_TIMESTAMP WHERE tipo_membresia_id=p_tipo_id AND moneda=UPPER(p_moneda) AND vigente_hasta IS NULL AND vigente_desde<p_desde;
 INSERT INTO precios_membresia(tipo_membresia_id,precio,moneda,vigente_desde,motivo_cambio,created_at,updated_at) VALUES(p_tipo_id,p_precio,UPPER(p_moneda),p_desde,p_motivo,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
 SELECT * FROM precios_membresia WHERE id=LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_membresias_buscar$$
CREATE PROCEDURE sp_membresias_buscar(IN p_cliente_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_desde DATE,IN p_hasta DATE)
BEGIN
 SELECT m.*,tm.nombre tipo,em.nombre estado,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente
 FROM membresias m JOIN clientes c ON c.id=m.cliente_id JOIN tipos_membresia tm ON tm.id=m.tipo_membresia_id JOIN estados_membresia em ON em.id=m.estado_membresia_id
 WHERE m.deleted_at IS NULL AND (p_cliente_id IS NULL OR m.cliente_id=p_cliente_id) AND (p_estado_id IS NULL OR m.estado_membresia_id=p_estado_id)
 AND (p_desde IS NULL OR m.fecha_fin>=p_desde) AND (p_hasta IS NULL OR m.fecha_inicio<=p_hasta)
 ORDER BY m.fecha_inicio DESC;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_crear$$
CREATE PROCEDURE sp_membresias_crear(IN p_cliente_id BIGINT UNSIGNED,IN p_tipo_id BIGINT UNSIGNED,IN p_precio_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_fecha_inicio DATE,IN p_fecha_fin DATE,IN p_origen VARCHAR(30),IN p_anterior_id BIGINT UNSIGNED,IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_precio DECIMAL(12,2); DECLARE v_moneda CHAR(3); DECLARE v_id BIGINT UNSIGNED; DECLARE v_lock BIGINT UNSIGNED;
 DECLARE EXIT HANDLER FOR 1062 BEGIN ROLLBACK; SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente ya posee una membresía activa'; END;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 IF p_fecha_fin<p_fecha_inicio THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Periodo de membresía inválido'; END IF;
 IF NOT EXISTS(SELECT 1 FROM clientes WHERE id=p_cliente_id AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Cliente no encontrado'; END IF;
 SELECT precio,moneda INTO v_precio,v_moneda FROM precios_membresia WHERE id=p_precio_id AND tipo_membresia_id=p_tipo_id AND vigente_desde<=p_fecha_inicio AND (vigente_hasta IS NULL OR vigente_hasta>=p_fecha_inicio);
 IF v_precio IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Precio no vigente o incompatible con el plan'; END IF;
 START TRANSACTION;
 SELECT id INTO v_lock FROM clientes WHERE id=p_cliente_id FOR UPDATE;
 IF EXISTS(SELECT 1 FROM membresias WHERE cliente_id=p_cliente_id AND bloqueo_activa=1 AND deleted_at IS NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente ya posee una membresía activa'; END IF;
 INSERT INTO membresias(cliente_id,tipo_membresia_id,precio_membresia_id,estado_membresia_id,membresia_anterior_id,fecha_inicio,fecha_fin,precio_contratado,moneda,bloqueo_activa,origen,creada_por,created_at,updated_at)
 VALUES(p_cliente_id,p_tipo_id,p_precio_id,p_estado_id,p_anterior_id,p_fecha_inicio,p_fecha_fin,v_precio,v_moneda,1,COALESCE(p_origen,'NUEVA'),p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SET v_id=LAST_INSERT_ID();
 INSERT INTO historial_estados_membresia(membresia_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(v_id,p_estado_id,'Alta de membresía',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
 SELECT * FROM membresias WHERE id=v_id;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_cambiar_estado$$
CREATE PROCEDURE sp_membresias_cambiar_estado(IN p_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_mantener_activa BOOLEAN,IN p_motivo VARCHAR(255),IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_anterior BIGINT UNSIGNED; DECLARE v_cliente BIGINT UNSIGNED;
 DECLARE EXIT HANDLER FOR 1062 BEGIN ROLLBACK; SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente ya posee otra membresía activa'; END;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 START TRANSACTION;
 SELECT estado_membresia_id,cliente_id INTO v_anterior,v_cliente FROM membresias WHERE id=p_id AND deleted_at IS NULL FOR UPDATE;
 IF v_anterior IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Membresía no encontrada'; END IF;
 IF NOT EXISTS(SELECT 1 FROM estados_membresia WHERE id=p_estado_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Estado inválido'; END IF;
 UPDATE membresias SET estado_membresia_id=p_estado_id,bloqueo_activa=IF(p_mantener_activa,1,NULL),updated_at=CURRENT_TIMESTAMP WHERE id=p_id;
 INSERT INTO historial_estados_membresia(membresia_id,estado_anterior_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(p_id,v_anterior,p_estado_id,p_motivo,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_cancelar$$
CREATE PROCEDURE sp_membresias_cancelar(IN p_id BIGINT UNSIGNED,IN p_estado_cancelada_id BIGINT UNSIGNED,IN p_motivo VARCHAR(255),IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 IF p_motivo IS NULL OR TRIM(p_motivo)='' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El motivo de cancelación es obligatorio'; END IF;
 START TRANSACTION;
 UPDATE membresias SET estado_membresia_id=p_estado_cancelada_id,bloqueo_activa=NULL,cancelada_at=CURRENT_TIMESTAMP,motivo_cancelacion=p_motivo,cancelada_por=p_usuario_id,updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND deleted_at IS NULL AND cancelada_at IS NULL;
 IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Membresía inexistente o ya cancelada'; END IF;
 INSERT INTO historial_estados_membresia(membresia_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(p_id,p_estado_cancelada_id,p_motivo,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_suspender$$
CREATE PROCEDURE sp_membresias_suspender(IN p_id BIGINT UNSIGNED,IN p_fecha_inicio DATE,IN p_fecha_fin DATE,IN p_dias_extension SMALLINT UNSIGNED,IN p_motivo VARCHAR(255),IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 IF p_fecha_fin IS NOT NULL AND p_fecha_fin<p_fecha_inicio THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Periodo de suspensión inválido'; END IF;
 IF EXISTS(SELECT 1 FROM suspensiones_membresia WHERE membresia_id=p_id AND (fecha_fin IS NULL OR fecha_fin>=p_fecha_inicio) AND (p_fecha_fin IS NULL OR fecha_inicio<=p_fecha_fin)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La suspensión se superpone con otra existente'; END IF;
 START TRANSACTION;
 INSERT INTO suspensiones_membresia(membresia_id,fecha_inicio,fecha_fin,dias_extension,motivo,autorizada_por,created_at,updated_at) VALUES(p_id,p_fecha_inicio,p_fecha_fin,COALESCE(p_dias_extension,0),p_motivo,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 IF p_dias_extension>0 THEN UPDATE membresias SET fecha_fin=DATE_ADD(fecha_fin,INTERVAL p_dias_extension DAY),updated_at=CURRENT_TIMESTAMP WHERE id=p_id; END IF;
 COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_membresias_eliminar$$
CREATE PROCEDURE sp_membresias_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
 IF EXISTS(SELECT 1 FROM cargos_cobro WHERE membresia_id=p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No puede retirarse una membresía con historial financiero'; END IF;
 UPDATE membresias SET bloqueo_activa=NULL,deleted_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND deleted_at IS NULL;
 IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Membresía no encontrada'; END IF;
END$$

DROP PROCEDURE IF EXISTS sp_cargos_cobro_crear$$
CREATE PROCEDURE sp_cargos_cobro_crear(IN p_numero VARCHAR(40),IN p_cliente_id BIGINT UNSIGNED,IN p_membresia_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_concepto VARCHAR(150),IN p_descripcion TEXT,IN p_subtotal DECIMAL(12,2),IN p_descuento DECIMAL(12,2),IN p_impuesto DECIMAL(12,2),IN p_moneda CHAR(3),IN p_vencimiento DATE,IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_total DECIMAL(12,2);
 SET v_total=p_subtotal-COALESCE(p_descuento,0)+COALESCE(p_impuesto,0);
 IF v_total<0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Total de cargo inválido'; END IF;
 IF EXISTS(SELECT 1 FROM cargos_cobro WHERE numero_cargo=p_numero) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Número de cargo duplicado'; END IF;
 INSERT INTO cargos_cobro(numero_cargo,cliente_id,membresia_id,estado_cargo_cobro_id,concepto,descripcion,subtotal,descuento,impuesto,total,moneda,fecha_emision,fecha_vencimiento,creado_por,created_at,updated_at)
 VALUES(p_numero,p_cliente_id,p_membresia_id,p_estado_id,p_concepto,p_descripcion,p_subtotal,COALESCE(p_descuento,0),COALESCE(p_impuesto,0),v_total,UPPER(p_moneda),CURRENT_DATE,p_vencimiento,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SELECT * FROM cargos_cobro WHERE id=LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_cargos_cobro_cambiar_estado$$
CREATE PROCEDURE sp_cargos_cobro_cambiar_estado(IN p_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED)
BEGIN UPDATE cargos_cobro SET estado_cargo_cobro_id=p_estado_id,updated_at=CURRENT_TIMESTAMP WHERE id=p_id; IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Cargo no encontrado'; END IF; END$$

DROP PROCEDURE IF EXISTS sp_pagos_registrar$$
CREATE PROCEDURE sp_pagos_registrar(IN p_idempotency CHAR(36),IN p_recibo VARCHAR(40),IN p_cliente_id BIGINT UNSIGNED,IN p_metodo_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_monto DECIMAL(12,2),IN p_moneda CHAR(3),IN p_referencia VARCHAR(120),IN p_referencia_externa VARCHAR(150),IN p_pagado_at DATETIME,IN p_usuario_id BIGINT UNSIGNED,IN p_observaciones TEXT)
BEGIN
 DECLARE v_id BIGINT UNSIGNED;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 IF p_monto<=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Monto inválido'; END IF;
 IF EXISTS(SELECT 1 FROM pagos WHERE idempotency_key=p_idempotency OR numero_recibo=p_recibo) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Pago duplicado'; END IF;
 IF EXISTS(SELECT 1 FROM metodos_pago WHERE id=p_metodo_id AND requiere_referencia=1) AND (p_referencia IS NULL OR TRIM(p_referencia)='') THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El método exige referencia'; END IF;
 START TRANSACTION;
 INSERT INTO pagos(idempotency_key,numero_recibo,cliente_id,metodo_pago_id,estado_pago_id,monto,moneda,referencia,referencia_externa,pagado_at,procesado_por,observaciones,created_at,updated_at)
 VALUES(p_idempotency,p_recibo,p_cliente_id,p_metodo_id,p_estado_id,p_monto,UPPER(p_moneda),p_referencia,p_referencia_externa,p_pagado_at,p_usuario_id,p_observaciones,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SET v_id=LAST_INSERT_ID();
 INSERT INTO historial_estados_pago(pago_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(v_id,p_estado_id,'Registro de pago',p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
 SELECT * FROM pagos WHERE id=v_id;
END$$

DROP PROCEDURE IF EXISTS sp_pagos_aplicar$$
CREATE PROCEDURE sp_pagos_aplicar(IN p_pago_id BIGINT UNSIGNED,IN p_cargo_id BIGINT UNSIGNED,IN p_monto DECIMAL(12,2))
BEGIN
 DECLARE v_pago_total DECIMAL(12,2); DECLARE v_pago_usado DECIMAL(12,2); DECLARE v_cargo_total DECIMAL(12,2); DECLARE v_cargo_pagado DECIMAL(12,2); DECLARE v_moneda_pago CHAR(3); DECLARE v_moneda_cargo CHAR(3);
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 START TRANSACTION;
 SELECT monto,moneda INTO v_pago_total,v_moneda_pago FROM pagos WHERE id=p_pago_id FOR UPDATE;
 SELECT total,moneda INTO v_cargo_total,v_moneda_cargo FROM cargos_cobro WHERE id=p_cargo_id FOR UPDATE;
 IF v_pago_total IS NULL OR v_cargo_total IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Pago o cargo inexistente'; END IF;
 IF v_moneda_pago<>v_moneda_cargo THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Las monedas no coinciden'; END IF;
 SELECT COALESCE(SUM(monto_aplicado),0) INTO v_pago_usado FROM aplicaciones_pago WHERE pago_id=p_pago_id;
 SELECT COALESCE(SUM(monto_aplicado),0) INTO v_cargo_pagado FROM aplicaciones_pago WHERE cargo_cobro_id=p_cargo_id;
 IF p_monto<=0 OR v_pago_usado+p_monto>v_pago_total THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La aplicación excede el saldo del pago'; END IF;
 IF v_cargo_pagado+p_monto>v_cargo_total THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La aplicación excede el saldo del cargo'; END IF;
 INSERT INTO aplicaciones_pago(pago_id,cargo_cobro_id,monto_aplicado,created_at,updated_at) VALUES(p_pago_id,p_cargo_id,p_monto,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)
 ON DUPLICATE KEY UPDATE monto_aplicado=monto_aplicado+p_monto,updated_at=CURRENT_TIMESTAMP;
 COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_pagos_cambiar_estado$$
CREATE PROCEDURE sp_pagos_cambiar_estado(IN p_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_motivo VARCHAR(255),IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_anterior BIGINT UNSIGNED; DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 START TRANSACTION; SELECT estado_pago_id INTO v_anterior FROM pagos WHERE id=p_id FOR UPDATE;
 IF v_anterior IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Pago no encontrado'; END IF;
 UPDATE pagos SET estado_pago_id=p_estado_id,updated_at=CURRENT_TIMESTAMP WHERE id=p_id;
 INSERT INTO historial_estados_pago(pago_id,estado_anterior_id,estado_nuevo_id,motivo,cambiado_por,cambiado_at,created_at,updated_at) VALUES(p_id,v_anterior,p_estado_id,p_motivo,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_reembolsos_crear$$
CREATE PROCEDURE sp_reembolsos_crear(IN p_numero VARCHAR(40),IN p_pago_id BIGINT UNSIGNED,IN p_monto DECIMAL(12,2),IN p_motivo VARCHAR(255),IN p_referencia VARCHAR(150),IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_total DECIMAL(12,2); DECLARE v_reembolsado DECIMAL(12,2); DECLARE v_moneda CHAR(3);
 SELECT monto,moneda INTO v_total,v_moneda FROM pagos WHERE id=p_pago_id;
 IF v_total IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Pago no encontrado'; END IF;
 SELECT COALESCE(SUM(monto),0) INTO v_reembolsado FROM reembolsos WHERE pago_id=p_pago_id;
 IF p_monto<=0 OR v_reembolsado+p_monto>v_total THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Monto de reembolso inválido'; END IF;
 INSERT INTO reembolsos(numero_reembolso,pago_id,monto,moneda,motivo,referencia_externa,procesado_at,procesado_por,created_at,updated_at) VALUES(p_numero,p_pago_id,p_monto,v_moneda,p_motivo,p_referencia,CURRENT_TIMESTAMP,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
END$$


-- =========================================================
-- ASISTENCIA, EJERCICIOS, RUTINAS Y EVALUACIONES
-- =========================================================
DROP PROCEDURE IF EXISTS sp_asistencias_registrar_entrada$$
CREATE PROCEDURE sp_asistencias_registrar_entrada(IN p_cliente_id BIGINT UNSIGNED,IN p_membresia_id BIGINT UNSIGNED,IN p_entrada DATETIME,IN p_metodo VARCHAR(30),IN p_usuario_id BIGINT UNSIGNED,IN p_observaciones TEXT)
BEGIN
 DECLARE EXIT HANDLER FOR 1062 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente ya tiene una asistencia abierta';
 IF NOT EXISTS(SELECT 1 FROM membresias m JOIN estados_membresia e ON e.id=m.estado_membresia_id WHERE m.id=p_membresia_id AND m.cliente_id=p_cliente_id AND m.deleted_at IS NULL AND m.bloqueo_activa=1 AND e.permite_acceso=1 AND DATE(p_entrada) BETWEEN m.fecha_inicio AND m.fecha_fin) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El cliente no posee una membresía válida para el acceso'; END IF;
 IF EXISTS(SELECT 1 FROM suspensiones_membresia WHERE membresia_id=p_membresia_id AND DATE(p_entrada)>=fecha_inicio AND (fecha_fin IS NULL OR DATE(p_entrada)<=fecha_fin)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La membresía se encuentra suspendida'; END IF;
 INSERT INTO asistencias(cliente_id,membresia_id,entrada_at,bloqueo_abierta,metodo_registro,entrada_registrada_por,observaciones,created_at,updated_at) VALUES(p_cliente_id,p_membresia_id,COALESCE(p_entrada,CURRENT_TIMESTAMP),1,COALESCE(p_metodo,'MANUAL'),p_usuario_id,p_observaciones,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SELECT * FROM asistencias WHERE id=LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_asistencias_registrar_salida$$
CREATE PROCEDURE sp_asistencias_registrar_salida(IN p_asistencia_id BIGINT UNSIGNED,IN p_salida DATETIME,IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 UPDATE asistencias SET salida_at=COALESCE(p_salida,CURRENT_TIMESTAMP),bloqueo_abierta=NULL,salida_registrada_por=p_usuario_id,updated_at=CURRENT_TIMESTAMP WHERE id=p_asistencia_id AND salida_at IS NULL AND COALESCE(p_salida,CURRENT_TIMESTAMP)>entrada_at;
 IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Asistencia inexistente, cerrada o con hora de salida inválida'; END IF;
END$$

DROP PROCEDURE IF EXISTS sp_asistencias_buscar$$
CREATE PROCEDURE sp_asistencias_buscar(IN p_cliente_id BIGINT UNSIGNED,IN p_desde DATETIME,IN p_hasta DATETIME,IN p_solo_abiertas BOOLEAN)
BEGIN
 SELECT a.*,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente,TIMESTAMPDIFF(MINUTE,a.entrada_at,a.salida_at) minutos
 FROM asistencias a JOIN clientes c ON c.id=a.cliente_id
 WHERE (p_cliente_id IS NULL OR a.cliente_id=p_cliente_id) AND (p_desde IS NULL OR a.entrada_at>=p_desde) AND (p_hasta IS NULL OR a.entrada_at<=p_hasta) AND (p_solo_abiertas=0 OR a.salida_at IS NULL)
 ORDER BY a.entrada_at DESC;
END$$

DROP PROCEDURE IF EXISTS sp_ejercicios_crear$$
CREATE PROCEDURE sp_ejercicios_crear(IN p_codigo VARCHAR(40),IN p_estado_id BIGINT UNSIGNED,IN p_nombre VARCHAR(120),IN p_patron VARCHAR(80),IN p_equipamiento VARCHAR(120),IN p_descripcion TEXT,IN p_instrucciones TEXT,IN p_video VARCHAR(500))
BEGIN
 IF EXISTS(SELECT 1 FROM ejercicios WHERE codigo=p_codigo) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Código de ejercicio duplicado'; END IF;
 INSERT INTO ejercicios(codigo,estado_ejercicio_id,nombre,patron_movimiento,equipamiento,descripcion,instrucciones,video_url,created_at,updated_at) VALUES(p_codigo,p_estado_id,p_nombre,p_patron,p_equipamiento,p_descripcion,p_instrucciones,p_video,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SELECT * FROM ejercicios WHERE id=LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_ejercicios_actualizar$$
CREATE PROCEDURE sp_ejercicios_actualizar(IN p_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_nombre VARCHAR(120),IN p_patron VARCHAR(80),IN p_equipamiento VARCHAR(120),IN p_descripcion TEXT,IN p_instrucciones TEXT,IN p_video VARCHAR(500))
BEGIN UPDATE ejercicios SET estado_ejercicio_id=p_estado_id,nombre=p_nombre,patron_movimiento=p_patron,equipamiento=p_equipamiento,descripcion=p_descripcion,instrucciones=p_instrucciones,video_url=p_video,updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND deleted_at IS NULL; IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Ejercicio no encontrado'; END IF; END$$

DROP PROCEDURE IF EXISTS sp_ejercicios_asignar_grupo$$
CREATE PROCEDURE sp_ejercicios_asignar_grupo(IN p_ejercicio_id BIGINT UNSIGNED,IN p_grupo_id BIGINT UNSIGNED,IN p_principal BOOLEAN)
BEGIN
 IF p_principal=1 THEN UPDATE ejercicio_grupo_muscular SET es_principal=0,updated_at=CURRENT_TIMESTAMP WHERE ejercicio_id=p_ejercicio_id; END IF;
 INSERT INTO ejercicio_grupo_muscular(ejercicio_id,grupo_muscular_id,es_principal,created_at,updated_at) VALUES(p_ejercicio_id,p_grupo_id,p_principal,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE es_principal=p_principal,updated_at=CURRENT_TIMESTAMP;
END$$

DROP PROCEDURE IF EXISTS sp_ejercicios_eliminar$$
CREATE PROCEDURE sp_ejercicios_eliminar(IN p_id BIGINT UNSIGNED) BEGIN UPDATE ejercicios SET deleted_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND deleted_at IS NULL; IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Ejercicio no encontrado'; END IF; END$$

DROP PROCEDURE IF EXISTS sp_rutinas_crear$$
CREATE PROCEDURE sp_rutinas_crear(IN p_cliente_id BIGINT UNSIGNED,IN p_entrenador_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_nombre VARCHAR(120),IN p_descripcion TEXT,IN p_inicio DATE,IN p_fin DATE,IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 IF p_fin IS NOT NULL AND p_fin<p_inicio THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Periodo de rutina inválido'; END IF;
 INSERT INTO rutinas(cliente_id,entrenador_id,estado_rutina_id,nombre,descripcion,fecha_inicio,fecha_fin,creada_por,created_at,updated_at) VALUES(p_cliente_id,p_entrenador_id,p_estado_id,p_nombre,p_descripcion,p_inicio,p_fin,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SELECT * FROM rutinas WHERE id=LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_rutinas_actualizar$$
CREATE PROCEDURE sp_rutinas_actualizar(IN p_id BIGINT UNSIGNED,IN p_estado_id BIGINT UNSIGNED,IN p_nombre VARCHAR(120),IN p_descripcion TEXT,IN p_inicio DATE,IN p_fin DATE)
BEGIN UPDATE rutinas SET estado_rutina_id=p_estado_id,nombre=p_nombre,descripcion=p_descripcion,fecha_inicio=p_inicio,fecha_fin=p_fin,updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND deleted_at IS NULL; IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Rutina no encontrada'; END IF; END$$

DROP PROCEDURE IF EXISTS sp_rutinas_asignar_objetivo$$
CREATE PROCEDURE sp_rutinas_asignar_objetivo(IN p_rutina_id BIGINT UNSIGNED,IN p_objetivo_id BIGINT UNSIGNED)
BEGIN INSERT IGNORE INTO objetivo_rutina(rutina_id,objetivo_id,created_at,updated_at) VALUES(p_rutina_id,p_objetivo_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP); END$$

DROP PROCEDURE IF EXISTS sp_rutinas_crear_version$$
CREATE PROCEDURE sp_rutinas_crear_version(IN p_rutina_id BIGINT UNSIGNED,IN p_notas TEXT,IN p_usuario_id BIGINT UNSIGNED)
BEGIN
 DECLARE v_version INT UNSIGNED; DECLARE EXIT HANDLER FOR 1062 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Conflicto al generar la versión; reintente';
 SELECT COALESCE(MAX(numero_version),0)+1 INTO v_version FROM versiones_rutina WHERE rutina_id=p_rutina_id;
 INSERT INTO versiones_rutina(rutina_id,numero_version,notas_cambio,creada_por,created_at,updated_at) VALUES(p_rutina_id,v_version,p_notas,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SELECT * FROM versiones_rutina WHERE id=LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_rutinas_publicar_version$$
CREATE PROCEDURE sp_rutinas_publicar_version(IN p_version_id BIGINT UNSIGNED)
BEGIN
 IF NOT EXISTS(SELECT 1 FROM sesiones_rutina s JOIN ejercicios_rutina e ON e.sesion_rutina_id=s.id WHERE s.version_rutina_id=p_version_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La versión debe contener al menos un ejercicio'; END IF;
 UPDATE versiones_rutina SET publicada_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP WHERE id=p_version_id AND publicada_at IS NULL;
 IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Versión inexistente o ya publicada'; END IF;
END$$

DROP PROCEDURE IF EXISTS sp_rutinas_agregar_sesion$$
CREATE PROCEDURE sp_rutinas_agregar_sesion(IN p_version_id BIGINT UNSIGNED,IN p_numero SMALLINT UNSIGNED,IN p_nombre VARCHAR(100),IN p_dia TINYINT UNSIGNED,IN p_indicaciones TEXT)
BEGIN
 IF EXISTS(SELECT 1 FROM versiones_rutina WHERE id=p_version_id AND publicada_at IS NOT NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No puede modificarse una versión publicada'; END IF;
 INSERT INTO sesiones_rutina(version_rutina_id,numero_sesion,nombre,dia_semana,indicaciones,created_at,updated_at) VALUES(p_version_id,p_numero,p_nombre,p_dia,p_indicaciones,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
END$$

DROP PROCEDURE IF EXISTS sp_rutinas_agregar_ejercicio$$
CREATE PROCEDURE sp_rutinas_agregar_ejercicio(IN p_sesion_id BIGINT UNSIGNED,IN p_ejercicio_id BIGINT UNSIGNED,IN p_orden SMALLINT UNSIGNED,IN p_series SMALLINT UNSIGNED,IN p_rep_min SMALLINT UNSIGNED,IN p_rep_max SMALLINT UNSIGNED,IN p_duracion INT UNSIGNED,IN p_distancia DECIMAL(10,2),IN p_peso DECIMAL(8,2),IN p_descanso SMALLINT UNSIGNED,IN p_rpe DECIMAL(3,1),IN p_rir TINYINT UNSIGNED,IN p_tempo VARCHAR(20),IN p_indicaciones TEXT)
BEGIN
 IF EXISTS(SELECT 1 FROM sesiones_rutina s JOIN versiones_rutina v ON v.id=s.version_rutina_id WHERE s.id=p_sesion_id AND v.publicada_at IS NOT NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No puede modificarse una versión publicada'; END IF;
 INSERT INTO ejercicios_rutina(sesion_rutina_id,ejercicio_id,orden,series,repeticiones_min,repeticiones_max,duracion_segundos,distancia,peso,descanso_segundos,rpe,rir,tempo,indicaciones,created_at,updated_at)
 VALUES(p_sesion_id,p_ejercicio_id,p_orden,p_series,p_rep_min,p_rep_max,p_duracion,p_distancia,p_peso,p_descanso,p_rpe,p_rir,p_tempo,p_indicaciones,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
END$$

DROP PROCEDURE IF EXISTS sp_rutinas_eliminar$$
CREATE PROCEDURE sp_rutinas_eliminar(IN p_id BIGINT UNSIGNED) BEGIN UPDATE rutinas SET deleted_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND deleted_at IS NULL; IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Rutina no encontrada'; END IF; END$$

DROP PROCEDURE IF EXISTS sp_evaluaciones_crear$$
CREATE PROCEDURE sp_evaluaciones_crear(IN p_cliente_id BIGINT UNSIGNED,IN p_evaluador_id BIGINT UNSIGNED,IN p_fecha DATETIME,IN p_metodo VARCHAR(100),IN p_observaciones TEXT,IN p_usuario_id BIGINT UNSIGNED)
BEGIN INSERT INTO evaluaciones_fisicas(cliente_id,evaluador_id,evaluada_at,metodo,observaciones,creada_por,created_at,updated_at) VALUES(p_cliente_id,p_evaluador_id,p_fecha,p_metodo,p_observaciones,p_usuario_id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP); SELECT * FROM evaluaciones_fisicas WHERE id=LAST_INSERT_ID(); END$$

DROP PROCEDURE IF EXISTS sp_evaluaciones_agregar_medida$$
CREATE PROCEDURE sp_evaluaciones_agregar_medida(IN p_evaluacion_id BIGINT UNSIGNED,IN p_tipo_id BIGINT UNSIGNED,IN p_valor DECIMAL(12,4),IN p_instrumento VARCHAR(100),IN p_observaciones TEXT)
BEGIN
 DECLARE v_unidad VARCHAR(20); DECLARE v_min DECIMAL(12,4); DECLARE v_max DECIMAL(12,4);
 SELECT unidad,valor_minimo,valor_maximo INTO v_unidad,v_min,v_max FROM tipos_medida WHERE id=p_tipo_id AND activo=1;
 IF v_unidad IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Tipo de medida inválido'; END IF;
 IF (v_min IS NOT NULL AND p_valor<v_min) OR (v_max IS NOT NULL AND p_valor>v_max) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Valor fuera del rango permitido'; END IF;
 INSERT INTO detalles_evaluacion(evaluacion_fisica_id,tipo_medida_id,valor,unidad_snapshot,instrumento,observaciones,created_at,updated_at) VALUES(p_evaluacion_id,p_tipo_id,p_valor,v_unidad,p_instrumento,p_observaciones,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
END$$

DROP PROCEDURE IF EXISTS sp_evaluaciones_eliminar$$
CREATE PROCEDURE sp_evaluaciones_eliminar(IN p_id BIGINT UNSIGNED) BEGIN UPDATE evaluaciones_fisicas SET deleted_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND deleted_at IS NULL; IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Evaluación no encontrada'; END IF; END$$

DROP PROCEDURE IF EXISTS sp_entrenamientos_iniciar$$
CREATE PROCEDURE sp_entrenamientos_iniciar(IN p_cliente_id BIGINT UNSIGNED,IN p_version_id BIGINT UNSIGNED,IN p_sesion_id BIGINT UNSIGNED,IN p_inicio DATETIME)
BEGIN
 IF p_version_id IS NOT NULL AND NOT EXISTS(SELECT 1 FROM versiones_rutina WHERE id=p_version_id AND publicada_at IS NOT NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='La versión de rutina no está publicada'; END IF;
 INSERT INTO entrenamientos_realizados(cliente_id,version_rutina_id,sesion_rutina_id,iniciado_at,created_at,updated_at) VALUES(p_cliente_id,p_version_id,p_sesion_id,COALESCE(p_inicio,CURRENT_TIMESTAMP),CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
 SELECT * FROM entrenamientos_realizados WHERE id=LAST_INSERT_ID();
END$$

DROP PROCEDURE IF EXISTS sp_entrenamientos_registrar_serie$$
CREATE PROCEDURE sp_entrenamientos_registrar_serie(IN p_entrenamiento_id BIGINT UNSIGNED,IN p_prescripcion_id BIGINT UNSIGNED,IN p_ejercicio_id BIGINT UNSIGNED,IN p_numero SMALLINT UNSIGNED,IN p_repeticiones SMALLINT UNSIGNED,IN p_peso DECIMAL(8,2),IN p_duracion INT UNSIGNED,IN p_distancia DECIMAL(10,2),IN p_rpe DECIMAL(3,1),IN p_notas TEXT)
BEGIN
 IF EXISTS(SELECT 1 FROM entrenamientos_realizados WHERE id=p_entrenamiento_id AND finalizado_at IS NOT NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El entrenamiento ya finalizó'; END IF;
 INSERT INTO series_realizadas(entrenamiento_realizado_id,ejercicio_rutina_id,ejercicio_id,numero_serie,repeticiones,peso,duracion_segundos,distancia,rpe,notas,created_at,updated_at) VALUES(p_entrenamiento_id,p_prescripcion_id,p_ejercicio_id,p_numero,p_repeticiones,p_peso,p_duracion,p_distancia,p_rpe,p_notas,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
END$$

DROP PROCEDURE IF EXISTS sp_entrenamientos_finalizar$$
CREATE PROCEDURE sp_entrenamientos_finalizar(IN p_id BIGINT UNSIGNED,IN p_fin DATETIME,IN p_esfuerzo TINYINT UNSIGNED,IN p_notas TEXT)
BEGIN UPDATE entrenamientos_realizados SET finalizado_at=COALESCE(p_fin,CURRENT_TIMESTAMP),esfuerzo_percibido=p_esfuerzo,notas=p_notas,updated_at=CURRENT_TIMESTAMP WHERE id=p_id AND finalizado_at IS NULL AND COALESCE(p_fin,CURRENT_TIMESTAMP)>iniciado_at; IF ROW_COUNT()=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Entrenamiento inexistente, finalizado o con hora inválida'; END IF; END$$


-- =========================================================
-- REPORTES Y CONSULTAS ESPECIALES
-- =========================================================
DROP PROCEDURE IF EXISTS sp_reporte_membresias_por_vencer$$
CREATE PROCEDURE sp_reporte_membresias_por_vencer(IN p_dias INT UNSIGNED)
BEGIN
 SELECT m.id,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente,c.telefono,c.correo_electronico,tm.nombre plan,m.fecha_fin,DATEDIFF(m.fecha_fin,CURRENT_DATE) dias_restantes
 FROM membresias m JOIN clientes c ON c.id=m.cliente_id JOIN tipos_membresia tm ON tm.id=m.tipo_membresia_id
 WHERE m.deleted_at IS NULL AND m.bloqueo_activa=1 AND m.fecha_fin BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE,INTERVAL COALESCE(p_dias,30) DAY)
 ORDER BY m.fecha_fin;
END$$

DROP PROCEDURE IF EXISTS sp_reporte_membresias_vencidas$$
CREATE PROCEDURE sp_reporte_membresias_vencidas()
BEGIN SELECT m.*,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente FROM membresias m JOIN clientes c ON c.id=m.cliente_id WHERE m.deleted_at IS NULL AND m.fecha_fin<CURRENT_DATE AND m.bloqueo_activa=1 ORDER BY m.fecha_fin; END$$

DROP PROCEDURE IF EXISTS sp_reporte_ingresos$$
CREATE PROCEDURE sp_reporte_ingresos(IN p_desde DATETIME,IN p_hasta DATETIME)
BEGIN
 SELECT DATE(p.pagado_at) fecha,p.moneda,COUNT(*) cantidad,SUM(p.monto) monto_bruto,COALESCE(SUM(r.total_reembolsado),0) reembolsos,SUM(p.monto)-COALESCE(SUM(r.total_reembolsado),0) ingreso_neto
 FROM pagos p LEFT JOIN (SELECT pago_id,SUM(monto) total_reembolsado FROM reembolsos GROUP BY pago_id) r ON r.pago_id=p.id
 WHERE p.pagado_at BETWEEN p_desde AND p_hasta GROUP BY DATE(p.pagado_at),p.moneda ORDER BY fecha;
END$$

DROP PROCEDURE IF EXISTS sp_reporte_cuentas_por_cobrar$$
CREATE PROCEDURE sp_reporte_cuentas_por_cobrar(IN p_cliente_id BIGINT UNSIGNED)
BEGIN
 SELECT c.id,c.numero_cargo,c.cliente_id,cl.numero_socio,CONCAT(cl.nombre,' ',cl.apellido) cliente,c.total,c.moneda,COALESCE(SUM(a.monto_aplicado),0) aplicado,c.total-COALESCE(SUM(a.monto_aplicado),0) saldo,c.fecha_vencimiento
 FROM cargos_cobro c JOIN clientes cl ON cl.id=c.cliente_id LEFT JOIN aplicaciones_pago a ON a.cargo_cobro_id=c.id
 WHERE (p_cliente_id IS NULL OR c.cliente_id=p_cliente_id) GROUP BY c.id,c.numero_cargo,c.cliente_id,cl.numero_socio,cl.nombre,cl.apellido,c.total,c.moneda,c.fecha_vencimiento
 HAVING saldo>0 ORDER BY c.fecha_vencimiento;
END$$

DROP PROCEDURE IF EXISTS sp_reporte_asistencia_diaria$$
CREATE PROCEDURE sp_reporte_asistencia_diaria(IN p_desde DATE,IN p_hasta DATE)
BEGIN SELECT DATE(entrada_at) fecha,COUNT(*) visitas,COUNT(DISTINCT cliente_id) clientes_unicos,AVG(TIMESTAMPDIFF(MINUTE,entrada_at,salida_at)) duracion_promedio_minutos FROM asistencias WHERE DATE(entrada_at) BETWEEN p_desde AND p_hasta GROUP BY DATE(entrada_at) ORDER BY fecha; END$$

DROP PROCEDURE IF EXISTS sp_reporte_clientes_sin_asistencia$$
CREATE PROCEDURE sp_reporte_clientes_sin_asistencia(IN p_dias INT UNSIGNED)
BEGIN
 SELECT c.id,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente,MAX(a.entrada_at) ultima_asistencia
 FROM clientes c JOIN membresias m ON m.cliente_id=c.id AND m.bloqueo_activa=1 AND m.deleted_at IS NULL LEFT JOIN asistencias a ON a.cliente_id=c.id
 WHERE c.deleted_at IS NULL GROUP BY c.id,c.numero_socio,c.nombre,c.apellido HAVING ultima_asistencia IS NULL OR ultima_asistencia<DATE_SUB(CURRENT_TIMESTAMP,INTERVAL COALESCE(p_dias,30) DAY) ORDER BY ultima_asistencia;
END$$

DROP PROCEDURE IF EXISTS sp_reporte_progreso_cliente$$
CREATE PROCEDURE sp_reporte_progreso_cliente(IN p_cliente_id BIGINT UNSIGNED,IN p_tipo_medida_id BIGINT UNSIGNED,IN p_desde DATETIME,IN p_hasta DATETIME)
BEGIN
 SELECT e.evaluada_at,d.valor,d.unidad_snapshot,d.instrumento,e.observaciones FROM evaluaciones_fisicas e JOIN detalles_evaluacion d ON d.evaluacion_fisica_id=e.id
 WHERE e.cliente_id=p_cliente_id AND e.deleted_at IS NULL AND d.tipo_medida_id=p_tipo_medida_id AND (p_desde IS NULL OR e.evaluada_at>=p_desde) AND (p_hasta IS NULL OR e.evaluada_at<=p_hasta) ORDER BY e.evaluada_at;
END$$

DROP PROCEDURE IF EXISTS sp_reporte_entrenamientos_cliente$$
CREATE PROCEDURE sp_reporte_entrenamientos_cliente(IN p_cliente_id BIGINT UNSIGNED,IN p_desde DATETIME,IN p_hasta DATETIME)
BEGIN
 SELECT e.id,e.iniciado_at,e.finalizado_at,e.esfuerzo_percibido,COUNT(s.id) series,SUM(COALESCE(s.peso,0)*COALESCE(s.repeticiones,0)) volumen
 FROM entrenamientos_realizados e LEFT JOIN series_realizadas s ON s.entrenamiento_realizado_id=e.id
 WHERE e.cliente_id=p_cliente_id AND e.iniciado_at BETWEEN p_desde AND p_hasta GROUP BY e.id,e.iniciado_at,e.finalizado_at,e.esfuerzo_percibido ORDER BY e.iniciado_at DESC;
END$$

DROP PROCEDURE IF EXISTS sp_dashboard_resumen$$
CREATE PROCEDURE sp_dashboard_resumen()
BEGIN
 SELECT
 (SELECT COUNT(*) FROM clientes WHERE deleted_at IS NULL) clientes,
 (SELECT COUNT(*) FROM membresias WHERE bloqueo_activa=1 AND deleted_at IS NULL) membresias_activas,
 (SELECT COUNT(*) FROM asistencias WHERE DATE(entrada_at)=CURRENT_DATE) asistencias_hoy,
 (SELECT COUNT(*) FROM asistencias WHERE salida_at IS NULL) personas_dentro,
 (SELECT COALESCE(SUM(monto),0) FROM pagos WHERE DATE(pagado_at)=CURRENT_DATE) ingresos_hoy,
 (SELECT COUNT(*) FROM cargos_cobro c WHERE c.total>(SELECT COALESCE(SUM(a.monto_aplicado),0) FROM aplicaciones_pago a WHERE a.cargo_cobro_id=c.id)) cargos_pendientes;
END$$

DROP PROCEDURE IF EXISTS sp_auditoria_registrar$$
CREATE PROCEDURE sp_auditoria_registrar(IN p_user_id BIGINT UNSIGNED,IN p_evento VARCHAR(40),IN p_entidad VARCHAR(120),IN p_entidad_id VARCHAR(64),IN p_anteriores JSON,IN p_nuevos JSON,IN p_motivo VARCHAR(255),IN p_ip VARCHAR(45),IN p_user_agent TEXT)
BEGIN INSERT INTO auditoria(user_id,evento,entidad,entidad_id,valores_anteriores,valores_nuevos,motivo,ip,user_agent,ocurrido_at) VALUES(p_user_id,p_evento,p_entidad,p_entidad_id,p_anteriores,p_nuevos,p_motivo,p_ip,p_user_agent,CURRENT_TIMESTAMP); END$$

DELIMITER ;
