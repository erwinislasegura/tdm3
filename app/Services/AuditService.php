<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class AuditService
{
    public static function log(string $action, string $module, string $description): void
    {
        $db = Database::connection();
        $stmt = $db->prepare('INSERT INTO audit_logs (user_id,action,module_name,description,ip_address,created_at) VALUES (?,?,?,?,?,NOW())');
        $stmt->execute([
            $_SESSION['user']['id'] ?? null,
            $action,
            $module,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? 'cli',
        ]);
    }
}
