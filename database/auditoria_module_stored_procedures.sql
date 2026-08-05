DELIMITER $$

DROP PROCEDURE IF EXISTS sp_auditoria_registrar$$
CREATE PROCEDURE sp_auditoria_registrar(
    IN p_user_id BIGINT UNSIGNED,
    IN p_evento VARCHAR(40),
    IN p_entidad VARCHAR(120),
    IN p_entidad_id VARCHAR(64),
    IN p_anteriores JSON,
    IN p_nuevos JSON,
    IN p_motivo VARCHAR(255),
    IN p_ip VARCHAR(45),
    IN p_user_agent TEXT
)
SQL SECURITY INVOKER
BEGIN
    IF p_evento IS NULL OR TRIM(p_evento) = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El evento de auditoría es obligatorio';
    END IF;
    IF p_entidad IS NULL OR TRIM(p_entidad) = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La entidad de auditoría es obligatoria';
    END IF;
    IF p_user_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM users WHERE id = p_user_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El usuario de auditoría no existe';
    END IF;

    INSERT INTO auditoria (
        user_id, evento, entidad, entidad_id, valores_anteriores,
        valores_nuevos, motivo, ip, user_agent, ocurrido_at
    ) VALUES (
        p_user_id, UPPER(TRIM(p_evento)), TRIM(p_entidad), NULLIF(TRIM(p_entidad_id), ''),
        p_anteriores, p_nuevos, NULLIF(TRIM(p_motivo), ''), p_ip, p_user_agent, CURRENT_TIMESTAMP
    );

END$$

DROP PROCEDURE IF EXISTS sp_auditoria_contar$$
CREATE PROCEDURE sp_auditoria_contar(
    IN p_texto VARCHAR(255), IN p_evento VARCHAR(40), IN p_entidad VARCHAR(120),
    IN p_user_id BIGINT UNSIGNED, IN p_desde DATE, IN p_hasta DATE
)
READS SQL DATA
BEGIN
    SELECT COUNT(*) AS total
    FROM auditoria a
    LEFT JOIN users u ON u.id = a.user_id
    WHERE (p_texto IS NULL OR p_texto = '' OR a.entidad_id LIKE CONCAT('%', p_texto, '%')
        OR a.motivo LIKE CONCAT('%', p_texto, '%') OR u.name LIKE CONCAT('%', p_texto, '%'))
      AND (p_evento IS NULL OR p_evento = '' OR a.evento = p_evento)
      AND (p_entidad IS NULL OR p_entidad = '' OR a.entidad = p_entidad)
      AND (p_user_id IS NULL OR a.user_id = p_user_id)
      AND (p_desde IS NULL OR a.ocurrido_at >= p_desde)
      AND (p_hasta IS NULL OR a.ocurrido_at < DATE_ADD(p_hasta, INTERVAL 1 DAY));
END$$

DROP PROCEDURE IF EXISTS sp_auditoria_filtrar$$
CREATE PROCEDURE sp_auditoria_filtrar(
    IN p_texto VARCHAR(255), IN p_evento VARCHAR(40), IN p_entidad VARCHAR(120),
    IN p_user_id BIGINT UNSIGNED, IN p_desde DATE, IN p_hasta DATE,
    IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED
)
READS SQL DATA
BEGIN
    IF p_limite IS NULL OR p_limite < 1 OR p_limite > 200 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El límite debe estar entre 1 y 200';
    END IF;

    SELECT a.id, a.user_id, COALESCE(u.name, 'Sistema') AS usuario,
           a.evento, a.entidad, a.entidad_id, a.valores_anteriores,
           a.valores_nuevos, a.motivo, a.ip, a.user_agent, a.ocurrido_at
    FROM auditoria a
    LEFT JOIN users u ON u.id = a.user_id
    WHERE (p_texto IS NULL OR p_texto = '' OR a.entidad_id LIKE CONCAT('%', p_texto, '%')
        OR a.motivo LIKE CONCAT('%', p_texto, '%') OR u.name LIKE CONCAT('%', p_texto, '%'))
      AND (p_evento IS NULL OR p_evento = '' OR a.evento = p_evento)
      AND (p_entidad IS NULL OR p_entidad = '' OR a.entidad = p_entidad)
      AND (p_user_id IS NULL OR a.user_id = p_user_id)
      AND (p_desde IS NULL OR a.ocurrido_at >= p_desde)
      AND (p_hasta IS NULL OR a.ocurrido_at < DATE_ADD(p_hasta, INTERVAL 1 DAY))
    ORDER BY a.ocurrido_at DESC, a.id DESC
    LIMIT p_limite OFFSET p_offset;
END$$

DROP PROCEDURE IF EXISTS sp_auditoria_obtener$$
CREATE PROCEDURE sp_auditoria_obtener(IN p_id BIGINT UNSIGNED)
READS SQL DATA
BEGIN
    IF NOT EXISTS (SELECT 1 FROM auditoria WHERE id = p_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro de auditoría no encontrado';
    END IF;

    SELECT a.*, COALESCE(u.name, 'Sistema') AS usuario, u.email AS usuario_email
    FROM auditoria a
    LEFT JOIN users u ON u.id = a.user_id
    WHERE a.id = p_id;
END$$

DROP PROCEDURE IF EXISTS sp_auditoria_catalogos$$
CREATE PROCEDURE sp_auditoria_catalogos()
READS SQL DATA
BEGIN
    SELECT DISTINCT evento FROM auditoria ORDER BY evento;
    SELECT DISTINCT entidad FROM auditoria ORDER BY entidad;
END$$

DELIMITER ;
