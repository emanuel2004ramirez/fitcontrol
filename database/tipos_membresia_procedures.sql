DROP PROCEDURE IF EXISTS sp_tipos_membresia_listar;
CREATE PROCEDURE sp_tipos_membresia_listar(IN p_buscar VARCHAR(100), IN p_limite INT, IN p_offset INT)
BEGIN
    SELECT 
        tm.id, tm.codigo, tm.nombre, tm.descripcion, tm.duracion_dias, tm.activo,
        pm.precio, pm.moneda
    FROM tipos_membresia tm
    -- Traemos solo el precio actual (el que no tiene fecha de vencimiento)
    LEFT JOIN precios_membresia pm ON tm.id = pm.tipo_membresia_id AND pm.vigente_hasta IS NULL
    WHERE p_buscar = '' 
       OR tm.codigo LIKE CONCAT('%', p_buscar, '%') 
       OR tm.nombre LIKE CONCAT('%', p_buscar, '%')
    ORDER BY tm.id DESC
    LIMIT p_limite OFFSET p_offset;
END;

DROP PROCEDURE IF EXISTS sp_tipos_membresia_crear;
CREATE PROCEDURE sp_tipos_membresia_crear(
    IN p_codigo VARCHAR(30), IN p_nombre VARCHAR(50), IN p_descripcion TEXT, 
    IN p_duracion_dias INT, IN p_activo TINYINT,
    IN p_precio DECIMAL(10,2), IN p_moneda VARCHAR(10)
)
BEGIN
    DECLARE v_tipo_id BIGINT;
    
    -- 1. Insertamos el tipo de membresía
    INSERT INTO tipos_membresia (codigo, nombre, descripcion, duracion_dias, activo, created_at, updated_at) 
    VALUES (p_codigo, p_nombre, p_descripcion, p_duracion_dias, p_activo, NOW(), NOW());
    
    SET v_tipo_id = LAST_INSERT_ID();
    
    -- 2. Insertamos el precio inicial
    INSERT INTO precios_membresia (tipo_membresia_id, precio, moneda, vigente_desde, motivo_cambio, created_at, updated_at)
    VALUES (v_tipo_id, p_precio, p_moneda, CURDATE(), 'Precio inicial', NOW(), NOW());
END;

DROP PROCEDURE IF EXISTS sp_tipos_membresia_actualizar;
CREATE PROCEDURE sp_tipos_membresia_actualizar(
    IN p_id BIGINT, IN p_codigo VARCHAR(30), IN p_nombre VARCHAR(50), 
    IN p_descripcion TEXT, IN p_duracion_dias INT, IN p_activo TINYINT,
    IN p_precio DECIMAL(10,2), IN p_moneda VARCHAR(10)
)
BEGIN
    DECLARE v_precio_actual DECIMAL(10,2);
    DECLARE v_moneda_actual VARCHAR(10);
    
    -- 1. Actualizar los datos base
    UPDATE tipos_membresia 
    SET codigo = p_codigo, nombre = p_nombre, descripcion = p_descripcion, 
        duracion_dias = p_duracion_dias, activo = p_activo, updated_at = NOW() 
    WHERE id = p_id;
    
    -- 2. Obtener el precio actual
    SELECT precio, moneda INTO v_precio_actual, v_moneda_actual
    FROM precios_membresia 
    WHERE tipo_membresia_id = p_id AND vigente_hasta IS NULL 
    ORDER BY id DESC LIMIT 1;
    
    -- 3. Si cambiaron el precio en el formulario, guardamos el historial
    IF v_precio_actual IS NULL OR v_precio_actual != p_precio OR v_moneda_actual != p_moneda THEN
        -- Caducar precio anterior
        UPDATE precios_membresia 
        SET vigente_hasta = CURDATE(), updated_at = NOW() 
        WHERE tipo_membresia_id = p_id AND vigente_hasta IS NULL;
        
        -- Insertar nuevo precio
        INSERT INTO precios_membresia (tipo_membresia_id, precio, moneda, vigente_desde, motivo_cambio, created_at, updated_at)
        VALUES (p_id, p_precio, p_moneda, CURDATE(), 'Actualización de precio', NOW(), NOW());
    END IF;
END;

DROP PROCEDURE IF EXISTS sp_tipos_membresia_eliminar;
CREATE PROCEDURE sp_tipos_membresia_eliminar(IN p_id BIGINT)
BEGIN
    -- Borramos en cascada manual: primero los precios, luego la membresía
    DELETE FROM precios_membresia WHERE tipo_membresia_id = p_id;
    DELETE FROM tipos_membresia WHERE id = p_id;
END;