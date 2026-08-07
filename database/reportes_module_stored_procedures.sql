-- FitControl: reportes gerenciales y listados administrativos paginados.
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_reportes_generar$$
CREATE PROCEDURE sp_reportes_generar(
 IN p_reporte VARCHAR(30), IN p_desde DATE, IN p_hasta DATE,
 IN p_busqueda VARCHAR(150), IN p_estado_id BIGINT UNSIGNED,
 IN p_limite INT UNSIGNED, IN p_offset INT UNSIGNED
)
BEGIN
 SET p_limite=LEAST(GREATEST(COALESCE(p_limite,25),1),10000);
 SET p_offset=GREATEST(COALESCE(p_offset,0),0);
 IF p_reporte='clientes' THEN
  SELECT c.numero_socio,c.nombre,c.apellido,e.nombre estado,c.telefono,c.correo_electronico,c.fecha_registro
  FROM clientes c JOIN estados_cliente e ON e.id=c.estado_cliente_id
  WHERE c.deleted_at IS NULL AND (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%'))
   AND (p_estado_id IS NULL OR c.estado_cliente_id=p_estado_id) AND (p_desde IS NULL OR c.fecha_registro>=p_desde) AND (p_hasta IS NULL OR c.fecha_registro<=p_hasta)
  ORDER BY c.apellido,c.nombre LIMIT p_limite OFFSET p_offset;
 ELSEIF p_reporte='personal' THEN
  SELECT p.codigo_empleado,p.nombre,p.apellido,c.nombre cargo,e.nombre estado,p.telefono,p.correo_electronico,p.fecha_contratacion
  FROM personal p JOIN cargos c ON c.id=p.cargo_id JOIN estados_personal e ON e.id=p.estado_personal_id
  WHERE p.deleted_at IS NULL AND (p_busqueda IS NULL OR p_busqueda='' OR p.codigo_empleado LIKE CONCAT('%',p_busqueda,'%') OR p.nombre LIKE CONCAT('%',p_busqueda,'%') OR p.apellido LIKE CONCAT('%',p_busqueda,'%'))
   AND (p_estado_id IS NULL OR p.estado_personal_id=p_estado_id) AND (p_desde IS NULL OR p.fecha_contratacion>=p_desde) AND (p_hasta IS NULL OR p.fecha_contratacion<=p_hasta)
  ORDER BY p.apellido,p.nombre LIMIT p_limite OFFSET p_offset;
 ELSEIF p_reporte='pagos' THEN
  SELECT p.numero_recibo,CONCAT(c.nombre,' ',c.apellido) cliente,m.nombre metodo,e.nombre estado,p.monto,p.moneda,p.referencia,p.pagado_at
  FROM pagos p JOIN clientes c ON c.id=p.cliente_id JOIN metodos_pago m ON m.id=p.metodo_pago_id JOIN estados_pago e ON e.id=p.estado_pago_id
  WHERE (p_busqueda IS NULL OR p_busqueda='' OR p.numero_recibo LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%'))
   AND (p_estado_id IS NULL OR p.estado_pago_id=p_estado_id) AND (p_desde IS NULL OR p.pagado_at>=p_desde) AND (p_hasta IS NULL OR p.pagado_at<p_hasta+INTERVAL 1 DAY)
  ORDER BY p.pagado_at DESC LIMIT p_limite OFFSET p_offset;
 ELSEIF p_reporte='asistencias' THEN
  SELECT c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente,a.entrada_at,a.salida_at,TIMESTAMPDIFF(MINUTE,a.entrada_at,COALESCE(a.salida_at,CURRENT_TIMESTAMP)) duracion_minutos,a.metodo_registro
  FROM asistencias a JOIN clientes c ON c.id=a.cliente_id
  WHERE (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%'))
   AND (p_desde IS NULL OR a.entrada_at>=p_desde) AND (p_hasta IS NULL OR a.entrada_at<p_hasta+INTERVAL 1 DAY)
  ORDER BY a.entrada_at DESC LIMIT p_limite OFFSET p_offset;
 ELSEIF p_reporte='membresias' THEN
  SELECT c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente,t.nombre tipo,e.nombre estado,m.fecha_inicio,m.fecha_fin,m.precio_contratado,m.moneda,m.origen
  FROM membresias m JOIN clientes c ON c.id=m.cliente_id JOIN tipos_membresia t ON t.id=m.tipo_membresia_id JOIN estados_membresia e ON e.id=m.estado_membresia_id
  WHERE m.deleted_at IS NULL AND (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%'))
   AND (p_estado_id IS NULL OR m.estado_membresia_id=p_estado_id) AND (p_desde IS NULL OR m.fecha_fin>=p_desde) AND (p_hasta IS NULL OR m.fecha_inicio<=p_hasta)
  ORDER BY m.fecha_inicio DESC LIMIT p_limite OFFSET p_offset;
 ELSEIF p_reporte='evaluaciones' THEN
  SELECT e.id evaluacion_id,c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente,CONCAT(p.nombre,' ',p.apellido) evaluador,e.evaluada_at,t.nombre medida,d.valor,d.unidad_snapshot unidad,d.instrumento
  FROM evaluaciones_fisicas e JOIN clientes c ON c.id=e.cliente_id JOIN personal p ON p.id=e.evaluador_id
   JOIN detalles_evaluacion d ON d.evaluacion_fisica_id=e.id JOIN tipos_medida t ON t.id=d.tipo_medida_id
  WHERE e.deleted_at IS NULL AND (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%'))
   AND (p_desde IS NULL OR e.evaluada_at>=p_desde) AND (p_hasta IS NULL OR e.evaluada_at<p_hasta+INTERVAL 1 DAY)
  ORDER BY e.evaluada_at DESC,t.nombre LIMIT p_limite OFFSET p_offset;
 ELSEIF p_reporte='entrenamientos' THEN
  SELECT c.numero_socio,CONCAT(c.nombre,' ',c.apellido) cliente,r.nombre rutina,s.nombre sesion,e.iniciado_at,e.finalizado_at,TIMESTAMPDIFF(MINUTE,e.iniciado_at,COALESCE(e.finalizado_at,CURRENT_TIMESTAMP)) duracion_minutos,e.esfuerzo_percibido
  FROM entrenamientos_realizados e JOIN clientes c ON c.id=e.cliente_id LEFT JOIN versiones_rutina v ON v.id=e.version_rutina_id LEFT JOIN rutinas r ON r.id=v.rutina_id LEFT JOIN sesiones_rutina s ON s.id=e.sesion_rutina_id
  WHERE (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%'))
   AND (p_desde IS NULL OR e.iniciado_at>=p_desde) AND (p_hasta IS NULL OR e.iniciado_at<p_hasta+INTERVAL 1 DAY)
  ORDER BY e.iniciado_at DESC LIMIT p_limite OFFSET p_offset;
 ELSE SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Tipo de reporte no permitido';
 END IF;
END$$

DROP PROCEDURE IF EXISTS sp_reportes_contar$$
CREATE PROCEDURE sp_reportes_contar(IN p_reporte VARCHAR(30),IN p_desde DATE,IN p_hasta DATE,IN p_busqueda VARCHAR(150),IN p_estado_id BIGINT UNSIGNED)
BEGIN
 IF p_reporte='clientes' THEN SELECT COUNT(*) total FROM clientes c WHERE c.deleted_at IS NULL AND (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%')) AND (p_estado_id IS NULL OR c.estado_cliente_id=p_estado_id) AND (p_desde IS NULL OR c.fecha_registro>=p_desde) AND (p_hasta IS NULL OR c.fecha_registro<=p_hasta);
 ELSEIF p_reporte='personal' THEN SELECT COUNT(*) total FROM personal p WHERE p.deleted_at IS NULL AND (p_busqueda IS NULL OR p_busqueda='' OR p.codigo_empleado LIKE CONCAT('%',p_busqueda,'%') OR p.nombre LIKE CONCAT('%',p_busqueda,'%') OR p.apellido LIKE CONCAT('%',p_busqueda,'%')) AND (p_estado_id IS NULL OR p.estado_personal_id=p_estado_id) AND (p_desde IS NULL OR p.fecha_contratacion>=p_desde) AND (p_hasta IS NULL OR p.fecha_contratacion<=p_hasta);
 ELSEIF p_reporte='pagos' THEN SELECT COUNT(*) total FROM pagos p JOIN clientes c ON c.id=p.cliente_id WHERE (p_busqueda IS NULL OR p_busqueda='' OR p.numero_recibo LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%')) AND (p_estado_id IS NULL OR p.estado_pago_id=p_estado_id) AND (p_desde IS NULL OR p.pagado_at>=p_desde) AND (p_hasta IS NULL OR p.pagado_at<p_hasta+INTERVAL 1 DAY);
 ELSEIF p_reporte='asistencias' THEN SELECT COUNT(*) total FROM asistencias a JOIN clientes c ON c.id=a.cliente_id WHERE (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%')) AND (p_desde IS NULL OR a.entrada_at>=p_desde) AND (p_hasta IS NULL OR a.entrada_at<p_hasta+INTERVAL 1 DAY);
 ELSEIF p_reporte='membresias' THEN SELECT COUNT(*) total FROM membresias m JOIN clientes c ON c.id=m.cliente_id WHERE m.deleted_at IS NULL AND (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%')) AND (p_estado_id IS NULL OR m.estado_membresia_id=p_estado_id) AND (p_desde IS NULL OR m.fecha_fin>=p_desde) AND (p_hasta IS NULL OR m.fecha_inicio<=p_hasta);
 ELSEIF p_reporte='evaluaciones' THEN SELECT COUNT(*) total FROM evaluaciones_fisicas e JOIN clientes c ON c.id=e.cliente_id JOIN detalles_evaluacion d ON d.evaluacion_fisica_id=e.id WHERE e.deleted_at IS NULL AND (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%')) AND (p_desde IS NULL OR e.evaluada_at>=p_desde) AND (p_hasta IS NULL OR e.evaluada_at<p_hasta+INTERVAL 1 DAY);
 ELSEIF p_reporte='entrenamientos' THEN SELECT COUNT(*) total FROM entrenamientos_realizados e JOIN clientes c ON c.id=e.cliente_id WHERE (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%')) AND (p_desde IS NULL OR e.iniciado_at>=p_desde) AND (p_hasta IS NULL OR e.iniciado_at<p_hasta+INTERVAL 1 DAY);
 ELSE SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Tipo de reporte no permitido'; END IF;
END$$

DROP PROCEDURE IF EXISTS sp_reportes_resumen$$
CREATE PROCEDURE sp_reportes_resumen(IN p_reporte VARCHAR(30),IN p_desde DATE,IN p_hasta DATE,IN p_busqueda VARCHAR(150),IN p_estado_id BIGINT UNSIGNED)
BEGIN
 IF p_reporte='pagos' THEN
  SELECT 'Pagos encontrados' metrica_1_etiqueta,COUNT(*) metrica_1_valor,'Ingresos aplicados' metrica_2_etiqueta,CONCAT(FORMAT(COALESCE(SUM(CASE WHEN ep.codigo='APLICADO' THEN p.monto ELSE 0 END),0),2),' HNL') metrica_2_valor,'Promedio aplicado' metrica_3_etiqueta,CONCAT(FORMAT(COALESCE(AVG(CASE WHEN ep.codigo='APLICADO' THEN p.monto END),0),2),' HNL') metrica_3_valor FROM pagos p JOIN clientes c ON c.id=p.cliente_id JOIN estados_pago ep ON ep.id=p.estado_pago_id WHERE (p_busqueda IS NULL OR p_busqueda='' OR p.numero_recibo LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%')) AND (p_estado_id IS NULL OR p.estado_pago_id=p_estado_id) AND (p_desde IS NULL OR p.pagado_at>=p_desde) AND (p_hasta IS NULL OR p.pagado_at<p_hasta+INTERVAL 1 DAY);
 ELSEIF p_reporte='membresias' THEN
  SELECT 'Membresías encontradas' metrica_1_etiqueta,COUNT(*) metrica_1_valor,'Activas' metrica_2_etiqueta,SUM(UPPER(em.codigo)='ACTIVA') metrica_2_valor,'Vencen en 30 días' metrica_3_etiqueta,SUM(m.fecha_fin BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE,INTERVAL 30 DAY)) metrica_3_valor FROM membresias m JOIN clientes c ON c.id=m.cliente_id JOIN estados_membresia em ON em.id=m.estado_membresia_id WHERE m.deleted_at IS NULL AND (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%')) AND (p_estado_id IS NULL OR m.estado_membresia_id=p_estado_id) AND (p_desde IS NULL OR m.fecha_fin>=p_desde) AND (p_hasta IS NULL OR m.fecha_inicio<=p_hasta);
 ELSEIF p_reporte='asistencias' THEN
  SELECT 'Visitas registradas' metrica_1_etiqueta,COUNT(*) metrica_1_valor,'Clientes distintos' metrica_2_etiqueta,COUNT(DISTINCT a.cliente_id) metrica_2_valor,'Duración promedio' metrica_3_etiqueta,CONCAT(ROUND(COALESCE(AVG(TIMESTAMPDIFF(MINUTE,a.entrada_at,COALESCE(a.salida_at,CURRENT_TIMESTAMP))),0)),' min') metrica_3_valor FROM asistencias a JOIN clientes c ON c.id=a.cliente_id WHERE (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%')) AND (p_desde IS NULL OR a.entrada_at>=p_desde) AND (p_hasta IS NULL OR a.entrada_at<p_hasta+INTERVAL 1 DAY);
 ELSEIF p_reporte='evaluaciones' THEN
  SELECT 'Evaluaciones' metrica_1_etiqueta,COUNT(DISTINCT e.id) metrica_1_valor,'Clientes evaluados' metrica_2_etiqueta,COUNT(DISTINCT e.cliente_id) metrica_2_valor,'Medidas registradas' metrica_3_etiqueta,COUNT(d.id) metrica_3_valor FROM evaluaciones_fisicas e JOIN clientes c ON c.id=e.cliente_id JOIN detalles_evaluacion d ON d.evaluacion_fisica_id=e.id WHERE e.deleted_at IS NULL AND (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%')) AND (p_desde IS NULL OR e.evaluada_at>=p_desde) AND (p_hasta IS NULL OR e.evaluada_at<p_hasta+INTERVAL 1 DAY);
 ELSEIF p_reporte='entrenamientos' THEN
  SELECT 'Sesiones registradas' metrica_1_etiqueta,COUNT(*) metrica_1_valor,'Clientes entrenados' metrica_2_etiqueta,COUNT(DISTINCT e.cliente_id) metrica_2_valor,'Duración promedio' metrica_3_etiqueta,CONCAT(ROUND(COALESCE(AVG(TIMESTAMPDIFF(MINUTE,e.iniciado_at,COALESCE(e.finalizado_at,CURRENT_TIMESTAMP))),0)),' min') metrica_3_valor FROM entrenamientos_realizados e JOIN clientes c ON c.id=e.cliente_id WHERE (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%')) AND (p_desde IS NULL OR e.iniciado_at>=p_desde) AND (p_hasta IS NULL OR e.iniciado_at<p_hasta+INTERVAL 1 DAY);
 ELSEIF p_reporte='clientes' THEN
  SELECT 'Clientes encontrados' metrica_1_etiqueta,COUNT(*) metrica_1_valor,'Con correo' metrica_2_etiqueta,SUM(c.correo_electronico IS NOT NULL) metrica_2_valor,'Con teléfono' metrica_3_etiqueta,SUM(c.telefono IS NOT NULL) metrica_3_valor FROM clientes c WHERE c.deleted_at IS NULL AND (p_busqueda IS NULL OR p_busqueda='' OR c.numero_socio LIKE CONCAT('%',p_busqueda,'%') OR c.nombre LIKE CONCAT('%',p_busqueda,'%') OR c.apellido LIKE CONCAT('%',p_busqueda,'%')) AND (p_estado_id IS NULL OR c.estado_cliente_id=p_estado_id) AND (p_desde IS NULL OR c.fecha_registro>=p_desde) AND (p_hasta IS NULL OR c.fecha_registro<=p_hasta);
 ELSEIF p_reporte='personal' THEN
  SELECT 'Empleados encontrados' metrica_1_etiqueta,COUNT(*) metrica_1_valor,'Con correo' metrica_2_etiqueta,SUM(p.correo_electronico IS NOT NULL) metrica_2_valor,'Con teléfono' metrica_3_etiqueta,SUM(p.telefono IS NOT NULL) metrica_3_valor FROM personal p WHERE p.deleted_at IS NULL AND (p_busqueda IS NULL OR p_busqueda='' OR p.codigo_empleado LIKE CONCAT('%',p_busqueda,'%') OR p.nombre LIKE CONCAT('%',p_busqueda,'%') OR p.apellido LIKE CONCAT('%',p_busqueda,'%')) AND (p_estado_id IS NULL OR p.estado_personal_id=p_estado_id) AND (p_desde IS NULL OR p.fecha_contratacion>=p_desde) AND (p_hasta IS NULL OR p.fecha_contratacion<=p_hasta);
 ELSE SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Tipo de reporte no permitido'; END IF;
END$$
DELIMITER ;
