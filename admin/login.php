<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startAdminSession();

if (adminIsLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    try {
        $user = userFindByEmail($pdo, $email);

        if (!$user || !$user['is_active'] || !password_verify($password, $user['password_hash'])) {
            $error = 'E-posta veya sifre hatali.';
            logSystemError('admin_login_failed', 'Basarisiz admin giris denemesi: ' . $email);
        } elseif (!in_array($user['role_slug'], ['super-admin', 'editor', 'moderator'], true)) {
            $error = 'Bu hesap admin paneline erisemiyor.';
            logSystemError('admin_login_forbidden', 'Yetkisiz admin giris denemesi: ' . $email);
        } else {
            $permissions = permissionSlugsByRoleId($pdo, (int) $user['role_id']);

            $_SESSION['admin'] = [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role_name' => $user['role_name'],
                'role_slug' => $user['role_slug'],
                'permissions' => $permissions,
            ];

            userUpdateLastLogin($pdo, (int) $user['id']);
            header('Location: index.php');
            exit;
        }
    } catch (Throwable $exception) {
        $error = 'Giris islemi sirasinda bir sorun olustu.';
        logSystemError('admin_login_error', $exception->getMessage());
    }
}

$needsSetup = userCountAdmins($pdo) === 0;
$pageTitle = 'FLORIA Admin | Giris';
require dirname(__DIR__) . '/app/views/layouts/public_header.php';
?>
<main class="auth-page">
    <div class="auth-shell">
        <section class="auth-showcase">
            <div>
                <div class="auth-brand">Admin Access</div>
                <h1>Yonetim paneli icin guvenli giris.</h1>
                <p>Bu ekran veritabanina bagli calisir, sifreleri hashlenmis olarak kontrol eder ve session tabanli oturum baslatir.</p>
            </div>
        </section>
        <section class="auth-panel">
            <div class="auth-kicker">Admin Login</div>
            <h2>Yonetim paneline giris yapin</h2>
            <p>Super Admin, Editor veya Moderator rollerine sahip hesaplar buradan giris yapabilir.</p>

            <?php if ($needsSetup): ?>
                <div class="cart-note">
                    Sistemde henuz super admin yok. Once <a href="setup.php">ilk yonetici hesabini olusturun</a>.
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="cart-note"><?= e($error); ?></div>
            <?php endif; ?>

            <form class="auth-form" method="post">
                <div class="field">
                    <label for="email">E-posta</label>
                    <input id="email" name="email" type="email" placeholder="admin@floria.com" required>
                </div>
                <div class="field">
                    <label for="password">Sifre</label>
                    <input id="password" name="password" type="password" placeholder="Sifreniz" required>
                </div>
                <button class="primary-btn" type="submit">Giris Yap</button>
            </form>
        </section>
    </div>
</main>
<?php require dirname(__DIR__) . '/app/views/layouts/public_footer.php'; ?>
