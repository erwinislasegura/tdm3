# Instalación local

1. Crear esquema base:
   - `mysql -u root -p < database/schema/001_initial_schema.sql`
2. Cargar datos demo + usuario ROOT:
   - `mysql -u root -p < database/seeders/001_demo_data.sql`
3. Aplicar migraciones incrementales:
   - `mysql -u root -p < database/migrations/002_add_match_stats.sql`
   - `mysql -u root -p < database/migrations/003_add_referees_module.sql`
   - `mysql -u root -p < database/migrations/005_add_root_role.sql`
   - `mysql -u root -p < database/migrations/006_add_settings_and_audit_indexes.sql`
4. Aplicar updates:
   - `mysql -u root -p < database/updates/004_add_live_score_fields.sql`
5. Levantar servidor:
   - `php -S 0.0.0.0:8000 -t public`

## Credenciales demo
- ROOT: `root@system.local` / `Root12345!` (**cambiar al primer inicio**).
- Super admin: `admin@tdmpro.test` / `secret123`
- Admin organización: `org@tdmpro.test` / `secret123`
