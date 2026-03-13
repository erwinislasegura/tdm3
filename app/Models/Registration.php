<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Registration extends Model
{
    public function all(): array
    {
        $sql = 'SELECT r.id,t.name AS tournament_name, CONCAT(pe.first_name," ",pe.last_name) AS player_name, r.status, r.created_at
        FROM registrations r
        INNER JOIN tournaments t ON t.id=r.tournament_id
        INNER JOIN players p ON p.id=r.player_id
        INNER JOIN people pe ON pe.id=p.person_id
        ORDER BY r.id DESC';
        return $this->db->query($sql)->fetchAll();
    }
}
