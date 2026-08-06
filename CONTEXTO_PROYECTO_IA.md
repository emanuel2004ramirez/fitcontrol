# FitControl — Contexto integral del proyecto para desarrolladores y asistentes de IA

## 1. Propósito de este documento

Este documento describe el estado funcional y técnico de **FitControl**, un sistema de gestión para gimnasios. Su objetivo principal es permitir que otro desarrollador o asistente de inteligencia artificial comprenda el proyecto antes de realizar cambios.

Debe leerse completamente antes de modificar arquitectura, base de datos, procedimientos almacenados, permisos, formularios o flujos operativos.

FitControl no debe tratarse como un CRUD genérico. El sistema conserva información histórica, aplica reglas de negocio en MySQL y separa las responsabilidades administrativas, financieras y deportivas de un gimnasio real.

---

## 2. Descripción general

FitControl administra el ciclo de vida de un cliente dentro de un gimnasio:

1. Un empleado autorizado inicia sesión.
2. Recepción registra al cliente.
3. Se completa su expediente administrativo, médico y legal.
4. Se crea una membresía.
5. La fecha final se calcula según la duración del plan.
6. Se registra el pago completo de la membresía.
7. La membresía queda habilitada para acceso.
8. Recepción registra entradas y salidas.
9. Un entrenador realiza evaluaciones físicas.
10. El entrenador crea una rutina y agrega ejercicios.
11. El sistema conserva el historial operativo y deportivo.
12. Gerencia consulta indicadores, reportes y auditoría.

Flujo principal:

```text
Cliente
  → expediente
  → membresía
  → pago completo
  → activación
  → asistencia
  → evaluación física
  → rutina
  → seguimiento
  → renovación, congelación o cancelación
```

---

## 3. Tecnologías

### Backend

- Laravel 12.
- PHP objetivo: 8.4.
- Runtime local observado durante el desarrollo: PHP 8.2.12.
- Composer para dependencias PHP.
- Arquitectura MVC complementada con capa Service.
- Form Requests para validación y autorización.
- Blade como motor de vistas.

### Base de datos

- MySQL 8.
- Base local utilizada: `fitcontrol_v2`.
- Procedimientos almacenados para toda operación de negocio.
- Transacciones y `SIGNAL SQLSTATE '45000'` para reglas profesionales.
- Claves foráneas, índices, restricciones y campos históricos.

### Interfaz

- Blade.
- Bootstrap 5.
- Bootstrap Icons.
- Diseño responsive.
- Sidebar basado en permisos.
- Navbar, breadcrumbs, tarjetas, alertas y componentes reutilizables.

### Paquetes relevantes

- `spatie/laravel-permission` para soporte de roles y permisos.
- Exportación de reportes PDF y Excel según los servicios existentes.

---

## 4. Principio arquitectónico obligatorio

Toda interacción de negocio con la base de datos debe ejecutarse mediante procedimientos almacenados.

La arquitectura esperada es:

```text
Vista Blade
    ↓
Form Request
    ↓
Controller
    ↓
Service
    ↓
Stored Procedure
    ↓
MySQL
```

### Controladores

Los controladores solamente deben:

- Recibir la petición.
- Utilizar un Form Request.
- Obtener los datos validados.
- Agregar el usuario autenticado cuando corresponda.
- Invocar un Service.
- Retornar una vista o redirección.

No deben contener consultas SQL ni lógica compleja de negocio.

### Services

Los Services son la única capa autorizada para invocar procedimientos almacenados mediante la clase base `StoredProcedureService`.

Métodos principales disponibles en esa clase:

- Selección de múltiples resultados.
- Selección de un resultado.
- Ejecución de procedimientos sin resultado principal.
- Paginación basada en un procedimiento de conteo y otro de filtrado.

No agregar consultas directas como:

```php
DB::table('clientes')->get();
Cliente::query()->where(...)->get();
```

La autenticación utiliza el modelo `User` porque Laravel necesita un proveedor autenticable, pero los datos auxiliares de autenticación, roles y perfil se obtienen mediante procedimientos.

### Stored Procedures

Convención:

```text
sp_modulo_accion
```

Ejemplos:

- `sp_clientes_crear`
- `sp_membresias_crear`
- `sp_pagos_registrar_completo`
- `sp_asistencias_registrar_entrada`
- `sp_rutinas_agregar_ejercicio_simple`

Los procedimientos deben validar nuevamente las reglas importantes. La validación web no reemplaza la integridad en MySQL.

---

## 5. Organización principal del repositorio

```text
app/
├── Console/Commands/          Instaladores y auditoría de procedimientos
├── Http/Controllers/          Controladores por módulo
├── Http/Middleware/           Permisos de sesión
├── Http/Requests/             Validaciones por operación
├── Models/                    Modelos y autenticación
├── Services/                  Única capa que invoca SP
└── Support/Authorization/     Definición de permisos

database/
├── migrations/                Estructura normalizada
├── seeders/                   Catálogos y datos de demostración
├── *_stored_procedures.sql    Procedimientos por módulo
└── fitcontrol_stored_procedures.sql

resources/views/
├── auth/
├── clientes/
├── membresias/
├── pagos/
├── asistencias/
├── ejercicios/
├── rutinas/
├── evaluaciones/
├── personal/
├── reportes/
├── components/
├── partials/
└── layouts/

routes/
└── web.php
```

---

## 6. Base de datos e historial

La base está diseñada para no perder información importante.

### Eliminación lógica

Se utiliza `deleted_at` en entidades donde retirar un registro no debe destruirlo físicamente.

Ejemplos:

- Clientes.
- Personal.
- Usuarios.
- Membresías.
- Ejercicios.
- Rutinas.
- Evaluaciones físicas.

### Información que debe conservarse

- Membresías anteriores.
- Pagos y recibos.
- Aplicaciones financieras internas.
- Cambios de estado.
- Congelaciones.
- Evaluaciones físicas.
- Medidas corporales.
- Rutinas y versiones internas.
- Asistencias.
- Cambios de cargo del personal.
- Auditoría.

No eliminar físicamente documentos financieros o eventos históricos para resolver errores funcionales.

---

## 7. Módulo de autenticación

Ruta principal:

```text
/login
```

El login permite usuario o correo y contraseña.

Flujo:

1. `LoginRequest` valida credenciales.
2. `UsuarioService` obtiene el candidato mediante SP.
3. Laravel autentica al usuario.
4. Se regeneran sesión y token.
5. Se registran acceso exitoso o fallido.
6. Se obtienen roles, permisos y perfil mediante SP.
7. Los permisos se guardan en sesión.
8. El menú se construye de forma dinámica.

Información guardada en sesión:

- `permissions`
- `role_codes`
- `personal_id`
- `auth_user`

`personal_id` es importante para asignar automáticamente al entrenador autenticado.

El login está limitado por `throttle:5,1`.

Si la sesión expira, una respuesta 419 redirige al login con un mensaje comprensible.

URL local recomendada:

```text
http://127.0.0.1:8000
```

No alternar entre `localhost` y `127.0.0.1`, porque las cookies son diferentes.

---

## 8. Roles y permisos

### Superadministrador

- Acceso total.
- Código: `super-admin`.
- Permiso especial efectivo: `*`.

### Gerencia

- Supervisión operativa.
- Clientes.
- Personal.
- Membresías.
- Pagos.
- Asistencias.
- Ejercicios.
- Rutinas.
- Evaluaciones.
- Entrenamientos.
- Reportes.
- Consulta de auditoría.

Los reportes están reservados para gerencia y superadministración.

### Recepción

- Registrar y administrar clientes.
- Administrar membresías.
- Registrar entradas y salidas.
- Consultar pagos según permisos asignados.

### Caja

- Registrar pagos completos.
- Consultar clientes y membresías.
- No debe acceder a reportes gerenciales.

### Entrenador

- Consultar clientes.
- Administrar ejercicios.
- Crear rutinas.
- Realizar evaluaciones.
- Administrar entrenamientos.
- Consultar asistencias de hoy.
- Registrar la salida de una asistencia abierta.
- No registrar nuevas entradas.

Las definiciones se encuentran principalmente en:

- `FitControlPermissions.php`
- `SetupAccessControl.php`

Cuando se modifican permisos, el usuario debe cerrar sesión y volver a ingresar porque los permisos están almacenados en sesión.

---

## 9. Usuarios demostrativos

Entorno de desarrollo:

| Usuario | Rol | Función |
|---|---|---|
| `admin` | Superadministrador | Acceso total |
| `maria` | Gerencia | Supervisión y reportes |
| `andrea` | Recepción | Clientes, membresías y accesos |
| `sofia` | Caja | Pagos |
| `jose` | Entrenador | Rutinas y evaluaciones |

Contraseña de desarrollo para las cuentas seed:

```text
123456
```

Esta contraseña no debe utilizarse en producción.

---

## 10. Módulo Clientes

### Objetivo

Crear y conservar el expediente de cada persona afiliada al gimnasio.

### Número de socio

El usuario no escribe el número de socio.

El procedimiento genera automáticamente:

```text
CLI-000001
CLI-000002
CLI-000003
```

La generación ocurre en `sp_clientes_crear` después de obtener el ID.

### Datos principales

- Nombre.
- Apellido.
- Estado inicial.
- Sexo.
- Fecha de nacimiento.
- Identificación.
- Teléfono.
- Correo electrónico obligatorio.
- Dirección.
- Ciudad.

El campo País ISO fue retirado de la interfaz por decisión funcional. La columna puede seguir existiendo y aceptar `NULL` para compatibilidad histórica.

### Submódulos del expediente

- Contactos de emergencia.
- Datos médicos.
- Consentimientos.
- Historial de estados.

### Correo de bienvenida

Después de crear el cliente, `ClienteCorreoService` envía `ClienteBienvenidaMail` al correo registrado. El mensaje contiene su nombre y número de socio automático. Un fallo del proveedor de correo no revierte la creación del cliente: se registra una advertencia y recepción recibe un mensaje informativo.

En desarrollo, `MAIL_MAILER=log` escribe el correo en `storage/logs/laravel.log`. Para entrega real debe configurarse un proveedor SMTP en `.env`.

### Reglas

- El correo es obligatorio.
- No duplicar correo.
- No duplicar identificación cuando se proporciona tipo y número.
- No aceptar fechas de nacimiento futuras.
- Los cambios de estado deben conservar historial.
- Retirar un cliente debe ser lógico y con motivo.

---

## 11. Módulo Membresías

### Objetivo

Representar el contrato temporal entre el gimnasio y el cliente.

### Creación

El usuario selecciona:

- Cliente.
- Plan y precio vigente.
- Estado inicial.
- Fecha de inicio.

No escribe la fecha final.

MySQL obtiene `duracion_dias` desde el tipo de membresía y calcula:

```text
fecha_fin = fecha_inicio + duracion_dias - 1
```

Ejemplo anual:

```text
Inicio: 05/08/2026
Duración: 365 días
Fin: 04/08/2027
```

### Planes de seed

- Diario.
- Semanal.
- Quincenal.
- Mensual.
- Estudiantil.
- Pareja.
- Familiar.
- Corporativa.
- Premium.
- Trimestral.
- Semestral.
- Anual.

### Reglas

- Un cliente solamente puede tener una membresía marcada como activa para control operativo.
- El precio debe corresponder al tipo de membresía.
- El precio debe estar vigente en la fecha inicial.
- Se conserva `precio_contratado` y moneda.
- Renovar crea continuidad histórica.
- Congelar crea una suspensión con fechas, motivo y posible extensión.
- Cancelar no destruye el registro.
- Los estados determinan si la membresía permite acceso.

---

## 12. Módulo Pagos

### Decisión funcional vigente

FitControl no expone un módulo independiente de cargos por cobrar.

El gimnasio trabaja con pago completo de contado. No se permiten pagos parciales.

### Flujo visible

1. Entrar a Pagos.
2. Seleccionar cliente y membresía pendiente.
3. El sistema muestra el total.
4. Seleccionar método de pago.
5. Agregar referencia si corresponde.
6. Presionar `Pagar total`.

El usuario no escribe:

- Número de recibo.
- Monto.
- Moneda.
- Estado.
- Cargo.
- Aplicación.

### Operación interna

`sp_pagos_registrar_completo` ejecuta una transacción que:

1. Valida la membresía.
2. Obtiene precio y moneda.
3. Valida el método de pago.
4. Exige referencia para métodos configurados con referencia obligatoria.
5. Genera un cargo técnico interno.
6. Genera el pago.
7. Genera número de recibo.
8. Aplica el total al registro interno.
9. Marca el pago como aplicado.
10. Activa la membresía si corresponde.
11. Registra historiales.

### Numeración

Los números se generan internamente con valores únicos basados en `UUID_SHORT()`.

### Compatibilidad histórica

Las tablas `cargos_cobro` y `aplicaciones_pago` permanecen porque sirven para:

- Integridad financiera.
- Historial.
- Relación entre membresía y pago.
- Reportes existentes.
- Compatibilidad con registros anteriores.

No deben eliminarse aunque el módulo de cargos no sea visible.

### Restricción central

`sp_pagos_aplicar` rechaza importes que no representen simultáneamente todo el saldo disponible y todo el total pendiente.

---

## 13. Definición de cliente pagado

Para asistencias, rutinas y evaluaciones, un cliente está habilitado cuando cumple todas estas condiciones:

- Cliente no eliminado.
- Membresía no eliminada.
- Membresía marcada como activa para operación.
- Estado de membresía que permite acceso.
- Fecha actual dentro de inicio y fin.
- Existe registro financiero asociado a la membresía.
- El total aplicado cubre completamente el total.
- No existe saldo pendiente.
- No existe congelación vigente cuando se trata de acceso.

Esta regla debe mantenerse consistente entre procedimientos de pagos, asistencias, rutinas y evaluaciones.

---

## 14. Módulo Asistencias

### Objetivo

Controlar quién entra, quién continúa dentro y quién sale del gimnasio.

### Pantalla principal

Muestra únicamente asistencias del día actual.

Indicadores:

- Personas dentro ahora.
- Entradas del día.
- Duración promedio.

### Entrada

El registro es solamente manual.

Formulario:

- Cliente.
- Observación opcional.

No se muestra:

- Método QR.
- Biométrico.
- Importación.
- Fecha y hora manual.

El servidor registra fecha y hora actuales y método `MANUAL`.

### Salida

Una asistencia abierta tiene entrada pero no salida.

La opción `Abiertas` significa:

```text
Asistencias con salida_at = NULL
```

Permite localizar clientes que todavía aparecen dentro del gimnasio.

### Reglas

- Solo clientes vigentes y totalmente pagados.
- No permitir acceso durante congelación.
- No permitir dos asistencias abiertas simultáneamente para un cliente.
- La salida debe ser posterior a la entrada.
- Se pueden registrar varias visitas el mismo día si la anterior fue cerrada.
- La hora de entrada no puede estar en el futuro.
- Observación es opcional.

---

## 15. Catálogo de ejercicios

### Código automático

El usuario no escribe el código.

El procedimiento genera:

```text
EJ-000001
EJ-000002
EJ-000003
```

### Datos

- Nombre.
- Estado.
- Patrón de movimiento opcional.
- Equipamiento opcional.
- Descripción opcional.
- Instrucciones opcionales.
- Video opcional.
- Grupos musculares.
- Grupo muscular principal.

### Reglas

- El nombre es obligatorio.
- El estado debe existir.
- Un ejercicio retirado no debe destruir el historial de rutinas.
- Los grupos musculares se gestionan mediante tabla relacional.

---

## 16. Módulo Rutinas

### Decisión de interfaz vigente

La interfaz debe ser sencilla. El entrenador crea una rutina y agrega ejercicios directamente con un botón `+`.

No se muestran al entrenador:

- Versiones.
- Sesiones.
- Número de orden.
- Publicación manual.
- Activación manual.

Las versiones y sesiones continúan internamente para conservar historial y compatibilidad.

### Creación

Se selecciona:

- Cliente pagado.
- Nombre.
- Estado.
- Fecha de inicio.
- Descripción opcional.

Si el usuario autenticado tiene rol `entrenador`, el sistema usa automáticamente su `personal_id`. El entrenador no se selecciona a sí mismo.

Gerencia o superadministración puede seleccionar un entrenador cuando corresponda.

### Rutina nueva

`sp_rutinas_crear` crea internamente:

1. Rutina.
2. Versión inicial.
3. Sesión predeterminada llamada `Ejercicios`.

### Agregar ejercicio

Formulario simple:

- Ejercicio.
- Series.
- Repeticiones.
- Peso opcional.
- Descanso opcional.
- Indicación opcional.
- Botón `+`.

`sp_rutinas_agregar_ejercicio_simple`:

1. Valida rutina y ejercicio.
2. Localiza la versión editable.
3. Crea una versión interna si la última ya está publicada.
4. Localiza o crea la sesión predeterminada.
5. Calcula automáticamente el siguiente orden.
6. Inserta el ejercicio.

El campo peso representa una carga recomendada y es opcional. Puede retirarse solo de la interfaz sin eliminar la columna histórica si el propietario decide simplificarla aún más.

### Reglas

- Solo clientes con membresía vigente y totalmente pagada.
- El orden no lo escribe el usuario.
- No perder versiones históricas.
- Los ejercicios se retiran mediante el procedimiento correspondiente.

---

## 17. Evaluaciones físicas

### Objetivo

Registrar el estado físico del cliente y comparar su evolución.

### Evaluación

- Cliente habilitado.
- Evaluador.
- Fecha y hora.
- Método.
- Observaciones.
- Medidas.

### Medidas

Los tipos de medida provienen de catálogo y pueden incluir:

- Peso.
- Estatura.
- Cintura.
- Cadera.
- Porcentaje de grasa.
- Masa muscular.
- Otras medidas configuradas.

### Reglas

- Solo clientes con membresía vigente y totalmente pagada aparecen para evaluación.
- Una evaluación debe conservarse como evento histórico.
- Las medidas tienen unidad snapshot.
- Se validan rangos configurados.
- Se pueden comparar dos evaluaciones del mismo cliente.
- Si una evaluación muestra cero medidas, significa que existe el encabezado pero no se agregaron detalles.

---

## 18. Personal

### Datos

- Código de empleado.
- Cargo actual.
- Sexo.
- Estado laboral.
- Nombre y apellido.
- Identificación.
- Teléfono.
- Correo.
- Fecha de contratación.
- Terminación y motivo cuando corresponda.

### Funcionalidades

- CRUD lógico.
- Cambios de estado.
- Historial de cargos.
- Horarios.
- Filtros y búsqueda.

### Reglas

- Los cambios de cargo deben conservar vigencia histórica.
- No eliminar un empleado relacionado con operaciones históricas.
- Un usuario puede vincularse con un empleado.
- `users.personal_id` es único y opcional.

---

## 19. Dashboard

El dashboard obtiene datos mediante procedimientos almacenados optimizados.

Puede incluir:

- Clientes activos.
- Membresías activas.
- Membresías próximas a vencer.
- Pagos recibidos.
- Asistencias de hoy.
- Personas dentro del gimnasio.
- Actividad reciente.
- Gráficas de ingresos y asistencia.

No convertir el dashboard en consultas directas a tablas desde el controlador.

---

## 20. Reportes

Los reportes están reservados para gerencia y superadministración.

Tipos contemplados:

- Clientes.
- Personal.
- Pagos.
- Información financiera histórica.
- Asistencias.
- Membresías.
- Evaluaciones.
- Entrenamientos.

Opciones:

- Filtros.
- Vista web.
- Impresión.
- PDF.
- Excel.

Todos los datos deben obtenerse mediante procedimientos almacenados.

Aunque puedan existir reportes históricos de cobros, no debe reactivarse el módulo operativo visible de cargos sin autorización expresa.

---

## 21. Auditoría

La tabla de auditoría conserva operaciones importantes mediante procedimientos almacenados.

Eventos relevantes:

- Crear.
- Editar.
- Retirar.
- Cambiar estado.
- Iniciar sesión.
- Pagos.
- Membresías.
- Operaciones sensibles.

Datos esperados:

- Usuario.
- Acción.
- Módulo o entidad.
- Registro afectado.
- Valores anteriores y nuevos cuando corresponda.
- Fecha y hora.
- IP.

No usar auditoría como sustituto de tablas históricas de dominio. Ambas cumplen objetivos diferentes.

---

## 22. Catálogos y seeders

Seeders principales:

- `GeneralCatalogSeeder`
- `MembershipCatalogSeeder`
- `FinanceCatalogSeeder`
- `TrainingCatalogSeeder`
- `AuthorizationSeeder`
- `DemoEmployeeSeeder`
- `DemoUserSeeder`
- `DemoClientSeeder`

El `DatabaseSeeder` prepara permisos, catálogos y datos de demostración.

Los seeders deben ser idempotentes siempre que sea posible: ejecutarlos nuevamente no debe duplicar catálogos o usuarios.

---

## 23. Instalación del proyecto

Flujo típico para un desarrollador nuevo:

```bash
git clone URL_DEL_REPOSITORIO
cd FitControl
composer install
copy .env.example .env
php artisan key:generate
```

Configurar MySQL en `.env`:

```env
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fitcontrol_v2
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
```

Después:

```bash
php artisan migrate
php artisan fitcontrol:install-all-procedures
php artisan db:seed
php artisan optimize:clear
php artisan serve
```

Abrir:

```text
http://127.0.0.1:8000/login
```

Si la base ya existe, no ejecutar `migrate:fresh` porque eliminaría información.

---

## 24. Comandos del proyecto

Instalar todos los procedimientos:

```bash
php artisan fitcontrol:install-all-procedures
```

Auditar procedimientos:

```bash
php artisan fitcontrol:audit-procedures
```

Instaladores por módulo:

```bash
php artisan fitcontrol:install-clientes-procedures
php artisan fitcontrol:install-personal-procedures
php artisan fitcontrol:install-membresias-procedures
php artisan fitcontrol:install-cargos-cobro-procedures
php artisan fitcontrol:install-pagos-procedures
php artisan fitcontrol:install-ejercicios-procedures
php artisan fitcontrol:install-rutinas-procedures
php artisan fitcontrol:install-evaluaciones-procedures
php artisan fitcontrol:install-asistencias-procedures
php artisan fitcontrol:install-dashboard-procedures
php artisan fitcontrol:install-reportes-procedures
php artisan fitcontrol:install-auth-procedures
php artisan fitcontrol:install-auditoria-procedures
```

Actualizar roles y permisos:

```bash
php artisan fitcontrol:setup-access --password=123456
```

Limpiar cachés:

```bash
php artisan optimize:clear
```

Ejecutar pruebas:

```bash
php artisan test
```

---

## 25. Estado de procedimientos

En la última verificación documentada:

- Procedimientos definidos: 308.
- Procedimientos instalados: 308.
- Faltantes: 0.
- Extras: 0.

Este número puede aumentar legítimamente si se agregan funciones. Lo importante es que auditoría reporte cero faltantes y cero extras.

---

## 26. Pruebas y restricciones automáticas

Existe una prueba de arquitectura que revisa que controladores, servicios y seeders no consulten tablas directamente.

Antes de entregar cambios ejecutar:

```bash
php artisan test
php artisan fitcontrol:audit-procedures
```

Resultado esperado:

```text
Todas las pruebas aprobadas
Procedimientos faltantes: 0
Procedimientos extras: 0
```

También ejecutar validación de sintaxis PHP en archivos modificados cuando corresponda:

```bash
php -l ruta/al/archivo.php
```

---

## 27. Manejo de errores

Las reglas de MySQL utilizan normalmente:

```sql
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Mensaje';
```

Laravel convierte esas excepciones en errores de validación comprensibles para el usuario.

Errores de integridad `23000` también se presentan de forma controlada.

No mostrar trazas SQL al usuario en producción.

Problemas previamente resueltos:

- Número temporal de cliente demasiado largo para `numero_socio`.
- `ORDER BY` incompatible con `SELECT DISTINCT` en MySQL 8.
- Mezcla de `localhost` y `127.0.0.1` causando sesiones diferentes.
- Formularios opcionales tratados erróneamente como obligatorios.
- Pago histórico sin aplicación interna.

---

## 28. Validaciones opcionales

`FitControlRequest` contiene validación común y mensajes en español.

Los campos declarados con `nullable` deben permanecer opcionales. No agregar validaciones globales que conviertan todos los valores vacíos en errores.

Ejemplos opcionales actuales:

- Observación de asistencia.
- Descripción de rutina.
- Peso de ejercicio en rutina.
- Instrucciones del ejercicio.
- Video.
- Referencia cuando el método es efectivo.

---

## 29. División de trabajo recomendada para cuatro integrantes

### Integrante 1 — Clientes y membresías

- Clientes.
- Expediente.
- Contactos.
- Datos médicos.
- Consentimientos.
- Membresías.
- Renovaciones y congelaciones.

Rama:

```text
feature/clientes-membresias
```

### Integrante 2 — Pagos

- Pago total.
- Métodos.
- Recibos.
- Historial financiero.
- Indicadores de ingresos.

Rama:

```text
feature/pagos
```

### Integrante 3 — Entrenamiento

- Ejercicios.
- Grupos musculares.
- Rutinas.
- Evaluaciones.
- Medidas.
- Comparaciones.

Rama:

```text
feature/entrenamiento
```

### Integrante 4 — Operación y seguridad

- Login.
- Roles y permisos.
- Personal.
- Asistencias.
- Dashboard.
- Reportes.
- Auditoría.
- Integración de rutas y menú.

Rama:

```text
feature/operacion-seguridad
```

Archivos compartidos como rutas, sidebar y permisos deben tener un responsable de integración para reducir conflictos.

---

## 30. Reglas para futuras IA

Antes de realizar cambios, una IA debe:

1. Leer este documento completo.
2. Revisar el Service, Controller, Request, vista y SQL del módulo afectado.
3. Confirmar que el procedimiento instalado proviene del archivo modular correcto.
4. Preservar historial.
5. No introducir consultas directas.
6. Mantener mensajes en español.
7. Mantener interfaz simple para usuarios no técnicos.
8. No pedir al usuario valores que pueden generarse automáticamente.
9. No reintroducir pagos parciales.
10. No reactivar el módulo visible de cargos por cobrar.
11. No mostrar versiones y sesiones de rutinas en la operación normal.
12. Mantener rutinas y evaluaciones restringidas a clientes pagados.
13. Mantener recepción como responsable principal de entradas.
14. Mantener reportes restringidos a gerencia y superadministración.
15. Ejecutar instalación de procedimientos, auditoría y pruebas.

---

## 31. Lista de decisiones funcionales vigentes

- Número de cliente automático.
- Código de ejercicio automático.
- Fecha final de membresía automática.
- Correo de cliente obligatorio.
- País ISO retirado del formulario.
- Asistencia únicamente manual.
- Observación de asistencia opcional.
- Pantalla de asistencias limitada al día actual.
- Entrenador puede consultar y registrar salida, pero no entrada.
- Reportes solamente para gerencia y superadministración.
- Rutinas y evaluaciones solamente para clientes pagados.
- Entrenador autenticado asignado automáticamente.
- Rutina visible como lista simple de ejercicios.
- Ejercicios agregados con botón `+`.
- Orden de rutina automático.
- Pago completo de contado.
- Sin pagos parciales.
- Sin módulo visible de cargos por cobrar.
- Historial financiero interno preservado.

---

## 32. Visión futura

Mejoras que pueden incorporarse sin romper el núcleo:

- Reservas de clases.
- Agenda de entrenadores.
- Planes familiares.
- Convenios corporativos.
- Promociones autorizadas.
- Notificaciones de vencimiento.
- Portal del cliente.
- Código QR o biometría, solo si se cambia expresamente la decisión de asistencia manual.
- Caja y cierre diario.
- Inventario y ventas.
- Mantenimiento de equipos.
- Multi-sucursal.
- Seguimiento automático de clientes inactivos.

Toda mejora futura debe respetar la arquitectura basada en Services y Stored Procedures.

---

## 33. Resumen final

FitControl es un sistema Laravel 12 orientado a la operación real de un gimnasio. Su núcleo es la relación controlada entre cliente, membresía, pago completo, acceso, evaluación y rutina.

La interfaz busca ser sencilla, mientras que MySQL conserva controles, transacciones e historial profesional. La complejidad técnica debe permanecer en Services y procedimientos almacenados, no trasladarse al usuario.

Principio final:

```text
Interfaz simple
    +
reglas estrictas en MySQL
    +
historial conservado
    +
permisos por responsabilidad
    =
FitControl
```
