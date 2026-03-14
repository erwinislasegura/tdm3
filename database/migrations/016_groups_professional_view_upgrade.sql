USE tdm3;

ALTER TABLE `groups`
    ADD COLUMN order_index INT NOT NULL DEFAULT 1,
    ADD COLUMN status VARCHAR(30) NOT NULL DEFAULT 'draft',
    ADD COLUMN is_locked TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN deleted_at DATETIME NULL;

ALTER TABLE group_players
    ADD COLUMN position_final INT NULL,
    ADD COLUMN qualified TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE group_matches
    ADD COLUMN sets_won_a INT NOT NULL DEFAULT 0,
    ADD COLUMN sets_won_b INT NOT NULL DEFAULT 0,
    ADD COLUMN is_walkover TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE group_standings
    ADD COLUMN sets_for INT NOT NULL DEFAULT 0,
    ADD COLUMN sets_against INT NOT NULL DEFAULT 0,
    ADD COLUMN sets_ratio DECIMAL(10,4) NOT NULL DEFAULT 0,
    ADD COLUMN qualified TINYINT(1) NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS qualifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    format_id INT NOT NULL,
    phase_id INT NULL,
    group_id INT NULL,
    player_id INT NOT NULL,
    qualification_position INT NOT NULL,
    qualification_type VARCHAR(40) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_qualification_player (format_id, player_id),
    FOREIGN KEY (format_id) REFERENCES competition_formats(id),
    FOREIGN KEY (group_id) REFERENCES `groups`(id),
    FOREIGN KEY (player_id) REFERENCES players(id)
);

INSERT INTO permissions (name,module) VALUES
('groups.lock','groups'),
('matches.edit','matches'),
('standings.view','standings'),
('standings.recalculate','standings'),
('qualifications.generate','qualifications')
ON DUPLICATE KEY UPDATE module=VALUES(module);

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.name IN (
    'groups.view','groups.generate','groups.edit','groups.close','groups.lock',
    'matches.view','matches.score','matches.edit','standings.view','standings.recalculate',
    'qualifications.generate','knockout.generate'
)
WHERE r.name IN ('root','super_admin','tournament_organizer');
