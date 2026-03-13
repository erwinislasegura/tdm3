USE tdm3;

INSERT IGNORE INTO roles (name, description) VALUES
('ROOT','Acceso absoluto del sistema'),
('SUPERADMIN','Administración global'),
('ADMIN_TORNEO','Gestión total del torneo'),
('ORGANIZADOR','Operación de inscripciones y cuadros'),
('ARBITRO','Carga y validación de sets'),
('OPERADOR_MESA','Gestión de mesa y live'),
('VISOR','Solo lectura');

INSERT IGNORE INTO permissions (name, module_name) VALUES
('live.view','live'),('live.manage','live'),
('players.manage','players'),('clubs.manage','clubs'),('categories.manage','categories'),
('registrations.manage','registrations'),('groups.manage','groups'),('sets.manage','sets'),
('ranking.manage','ranking'),('logs.view','logs');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.name IN ('root','ROOT');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r
JOIN permissions p ON p.name IN ('live.view','live.manage','tournament.manage','matches.view','matches.score','groups.view','groups.generate','knockout.generate','ranking.manage','registrations.manage')
WHERE r.name IN ('SUPERADMIN','ADMIN_TORNEO','ORGANIZADOR','OPERADOR_MESA');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r
JOIN permissions p ON p.name IN ('live.view','matches.view','groups.view','knockout.view','ranking.manage')
WHERE r.name IN ('ARBITRO','VISOR');
