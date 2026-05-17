<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startPublicSession();
requirePublicLogin();

$orderNumber = trim((string) ($_GET['order'] ?? ''));

try {
    $settings = settingsAll($pdo);
    $menus = menuActiveAll($pdo);
    $order = $orderNumber !== '' ? orderFindByNumber($pdo, $orderNumber) : null;
    $publicUser = publicUser();

    if ($order && (int) ($order['user_id'] ?? 0) !== (int) ($publicUser['id'] ?? 0)) {
        $order = null;
    }
} catch (Throwable $exception) {
    $settings = [];
    $menus = [];
    $order = null;
    $error = 'Siparis detayi yuklenirken bir sorun olustu.';
    logSystemError('public_order_detail_error', $exception->getMessage());
}

$siteTitle = $settings['site_title'] ?? 'FLORIA';
$siteTagline = $settings['site_tagline'] ?? 'Zarafetin yeni hali';
$pageTitle = $siteTitle . ' | Siparis Detayi';

require dirname(__DIR__) . '/app/views/layouts/public_header.php';
?>
<main class="cart-page">
    <div class="cart-shell">
        <?php require dirname(__DIR__) . '/app/views/components/public_site_header.php'; ?>

        <?php if (!empty($error)): ?>
            <?= renderAlert('error', $error); ?>
        <?php endif; ?>

        <?php if (!$order): ?>
            <div class="empty-state">Goruntulemek istediginiz siparis bulunamadi.</div>
        <?php else: ?>
            <section class="cart-hero">
                <div>
                    <h1><?= e($order['order_number']); ?></h1>
                    <p>Siparisinizin icerigini, toplam tutarini ve guncel durum bilgisini bu sayfada gorebilirsiniz.</p>
                </div>
                <div class="cart-hero-stats">
                    <div class="cart-stat">
                        <strong>Durum</strong>
                        <span><?= e(orderStatusLabel((string) $order['status'])); ?></span>
                    </div>
                    <div class="cart-stat">
                        <strong>Toplam</strong>
                        <span>₺<?= e(number_format((float) $order['total_amount'], 2, ',', '.')); ?></span>
                    </div>
                </div>
            </section>

            <div class="cart-layout">
                <section class="panel-card">
                    <div class="panel-header">
                        <div>
                            <h2>Siparis Icerigi</h2>
                            <p>Bu sipariste yer alan urunler asagida listelenmektedir.</p>
                        </div>
                    </div>
                    <div class="cart-items">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="cart-item">
                                <div class="cart-info" style="grid-column: 1 / -1;">
                                    <h3><?= e($item['product_name']); ?></h3>
                                    <p>Adet: <?= e((string) $item['quantity']); ?></p>
                                    <div class="cart-caption">
                                        Birim Fiyat: ₺<?= e(number_format((float) $item['unit_price'], 2, ',', '.')); ?><br>
                                        Ara Toplam: ₺<?= e(number_format((float) $item['line_total'], 2, ',', '.')); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <aside class="summary-card">
                    <h3>Siparis Bilgisi</h3>
                    <div class="summary-list">
                        <div class="summary-row"><span>Musteri</span><strong><?= e($order['customer_name']); ?></strong></div>
                        <div class="summary-row"><span>E-posta</span><strong><?= e($order['customer_email'] ?: '-'); ?></strong></div>
                        <div class="summary-row"><span>Telefon</span><strong><?= e($order['customer_phone'] ?: '-'); ?></strong></div>
                    </div>
                    <div class="summary-total">
                        <span>Genel Toplam</span>
                        <strong>₺<?= e(number_format((float) $order['total_amount'], 2, ',', '.')); ?></strong>
                    </div>
                    <p class="summary-note"><?= e($order['address']); ?></p>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require dirname(__DIR__) . '/app/views/components/public_site_footer.php'; ?>
<?php require dirname(__DIR__) . '/app/views/layouts/public_footer.php'; ?>
