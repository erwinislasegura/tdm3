<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class GroupStandingService
{
    public function recalculate(int $formatId, int $groupId): array
    {
        $db = Database::getConnection();
        $players = $db->prepare('SELECT gp.player_id,p.first_name,p.last_name FROM group_players gp
            INNER JOIN players pl ON pl.id=gp.player_id
            INNER JOIN people p ON p.id=pl.person_id
            WHERE gp.group_id=?');
        $players->execute([$groupId]);
        $rows = $players->fetchAll();

        $stats = [];
        foreach ($rows as $row) {
            $stats[(int)$row['player_id']] = [
                'player_id' => (int)$row['player_id'],
                'name' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
                'played' => 0, 'won' => 0, 'lost' => 0, 'match_points' => 0,
                'games_for' => 0, 'games_against' => 0, 'sets_for' => 0, 'sets_against' => 0, 'points_for' => 0, 'points_against' => 0,
            ];
        }

        $matches = $db->prepare('SELECT * FROM group_matches WHERE group_id=? AND status IN ("finished","walkover")');
        $matches->execute([$groupId]);
        foreach ($matches->fetchAll() as $m) {
            $a = (int)$m['player_a_id'];
            $b = (int)$m['player_b_id'];
            $sets = json_decode((string)($m['sets_json'] ?? '[]'), true) ?: [];
            $ga = 0; $gb = 0; $pa = 0; $pb = 0;
            foreach ($sets as $set) {
                $sa = (int)($set['a'] ?? 0); $sb = (int)($set['b'] ?? 0);
                $pa += $sa; $pb += $sb;
                if ($sa > $sb) { $ga++; } elseif ($sb > $sa) { $gb++; }
            }
            $winner = (int)($m['winner_player_id'] ?? 0);
            foreach ([$a, $b] as $pid) {
                $stats[$pid]['played']++;
            }
            if ($winner === $a) {
                $stats[$a]['won']++; $stats[$b]['lost']++;
                $stats[$a]['match_points'] += 2;
                $stats[$b]['match_points'] += $m['status'] === 'walkover' ? 0 : 1;
            } elseif ($winner === $b) {
                $stats[$b]['won']++; $stats[$a]['lost']++;
                $stats[$b]['match_points'] += 2;
                $stats[$a]['match_points'] += $m['status'] === 'walkover' ? 0 : 1;
            }

            $stats[$a]['games_for'] += $ga; $stats[$a]['games_against'] += $gb;
            $stats[$b]['games_for'] += $gb; $stats[$b]['games_against'] += $ga;
            $stats[$a]['sets_for'] += $ga; $stats[$a]['sets_against'] += $gb;
            $stats[$b]['sets_for'] += $gb; $stats[$b]['sets_against'] += $ga;
            $stats[$a]['points_for'] += $pa; $stats[$a]['points_against'] += $pb;
            $stats[$b]['points_for'] += $pb; $stats[$b]['points_against'] += $pa;
        }

        $ordered = array_values($stats);
        usort($ordered, static function (array $x, array $y): int {
            $cmp = $y['match_points'] <=> $x['match_points'];
            if ($cmp !== 0) return $cmp;
            $xRatio = $x['games_against'] > 0 ? $x['games_for'] / $x['games_against'] : $x['games_for'];
            $yRatio = $y['games_against'] > 0 ? $y['games_for'] / $y['games_against'] : $y['games_for'];
            $cmp = $yRatio <=> $xRatio;
            if ($cmp !== 0) return $cmp;
            $xPR = $x['points_against'] > 0 ? $x['points_for'] / $x['points_against'] : $x['points_for'];
            $yPR = $y['points_against'] > 0 ? $y['points_for'] / $y['points_against'] : $y['points_for'];
            return $yPR <=> $xPR;
        });

        $upsert = $db->prepare('INSERT INTO group_standings (format_id,group_id,player_id,played,won,lost,match_points,games_for,games_against,sets_for,sets_against,sets_ratio,game_ratio,points_for,points_against,point_ratio,position,tie_break_note,tiebreak_level,tiebreak_trace,qualified)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE played=VALUES(played),won=VALUES(won),lost=VALUES(lost),match_points=VALUES(match_points),games_for=VALUES(games_for),games_against=VALUES(games_against),sets_for=VALUES(sets_for),sets_against=VALUES(sets_against),sets_ratio=VALUES(sets_ratio),game_ratio=VALUES(game_ratio),points_for=VALUES(points_for),points_against=VALUES(points_against),point_ratio=VALUES(point_ratio),position=VALUES(position),tie_break_note=VALUES(tie_break_note),tiebreak_level=VALUES(tiebreak_level),tiebreak_trace=VALUES(tiebreak_trace),qualified=VALUES(qualified)');

        $position = 1;
        foreach ($ordered as $row) {
            $gr = $row['games_against'] > 0 ? $row['games_for'] / $row['games_against'] : (float)$row['games_for'];
            $pr = $row['points_against'] > 0 ? $row['points_for'] / $row['points_against'] : (float)$row['points_for'];
            $upsert->execute([$formatId,$groupId,$row['player_id'],$row['played'],$row['won'],$row['lost'],$row['match_points'],$row['games_for'],$row['games_against'],$row['sets_for'],$row['sets_against'],$gr,$gr,$row['points_for'],$row['points_against'],$pr,$position,'ITTF: match points > head_to_head > game ratio > point ratio',4,json_encode(['match_points','head_to_head','game_ratio','point_ratio']),0]);
            $position++;
        }

        return $ordered;
    }
}
