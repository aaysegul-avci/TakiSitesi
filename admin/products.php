<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startAdminSession();
requireAdminLogin();
requireAdminPermission('products.create');

$message = null;
$error = null;

$filters = [
    'search' => trim((string) ($_GET['search'] ?? '')),
    'category_id' => trim((string) ($_GET['category_id'] ?? '')),
    'is_active' => isset($_GET['is_active']) ? trim((string) $_GET['is_active']) : '',
    'sort' => trim((string) ($_GET['sort'] ?? 'newest')),
];

try {
    $categories = categoryAll($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = (string) ($_POST['action'] ?? 'create');

        if ($action === 'bulk_delete') {
            requireAdminPermission('products.delete');
            $deletedCount = productDeleteMany($pdo, (array) ($_POST['selected_ids'] ?? []));
            $message = $deletedCount > 0
                ? $deletedCount . ' urun tek hamlede silindi.'
                : 'Toplu silme icin urun secilmedi.';
        } else {
            $name = trim((string) ($_POST['name'] ?? ''));
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $price = (float) ($_POST['price'] ?? 0);
            $stock = (int) ($_POST['stock'] ?? 0);
            $shortDescription = trim((string) ($_POST['short_description'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $sku = trim((string) ($_POST['sku'] ?? ''));
            $coverImage = trim((string) ($_POST['cover_image'] ?? ''));
            $slug = trim((string) ($_POST['slug'] ?? ''));
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if ($name === '' || $categoryId === 0 || $price <= 0 || $slug === '') {
                $error = 'Urun adi, kategori, fiyat ve slug zorunludur.';
            } else {
                $uploadedCoverImage = storeUploadedImage($_FILES['cover_image_file'] ?? [], 'uploads/products');
                $finalCoverImage = $uploadedCoverImage ?? ($coverImage !== '' ? $coverImage : null);

                productCreate($pdo, [
                    'category_id' => $categoryId,
                    'name' => $name,
                    'slug' => $slug,
                    'short_description' => $shortDescription,
                    'description' => $description,
                    'price' => $price,
                    'stock' => $stock,
                    'sku' => $sku !== '' ? $sku : null,
                    'cover_image' => $finalCoverImage,
                    'is_active' => $isActive,
                ]);

                $message = 'Urun basariyla kaydedildi.';
            }
        }
    }

    $products = productAdminFilteredList($pdo, $filters);
} catch (Throwable $exception) {
    $categories = $categories ?? [];
    $products = [];
    $error = 'Urun yonetimi yuklenirken bir sorun olustu.';
    logSystemError('admin_products_error', $exception->getMessage());
}

$pageTitle = 'FLORIA Admin | Urunler';
require dirname(__DIR__) . '/app/views/layouts/admin_header.php';
require dirname(__DIR__) . '/app/views/components/admin_sidebar.php';
?>
<main class="admin-main">
    <section class="catalog-toolbar">
        <div>
            <h2>Urun Yonetimi</h2>
            <p>Urunlerinizi ekleyin, filtreleyin, secilenleri toplu silin ve listeyi panelden yonetin.</p>
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
                    <h2>Yeni Urun Ekle</h2>
                    <p>Prepared insert kullanan urun kaydi formu aktif kalmaya devam ediyor.</p>
                </div>
                <div class="panel-badge">Create</div>
            </div>

            <form class="checkout-form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">
                <div class="two-col">
                    <div class="field">
                        <label for="name">Urun Adi</label>
                        <input id="name" name="name" type="text" required>
                    </div>
                    <div class="field">
                        <label for="slug">Slug</label>
                        <input id="slug" name="slug" type="text" placeholder="kalpli-yuzuk" required>
                    </div>
                    <div class="field">
                        <label for="category_id">Kategori</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Kategori secin</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= e((string) $category['id']); ?>"><?= e($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="price">Fiyat</label>
                        <input id="price" name="price" type="number" step="0.01" required>
                    </div>
                    <div class="field">
                        <label for="stock">Stok</label>
                        <input id="stock" name="stock" type="number" value="0">
                    </div>
                    <div class="field">
                        <label for="sku">SKU</label>
                        <input id="sku" name="sku" type="text">
                    </div>
                    <div class="field full-span">
                        <label for="cover_image_file">Kapak Gorseli Yukle</label>
                        <input id="cover_image_file" name="cover_image_file" type="file" accept="image/*">
                        <div class="upload-note">Bilgisayarinizdan gorsel secin. Onerilen olcu: 1200 x 1200 px. Kartta tum gorseller ayni alana oturur, farkli oranli gorsellerde kenarlar kirpilabilir.</div>
                    </div>
                    <div class="field full-span">
                        <label for="cover_image">Kapak Gorseli</label>
                        <input id="cover_image" name="cover_image" type="text" placeholder="Istersen manuel yol da girebilirsin: uploads/products/urun.jpg">
                    </div>
                    <div class="field full-span">
                        <div class="product-preview-shell">
                            <div class="product-preview-copy">
                                <strong>Kart Onizleme</strong>
                                <span>Yukledigin gorsel vitrinde bu oranla gosterilecek.</span>
                            </div>
                            <article class="product-card product-preview-card">
                                <img
                                    id="product-card-preview-image"
                                    class="product-image"
                                    src="<?= e('../images/kelebekli-yuzuk.jpg'); ?>"
                                    alt="Urun onizleme gorseli"
                                >
                                <span id="product-card-preview-category" class="product-meta">Kategori</span>
                                <h3 id="product-card-preview-name">Urun adi burada gorunecek</h3>
                                <p id="product-card-preview-price">₺0,00</p>
                                <div id="product-card-preview-description" class="product-copy">Kisa aciklama girdiginizde karttaki gorunumu burada gorebilirsin.</div>
                                <div class="product-actions">
                                    <span class="primary-btn product-preview-button">Detayi Gor</span>
                                </div>
                            </article>
                        </div>
                    </div>
                    <div class="field full-span">
                        <label for="short_description">Kisa Aciklama</label>
                        <input id="short_description" name="short_description" type="text">
                    </div>
                    <div class="field full-span">
                        <label for="description">Detay Aciklama</label>
                        <textarea id="description" name="description" rows="5"></textarea>
                    </div>
                    <div class="field full-span">
                        <label class="filter-option"><input type="checkbox" name="is_active" checked> Urunu aktif olarak yayinla</label>
                    </div>
                </div>
                <button class="primary-btn" type="submit">Urunu Kaydet</button>
            </form>
        </section>

        <aside class="summary-card">
            <h3>Modul Notu</h3>
            <div class="summary-list">
                <div class="summary-row"><span>Prepared Insert</span><strong>Hazir</strong></div>
                <div class="summary-row"><span>Gelişmis Filtre</span><strong>Hazir</strong></div>
                <div class="summary-row"><span>Toplu Sil</span><strong>Hazir</strong></div>
                <div class="summary-row"><span>CSV Export</span><strong>Hazir</strong></div>
            </div>
            <p class="summary-note">Urun ekleme, filtreleme, toplu islem ve disa aktarma secenekleri bu ekranda bir arada sunulur.</p>
        </aside>
    </section>

    <section class="panel-card" style="margin-top: 24px;">
        <div class="panel-header">
            <div>
                <h2>Filtrele ve Listele</h2>
                <p>Aradiginiz urune hizlica ulasin, kriterlere gore siralayin ve secilenleri tek hamlede silin.</p>
            </div>
            <div class="auth-links">
                <a href="export_products.php" class="secondary-btn">CSV Urun Export</a>
                <a href="export_products_pdf.php" class="secondary-btn">PDF Urun Export</a>
                <a href="export_users.php" class="secondary-btn">CSV Kullanici Export</a>
                <a href="export_users_pdf.php" class="secondary-btn">PDF Kullanici Export</a>
                <a href="export_orders.php" class="secondary-btn">CSV Siparis Export</a>
                <a href="export_orders_pdf.php" class="secondary-btn">PDF Siparis Export</a>
            </div>
        </div>

        <form id="admin-product-filter-form" method="get" class="checkout-form" style="margin-bottom: 24px;">
            <div class="two-col">
                <div class="field">
                    <label for="filter-search">Arama</label>
                    <input id="filter-search" name="search" type="text" value="<?= e($filters['search']); ?>" placeholder="Urun adi veya slug">
                </div>
                <div class="field">
                    <label for="filter-category">Kategori</label>
                    <select id="filter-category" name="category_id">
                        <option value="">Tum kategoriler</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= e((string) $category['id']); ?>" <?= $filters['category_id'] === (string) $category['id'] ? 'selected' : ''; ?>>
                                <?= e($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="filter-status">Durum</label>
                    <select id="filter-status" name="is_active">
                        <option value="">Tum durumlar</option>
                        <option value="1" <?= $filters['is_active'] === '1' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="0" <?= $filters['is_active'] === '0' ? 'selected' : ''; ?>>Pasif</option>
                    </select>
                </div>
                <div class="field">
                    <label for="filter-sort">Siralama</label>
                    <select id="filter-sort" name="sort">
                        <option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : ''; ?>>En Yeni</option>
                        <option value="price_asc" <?= $filters['sort'] === 'price_asc' ? 'selected' : ''; ?>>Fiyat Artan</option>
                        <option value="price_desc" <?= $filters['sort'] === 'price_desc' ? 'selected' : ''; ?>>Fiyat Azalan</option>
                        <option value="name_asc" <?= $filters['sort'] === 'name_asc' ? 'selected' : ''; ?>>Ada Gore A-Z</option>
                        <option value="name_desc" <?= $filters['sort'] === 'name_desc' ? 'selected' : ''; ?>>Ada Gore Z-A</option>
                    </select>
                </div>
            </div>
            <div class="auth-links auth-links-stack">
                <span class="filter-helper">Secim yaptiginda filtreler otomatik uygulanir.</span>
                <a class="secondary-btn" href="products.php">Sifirla</a>
            </div>
        </form>

        <form method="post">
            <input type="hidden" name="action" value="bulk_delete">
            <div class="panel-header">
                <div>
                    <h2>Kayitli Urunler</h2>
                    <p><?= e((string) count($products)); ?> urun listeleniyor.</p>
                </div>
                <div class="auth-links">
                    <button class="secondary-btn" type="button" onclick="toggleAllProducts(true)">Tumunu Sec</button>
                    <button class="secondary-btn" type="button" onclick="toggleAllProducts(false)">Secimi Kaldir</button>
                    <?php if (adminCan('products.delete')): ?>
                        <button class="primary-btn" type="submit">Secilenleri Sil</button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <article class="product-card">
                        <label class="filter-option" style="margin-bottom: 12px;">
                            <input class="bulk-product-checkbox" type="checkbox" name="selected_ids[]" value="<?= e((string) $product['id']); ?>">
                            Toplu islem icin sec
                        </label>
                        <img class="product-image" src="<?= e('../' . ($product['cover_image'] ?: 'images/kelebekli-yuzuk.jpg')); ?>" alt="<?= e($product['name']); ?>">
                        <span class="product-meta"><?= e($product['category_name']); ?></span>
                        <h3><?= e($product['name']); ?></h3>
                        <p>₺<?= e(number_format((float) $product['price'], 2, ',', '.')); ?></p>
                        <div class="product-copy"><?= e($product['short_description'] ?: 'Kisa aciklama eklenmemis.'); ?></div>
                        <div class="cart-caption">
                            Durum: <?= $product['is_active'] ? 'Aktif' : 'Pasif'; ?><br>
                            Slug: <?= e($product['slug']); ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </form>
    </section>
</main>

<script>
function toggleAllProducts(checked) {
    document.querySelectorAll('.bulk-product-checkbox').forEach(function (checkbox) {
        checkbox.checked = checked;
    });
}

(function () {
    var imageInput = document.getElementById('cover_image_file');
    var imagePathInput = document.getElementById('cover_image');
    var nameInput = document.getElementById('name');
    var priceInput = document.getElementById('price');
    var shortDescriptionInput = document.getElementById('short_description');
    var categoryInput = document.getElementById('category_id');

    var previewImage = document.getElementById('product-card-preview-image');
    var previewName = document.getElementById('product-card-preview-name');
    var previewPrice = document.getElementById('product-card-preview-price');
    var previewDescription = document.getElementById('product-card-preview-description');
    var previewCategory = document.getElementById('product-card-preview-category');
    var fallbackImage = '<?= e('../images/kelebekli-yuzuk.jpg'); ?>';

    function toPreviewPath(path) {
        if (!path) {
            return fallbackImage;
        }

        if (
            path.indexOf('http://') === 0 ||
            path.indexOf('https://') === 0 ||
            path.indexOf('data:') === 0 ||
            path.indexOf('../') === 0
        ) {
            return path;
        }

        return '../' + path.replace(/^\/+/, '');
    }

    function updateTextPreview() {
        var selectedCategory = categoryInput.options[categoryInput.selectedIndex];
        var price = parseFloat(priceInput.value || '0');

        previewName.textContent = nameInput.value.trim() || 'Urun adi burada gorunecek';
        previewDescription.textContent = shortDescriptionInput.value.trim() || 'Kisa aciklama girdiginizde karttaki gorunumu burada gorebilirsin.';
        previewCategory.textContent = selectedCategory && selectedCategory.value ? selectedCategory.text : 'Kategori';
        previewPrice.textContent = '₺' + price.toLocaleString('tr-TR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function updateManualImagePreview() {
        if (imageInput.files && imageInput.files.length > 0) {
            return;
        }

        previewImage.src = toPreviewPath(imagePathInput.value.trim());
    }

    imageInput.addEventListener('change', function () {
        var file = imageInput.files && imageInput.files[0];

        if (!file) {
            updateManualImagePreview();
            return;
        }

        var reader = new FileReader();
        reader.onload = function (event) {
            previewImage.src = String(event.target.result || fallbackImage);
        };
        reader.readAsDataURL(file);
    });

    imagePathInput.addEventListener('input', updateManualImagePreview);
    nameInput.addEventListener('input', updateTextPreview);
    priceInput.addEventListener('input', updateTextPreview);
    shortDescriptionInput.addEventListener('input', updateTextPreview);
    categoryInput.addEventListener('change', updateTextPreview);

    updateTextPreview();
    updateManualImagePreview();
})();

(function () {
    var form = document.getElementById('admin-product-filter-form');
    if (!form) {
        return;
    }

    var searchInput = document.getElementById('filter-search');
    var selects = form.querySelectorAll('select');
    var storageKey = 'floria_admin_products_scroll';
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
<?php require dirname(__DIR__) . '/app/views/layouts/admin_footer.php'; ?>
