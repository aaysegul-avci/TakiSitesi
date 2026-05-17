<?php

declare(strict_types=1);

function logRecent(PDO $pdo, int $limit = 5): array
{
    $limit = max(1, $limit);
    $statement = $pdo->prepare("SELECT * FROM logs ORDER BY created_at DESC LIMIT {$limit}");
    $statement->execute();

    return $statement->fetchAll();
}

function logCount(PDO $pdo): int
{
    $statement = $pdo->prepare('SELECT COUNT(*) FROM logs');
    $statement->execute();

    return (int) $statement->fetchColumn();
}
