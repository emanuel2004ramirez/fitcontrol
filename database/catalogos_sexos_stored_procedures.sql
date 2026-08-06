DELIMITER //

DROP PROCEDURE IF EXISTS sp_sexos_listar //
CREATE PROCEDURE sp_sexos_listar()
BEGIN
    SELECT id, nombre, descripcion 
    FROM catalogos_sexos 
    WHERE deleted_at IS NULL;
END //

DROP PROCEDURE IF EXISTS sp_sexos_crear //
CREATE PROCEDURE sp_sexos_crear(IN p_nombre VARCHAR(255), IN p_descripcion VARCHAR(255))
BEGIN
    IF EXISTS (SELECT 1 FROM catalogos_sexos WHERE nombre = p_nombre) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ya existe un sexo con ese nombre.';
    END IF;

    INSERT INTO catalogos_sexos (nombre, descripcion, created_at, updated_at) 
    VALUES (p_nombre, p_descripcion, NOW(), NOW());
END //

DROP PROCEDURE IF EXISTS sp_sexos_actualizar //
CREATE PROCEDURE sp_sexos_actualizar(IN p_id BIGINT, IN p_nombre VARCHAR(255), IN p_descripcion VARCHAR(255))
BEGIN
    IF EXISTS (SELECT 1 FROM catalogos_sexos WHERE nombre = p_nombre AND id != p_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ya existe otro sexo con ese nombre.';
    END IF;

    UPDATE catalogos_sexos 
    SET nombre = p_nombre, 
        descripcion = p_descripcion, 
        updated_at = NOW() 
    WHERE id = p_id;
END //

DROP PROCEDURE IF EXISTS sp_sexos_eliminar //
CREATE PROCEDURE sp_sexos_eliminar(IN p_id BIGINT)
BEGIN
    UPDATE catalogos_sexos 
    SET deleted_at = NOW(), 
        updated_at = NOW() 
    WHERE id = p_id;
END //

DELIMITER ;
