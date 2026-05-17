<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startPublicSession();

if (publicIsLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    try {
        $user = userFindByEmail($pdo, $email);

        if ($user && $user['is_active'] && password_verify($password, $user['password_hash'])) {
            $_SESSION['public_user'] = [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
            ];
            userUpdateLastLogin($pdo, (int) $user['id']);
            header('Location: index.php');
            exit;
        } else {
            $error = 'E-posta veya sifre hatali.';
        }
    } catch (Throwable $exception) {
        $error = 'Giris islemi sirasinda bir hata olustu.';
        logSystemError('public_login_error', $exception->getMessage());
    }
}

$pageTitle = 'FLORIA | Kullanici Girisi';
require dirname(__DIR__) . '/app/views/layouts/public_header.php';
?>
<main class="auth-page">
    <div class="auth-shell">
        <section class="auth-showcase">
            <div>
                <div class="auth-brand">FLORIA Membership</div>
                <h1>Hesabiniza girin, siparislerinizi takip edin.</h1>
                <p>Sepetiniz, siparis gecmisiniz ve teslimat sureciniz tek bir hesap altinda sizi bekliyor.</p>
            </div>
        </section>
        <section class="auth-panel">
            <div class="auth-kicker">Kullanici Girisi</div>
            <h2>FLORIA hesabinizla devam edin</h2>
            <p>Kayitli kullanicilar giris yapabilir; henuz hesabiniz yoksa hemen yeni hesap olusturabilirsiniz.</p>

            <?php if (!empty($error)): ?>
                <?= renderAlert('error', $error); ?>
            <?php endif; ?>

            <form class="auth-form" method="post">
                <div class="field">
                    <label for="email">E-posta</label>
                    <input id="email" name="email" type="email" required>
                </div>
                <div class="field">
                    <label for="password">Sifre</label>
                    <input id="password" name="password" type="password" required>
                </div>
                <button class="primary-btn" type="submit">Giris Yap</button>
                <a class="secondary-btn" href="register.php">Kayit Ol</a>
            </form>
        </section>
    </div>
</main>
<?php require dirname(__DIR__) . '/app/views/components/public_site_footer.php'; ?>
<?php require dirname(__DIR__) . '/app/views/layouts/public_footer.php'; ?>
