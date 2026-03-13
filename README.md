# TDM Pro Manager (versión mejorada)

Plataforma profesional de torneos de tenis de mesa con PHP 8.2+, MySQL y arquitectura MVC.

## Mejoras aplicadas en esta iteración

- UI SaaS premium rediseñada: sidebar elegante, topbar limpia, tarjetas métricas, tablas modernas y login profesional.
- Persistencia real mejorada en MySQL para configuración global, usuarios/roles, auditoría y módulos operativos.
- Usuario ROOT operativo por defecto con control total.
- Control de acceso robusto: `AuthMiddleware`, `AdminMiddleware`, `RootMiddleware`.
- Dashboard mejorado con métricas clave + actividad reciente desde auditoría.
- Tablas con buscador y paginación en organizaciones, jugadores, torneos y usuarios.
- Logs de auditoría para login/logout y acciones críticas de creación/configuración.

## Estructura

```txt
/app
  /Core /Controllers /Models /Middlewares /Services /helpers /views
/public
  /assets/css /assets/js /uploads /pdf
/config
/database
  /schema /seeders /migrations /updates /backups
/routes /storage /logs /docs /tests
```

## Credenciales demo

- ROOT: `root@system.local` / `Root12345!` (debe cambiarse al primer inicio).
- Super admin: `admin@tdmpro.test` / `secret123`
- Admin organización: `org@tdmpro.test` / `secret123`

## Instalación

```bash
mysql -u root -p < database/schema/001_initial_schema.sql
mysql -u root -p < database/seeders/001_demo_data.sql
# opcional migraciones adicionales
mysql -u root -p < database/migrations/002_add_match_stats.sql
mysql -u root -p < database/migrations/003_add_referees_module.sql
mysql -u root -p < database/migrations/005_add_root_role.sql
mysql -u root -p < database/migrations/006_add_settings_and_audit_indexes.sql
mysql -u root -p < database/updates/004_add_live_score_fields.sql
php -S 0.0.0.0:8000 -t .
```

## Módulos administrativos actuales

- Dashboard
- Usuarios y roles (ROOT)
- Organizaciones
- Jugadores
- Torneos
- Inscripciones
- Partidos
- Rankings
- Auditoría (ROOT)
- Configuración global (ROOT)

Documentación extendida en `/docs`.


## Nota XAMPP / subcarpeta

Si abres el proyecto como `http://localhost/tdm3/`, el sistema detecta automáticamente la subruta.
También puedes forzarla con `APP_BASE_PATH` (por ejemplo `/tdm3`).

Asegúrate de tener `.htaccess` habilitado con `mod_rewrite` en Apache para rutas amigables.
