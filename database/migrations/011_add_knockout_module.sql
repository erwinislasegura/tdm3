USE tdm3;

CREATE TABLE IF NOT EXISTS knockout_brackets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    format_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    draw_size INT NOT NULL,
    seeded_count INT NOT NULL DEFAULT 0,
    status VARCHAR(30) NOT NULL DEFAULT 'draft',
    locked_at DATETIME NULL,
    locked_by INT NULL,
    created_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    FOREIGN KEY (format_id) REFERENCES competition_formats(id)
);

CREATE TABLE IF NOT EXISTS knockout_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bracket_id INT NOT NULL,
    slot_number INT NOT NULL,
    seed_number INT NULL,
    player_id INT NULL,
    source_ref VARCHAR(40) NULL,
    is_bye TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_bracket_slot (bracket_id, slot_number),
    FOREIGN KEY (bracket_id) REFERENCES knockout_brackets(id),
    FOREIGN KEY (player_id) REFERENCES players(id)
);

CREATE TABLE IF NOT EXISTS knockout_matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bracket_id INT NOT NULL,
    round_number INT NOT NULL,
    match_number INT NOT NULL,
    slot_a INT NOT NULL,
    slot_b INT NOT NULL,
    player_a_id INT NULL,
    player_b_id INT NULL,
    winner_player_id INT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'scheduled',
    score_summary VARCHAR(80) NULL,
    notes TEXT NULL,
    scheduled_at DATETIME NULL,
    table_number INT NULL,
    referee_id INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_knockout_match (bracket_id, round_number, match_number),
    FOREIGN KEY (bracket_id) REFERENCES knockout_brackets(id)
);
