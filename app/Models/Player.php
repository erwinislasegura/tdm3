<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Player extends Model
{
    public function paginated(string $search, int $page, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $where = $search ? ' WHERE pe.first_name LIKE :q OR pe.last_name LIKE :q OR c.name LIKE :q ' : '';

        $count = $this->db->prepare('SELECT COUNT(*) c FROM players p INNER JOIN people pe ON pe.id=p.person_id LEFT JOIN clubs c ON c.id=p.club_id' . $where);
        if ($search) {
            $count->bindValue(':q', "%{$search}%");
        }
        $count->execute();
        $total = (int)$count->fetch()['c'];

        $sql = 'SELECT p.id, pe.first_name, pe.last_name, pe.gender, p.ranking_points, p.status, c.name AS club_name
                FROM players p
                INNER JOIN people pe ON pe.id=p.person_id
                LEFT JOIN clubs c ON c.id=p.club_id'
                . $where . ' ORDER BY p.id DESC LIMIT :l OFFSET :o';
        $stmt = $this->db->prepare($sql);
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
        $this->db->beginTransaction();
        try {
            $stmtPerson = $this->db->prepare('INSERT INTO people (first_name,last_name,document,birth_date,gender,nationality,notes,created_at) VALUES (?,?,?,?,?,?,?,NOW())');
            $stmtPerson->execute([$data['first_name'], $data['last_name'], $data['document'] ?: null, $data['birth_date'] ?: null, $data['gender'], $data['nationality'] ?: null, $data['notes'] ?? null]);
            $personId = (int)$this->db->lastInsertId();

            $stmtPlayer = $this->db->prepare('INSERT INTO players (person_id,club_id,current_category,ranking_points,status,created_at) VALUES (?,?,?,?,?,NOW())');
            $stmtPlayer->execute([$personId, $data['club_id'] ?: null, $data['current_category'] ?: null, 0, 'active']);
            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function clubs(): array
    {
        return $this->db->query('SELECT id,name FROM clubs ORDER BY name')->fetchAll();
    }
}
