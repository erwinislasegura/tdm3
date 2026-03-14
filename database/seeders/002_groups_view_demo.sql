USE tdm3;

-- Fase con grupos completos e incompletos
INSERT INTO competition_formats (tournament_id, category_name, registered_players, group_count, group_size, qualified_per_group, best_third_slots, status, created_by, updated_by)
VALUES (1, 'Todo Competidor - Vista Grupos Demo', 12, 2, 6, 2, 0, 'groups_generated', 1, 1);

SET @format_id := LAST_INSERT_ID();

INSERT INTO `groups` (tournament_id, phase_id, name, order_index, status, is_locked)
VALUES (1, 1, 'Grupo A Demo', 1, 'active', 0),
       (1, 1, 'Grupo B Demo', 2, 'active', 0);

SET @group_a := LAST_INSERT_ID() - 1;
SET @group_b := LAST_INSERT_ID();

INSERT INTO group_players (format_id, group_id, player_id, seed_number, ranking_position, source_tag)
VALUES
(@format_id, @group_a, 1, 1, 1, 'demo'),(@format_id, @group_a, 2, 2, 2, 'demo'),(@format_id, @group_a, 3, 3, 3, 'demo'),(@format_id, @group_a, 4, 4, 4, 'demo'),
(@format_id, @group_b, 5, 1, 5, 'demo'),(@format_id, @group_b, 6, 2, 6, 'demo'),(@format_id, @group_b, 7, 3, 7, 'demo'),(@format_id, @group_b, 8, 4, 8, 'demo');

INSERT INTO group_matches (format_id, group_id, player_a_id, player_b_id, table_number, scheduled_at, status, winner_player_id, sets_json, points_a, points_b, sets_won_a, sets_won_b)
VALUES
(@format_id, @group_a, 1, 2, 1, NOW(), 'finished', 1, '[{"a":11,"b":7},{"a":11,"b":8},{"a":11,"b":9}]', 33, 24, 3, 0),
(@format_id, @group_a, 3, 4, 2, NOW(), 'finished', 3, '[{"a":11,"b":9},{"a":9,"b":11},{"a":11,"b":6},{"a":11,"b":7}]', 42, 33, 3, 1),
(@format_id, @group_a, 1, 3, 1, DATE_ADD(NOW(), INTERVAL 1 HOUR), 'scheduled', NULL, NULL, 0, 0, 0, 0),
(@format_id, @group_b, 5, 6, 3, DATE_ADD(NOW(), INTERVAL 30 MINUTE), 'in_game', NULL, '[{"a":11,"b":8},{"a":8,"b":11}]', 19, 19, 1, 1),
(@format_id, @group_b, 7, 8, 4, DATE_ADD(NOW(), INTERVAL 2 HOUR), 'scheduled', NULL, NULL, 0, 0, 0, 0);

INSERT INTO group_match_sets (group_match_id, set_number, player_a_points, player_b_points, winner_player_id)
SELECT id, 1, 11, 7, 1 FROM group_matches WHERE format_id=@format_id AND group_id=@group_a AND player_a_id=1 AND player_b_id=2;

-- Fase sin grupos para probar empty state
INSERT INTO competition_formats (tournament_id, category_name, registered_players, group_count, group_size, qualified_per_group, best_third_slots, status, created_by, updated_by)
VALUES (1, 'Sub21 - Sin Grupos Demo', 0, 2, 4, 2, 0, 'draft', 1, 1);
