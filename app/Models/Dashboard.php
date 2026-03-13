<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Dashboard extends Model
{
    public function metrics(): array
    {
        $queries = [
            'users' => 'SELECT COUNT(*) c FROM users',
            'players' => 'SELECT COUNT(*) c FROM players',
            'tournaments' => 'SELECT COUNT(*) c FROM tournaments',
            'active_tournaments' => 'SELECT COUNT(*) c FROM tournaments WHERE status="active"',
            'pending_matches' => 'SELECT COUNT(*) c FROM matches WHERE status IN ("scheduled","in_progress")',
            'finished_matches' => 'SELECT COUNT(*) c FROM matches WHERE status="finished"',
            'active_rankings' => 'SELECT COUNT(*) c FROM rankings',
        ];

        $result = [];
        foreach ($queries as $key => $query) {
            $result[$key] = (int)$this->db->query($query)->fetch()['c'];
        }
        return $result;
    }

    public function recentActivity(): array
    {
        return $this->db->query('SELECT action,module_name,description,created_at FROM audit_logs ORDER BY id DESC LIMIT 8')->fetchAll();
    }
}
