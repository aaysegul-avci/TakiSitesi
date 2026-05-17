<?php

declare(strict_types=1);

function userFindByEmail(PDO $pdo, string $email): ?array
{
    $sql = '
        SELECT users.*, roles.name AS role_name, roles.slug AS role_slug
        FROM users
        INNER JOIN roles ON roles.id = users.role_id
        WHERE users.email = :email
        LIMIT 1
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    return $user ?: null;
}

function userCreate(PDO $pdo, array $data): int
{
    $sql = '
        INSERT INTO users (role_id, name, email, password_hash, phone, is_active)
        VALUES (:role_id, :name, :email, :password_hash, :phone, :is_active)
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute([
        'role_id' => $data['role_id'],
        'name' => $data['name'],
        'email' => $data['email'],
        'password_hash' => $data['password_hash'],
        'phone' => $data['phone'] ?? null,
        'is_active' => $data['is_active'] ?? 1,
    ]);

    return (int) $pdo->lastInsertId();
}

function userUpdateLastLogin(PDO $pdo, int $userId): void
{
    $statement = $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
    $statement->execute(['id' => $userId]);
}

function userCountAdmins(PDO $pdo): int
{
    $sql = '
        SELECT COUNT(*) AS total
        FROM users
        INNER JOIN roles ON roles.id = users.role_id
        WHERE roles.slug = :slug
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute(['slug' => 'super-admin']);

    return (int) $statement->fetchColumn();
}

function userUpdateProfile(PDO $pdo, int $userId, array $data): void
{
    $fields = [
        'name = :name',
        'email = :email',
        'phone = :phone',
    ];

    $params = [
        'id' => $userId,
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'] ?? null,
    ];

    if (!empty($data['password_hash'])) {
        $fields[] = 'password_hash = :password_hash';
        $params['password_hash'] = $data['password_hash'];
    }

    $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
    $statement = $pdo->prepare($sql);
    $statement->execute($params);
}

function userCount(PDO $pdo): int
{
    $statement = $pdo->prepare('SELECT COUNT(*) FROM users');
    $statement->execute();

    return (int) $statement->fetchColumn();
}

function userAll(PDO $pdo): array
{
    $sql = '
        SELECT users.id, users.name, users.email, users.phone, users.is_active, users.created_at, roles.name AS role_name, roles.slug AS role_slug
        FROM users
        INNER JOIN roles ON roles.id = users.role_id
        ORDER BY users.id DESC
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute();

    return $statement->fetchAll();
}
