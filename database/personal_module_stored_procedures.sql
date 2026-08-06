-- FitControl: procedimientos del módulo Personal.
-- Ejecutar con un usuario que tenga privilegio CREATE ROUTINE.
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_personal_filtrar$$
CREATE PROCEDURE sp_personal_filtrar(
    IN p_texto VARCHAR(150), IN p_estado_id BIGINT UNSIGNED,
    IN p_cargo_id BIGINT UNSIGNED, IN p_fecha_desde DATE,
    IN p_fecha_hasta DATE, IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED
)
BEGIN
    IF p_limite IS NULL OR p_limite < 1 OR p_limite > 100 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El límite debe estar entre 1 y 100';
    END IF;

    SELECT p.id, p.codigo_empleado, p.nombre, p.apellido,
           CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo,
           p.cargo_id, c.nombre AS cargo, p.sexo_id, s.nombre AS sexo,
           p.estado_personal_id, ep.codigo AS estado_codigo, ep.nombre AS estado,
           p.tipo_identificacion, p.numero_identificacion, p.telefono,
           p.correo_electronico, p.fecha_contratacion, p.fecha_terminacion,
           p.motivo_terminacion, p.created_at, p.updated_at
      FROM personal p
      JOIN cargos c ON c.id = p.cargo_id
 LEFT JOIN sexos s ON s.id = p.sexo_id
      JOIN estados_personal ep ON ep.id = p.estado_personal_id
     WHERE p.deleted_at IS NULL
       AND (p_texto IS NULL OR p_texto = '' OR p.codigo_empleado LIKE CONCAT('%', p_texto, '%')
            OR p.nombre LIKE CONCAT('%', p_texto, '%') OR p.apellido LIKE CONCAT('%', p_texto, '%')
            OR CONCAT(p.nombre, ' ', p.apellido) LIKE CONCAT('%', p_texto, '%')
            OR p.numero_identificacion LIKE CONCAT('%', p_texto, '%')
            OR p.correo_electronico LIKE CONCAT('%', p_texto, '%'))
       AND (p_estado_id IS NULL OR p.estado_personal_id = p_estado_id)
       AND (p_cargo_id IS NULL OR p.cargo_id = p_cargo_id)
       AND (p_fecha_desde IS NULL OR p.fecha_contratacion >= p_fecha_desde)
       AND (p_fecha_hasta IS NULL OR p.fecha_contratacion <= p_fecha_hasta)
  ORDER BY p.apellido, p.nombre, p.id
     LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_personal_contar$$
CREATE PROCEDURE sp_personal_contar(
    IN p_texto VARCHAR(150), IN p_estado_id BIGINT UNSIGNED,
    IN p_cargo_id BIGINT UNSIGNED, IN p_fecha_desde DATE, IN p_fecha_hasta DATE
)
BEGIN
    SELECT COUNT(*) AS total
      FROM personal p
     WHERE p.deleted_at IS NULL
       AND (p_texto IS NULL OR p_texto = '' OR p.codigo_empleado LIKE CONCAT('%', p_texto, '%')
            OR p.nombre LIKE CONCAT('%', p_texto, '%') OR p.apellido LIKE CONCAT('%', p_texto, '%')
            OR CONCAT(p.nombre, ' ', p.apellido) LIKE CONCAT('%', p_texto, '%')
            OR p.numero_identificacion LIKE CONCAT('%', p_texto, '%')
            OR p.correo_electronico LIKE CONCAT('%', p_texto, '%'))
       AND (p_estado_id IS NULL OR p.estado_personal_id = p_estado_id)
       AND (p_cargo_id IS NULL OR p.cargo_id = p_cargo_id)
       AND (p_fecha_desde IS NULL OR p.fecha_contratacion >= p_fecha_desde)
       AND (p_fecha_hasta IS NULL OR p.fecha_contratacion <= p_fecha_hasta);
END$$

DROP PROCEDURE IF EXISTS sp_personal_obtener$$
CREATE PROCEDURE sp_personal_obtener(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM personal WHERE id = p_id AND deleted_at IS NULL) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El empleado no existe o fue retirado';
    END IF;

    SELECT p.*, CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo,
           c.nombre AS cargo, s.nombre AS sexo, ep.codigo AS estado_codigo,
           ep.nombre AS estado, ep.es_terminal AS estado_terminal
      FROM personal p
      JOIN cargos c ON c.id = p.cargo_id
 LEFT JOIN sexos s ON s.id = p.sexo_id
      JOIN estados_personal ep ON ep.id = p.estado_personal_id
     WHERE p.id = p_id AND p.deleted_at IS NULL;
END$$

DROP PROCEDURE IF EXISTS sp_personal_crear$$
CREATE PROCEDURE sp_personal_crear(
    IN p_codigo VARCHAR(30), IN p_cargo_id BIGINT UNSIGNED, IN p_sexo_id BIGINT UNSIGNED,
    IN p_estado_id BIGINT UNSIGNED, IN p_nombre VARCHAR(100), IN p_apellido VARCHAR(100),
    IN p_tipo_id VARCHAR(30), IN p_numero_id VARCHAR(60), IN p_telefono VARCHAR(25),
    IN p_correo VARCHAR(150), IN p_fecha_contratacion DATE, IN p_usuario_id BIGINT UNSIGNED
)
BEGIN
    DECLARE v_id BIGINT UNSIGNED;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;

    IF EXISTS (SELECT 1 FROM personal WHERE codigo_empleado = p_codigo) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El código de empleado ya está registrado';
    END IF;
    IF p_correo IS NOT NULL AND EXISTS (SELECT 1 FROM personal WHERE correo_electronico = p_correo) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El correo electrónico ya está registrado';
    END IF;
    IF p_numero_id IS NOT NULL AND EXISTS (SELECT 1 FROM personal WHERE tipo_identificacion <=> p_tipo_id AND numero_identificacion = p_numero_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La identificación ya está registrada';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM cargos WHERE id = p_cargo_id AND activo = 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El cargo seleccionado no está disponible';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM estados_personal WHERE id = p_estado_id AND activo = 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El estado seleccionado no está disponible';
    END IF;

    START TRANSACTION;
    INSERT INTO personal (codigo_empleado, cargo_id, sexo_id, estado_personal_id, nombre, apellido,
        tipo_identificacion, numero_identificacion, telefono, correo_electronico, fecha_contratacion,
        created_at, updated_at)
    VALUES (p_codigo, p_cargo_id, p_sexo_id, p_estado_id, p_nombre, p_apellido, p_tipo_id,
        p_numero_id, p_telefono, p_correo, p_fecha_contratacion, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    SET v_id = LAST_INSERT_ID();
    INSERT INTO historial_cargos_personal
        (personal_id, cargo_id, vigente_desde, motivo, registrado_por, created_at, updated_at)
    VALUES (v_id, p_cargo_id, p_fecha_contratacion, 'Cargo inicial', p_usuario_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    INSERT INTO historial_estados_personal
        (personal_id, estado_nuevo_id, motivo, cambiado_por, cambiado_at, created_at, updated_at)
    VALUES (v_id, p_estado_id, 'Alta de empleado', p_usuario_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
    CALL sp_personal_obtener(v_id);
END$$

DROP PROCEDURE IF EXISTS sp_personal_actualizar$$
CREATE PROCEDURE sp_personal_actualizar(
    IN p_id BIGINT UNSIGNED, IN p_sexo_id BIGINT UNSIGNED, IN p_nombre VARCHAR(100),
    IN p_apellido VARCHAR(100), IN p_tipo_id VARCHAR(30), IN p_numero_id VARCHAR(60),
    IN p_telefono VARCHAR(25), IN p_correo VARCHAR(150)
)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM personal WHERE id = p_id AND deleted_at IS NULL) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El empleado no existe o fue retirado';
    END IF;
    IF p_correo IS NOT NULL AND EXISTS (SELECT 1 FROM personal WHERE correo_electronico = p_correo AND id <> p_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El correo electrónico ya está registrado';
    END IF;
    IF p_numero_id IS NOT NULL AND EXISTS (SELECT 1 FROM personal WHERE tipo_identificacion <=> p_tipo_id AND numero_identificacion = p_numero_id AND id <> p_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La identificación ya está registrada';
    END IF;
    UPDATE personal SET sexo_id = p_sexo_id, nombre = p_nombre, apellido = p_apellido,
        tipo_identificacion = p_tipo_id, numero_identificacion = p_numero_id,
        telefono = p_telefono, correo_electronico = p_correo, updated_at = CURRENT_TIMESTAMP
     WHERE id = p_id AND deleted_at IS NULL;
    CALL sp_personal_obtener(p_id);
END$$

DROP PROCEDURE IF EXISTS sp_personal_asignar_cargo$$
CREATE PROCEDURE sp_personal_asignar_cargo(
    IN p_personal_id BIGINT UNSIGNED, IN p_cargo_id BIGINT UNSIGNED,
    IN p_vigente_desde DATE, IN p_motivo VARCHAR(255), IN p_usuario_id BIGINT UNSIGNED
)
BEGIN
    DECLARE v_cargo_actual BIGINT UNSIGNED;
    DECLARE v_inicio_actual DATE;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    START TRANSACTION;
    SELECT p.cargo_id, h.vigente_desde INTO v_cargo_actual, v_inicio_actual
      FROM personal p
 LEFT JOIN historial_cargos_personal h ON h.personal_id = p.id AND h.vigente_hasta IS NULL
     WHERE p.id = p_personal_id AND p.deleted_at IS NULL FOR UPDATE;
    IF v_cargo_actual IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El empleado no existe o fue retirado'; END IF;
    IF v_cargo_actual = p_cargo_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El empleado ya tiene asignado ese cargo'; END IF;
    IF NOT EXISTS (SELECT 1 FROM cargos WHERE id = p_cargo_id AND activo = 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El cargo seleccionado no está disponible';
    END IF;
    IF v_inicio_actual IS NOT NULL AND p_vigente_desde <= v_inicio_actual THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La fecha del nuevo cargo debe ser posterior al inicio del cargo vigente';
    END IF;
    UPDATE historial_cargos_personal SET vigente_hasta = DATE_SUB(p_vigente_desde, INTERVAL 1 DAY),
        updated_at = CURRENT_TIMESTAMP WHERE personal_id = p_personal_id AND vigente_hasta IS NULL;
    INSERT INTO historial_cargos_personal
        (personal_id, cargo_id, vigente_desde, motivo, registrado_por, created_at, updated_at)
    VALUES (p_personal_id, p_cargo_id, p_vigente_desde, p_motivo, p_usuario_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    UPDATE personal SET cargo_id = p_cargo_id, updated_at = CURRENT_TIMESTAMP WHERE id = p_personal_id;
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_horarios_personal_guardar$$
CREATE PROCEDURE sp_horarios_personal_guardar(
    IN p_id BIGINT UNSIGNED, IN p_personal_id BIGINT UNSIGNED, IN p_dia TINYINT UNSIGNED,
    IN p_inicio TIME, IN p_fin TIME, IN p_desde DATE, IN p_hasta DATE
)
BEGIN
    DECLARE v_id BIGINT UNSIGNED;
    IF NOT EXISTS (SELECT 1 FROM personal WHERE id = p_personal_id AND deleted_at IS NULL) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El empleado no existe o fue retirado';
    END IF;
    IF p_dia NOT BETWEEN 1 AND 7 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El día de la semana no es válido'; END IF;
    IF p_fin <= p_inicio THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La hora de salida debe ser posterior a la entrada'; END IF;
    IF p_hasta IS NOT NULL AND p_hasta < p_desde THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La vigencia del horario no es válida'; END IF;
    IF EXISTS (SELECT 1 FROM horarios_personal WHERE personal_id = p_personal_id AND dia_semana = p_dia
        AND id <> COALESCE(p_id, 0) AND hora_inicio < p_fin AND hora_fin > p_inicio
        AND (vigente_hasta IS NULL OR vigente_hasta >= p_desde)
        AND (p_hasta IS NULL OR vigente_desde <= p_hasta)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El horario se superpone con otro vigente';
    END IF;
    IF p_id IS NULL THEN
        INSERT INTO horarios_personal (personal_id, dia_semana, hora_inicio, hora_fin, vigente_desde, vigente_hasta, created_at, updated_at)
        VALUES (p_personal_id, p_dia, p_inicio, p_fin, p_desde, p_hasta, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
        SET v_id = LAST_INSERT_ID();
    ELSE
        UPDATE horarios_personal SET dia_semana = p_dia, hora_inicio = p_inicio, hora_fin = p_fin,
            vigente_desde = p_desde, vigente_hasta = p_hasta, updated_at = CURRENT_TIMESTAMP
         WHERE id = p_id AND personal_id = p_personal_id;
        IF ROW_COUNT() = 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El horario no existe para este empleado'; END IF;
        SET v_id = p_id;
    END IF;
    SELECT * FROM horarios_personal WHERE id = v_id;
END$$

DROP PROCEDURE IF EXISTS sp_personal_historial_cargos$$
CREATE PROCEDURE sp_personal_historial_cargos(IN p_personal_id BIGINT UNSIGNED)
BEGIN
    SELECT h.id, h.cargo_id, c.nombre AS cargo, h.vigente_desde, h.vigente_hasta,
           h.motivo, h.registrado_por, h.created_at
      FROM historial_cargos_personal h JOIN cargos c ON c.id = h.cargo_id
     WHERE h.personal_id = p_personal_id ORDER BY h.vigente_desde DESC, h.id DESC;
END$$

DROP PROCEDURE IF EXISTS sp_personal_cambiar_estado$$
CREATE PROCEDURE sp_personal_cambiar_estado(
    IN p_id BIGINT UNSIGNED, IN p_estado_id BIGINT UNSIGNED,
    IN p_motivo VARCHAR(255), IN p_usuario_id BIGINT UNSIGNED
)
BEGIN
    DECLARE v_anterior BIGINT UNSIGNED;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    START TRANSACTION;
    SELECT estado_personal_id INTO v_anterior FROM personal
     WHERE id = p_id AND deleted_at IS NULL FOR UPDATE;
    IF v_anterior IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El empleado no existe o fue retirado'; END IF;
    IF v_anterior = p_estado_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El empleado ya se encuentra en ese estado'; END IF;
    IF NOT EXISTS (SELECT 1 FROM estados_personal WHERE id = p_estado_id AND activo = 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El estado seleccionado no está disponible';
    END IF;
    UPDATE personal SET estado_personal_id = p_estado_id, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    INSERT INTO historial_estados_personal
        (personal_id, estado_anterior_id, estado_nuevo_id, motivo, cambiado_por, cambiado_at, created_at, updated_at)
    VALUES (p_id, v_anterior, p_estado_id, p_motivo, p_usuario_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_personal_eliminar$$
CREATE PROCEDURE sp_personal_eliminar(IN p_id BIGINT UNSIGNED, IN p_fecha_terminacion DATE, IN p_motivo VARCHAR(255))
BEGIN
    DECLARE v_fecha DATE DEFAULT COALESCE(p_fecha_terminacion, CURRENT_DATE);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    IF NOT EXISTS (SELECT 1 FROM personal WHERE id = p_id AND deleted_at IS NULL) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El empleado no existe o fue retirado';
    END IF;
    IF EXISTS (SELECT 1 FROM personal WHERE id = p_id AND v_fecha < fecha_contratacion) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La terminación no puede ser anterior a la contratación';
    END IF;
    START TRANSACTION;
    UPDATE personal SET fecha_terminacion = v_fecha, motivo_terminacion = p_motivo,
        deleted_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP WHERE id = p_id;
    UPDATE historial_cargos_personal SET vigente_hasta = GREATEST(vigente_desde, v_fecha),
        updated_at = CURRENT_TIMESTAMP WHERE personal_id = p_id AND vigente_hasta IS NULL;
    UPDATE horarios_personal SET vigente_hasta = GREATEST(vigente_desde, v_fecha),
        updated_at = CURRENT_TIMESTAMP WHERE personal_id = p_id AND vigente_hasta IS NULL;
    UPDATE users SET activo = 0, deleted_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP
     WHERE personal_id = p_id AND deleted_at IS NULL;
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_personal_historial_estados$$
CREATE PROCEDURE sp_personal_historial_estados(IN p_personal_id BIGINT UNSIGNED)
BEGIN
    SELECT h.id, ea.nombre AS estado_anterior, en.nombre AS estado_nuevo,
           h.motivo, h.cambiado_por, h.cambiado_at
      FROM historial_estados_personal h
 LEFT JOIN estados_personal ea ON ea.id = h.estado_anterior_id
      JOIN estados_personal en ON en.id = h.estado_nuevo_id
     WHERE h.personal_id = p_personal_id ORDER BY h.cambiado_at DESC, h.id DESC;
END$$

DROP PROCEDURE IF EXISTS sp_personal_horarios$$
CREATE PROCEDURE sp_personal_horarios(IN p_personal_id BIGINT UNSIGNED)
BEGIN
    SELECT id, personal_id, dia_semana,
           ELT(dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo') AS dia,
           hora_inicio, hora_fin, vigente_desde, vigente_hasta, created_at, updated_at
      FROM horarios_personal WHERE personal_id = p_personal_id
  ORDER BY (vigente_hasta IS NULL) DESC, dia_semana, hora_inicio, vigente_desde DESC;
END$$

DROP PROCEDURE IF EXISTS sp_horarios_personal_eliminar$$
CREATE PROCEDURE sp_horarios_personal_eliminar(IN p_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM horarios_personal WHERE id = p_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El horario no existe';
    END IF;
    UPDATE horarios_personal
       SET vigente_hasta = CASE WHEN vigente_desde >= CURRENT_DATE THEN vigente_desde ELSE CURRENT_DATE END,
           updated_at = CURRENT_TIMESTAMP
     WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_personal_evaluacion_desempeno_crear$$
CREATE PROCEDURE sp_personal_evaluacion_desempeno_crear(
    IN p_personal_id BIGINT UNSIGNED, IN p_periodo_inicio DATE, IN p_periodo_fin DATE,
    IN p_fecha_evaluacion DATE, IN p_puntualidad TINYINT UNSIGNED,
    IN p_responsabilidad TINYINT UNSIGNED, IN p_atencion_cliente TINYINT UNSIGNED,
    IN p_trabajo_equipo TINYINT UNSIGNED, IN p_rendimiento TINYINT UNSIGNED,
    IN p_comentarios TEXT, IN p_evaluador_id BIGINT UNSIGNED
)
BEGIN
    DECLARE v_id BIGINT UNSIGNED;
    DECLARE v_promedio DECIMAL(4,2);

    IF NOT EXISTS (SELECT 1 FROM personal WHERE id = p_personal_id AND deleted_at IS NULL) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El empleado no existe o fue retirado';
    END IF;
    IF p_periodo_fin < p_periodo_inicio THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El periodo evaluado no es valido';
    END IF;
    IF p_fecha_evaluacion > CURRENT_DATE THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La fecha de evaluacion no puede ser futura';
    END IF;
    IF p_puntualidad NOT BETWEEN 1 AND 5 OR p_responsabilidad NOT BETWEEN 1 AND 5
        OR p_atencion_cliente NOT BETWEEN 1 AND 5 OR p_trabajo_equipo NOT BETWEEN 1 AND 5
        OR p_rendimiento NOT BETWEEN 1 AND 5 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Las calificaciones deben estar entre 1 y 5';
    END IF;

    SET v_promedio = ROUND((p_puntualidad + p_responsabilidad + p_atencion_cliente + p_trabajo_equipo + p_rendimiento) / 5, 2);

    INSERT INTO evaluaciones_desempeno_personal
        (personal_id, evaluador_id, periodo_inicio, periodo_fin, fecha_evaluacion,
         puntualidad, responsabilidad, atencion_cliente, trabajo_equipo, rendimiento,
         promedio, estado, comentarios, created_at, updated_at)
    VALUES
        (p_personal_id, p_evaluador_id, p_periodo_inicio, p_periodo_fin, p_fecha_evaluacion,
         p_puntualidad, p_responsabilidad, p_atencion_cliente, p_trabajo_equipo, p_rendimiento,
         v_promedio, 'pendiente', p_comentarios, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

    SET v_id = LAST_INSERT_ID();
    SELECT e.id, e.personal_id, e.evaluador_id, evaluador.name AS evaluador,
           e.aprobado_por, aprobador.name AS aprobador, e.periodo_inicio, e.periodo_fin,
           e.fecha_evaluacion, e.puntualidad, e.responsabilidad, e.atencion_cliente,
           e.trabajo_equipo, e.rendimiento, e.promedio, e.estado, e.comentarios,
           e.aprobado_at, e.created_at, e.updated_at
      FROM evaluaciones_desempeno_personal e
 LEFT JOIN users evaluador ON evaluador.id = e.evaluador_id
 LEFT JOIN users aprobador ON aprobador.id = e.aprobado_por
     WHERE e.id = v_id;
END$$

DROP PROCEDURE IF EXISTS sp_personal_evaluacion_desempeno_aprobar$$
CREATE PROCEDURE sp_personal_evaluacion_desempeno_aprobar(
    IN p_personal_id BIGINT UNSIGNED, IN p_evaluacion_id BIGINT UNSIGNED, IN p_usuario_id BIGINT UNSIGNED
)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM evaluaciones_desempeno_personal WHERE id = p_evaluacion_id AND personal_id = p_personal_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La evaluacion no existe para este empleado';
    END IF;

    UPDATE evaluaciones_desempeno_personal
       SET estado = 'aprobada', aprobado_por = p_usuario_id, aprobado_at = CURRENT_TIMESTAMP,
           updated_at = CURRENT_TIMESTAMP
     WHERE id = p_evaluacion_id AND personal_id = p_personal_id;
END$$

DROP PROCEDURE IF EXISTS sp_personal_evaluaciones_desempeno$$
CREATE PROCEDURE sp_personal_evaluaciones_desempeno(IN p_personal_id BIGINT UNSIGNED)
BEGIN
    SELECT e.id, e.personal_id, e.evaluador_id, evaluador.name AS evaluador,
           e.aprobado_por, aprobador.name AS aprobador, e.periodo_inicio, e.periodo_fin,
           e.fecha_evaluacion, e.puntualidad, e.responsabilidad, e.atencion_cliente,
           e.trabajo_equipo, e.rendimiento, e.promedio, e.estado, e.comentarios,
           e.aprobado_at, e.created_at, e.updated_at
      FROM evaluaciones_desempeno_personal e
 LEFT JOIN users evaluador ON evaluador.id = e.evaluador_id
 LEFT JOIN users aprobador ON aprobador.id = e.aprobado_por
     WHERE e.personal_id = p_personal_id
  ORDER BY e.fecha_evaluacion DESC, e.id DESC;
END$$

DELIMITER ;
