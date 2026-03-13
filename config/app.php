<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => 'TDM Pro Manager',
        'url' => getenv('APP_URL') ?: 'http://localhost',
        'base_path' => getenv('APP_BASE_PATH') ?: '',
        'debug' => true,
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_DATABASE') ?: 'tdm3',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
    ],
];
