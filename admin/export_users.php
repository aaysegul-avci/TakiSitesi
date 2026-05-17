<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startAdminSession();
requireAdminLogin();
requireAdminPermission('users.view');

$users = userAll($pdo);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=floria-users.csv');

$output = fopen('php://output', 'wb');
fputcsv($output, ['ID', 'Ad Soyad', 'E-posta', 'Telefon', 'Rol', 'Kayit Tarihi']);

foreach ($users as $user) {
    fputcsv($output, [
        $user['id'],
        $user['name'],
        $user['email'],
        $user['phone'],
        $user['role_name'],
        $user['created_at'],
    ]);
}

fclose($output);
exit;
