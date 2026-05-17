<?php

declare(strict_types=1);

function menuAll(PDO $pdo): array
{
    $statement = $pdo->prepare('SELECT * FROM menus ORDER BY sort_order ASC, id DESC');
    $statement->execute();

    return $statement->fetchAll();
}

function menuCreate(PDO $pdo, array $data): int
{
    $sql = '
        INSERT INTO menus (title, url, target, sort_order, is_active)
        VALUES (:title, :url, :target, :sort_order, :is_active)
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute([
        'title' => $data['title'],
        'url' => $data['url'],
        'target' => $data['target'] ?? '_self',
        'sort_order' => $data['sort_order'] ?? 0,
        'is_active' => $data['is_active'] ?? 1,
    ]);

    return (int) $pdo->lastInsertId();
}

function menuDelete(PDO $pdo, int $id): void
{
    $statement = $pdo->prepare('DELETE FROM menus WHERE id = :id');
    $statement->execute(['id' => $id]);
}

function menuDeleteMany(PDO $pdo, array $ids): int
{
    $ids = array_values(array_filter(array_map('intval', $ids)));

    if ($ids === []) {
        return 0;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $statement = $pdo->prepare("DELETE FROM menus WHERE id IN ({$placeholders})");
    $statement->execute($ids);

    return $statement->rowCount();
}

function menuActiveAll(PDO $pdo): array
{
    $statement = $pdo->prepare('SELECT * FROM menus WHERE is_active = 1 ORDER BY sort_order ASC, id ASC');
    $statement->execute();

    return $statement->fetchAll();
}
