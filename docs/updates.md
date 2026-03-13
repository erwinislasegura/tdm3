# Guía de actualizaciones SQL

Orden recomendado para dejar la plataforma profesional de torneos + ranking:
1. `database/migrations/002_add_match_stats.sql`
2. `database/migrations/003_add_referees_module.sql`
3. `database/updates/004_add_live_score_fields.sql`
4. `database/migrations/005_add_root_role.sql`
5. `database/migrations/006_add_settings_and_audit_indexes.sql`
6. `database/migrations/010_add_group_stage_module.sql`
7. `database/migrations/011_add_knockout_module.sql`
8. `database/migrations/012_add_group_tiebreak_logic.sql`
9. `database/migrations/013_add_rbac_permissions_for_draws.sql`
10. `database/migrations/014_professional_platform_upgrade.sql`

Luego cargar datos demo profesionales:
- `database/seeders/001_demo_data.sql`

Cobertura funcional esperada tras migrar y seedear:
- Flujo completo: torneo -> inscripciones -> grupos -> partidos/sets -> standings -> clasificación -> llave -> knockout -> live -> ranking.
- Persistencia en MySQL para usuarios, roles/permisos, jugadores, clubes, torneos, categorías, grupos, partidos, sets, ranking, movimientos y auditoría.
- Live scoring con vistas pública y admin usando fetch/AJAX contra datos reales de DB.
