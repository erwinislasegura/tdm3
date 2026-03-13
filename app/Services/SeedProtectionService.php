<?php

declare(strict_types=1);

namespace App\Services;

class SeedProtectionService
{
    public function bracketPositions(int $drawSize, int $seedCount): array
    {
        $seedCount = min($seedCount, $drawSize);
        $positions = [1 => 1, 2 => $drawSize];
        if ($seedCount >= 4) {
            $positions[3] = intdiv($drawSize, 2) + 1;
            $positions[4] = intdiv($drawSize, 2);
        }
        $quarterPoints = [intdiv($drawSize, 4), intdiv($drawSize, 4) + 1, intdiv($drawSize * 3, 4), intdiv($drawSize * 3, 4) + 1];
        for ($s = 5; $s <= $seedCount; $s++) {
            $positions[$s] = $quarterPoints[($s - 5) % count($quarterPoints)] ?? $s;
        }
        return $positions;
    }
}
