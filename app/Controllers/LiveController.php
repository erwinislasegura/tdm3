<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\LiveScore;
use App\Services\PermissionService;

class LiveController extends Controller
{
    public function admin(): void
    {
        PermissionService::authorize('live.view');
        $this->render('live/admin', [
            'feed' => (new LiveScore())->feed(),
            'isAdminView' => true,
        ]);
    }

    public function public(): void
    {
        $this->render('live/public', [
            'feed' => (new LiveScore())->feed(),
            'isAdminView' => false,
        ]);
    }

    public function feed(): void
    {
        if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin')) {
            PermissionService::authorize('live.view');
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode((new LiveScore())->feed(), JSON_UNESCAPED_UNICODE);
    }
}
