<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class GroupViewService
{
    public function load(int $formatId): array
    {
        $db = Database::getConnection();

        $formatStmt = $db->prepare('SELECT cf.*, t.name tournament_name FROM competition_formats cf INNER JOIN tournaments t ON t.id=cf.tournament_id WHERE cf.id=? LIMIT 1');
        $formatStmt->execute([$formatId]);
        $format = $formatStmt->fetch();

        if (!$format) {
            return [];
        }

        $checklist = [
            'participants' => (int)$format['registered_players'] > 0,
            'draw' => false,
            'matches' => false,
            'phase_enabled' => in_array((string)$format['status'], ['groups_generated', 'groups_closed'], true),
        ];

        $groupsStmt = $db->prepare('SELECT g.*,
            COUNT(DISTINCT gp.id) players_count,
            SUM(CASE WHEN gp.qualified = 1 THEN 1 ELSE 0 END) qualified_count,
            COUNT(DISTINCT gm.id) matches_count,
            SUM(CASE WHEN gm.status IN ("finished","walkover") THEN 1 ELSE 0 END) finished_matches
            FROM `groups` g
            LEFT JOIN group_players gp ON gp.group_id=g.id AND gp.format_id=?
            LEFT JOIN group_matches gm ON gm.group_id=g.id AND gm.format_id=?
            WHERE g.tournament_id=? AND g.deleted_at IS NULL AND (g.phase_id IS NULL OR g.phase_id IS NOT NULL)
            GROUP BY g.id
            HAVING players_count > 0
            ORDER BY g.order_index ASC, g.name ASC');
        $groupsStmt->execute([$formatId, $formatId, (int)$format['tournament_id']]);
        $groups = $groupsStmt->fetchAll();

        $checklist['draw'] = count($groups) > 0;

        $details = [];
        foreach ($groups as $group) {
            $groupId = (int)$group['id'];

            $players = $db->prepare('SELECT gp.*, CONCAT(pe.first_name, " ", pe.last_name) player_name,
                COALESCE(c.name, "Sin club") club_name,
                gs.position current_position,
                gs.qualified standing_qualified
                FROM group_players gp
                INNER JOIN players p ON p.id=gp.player_id
                INNER JOIN people pe ON pe.id=p.person_id
                LEFT JOIN clubs c ON c.id=p.club_id
                LEFT JOIN group_standings gs ON gs.group_id=gp.group_id AND gs.player_id=gp.player_id
                WHERE gp.group_id=? AND gp.format_id=?
                ORDER BY COALESCE(gs.position, 999), gp.seed_number ASC');
            $players->execute([$groupId, $formatId]);

            $standings = $db->prepare('SELECT gs.*, CONCAT(pe.first_name, " ", pe.last_name) player_name
                FROM group_standings gs
                INNER JOIN players p ON p.id=gs.player_id
                INNER JOIN people pe ON pe.id=p.person_id
                WHERE gs.group_id=? AND gs.format_id=?
                ORDER BY gs.position ASC');
            $standings->execute([$groupId, $formatId]);

            $matches = $db->prepare('SELECT gm.*, CONCAT(pa.first_name, " ", pa.last_name) player_a_name,
                CONCAT(pb.first_name, " ", pb.last_name) player_b_name
                FROM group_matches gm
                INNER JOIN players pla ON pla.id=gm.player_a_id
                INNER JOIN people pa ON pa.id=pla.person_id
                INNER JOIN players plb ON plb.id=gm.player_b_id
                INNER JOIN people pb ON pb.id=plb.person_id
                WHERE gm.group_id=? AND gm.format_id=?
                ORDER BY gm.scheduled_at IS NULL, gm.scheduled_at ASC, gm.id ASC');
            $matches->execute([$groupId, $formatId]);

            $matchRows = $matches->fetchAll();
            if (count($matchRows) > 0) {
                $checklist['matches'] = true;
            }

            $details[$groupId] = [
                'players' => $players->fetchAll(),
                'standings' => $standings->fetchAll(),
                'matches' => $matchRows,
            ];
        }

        return [
            'format' => $format,
            'groups' => $groups,
            'groupDetails' => $details,
            'checklist' => $checklist,
            'emptyStates' => $this->buildEmptyStates($checklist),
        ];
    }

    private function buildEmptyStates(array $checklist): array
    {
        $messages = [];
        if (!$checklist['participants']) {
            $messages[] = 'Aún no hay participantes inscritos para esta fase.';
        }
        if (!$checklist['draw']) {
            $messages[] = 'Debes generar el sorteo de grupos antes de registrar resultados.';
        }
        if (!$checklist['matches']) {
            $messages[] = 'Los partidos del grupo aún no han sido generados.';
        }
        return $messages;
    }
}
