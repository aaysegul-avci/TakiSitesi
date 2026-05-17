<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startAdminSession();

$message = null;
$error = null;

if (userCountAdmins($pdo) > 0) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $phone = trim((string) ($_POST['phone'] ?? ''));

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Tum zorunlu alanlari doldurun.';
    } else {
        try {
            $role = roleFindBySlug($pdo, 'super-admin');

            if (!$role) {
                $error = 'Super Admin rolu bulunamadi. Once SQL semasini yukleyin.';
            } elseif (userFindByEmail($pdo, $email)) {
                $error = 'Bu e-posta ile zaten bir kullanici mevcut.';
            } else {
                userCreate($pdo, [
                    'role_id' => (int) $role['id'],
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ]);

                $message = 'Ilk Super Admin hesabi olusturuldu. Artik giris yapabilirsiniz.';
            }
        } catch (Throwable $exception) {
            $error = 'Kurulum sirasinda bir hata olustu.';
            logSystemError('admin_setup_error', $exception->getMessage());
        }
    }
}

$pageTitle = 'FLORIA Admin | Ilk Kurulum';
require dirname(__DIR__) . '/app/views/layouts/public_header.php';
?>
<main class="auth-page">
    <div class="auth-shell">
        <section class="auth-showcase">
            <div>
                <div class="auth-brand">First Setup</div>
                <h1>Ilk Super Admin hesabini olusturun.</h1>
                <p>Bu adim, projedeki rol tabanli yonetim sisteminin baslangic kullanicisini uretir.</p>
            </div>
            <div class="auth-badges">
                <span>Super Admin</span>
                <span>password_hash</span>
                <span>UTF-8</span>
            </div>
        </section>
        <section class="auth-panel">
            <div class="auth-kicker">Kurulum</div>
            <h2>Ilk yonetici hesabi</h2>
            <p>Bu islem sadece sistemde hic super admin yoksa kullanilir.</p>

            <?php if ($message): ?>
                <div class="cart-note"><?= e($message); ?> <a href="login.php">Giris ekranina git</a>.</div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="cart-note"><?= e($error); ?></div>
            <?php endif; ?>

            <form class="auth-form" method="post">
                <div class="field">
                    <label for="name">Ad Soyad</label>
                    <input id="name" name="name" type="text" required>
                </div>
                <div class="field">
                    <label for="email">E-posta</label>
                    <input id="email" name="email" type="email" required>
                </div>
                <div class="field">
                    <label for="phone">Telefon</label>
                    <input id="phone" name="phone" type="text">
                </div>
                <div class="field">
                    <label for="password">Sifre</label>
                    <input id="password" name="password" type="password" required>
                </div>
                <button class="primary-btn" type="submit">Hesabi Olustur</button>
            </form>
        </section>
    </div>
</main>
<?php require dirname(__DIR__) . '/app/views/layouts/public_footer.php'; ?>
