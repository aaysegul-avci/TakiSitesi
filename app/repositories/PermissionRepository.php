<?php

declare(strict_types=1);

function permissionSlugsByRoleId(PDO $pdo, int $roleId): array
{
    $sql = '
        SELECT permissions.slug
        FROM role_permissions
        INNER JOIN permissions ON permissions.id = role_permissions.permission_id
        WHERE role_permissions.role_id = :role_id
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute(['role_id' => $roleId]);

    return array_map(
        static fn(array $row): string => $row['slug'],
        $statement->fetchAll()
    );
}
