<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'FLORIA';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?= e($pageTitle); ?></title>
    <link rel="stylesheet" href="<?= publicAsset('styles.css?v=20260331-4'); ?>">
</head>
<body>
