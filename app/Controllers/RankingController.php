<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ranking;

class RankingController extends Controller
{
    public function index(): void
    {
        $ranking = (new Ranking())->top(100);
        $this->render('rankings/index', compact('ranking'));
    }
}
