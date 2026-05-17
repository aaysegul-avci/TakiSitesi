<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startPublicSession();

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Tum zorunlu alanlari doldurun.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Sifreler birbiriyle eslesmiyor.';
    } elseif (strlen($password) < 6) {
        $error = 'Sifre en az 6 karakter olmali.';
    } else {
        try {
            $customerRole = roleFindBySlug($pdo, 'customer');

            if (!$customerRole) {
                $error = 'Varsayilan kullanici rolu bulunamadi. SQL semasini yukleyin.';
            } elseif (userFindByEmail($pdo, $email)) {
                $error = 'Bu e-posta ile zaten kayit mevcut.';
            } else {
                userCreate($pdo, [
                    'role_id' => (int) $customerRole['id'],
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ]);

                $success = 'Kayit basarili. Artik giris yapabilirsiniz.';
            }
        } catch (Throwable $exception) {
            $error = 'Kayit islemi sirasinda bir sorun olustu.';
            logSystemError('public_register_error', $exception->getMessage());
        }
    }
}

$pageTitle = 'FLORIA | Kayit';
require dirname(__DIR__) . '/app/views/layouts/public_header.php';
?>
<main class="auth-page">
    <div class="auth-shell">
        <section class="auth-showcase">
            <div>
                <div class="auth-brand">FLORIA Membership</div>
                <h1>Alisveris deneyiminizi hesabinizla yonetin.</h1>
                <p>Kayit olduktan sonra siparislerinizi takip edebilir, yeni koleksiyonlardan ilk siz haberdar olabilirsiniz.</p>
            </div>
            <div class="auth-badges">
                <span>password_hash</span>
                <span>Prepared Insert</span>
                <span>UTF-8</span>
            </div>
        </section>
        <section class="auth-panel">
            <div class="auth-kicker">Kayit Ol</div>
            <h2>Yeni kullanici hesabi olusturun</h2>
            <p>Hesabinizi olusturarak siparis takibinizi kolaylastirin ve bilgilerinizi tek ekranda yonetin.</p>

            <?php if ($success): ?>
                <?= renderAlert('success', $success); ?>
                <div class="auth-links"><a href="login.php">Giris yap</a></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <?= renderAlert('error', $error); ?>
            <?php endif; ?>

            <form class="auth-form" method="post">
                <div class="auth-form-grid">
                    <div class="field">
                        <label for="name">Ad Soyad</label>
                        <input id="name" name="name" type="text" required>
                    </div>
                    <div class="field">
                        <label for="email">E-posta</label>
                        <input id="email" name="email" type="email" required>
                    </div>
                </div>
                <div class="field">
                    <label for="phone">Telefon</label>
                    <input id="phone" name="phone" type="text">
                </div>
                <div class="auth-form-grid">
                    <div class="field">
                        <label for="password">Sifre</label>
                        <input id="password" name="password" type="password" required>
                    </div>
                    <div class="field">
                        <label for="password_confirm">Sifre Tekrar</label>
                        <input id="password_confirm" name="password_confirm" type="password" required>
                    </div>
                </div>
                <button class="primary-btn" type="submit">Kaydol</button>
                <a class="secondary-btn" href="login.php">Giris Yap</a>
            </form>
        </section>
    </div>
</main>
<?php require dirname(__DIR__) . '/app/views/components/public_site_footer.php'; ?>
<?php require dirname(__DIR__) . '/app/views/layouts/public_footer.php'; ?>
