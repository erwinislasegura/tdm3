USE tdm3;

INSERT INTO roles (name,description) VALUES
('root','Control absoluto del sistema'),('super_admin','Control total'),('organization_admin','Admin de organización'),('tournament_organizer','Gestión de torneos'),('referee','Arbitraje'),('table_operator','Mesa técnica'),('viewer','Solo lectura'),
('ROOT','Control absoluto del sistema'),('SUPERADMIN','Control total profesional'),('ADMIN_TORNEO','Admin de torneo'),('ORGANIZADOR','Operador torneo'),('ARBITRO','Arbitraje competitivo'),('OPERADOR_MESA','Mesa técnica live'),('VISOR','Solo lectura live');

INSERT INTO users (name,email,password,status,created_at,updated_at) VALUES
('ROOT','root@system.local','$2y$12$CGt6D5iujZUIZS5dhzqPqu0kvtlDkrjUf3vX8He8DDtMc98fpHhIK','active',NOW(),NOW()),
('Super Admin','admin@tdmpro.test','$2y$10$VY8ycQjB2D8D6wI6D4jMcuJfPcbvVPKM8qfZOJzN4Qm7FfupkM9d6','active',NOW(),NOW()),
('Operador Mesa 1','mesa1@tdmpro.test','$2y$10$VY8ycQjB2D8D6wI6D4jMcuJfPcbvVPKM8qfZOJzN4Qm7FfupkM9d6','active',NOW(),NOW()),
('Operador Mesa 2','mesa2@tdmpro.test','$2y$10$VY8ycQjB2D8D6wI6D4jMcuJfPcbvVPKM8qfZOJzN4Qm7FfupkM9d6','active',NOW(),NOW());
INSERT INTO user_roles VALUES (1,1),(2,2),(3,6),(4,6);

INSERT INTO organizations (name,type,description,city,email,phone,primary_color,secondary_color,status,created_at,updated_at) VALUES
('Federación Metropolitana TM','federation','Federación oficial regional.','Santiago','federacion@tm.cl','+56911111111','#0f172a','#06b6d4','active',NOW(),NOW()),
('Asociación Sur Ping Pong','association','Asociación comunitaria deportiva.','Concepción','sur@tm.cl','+56922222222','#1e293b','#f97316','active',NOW(),NOW());

INSERT INTO clubs (organization_id,name,description,city,status,created_at) VALUES
(1,'Club Smash Elite','Club competitivo alto rendimiento','Santiago','active',NOW()),
(1,'Academia TopSpin','Formación juvenil','Santiago','active',NOW()),
(2,'Club Raqueta Sur','Semillero regional','Concepción','active',NOW()),
(2,'Comunidad PingLab','Entrenamiento recreativo y competitivo','Temuco','active',NOW());

INSERT INTO people (first_name,last_name,document,birth_date,gender,nationality,created_at) VALUES
('Matías','Rojas','11111111-1','2001-01-10','M','CL',NOW()),('Nicolás','Pérez','11111111-2','2002-02-10','M','CL',NOW()),('Daniela','Ruiz','11111111-3','2000-03-10','F','CL',NOW()),('Camila','Soto','11111111-4','2004-04-10','F','CL',NOW()),('Cristóbal','Mella','11111111-5','1998-05-10','M','CL',NOW()),('Ignacia','Fuentes','11111111-6','2003-06-10','F','CL',NOW()),('Tomás','Vera','11111111-7','2001-07-10','M','CL',NOW()),('Felipe','Navarro','11111111-8','1999-08-10','M','CL',NOW()),('Joaquín','Lagos','11111111-9','2005-09-10','M','CL',NOW()),('Valentina','Jara','11111111-0','2006-10-10','F','CL',NOW()),('Sebastián','Araya','22222222-1','2000-11-10','M','CL',NOW()),('Diego','Contreras','22222222-2','2002-12-10','M','CL',NOW()),('Catalina','Méndez','22222222-3','2001-01-11','F','CL',NOW()),('Antonia','Silva','22222222-4','2004-02-11','F','CL',NOW()),('Benjamín','Farías','22222222-5','2003-03-11','M','CL',NOW()),('Martina','Garrido','22222222-6','2005-04-11','F','CL',NOW()),('Agustín','Riquelme','22222222-7','2002-05-11','M','CL',NOW()),('Isidora','Pino','22222222-8','2006-06-11','F','CL',NOW()),('Vicente','Muñoz','22222222-9','2001-07-11','M','CL',NOW()),('Florencia','Leiva','22222222-0','2000-08-11','F','CL',NOW()),('Emilio','Cáceres','33333333-1','2004-02-10','M','CL',NOW()),('Amanda','Reyes','33333333-2','2005-03-10','F','CL',NOW()),('Bruno','Salas','33333333-3','2003-04-10','M','CL',NOW()),('Renata','Parra','33333333-4','2004-05-10','F','CL',NOW()),
('Álvaro','Jeldes','44444444-1','1990-02-10','M','CL',NOW()),('Paula','Gómez','44444444-2','1989-03-10','F','CL',NOW());

INSERT INTO players (person_id,club_id,current_category,ranking_points,status,created_at) VALUES
(1,1,'Todo Competidor',1200,'active',NOW()),(2,1,'Todo Competidor',1150,'active',NOW()),(3,2,'Damas Open',1100,'active',NOW()),(4,2,'Sub21',980,'active',NOW()),(5,1,'Todo Competidor',970,'active',NOW()),(6,2,'Damas Open',940,'active',NOW()),(7,3,'Todo Competidor',900,'active',NOW()),(8,3,'Todo Competidor',880,'active',NOW()),(9,3,'Sub18',850,'active',NOW()),(10,4,'Sub18',830,'active',NOW()),(11,1,'Todo Competidor',820,'active',NOW()),(12,2,'Todo Competidor',810,'active',NOW()),(13,2,'Damas Open',790,'active',NOW()),(14,4,'Sub21',780,'active',NOW()),(15,4,'Todo Competidor',760,'active',NOW()),(16,4,'Sub18',740,'active',NOW()),(17,3,'Todo Competidor',720,'active',NOW()),(18,4,'Sub18',710,'active',NOW()),(19,1,'Todo Competidor',700,'active',NOW()),(20,2,'Damas Open',690,'active',NOW()),(21,1,'Sub21',680,'active',NOW()),(22,2,'Sub21',670,'active',NOW()),(23,3,'Todo Competidor',660,'active',NOW()),(24,4,'Todo Competidor',650,'active',NOW());
INSERT INTO referees (person_id,organization_id,license_code,experience_years,status) VALUES (25,1,'REF-001',8,'active'),(26,2,'REF-002',6,'active');

INSERT INTO tournaments (organization_id,name,description,start_date,end_date,city,venue,status,is_public,created_at,updated_at) VALUES
(1,'Torneo Demo Profesional TDM','Flujo completo grupos + llaves + ranking live.','2026-04-10','2026-04-12','Santiago','Centro Deportivo Central','active',1,NOW(),NOW());
INSERT INTO tournament_categories (tournament_id,name,gender_scope,age_min,age_max,level_scope) VALUES
(1,'Todo Competidor','mixto',NULL,NULL,'avanzado'),(1,'Sub21','mixto',15,21,'intermedio'),(1,'Damas Open','damas',NULL,NULL,'open');
INSERT INTO tournament_modalities (tournament_id,name,format_type,max_slots) VALUES (1,'Individual','mixed_groups_knockout',64);
INSERT INTO tournament_phases (tournament_id,name,phase_type,sort_order) VALUES (1,'Fase de grupos','round_robin',1),(1,'Llaves finales','single_elimination',2);

INSERT INTO registrations (tournament_id,player_id,category_id,modality_id,status,payment_status,created_at)
SELECT 1,id,1,1,'approved','paid',NOW() FROM players WHERE id BETWEEN 1 AND 16;

INSERT INTO `groups` (tournament_id,phase_id,name) VALUES (1,1,'Grupo A'),(1,1,'Grupo B'),(1,1,'Grupo C'),(1,1,'Grupo D');
INSERT INTO group_members (group_id,player_id,seed_number,points,wins,losses) VALUES
(1,1,1,6,2,0),(1,2,2,3,1,1),(1,3,3,3,1,1),(1,4,4,0,0,2),
(2,5,1,6,2,0),(2,6,2,3,1,1),(2,7,3,3,1,1),(2,8,4,0,0,2),
(3,9,1,6,2,0),(3,10,2,3,1,1),(3,11,3,3,1,1),(3,12,4,0,0,2),
(4,13,1,6,2,0),(4,14,2,3,1,1),(4,15,3,3,1,1),(4,16,4,0,0,2);

INSERT INTO matches (tournament_id,phase,group_id,bracket_round,player_a_id,player_b_id,player_a_name,player_b_name,table_number,match_time,referee_id,status,winner_player_id,winner_name,score_summary,live_updated_at)
VALUES
(1,'Grupos',1,'R1',1,2,'Matías Rojas','Nicolás Pérez',1,'2026-04-10 09:00:00',1,'finished',1,'Matías Rojas','3-1',NOW()),
(1,'Grupos',2,'R1',5,6,'Cristóbal Mella','Ignacia Fuentes',2,'2026-04-10 09:00:00',2,'in_progress',NULL,NULL,'1-1',NOW()),
(1,'Grupos',3,'R1',9,10,'Joaquín Lagos','Valentina Jara',3,'2026-04-10 09:00:00',1,'scheduled',NULL,NULL,NULL,NOW()),
(1,'Llaves',NULL,'QF',1,6,'Matías Rojas','Ignacia Fuentes',1,'2026-04-12 16:00:00',1,'scheduled',NULL,NULL,NULL,NOW());

INSERT INTO match_sets (match_id,set_number,player_a_points,player_b_points,winner_side) VALUES
(1,1,11,6,'A'),(1,2,9,11,'B'),(1,3,11,7,'A'),(1,4,11,4,'A'),
(2,1,11,8,'A'),(2,2,8,11,'B');

INSERT INTO rankings (organization_id,category,modality,gender_scope,player_id,position,points,last_update)
SELECT 1,'Todo Competidor','Individual','mixto',id,id,1300-(id*20),NOW() FROM players WHERE id <= 16;

INSERT INTO ranking_movements (ranking_id,previous_position,new_position,points_delta,reason_text,created_at)
SELECT id,position+1,position,15,'Actualización post torneo demo',NOW() FROM rankings WHERE player_id <= 8;

INSERT INTO audit_logs (user_id,action,module_name,description,ip_address,created_at) VALUES
(1,'seed','system','Carga inicial demo profesional', '127.0.0.1', NOW()),
(2,'create','tournaments','Creación Torneo Demo Profesional TDM', '127.0.0.1', NOW()),
(3,'live_update','matches','Actualización live marcador mesa 2', '127.0.0.1', NOW());

INSERT INTO settings (setting_key,setting_value,updated_at) VALUES
('platform_name','TDM Pro Manager',NOW()),('contact_email','contacto@tdmpro.test',NOW()),('accent_color','#06b6d4',NOW()),('primary_color','#0f172a',NOW()),('maintenance_mode','0',NOW()),('timezone','America/Santiago',NOW());
