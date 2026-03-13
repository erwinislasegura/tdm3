<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CompetitionFormat;
use App\Services\AuditLogService;
use App\Services\GroupGenerationService;
use App\Services\GroupStandingService;
use App\Services\GroupViewService;
use App\Services\KnockoutDrawService;
use App\Services\MatchScoreService;
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
        $payload = (new GroupViewService())->load($id);
        if ($payload === []) {
            flash('error', 'Formato no encontrado');
            redirect('/admin/competition-formats');
        }

        $format = $payload['format'];
        $groups = $payload['groups'];
        $groupDetails = $payload['groupDetails'];
        $checklist = $payload['checklist'];
        $emptyStates = $payload['emptyStates'];
        $model = new CompetitionFormat();
        $bracket = $model->latestBracket($id);
        $slots = $bracket ? $model->bracketSlots((int)$bracket['id']) : [];

        $this->render('competition_formats/show', compact('format', 'groups', 'groupDetails', 'checklist', 'emptyStates', 'bracket', 'slots'));
    }

    public function generateGroups(int $id): void
    {
        PermissionService::authorize('groups.generate');
        if (!verify_csrf()) {
            flash('error', 'Token inválido');
            redirect('/admin/competition-formats/' . $id);
        }
        try {
            $result = (new GroupGenerationService())->generate($id);
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

    public function recalculateStandings(int $id, int $groupId): void
    {
        PermissionService::authorize('standings.recalculate');
        if (!verify_csrf()) {
            flash('error', 'Token inválido');
            redirect('/admin/competition-formats/' . $id);
        }

        try {
            (new GroupStandingService())->recalculate($id, $groupId);
            AuditLogService::log('recalculate', 'group_standings', 'Recalculada tabla para grupo #' . $groupId);
            flash('success', 'Tabla recalculada correctamente.');
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }

        redirect('/admin/competition-formats/' . $id);
    }

    public function lockGroup(int $id, int $groupId): void
    {
        PermissionService::authorize('groups.lock');
        if (!verify_csrf()) {
            flash('error', 'Token inválido');
            redirect('/admin/competition-formats/' . $id);
        }

        (new CompetitionFormat())->toggleGroupLock($groupId);
        AuditLogService::log('lock', 'groups', 'Cambio de bloqueo grupo #' . $groupId);
        flash('success', 'Estado de bloqueo actualizado.');
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

        try {
            (new MatchScoreService())->scoreGroupMatch($id, $matchId, $winner, $sets, [
                'status' => $_POST['status'] ?? 'finished',
                'walkover_side' => $_POST['walkover_side'] ?? null,
                'notes' => $_POST['notes'] ?? null,
                'table_number' => $_POST['table_number'] ?? null,
                'scheduled_at' => $_POST['scheduled_at'] ?? null,
                'referee_id' => $_POST['referee_id'] ?? null,
            ]);
            AuditLogService::log('score', 'group_matches', 'Marcador actualizado partido #' . $matchId);
            flash('success', 'Resultado guardado');
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/admin/competition-formats/' . $id);
    }

    public function closeGroups(int $id): void
    {
        PermissionService::authorize('qualifications.generate');
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

    // Alias de rutas de fase solicitadas
    public function phaseGroupsView(int $multiTournamentId, int $tournamentId, int $phaseId): void
    {
        $this->show($phaseId);
    }

    public function phaseGenerateGroups(int $phaseId): void
    {
        $this->generateGroups($phaseId);
    }

    public function phaseGroupClose(int $phaseId, int $groupId): void
    {
        $this->lockGroup($phaseId, $groupId);
    }

    public function phaseClassify(int $phaseId): void
    {
        $this->closeGroups($phaseId);
    }

    public function scoreByMatchId(int $matchId): void
    {
        $formatId = (new CompetitionFormat())->formatIdByMatch($matchId);
        $this->scoreMatch($formatId, $matchId);
    }
}
