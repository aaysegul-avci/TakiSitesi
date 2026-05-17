<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startPublicSession();
requirePublicLogin();

try {
    $settings = settingsAll($pdo);
    $menus = menuActiveAll($pdo);
} catch (Throwable $exception) {
    $settings = [];
    $menus = [];
    logSystemError('public_checkout_boot_error', $exception->getMessage());
}

$cartPayload = (string) ($_POST['cart_payload'] ?? '[]');
$cartItems = json_decode($cartPayload, true);
$cartItems = is_array($cartItems) ? $cartItems : [];
$error = null;
$success = null;
$createdOrderNumber = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_name'])) {
    $customerName = trim((string) ($_POST['customer_name'] ?? ''));
    $customerEmail = trim((string) ($_POST['customer_email'] ?? ''));
    $customerPhone = trim((string) ($_POST['customer_phone'] ?? ''));
    $address = trim((string) ($_POST['address'] ?? ''));
    $cardHolder = trim((string) ($_POST['card_holder'] ?? ''));
    $cardNumber = preg_replace('/\D+/', '', (string) ($_POST['card_number'] ?? ''));
    $cardExpiry = trim((string) ($_POST['card_expiry'] ?? ''));
    $cardCvc = preg_replace('/\D+/', '', (string) ($_POST['card_cvc'] ?? ''));
    $cartItems = json_decode((string) ($_POST['cart_payload'] ?? '[]'), true);
    $cartItems = is_array($cartItems) ? $cartItems : [];

    if ($customerName === '' || $address === '' || empty($cartItems)) {
        $error = 'Teslimat bilgileri veya sepet verisi eksik.';
    } elseif ($cardHolder === '' || strlen($cardNumber) < 16 || $cardExpiry === '' || strlen($cardCvc) < 3) {
        $error = 'Odeme bilgilerini eksiksiz doldurun.';
    } else {
        try {
            $orderItems = [];
            $totalAmount = 0.0;

            foreach ($cartItems as $item) {
                $price = (float) str_replace(',', '.', preg_replace('/[^\d,]/', '', (string) ($item['fiyat'] ?? '0')));
                $orderItems[] = [
                    'product_id' => (int) ($item['id'] ?? 0),
                    'product_name' => (string) ($item['ad'] ?? 'Urun'),
                    'unit_price' => $price,
                    'quantity' => 1,
                    'line_total' => $price,
                ];
                $totalAmount += $price;
            }

            $orderNumber = generateOrderNumber();
            $publicUser = $_SESSION['public_user'] ?? null;

            orderCreateWithItems($pdo, [
                'user_id' => $publicUser['id'] ?? null,
                'order_number' => $orderNumber,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'address' => $address,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ], $orderItems);

            $success = 'Siparis basariyla olusturuldu.';
            $createdOrderNumber = $orderNumber;
        } catch (Throwable $exception) {
            $error = 'Siparis kaydedilirken bir sorun olustu.';
            logSystemError('public_checkout_order_error', $exception->getMessage());
        }
    }
}

$siteTitle = $settings['site_title'] ?? 'FLORIA';
$siteTagline = $settings['site_tagline'] ?? 'Zarafetin yeni hali';
$pageTitle = $siteTitle . ' | Odeme';

require dirname(__DIR__) . '/app/views/layouts/public_header.php';
?>
<main class="checkout-page">
    <div class="checkout-shell">
        <?php require dirname(__DIR__) . '/app/views/components/public_site_header.php'; ?>

        <?php if ($success): ?>
            <div class="alert-box alert-success" style="margin-bottom: 24px;">
                <?= e($success); ?> Siparis numarasi: <strong><?= e($createdOrderNumber ?? '-'); ?></strong>
                <?php if (publicIsLoggedIn()): ?>
                    <a href="orders.php">Siparislerimi Gor</a>
                <?php endif; ?>
            </div>
            <script>
                localStorage.removeItem('floria_sepet');
            </script>
        <?php endif; ?>

        <?php if ($error): ?>
            <?= renderAlert('error', $error); ?>
        <?php endif; ?>

        <div class="checkout-layout">
            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h1>Odeme ve Teslimat</h1>
                        <p class="panel-lead">Teslimat bilgilerinizi tamamlayin, siparisinizi olusturun ve sonraki sureci panelden takip edin.</p>
                    </div>
                </div>

                <form class="checkout-form" method="post">
                    <input type="hidden" name="cart_payload" value='<?= e(json_encode($cartItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>'>
                    <div class="form-section-card">
                        <h2>Teslimat Bilgileri</h2>
                        <div class="two-col">
                            <div class="field">
                                <label for="customer_name">Ad Soyad</label>
                                <input id="customer_name" name="customer_name" type="text" required>
                            </div>
                            <div class="field">
                                <label for="customer_phone">Telefon</label>
                                <input id="customer_phone" name="customer_phone" type="text">
                            </div>
                            <div class="field full-span">
                                <label for="customer_email">E-posta</label>
                                <input id="customer_email" name="customer_email" type="email">
                            </div>
                            <div class="field full-span">
                                <label for="address">Adres</label>
                                <textarea id="address" name="address" rows="4" required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-card">
                        <h2>Odeme Bilgileri</h2>
                        <p>Kart bilgileriniz yalnizca siparis akisini tamamlamak icin kullanilir; bu proje demo odeme kurgusuyla calisir.</p>
                        <div class="two-col">
                            <div class="field full-span">
                                <label for="card_holder">Kart Uzerindeki Isim</label>
                                <input id="card_holder" name="card_holder" type="text" required>
                            </div>
                            <div class="field full-span">
                                <label for="card_number">Kart Numarasi</label>
                                <input id="card_number" name="card_number" type="text" inputmode="numeric" maxlength="19" placeholder="0000 0000 0000 0000" required>
                            </div>
                            <div class="field">
                                <label for="card_expiry">Son Kullanma</label>
                                <input id="card_expiry" name="card_expiry" type="text" maxlength="5" placeholder="AA/YY" required>
                            </div>
                            <div class="field">
                                <label for="card_cvc">CVC</label>
                                <input id="card_cvc" name="card_cvc" type="text" inputmode="numeric" maxlength="4" placeholder="000" required>
                            </div>
                        </div>
                    </div>

                    <button class="primary-btn" type="submit">Odemeyi Onayla</button>
                </form>
            </section>

            <aside class="summary-card">
                <h3>Siparis Ozeti</h3>
                <div class="summary-list">
                    <?php $summaryTotal = 0.0; ?>
                    <?php foreach ($cartItems as $item): ?>
                        <?php $price = (float) str_replace(',', '.', preg_replace('/[^\d,]/', '', (string) ($item['fiyat'] ?? '0'))); ?>
                        <?php $summaryTotal += $price; ?>
                        <div class="summary-row">
                            <span><?= e((string) ($item['ad'] ?? 'Urun')); ?></span>
                            <strong><?= e((string) ($item['fiyat'] ?? '₺0')); ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="summary-total">
                    <span>Genel Toplam</span>
                    <strong>₺<?= e(number_format($summaryTotal, 2, ',', '.')); ?></strong>
                </div>
                <p class="summary-note">Siparis olustuktan sonra durum bilgisi admin panelinden guncellenir ve hesabinizda gorunur.</p>
            </aside>
        </div>
    </div>
</main>
<script>
(function () {
    var cardNumberInput = document.getElementById('card_number');
    var expiryInput = document.getElementById('card_expiry');
    var cvcInput = document.getElementById('card_cvc');

    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function () {
            var digits = cardNumberInput.value.replace(/\D+/g, '').slice(0, 16);
            cardNumberInput.value = digits.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
        });
    }

    if (expiryInput) {
        expiryInput.addEventListener('input', function () {
            var digits = expiryInput.value.replace(/\D+/g, '').slice(0, 4);
            if (digits.length >= 3) {
                expiryInput.value = digits.slice(0, 2) + '/' + digits.slice(2);
            } else {
                expiryInput.value = digits;
            }
        });
    }

    if (cvcInput) {
        cvcInput.addEventListener('input', function () {
            cvcInput.value = cvcInput.value.replace(/\D+/g, '').slice(0, 4);
        });
    }
})();
</script>
<?php require dirname(__DIR__) . '/app/views/components/public_site_footer.php'; ?>
<?php require dirname(__DIR__) . '/app/views/layouts/public_footer.php'; ?>
