<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startAdminSession();
requireAdminLogin();

$admin = adminUser();
$message = null;
$error = null;

try {
    $currentUser = userFindByEmail($pdo, $admin['email']);

    if (!$currentUser) {
        throw new RuntimeException('Aktif admin kullanicisi bulunamadi.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $newPassword = (string) ($_POST['new_password'] ?? '');

        $payload = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ];

        if ($newPassword !== '') {
            $payload['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        userUpdateProfile($pdo, (int) $currentUser['id'], $payload);

        $_SESSION['admin']['name'] = $name;
        $_SESSION['admin']['email'] = $email;
        $message = 'Profil bilgileriniz guncellendi.';
        $currentUser = userFindByEmail($pdo, $email);
    }
} catch (Throwable $exception) {
    $error = 'Profil sayfasi yuklenirken bir sorun olustu.';
    logSystemError('admin_profile_error', $exception->getMessage());
}

$pageTitle = 'FLORIA Admin | Profilim';
require dirname(__DIR__) . '/app/views/layouts/admin_header.php';
require dirname(__DIR__) . '/app/views/components/admin_sidebar.php';
?>
<main class="admin-main">
    <section class="catalog-toolbar">
        <div>
            <h2>Profilim</h2>
            <p>Hoca maddesindeki admin kendi bilgilerini guncelleyebilmeli gereksinimi icin ilk ekran.</p>
        </div>
        <div class="panel-badge">Profile Update</div>
    </section>

    <?php if ($message): ?>
        <div class="cart-note" style="margin-top: 24px;"><?= e($message); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="cart-note" style="margin-top: 24px;"><?= e($error); ?></div>
    <?php endif; ?>

    <section class="panel-card" style="margin-top: 24px;">
        <form class="checkout-form" method="post">
            <div class="two-col">
                <div class="field">
                    <label for="name">Ad Soyad</label>
                    <input id="name" name="name" type="text" value="<?= e($currentUser['name'] ?? ''); ?>" required>
                </div>
                <div class="field">
                    <label for="email">E-posta</label>
                    <input id="email" name="email" type="email" value="<?= e($currentUser['email'] ?? ''); ?>" required>
                </div>
                <div class="field">
                    <label for="phone">Telefon</label>
                    <input id="phone" name="phone" type="text" value="<?= e($currentUser['phone'] ?? ''); ?>">
                </div>
                <div class="field">
                    <label for="new_password">Yeni Sifre</label>
                    <input id="new_password" name="new_password" type="password" placeholder="Bos birakirsaniz degismez">
                </div>
            </div>
            <button class="primary-btn" type="submit">Profili Guncelle</button>
        </form>
    </section>
</main>
<?php require dirname(__DIR__) . '/app/views/layouts/admin_footer.php'; ?>
