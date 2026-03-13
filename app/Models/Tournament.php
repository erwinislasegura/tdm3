<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Tournament extends Model
{
    public function paginated(string $search, int $page, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $where = $search ? ' WHERE t.name LIKE :q OR o.name LIKE :q OR t.city LIKE :q ' : '';

        $count = $this->db->prepare('SELECT COUNT(*) c FROM tournaments t LEFT JOIN organizations o ON o.id=t.organization_id' . $where);
        if ($search) {
            $count->bindValue(':q', "%{$search}%");
        }
        $count->execute();
        $total = (int)$count->fetch()['c'];

        $sql = 'SELECT t.*, o.name as organization_name FROM tournaments t LEFT JOIN organizations o ON o.id=t.organization_id'
               . $where . ' ORDER BY t.start_date DESC LIMIT :l OFFSET :o';
        $stmt = $this->db->prepare($sql);
        if ($search) {
            $stmt->bindValue(':q', "%{$search}%");
        }
        $stmt->bindValue(':l', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return ['data' => $stmt->fetchAll(), 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    public function publicList(): array
    {
        $stmt = $this->db->prepare('SELECT id,name,description,start_date,end_date,city,status FROM tournaments WHERE is_public=1 ORDER BY start_date DESC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare('INSERT INTO tournaments (organization_id,name,description,start_date,end_date,city,venue,status,is_public,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,NOW(),NOW())');
        return $stmt->execute([$data['organization_id'], $data['name'], $data['description'], $data['start_date'], $data['end_date'], $data['city'], $data['venue'], $data['status'], isset($data['is_public']) ? 1 : 0]);
    }

    public function organizations(): array
    {
        return $this->db->query('SELECT id,name FROM organizations ORDER BY name')->fetchAll();
    }
}
