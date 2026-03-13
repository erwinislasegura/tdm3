<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class MatchModel extends Model
{
    public function all(): array
    {
        $sql = 'SELECT m.id,m.phase,m.table_number,m.match_time,m.status,m.player_a_name,m.player_b_name,m.winner_name,t.name tournament_name
                FROM matches m INNER JOIN tournaments t ON t.id=m.tournament_id ORDER BY m.match_time DESC';
        return $this->db->query($sql)->fetchAll();
    }
}
