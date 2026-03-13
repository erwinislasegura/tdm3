<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Setting;
use App\Services\AuditService;

class SettingController extends Controller
{
    public function index(): void
    {
        $rows = (new Setting())->all();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        $this->render('settings/index', compact('settings'));
    }

    public function store(): void
    {
        if (!verify_csrf()) {
            flash('error', 'Token inválido');
            redirect('/admin/settings');
        }

        $allowed = ['platform_name', 'contact_email', 'contact_phone', 'address', 'timezone', 'maintenance_mode', 'primary_color', 'accent_color'];
        $data = [];
        foreach ($allowed as $key) {
            $data[$key] = trim((string)($_POST[$key] ?? ''));
        }

        (new Setting())->upsertMany($data);
        AuditService::log('update', 'settings', 'Configuración global actualizada');
        flash('success', 'Configuración guardada');
        redirect('/admin/settings');
    }
}
