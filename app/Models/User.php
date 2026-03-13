<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT u.*, r.name as role_name, r.id as role_id
                FROM users u
                LEFT JOIN user_roles ur ON ur.user_id=u.id
                LEFT JOIN roles r ON r.id=ur.role_id
                WHERE u.email=? LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function paginated(string $search, int $page, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $where = $search ? ' WHERE u.name LIKE :q OR u.email LIKE :q ' : '';

        $count = $this->db->prepare('SELECT COUNT(*) c FROM users u' . $where);
        if ($search) {
            $count->bindValue(':q', "%{$search}%");
        }
        $count->execute();
        $total = (int)$count->fetch()['c'];

        $sql = 'SELECT u.id,u.name,u.email,u.status,r.name role_name
                FROM users u
                LEFT JOIN user_roles ur ON ur.user_id=u.id
                LEFT JOIN roles r ON r.id=ur.role_id'
                . $where . ' ORDER BY u.id DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        if ($search) {
            $stmt->bindValue(':q', "%{$search}%");
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return ['data' => $stmt->fetchAll(), 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    public function roles(): array
    {
        return $this->db->query('SELECT id,name FROM roles ORDER BY id')->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare('INSERT INTO users (name,email,password,status,created_at,updated_at) VALUES (?,?,?,?,NOW(),NOW())');
        $ok = $stmt->execute([$data['name'], $data['email'], password_hash($data['password'], PASSWORD_DEFAULT), $data['status'] ?? 'active']);
        if (!$ok) {
            return false;
        }
        $userId = (int)$this->db->lastInsertId();
        $roleStmt = $this->db->prepare('INSERT INTO user_roles (user_id,role_id) VALUES (?,?)');
        return $roleStmt->execute([$userId, (int)$data['role_id']]);
    }
}
