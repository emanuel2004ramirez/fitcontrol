DROP PROCEDURE IF EXISTS sp_metodos_pago_listar;
CREATE PROCEDURE sp_metodos_pago_listar(IN p_buscar VARCHAR(100), IN p_limite INT, IN p_offset INT)
BEGIN
    SELECT id, codigo, nombre, requiere_referencia, activo
    FROM metodos_pago
    WHERE p_buscar = '' OR codigo LIKE CONCAT('%', p_buscar, '%') OR nombre LIKE CONCAT('%', p_buscar, '%')
    ORDER BY id DESC
    LIMIT p_limite OFFSET p_offset;
END;

DROP PROCEDURE IF EXISTS sp_metodos_pago_crear;
CREATE PROCEDURE sp_metodos_pago_crear(IN p_codigo VARCHAR(30), IN p_nombre VARCHAR(50), IN p_requiere_referencia TINYINT, IN p_activo TINYINT)
BEGIN
    INSERT INTO metodos_pago (codigo, nombre, requiere_referencia, activo, created_at, updated_at) 
    VALUES (p_codigo, p_nombre, p_requiere_referencia, p_activo, NOW(), NOW());
END;

DROP PROCEDURE IF EXISTS sp_metodos_pago_actualizar;
CREATE PROCEDURE sp_metodos_pago_actualizar(IN p_id BIGINT, IN p_codigo VARCHAR(30), IN p_nombre VARCHAR(50), IN p_requiere_referencia TINYINT, IN p_activo TINYINT)
BEGIN
    UPDATE metodos_pago SET codigo = p_codigo, nombre = p_nombre, requiere_referencia = p_requiere_referencia, activo = p_activo, updated_at = NOW() WHERE id = p_id;
END;

DROP PROCEDURE IF EXISTS sp_metodos_pago_eliminar;
CREATE PROCEDURE sp_metodos_pago_eliminar(IN p_id BIGINT)
BEGIN
    DELETE FROM metodos_pago WHERE id = p_id;
END;