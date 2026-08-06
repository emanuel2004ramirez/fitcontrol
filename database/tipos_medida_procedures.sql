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