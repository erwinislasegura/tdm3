<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Services\AuditService;

class UserController extends Controller
{
    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        $page = request_int('page', 1);
        $model = new User();
        $result = $model->paginated($search, $page);
        $roles = $model->roles();
        $this->render('users/index', ['users' => $result['data'], 'meta' => $result, 'roles' => $roles, 'search' => $search]);
    }

    public function store(): void
    {
        if (!verify_csrf()) {
            flash('error', 'Token inválido');
            redirect('/admin/users');
        }
        foreach (['name', 'email', 'password', 'role_id'] as $field) {
            if (empty($_POST[$field])) {
                flash('error', 'Todos los campos son obligatorios');
                redirect('/admin/users');
            }
        }
        $ok = (new User())->create($_POST);
        flash($ok ? 'success' : 'error', $ok ? 'Usuario creado' : 'Error al crear usuario');
        if ($ok) {
            AuditService::log('create', 'users', 'Usuario creado: ' . $_POST['email']);
        }
        redirect('/admin/users');
    }
}
