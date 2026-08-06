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