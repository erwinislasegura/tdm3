<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class KnockoutDrawService
{
    public function generate(int $formatId, int $seedCount): int
    {
        $db = Database::getConnection();
        $q = $db->prepare('SELECT qp.player_id, qp.qualification_position, gp.seed_number
            FROM qualified_players qp
            LEFT JOIN group_players gp ON gp.player_id=qp.player_id AND gp.format_id=qp.format_id
            WHERE qp.format_id=?
            ORDER BY qp.qualification_position ASC, gp.seed_number ASC');
        $q->execute([$formatId]);
        $players = $q->fetchAll();
        $count = count($players);
        if ($count === 0) {
            throw new \RuntimeException('No hay clasificados para generar el knockout.');
        }
        $drawSize = 1;
        while ($drawSize < $count) {
            $drawSize *= 2;
        }
        $db->prepare('UPDATE knockout_brackets SET deleted_at=NOW(), status="replaced" WHERE format_id=? AND deleted_at IS NULL')->execute([$formatId]);
        $insertB = $db->prepare('INSERT INTO knockout_brackets (format_id,name,draw_size,seeded_count,status,created_by) VALUES (?,?,?,?,"generated",?)');
        $insertB->execute([$formatId, 'Main Draw', $drawSize, $seedCount, auth_user()['id'] ?? null]);
        $bracketId = (int)$db->lastInsertId();

        $seedService = new SeedProtectionService();
        $seedPositions = $seedService->bracketPositions($drawSize, $seedCount);
        $slots = array_fill(1, $drawSize, null);

        foreach ($players as $idx => $player) {
            $seed = (int)($player['seed_number'] ?? 0);
            if ($seed > 0 && isset($seedPositions[$seed]) && $slots[$seedPositions[$seed]] === null) {
                $slots[$seedPositions[$seed]] = $player;
                unset($players[$idx]);
            }
        }

        $players = array_values($players);
        for ($i = 1; $i <= $drawSize; $i++) {
            if ($slots[$i] !== null) {
                continue;
            }
            $slots[$i] = array_shift($players) ?: ['player_id' => null, 'qualification_position' => null, 'seed_number' => null, 'bye' => 1];
        }

        $insertS = $db->prepare('INSERT INTO knockout_slots (bracket_id,slot_number,seed_number,player_id,source_ref,is_bye) VALUES (?,?,?,?,?,?)');
        foreach ($slots as $number => $slot) {
            $insertS->execute([$bracketId, $number, $slot['seed_number'] ?? null, $slot['player_id'] ?? null, $slot['qualification_position'] ? ('Q' . $slot['qualification_position']) : 'BYE', isset($slot['bye']) ? 1 : 0]);
        }

        $insertM = $db->prepare('INSERT INTO knockout_matches (bracket_id,round_number,match_number,slot_a,slot_b,player_a_id,player_b_id,status) VALUES (?,?,?,?,?,?,?,?)');
        $matchNumber = 1;
        for ($i = 1; $i <= $drawSize; $i += 2) {
            $a = $slots[$i]['player_id'] ?? null;
            $b = $slots[$i + 1]['player_id'] ?? null;
            $status = ($a === null || $b === null) ? 'walkover' : 'scheduled';
            $insertM->execute([$bracketId, 1, $matchNumber, $i, $i + 1, $a, $b, $status]);
            $matchNumber++;
        }

        return $bracketId;
    }
}
