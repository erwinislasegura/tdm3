<?php

declare(strict_types=1);

namespace App\Services;

class AssociationSeparationService
{
    public function optimize(array $groups, array $players, string $rule): array
    {
        if ($rule === 'none') {
            return ['groups' => $groups, 'swaps' => [], 'warnings' => []];
        }

        $swaps = [];
        $warnings = [];
        $maxIterations = 200;
        for ($i = 0; $i < $maxIterations; $i++) {
            $conflict = $this->firstConflict($groups, $players, $rule);
            if ($conflict === null) {
                break;
            }
            [$groupId, $playerId] = $conflict;
            $swap = $this->findSwap($groups, $players, $rule, $groupId, $playerId);
            if ($swap === null) {
                $warnings[] = "No se pudo separar completamente por {$rule} en grupo {$groupId}";
                break;
            }
            [$otherGroupId, $otherPlayerId] = $swap;
            $this->swapPlayers($groups, $groupId, $playerId, $otherGroupId, $otherPlayerId);
            $swaps[] = compact('groupId', 'playerId', 'otherGroupId', 'otherPlayerId');
        }

        return ['groups' => $groups, 'swaps' => $swaps, 'warnings' => $warnings];
    }

    private function firstConflict(array $groups, array $players, string $rule): ?array
    {
        foreach ($groups as $groupId => $playerIds) {
            $seen = [];
            foreach ($playerIds as $playerId) {
                $key = (string)($players[$playerId][$rule] ?? '');
                if ($key === '') {
                    continue;
                }
                if (isset($seen[$key])) {
                    return [$groupId, $playerId];
                }
                $seen[$key] = true;
            }
        }
        return null;
    }

    private function findSwap(array $groups, array $players, string $rule, string|int $groupId, int $playerId): ?array
    {
        $target = (string)($players[$playerId][$rule] ?? '');
        foreach ($groups as $otherGroupId => $otherPlayers) {
            if ((string)$otherGroupId === (string)$groupId) {
                continue;
            }
            foreach ($otherPlayers as $candidate) {
                if ((string)($players[$candidate][$rule] ?? '') === $target) {
                    continue;
                }
                return [$otherGroupId, $candidate];
            }
        }
        return null;
    }

    private function swapPlayers(array &$groups, string|int $groupA, int $playerA, string|int $groupB, int $playerB): void
    {
        $idxA = array_search($playerA, $groups[$groupA], true);
        $idxB = array_search($playerB, $groups[$groupB], true);
        if ($idxA === false || $idxB === false) {
            return;
        }
        $groups[$groupA][$idxA] = $playerB;
        $groups[$groupB][$idxB] = $playerA;
    }
}
