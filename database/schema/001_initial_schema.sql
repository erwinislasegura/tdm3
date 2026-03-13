CREATE DATABASE IF NOT EXISTS tdm3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tdm3;

CREATE TABLE roles (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(50) UNIQUE NOT NULL,description VARCHAR(255));
CREATE TABLE permissions (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(80) UNIQUE NOT NULL,module_name VARCHAR(80));
CREATE TABLE role_permissions (role_id INT,permission_id INT,PRIMARY KEY(role_id,permission_id),FOREIGN KEY (role_id) REFERENCES roles(id),FOREIGN KEY (permission_id) REFERENCES permissions(id));

CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120) NOT NULL,email VARCHAR(120) UNIQUE NOT NULL,password VARCHAR(255) NOT NULL,status ENUM('active','inactive') DEFAULT 'active',created_at DATETIME,updated_at DATETIME);
CREATE TABLE user_roles (user_id INT,role_id INT,PRIMARY KEY(user_id,role_id),FOREIGN KEY (user_id) REFERENCES users(id),FOREIGN KEY (role_id) REFERENCES roles(id));

CREATE TABLE organizations (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(150) NOT NULL,type VARCHAR(50),logo VARCHAR(255),description TEXT,city VARCHAR(100),address VARCHAR(150),email VARCHAR(120),phone VARCHAR(40),primary_color VARCHAR(20),secondary_color VARCHAR(20),status ENUM('active','inactive') DEFAULT 'active',created_at DATETIME,updated_at DATETIME);
CREATE TABLE organization_members (organization_id INT,user_id INT,membership_role VARCHAR(50),PRIMARY KEY(organization_id,user_id),FOREIGN KEY (organization_id) REFERENCES organizations(id),FOREIGN KEY (user_id) REFERENCES users(id));
CREATE TABLE clubs (id INT AUTO_INCREMENT PRIMARY KEY,organization_id INT,name VARCHAR(120),description TEXT,city VARCHAR(100),status ENUM('active','inactive') DEFAULT 'active',created_at DATETIME,FOREIGN KEY (organization_id) REFERENCES organizations(id));

CREATE TABLE people (id INT AUTO_INCREMENT PRIMARY KEY,first_name VARCHAR(80),last_name VARCHAR(80),document VARCHAR(40),birth_date DATE,gender VARCHAR(10),nationality VARCHAR(60),photo VARCHAR(255),notes TEXT,created_at DATETIME);
CREATE TABLE players (id INT AUTO_INCREMENT PRIMARY KEY,person_id INT UNIQUE,club_id INT NULL,current_category VARCHAR(50),ranking_points INT DEFAULT 0,status ENUM('active','inactive') DEFAULT 'active',created_at DATETIME,FOREIGN KEY (person_id) REFERENCES people(id),FOREIGN KEY (club_id) REFERENCES clubs(id));
CREATE TABLE coaches (id INT AUTO_INCREMENT PRIMARY KEY,person_id INT UNIQUE,organization_id INT,experience_years INT DEFAULT 0,availability VARCHAR(120),status ENUM('active','inactive') DEFAULT 'active',FOREIGN KEY (person_id) REFERENCES people(id),FOREIGN KEY (organization_id) REFERENCES organizations(id));
CREATE TABLE referees (id INT AUTO_INCREMENT PRIMARY KEY,person_id INT UNIQUE,organization_id INT,license_code VARCHAR(60),experience_years INT DEFAULT 0,status ENUM('active','inactive') DEFAULT 'active',FOREIGN KEY (person_id) REFERENCES people(id),FOREIGN KEY (organization_id) REFERENCES organizations(id));

CREATE TABLE venues (id INT AUTO_INCREMENT PRIMARY KEY,organization_id INT,name VARCHAR(120),address VARCHAR(180),city VARCHAR(80),status ENUM('active','inactive') DEFAULT 'active',FOREIGN KEY (organization_id) REFERENCES organizations(id));
CREATE TABLE venue_tables (id INT AUTO_INCREMENT PRIMARY KEY,venue_id INT,table_number INT,status ENUM('available','occupied','maintenance') DEFAULT 'available',FOREIGN KEY (venue_id) REFERENCES venues(id));

CREATE TABLE tournaments (id INT AUTO_INCREMENT PRIMARY KEY,organization_id INT,name VARCHAR(140),description TEXT,start_date DATE,end_date DATE,city VARCHAR(80),venue VARCHAR(120),rules TEXT,status ENUM('draft','published','active','finished','cancelled') DEFAULT 'draft',is_public TINYINT(1) DEFAULT 1,cover_image VARCHAR(255),created_at DATETIME,updated_at DATETIME,FOREIGN KEY (organization_id) REFERENCES organizations(id));
CREATE TABLE tournament_categories (id INT AUTO_INCREMENT PRIMARY KEY,tournament_id INT,name VARCHAR(80),gender_scope VARCHAR(20),age_min INT NULL,age_max INT NULL,level_scope VARCHAR(30),FOREIGN KEY (tournament_id) REFERENCES tournaments(id));
CREATE TABLE tournament_modalities (id INT AUTO_INCREMENT PRIMARY KEY,tournament_id INT,name VARCHAR(60),format_type VARCHAR(60),max_slots INT,FOREIGN KEY (tournament_id) REFERENCES tournaments(id));
CREATE TABLE tournament_phases (id INT AUTO_INCREMENT PRIMARY KEY,tournament_id INT,name VARCHAR(80),phase_type VARCHAR(40),sort_order INT,FOREIGN KEY (tournament_id) REFERENCES tournaments(id));

CREATE TABLE registrations (id INT AUTO_INCREMENT PRIMARY KEY,tournament_id INT,player_id INT,category_id INT NULL,modality_id INT NULL,status ENUM('pending','approved','rejected') DEFAULT 'pending',payment_status ENUM('na','pending','paid') DEFAULT 'na',created_at DATETIME,UNIQUE KEY uq_reg (tournament_id,player_id),FOREIGN KEY (tournament_id) REFERENCES tournaments(id),FOREIGN KEY (player_id) REFERENCES players(id));
CREATE TABLE `groups` (id INT AUTO_INCREMENT PRIMARY KEY,tournament_id INT,phase_id INT,name VARCHAR(40),FOREIGN KEY (tournament_id) REFERENCES tournaments(id),FOREIGN KEY (phase_id) REFERENCES tournament_phases(id));
CREATE TABLE group_members (group_id INT,player_id INT,seed_number INT NULL,points INT DEFAULT 0,wins INT DEFAULT 0,losses INT DEFAULT 0,PRIMARY KEY(group_id,player_id),FOREIGN KEY (group_id) REFERENCES `groups`(id),FOREIGN KEY (player_id) REFERENCES players(id));

CREATE TABLE matches (id INT AUTO_INCREMENT PRIMARY KEY,tournament_id INT,phase VARCHAR(80),group_id INT NULL,bracket_round VARCHAR(50),player_a_id INT NULL,player_b_id INT NULL,player_a_name VARCHAR(120),player_b_name VARCHAR(120),table_number INT NULL,match_time DATETIME NULL,referee_id INT NULL,status ENUM('scheduled','in_progress','finished','walkover','suspended') DEFAULT 'scheduled',winner_player_id INT NULL,winner_name VARCHAR(120),score_summary VARCHAR(80),notes TEXT,is_third_place TINYINT(1) DEFAULT 0,live_updated_at DATETIME NULL,FOREIGN KEY (tournament_id) REFERENCES tournaments(id),FOREIGN KEY (group_id) REFERENCES `groups`(id),FOREIGN KEY (referee_id) REFERENCES referees(id));
CREATE TABLE match_sets (id INT AUTO_INCREMENT PRIMARY KEY,match_id INT,set_number INT,player_a_points INT,player_b_points INT,winner_side ENUM('A','B'),FOREIGN KEY (match_id) REFERENCES matches(id));

CREATE TABLE ranking_rules (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120),position_points JSON,participation_points INT DEFAULT 0,bonus_rules JSON NULL,is_elo_enabled TINYINT(1) DEFAULT 0,created_at DATETIME);
CREATE TABLE rankings (id INT AUTO_INCREMENT PRIMARY KEY,organization_id INT NULL,category VARCHAR(50),modality VARCHAR(50),gender_scope VARCHAR(20),player_id INT,position INT,points INT,last_update DATETIME,FOREIGN KEY (organization_id) REFERENCES organizations(id),FOREIGN KEY (player_id) REFERENCES players(id));
CREATE TABLE ranking_movements (id INT AUTO_INCREMENT PRIMARY KEY,ranking_id INT,previous_position INT,new_position INT,points_delta INT,reason_text VARCHAR(255),created_at DATETIME,FOREIGN KEY (ranking_id) REFERENCES rankings(id));

CREATE TABLE notifications (id INT AUTO_INCREMENT PRIMARY KEY,user_id INT,title VARCHAR(120),body TEXT,link_url VARCHAR(255),is_read TINYINT(1) DEFAULT 0,created_at DATETIME,FOREIGN KEY (user_id) REFERENCES users(id));
CREATE TABLE documents (id INT AUTO_INCREMENT PRIMARY KEY,tournament_id INT NULL,organization_id INT NULL,title VARCHAR(120),file_path VARCHAR(255),doc_type VARCHAR(50),created_at DATETIME,FOREIGN KEY (tournament_id) REFERENCES tournaments(id),FOREIGN KEY (organization_id) REFERENCES organizations(id));
CREATE TABLE audit_logs (id BIGINT AUTO_INCREMENT PRIMARY KEY,user_id INT,action VARCHAR(80),module_name VARCHAR(80),description TEXT,ip_address VARCHAR(50),created_at DATETIME,FOREIGN KEY (user_id) REFERENCES users(id));
CREATE TABLE settings (id INT AUTO_INCREMENT PRIMARY KEY,setting_key VARCHAR(100) UNIQUE,setting_value TEXT,organization_id INT NULL,updated_at DATETIME,FOREIGN KEY (organization_id) REFERENCES organizations(id));

CREATE INDEX idx_matches_tournament ON matches(tournament_id,status);
CREATE INDEX idx_registrations_tournament ON registrations(tournament_id,status);
CREATE INDEX idx_rankings_org ON rankings(organization_id,category,position);
