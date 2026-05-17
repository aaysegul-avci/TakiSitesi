<?php

declare(strict_types=1);

function roleFindBySlug(PDO $pdo, string $slug): ?array
{
    $statement = $pdo->prepare('SELECT * FROM roles WHERE slug = :slug LIMIT 1');
    $statement->execute(['slug' => $slug]);
    $role = $statement->fetch();

    return $role ?: null;
}

function roleAll(PDO $pdo): array
{
    $statement = $pdo->prepare('SELECT * FROM roles ORDER BY name ASC');
    $statement->execute();

    return $statement->fetchAll();
}
