<?php

declare(strict_types=1);

function startAdminSession(): void
{
    $config = require __DIR__ . '/../config/config.php';

    if (session_status() === PHP_SESSION_NONE) {
        session_name($config['session']['name']);
        session_start();
    }
}

function startPublicSession(): void
{
    $config = require __DIR__ . '/../config/config.php';

    if (session_status() === PHP_SESSION_NONE) {
        session_name($config['session']['public_name']);
        session_start();
    }
}

function adminIsLoggedIn(): bool
{
    startAdminSession();
    return isset($_SESSION['admin']);
}

function adminUser(): ?array
{
    startAdminSession();
    return $_SESSION['admin'] ?? null;
}

function requireAdminLogin(): void
{
    if (!adminIsLoggedIn()) {
        logSystemError('admin_login_required', 'Oturumsuz admin sayfa erisim denemesi.');
        header('Location: login.php');
        exit;
    }
}

function adminHasRole(array $roles): bool
{
    $admin = adminUser();

    if (!$admin || !isset($admin['role_name'])) {
        return false;
    }

    return in_array($admin['role_name'], $roles, true);
}

function adminCan(string $permission): bool
{
    $admin = adminUser();

    if (!$admin) {
        return false;
    }

    $permissions = $admin['permissions'] ?? [];
    return in_array($permission, $permissions, true);
}

function requireAdminPermission(string $permission): void
{
    if (!adminCan($permission)) {
        logSystemError('admin_permission_denied', 'Yetkisiz erisim denemesi: ' . $permission);
        header('Location: index.php');
        exit;
    }
}

function publicUser(): ?array
{
    startPublicSession();
    return $_SESSION['public_user'] ?? null;
}

function publicIsLoggedIn(): bool
{
    return publicUser() !== null;
}

function requirePublicLogin(): void
{
    if (!publicIsLoggedIn()) {
        logSystemError('public_login_required', 'Oturumsuz kullanici korumali sayfa erisim denemesi.');
        header('Location: login.php');
        exit;
    }
}
