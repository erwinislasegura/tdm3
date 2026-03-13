<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\MatchModel;

class MatchController extends Controller
{
    public function index(): void
    {
        $matches = (new MatchModel())->all();
        $this->render('matches/index', compact('matches'));
    }
}
