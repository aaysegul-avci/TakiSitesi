<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startAdminSession();
requireAdminLogin();

try {
    $overview = dashboardStats($pdo);
    $recentLogs = logRecent($pdo, 5);
    $stats = [
        ['title' => 'Toplam Kullanici', 'value' => (string) $overview['users_total'], 'desc' => 'tum kullanicilar'],
        ['title' => 'Toplam Urun', 'value' => (string) $overview['products_total'], 'desc' => 'products tablosu'],
        ['title' => 'Aktif Slider', 'value' => (string) $overview['sliders_active'], 'desc' => 'sliders tablosu'],
        ['title' => 'Log Kaydi', 'value' => (string) $overview['logs_total'], 'desc' => 'logs tablosu'],
        ['title' => 'Toplam Siparis', 'value' => (string) $overview['orders_total'], 'desc' => 'orders tablosu'],
    ];
} catch (Throwable $exception) {
    $stats = [
        ['title' => 'Toplam Kullanici', 'value' => '0', 'desc' => 'veri okunamadi'],
        ['title' => 'Toplam Urun', 'value' => '0', 'desc' => 'veri okunamadi'],
        ['title' => 'Aktif Slider', 'value' => '0', 'desc' => 'veri okunamadi'],
        ['title' => 'Son Hata Kaydi', 'value' => '0', 'desc' => 'veri okunamadi'],
    ];
    $recentLogs = [];
    logSystemError('admin_dashboard_error', $exception->getMessage());
}

$pageTitle = 'FLORIA Admin | Dashboard';
require dirname(__DIR__) . '/app/views/layouts/admin_header.php';
require dirname(__DIR__) . '/app/views/components/admin_sidebar.php';
?>
<main class="admin-main">
    <section class="catalog-toolbar">
        <div>
            <h2>Dashboard</h2>
            <p>Magazanizin genel durumunu, siparis hareketlerini ve sistem ozetini bu ekrandan takip edin.</p>
        </div>
    </section>

    <section class="product-grid" style="margin-top: 24px;">
        <?php foreach ($stats as $stat): ?>
            <article class="product-card">
                <span class="product-meta">Widget</span>
                <h3><?= e($stat['title']); ?></h3>
                <p><?= e($stat['value']); ?></p>
                <div class="product-copy"><?= e($stat['desc']); ?></div>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="cart-layout" style="margin-top: 24px;">
        <section class="panel-card">
            <div class="panel-header">
                <div>
                    <h2>Son Sistem Kayitlari</h2>
                    <p>Veritabani ve sistem kaynakli son kayitlar bu alanda listelenir.</p>
                </div>
                <div class="panel-badge">Logs</div>
            </div>

            <?php if (empty($recentLogs)): ?>
                <div class="empty-state">Henuz log kaydi bulunmuyor ya da logs tablosu baglanmadi.</div>
            <?php else: ?>
                <div class="cart-items">
                    <?php foreach ($recentLogs as $log): ?>
                        <div class="cart-item">
                            <div class="cart-info" style="grid-column: 1 / -1;">
                                <h3><?= e($log['log_type']); ?></h3>
                                <p><?= e($log['message']); ?></p>
                                <div class="cart-caption"><?= e((string) ($log['ip_address'] ?? 'IP yok')); ?> | <?= e($log['created_at']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <aside class="summary-card">
            <h3>Dashboard Notu</h3>
            <div class="summary-list">
                <div class="summary-row"><span>Kullanicilar</span><strong>Canli</strong></div>
                <div class="summary-row"><span>Urunler</span><strong>Canli</strong></div>
                <div class="summary-row"><span>Sliderlar</span><strong>Canli</strong></div>
                <div class="summary-row"><span>Loglar</span><strong>Canli</strong></div>
            </div>
            <p class="summary-note">Bu ekranda kullanici, urun, slider, siparis ve log sayilari canli olarak takip edilir.</p>
        </aside>
    </section>

    <section class="panel-card" style="margin-top: 24px;">
        <div class="panel-header">
            <div>
                <h2>Yonetim Ozeti</h2>
                <p>Urunler, siparisler, sliderlar, menuler ve genel ayarlar panel uzerinden yonetilmektedir.</p>
            </div>
        </div>
        <div class="cart-note">
            Siparis durumlari guncellendikce kullanici tarafindaki Siparislerim ekranina da ayni bilgi yansir.
        </div>
    </section>
</main>
<?php require dirname(__DIR__) . '/app/views/layouts/admin_footer.php'; ?>
