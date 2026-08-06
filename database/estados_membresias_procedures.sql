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