<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class LiveScore extends Model
{
    public function feed(): array
    {
        return [
            'in_progress' => $this->inProgressMatches(),
            'active_bracket_matches' => $this->activeKnockoutMatches(),
            'group_standings' => $this->groupStandings(),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }

    private function inProgressMatches(): array
    {
        $stmt = $this->db->query("SELECT m.id,m.tournament_id,t.name tournament_name,m.phase,m.bracket_round,m.status,m.table_number,m.match_time,m.player_a_name,m.player_b_name,m.winner_name,m.score_summary,m.live_updated_at
            FROM matches m
            INNER JOIN tournaments t ON t.id = m.tournament_id
            WHERE m.status IN ('scheduled','in_progress','finished')
            ORDER BY (m.status='in_progress') DESC,m.match_time DESC,m.id DESC
            LIMIT 20");
        $matches = $stmt->fetchAll();

        $setStmt = $this->db->prepare('SELECT set_number,player_a_points,player_b_points,winner_side FROM match_sets WHERE match_id = ? ORDER BY set_number ASC');
        foreach ($matches as &$match) {
            $setStmt->execute([(int)$match['id']]);
            $match['sets'] = $setStmt->fetchAll();
        }

        return $matches;
    }

    private function activeKnockoutMatches(): array
    {
        $stmt = $this->db->query("SELECT km.id,km.round_number,km.match_number,km.status,km.score_summary,km.table_number,
                CONCAT(pa.first_name,' ',pa.last_name) player_a_name,
                CONCAT(pb.first_name,' ',pb.last_name) player_b_name,
                CONCAT(pw.first_name,' ',pw.last_name) winner_name,
                kb.name bracket_name,cf.category_name,t.name tournament_name
            FROM knockout_matches km
            INNER JOIN knockout_brackets kb ON kb.id = km.bracket_id
            INNER JOIN competition_formats cf ON cf.id = kb.format_id
            INNER JOIN tournaments t ON t.id = cf.tournament_id
            LEFT JOIN players pla ON pla.id = km.player_a_id
            LEFT JOIN people pa ON pa.id = pla.person_id
            LEFT JOIN players plb ON plb.id = km.player_b_id
            LEFT JOIN people pb ON pb.id = plb.person_id
            LEFT JOIN players plw ON plw.id = km.winner_player_id
            LEFT JOIN people pw ON pw.id = plw.person_id
            WHERE km.status IN ('scheduled','in_progress','finished')
            ORDER BY (km.status='in_progress') DESC, km.round_number ASC, km.match_number ASC
            LIMIT 24");

        return $stmt->fetchAll();
    }

    private function groupStandings(): array
    {
        $stmt = $this->db->query("SELECT gs.group_id,g.name group_name,cf.category_name,t.name tournament_name,gs.position,gs.match_points,gs.won,gs.lost,
                CONCAT(p.first_name,' ',p.last_name) player_name
            FROM group_standings gs
            INNER JOIN `groups` g ON g.id = gs.group_id
            INNER JOIN competition_formats cf ON cf.id = gs.format_id
            INNER JOIN tournaments t ON t.id = cf.tournament_id
            INNER JOIN players pl ON pl.id = gs.player_id
            INNER JOIN people p ON p.id = pl.person_id
            ORDER BY gs.group_id ASC, gs.position ASC
            LIMIT 80");

        return $stmt->fetchAll();
    }
}
