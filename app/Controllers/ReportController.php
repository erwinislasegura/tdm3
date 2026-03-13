<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class ReportController extends Controller
{
    public function index(): void
    {
        $this->render('reports/index');
    }
}
