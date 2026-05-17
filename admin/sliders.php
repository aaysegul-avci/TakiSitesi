<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startAdminSession();
requireAdminLogin();
requireAdminPermission('sliders.manage');

$message = null;
$error = null;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (($_POST['action'] ?? '') === 'bulk_delete') {
            $deletedCount = sliderDeleteMany($pdo, (array) ($_POST['selected_ids'] ?? []));
            $message = $deletedCount > 0
                ? $deletedCount . ' slider tek hamlede silindi.'
                : 'Toplu silme icin slider secilmedi.';
        } elseif (isset($_POST['delete_id'])) {
            sliderDelete($pdo, (int) $_POST['delete_id']);
            $message = 'Slider kaydi silindi.';
        } else {
            $title = trim((string) ($_POST['title'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $imagePath = trim((string) ($_POST['image_path'] ?? ''));
            $buttonText = trim((string) ($_POST['button_text'] ?? ''));
            $buttonUrl = trim((string) ($_POST['button_url'] ?? ''));
            $sortOrder = (int) ($_POST['sort_order'] ?? 0);
            $uploadedImagePath = storeUploadedImage($_FILES['image_file'] ?? [], 'uploads/sliders');
            $finalImagePath = $uploadedImagePath ?? ($imagePath !== '' ? $imagePath : '');

            if ($title === '' || $finalImagePath === '') {
                $error = 'Slider basligi ve gorseli zorunludur.';
            } else {
                sliderCreate($pdo, [
                    'title' => $title,
                    'description' => $description,
                    'image_path' => $finalImagePath,
                    'button_text' => $buttonText,
                    'button_url' => $buttonUrl,
                    'sort_order' => $sortOrder,
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                ]);
                $message = 'Slider basariyla kaydedildi.';
            }
        }
    }

    $sliders = sliderAll($pdo);
} catch (Throwable $exception) {
    $sliders = [];
    $error = 'Slider modulu yuklenirken bir sorun olustu.';
    logSystemError('admin_sliders_error', $exception->getMessage());
}

$pageTitle = 'FLORIA Admin | Slider';
require dirname(__DIR__) . '/app/views/layouts/admin_header.php';
require dirname(__DIR__) . '/app/views/components/admin_sidebar.php';
?>
<main class="admin-main">
    <section class="catalog-toolbar">
        <div>
            <h2>Slider Yonetimi</h2>
            <p>Ana sayfa vitrininizde kullanacaginiz gorsel, baslik ve buton alanlarini bu ekrandan yonetebilirsiniz.</p>
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
                    <h2>Yeni Slider Ekle</h2>
                    <p>Ana sayfada yer alacak vitrin alanini yeni kampanya, koleksiyon veya duyurular icin hazirlayin.</p>
                </div>
            </div>
            <form class="checkout-form" method="post" enctype="multipart/form-data">
                <div class="two-col">
                    <div class="field">
                        <label for="title">Baslik</label>
                        <input id="title" name="title" type="text" required>
                    </div>
                    <div class="field">
                        <label for="sort_order">Sira No</label>
                        <input id="sort_order" name="sort_order" type="number" value="0">
                    </div>
                    <div class="field full-span">
                        <label for="description">Aciklama</label>
                        <textarea id="description" name="description" rows="4"></textarea>
                    </div>
                    <div class="field full-span">
                        <label for="image_file">Slider Gorseli Yukle</label>
                        <input id="image_file" name="image_file" type="file" accept="image/*">
                        <div class="upload-note">Bilgisayarinizdan gorsel secin. Onerilen olcu: 1600 x 900 px. Hero alani genis oldugu icin yatay gorseller en iyi sonucu verir.</div>
                    </div>
                    <div class="field full-span">
                        <label for="image_path">Gorsel Yolu</label>
                        <input id="image_path" name="image_path" type="text" placeholder="Istersen manuel yol da girebilirsin: uploads/sliders/slider-1.jpg">
                    </div>
                    <div class="field">
                        <label for="button_text">Buton Metni</label>
                        <input id="button_text" name="button_text" type="text">
                    </div>
                    <div class="field">
                        <label for="button_url">Buton Linki</label>
                        <input id="button_url" name="button_url" type="text" placeholder="/products.php">
                    </div>
                    <div class="field full-span">
                        <div class="product-preview-shell">
                            <div class="product-preview-copy">
                                <strong>Hero Onizleme</strong>
                                <span>Slider gorselinin anasayfa vitrininde nasil duracagini burada gorebilirsin.</span>
                            </div>
                            <section id="slider-hero-preview" class="slider-preview-hero" style="background-image: linear-gradient(90deg, rgba(54, 37, 46, 0.82), rgba(54, 37, 46, 0.44)), url('../images/arka%20foto.jpg');">
                                <div class="slider-preview-content">
                                    <span class="slider-preview-badge">Anasayfa Hero</span>
                                    <h3 id="slider-preview-title">Isiltinizi Tamamlayan Ozel Secki</h3>
                                    <p id="slider-preview-description">Gunluk sikligi zarif detaylarla tamamlayan yuzuk, kolye, kupe ve bileklik koleksiyonlarini kesfedin.</p>
                                    <a id="slider-preview-button" class="btn-hero" href="javascript:void(0)">Koleksiyonu Kesfet</a>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div class="field full-span">
                        <label class="filter-option"><input type="checkbox" name="is_active" checked> Aktif olarak yayina al</label>
                    </div>
                </div>
                <button class="primary-btn" type="submit">Slider Kaydet</button>
            </form>
        </section>

        <aside class="summary-card">
            <h3>Modul Ozet</h3>
            <div class="summary-list">
                <div class="summary-row"><span>Toplam Slider</span><strong><?= e((string) count($sliders)); ?></strong></div>
                <div class="summary-row"><span>Prepared Insert</span><strong>Aktif</strong></div>
                <div class="summary-row"><span>Siralama</span><strong>Aktif</strong></div>
                <div class="summary-row"><span>Silme</span><strong>Aktif</strong></div>
            </div>
            <p class="summary-note">Slider alani ana sayfadaki hero gorseli, aciklamasi ve yonlendirme butonunu tek ekrandan yonetmenizi saglar.</p>
        </aside>
    </section>

    <section class="panel-card" style="margin-top: 24px;">
            <div class="panel-header">
                <div>
                    <h2>Kayitli Sliderlar</h2>
                    <p>Kayitli gorseller, basliklar ve butonlar ana sayfada gosterilecek slider icerigini olusturur.</p>
                </div>
                <div class="panel-badge"><?= e((string) count($sliders)); ?> Kayit</div>
            </div>

        <?php if (empty($sliders)): ?>
            <div class="empty-state">Henuz slider eklenmedi.</div>
        <?php else: ?>
            <form method="post">
                <input type="hidden" name="action" value="bulk_delete">
                <div class="auth-links" style="margin-bottom: 20px;">
                    <button class="secondary-btn" type="button" onclick="toggleAllSliderItems(true)">Tumunu Sec</button>
                    <button class="secondary-btn" type="button" onclick="toggleAllSliderItems(false)">Secimi Kaldir</button>
                    <button class="primary-btn" type="submit">Secilenleri Sil</button>
                </div>
            <div class="cart-items">
                <?php foreach ($sliders as $slider): ?>
                    <div class="cart-item">
                        <label class="filter-option" style="grid-column: 1 / -1;">
                            <input class="bulk-slider-checkbox" type="checkbox" name="selected_ids[]" value="<?= e((string) $slider['id']); ?>">
                            Toplu islem icin sec
                        </label>
                        <img src="<?= e('../' . $slider['image_path']); ?>" alt="<?= e($slider['title']); ?>">
                        <div class="cart-info">
                            <h3><?= e($slider['title']); ?></h3>
                            <p>Sira: <?= e((string) $slider['sort_order']); ?> | <?= $slider['is_active'] ? 'Aktif' : 'Pasif'; ?></p>
                            <div class="cart-caption"><?= e($slider['description'] ?: 'Aciklama girilmemis.'); ?></div>
                        </div>
                        <button class="remove-btn" type="submit" name="delete_id" value="<?= e((string) $slider['id']); ?>" formnovalidate>Sil</button>
                    </div>
                <?php endforeach; ?>
            </div>
            </form>
        <?php endif; ?>
    </section>
</main>
<script>
(function () {
    window.toggleAllSliderItems = function (checked) {
        document.querySelectorAll('.bulk-slider-checkbox').forEach(function (checkbox) {
            checkbox.checked = checked;
        });
    };
})();

(function () {
    var imageInput = document.getElementById('image_file');
    var imagePathInput = document.getElementById('image_path');
    var titleInput = document.getElementById('title');
    var descriptionInput = document.getElementById('description');
    var buttonTextInput = document.getElementById('button_text');
    var buttonUrlInput = document.getElementById('button_url');

    var previewHero = document.getElementById('slider-hero-preview');
    var previewTitle = document.getElementById('slider-preview-title');
    var previewDescription = document.getElementById('slider-preview-description');
    var previewButton = document.getElementById('slider-preview-button');
    var fallbackImage = '../images/arka%20foto.jpg';

    function buildHeroBackground(imagePath) {
        return "linear-gradient(90deg, rgba(54, 37, 46, 0.82), rgba(54, 37, 46, 0.44)), url('" + imagePath + "')";
    }

    function normalizePath(path) {
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

        return '../' + path.replace(/^\/+/, '').replace(/ /g, '%20');
    }

    function updateTextPreview() {
        previewTitle.textContent = titleInput.value.trim() || 'Isiltinizi Tamamlayan Ozel Secki';
        previewDescription.textContent = descriptionInput.value.trim() || 'Gunluk sikligi zarif detaylarla tamamlayan yuzuk, kolye, kupe ve bileklik koleksiyonlarini kesfedin.';
        previewButton.textContent = buttonTextInput.value.trim() || 'Koleksiyonu Kesfet';
        previewButton.setAttribute('href', buttonUrlInput.value.trim() || 'javascript:void(0)');
    }

    function updateManualImagePreview() {
        if (imageInput.files && imageInput.files.length > 0) {
            return;
        }

        previewHero.style.backgroundImage = buildHeroBackground(normalizePath(imagePathInput.value.trim()));
    }

    imageInput.addEventListener('change', function () {
        var file = imageInput.files && imageInput.files[0];

        if (!file) {
            updateManualImagePreview();
            return;
        }

        var reader = new FileReader();
        reader.onload = function (event) {
            previewHero.style.backgroundImage = buildHeroBackground(String(event.target.result || fallbackImage));
        };
        reader.readAsDataURL(file);
    });

    imagePathInput.addEventListener('input', updateManualImagePreview);
    titleInput.addEventListener('input', updateTextPreview);
    descriptionInput.addEventListener('input', updateTextPreview);
    buttonTextInput.addEventListener('input', updateTextPreview);
    buttonUrlInput.addEventListener('input', updateTextPreview);

    updateTextPreview();
    updateManualImagePreview();
})();
</script>
<?php require dirname(__DIR__) . '/app/views/layouts/admin_footer.php'; ?>
