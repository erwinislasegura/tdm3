<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Player;
use App\Services\AuditService;

class PlayerController extends Controller
{
    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        $page = request_int('page', 1);
        $model = new Player();
        $result = $model->paginated($search, $page);
        $clubs = $model->clubs();
        $this->render('players/index', ['players' => $result['data'], 'meta' => $result, 'clubs' => $clubs, 'search' => $search]);
    }

    public function store(): void
    {
        if (!verify_csrf()) {
            flash('error', 'Token inválido');
            redirect('/admin/players');
        }

        if (empty(trim($_POST['first_name'] ?? '')) || empty(trim($_POST['last_name'] ?? ''))) {
            flash('error', 'Nombres y apellidos son obligatorios');
            redirect('/admin/players');
        }

        $ok = (new Player())->create($_POST);
        if (!$ok) {
            flash('error', 'No se pudo registrar el jugador');
            redirect('/admin/players');
        }

        AuditService::log('create', 'players', 'Jugador creado: ' . ($_POST['first_name'] ?? '') . ' ' . ($_POST['last_name'] ?? ''));
        flash('success', 'Jugador registrado');
        redirect('/admin/players');
    }
}
