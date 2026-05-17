<?php

declare(strict_types=1);

function logSystemError(string $type, string $message): void
{
    $directory = dirname(__DIR__, 2) . '/storage/logs';
    $file = $directory . '/system.log';

    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'cli';
    $time = date('Y-m-d H:i:s');
    $line = sprintf("[%s] [%s] [%s] %s%s", $time, $ip, $type, $message, PHP_EOL);

    file_put_contents($file, $line, FILE_APPEND);

    if (!empty($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
        try {
            $statement = $GLOBALS['pdo']->prepare('
                INSERT INTO logs (log_type, ip_address, message, context)
                VALUES (:log_type, :ip_address, :message, :context)
            ');
            $statement->execute([
                'log_type' => $type,
                'ip_address' => $ip,
                'message' => $message,
                'context' => json_encode(['time' => $time], JSON_UNESCAPED_UNICODE),
            ]);
        } catch (Throwable) {
            // File logging remains the fallback if database logging is unavailable.
        }
    }
}
