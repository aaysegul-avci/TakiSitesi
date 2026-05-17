<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function basePath(string $path = ''): string
{
    $root = dirname(__DIR__, 2);
    return $path === '' ? $root : $root . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return '../' . ltrim($path, '/');
}

function publicAsset(string $path): string
{
    return '../' . ltrim($path, '/');
}

function flashMessage(string $type, string $message): array
{
    return [
        'type' => $type,
        'message' => $message,
    ];
}

function renderAlert(string $type, string $message): string
{
    $allowedTypes = ['success', 'error', 'info', 'warning'];
    $safeType = in_array($type, $allowedTypes, true) ? $type : 'info';

    return sprintf(
        '<div class="alert-box alert-%s">%s</div>',
        e($safeType),
        e($message)
    );
}

function storeUploadedImage(array $file, string $targetDirectory): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Dosya yukleme sirasinda bir hata olustu.');
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $originalName = (string) ($file['name'] ?? '');
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
        throw new RuntimeException('Sadece jpg, jpeg, png, gif veya webp dosyalari yuklenebilir.');
    }

    $directoryPath = basePath($targetDirectory);
    if (!is_dir($directoryPath)) {
        mkdir($directoryPath, 0777, true);
    }

    $safeName = uniqid('img_', true) . '.' . $extension;
    $targetPath = $directoryPath . '/' . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Gorsel yuklenemedi.');
    }

    return trim($targetDirectory, '/') . '/' . $safeName;
}
