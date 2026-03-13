<?php

declare(strict_types=1);

namespace App\Middlewares;

class RootMiddleware
{
    public function handle(): void
    {
        if (!can(['root'])) {
            http_response_code(403);
            echo 'Solo ROOT puede acceder a esta sección';
            exit;
        }
    }
}
