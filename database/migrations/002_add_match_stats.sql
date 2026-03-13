USE tdm3;
ALTER TABLE matches ADD COLUMN player_a_sets_won INT DEFAULT 0 AFTER score_summary;
ALTER TABLE matches ADD COLUMN player_b_sets_won INT DEFAULT 0 AFTER player_a_sets_won;
