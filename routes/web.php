<?php

declare(strict_types=1);

use App\Controllers\AuditController;
use App\Controllers\CompetitionFormatController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\LiveController;
use App\Controllers\MatchController;
use App\Controllers\OrganizationController;
use App\Controllers\PlayerController;
use App\Controllers\PublicController;
use App\Controllers\RankingController;
use App\Controllers\RegistrationController;
use App\Controllers\ReportController;
use App\Controllers\SettingController;
use App\Controllers\TournamentController;
use App\Controllers\UserController;
use App\Middlewares\AdminMiddleware;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RootMiddleware;

$router->get('/', [AuthController::class, 'loginForm']);
$router->get('/torneos', [PublicController::class, 'tournaments']);
$router->get('/rankings', [PublicController::class, 'rankings']);
$router->get('/nosotros', [PublicController::class, 'about']);
$router->get('/contacto', [PublicController::class, 'contact']);
$router->get('/live', [LiveController::class, 'public']);
$router->get('/live/feed', [LiveController::class, 'feed']);

$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

$admin = [AuthMiddleware::class, AdminMiddleware::class];
$rootOnly = [AuthMiddleware::class, RootMiddleware::class];

$router->get('/admin/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class]);
$router->get('/admin/organizations', [OrganizationController::class, 'index'], $admin);
$router->post('/admin/organizations', [OrganizationController::class, 'store'], $admin);
$router->get('/admin/players', [PlayerController::class, 'index'], $admin);
$router->post('/admin/players', [PlayerController::class, 'store'], $admin);
$router->get('/admin/tournaments', [TournamentController::class, 'index'], $admin);
$router->post('/admin/tournaments', [TournamentController::class, 'store'], $admin);
$router->get('/admin/registrations', [RegistrationController::class, 'index'], $admin);
$router->get('/admin/matches', [MatchController::class, 'index'], $admin);
$router->get('/admin/live', [LiveController::class, 'admin'], [AuthMiddleware::class]);
$router->get('/admin/live/feed', [LiveController::class, 'feed'], [AuthMiddleware::class]);
$router->get('/admin/rankings', [RankingController::class, 'index'], [AuthMiddleware::class]);
$router->get('/admin/reports', [ReportController::class, 'index'], [AuthMiddleware::class]);

$router->get('/admin/users', [UserController::class, 'index'], $rootOnly);
$router->post('/admin/users', [UserController::class, 'store'], $rootOnly);
$router->get('/admin/settings', [SettingController::class, 'index'], $rootOnly);
$router->post('/admin/settings', [SettingController::class, 'store'], $rootOnly);
$router->get('/admin/audit-logs', [AuditController::class, 'index'], $rootOnly);


$router->get('/admin/competition-formats', [CompetitionFormatController::class, 'index'], [AuthMiddleware::class]);
$router->post('/admin/competition-formats', [CompetitionFormatController::class, 'store'], [AuthMiddleware::class]);
$router->get('/admin/competition-formats/{id}', [CompetitionFormatController::class, 'show'], [AuthMiddleware::class]);
$router->post('/admin/competition-formats/{id}/generate-groups', [CompetitionFormatController::class, 'generateGroups'], [AuthMiddleware::class]);
$router->post('/admin/competition-formats/{id}/matches/{id}/score', [CompetitionFormatController::class, 'scoreMatch'], [AuthMiddleware::class]);
$router->post('/admin/competition-formats/{id}/close-groups', [CompetitionFormatController::class, 'closeGroups'], [AuthMiddleware::class]);
$router->post('/admin/competition-formats/{id}/generate-knockout', [CompetitionFormatController::class, 'generateKnockout'], [AuthMiddleware::class]);
