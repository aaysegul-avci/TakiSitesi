<?php

declare(strict_types=1);

return [
    'app_name' => 'FLORIA',
    'app_url' => 'http://localhost:8000',
    'timezone' => 'Europe/Istanbul',
    'locale' => 'tr_TR',
    'db' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'floria_db',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
    'session' => [
        'name' => 'floria_admin_session',
        'public_name' => 'floria_public_session',
    ],
];
