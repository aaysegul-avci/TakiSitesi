<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/helpers/logger.php';
require_once __DIR__ . '/helpers/auth.php';

$config = require __DIR__ . '/config/config.php';
$pdo = require __DIR__ . '/config/database.php';
$GLOBALS['pdo'] = $pdo;

date_default_timezone_set($config['timezone']);

require_once __DIR__ . '/repositories/RoleRepository.php';
require_once __DIR__ . '/repositories/PermissionRepository.php';
require_once __DIR__ . '/repositories/UserRepository.php';
require_once __DIR__ . '/repositories/CategoryRepository.php';
require_once __DIR__ . '/repositories/ProductRepository.php';
require_once __DIR__ . '/repositories/SliderRepository.php';
require_once __DIR__ . '/repositories/MenuRepository.php';
require_once __DIR__ . '/repositories/SettingRepository.php';
require_once __DIR__ . '/repositories/LogRepository.php';
require_once __DIR__ . '/repositories/DashboardRepository.php';
require_once __DIR__ . '/repositories/OrderRepository.php';
