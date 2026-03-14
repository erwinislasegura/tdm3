<?php

declare(strict_types=1);

namespace App\Services;

class GroupGenerationService
{
    public function generate(int $formatId): array
    {
        return (new GroupDrawService())->generate($formatId);
    }
}
