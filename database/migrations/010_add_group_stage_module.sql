USE tdm3;

CREATE TABLE IF NOT EXISTS competition_formats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    category_name VARCHAR(100) NOT NULL,
    registered_players INT NOT NULL DEFAULT 0,
    group_count INT NOT NULL,
    group_size INT NOT NULL,
    qualified_per_group INT NOT NULL DEFAULT 1,
    advancement_mode VARCHAR(40) NOT NULL DEFAULT 'group_winners',
    best_third_slots INT NOT NULL DEFAULT 0,
    final_bracket_type VARCHAR(40) NOT NULL DEFAULT 'single_elimination',
    protected_seeds INT NOT NULL DEFAULT 8,
    ranking_criteria VARCHAR(40) NOT NULL DEFAULT 'system_ranking',
    separation_rule VARCHAR(40) NOT NULL DEFAULT 'club',
    generation_mode VARCHAR(40) NOT NULL DEFAULT 'automatic',
    allow_same_group_early_cross TINYINT(1) NOT NULL DEFAULT 0,
    allow_manual_edit_post_draw TINYINT(1) NOT NULL DEFAULT 1,
    status VARCHAR(30) NOT NULL DEFAULT 'draft',
    locked_at DATETIME NULL,
    locked_by INT NULL,
    created_by INT NULL,
    updated_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id)
);

CREATE TABLE IF NOT EXISTS format_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    format_id INT NOT NULL,
    rule_key VARCHAR(80) NOT NULL,
    rule_value TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_format_rule (format_id, rule_key),
    FOREIGN KEY (format_id) REFERENCES competition_formats(id)
);

CREATE TABLE IF NOT EXISTS group_players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    format_id INT NOT NULL,
    group_id INT NOT NULL,
    player_id INT NOT NULL,
    seed_number INT NULL,
    ranking_position INT NULL,
    source_tag VARCHAR(40) NULL,
    is_qualified TINYINT(1) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_group_player (group_id, player_id),
    FOREIGN KEY (format_id) REFERENCES competition_formats(id),
    FOREIGN KEY (group_id) REFERENCES `groups`(id),
    FOREIGN KEY (player_id) REFERENCES players(id)
);

CREATE TABLE IF NOT EXISTS group_matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    format_id INT NOT NULL,
    group_id INT NOT NULL,
    player_a_id INT NOT NULL,
    player_b_id INT NOT NULL,
    scheduled_at DATETIME NULL,
    table_number INT NULL,
    referee_id INT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'scheduled',
    winner_player_id INT NULL,
    walkover_side VARCHAR(1) NULL,
    notes TEXT NULL,
    sets_json JSON NULL,
    points_a INT NOT NULL DEFAULT 0,
    points_b INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (format_id) REFERENCES competition_formats(id),
    FOREIGN KEY (group_id) REFERENCES `groups`(id),
    FOREIGN KEY (player_a_id) REFERENCES players(id),
    FOREIGN KEY (player_b_id) REFERENCES players(id)
);

CREATE TABLE IF NOT EXISTS group_standings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    format_id INT NOT NULL,
    group_id INT NOT NULL,
    player_id INT NOT NULL,
    played INT NOT NULL DEFAULT 0,
    won INT NOT NULL DEFAULT 0,
    lost INT NOT NULL DEFAULT 0,
    match_points INT NOT NULL DEFAULT 0,
    games_for INT NOT NULL DEFAULT 0,
    games_against INT NOT NULL DEFAULT 0,
    game_ratio DECIMAL(10,4) NOT NULL DEFAULT 0,
    points_for INT NOT NULL DEFAULT 0,
    points_against INT NOT NULL DEFAULT 0,
    point_ratio DECIMAL(10,4) NOT NULL DEFAULT 0,
    position INT NULL,
    tie_break_note VARCHAR(255) NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_standing (group_id, player_id),
    FOREIGN KEY (format_id) REFERENCES competition_formats(id),
    FOREIGN KEY (group_id) REFERENCES `groups`(id),
    FOREIGN KEY (player_id) REFERENCES players(id)
);

CREATE TABLE IF NOT EXISTS classification_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    format_id INT NOT NULL,
    best_rank_criteria VARCHAR(40) NOT NULL DEFAULT 'match_points',
    bye_criteria VARCHAR(40) NOT NULL DEFAULT 'seeded_priority',
    avoid_same_group_early_round TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (format_id) REFERENCES competition_formats(id)
);

CREATE TABLE IF NOT EXISTS qualified_players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    format_id INT NOT NULL,
    player_id INT NOT NULL,
    group_id INT NULL,
    qualification_position INT NOT NULL,
    qualification_type VARCHAR(40) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_qualified_player (format_id, player_id),
    FOREIGN KEY (format_id) REFERENCES competition_formats(id),
    FOREIGN KEY (player_id) REFERENCES players(id),
    FOREIGN KEY (group_id) REFERENCES `groups`(id)
);

CREATE TABLE IF NOT EXISTS draw_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    format_id INT NOT NULL,
    log_type VARCHAR(40) NOT NULL,
    payload JSON NULL,
    created_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (format_id) REFERENCES competition_formats(id)
);
