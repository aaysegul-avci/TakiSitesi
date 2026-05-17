<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startPublicSession();

try {
    $settings = settingsAll($pdo);
    $menus = menuActiveAll($pdo);
} catch (Throwable $exception) {
    $settings = [];
    $menus = [];
    logSystemError('public_cart_error', $exception->getMessage());
}

$siteTitle = $settings['site_title'] ?? 'FLORIA';
$siteTagline = $settings['site_tagline'] ?? 'Zarafetin yeni hali';
$pageTitle = $siteTitle . ' | Sepet';
$isLoggedIn = publicIsLoggedIn();

require dirname(__DIR__) . '/app/views/layouts/public_header.php';
?>
<main class="cart-page">
    <div class="cart-shell">
        <?php require dirname(__DIR__) . '/app/views/components/public_site_header.php'; ?>

        <section class="cart-hero">
            <div>
                <h1>Sepetinizdeki secimleri gozden gecirin.</h1>
                <p>Urunlerinizi kontrol edin, gerektiginde kaldirin ve guvenli siparis adimina ilerleyin.</p>
            </div>
            <div class="cart-hero-stats">
                <div class="cart-stat">
                    <strong>Sepet</strong>
                    <span>Eklediginiz urunler, toplam tutar ve odeme gecisi burada yonetilir.</span>
                </div>
                <div class="cart-stat">
                    <strong>Siparis</strong>
                    <span>Onayi tamamlanan siparisleriniz admin paneline duser ve durum takibi baslar.</span>
                </div>
            </div>
        </section>

        <div class="cart-layout">
            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h1>Sepetim</h1>
                        <p class="panel-lead">Eklediginiz urunleri inceleyin, kaldirin veya odeme adimina gecin.</p>
                    </div>
                </div>
                <div id="cart-items" class="cart-items"></div>
            </section>

            <aside class="summary-card">
                <h3>Siparis Ozeti</h3>
                <div class="summary-list" id="cart-summary-list"></div>
                <div class="summary-total">
                    <span>Toplam</span>
                    <strong id="total-price">₺0</strong>
                </div>
                <form id="checkout-redirect-form" method="post" action="<?= $isLoggedIn ? 'checkout.php' : 'login.php'; ?>">
                    <input type="hidden" name="cart_payload" id="cart-payload">
                    <button class="primary-btn summary-action" type="submit"><?= $isLoggedIn ? 'Odeme Adimina Gec' : 'Giris Yaparak Devam Et'; ?></button>
                </form>
                <p class="summary-note"><?= $isLoggedIn
                    ? 'Odeme adimina gectiginizde siparisiniz kaydedilir ve durum bilgisi panelden takip edilir.'
                    : 'Siparis durumunu sonradan takip edebilmek icin once hesabinizla giris yapmaniz gerekir.'; ?></p>
            </aside>
        </div>
    </div>
</main>
<script>
const cart = JSON.parse(localStorage.getItem('floria_sepet')) || [];
const cartItems = document.getElementById('cart-items');
const summaryList = document.getElementById('cart-summary-list');
const totalPrice = document.getElementById('total-price');
const cartPayload = document.getElementById('cart-payload');
const checkoutRedirectForm = document.getElementById('checkout-redirect-form');

function renderCart() {
    cartItems.innerHTML = '';
    summaryList.innerHTML = '';
    let total = 0;

    if (!cart.length) {
        cartItems.innerHTML = '<div class="empty-state">Sepetiniz bos. Urunler sayfasindan urun ekleyebilirsiniz.</div>';
        totalPrice.innerText = '₺0';
        cartPayload.value = '[]';
        return;
    }

    cart.forEach((item, index) => {
        const numericPrice = parseFloat(String(item.fiyat).replace(/[^\d,]/g, '').replace(',', '.')) || 0;
        total += numericPrice;

        cartItems.innerHTML += `
            <div class="cart-item">
                <img src="${item.resim}" alt="${item.ad}">
                <div class="cart-info">
                    <h3>${item.ad}</h3>
                    <p class="price">${item.fiyat}</p>
                    <div class="cart-caption">Siparis onayi sonrasi urun durumunu hesabinizdaki Siparislerim ekranindan takip edebilirsiniz.</div>
                </div>
                <button class="remove-btn" type="button" onclick="removeItem(${index})">Kaldir</button>
            </div>
        `;

        summaryList.innerHTML += `
            <div class="summary-row">
                <span>${item.ad}</span>
                <strong>${item.fiyat}</strong>
            </div>
        `;
    });

    totalPrice.innerText = '₺' + total.toFixed(2).replace('.', ',');
    cartPayload.value = JSON.stringify(cart);
}

function removeItem(index) {
    cart.splice(index, 1);
    localStorage.setItem('floria_sepet', JSON.stringify(cart));
    renderCart();
}

checkoutRedirectForm.addEventListener('submit', function(event) {
    if (!cart.length) {
        event.preventDefault();
        alert('Sepetiniz bos.');
    }
});

renderCart();
</script>
<?php require dirname(__DIR__) . '/app/views/components/public_site_footer.php'; ?>
<?php require dirname(__DIR__) . '/app/views/layouts/public_footer.php'; ?>
