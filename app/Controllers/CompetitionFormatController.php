<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\CompetitionFormat;
use App\Services\AuditLogService;
use App\Services\GroupDrawService;
use App\Services\GroupStandingService;
use App\Services\KnockoutDrawService;
use App\Services\PermissionService;
use App\Services\QualificationService;

class CompetitionFormatController extends Controller
{
    public function index(): void
    {
        PermissionService::authorize('groups.view');
        $search = trim($_GET['q'] ?? '');
        $page = request_int('page', 1);
        $model = new CompetitionFormat();
        $formats = $model->paginated($search, $page);
        $this->render('competition_formats/index', [
            'formats' => $formats['data'],
            'meta' => $formats,
            'search' => $search,
            'tournaments' => $model->tournaments(),
        ]);
    }

    public function store(): void
    {
        PermissionService::authorize('groups.create');
        if (!verify_csrf()) {
            flash('error', 'Token inválido');
            redirect('/admin/competition-formats');
        }

        $id = (new CompetitionFormat())->create($_POST, (int)(auth_user()['id'] ?? 0));
        AuditLogService::log('create', 'competition_formats', 'Formato creado #' . $id);
        flash('success', 'Formato creado correctamente');
        redirect('/admin/competition-formats');
    }

    public function show(int $id): void
    {
        PermissionService::authorize('groups.view');
        $model = new CompetitionFormat();
        $format = $model->find($id);
        if (!$format) {
            flash('error', 'Formato no encontrado');
            redirect('/admin/competition-formats');
        }
        $groups = $model->groups($id);
        $groupDetails = [];
        foreach ($groups as $group) {
            $groupDetails[$group['id']] = $model->groupDetails((int)$group['id']);
        }
        $bracket = $model->latestBracket($id);
        $slots = $bracket ? $model->bracketSlots((int)$bracket['id']) : [];

        $this->render('competition_formats/show', compact('format', 'groups', 'groupDetails', 'bracket', 'slots'));
    }

    public function generateGroups(int $id): void
    {
        PermissionService::authorize('groups.generate');
        if (!verify_csrf()) {
            flash('error', 'Token inválido');
            redirect('/admin/competition-formats/' . $id);
        }
        try {
            $result = (new GroupDrawService())->generate($id);
            AuditLogService::log('generate', 'groups', 'Grupos generados formato #' . $id);
            if (!empty($result['warnings'])) {
                flash('error', implode(' | ', $result['warnings']));
            } else {
                flash('success', 'Grupos generados correctamente');
            }
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/admin/competition-formats/' . $id);
    }

    public function scoreMatch(int $id, int $matchId): void
    {
        PermissionService::authorize('matches.score');
        if (!verify_csrf()) {
            flash('error', 'Token inválido');
            redirect('/admin/competition-formats/' . $id);
        }

        $winner = (int)($_POST['winner_player_id'] ?? 0);
        $sets = json_decode((string)($_POST['sets_json'] ?? '[]'), true);
        if (!is_array($sets)) {
            $sets = [];
        }

        $db = Database::getConnection();
        $stmt = $db->prepare('UPDATE group_matches SET winner_player_id=?, status=?, sets_json=?, walkover_side=?, notes=?, table_number=?, scheduled_at=?, referee_id=? WHERE id=?');
        $stmt->execute([$winner, $_POST['status'] ?? 'finished', json_encode($sets), $_POST['walkover_side'] ?? null, $_POST['notes'] ?? null, $_POST['table_number'] ?: null, $_POST['scheduled_at'] ?: null, $_POST['referee_id'] ?: null, $matchId]);

        $g = $db->prepare('SELECT group_id FROM group_matches WHERE id=?');
        $g->execute([$matchId]);
        $groupId = (int)$g->fetch()['group_id'];
        (new GroupStandingService())->recalculate($id, $groupId);

        AuditLogService::log('score', 'group_matches', 'Marcador actualizado partido #' . $matchId);
        flash('success', 'Resultado guardado');
        redirect('/admin/competition-formats/' . $id);
    }

    public function closeGroups(int $id): void
    {
        PermissionService::authorize('groups.close');
        if (!verify_csrf()) {
            flash('error', 'Token inválido');
            redirect('/admin/competition-formats/' . $id);
        }
        $format = (new CompetitionFormat())->find($id);
        try {
            $qualified = (new QualificationService())->closeGroupsAndClassify($id, (int)$format['qualified_per_group'], (int)$format['best_third_slots']);
            AuditLogService::log('close', 'groups', 'Grupos cerrados formato #' . $id . ' - clasificados: ' . count($qualified));
            flash('success', 'Grupos cerrados y clasificación definida');
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/admin/competition-formats/' . $id);
    }

    public function generateKnockout(int $id): void
    {
        PermissionService::authorize('knockout.generate');
        if (!verify_csrf()) {
            flash('error', 'Token inválido');
            redirect('/admin/competition-formats/' . $id);
        }
        $format = (new CompetitionFormat())->find($id);
        try {
            $bracketId = (new KnockoutDrawService())->generate($id, (int)$format['protected_seeds']);
            AuditLogService::log('generate', 'knockout', 'Cuadro generado #' . $bracketId);
            flash('success', 'Knockout generado correctamente');
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/admin/competition-formats/' . $id);
    }
}
