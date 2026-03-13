<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class CompetitionFormat extends Model
{
    public function paginated(string $search, int $page, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $where = $search ? ' WHERE cf.category_name LIKE :q OR t.name LIKE :q ' : '';
        $count = $this->db->prepare('SELECT COUNT(*) c FROM competition_formats cf INNER JOIN tournaments t ON t.id=cf.tournament_id' . $where);
        if ($search) {
            $count->bindValue(':q', "%{$search}%");
        }
        $count->execute();
        $total = (int)$count->fetch()['c'];

        $sql = 'SELECT cf.*, t.name tournament_name FROM competition_formats cf INNER JOIN tournaments t ON t.id=cf.tournament_id'
            . $where . ' ORDER BY cf.id DESC LIMIT :l OFFSET :o';
        $stmt = $this->db->prepare($sql);
        if ($search) {
            $stmt->bindValue(':q', "%{$search}%");
        }
        $stmt->bindValue(':l', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return ['data' => $stmt->fetchAll(), 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    public function create(array $data, int $userId): int
    {
        $stmt = $this->db->prepare('INSERT INTO competition_formats
            (tournament_id,category_name,registered_players,group_count,group_size,qualified_per_group,advancement_mode,best_third_slots,final_bracket_type,protected_seeds,ranking_criteria,separation_rule,generation_mode,created_by,updated_by)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $data['tournament_id'], $data['category_name'], $data['registered_players'], $data['group_count'], $data['group_size'],
            $data['qualified_per_group'], $data['advancement_mode'], $data['best_third_slots'], 'single_elimination',
            $data['protected_seeds'], $data['ranking_criteria'], $data['separation_rule'], $data['generation_mode'],
            $userId, $userId,
        ]);
        $id = (int)$this->db->lastInsertId();

        $ruleStmt = $this->db->prepare('INSERT INTO format_rules (format_id,rule_key,rule_value) VALUES (?,?,?)');
        $ruleStmt->execute([$id, 'allow_same_group_early_cross', (string)($data['allow_same_group_early_cross'] ?? '0')]);
        $ruleStmt->execute([$id, 'best_third_criteria', (string)($data['best_third_criteria'] ?? 'match_points')]);
        return $id;
    }

    public function tournaments(): array
    {
        return $this->db->query('SELECT id,name FROM tournaments ORDER BY start_date DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT cf.*, t.name tournament_name FROM competition_formats cf INNER JOIN tournaments t ON t.id=cf.tournament_id WHERE cf.id=? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function groups(int $formatId): array
    {
        $stmt = $this->db->prepare('SELECT g.id,g.name FROM `groups` g INNER JOIN group_players gp ON gp.group_id=g.id WHERE gp.format_id=? GROUP BY g.id,g.name ORDER BY g.name');
        $stmt->execute([$formatId]);
        return $stmt->fetchAll();
    }

    public function groupDetails(int $groupId): array
    {
        $players = $this->db->prepare('SELECT gp.*, CONCAT(p.first_name," ",p.last_name) player_name
            FROM group_players gp
            INNER JOIN players pl ON pl.id=gp.player_id
            INNER JOIN people p ON p.id=pl.person_id
            WHERE gp.group_id=? ORDER BY gp.seed_number ASC');
        $players->execute([$groupId]);

        $matches = $this->db->prepare('SELECT gm.*, CONCAT(pa.first_name," ",pa.last_name) player_a_name, CONCAT(pb.first_name," ",pb.last_name) player_b_name
            FROM group_matches gm
            INNER JOIN players pla ON pla.id=gm.player_a_id
            INNER JOIN people pa ON pa.id=pla.person_id
            INNER JOIN players plb ON plb.id=gm.player_b_id
            INNER JOIN people pb ON pb.id=plb.person_id
            WHERE gm.group_id=? ORDER BY gm.id ASC');
        $matches->execute([$groupId]);

        $standing = $this->db->prepare('SELECT gs.*, CONCAT(p.first_name," ",p.last_name) player_name
            FROM group_standings gs
            INNER JOIN players pl ON pl.id=gs.player_id
            INNER JOIN people p ON p.id=pl.person_id
            WHERE gs.group_id=? ORDER BY gs.position ASC');
        $standing->execute([$groupId]);

        return ['players' => $players->fetchAll(), 'matches' => $matches->fetchAll(), 'standings' => $standing->fetchAll()];
    }

    public function latestBracket(int $formatId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM knockout_brackets WHERE format_id=? AND deleted_at IS NULL ORDER BY id DESC LIMIT 1');
        $stmt->execute([$formatId]);
        return $stmt->fetch() ?: null;
    }

    public function bracketSlots(int $bracketId): array
    {
        $stmt = $this->db->prepare('SELECT ks.*, CONCAT(p.first_name," ",p.last_name) player_name
            FROM knockout_slots ks
            LEFT JOIN players pl ON pl.id=ks.player_id
            LEFT JOIN people p ON p.id=pl.person_id
            WHERE ks.bracket_id=? ORDER BY ks.slot_number ASC');
        $stmt->execute([$bracketId]);
        return $stmt->fetchAll();
    }
}
