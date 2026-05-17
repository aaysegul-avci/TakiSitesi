<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';
require_once dirname(__DIR__) . '/app/helpers/pdf.php';

startAdminSession();
requireAdminLogin();
requireAdminPermission('users.view');

$users = userAll($pdo);
$lines = [];

foreach ($users as $user) {
    $lines[] = sprintf(
        '#%d | %s | %s | Rol: %s | Telefon: %s',
        $user['id'],
        $user['name'],
        $user['email'],
        $user['role_name'],
        $user['phone'] ?: '-'
    );
}

outputSimplePdf('floria-users.pdf', 'FLORIA Kullanici Raporu', $lines);
