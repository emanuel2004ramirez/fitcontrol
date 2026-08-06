DELIMITER //

DROP PROCEDURE IF EXISTS sp_sexos_listar //
CREATE PROCEDURE sp_sexos_listar(
    IN p_buscar VARCHAR(255),
    IN p_limite INT,
    IN p_offset INT
)
BEGIN
    SELECT id, codigo, nombre, activo
    FROM sexos
    WHERE (p_buscar = '' OR codigo LIKE CONCAT('%', p_buscar, '%') OR nombre LIKE CONCAT('%', p_buscar, '%'))
    ORDER BY id ASC
    LIMIT p_limite OFFSET p_offset;
END //

DROP PROCEDURE IF EXISTS sp_sexos_crear //
CREATE PROCEDURE sp_sexos_crear(
    IN p_codigo VARCHAR(255), 
    IN p_nombre VARCHAR(255),
    IN p_activo TINYINT
)
BEGIN
    INSERT INTO sexos (codigo, nombre, activo, created_at, updated_at) 
    VALUES (p_codigo, p_nombre, p_activo, NOW(), NOW());
END //

DROP PROCEDURE IF EXISTS sp_sexos_actualizar //
CREATE PROCEDURE sp_sexos_actualizar(
    IN p_id BIGINT, 
    IN p_codigo VARCHAR(255), 
    IN p_nombre VARCHAR(255),
    IN p_activo TINYINT
)
BEGIN
    UPDATE sexos 
    SET codigo = p_codigo, 
        nombre = p_nombre, 
        activo = p_activo,
        updated_at = NOW() 
    WHERE id = p_id;
END //

DROP PROCEDURE IF EXISTS sp_sexos_eliminar //
CREATE PROCEDURE sp_sexos_eliminar(IN p_id BIGINT)
BEGIN
    DELETE FROM sexos WHERE id = p_id;
END //

DELIMITER ;
