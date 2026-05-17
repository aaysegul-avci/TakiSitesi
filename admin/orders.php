<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startAdminSession();
requireAdminLogin();
requireAdminPermission('dashboard.view');

$message = null;
$error = null;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
        orderUpdateStatus($pdo, (int) $_POST['order_id'], (string) $_POST['status']);
        $message = 'Siparis durumu guncellendi.';
    }

    $orders = orderAll($pdo);
    $statusOptions = orderStatusOptions();
} catch (Throwable $exception) {
    $orders = [];
    $statusOptions = orderStatusOptions();
    $error = 'Siparisler yuklenirken bir sorun olustu.';
    logSystemError('admin_orders_error', $exception->getMessage());
}

$pageTitle = 'FLORIA Admin | Siparisler';
require dirname(__DIR__) . '/app/views/layouts/admin_header.php';
require dirname(__DIR__) . '/app/views/components/admin_sidebar.php';
?>
<main class="admin-main">
    <section class="catalog-toolbar">
        <div>
            <h2>Siparis Yonetimi</h2>
            <p>Siteden olusan siparisler bu ekrana duser. Durumu onaylayabilir, hazirlaniyor veya kargoya verildi olarak guncelleyebilirsiniz.</p>
        </div>
        <div class="panel-badge"><?= e((string) count($orders)); ?> Siparis</div>
    </section>

    <?php if ($message): ?>
        <div class="cart-note" style="margin-top: 24px;"><?= e($message); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="cart-note" style="margin-top: 24px;"><?= e($error); ?></div>
    <?php endif; ?>

    <section class="panel-card" style="margin-top: 24px;">
        <div class="panel-header">
            <div>
                <h2>Gelen Siparisler</h2>
                <p>Her siparis olustugunda bu listeye dusur. Musteri tarafinda gorunen durum bilgisi de bu ekrandan yonetilir.</p>
            </div>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-state">Henuz siparis olusmadi.</div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($orders as $order): ?>
                    <article class="cart-item cart-item-order">
                        <div class="cart-info" style="grid-column: 1 / -1;">
                            <h3><?= e($order['customer_name']); ?></h3>
                            <p>Siparis No: <?= e($order['order_number']); ?></p>
                            <div class="cart-caption">
                                E-posta: <?= e($order['customer_email'] ?: '-'); ?><br>
                                Telefon: <?= e($order['customer_phone'] ?: '-'); ?><br>
                                Tutar: ₺<?= e(number_format((float) $order['total_amount'], 2, ',', '.')); ?><br>
                                Tarih: <?= e((string) $order['created_at']); ?>
                            </div>
                        </div>
                        <div class="order-status-shell">
                            <span class="order-status-chip status-<?= e($order['status']); ?>"><?= e(orderStatusLabel((string) $order['status'])); ?></span>
                            <form method="post" class="order-status-form">
                                <input type="hidden" name="order_id" value="<?= e((string) $order['id']); ?>">
                                <select name="status">
                                    <?php foreach ($statusOptions as $statusValue => $statusLabel): ?>
                                        <option value="<?= e($statusValue); ?>" <?= $order['status'] === $statusValue ? 'selected' : ''; ?>>
                                            <?= e($statusLabel); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="primary-btn" type="submit">Guncelle</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
<?php require dirname(__DIR__) . '/app/views/layouts/admin_footer.php'; ?>
