<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startAdminSession();
requireAdminLogin();
requireAdminPermission('products.update');

$products = productAdminFilteredList($pdo);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=floria-products.csv');

$output = fopen('php://output', 'wb');
fputcsv($output, ['ID', 'Kategori', 'Urun Adi', 'Slug', 'Fiyat', 'Stok', 'Durum']);

foreach ($products as $product) {
    fputcsv($output, [
        $product['id'],
        $product['category_name'],
        $product['name'],
        $product['slug'],
        $product['price'],
        $product['stock'],
        $product['is_active'] ? 'Aktif' : 'Pasif',
    ]);
}

fclose($output);
exit;
