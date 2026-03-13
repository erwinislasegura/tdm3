<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Services\AuditService;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        $this->render('auth/login');
    }

    public function login(): void
    {
        if (!verify_csrf()) {
            flash('error', 'Token CSRF inválido');
            redirect('/login');
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?: '';
        $password = $_POST['password'] ?? '';

        $user = (new User())->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            flash('error', 'Credenciales inválidas');
            AuditService::log('login_failed', 'auth', 'Intento fallido: ' . $email);
            redirect('/login');
        }

        $permissions = (new User())->permissionsForUser((int)$user['id']);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role_name' => $user['role_name'] ?? 'viewer',
            'permissions' => $permissions,
        ];

        if ($email === 'root@system.local') {
            flash('success', 'Por seguridad, fuerza cambio de contraseña en primer acceso.');
        }

        AuditService::log('login_success', 'auth', 'Ingreso exitoso de ' . $email);
        redirect('/admin/dashboard');
    }

    public function logout(): void
    {
        AuditService::log('logout', 'auth', 'Cierre de sesión');
        session_destroy();
        redirect('/login');
    }
}
