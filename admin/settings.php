<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startAdminSession();
requireAdminLogin();
requireAdminPermission('settings.update');

$message = null;
$error = null;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fields = [
            'site_title',
            'site_tagline',
            'contact_email',
            'contact_phone',
            'contact_address',
            'instagram_url',
            'facebook_url',
            'logo_path',
            'footer_text',
            'home_intro_title',
            'home_intro_text',
            'products_hero_title',
            'products_hero_text',
            'orders_hero_title',
            'orders_hero_text',
        ];

        foreach ($fields as $field) {
            settingUpsert($pdo, $field, trim((string) ($_POST[$field] ?? '')));
        }

        $message = 'Genel ayarlar guncellendi.';
    }

    $settings = settingsAll($pdo);
} catch (Throwable $exception) {
    $settings = [];
    $error = 'Ayarlar yuklenirken bir sorun olustu.';
    logSystemError('admin_settings_error', $exception->getMessage());
}

$pageTitle = 'FLORIA Admin | Ayarlar';
require dirname(__DIR__) . '/app/views/layouts/admin_header.php';
require dirname(__DIR__) . '/app/views/components/admin_sidebar.php';
?>
<main class="admin-main">
    <section class="catalog-toolbar">
        <div>
            <h2>Genel Ayarlar</h2>
            <p>Logo, iletisim bilgileri, sosyal medya hesaplari ve genel kurumsal veriler bu panelden guncellenir.</p>
        </div>
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
                <h2>Kurumsal Bilgiler</h2>
                <p>Site basligi, slogan, iletisim bilgileri, sosyal medya ve footer metni bu alandan yonetilir.</p>
            </div>
        </div>

        <form class="checkout-form" method="post">
            <div class="two-col">
                <div class="field">
                    <label for="site_title">Site Basligi</label>
                    <input id="site_title" name="site_title" type="text" value="<?= e($settings['site_title'] ?? 'FLORIA'); ?>">
                </div>
                <div class="field">
                    <label for="site_tagline">Slogan</label>
                    <input id="site_tagline" name="site_tagline" type="text" value="<?= e($settings['site_tagline'] ?? 'Zarafetin yeni hali'); ?>">
                </div>
                <div class="field">
                    <label for="contact_email">Iletisim E-postasi</label>
                    <input id="contact_email" name="contact_email" type="email" value="<?= e($settings['contact_email'] ?? ''); ?>">
                </div>
                <div class="field">
                    <label for="contact_phone">Telefon</label>
                    <input id="contact_phone" name="contact_phone" type="text" value="<?= e($settings['contact_phone'] ?? ''); ?>">
                </div>
                <div class="field full-span">
                    <label for="contact_address">Adres</label>
                    <textarea id="contact_address" name="contact_address" rows="4"><?= e($settings['contact_address'] ?? ''); ?></textarea>
                </div>
                <div class="field">
                    <label for="instagram_url">Instagram</label>
                    <input id="instagram_url" name="instagram_url" type="text" value="<?= e($settings['instagram_url'] ?? ''); ?>">
                </div>
                <div class="field">
                    <label for="facebook_url">Facebook</label>
                    <input id="facebook_url" name="facebook_url" type="text" value="<?= e($settings['facebook_url'] ?? ''); ?>">
                </div>
                <div class="field full-span">
                    <label for="logo_path">Logo Yolu</label>
                    <input id="logo_path" name="logo_path" type="text" value="<?= e($settings['logo_path'] ?? ''); ?>">
                </div>
                <div class="field full-span">
                    <label for="footer_text">Footer Metni</label>
                    <textarea id="footer_text" name="footer_text" rows="3"><?= e($settings['footer_text'] ?? ''); ?></textarea>
                </div>
                <div class="field">
                    <label for="home_intro_title">Ana Sayfa Baslik</label>
                    <input id="home_intro_title" name="home_intro_title" type="text" value="<?= e($settings['home_intro_title'] ?? "FLORIA'ya Hos Geldiniz"); ?>">
                </div>
                <div class="field">
                    <label for="products_hero_title">Urunler Baslik</label>
                    <input id="products_hero_title" name="products_hero_title" type="text" value="<?= e($settings['products_hero_title'] ?? 'Tum koleksiyonu tek ekranda kesfedin.'); ?>">
                </div>
                <div class="field full-span">
                    <label for="home_intro_text">Ana Sayfa Aciklama</label>
                    <textarea id="home_intro_text" name="home_intro_text" rows="3"><?= e($settings['home_intro_text'] ?? 'Her detayinda zarafeti hissettiren taki seckileriyle gunluk stilinizi tamamlayin.'); ?></textarea>
                </div>
                <div class="field full-span">
                    <label for="products_hero_text">Urunler Aciklama</label>
                    <textarea id="products_hero_text" name="products_hero_text" rows="3"><?= e($settings['products_hero_text'] ?? 'Yuzuk, kolye, kupe ve bileklik seckilerini filtreleyin; tarziniza en uygun parcayi kolayca bulun.'); ?></textarea>
                </div>
                <div class="field">
                    <label for="orders_hero_title">Siparislerim Baslik</label>
                    <input id="orders_hero_title" name="orders_hero_title" type="text" value="<?= e($settings['orders_hero_title'] ?? 'Siparislerinizi adim adim takip edin.'); ?>">
                </div>
                <div class="field full-span">
                    <label for="orders_hero_text">Siparislerim Aciklama</label>
                    <textarea id="orders_hero_text" name="orders_hero_text" rows="3"><?= e($settings['orders_hero_text'] ?? 'Siparislerinizin guncel durumunu, olusturma tarihini ve toplam tutarini bu ekranda rahatca gorebilirsiniz.'); ?></textarea>
                </div>
            </div>
            <button class="primary-btn" type="submit">Ayarları Kaydet</button>
        </form>
    </section>
</main>
<?php require dirname(__DIR__) . '/app/views/layouts/admin_footer.php'; ?>
