<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startPublicSession();
requirePublicLogin();

try {
    $settings = settingsAll($pdo);
    $menus = menuActiveAll($pdo);
    $orders = orderListByUserId($pdo, (int) (publicUser()['id'] ?? 0));
} catch (Throwable $exception) {
    $settings = [];
    $menus = [];
    $orders = [];
    $error = 'Siparisleriniz yuklenirken bir sorun olustu.';
    logSystemError('public_orders_error', $exception->getMessage());
}

$siteTitle = $settings['site_title'] ?? 'FLORIA';
$siteTagline = $settings['site_tagline'] ?? 'Zarafetin yeni hali';
$pageTitle = $siteTitle . ' | Siparislerim';

require dirname(__DIR__) . '/app/views/layouts/public_header.php';
?>
<main class="cart-page">
    <div class="cart-shell">
        <?php require dirname(__DIR__) . '/app/views/components/public_site_header.php'; ?>

        <section class="cart-hero">
            <div>
                <h1><?= e($settings['orders_hero_title'] ?? 'Siparislerinizi adim adim takip edin.'); ?></h1>
                <p><?= e($settings['orders_hero_text'] ?? 'Siparislerinizin guncel durumunu, olusturma tarihini ve toplam tutarini bu ekranda rahatca gorebilirsiniz.'); ?></p>
            </div>
            <div class="cart-hero-stats">
                <div class="cart-stat">
                    <strong><?= e((string) count($orders)); ?></strong>
                    <span>Toplam siparisiniz</span>
                </div>
                <div class="cart-stat">
                    <strong>Guncel</strong>
                    <span>Siparis durumunuz son hareket bilgisine gore burada yenilenir.</span>
                </div>
            </div>
        </section>

        <?php if (!empty($error)): ?>
            <?= renderAlert('error', $error); ?>
        <?php endif; ?>

        <section class="panel-card">
            <div class="panel-header">
                <div>
                    <h2>Siparislerim</h2>
                    <p>Olusturdugunuz siparisleri ve surec durumlarini tek ekrandan takip edebilirsiniz.</p>
                </div>
            </div>

            <?php if (empty($orders)): ?>
                <div class="empty-state">Henuz siparisiniz bulunmuyor.</div>
            <?php else: ?>
                <div class="cart-items">
                    <?php foreach ($orders as $order): ?>
                        <article class="cart-item cart-item-order">
                            <div class="cart-info" style="grid-column: 1 / -1;">
                                <h3><a href="order.php?order=<?= e($order['order_number']); ?>"><?= e($order['order_number']); ?></a></h3>
                                <p>Toplam: ₺<?= e(number_format((float) $order['total_amount'], 2, ',', '.')); ?></p>
                                <div class="cart-caption">
                                    Tarih: <?= e((string) $order['created_at']); ?><br>
                                    Son Guncelleme: <?= e((string) $order['updated_at']); ?>
                                </div>
                            </div>
                            <div class="order-status-shell">
                                <span class="order-status-chip status-<?= e($order['status']); ?>"><?= e(orderStatusLabel((string) $order['status'])); ?></span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>
<?php require dirname(__DIR__) . '/app/views/components/public_site_footer.php'; ?>
<?php require dirname(__DIR__) . '/app/views/layouts/public_footer.php'; ?>
