<?php

declare(strict_types=1);

$admin = adminUser();
?>
<aside class="admin-sidebar">
    <h1>FLORIA</h1>
    <p><?= e($admin['name'] ?? 'Yonetici'); ?></p>
    <nav>
        <a href="index.php">Dashboard</a>
        <?php if (adminCan('users.view')): ?>
            <a href="users.php">Kullanicilar</a>
        <?php endif; ?>
        <a href="products.php">Urunler</a>
        <a href="orders.php">Siparisler</a>
        <a href="sliders.php">Slider</a>
        <a href="menus.php">Menuler</a>
        <a href="settings.php">Ayarlar</a>
        <a href="profile.php">Profilim</a>
        <a href="logout.php">Cikis Yap</a>
    </nav>
</aside>
