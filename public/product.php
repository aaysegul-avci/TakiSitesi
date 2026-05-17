<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

$slug = trim((string) ($_GET['slug'] ?? ''));

try {
    $settings = settingsAll($pdo);
    $menus = menuActiveAll($pdo);
    $product = $slug !== '' ? productFindBySlug($pdo, $slug) : null;
    $relatedProducts = productRecent($pdo, 3);
} catch (Throwable $exception) {
    $settings = [];
    $menus = [];
    $product = null;
    $relatedProducts = [];
    logSystemError('public_product_detail_error', $exception->getMessage());
}

$siteTitle = $settings['site_title'] ?? 'FLORIA';
$siteTagline = $settings['site_tagline'] ?? 'Zarafetin yeni hali';
$pageTitle = $product ? ($product['name'] . ' | ' . $siteTitle) : ($siteTitle . ' | Urun Detayi');

require dirname(__DIR__) . '/app/views/layouts/public_header.php';
?>
<main class="catalog-page">
    <div class="catalog-shell">
        <?php require dirname(__DIR__) . '/app/views/components/public_site_header.php'; ?>

        <?php if (!$product): ?>
            <div class="empty-state">Aradiginiz urun bulunamadi.</div>
        <?php else: ?>
            <section class="cart-layout">
                <section class="panel-card">
                    <div class="cart-item" style="grid-template-columns: minmax(280px, 360px) 1fr;">
                        <img src="<?= e('../' . ($product['cover_image'] ?: 'images/kelebekli-yuzuk.jpg')); ?>" alt="<?= e($product['name']); ?>" style="width:100%; height:360px;">
                        <div class="cart-info">
                            <span class="product-meta"><?= e($product['category_name']); ?></span>
                            <h3><?= e($product['name']); ?></h3>
                            <p class="price">₺<?= e(number_format((float) $product['price'], 2, ',', '.')); ?></p>
                            <div class="cart-caption"><?= e($product['short_description'] ?: 'Kisa aciklama eklenmemis.'); ?></div>
                            <div class="cart-note" style="margin-top:16px;">
                                <?= e($product['description'] ?: 'Bu urun gunluk kullanimdan ozel gun kombinlerine kadar rahatca eslik edecek zarif bir secim sunar.'); ?>
                            </div>
                            <div class="product-actions" style="margin-top: 18px;">
                                <button class="primary-btn" type="button" onclick='addToCart(<?= json_encode([
                                    'id' => (int) $product['id'],
                                    'ad' => $product['name'],
                                    'fiyat' => '₺' . number_format((float) $product['price'], 2, ',', '.'),
                                    'resim' => '../' . ($product['cover_image'] ?: 'images/kelebekli-yuzuk.jpg'),
                                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>)'>Sepete Ekle</button>
                                <a class="secondary-btn" href="products.php">Kataloga Don</a>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="summary-card">
                    <h3>Urun Bilgisi</h3>
                    <div class="summary-list">
                        <div class="summary-row"><span>Kategori</span><strong><?= e($product['category_name']); ?></strong></div>
                        <div class="summary-row"><span>Slug</span><strong><?= e($product['slug']); ?></strong></div>
                        <div class="summary-row"><span>Stok</span><strong><?= e((string) $product['stock']); ?></strong></div>
                    </div>
                    <p class="summary-note">Kategori, stok ve fiyat bilgileriyle urun seciminizi daha rahat tamamlayabilirsiniz.</p>
                </aside>
            </section>

            <?php if (!empty($relatedProducts)): ?>
                <section class="panel-card" style="margin-top: 24px;">
                    <div class="panel-header">
                        <div>
                            <h2>Diger Urunler</h2>
                            <p>Ayni katalogdan diger parcalara da hizlica ulasabilirsiniz.</p>
                        </div>
                    </div>
                    <div class="product-grid">
                        <?php foreach ($relatedProducts as $related): ?>
                            <?php if ($related['id'] === $product['id']) continue; ?>
                            <article class="product-card">
                                <img class="product-image" src="<?= e('../' . ($related['cover_image'] ?: 'images/kelebekli-yuzuk.jpg')); ?>" alt="<?= e($related['name']); ?>">
                                <span class="product-meta"><?= e($related['category_name']); ?></span>
                                <h3><?= e($related['name']); ?></h3>
                                <p>₺<?= e(number_format((float) $related['price'], 2, ',', '.')); ?></p>
                                <div class="product-copy"><?= e($related['short_description'] ?: 'Kisa aciklama eklenmemis.'); ?></div>
                                <div class="product-actions">
                                    <a class="primary-btn" href="product.php?slug=<?= e($related['slug']); ?>">Detayi Gor</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>
<script>
function addToCart(product) {
    const cart = JSON.parse(localStorage.getItem('floria_sepet')) || [];
    cart.push(product);
    localStorage.setItem('floria_sepet', JSON.stringify(cart));
    alert(product.ad + ' sepete eklendi.');
}
</script>
<?php require dirname(__DIR__) . '/app/views/components/public_site_footer.php'; ?>
<?php require dirname(__DIR__) . '/app/views/layouts/public_footer.php'; ?>
