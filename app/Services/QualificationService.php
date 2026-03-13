<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class QualificationService
{
    public function closeGroupsAndClassify(int $formatId, int $qualifiedPerGroup, int $bestThirdSlots = 0): array
    {
        $db = Database::getConnection();
        $check = $db->prepare('SELECT COUNT(*) c FROM group_matches WHERE format_id=? AND status NOT IN ("finished","walkover")');
        $check->execute([$formatId]);
        if ((int)$check->fetch()['c'] > 0) {
            throw new \RuntimeException('Existen partidos de grupo sin cerrar.');
        }

        $db->prepare('DELETE FROM qualified_players WHERE format_id=?')->execute([$formatId]);
        $groups = $db->prepare('SELECT id FROM `groups` WHERE phase_id IS NULL AND tournament_id=(SELECT tournament_id FROM competition_formats WHERE id=?)');
        $groups->execute([$formatId]);
        $qualified = [];
        foreach ($groups->fetchAll() as $g) {
            $stmt = $db->prepare('SELECT * FROM group_standings WHERE group_id=? ORDER BY position ASC');
            $stmt->execute([(int)$g['id']]);
            $rows = $stmt->fetchAll();
            for ($i = 0; $i < min($qualifiedPerGroup, count($rows)); $i++) {
                $qualified[] = ['player_id' => (int)$rows[$i]['player_id'], 'group_id' => (int)$g['id'], 'position' => $i + 1, 'type' => 'group_position'];
            }
            if ($bestThirdSlots > 0 && isset($rows[2])) {
                $qualified[] = ['player_id' => (int)$rows[2]['player_id'], 'group_id' => (int)$g['id'], 'position' => 3, 'type' => 'third_candidate', 'metric' => (int)$rows[2]['match_points']];
            }
        }

        if ($bestThirdSlots > 0) {
            $third = array_values(array_filter($qualified, static fn(array $r): bool => $r['position'] === 3));
            usort($third, static fn(array $a, array $b): int => ($b['metric'] ?? 0) <=> ($a['metric'] ?? 0));
            $allowed = array_slice($third, 0, $bestThirdSlots);
            $allowedIds = array_column($allowed, 'player_id');
            $qualified = array_values(array_filter($qualified, static fn(array $r): bool => $r['position'] !== 3 || in_array($r['player_id'], $allowedIds, true)));
            foreach ($qualified as &$q) {
                if ($q['position'] === 3) {
                    $q['type'] = 'best_third';
                }
            }
        }

        $insert = $db->prepare('INSERT INTO qualified_players (format_id,player_id,group_id,qualification_position,qualification_type) VALUES (?,?,?,?,?)');
        foreach ($qualified as $q) {
            $insert->execute([$formatId, $q['player_id'], $q['group_id'], $q['position'], $q['type']]);
        }

        $db->prepare('UPDATE competition_formats SET status="groups_closed" WHERE id=?')->execute([$formatId]);
        return $qualified;
    }
}
