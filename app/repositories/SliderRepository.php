<?php

declare(strict_types=1);

function sliderAll(PDO $pdo): array
{
    $statement = $pdo->prepare('SELECT * FROM sliders ORDER BY sort_order ASC, id DESC');
    $statement->execute();

    return $statement->fetchAll();
}

function sliderCreate(PDO $pdo, array $data): int
{
    $sql = '
        INSERT INTO sliders (title, description, image_path, button_text, button_url, sort_order, is_active)
        VALUES (:title, :description, :image_path, :button_text, :button_url, :sort_order, :is_active)
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute([
        'title' => $data['title'],
        'description' => $data['description'] ?? null,
        'image_path' => $data['image_path'],
        'button_text' => $data['button_text'] ?? null,
        'button_url' => $data['button_url'] ?? null,
        'sort_order' => $data['sort_order'] ?? 0,
        'is_active' => $data['is_active'] ?? 1,
    ]);

    return (int) $pdo->lastInsertId();
}

function sliderDelete(PDO $pdo, int $id): void
{
    $statement = $pdo->prepare('DELETE FROM sliders WHERE id = :id');
    $statement->execute(['id' => $id]);
}

function sliderDeleteMany(PDO $pdo, array $ids): int
{
    $ids = array_values(array_filter(array_map('intval', $ids)));

    if ($ids === []) {
        return 0;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $statement = $pdo->prepare("DELETE FROM sliders WHERE id IN ({$placeholders})");
    $statement->execute($ids);

    return $statement->rowCount();
}

function sliderCountActive(PDO $pdo): int
{
    $statement = $pdo->prepare('SELECT COUNT(*) FROM sliders WHERE is_active = 1');
    $statement->execute();

    return (int) $statement->fetchColumn();
}
