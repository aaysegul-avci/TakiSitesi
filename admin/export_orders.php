<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startAdminSession();
requireAdminLogin();
requireAdminPermission('dashboard.view');

$orders = orderAll($pdo);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=floria-orders.csv');

$output = fopen('php://output', 'wb');
fputcsv($output, ['ID', 'Siparis No', 'Musteri', 'E-posta', 'Telefon', 'Tutar', 'Durum', 'Tarih']);

foreach ($orders as $order) {
    fputcsv($output, [
        $order['id'],
        $order['order_number'],
        $order['customer_name'],
        $order['customer_email'],
        $order['customer_phone'],
        $order['total_amount'],
        $order['status'],
        $order['created_at'],
    ]);
}

fclose($output);
exit;
