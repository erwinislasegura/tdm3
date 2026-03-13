<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Organization;
use App\Services\AuditService;

class OrganizationController extends Controller
{
    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        $page = request_int('page', 1);
        $result = (new Organization())->paginated($search, $page);
        $this->render('organizations/index', ['organizations' => $result['data'], 'meta' => $result, 'search' => $search]);
    }

    public function store(): void
    {
        if (!verify_csrf()) {
            flash('error', 'Token inválido');
            redirect('/admin/organizations');
        }

        $required = ['name', 'type'];
        foreach ($required as $field) {
            if (empty(trim($_POST[$field] ?? ''))) {
                flash('error', 'Completa los campos requeridos');
                redirect('/admin/organizations');
            }
        }

        (new Organization())->create(array_map('trim', $_POST));
        AuditService::log('create', 'organizations', 'Organización creada: ' . ($_POST['name'] ?? ''));
        flash('success', 'Organización creada');
        redirect('/admin/organizations');
    }
}
