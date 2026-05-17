<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';
require_once dirname(__DIR__) . '/app/helpers/pdf.php';

startAdminSession();
requireAdminLogin();
requireAdminPermission('products.update');

$products = productAdminFilteredList($pdo);
$lines = [];

foreach ($products as $product) {
    $lines[] = sprintf(
        '#%d | %s | %s | %s TL | Stok: %d | %s',
        $product['id'],
        $product['category_name'],
        $product['name'],
        number_format((float) $product['price'], 2, '.', ''),
        (int) $product['stock'],
        $product['is_active'] ? 'Aktif' : 'Pasif'
    );
}

outputSimplePdf('floria-products.pdf', 'FLORIA Urun Raporu', $lines);
