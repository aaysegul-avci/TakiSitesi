<?php

declare(strict_types=1);

function dashboardStats(PDO $pdo): array
{
    $userStatement = $pdo->prepare('SELECT COUNT(*) FROM users');
    $userStatement->execute();

    $orderStatement = $pdo->prepare('SELECT COUNT(*) FROM orders');
    $orderStatement->execute();

    return [
        'users_total' => (int) $userStatement->fetchColumn(),
        'products_total' => productCount($pdo),
        'sliders_active' => sliderCountActive($pdo),
        'logs_total' => logCount($pdo),
        'orders_total' => (int) $orderStatement->fetchColumn(),
    ];
}
