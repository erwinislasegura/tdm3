USE tdm3;
INSERT IGNORE INTO roles (name,description) VALUES ('root','Control absoluto del sistema');
INSERT IGNORE INTO users (name,email,password,status,created_at,updated_at)
VALUES ('ROOT','root@system.local','$2y$12$snBUTlipdR7tMzZhVrpYaejjb/8tqeknACXChEabcRlN.6Z8gzYUi','active',NOW(),NOW());
INSERT IGNORE INTO user_roles (user_id,role_id)
SELECT u.id, r.id FROM users u JOIN roles r ON r.name='root' WHERE u.email='root@system.local';
