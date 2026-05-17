<?php

declare(strict_types=1);

function categoryAll(PDO $pdo): array
{
    $statement = $pdo->prepare('SELECT * FROM categories ORDER BY sort_order ASC, name ASC');
    $statement->execute();

    return $statement->fetchAll();
}
