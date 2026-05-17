<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';
require_once dirname(__DIR__) . '/app/helpers/pdf.php';

startAdminSession();
requireAdminLogin();
requireAdminPermission('dashboard.view');

$orders = orderAll($pdo);
$lines = [];

foreach ($orders as $order) {
    $lines[] = sprintf(
        '#%d | %s | %s | %s TL | %s | %s',
        $order['id'],
        $order['order_number'],
        $order['customer_name'],
        number_format((float) $order['total_amount'], 2, '.', ''),
        orderStatusLabel((string) $order['status']),
        $order['created_at']
    );
}

outputSimplePdf('floria-orders.pdf', 'FLORIA Siparis Raporu', $lines);
