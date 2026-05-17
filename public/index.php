<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startPublicSession();

try {
    $settings = settingsAll($pdo);
    $menus = menuActiveAll($pdo);
    $sliders = sliderAll($pdo);
} catch (Throwable $exception) {
    $settings = [];
    $menus = [];
    $sliders = [];
    logSystemError('public_home_error', $exception->getMessage());
}

$siteTitle = $settings['site_title'] ?? 'FLORIA';
$siteTagline = $settings['site_tagline'] ?? 'Zarafetin yeni hali';
$footerText = $settings['footer_text'] ?? 'FLORIA';
$publicUser = publicUser();
$activeSliders = array_values(array_filter($sliders, static fn(array $slider): bool => (int) ($slider['is_active'] ?? 0) === 1));
if ($activeSliders === []) {
    $activeSliders = [[
        'title' => $siteTitle,
        'description' => 'Zamansiz tasarimlari, ozenli dokulari ve gunluk sikligi bir araya getiren ozel seckiyi kesfedin.',
        'button_text' => 'Urunleri Gor',
        'button_url' => 'products.php',
        'image_path' => 'images/arka foto.jpg',
    ]];
}

$pageTitle = $siteTitle . ' | Ana Sayfa';
require dirname(__DIR__) . '/app/views/layouts/public_header.php';
?>
<main class="catalog-page">
    <div class="catalog-shell">
        <?php require dirname(__DIR__) . '/app/views/components/public_site_header.php'; ?>

        <section class="hero-carousel" data-carousel>
            <div class="hero-carousel-track">
                <?php foreach ($activeSliders as $index => $slider): ?>
                    <?php $sliderPath = str_replace(' ', '%20', (string) ($slider['image_path'] ?? 'images/arka foto.jpg')); ?>
                    <article
                        class="catalog-hero hero-slide<?= $index === 0 ? ' is-active' : ''; ?>"
                        data-slide
                        style="<?= e("background-image: linear-gradient(90deg, rgba(54, 37, 46, 0.82), rgba(54, 37, 46, 0.44)), url('../" . $sliderPath . "');"); ?>"
                    >
                        <h1><?= e($slider['title'] ?? $siteTitle); ?></h1>
                        <p><?= e($slider['description'] ?? 'Zamansiz tasarimlari, ozenli dokulari ve gunluk sikligi bir araya getiren ozel seckiyi kesfedin.'); ?></p>
                        <a class="btn-hero" href="<?= e(!empty($slider['button_url']) ? $slider['button_url'] : 'products.php'); ?>">
                            <?= e($slider['button_text'] ?: 'Urunleri Gor'); ?>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if (count($activeSliders) > 1): ?>
                <div class="hero-carousel-controls">
                    <button class="hero-arrow" type="button" data-carousel-prev aria-label="Onceki slayt">‹</button>
                    <div class="hero-dots">
                        <?php foreach ($activeSliders as $index => $slider): ?>
                            <button
                                class="hero-dot<?= $index === 0 ? ' is-active' : ''; ?>"
                                type="button"
                                data-carousel-dot
                                data-slide-index="<?= e((string) $index); ?>"
                                aria-label="Slayt <?= e((string) ($index + 1)); ?>"
                            ></button>
                        <?php endforeach; ?>
                    </div>
                    <button class="hero-arrow" type="button" data-carousel-next aria-label="Sonraki slayt">›</button>
                </div>
            <?php endif; ?>
        </section>

        <section class="cart-layout">
            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h2><?= e($settings['home_intro_title'] ?? "FLORIA'ya Hos Geldiniz"); ?></h2>
                        <p><?= e($settings['home_intro_text'] ?? 'Her detayinda zarafeti hissettiren taki seckileriyle gunluk stilinizi tamamlayin.'); ?></p>
                    </div>
                </div>
                <div class="cart-note">
                    <?= $publicUser
                        ? 'Hesabiniza girdiniz. Siparislerinizi Siparislerim ekranindan takip edebilir, koleksiyonu kesfetmeye devam edebilirsiniz.'
                        : 'Hesabiniza girmek icin Giris Yap, yeni uyelik olusturmak icin Kayit Ol baglantilarini kullanabilirsiniz.'; ?>
                </div>
                <div class="auth-links" style="margin-top: 18px;">
                    <a class="primary-btn" href="products.php">Urunleri Kesfet</a>
                    <?php if ($publicUser): ?>
                        <a class="secondary-btn" href="orders.php">Siparislerim</a>
                    <?php else: ?>
                        <a class="secondary-btn" href="login.php">Giris Yap</a>
                        <a class="secondary-btn" href="register.php">Kayit Ol</a>
                    <?php endif; ?>
                </div>
            </section>

            <aside class="summary-card">
                <h3>Kurumsal Bilgiler</h3>
                <div class="summary-list">
                    <div class="summary-row"><span>E-posta</span><strong><?= e($settings['contact_email'] ?? '-'); ?></strong></div>
                    <div class="summary-row"><span>Telefon</span><strong><?= e($settings['contact_phone'] ?? '-'); ?></strong></div>
                    <div class="summary-row"><span>Instagram</span><strong><?= e($settings['instagram_url'] ?? '-'); ?></strong></div>
                </div>
                <p class="summary-note"><?= e($settings['contact_address'] ?? 'Istanbul, Turkiye'); ?></p>
            </aside>
        </section>

        <?php
        try {
            $featuredProducts = productRecent($pdo, 4);
        } catch (Throwable $exception) {
            $featuredProducts = [];
            logSystemError('public_home_featured_error', $exception->getMessage());
        }
        ?>

        <?php if (!empty($featuredProducts)): ?>
            <section class="panel-card" style="margin-top: 24px;">
                <div class="panel-header">
                    <div>
                        <h2>Secili Urunler</h2>
                        <p>En sevilen tasarimlari inceleyin, detaylarina bakin ve sepetinize ekleyin.</p>
                    </div>
                </div>
                <div class="product-grid">
                    <?php foreach ($featuredProducts as $product): ?>
                        <article class="product-card">
                            <img class="product-image" src="<?= e('../' . ($product['cover_image'] ?: 'images/kelebekli-yuzuk.jpg')); ?>" alt="<?= e($product['name']); ?>">
                            <span class="product-meta"><?= e($product['category_name']); ?></span>
                            <h3><?= e($product['name']); ?></h3>
                            <p>₺<?= e(number_format((float) $product['price'], 2, ',', '.')); ?></p>
                            <div class="product-copy"><?= e($product['short_description'] ?: 'Kisa aciklama eklenmemis.'); ?></div>
                            <div class="product-actions">
                                <a class="primary-btn" href="product.php?slug=<?= e($product['slug']); ?>">Detayi Gor</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</main>
<script>
(function () {
    var carousel = document.querySelector('[data-carousel]');
    if (!carousel) {
        return;
    }

    var slides = Array.prototype.slice.call(carousel.querySelectorAll('[data-slide]'));
    var dots = Array.prototype.slice.call(carousel.querySelectorAll('[data-carousel-dot]'));
    var prevButton = carousel.querySelector('[data-carousel-prev]');
    var nextButton = carousel.querySelector('[data-carousel-next]');
    var currentIndex = 0;
    var intervalId = null;

    function showSlide(index) {
        currentIndex = (index + slides.length) % slides.length;

        slides.forEach(function (slide, slideIndex) {
            slide.classList.toggle('is-active', slideIndex === currentIndex);
        });

        dots.forEach(function (dot, dotIndex) {
            dot.classList.toggle('is-active', dotIndex === currentIndex);
        });
    }

    function nextSlide() {
        showSlide(currentIndex + 1);
    }

    function startAutoPlay() {
        if (slides.length <= 1) {
            return;
        }

        intervalId = window.setInterval(nextSlide, 5000);
    }

    function resetAutoPlay() {
        if (intervalId) {
            window.clearInterval(intervalId);
        }

        startAutoPlay();
    }

    if (prevButton) {
        prevButton.addEventListener('click', function () {
            showSlide(currentIndex - 1);
            resetAutoPlay();
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', function () {
            nextSlide();
            resetAutoPlay();
        });
    }

    dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
            showSlide(Number(dot.getAttribute('data-slide-index') || '0'));
            resetAutoPlay();
        });
    });

    carousel.addEventListener('mouseenter', function () {
        if (intervalId) {
            window.clearInterval(intervalId);
        }
    });

    carousel.addEventListener('mouseleave', function () {
        resetAutoPlay();
    });

    showSlide(0);
    startAutoPlay();
})();
</script>
<?php require dirname(__DIR__) . '/app/views/components/public_site_footer.php'; ?>
<?php require dirname(__DIR__) . '/app/views/layouts/public_footer.php'; ?>
