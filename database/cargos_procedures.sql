DROP PROCEDURE IF EXISTS sp_cargos_listar;
CREATE PROCEDURE sp_cargos_listar(IN p_buscar VARCHAR(100), IN p_limite INT, IN p_offset INT)
BEGIN
    SELECT id, codigo, nombre, descripcion, activo
    FROM cargos
    WHERE p_buscar = '' OR codigo LIKE CONCAT('%', p_buscar, '%') OR nombre LIKE CONCAT('%', p_buscar, '%')
    ORDER BY id DESC
    LIMIT p_limite OFFSET p_offset;
END;

DROP PROCEDURE IF EXISTS sp_cargos_crear;
CREATE PROCEDURE sp_cargos_crear(IN p_codigo VARCHAR(30), IN p_nombre VARCHAR(50), IN p_descripcion TEXT, IN p_activo TINYINT)
BEGIN
    INSERT INTO cargos (codigo, nombre, descripcion, activo, created_at, updated_at) 
    VALUES (p_codigo, p_nombre, p_descripcion, p_activo, NOW(), NOW());
END;

DROP PROCEDURE IF EXISTS sp_cargos_actualizar;
CREATE PROCEDURE sp_cargos_actualizar(IN p_id BIGINT, IN p_codigo VARCHAR(30), IN p_nombre VARCHAR(50), IN p_descripcion TEXT, IN p_activo TINYINT)
BEGIN
    UPDATE cargos 
    SET codigo = p_codigo, 
        nombre = p_nombre, 
        descripcion = p_descripcion, 
        activo = p_activo, 
        updated_at = NOW() 
    WHERE id = p_id;
END;

DROP PROCEDURE IF EXISTS sp_cargos_eliminar;
CREATE PROCEDURE sp_cargos_eliminar(IN p_id BIGINT)
BEGIN
    DELETE FROM cargos WHERE id = p_id;
END;