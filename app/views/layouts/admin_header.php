<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'FLORIA Admin';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?= e($pageTitle); ?></title>
    <link rel="stylesheet" href="../styles.css?v=20260331-4">
    <style>
        body.admin-body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(180deg, #fcf8f5 0%, #f7f1ec 100%);
            font-family: 'Montserrat', sans-serif;
            color: #2f2430;
        }
        .admin-shell {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            min-height: 100vh;
        }
        .admin-sidebar {
            padding: 28px 22px;
            background: #382730;
            color: #fff;
        }
        .admin-sidebar h1 {
            margin: 0 0 24px;
            font-family: 'Cormorant Garamond', serif;
            font-size: 42px;
        }
        .admin-sidebar nav {
            position: static;
            transform: none;
            width: auto;
            height: auto;
            padding-top: 0;
            background: transparent;
            box-shadow: none;
        }
        .admin-sidebar nav a {
            display: block;
            margin-bottom: 10px;
            padding: 12px 14px;
            border-radius: 16px;
            text-decoration: none;
            color: rgba(255,255,255,0.86);
            background: rgba(255,255,255,0.06);
        }
        .admin-main {
            padding: 30px;
        }
        @media (max-width: 900px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="admin-body">
<div class="admin-shell">
