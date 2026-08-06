DELIMITER //

DROP PROCEDURE IF EXISTS sp_estados_cliente_listar//
CREATE PROCEDURE sp_estados_cliente_listar(
    IN p_buscar VARCHAR(255),
    IN p_limite INT,
    IN p_offset INT
)
BEGIN
    SELECT id, codigo, nombre, activo, es_terminal, orden, created_at, updated_at
    FROM estados_cliente
    WHERE (p_buscar IS NULL OR p_buscar = '' OR codigo LIKE CONCAT('%', p_buscar, '%') OR nombre LIKE CONCAT('%', p_buscar, '%'))
    ORDER BY orden ASC, nombre ASC
    LIMIT p_limite OFFSET p_offset;
END //

DROP PROCEDURE IF EXISTS sp_estados_cliente_crear//
CREATE PROCEDURE sp_estados_cliente_crear(
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_activo TINYINT,
    IN p_es_terminal TINYINT,
    IN p_orden SMALLINT
)
BEGIN
    INSERT INTO estados_cliente (codigo, nombre, activo, es_terminal, orden, created_at, updated_at)
    VALUES (p_codigo, p_nombre, p_activo, p_es_terminal, p_orden, NOW(), NOW());
END //

DROP PROCEDURE IF EXISTS sp_estados_cliente_actualizar//
CREATE PROCEDURE sp_estados_cliente_actualizar(
    IN p_id BIGINT,
    IN p_codigo VARCHAR(30),
    IN p_nombre VARCHAR(50),
    IN p_activo TINYINT,
    IN p_es_terminal TINYINT,
    IN p_orden SMALLINT
)
BEGIN
    UPDATE estados_cliente
    SET codigo = p_codigo,
        nombre = p_nombre,
        activo = p_activo,
        es_terminal = p_es_terminal,
        orden = p_orden,
        updated_at = NOW()
    WHERE id = p_id;
END //

DROP PROCEDURE IF EXISTS sp_estados_cliente_eliminar//
CREATE PROCEDURE sp_estados_cliente_eliminar(
    IN p_id BIGINT
)
BEGIN
    DELETE FROM estados_cliente WHERE id = p_id;
END //

DELIMITER ;
