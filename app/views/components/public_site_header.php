<?php

declare(strict_types=1);

$siteTitle = $siteTitle ?? 'FLORIA';
$siteTagline = $siteTagline ?? 'Zarafetin yeni hali';
$menus = $menus ?? [];
$publicUser = publicUser();
?>
<div class="page-topbar">
    <a href="index.php" class="page-brand">
        <strong><?= e($siteTitle); ?></strong>
        <span><?= e($siteTagline); ?></span>
    </a>
    <div class="auth-links">
        <?php if ($publicUser): ?>
            <a href="orders.php">Siparislerim</a>
            <a href="cart.php">Sepetim</a>
            <a href="logout.php">Cikis Yap</a>
        <?php else: ?>
            <a href="login.php">Giris Yap</a>
            <a href="register.php">Kayit Ol</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($menus)): ?>
    <section class="catalog-toolbar" style="margin-bottom: 24px;">
        <div>
            <div class="jewelry-ribbon" aria-label="Takı kategorileri">
                <div class="jewelry-ribbon-track">
                    <div class="jewelry-ribbon-group">
                        <span class="jewelry-pill">Yuzuk</span>
                        <span class="jewelry-dot"></span>
                        <span class="jewelry-pill">Kolye</span>
                        <span class="jewelry-dot"></span>
                        <span class="jewelry-pill">Kupe</span>
                        <span class="jewelry-dot"></span>
                        <span class="jewelry-pill">Bileklik</span>
                        <span class="jewelry-dot"></span>
                        <span class="jewelry-pill">Zarif Detaylar</span>
                    </div>
                    <div class="jewelry-ribbon-group" aria-hidden="true">
                        <span class="jewelry-pill">Yuzuk</span>
                        <span class="jewelry-dot"></span>
                        <span class="jewelry-pill">Kolye</span>
                        <span class="jewelry-dot"></span>
                        <span class="jewelry-pill">Kupe</span>
                        <span class="jewelry-dot"></span>
                        <span class="jewelry-pill">Bileklik</span>
                        <span class="jewelry-dot"></span>
                        <span class="jewelry-pill">Zarif Detaylar</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="auth-links">
            <?php foreach ($menus as $menu): ?>
                <?php
                $menuUrl = (string) ($menu['url'] ?? '');
                $menuTitle = mb_strtolower((string) ($menu['title'] ?? ''), 'UTF-8');
                $isAuthMenu = str_contains($menuUrl, 'login.php')
                    || str_contains($menuUrl, 'register.php')
                    || str_contains($menuTitle, 'giris')
                    || str_contains($menuTitle, 'kayit');

                if ($isAuthMenu) {
                    continue;
                }
                ?>
                <a href="<?= e($menu['url']); ?>" target="<?= e($menu['target']); ?>"><?= e($menu['title']); ?></a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
