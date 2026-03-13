<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Tournament;
use App\Models\Ranking;

class PublicController extends Controller
{
    public function home(): void
    {
        $tournaments = (new Tournament())->publicList();
        $ranking = (new Ranking())->top(8);
        $this->render('public/home', compact('tournaments', 'ranking'));
    }

    public function tournaments(): void
    {
        $tournaments = (new Tournament())->publicList();
        $this->render('public/tournaments', compact('tournaments'));
    }

    public function rankings(): void
    {
        $ranking = (new Ranking())->top(100);
        $this->render('public/rankings', compact('ranking'));
    }

    public function about(): void
    {
        $this->render('public/about');
    }

    public function contact(): void
    {
        $this->render('public/contact');
    }
}
