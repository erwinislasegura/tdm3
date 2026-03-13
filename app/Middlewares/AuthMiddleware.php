<?php

declare(strict_types=1);

namespace App\Middlewares;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!auth_user()) {
            flash('error', 'Debes iniciar sesión.');
            redirect('/login');
        }
    }
}
