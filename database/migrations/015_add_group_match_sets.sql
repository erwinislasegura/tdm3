USE tdm3;

CREATE TABLE IF NOT EXISTS group_match_sets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_match_id INT NOT NULL,
    set_number INT NOT NULL,
    player_a_points INT NOT NULL,
    player_b_points INT NOT NULL,
    winner_player_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_group_match_set (group_match_id, set_number),
    FOREIGN KEY (group_match_id) REFERENCES group_matches(id) ON DELETE CASCADE,
    FOREIGN KEY (winner_player_id) REFERENCES players(id)
);
