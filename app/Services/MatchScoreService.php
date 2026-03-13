<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use RuntimeException;

class MatchScoreService
{
    /**
     * @param array<int,array{a:int,b:int}> $sets
     */
    public function scoreGroupMatch(int $formatId, int $matchId, int $winnerPlayerId, array $sets, array $meta = []): void
    {
        $db = Database::getConnection();

        $matchStmt = $db->prepare('SELECT id,format_id,group_id,player_a_id,player_b_id,status FROM group_matches WHERE id=? AND format_id=? LIMIT 1');
        $matchStmt->execute([$matchId, $formatId]);
        $match = $matchStmt->fetch();
        if (!$match) {
            throw new RuntimeException('Partido no encontrado para el formato seleccionado.');
        }

        if (in_array((string)$match['status'], ['finished', 'walkover'], true)) {
            throw new RuntimeException('El partido ya está cerrado y no admite nuevos sets.');
        }

        $playerA = (int)$match['player_a_id'];
        $playerB = (int)$match['player_b_id'];

        if ($winnerPlayerId !== 0 && !in_array($winnerPlayerId, [$playerA, $playerB], true)) {
            throw new RuntimeException('El ganador debe ser uno de los jugadores del partido.');
        }

        if ($sets === []) {
            throw new RuntimeException('Debe registrar al menos un set válido.');
        }

        $normalizedSets = [];
        $winsA = 0;
        $winsB = 0;
        $pointsA = 0;
        $pointsB = 0;

        foreach ($sets as $index => $set) {
            $a = (int)($set['a'] ?? -1);
            $b = (int)($set['b'] ?? -1);
            if ($a < 0 || $b < 0 || $a === $b) {
                throw new RuntimeException('Set inválido en la posición ' . ($index + 1) . '.');
            }

            $setWinner = $a > $b ? $playerA : $playerB;
            if ($setWinner === $playerA) {
                $winsA++;
            } else {
                $winsB++;
            }

            $pointsA += $a;
            $pointsB += $b;
            $normalizedSets[] = ['number' => $index + 1, 'a' => $a, 'b' => $b, 'winner_player_id' => $setWinner];
        }

        $calculatedWinner = $winsA === $winsB ? 0 : ($winsA > $winsB ? $playerA : $playerB);
        if ($winnerPlayerId === 0) {
            $winnerPlayerId = $calculatedWinner;
        }

        if ($winnerPlayerId === 0) {
            throw new RuntimeException('No se pudo determinar ganador, revise los sets ingresados.');
        }

        if ($winnerPlayerId !== $calculatedWinner) {
            throw new RuntimeException('El ganador indicado no coincide con el resultado de los sets.');
        }

        $status = (string)($meta['status'] ?? 'finished');
        if (!in_array($status, ['pending', 'scheduled', 'called', 'in_game', 'finished', 'suspended', 'walkover'], true)) {
            $status = 'finished';
        }

        $db->beginTransaction();
        try {
            $db->prepare('DELETE FROM group_match_sets WHERE group_match_id=?')->execute([$matchId]);

            $insertSet = $db->prepare('INSERT INTO group_match_sets (group_match_id,set_number,player_a_points,player_b_points,winner_player_id) VALUES (?,?,?,?,?)');
            foreach ($normalizedSets as $set) {
                $insertSet->execute([$matchId, $set['number'], $set['a'], $set['b'], $set['winner_player_id']]);
            }

            $update = $db->prepare('UPDATE group_matches
                SET winner_player_id=:winner, status=:status, sets_json=:sets, walkover_side=:walkover, notes=:notes,
                    table_number=:table_number, scheduled_at=:scheduled_at, referee_id=:referee_id,
                    points_a=:points_a, points_b=:points_b
                WHERE id=:id');

            $tableNumber = $meta['table_number'] ?? null;
            $scheduledAt = $meta['scheduled_at'] ?? null;
            $refereeId = $meta['referee_id'] ?? null;

            $update->execute([
                ':winner' => $winnerPlayerId,
                ':status' => $status,
                ':sets' => json_encode(array_map(static fn(array $set): array => ['a' => $set['a'], 'b' => $set['b']], $normalizedSets), JSON_THROW_ON_ERROR),
                ':walkover' => $meta['walkover_side'] ?? null,
                ':notes' => $meta['notes'] ?? null,
                ':table_number' => $tableNumber !== '' ? $tableNumber : null,
                ':scheduled_at' => $scheduledAt !== '' ? $scheduledAt : null,
                ':referee_id' => $refereeId !== '' ? $refereeId : null,
                ':points_a' => $pointsA,
                ':points_b' => $pointsB,
                ':id' => $matchId,
            ]);

            (new GroupStandingService())->recalculate($formatId, (int)$match['group_id']);
            $db->commit();
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
}
