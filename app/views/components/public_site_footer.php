<?php

declare(strict_types=1);

$settings = $settings ?? [];
?>
<footer>
    <p><?= e($settings['footer_text'] ?? 'FLORIA Tum haklari saklidir.'); ?></p>
    <p><?= e($settings['contact_email'] ?? 'iletisim bilgisi ayarlardan gelir'); ?> | <?= e($settings['contact_phone'] ?? 'telefon bilgisi ayarlardan gelir'); ?></p>
</footer>
