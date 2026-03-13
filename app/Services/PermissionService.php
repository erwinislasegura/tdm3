<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class PermissionService
{
    public static function userPermissions(int $userId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT DISTINCT p.name FROM permissions p
            INNER JOIN role_permissions rp ON rp.permission_id = p.id
            INNER JOIN user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = ?');
        $stmt->execute([$userId]);
        return array_map(static fn(array $row): string => $row['name'], $stmt->fetchAll());
    }

    public static function can(string $permission): bool
    {
        $user = auth_user();
        if (!$user) {
            return false;
        }
        if (($user['role_name'] ?? '') === 'root') {
            return true;
        }
        return in_array($permission, $user['permissions'] ?? [], true);
    }

    public static function authorize(string $permission): void
    {
        if (self::can($permission)) {
            return;
        }
        http_response_code(403);
        echo '403 - Sin permiso';
        exit;
    }
}
