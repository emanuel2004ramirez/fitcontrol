DELIMITER $$

DROP PROCEDURE IF EXISTS sp_auth_user_by_id$$
CREATE PROCEDURE sp_auth_user_by_id(IN p_id BIGINT UNSIGNED)
READS SQL DATA
BEGIN
    SELECT id, personal_id, name, username, email, password, activo,
           debe_cambiar_password, intentos_fallidos, bloqueado_hasta,
           password_changed_at, ultimo_acceso_at, deleted_at
    FROM users WHERE id = p_id AND deleted_at IS NULL LIMIT 1;
END$$

DROP PROCEDURE IF EXISTS sp_auth_login_exitoso$$
CREATE PROCEDURE sp_auth_login_exitoso(IN p_user_id BIGINT UNSIGNED)
BEGIN
    UPDATE users SET intentos_fallidos = 0, bloqueado_hasta = NULL,
        ultimo_acceso_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP
    WHERE id = p_user_id AND activo = 1 AND deleted_at IS NULL;
    IF ROW_COUNT() = 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario inactivo o inexistente'; END IF;
END$$

DROP PROCEDURE IF EXISTS sp_auth_login_fallido$$
CREATE PROCEDURE sp_auth_login_fallido(IN p_login VARCHAR(150))
BEGIN
    UPDATE users
    SET intentos_fallidos = intentos_fallidos + 1,
        bloqueado_hasta = CASE WHEN intentos_fallidos + 1 >= 5 THEN DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 15 MINUTE) ELSE bloqueado_hasta END,
        updated_at = CURRENT_TIMESTAMP
    WHERE deleted_at IS NULL AND (username = LOWER(TRIM(p_login)) OR email = LOWER(TRIM(p_login)));
END$$

DROP PROCEDURE IF EXISTS sp_auth_user_roles$$
CREATE PROCEDURE sp_auth_user_roles(IN p_user_id BIGINT UNSIGNED)
READS SQL DATA
BEGIN
    SELECT r.id, r.codigo, r.nombre
    FROM roles r JOIN role_user ru ON ru.role_id = r.id
    WHERE ru.user_id = p_user_id AND r.activo = 1 ORDER BY r.nombre;
END$$

DROP PROCEDURE IF EXISTS sp_auth_user_permissions$$
CREATE PROCEDURE sp_auth_user_permissions(IN p_user_id BIGINT UNSIGNED)
READS SQL DATA
BEGIN
    SELECT DISTINCT p.codigo
    FROM permisos p
    JOIN permiso_rol pr ON pr.permiso_id = p.id
    JOIN role_user ru ON ru.role_id = pr.role_id
    JOIN roles r ON r.id = ru.role_id AND r.activo = 1
    WHERE ru.user_id = p_user_id
    UNION
    SELECT DISTINCT p.codigo
    FROM permisos p JOIN model_has_permissions mp ON mp.permiso_id = p.id
    WHERE mp.user_id = p_user_id;
END$$

DROP PROCEDURE IF EXISTS sp_auth_user_profile$$
CREATE PROCEDURE sp_auth_user_profile(IN p_user_id BIGINT UNSIGNED)
READS SQL DATA
BEGIN
    SELECT u.id, u.name, u.username, u.email, u.personal_id,
           pe.codigo_empleado, CONCAT(pe.nombre, ' ', pe.apellido) AS empleado,
           c.codigo AS cargo_codigo, c.nombre AS cargo
    FROM users u
    LEFT JOIN personal pe ON pe.id = u.personal_id AND pe.deleted_at IS NULL
    LEFT JOIN cargos c ON c.id = pe.cargo_id
    WHERE u.id = p_user_id AND u.deleted_at IS NULL
    LIMIT 1;
END$$

DROP PROCEDURE IF EXISTS sp_auth_upsert_role$$
CREATE PROCEDURE sp_auth_upsert_role(IN p_codigo VARCHAR(50), IN p_nombre VARCHAR(100), IN p_descripcion TEXT)
BEGIN
    INSERT INTO roles(codigo, nombre, name, guard_name, descripcion, activo, created_at, updated_at)
    VALUES(p_codigo, p_nombre, p_codigo, 'web', p_descripcion, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
    ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), name = VALUES(name), guard_name = 'web',
        descripcion = VALUES(descripcion), activo = 1, updated_at = CURRENT_TIMESTAMP;
    SELECT id, codigo, nombre FROM roles WHERE codigo = p_codigo LIMIT 1;
END$$

DROP PROCEDURE IF EXISTS sp_auth_upsert_permission$$
CREATE PROCEDURE sp_auth_upsert_permission(IN p_codigo VARCHAR(100), IN p_nombre VARCHAR(120), IN p_modulo VARCHAR(60), IN p_descripcion TEXT)
BEGIN
    INSERT INTO permisos(codigo, nombre, name, guard_name, modulo, descripcion, created_at, updated_at)
    VALUES(p_codigo, p_nombre, p_codigo, 'web', p_modulo, p_descripcion, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
    ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), name = VALUES(name), guard_name = 'web',
        modulo = VALUES(modulo), descripcion = VALUES(descripcion), updated_at = CURRENT_TIMESTAMP;
    SELECT id, codigo FROM permisos WHERE codigo = p_codigo LIMIT 1;
END$$

DROP PROCEDURE IF EXISTS sp_users_vincular_personal$$
CREATE PROCEDURE sp_users_vincular_personal(IN p_user_id BIGINT UNSIGNED, IN p_personal_id BIGINT UNSIGNED)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM users WHERE id = p_user_id AND deleted_at IS NULL) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario no encontrado';
    END IF;
    IF p_personal_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM personal WHERE id = p_personal_id AND deleted_at IS NULL) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Empleado no encontrado';
    END IF;
    IF p_personal_id IS NOT NULL AND EXISTS (SELECT 1 FROM users WHERE id <> p_user_id AND personal_id = p_personal_id AND deleted_at IS NULL) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El empleado ya tiene usuario';
    END IF;
    UPDATE users SET personal_id = p_personal_id, updated_at = CURRENT_TIMESTAMP WHERE id = p_user_id;
END$$

DELIMITER ;
