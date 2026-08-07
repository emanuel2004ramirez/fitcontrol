-- FitControl: procedimientos optimizados del Dashboard.
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_dashboard_indicadores$$
CREATE PROCEDURE sp_dashboard_indicadores()
BEGIN
 SELECT
  (SELECT COUNT(*) FROM clientes WHERE deleted_at IS NULL) clientes_registrados,
  (SELECT COUNT(*) FROM membresias WHERE bloqueo_activa=1 AND deleted_at IS NULL AND CURRENT_DATE BETWEEN fecha_inicio AND fecha_fin) membresias_activas,
  (SELECT COUNT(*) FROM asistencias WHERE entrada_at>=CURRENT_DATE AND entrada_at<CURRENT_DATE+INTERVAL 1 DAY) asistencias_hoy,
  (SELECT COUNT(*) FROM asistencias WHERE salida_at IS NULL) personas_dentro,
  (SELECT COALESCE(SUM(monto),0) FROM pagos WHERE pagado_at>=CURRENT_DATE AND pagado_at<CURRENT_DATE+INTERVAL 1 DAY) ingresos_hoy,
  (SELECT COALESCE(SUM(monto),0) FROM pagos WHERE pagado_at>=DATE_FORMAT(CURRENT_DATE,'%Y-%m-01') AND pagado_at<CURRENT_DATE+INTERVAL 1 DAY) ingresos_mes,
  (SELECT COUNT(*) FROM membresias WHERE bloqueo_activa=1 AND deleted_at IS NULL AND fecha_fin>=CURRENT_DATE AND fecha_fin<CURRENT_DATE+INTERVAL 8 DAY) membresias_por_vencer;
END$$
DROP PROCEDURE IF EXISTS sp_dashboard_asistencias_serie$$
CREATE PROCEDURE sp_dashboard_asistencias_serie(IN p_dias INT UNSIGNED)
BEGIN WITH RECURSIVE fechas AS (SELECT CURRENT_DATE-INTERVAL (LEAST(COALESCE(p_dias,14),90)-1) DAY fecha UNION ALL SELECT fecha+INTERVAL 1 DAY FROM fechas WHERE fecha<CURRENT_DATE) SELECT f.fecha,COUNT(a.id) visitas,COUNT(DISTINCT a.cliente_id) clientes FROM fechas f LEFT JOIN asistencias a ON a.entrada_at>=f.fecha AND a.entrada_at<f.fecha+INTERVAL 1 DAY GROUP BY f.fecha ORDER BY f.fecha;END$$
DROP PROCEDURE IF EXISTS sp_dashboard_ingresos_serie$$
CREATE PROCEDURE sp_dashboard_ingresos_serie(IN p_dias INT UNSIGNED)
BEGIN WITH RECURSIVE fechas AS (SELECT CURRENT_DATE-INTERVAL (LEAST(COALESCE(p_dias,14),90)-1) DAY fecha UNION ALL SELECT fecha+INTERVAL 1 DAY FROM fechas WHERE fecha<CURRENT_DATE) SELECT f.fecha,COALESCE(SUM(p.monto),0) ingresos FROM fechas f LEFT JOIN pagos p ON p.pagado_at>=f.fecha AND p.pagado_at<f.fecha+INTERVAL 1 DAY GROUP BY f.fecha ORDER BY f.fecha;END$$
DROP PROCEDURE IF EXISTS sp_dashboard_membresias_estados$$
DROP PROCEDURE IF EXISTS sp_dashboard_membresias_planes$$
CREATE PROCEDURE sp_dashboard_membresias_planes()
BEGIN
 SELECT t.nombre plan,COUNT(m.id) cantidad
 FROM tipos_membresia t
 LEFT JOIN membresias m ON m.tipo_membresia_id=t.id
  AND m.bloqueo_activa=1 AND m.deleted_at IS NULL
  AND CURRENT_DATE BETWEEN m.fecha_inicio AND m.fecha_fin
 WHERE t.activo=1
 GROUP BY t.id,t.nombre
 HAVING COUNT(m.id)>0
 ORDER BY cantidad DESC,t.nombre;
END$$
DROP PROCEDURE IF EXISTS sp_dashboard_actividad_reciente$$
CREATE PROCEDURE sp_dashboard_actividad_reciente(IN p_limite INT UNSIGNED)
BEGIN SELECT * FROM (
 SELECT 'ASISTENCIA' tipo,a.id referencia_id,CONCAT('Entrada de ',c.nombre,' ',c.apellido) descripcion,a.entrada_at fecha,'bi-person-check' icono FROM asistencias a JOIN clientes c ON c.id=a.cliente_id
 UNION ALL SELECT 'PAGO',p.id,CONCAT('Pago ',p.numero_recibo,' · ',FORMAT(p.monto,2),' ',p.moneda),COALESCE(p.pagado_at,p.created_at),'bi-wallet2' FROM pagos p
 UNION ALL SELECT 'CLIENTE',c.id,CONCAT('Nuevo cliente ',c.nombre,' ',c.apellido),c.created_at,'bi-person-plus' FROM clientes c WHERE c.deleted_at IS NULL
 ) actividad ORDER BY fecha DESC LIMIT p_limite;
END$$
DROP PROCEDURE IF EXISTS sp_dashboard_alertas$$
CREATE PROCEDURE sp_dashboard_alertas(IN p_limite INT UNSIGNED)
BEGIN
 SELECT 'MEMBRESIA' tipo,m.id referencia_id,CONCAT(c.nombre,' ',c.apellido) cliente,CONCAT('Vence el ',DATE_FORMAT(m.fecha_fin,'%d/%m/%Y')) mensaje,m.fecha_fin fecha
 FROM membresias m JOIN clientes c ON c.id=m.cliente_id WHERE m.bloqueo_activa=1 AND m.deleted_at IS NULL AND m.fecha_fin>=CURRENT_DATE AND m.fecha_fin<CURRENT_DATE+INTERVAL 8 DAY
 ORDER BY m.fecha_fin LIMIT p_limite;
END$$
DELIMITER ;
