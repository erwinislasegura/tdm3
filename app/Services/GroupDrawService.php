<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class GroupDrawService
{
    public function generate(int $formatId): array
    {
        $db = Database::getConnection();
        $formatStmt = $db->prepare('SELECT * FROM competition_formats WHERE id=? AND deleted_at IS NULL');
        $formatStmt->execute([$formatId]);
        $format = $formatStmt->fetch();
        if (!$format) {
            throw new \RuntimeException('Formato no encontrado');
        }

        $playersStmt = $db->prepare('SELECT p.id as player_id, p.ranking_points,
            COALESCE(c.name,"") club, COALESCE(o.name,"") association, COALESCE(o.name,"") federation
            FROM registrations r
            INNER JOIN players p ON p.id=r.player_id
            LEFT JOIN clubs c ON c.id=p.club_id
            LEFT JOIN organizations o ON o.id=c.organization_id
            WHERE r.tournament_id=? AND r.status="approved" AND p.status="active"
            ORDER BY p.ranking_points DESC, p.id ASC');
        $playersStmt->execute([(int)$format['tournament_id']]);
        $players = $playersStmt->fetchAll();

        if (count($players) < (int)$format['group_count']) {
            throw new \RuntimeException('Jugadores insuficientes para la cantidad de grupos.');
        }

        $groupCount = (int)$format['group_count'];
        $groupNames = range('A', 'Z');
        $db->beginTransaction();
        try {
            $db->prepare('DELETE gm FROM group_matches gm INNER JOIN `groups` g ON g.id=gm.group_id WHERE gm.format_id=?')->execute([$formatId]);
            $db->prepare('DELETE gp FROM group_players gp INNER JOIN `groups` g ON g.id=gp.group_id WHERE gp.format_id=?')->execute([$formatId]);
            $db->prepare('DELETE FROM group_standings WHERE format_id=?')->execute([$formatId]);
            $db->prepare('DELETE FROM `groups` WHERE tournament_id=? AND phase_id IS NULL AND name LIKE "Auto-%"')->execute([(int)$format['tournament_id']]);

            $groupIds = [];
            $insGroup = $db->prepare('INSERT INTO `groups` (tournament_id,phase_id,name) VALUES (?,NULL,?)');
            for ($i = 0; $i < $groupCount; $i++) {
                $name = 'Auto-' . ($groupNames[$i] ?? ('G' . ($i + 1)));
                $insGroup->execute([(int)$format['tournament_id'], $name]);
                $groupIds[] = (int)$db->lastInsertId();
            }

            $groups = array_fill_keys($groupIds, []);
            $dir = 1; $idx = 0;
            foreach ($players as $p) {
                $groups[$groupIds[$idx]][] = (int)$p['player_id'];
                if ($dir === 1) {
                    if ($idx === $groupCount - 1) { $dir = -1; } else { $idx++; }
                } else {
                    if ($idx === 0) { $dir = 1; } else { $idx--; }
                }
            }

            $playerMap = [];
            foreach ($players as $p) {
                $playerMap[(int)$p['player_id']] = ['club' => $p['club'], 'association' => $p['association'], 'federation' => $p['federation']];
            }
            $sep = (new AssociationSeparationService())->optimize($groups, $playerMap, (string)$format['separation_rule']);
            $groups = $sep['groups'];

            $insertGp = $db->prepare('INSERT INTO group_players (format_id,group_id,player_id,seed_number,ranking_position,source_tag) VALUES (?,?,?,?,?,?)');
            $insertMatch = $db->prepare('INSERT INTO group_matches (format_id,group_id,player_a_id,player_b_id,status) VALUES (?,?,?,?,"scheduled")');
            foreach ($groups as $gid => $playerIds) {
                foreach (array_values($playerIds) as $rankIdx => $pid) {
                    $insertGp->execute([$formatId, $gid, $pid, $rankIdx + 1, $rankIdx + 1, 'auto']);
                }
                $n = count($playerIds);
                for ($i = 0; $i < $n; $i++) {
                    for ($j = $i + 1; $j < $n; $j++) {
                        $insertMatch->execute([$formatId, $gid, $playerIds[$i], $playerIds[$j]]);
                    }
                }
            }

            $db->prepare('UPDATE competition_formats SET status="groups_generated", updated_by=? WHERE id=?')
                ->execute([auth_user()['id'] ?? null, $formatId]);

            $db->prepare('INSERT INTO draw_logs (format_id,log_type,payload,created_by) VALUES (?,?,?,?)')
                ->execute([$formatId, 'group_generation', json_encode(['swaps' => $sep['swaps'], 'warnings' => $sep['warnings']]), auth_user()['id'] ?? null]);

            $db->commit();
            return ['groups' => $groups, 'warnings' => $sep['warnings']];
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
