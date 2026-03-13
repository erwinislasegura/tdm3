<?php

declare(strict_types=1);

namespace App\Services;

class AuditLogService
{
    public static function log(string $action, string $module, string $description): void
    {
        AuditService::log($action, $module, $description);
    }
}
