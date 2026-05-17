<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

$filters = [
    'search' => trim((string) ($_GET['search'] ?? '')),
    'category_id' => trim((string) ($_GET['category_id'] ?? '')),
    'sort' => trim((string) ($_GET['sort'] ?? 'newest')),
];

try {
    $settings = settingsAll($pdo);
    $menus = menuActiveAll($pdo);
    $categories = categoryAll($pdo);
    $products = productAll($pdo, $filters);
} catch (Throwable $exception) {
    $settings = [];
    $menus = [];
    $categories = [];
    $products = [];
    $loadError = 'Urunler veritabanindan okunurken bir sorun olustu.';
    logSystemError('public_products_error', $exception->getMessage());
}

$siteTitle = $settings['site_title'] ?? 'FLORIA';
$siteTagline = $settings['site_tagline'] ?? 'Zarafetin yeni hali';
$pageTitle = $siteTitle . ' | Urunler';
require dirname(__DIR__) . '/app/views/layouts/public_header.php';
?>
<main class="catalog-page">
    <div class="catalog-shell">
        <?php require dirname(__DIR__) . '/app/views/components/public_site_header.php'; ?>

        <section class="catalog-hero">
            <h1><?= e($settings['products_hero_title'] ?? 'Tum koleksiyonu tek ekranda kesfedin.'); ?></h1>
            <p><?= e($settings['products_hero_text'] ?? 'Yuzuk, kolye, kupe ve bileklik seckilerini filtreleyin; tarziniza en uygun parcayi kolayca bulun.'); ?></p>
        </section>

        <div class="catalog-layout">
            <aside class="filter-card">
                <h2>Filtrele</h2>
                <p>Kategori, arama ve siralama secenekleriyle urunleri hizlica daraltin.</p>
                <form id="public-filter-form" method="get">
                    <div class="filter-group">
                        <h3>Kategori</h3>
                        <div class="field">
                            <label for="category_id">Kategori Secin</label>
                            <select id="category_id" name="category_id">
                                <option value="">Tum kategoriler</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= e((string) $category['id']); ?>" <?= (string) $category['id'] === $filters['category_id'] ? 'selected' : ''; ?>>
                                        <?= e($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="filter-group">
                        <h3>Arama</h3>
                        <div class="field">
                            <label for="search">Urun adi</label>
                            <input id="search" name="search" type="text" value="<?= e($filters['search']); ?>">
                        </div>
                    </div>
                    <div class="filter-group">
                        <h3>Siralama</h3>
                        <div class="field">
                            <label for="sort">Listeleme</label>
                            <select id="sort" name="sort">
                                <option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : ''; ?>>En Yeni</option>
                                <option value="price_asc" <?= $filters['sort'] === 'price_asc' ? 'selected' : ''; ?>>Fiyat Artan</option>
                                <option value="price_desc" <?= $filters['sort'] === 'price_desc' ? 'selected' : ''; ?>>Fiyat Azalan</option>
                                <option value="name_asc" <?= $filters['sort'] === 'name_asc' ? 'selected' : ''; ?>>Ada Gore A-Z</option>
                                <option value="name_desc" <?= $filters['sort'] === 'name_desc' ? 'selected' : ''; ?>>Ada Gore Z-A</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter-actions filter-actions-inline">
                        <span class="filter-helper">Filtreler secim yaptigin anda otomatik uygulanir.</span>
                        <a class="secondary-btn" href="products.php">Sifirla</a>
                    </div>
                </form>
            </aside>

            <section class="catalog-main">
                <section class="catalog-toolbar">
                    <div>
                        <h2>Urun Katalogu</h2>
                        <p><?= count($products); ?> urun bulundu.</p>
                    </div>
                </section>

                <?php if (!empty($loadError)): ?>
                    <div class="empty-state"><?= e($loadError); ?></div>
                <?php endif; ?>

                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                        <article class="product-card">
                            <img class="product-image" src="<?= e('../' . ($product['cover_image'] ?: 'images/kelebekli-yuzuk.jpg')); ?>" alt="<?= e($product['name']); ?>">
                            <span class="product-meta"><?= e($product['category_name']); ?></span>
                            <h3><?= e($product['name']); ?></h3>
                            <p>₺<?= e(number_format((float) $product['price'], 2, ',', '.')); ?></p>
                            <div class="product-copy"><?= e($product['short_description'] ?? 'Urun aciklamasi admin panelinden doldurulacak.'); ?></div>
                            <div class="product-actions">
                                <a class="primary-btn" href="product.php?slug=<?= e($product['slug']); ?>">Detayi Gor</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>
</main>
<script>
(function () {
    var form = document.getElementById('public-filter-form');
    if (!form) {
        return;
    }

    var searchInput = document.getElementById('search');
    var selects = form.querySelectorAll('select');
    var storageKey = 'floria_public_products_scroll';
    var submitTimer = null;

    function storeScrollPosition() {
        sessionStorage.setItem(storageKey, String(window.scrollY));
    }

    function submitFilters() {
        storeScrollPosition();
        form.submit();
    }

    selects.forEach(function (select) {
        select.addEventListener('change', submitFilters);
    });

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            window.clearTimeout(submitTimer);
            submitTimer = window.setTimeout(submitFilters, 350);
        });
    }

    window.addEventListener('load', function () {
        var savedPosition = sessionStorage.getItem(storageKey);
        if (!savedPosition) {
            return;
        }

        window.scrollTo({
            top: Number(savedPosition),
            behavior: 'auto'
        });
        sessionStorage.removeItem(storageKey);
    });
})();
</script>
<?php require dirname(__DIR__) . '/app/views/components/public_site_footer.php'; ?>
<?php require dirname(__DIR__) . '/app/views/layouts/public_footer.php'; ?>
