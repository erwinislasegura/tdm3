<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Registration;

class RegistrationController extends Controller
{
    public function index(): void
    {
        $registrations = (new Registration())->all();
        $this->render('registrations/index', compact('registrations'));
    }
}
