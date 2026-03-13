<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Setting extends Model
{
    public function all(): array
    {
        return $this->db->query('SELECT setting_key, setting_value FROM settings ORDER BY setting_key')->fetchAll();
    }

    public function upsertMany(array $settings): void
    {
        $stmt = $this->db->prepare('INSERT INTO settings (setting_key,setting_value,updated_at) VALUES (?,?,NOW()) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),updated_at=NOW()');
        foreach ($settings as $key => $value) {
            $stmt->execute([$key, $value]);
        }
    }
}
