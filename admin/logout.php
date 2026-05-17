<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/helpers/auth.php';

startAdminSession();
$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;
