<?php

declare(strict_types=1);

function productAll(PDO $pdo, array $filters = []): array
{
    $conditions = ['products.is_active = 1'];
    $params = [];

    if (!empty($filters['category_id'])) {
        $conditions[] = 'products.category_id = :category_id';
        $params['category_id'] = (int) $filters['category_id'];
    }

    if (!empty($filters['search'])) {
        $conditions[] = 'products.name LIKE :search';
        $params['search'] = '%' . $filters['search'] . '%';
    }

    $allowedSorts = [
        'newest' => 'products.created_at DESC',
        'price_asc' => 'products.price ASC',
        'price_desc' => 'products.price DESC',
        'name_asc' => 'products.name ASC',
        'name_desc' => 'products.name DESC',
    ];
    $orderBy = $allowedSorts[$filters['sort'] ?? 'newest'] ?? $allowedSorts['newest'];

    $sql = '
        SELECT products.*, categories.name AS category_name
        FROM products
        INNER JOIN categories ON categories.id = products.category_id
        WHERE ' . implode(' AND ', $conditions) . '
        ORDER BY ' . $orderBy . '
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function productAdminList(PDO $pdo): array
{
    return productAdminFilteredList($pdo);
}

function productAdminFilteredList(PDO $pdo, array $filters = []): array
{
    $conditions = ['1=1'];
    $params = [];

    if (!empty($filters['category_id'])) {
        $conditions[] = 'products.category_id = :category_id';
        $params['category_id'] = (int) $filters['category_id'];
    }

    if (!empty($filters['search'])) {
        $conditions[] = '(products.name LIKE :search OR products.slug LIKE :search)';
        $params['search'] = '%' . $filters['search'] . '%';
    }

    if (($filters['is_active'] ?? '') !== '') {
        $conditions[] = 'products.is_active = :is_active';
        $params['is_active'] = (int) $filters['is_active'];
    }

    $allowedSorts = [
        'newest' => 'products.id DESC',
        'price_asc' => 'products.price ASC',
        'price_desc' => 'products.price DESC',
        'name_asc' => 'products.name ASC',
        'name_desc' => 'products.name DESC',
    ];
    $orderBy = $allowedSorts[$filters['sort'] ?? 'newest'] ?? $allowedSorts['newest'];

    $sql = '
        SELECT products.*, categories.name AS category_name
        FROM products
        INNER JOIN categories ON categories.id = products.category_id
        WHERE ' . implode(' AND ', $conditions) . '
        ORDER BY ' . $orderBy . '
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function productCreate(PDO $pdo, array $data): int
{
    $sql = '
        INSERT INTO products (
            category_id, name, slug, short_description, description,
            price, stock, sku, cover_image, is_active
        ) VALUES (
            :category_id, :name, :slug, :short_description, :description,
            :price, :stock, :sku, :cover_image, :is_active
        )
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute([
        'category_id' => $data['category_id'],
        'name' => $data['name'],
        'slug' => $data['slug'],
        'short_description' => $data['short_description'] ?? null,
        'description' => $data['description'] ?? null,
        'price' => $data['price'],
        'stock' => $data['stock'] ?? 0,
        'sku' => $data['sku'] ?? null,
        'cover_image' => $data['cover_image'] ?? null,
        'is_active' => $data['is_active'] ?? 1,
    ]);

    return (int) $pdo->lastInsertId();
}

function productCount(PDO $pdo): int
{
    $statement = $pdo->prepare('SELECT COUNT(*) FROM products');
    $statement->execute();

    return (int) $statement->fetchColumn();
}

function productFindBySlug(PDO $pdo, string $slug): ?array
{
    $sql = '
        SELECT products.*, categories.name AS category_name
        FROM products
        INNER JOIN categories ON categories.id = products.category_id
        WHERE products.slug = :slug AND products.is_active = 1
        LIMIT 1
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute(['slug' => $slug]);
    $product = $statement->fetch();

    return $product ?: null;
}

function productRecent(PDO $pdo, int $limit = 4): array
{
    $limit = max(1, $limit);
    $statement = $pdo->prepare("SELECT products.*, categories.name AS category_name
        FROM products
        INNER JOIN categories ON categories.id = products.category_id
        WHERE products.is_active = 1
        ORDER BY products.created_at DESC
        LIMIT {$limit}");
    $statement->execute();

    return $statement->fetchAll();
}

function productDeleteMany(PDO $pdo, array $ids): int
{
    $ids = array_values(array_filter(array_map('intval', $ids)));

    if ($ids === []) {
        return 0;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $statement = $pdo->prepare("DELETE FROM products WHERE id IN ({$placeholders})");
    $statement->execute($ids);

    return $statement->rowCount();
}
