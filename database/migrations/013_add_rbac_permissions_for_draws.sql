USE tdm3;

INSERT IGNORE INTO roles (name, description) VALUES
('superadmin','Super administrador global'),
('admin_torneo','Administrador de torneo'),
('organizador','Operación de cuadros y grupos'),
('arbitro','Carga y validación de partidos'),
('mesa_control','Gestión operativa en mesa de control'),
('visor','Solo lectura'),
('jugador','Acceso básico de jugador');

INSERT IGNORE INTO permissions (name, module_name) VALUES
('groups.view','groups'),('groups.create','groups'),('groups.edit','groups'),('groups.delete','groups'),('groups.generate','groups'),('groups.close','groups'),
('matches.view','matches'),('matches.create','matches'),('matches.edit','matches'),('matches.delete','matches'),('matches.score','matches'),
('knockout.view','knockout'),('knockout.create','knockout'),('knockout.edit','knockout'),('knockout.delete','knockout'),('knockout.generate','knockout'),
('tournament.manage','tournaments'),('settings.manage','settings'),('users.manage','users'),('roles.manage','roles'),('audit.view','audit');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.name = 'root';

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p ON p.name IN (
'groups.view','groups.create','groups.edit','groups.generate','groups.close',
'matches.view','matches.create','matches.edit','matches.score',
'knockout.view','knockout.create','knockout.edit','knockout.generate',
'tournament.manage','audit.view')
WHERE r.name IN ('superadmin','admin_torneo','organizador');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p ON p.name IN ('matches.view','matches.score','groups.view','knockout.view')
WHERE r.name IN ('arbitro','mesa_control');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p ON p.name IN ('groups.view','matches.view','knockout.view')
WHERE r.name IN ('visor','jugador');
