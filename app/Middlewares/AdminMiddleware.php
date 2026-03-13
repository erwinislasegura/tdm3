<?php

declare(strict_types=1);

namespace App\Middlewares;

class AdminMiddleware
{
    public function handle(): void
    {
        if (!can(['root', 'super_admin', 'organization_admin', 'tournament_organizer'])) {
            http_response_code(403);
            echo 'Acceso denegado';
            exit;
        }
    }
}
