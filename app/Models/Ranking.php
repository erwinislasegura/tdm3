<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Ranking extends Model
{
    public function top(int $limit = 20): array
    {
        $stmt = $this->db->prepare('SELECT r.position,r.points, CONCAT(pe.first_name," ",pe.last_name) AS player_name, c.name club_name
            FROM rankings r
            INNER JOIN players p ON p.id=r.player_id
            INNER JOIN people pe ON pe.id=p.person_id
            LEFT JOIN clubs c ON c.id=p.club_id
            ORDER BY r.position ASC LIMIT ?');
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
