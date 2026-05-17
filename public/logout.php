<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startPublicSession();

unset($_SESSION['public_user']);
session_regenerate_id(true);

header('Location: index.php');
exit;
