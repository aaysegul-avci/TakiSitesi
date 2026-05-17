<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startAdminSession();
requireAdminLogin();
requireAdminPermission('menus.manage');

$message = null;
$error = null;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (($_POST['action'] ?? '') === 'bulk_delete') {
            $deletedCount = menuDeleteMany($pdo, (array) ($_POST['selected_ids'] ?? []));
            $message = $deletedCount > 0
                ? $deletedCount . ' menu tek hamlede silindi.'
                : 'Toplu silme icin menu secilmedi.';
        } elseif (isset($_POST['delete_id'])) {
            menuDelete($pdo, (int) $_POST['delete_id']);
            $message = 'Menu kaydi silindi.';
        } else {
            $title = trim((string) ($_POST['title'] ?? ''));
            $url = trim((string) ($_POST['url'] ?? ''));
            $target = (string) ($_POST['target'] ?? '_self');
            $sortOrder = (int) ($_POST['sort_order'] ?? 0);

            if ($title === '' || $url === '') {
                $error = 'Menu basligi ve linki zorunludur.';
            } else {
                menuCreate($pdo, [
                    'title' => $title,
                    'url' => $url,
                    'target' => $target,
                    'sort_order' => $sortOrder,
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                ]);
                $message = 'Menu ogesi kaydedildi.';
            }
        }
    }

    $menus = menuAll($pdo);
} catch (Throwable $exception) {
    $menus = [];
    $error = 'Menu modulu yuklenirken bir sorun olustu.';
    logSystemError('admin_menus_error', $exception->getMessage());
}

$pageTitle = 'FLORIA Admin | Menuler';
require dirname(__DIR__) . '/app/views/layouts/admin_header.php';
require dirname(__DIR__) . '/app/views/components/admin_sidebar.php';
?>
<main class="admin-main">
    <section class="catalog-toolbar">
        <div>
            <h2>Menu Yonetimi</h2>
            <p>Header navigasyon menusu icin baslik, link ve sira bilgileri bu ekrandan yonetilecek.</p>
        </div>
    </section>

    <?php if ($message): ?>
        <div class="cart-note" style="margin-top: 24px;"><?= e($message); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="cart-note" style="margin-top: 24px;"><?= e($error); ?></div>
    <?php endif; ?>

    <section class="cart-layout" style="margin-top: 24px;">
        <section class="panel-card">
            <div class="panel-header">
                <div>
                    <h2>Yeni Menu Ogesi</h2>
                    <p>Public header menusu artik koddan degil, panelden kontrol edilecek sekilde hazirlaniyor.</p>
                </div>
                <div class="panel-badge">Create</div>
            </div>
            <form class="checkout-form" method="post">
                <div class="two-col">
                    <div class="field">
                        <label for="title">Menu Adi</label>
                        <input id="title" name="title" type="text" required>
                    </div>
                    <div class="field">
                        <label for="url">Baglanti Adresi</label>
                        <input id="url" name="url" type="text" placeholder="/index.php" required>
                    </div>
                    <div class="field">
                        <label for="target">Acilis Tipi</label>
                        <select id="target" name="target">
                            <option value="_self">Ayni Sekme</option>
                            <option value="_blank">Yeni Sekme</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="sort_order">Sira No</label>
                        <input id="sort_order" name="sort_order" type="number" value="0">
                    </div>
                    <div class="field full-span">
                        <label class="filter-option"><input type="checkbox" name="is_active" checked> Aktif menu olarak yayinla</label>
                    </div>
                </div>
                <button class="primary-btn" type="submit">Menu Kaydet</button>
            </form>
        </section>

        <aside class="summary-card">
            <h3>Menu Ozet</h3>
            <div class="summary-list">
                <div class="summary-row"><span>Kayitli Menu</span><strong><?= e((string) count($menus)); ?></strong></div>
                <div class="summary-row"><span>Siralama</span><strong>Var</strong></div>
                <div class="summary-row"><span>Aktif/Pasif</span><strong>Var</strong></div>
                <div class="summary-row"><span>Silme</span><strong>Var</strong></div>
            </div>
            <p class="summary-note">Menu basliklari, linkleri ve sira duzeni public alandaki navigasyonu belirler.</p>
        </aside>
    </section>

    <section class="panel-card" style="margin-top: 24px;">
        <div class="panel-header">
            <div>
                <h2>Kayitli Menu Ogesi</h2>
                <p>Bu liste navigasyon tablosundan gelmektedir.</p>
            </div>
            <div class="panel-badge"><?= e((string) count($menus)); ?> Kayit</div>
        </div>

        <?php if (empty($menus)): ?>
            <div class="empty-state">Henuz menu kaydi yok.</div>
        <?php else: ?>
            <form method="post">
                <input type="hidden" name="action" value="bulk_delete">
                <div class="auth-links" style="margin-bottom: 20px;">
                    <button class="secondary-btn" type="button" onclick="toggleAllMenuItems(true)">Tumunu Sec</button>
                    <button class="secondary-btn" type="button" onclick="toggleAllMenuItems(false)">Secimi Kaldir</button>
                    <button class="primary-btn" type="submit">Secilenleri Sil</button>
                </div>
            <div class="cart-items">
                <?php foreach ($menus as $menu): ?>
                    <div class="cart-item">
                        <label class="filter-option" style="grid-column: 1 / -1;">
                            <input class="bulk-menu-checkbox" type="checkbox" name="selected_ids[]" value="<?= e((string) $menu['id']); ?>">
                            Toplu islem icin sec
                        </label>
                        <div class="cart-info" style="grid-column: 1 / span 2;">
                            <h3><?= e($menu['title']); ?></h3>
                            <p><?= e($menu['url']); ?> | <?= e($menu['target']); ?></p>
                            <div class="cart-caption">Sira: <?= e((string) $menu['sort_order']); ?> | <?= $menu['is_active'] ? 'Aktif' : 'Pasif'; ?></div>
                        </div>
                        <button class="remove-btn" type="submit" name="delete_id" value="<?= e((string) $menu['id']); ?>" formnovalidate>Sil</button>
                    </div>
                <?php endforeach; ?>
            </div>
            </form>
        <?php endif; ?>
    </section>
</main>
<script>
function toggleAllMenuItems(checked) {
    document.querySelectorAll('.bulk-menu-checkbox').forEach(function (checkbox) {
        checkbox.checked = checked;
    });
}
</script>
<?php require dirname(__DIR__) . '/app/views/layouts/admin_footer.php'; ?>
