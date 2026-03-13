# Instalación local

## Opción 1: XAMPP (recomendado para tu caso)
1. Copia el proyecto en `htdocs`, por ejemplo: `C:\xampp\htdocs\tdm3`.
2. Habilita `mod_rewrite` en Apache.
3. Asegúrate de que existe `.htaccess` en la raíz del proyecto.
4. Crea la base de datos y carga scripts:
   - `mysql -u root -p < database/schema/001_initial_schema.sql`
   - `mysql -u root -p < database/seeders/001_demo_data.sql`
5. (Opcional) aplica migraciones incrementales:
   - `mysql -u root -p < database/migrations/002_add_match_stats.sql`
   - `mysql -u root -p < database/migrations/003_add_referees_module.sql`
   - `mysql -u root -p < database/migrations/005_add_root_role.sql`
   - `mysql -u root -p < database/migrations/006_add_settings_and_audit_indexes.sql`
   - `mysql -u root -p < database/updates/004_add_live_score_fields.sql`
6. Abre en navegador:
   - `http://localhost/tdm3/`

> Si usas subcarpeta distinta, puedes fijar `APP_BASE_PATH` (ej: `/mi-carpeta`) en variables de entorno.

## Opción 2: servidor embebido PHP
- `php -S 0.0.0.0:8000 -t .`
- Abrir `http://localhost:8000`

## Credenciales demo
- ROOT: `root@system.local` / `Root12345!` (**cambiar al primer inicio**).
- Super admin: `admin@tdmpro.test` / `secret123`
- Admin organización: `org@tdmpro.test` / `secret123`
