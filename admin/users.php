<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

startAdminSession();
requireAdminLogin();
requireAdminPermission('users.view');

$message = null;
$error = null;
$admin = adminUser();
$isSuperAdmin = ($admin['role_slug'] ?? '') === 'super-admin';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isSuperAdmin) {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $roleSlug = trim((string) ($_POST['role_slug'] ?? ''));

        if ($name === '' || $email === '' || $password === '' || $roleSlug === '') {
            $error = 'Tum zorunlu alanlari doldurun.';
        } elseif (!in_array($roleSlug, ['editor', 'moderator'], true)) {
            $error = 'Bu ekrandan yalnizca editor veya moderator hesabi olusturulabilir.';
        } elseif (userFindByEmail($pdo, $email)) {
            $error = 'Bu e-posta ile zaten kayitli bir hesap bulunuyor.';
        } else {
            $role = roleFindBySlug($pdo, $roleSlug);

            if (!$role) {
                $error = 'Secilen rol bulunamadi.';
            } else {
                userCreate($pdo, [
                    'role_id' => (int) $role['id'],
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ]);

                $message = ucfirst($roleSlug) . ' hesabi basariyla olusturuldu.';
            }
        }
    }

    $users = userAll($pdo);
    $roles = roleAll($pdo);
} catch (Throwable $exception) {
    $users = [];
    $roles = [];
    $error = 'Kullanici verileri yuklenirken bir sorun olustu.';
    logSystemError('admin_users_error', $exception->getMessage());
}

$managerRoles = array_values(array_filter($roles, static function (array $role): bool {
    return in_array($role['slug'], ['editor', 'moderator'], true);
}));
$teamUsers = array_values(array_filter($users, static function (array $user): bool {
    return in_array($user['role_slug'], ['super-admin', 'editor', 'moderator'], true);
}));
$customerUsers = array_values(array_filter($users, static function (array $user): bool {
    return $user['role_slug'] === 'customer';
}));

$pageTitle = 'FLORIA Admin | Kullanicilar';
require dirname(__DIR__) . '/app/views/layouts/admin_header.php';
require dirname(__DIR__) . '/app/views/components/admin_sidebar.php';
?>
<main class="admin-main">
    <section class="catalog-toolbar">
        <div>
            <h2>Kullanici Yonetimi</h2>
            <p>Musteri hesaplarini listeleyin, yonetici rollerini kontrol edin ve gerekirse yeni editor veya moderator ekleyin.</p>
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
                    <h2>Yonetici Hesabi Olustur</h2>
                    <p>Bu alan yalnizca Super Admin tarafindan kullanilir. Buradan editor veya moderator hesabi acabilirsiniz.</p>
                </div>
            </div>

            <?php if ($isSuperAdmin): ?>
                <form class="checkout-form" method="post">
                    <div class="two-col">
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
                            <label for="role_slug">Rol</label>
                            <select id="role_slug" name="role_slug" required>
                                <option value="">Rol secin</option>
                                <?php foreach ($managerRoles as $role): ?>
                                    <option value="<?= e($role['slug']); ?>"><?= e($role['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field full-span">
                            <label for="password">Gecici Sifre</label>
                            <input id="password" name="password" type="password" required>
                        </div>
                    </div>
                    <button class="primary-btn" type="submit">Hesap Olustur</button>
                </form>
            <?php else: ?>
                <div class="empty-state">Bu alani yalnizca Super Admin kullanabilir. Siz mevcut kullanicilari listeleme yetkisine sahipsiniz.</div>
            <?php endif; ?>
        </section>

        <aside class="summary-card">
            <h3>Rol Dagilimi</h3>
            <div class="summary-list">
                <div class="summary-row"><span>Super Admin</span><strong><?= e((string) count(array_filter($users, static fn(array $user): bool => $user['role_slug'] === 'super-admin'))); ?></strong></div>
                <div class="summary-row"><span>Editor</span><strong><?= e((string) count(array_filter($users, static fn(array $user): bool => $user['role_slug'] === 'editor'))); ?></strong></div>
                <div class="summary-row"><span>Moderator</span><strong><?= e((string) count(array_filter($users, static fn(array $user): bool => $user['role_slug'] === 'moderator'))); ?></strong></div>
                <div class="summary-row"><span>Customer</span><strong><?= e((string) count(array_filter($users, static fn(array $user): bool => $user['role_slug'] === 'customer'))); ?></strong></div>
            </div>
            <p class="summary-note">Magaza musterileri customer olarak kaydolur. Admin paneline erisim yalnizca super admin, editor ve moderator rollerine aciktir.</p>
        </aside>
    </section>

    <section class="panel-card" style="margin-top: 24px;">
        <div class="panel-header">
            <div>
                <h2>Yonetim Ekibi</h2>
                <p>Admin paneline erisebilen hesaplar bu alanda listelenir.</p>
            </div>
            <div class="panel-badge"><?= e((string) count($teamUsers)); ?> Hesap</div>
        </div>

        <?php if (empty($teamUsers)): ?>
            <div class="empty-state">Henuz yonetim ekibi hesabi bulunmuyor.</div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($teamUsers as $user): ?>
                    <article class="cart-item cart-item-order">
                        <div class="cart-info" style="grid-column: 1 / -1;">
                            <h3><?= e($user['name']); ?></h3>
                            <p><?= e($user['email']); ?></p>
                            <div class="cart-caption">
                                Rol: <?= e($user['role_name']); ?><br>
                                Telefon: <?= e($user['phone'] ?: '-'); ?><br>
                                Durum: <?= $user['is_active'] ? 'Aktif' : 'Pasif'; ?><br>
                                Kayit Tarihi: <?= e((string) $user['created_at']); ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel-card" style="margin-top: 24px;">
        <div class="panel-header">
            <div>
                <h2>Musteriler</h2>
                <p>Magaza uzerinden kayit olan normal kullanici hesaplari bu listede tutulur.</p>
            </div>
            <div class="panel-badge"><?= e((string) count($customerUsers)); ?> Musteri</div>
        </div>

        <?php if (empty($customerUsers)): ?>
            <div class="empty-state">Henuz kayitli musteri bulunmuyor.</div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($customerUsers as $user): ?>
                    <article class="cart-item cart-item-order">
                        <div class="cart-info" style="grid-column: 1 / -1;">
                            <h3><?= e($user['name']); ?></h3>
                            <p><?= e($user['email']); ?></p>
                            <div class="cart-caption">
                                Rol: Musteri<br>
                                Telefon: <?= e($user['phone'] ?: '-'); ?><br>
                                Durum: <?= $user['is_active'] ? 'Aktif' : 'Pasif'; ?><br>
                                Kayit Tarihi: <?= e((string) $user['created_at']); ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
<?php require dirname(__DIR__) . '/app/views/layouts/admin_footer.php'; ?>
