<?php

declare(strict_types=1);

function settingsAll(PDO $pdo): array
{
    $statement = $pdo->prepare('SELECT * FROM settings ORDER BY setting_key ASC');
    $statement->execute();

    $rows = $statement->fetchAll();
    $settings = [];

    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    return $settings;
}

function settingUpsert(PDO $pdo, string $key, ?string $value): void
{
    $sql = '
        INSERT INTO settings (setting_key, setting_value)
        VALUES (:setting_key, :setting_value)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute([
        'setting_key' => $key,
        'setting_value' => $value,
    ]);
}
