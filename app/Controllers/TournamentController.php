<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Tournament;
use App\Services\AuditService;

class TournamentController extends Controller
{
    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        $page = request_int('page', 1);
        $model = new Tournament();
        $result = $model->paginated($search, $page);
        $organizations = $model->organizations();
        $this->render('tournaments/index', ['tournaments' => $result['data'], 'meta' => $result, 'organizations' => $organizations, 'search' => $search]);
    }

    public function store(): void
    {
        if (!verify_csrf()) {
            flash('error', 'Token inválido');
            redirect('/admin/tournaments');
        }

        foreach (['organization_id', 'name', 'start_date', 'end_date'] as $field) {
            if (empty($_POST[$field])) {
                flash('error', 'Completa los campos obligatorios');
                redirect('/admin/tournaments');
            }
        }

        (new Tournament())->create($_POST);
        AuditService::log('create', 'tournaments', 'Torneo creado: ' . ($_POST['name'] ?? ''));
        flash('success', 'Torneo creado');
        redirect('/admin/tournaments');
    }
}
