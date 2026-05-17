<?php

declare(strict_types=1);

$config = require __DIR__ . '/config.php';

date_default_timezone_set($config['timezone']);

$db = $config['db'];
$dsn = sprintf(
    '%s:host=%s;port=%d;dbname=%s;charset=%s',
    $db['driver'],
    $db['host'],
    $db['port'],
    $db['database'],
    $db['charset']
);

try {
    $pdo = new PDO(
        $dsn,
        $db['username'],
        $db['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    require_once __DIR__ . '/../helpers/logger.php';
    logSystemError('database_connection_failed', $exception->getMessage());
    http_response_code(500);
    exit('Veritabani baglantisi kurulurken bir sorun olustu.');
}

return $pdo;
