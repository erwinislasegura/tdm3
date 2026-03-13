<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Organization extends Model
{
    public function paginated(string $search, int $page, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $where = $search ? ' WHERE name LIKE :q OR city LIKE :q ' : '';

        $count = $this->db->prepare('SELECT COUNT(*) c FROM organizations' . $where);
        if ($search) {
            $count->bindValue(':q', "%{$search}%");
        }
        $count->execute();
        $total = (int)$count->fetch()['c'];

        $stmt = $this->db->prepare('SELECT * FROM organizations' . $where . ' ORDER BY id DESC LIMIT :l OFFSET :o');
        if ($search) {
            $stmt->bindValue(':q', "%{$search}%");
        }
        $stmt->bindValue(':l', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return ['data' => $stmt->fetchAll(), 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare('INSERT INTO organizations (name,type,description,city,email,phone,primary_color,secondary_color,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,NOW(),NOW())');
        return $stmt->execute([$data['name'], $data['type'], $data['description'], $data['city'], $data['email'], $data['phone'], $data['primary_color'], $data['secondary_color'], $data['status'] ?? 'active']);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare('UPDATE organizations SET name=?, type=?, city=?, email=?, phone=?, status=?, updated_at=NOW() WHERE id=?');
        return $stmt->execute([$data['name'], $data['type'], $data['city'], $data['email'], $data['phone'], $data['status'], $id]);
    }
}
