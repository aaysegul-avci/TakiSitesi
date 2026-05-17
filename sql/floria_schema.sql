SET NAMES utf8mb4;
SET time_zone = '+03:00';

CREATE DATABASE IF NOT EXISTS floria_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE floria_db;

CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE role_permissions (
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_role_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id INT UNSIGNED NOT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    short_description VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    sku VARCHAR(100) DEFAULT NULL UNIQUE,
    cover_image VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255) DEFAULT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    meta_title VARCHAR(255) DEFAULT NULL,
    meta_description VARCHAR(255) DEFAULT NULL,
    content LONGTEXT DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE menus (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    url VARCHAR(255) NOT NULL,
    target VARCHAR(20) NOT NULL DEFAULT '_self',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sliders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    image_path VARCHAR(255) NOT NULL,
    button_text VARCHAR(100) DEFAULT NULL,
    button_url VARCHAR(255) DEFAULT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(120) NOT NULL UNIQUE,
    setting_value LONGTEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    customer_name VARCHAR(120) NOT NULL,
    customer_email VARCHAR(150) DEFAULT NULL,
    customer_phone VARCHAR(30) DEFAULT NULL,
    address TEXT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    line_total DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    log_type VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    message TEXT NOT NULL,
    context JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles (name, slug) VALUES
('Super Admin', 'super-admin'),
('Editor', 'editor'),
('Moderator', 'moderator'),
('Customer', 'customer');

INSERT INTO permissions (name, slug) VALUES
('Dashboard Goruntule', 'dashboard.view'),
('Urun Ekle', 'products.create'),
('Urun Duzenle', 'products.update'),
('Urun Sil', 'products.delete'),
('Ayarlari Duzenle', 'settings.update'),
('Menu Duzenle', 'menus.manage'),
('Slider Duzenle', 'sliders.manage'),
('Kullanicilari Goruntule', 'users.view');

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'super-admin';

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
INNER JOIN permissions p ON p.slug IN (
    'dashboard.view',
    'products.create',
    'products.update',
    'menus.manage',
    'sliders.manage'
)
WHERE r.slug = 'editor';

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
INNER JOIN permissions p ON p.slug IN (
    'dashboard.view',
    'users.view'
)
WHERE r.slug = 'moderator';

INSERT INTO categories (name, slug, description, sort_order) VALUES
('Yuzuk', 'yuzuk', 'Floria yuzuk koleksiyonu', 1),
('Kolye', 'kolye', 'Floria kolye koleksiyonu', 2),
('Kupe', 'kupe', 'Floria kupe koleksiyonu', 3),
('Bileklik', 'bileklik', 'Floria bileklik koleksiyonu', 4);

INSERT INTO products (category_id, name, slug, short_description, description, price, stock, sku, cover_image, is_active)
SELECT id, 'Kelebekli Yuzuk', 'kelebekli-yuzuk', 'Gunluk kullanimda zarif duran, ince formuyla dikkat ceken hafif bir tasarim.', 'Gunluk kullanimda zarif duran, ince formuyla dikkat ceken hafif bir tasarim.', 150.00, 12, 'YZK-001', 'images/kelebekli-yuzuk.jpg', 1
FROM categories WHERE slug = 'yuzuk'
ON DUPLICATE KEY UPDATE
short_description = VALUES(short_description), description = VALUES(description), price = VALUES(price), stock = VALUES(stock), sku = VALUES(sku), cover_image = VALUES(cover_image), is_active = VALUES(is_active);

INSERT INTO products (category_id, name, slug, short_description, description, price, stock, sku, cover_image, is_active)
SELECT id, 'Uclu Kolye', 'uclu-kolye', 'Katmanli gorunumu sayesinde tek basina dahi tamamlanmis bir stil etkisi verir.', 'Katmanli gorunumu sayesinde tek basina dahi tamamlanmis bir stil etkisi verir.', 250.00, 10, 'KLY-001', 'images/uclu-kolye.jpg', 1
FROM categories WHERE slug = 'kolye'
ON DUPLICATE KEY UPDATE
short_description = VALUES(short_description), description = VALUES(description), price = VALUES(price), stock = VALUES(stock), sku = VALUES(sku), cover_image = VALUES(cover_image), is_active = VALUES(is_active);

INSERT INTO products (category_id, name, slug, short_description, description, price, stock, sku, cover_image, is_active)
SELECT id, 'Tasli Kupe', 'tasli-kupe', 'Iltili detaylariyla sade kombinleri bir anda daha ozenli gosteren bir secim.', 'Iltili detaylariyla sade kombinleri bir anda daha ozenli gosteren bir secim.', 200.00, 9, 'KPE-001', 'images/tasli-kupe.jpg', 1
FROM categories WHERE slug = 'kupe'
ON DUPLICATE KEY UPDATE
short_description = VALUES(short_description), description = VALUES(description), price = VALUES(price), stock = VALUES(stock), sku = VALUES(sku), cover_image = VALUES(cover_image), is_active = VALUES(is_active);

INSERT INTO products (category_id, name, slug, short_description, description, price, stock, sku, cover_image, is_active)
SELECT id, 'Yildizli Yuzuk', 'yildizli-yuzuk', 'Parmakta ince bir isilti birakan, minimal ve genc bir yorum tasir.', 'Parmakta ince bir isilti birakan, minimal ve genc bir yorum tasir.', 120.00, 14, 'YZK-002', 'images/yildizli-yuzuk.jpg', 1
FROM categories WHERE slug = 'yuzuk'
ON DUPLICATE KEY UPDATE
short_description = VALUES(short_description), description = VALUES(description), price = VALUES(price), stock = VALUES(stock), sku = VALUES(sku), cover_image = VALUES(cover_image), is_active = VALUES(is_active);

INSERT INTO products (category_id, name, slug, short_description, description, price, stock, sku, cover_image, is_active)
SELECT id, 'Kalpli Yuzuk', 'kalpli-yuzuk', 'Romantik formu ile gunluk kullanim ve hediye secenekleri icin guzel bir parca.', 'Romantik formu ile gunluk kullanim ve hediye secenekleri icin guzel bir parca.', 120.00, 15, 'YZK-003', 'images/kalpli-yuzuk.jpg', 1
FROM categories WHERE slug = 'yuzuk'
ON DUPLICATE KEY UPDATE
short_description = VALUES(short_description), description = VALUES(description), price = VALUES(price), stock = VALUES(stock), sku = VALUES(sku), cover_image = VALUES(cover_image), is_active = VALUES(is_active);

INSERT INTO products (category_id, name, slug, short_description, description, price, stock, sku, cover_image, is_active)
SELECT id, 'Yildizli Kupe', 'yildizli-kupe', 'Aksam stillerinde daha belirgin duran, parlayan ve iddiali bir modeldir.', 'Aksam stillerinde daha belirgin duran, parlayan ve iddiali bir modeldir.', 350.00, 7, 'KPE-002', 'images/yildiz-kupe.jpg', 1
FROM categories WHERE slug = 'kupe'
ON DUPLICATE KEY UPDATE
short_description = VALUES(short_description), description = VALUES(description), price = VALUES(price), stock = VALUES(stock), sku = VALUES(sku), cover_image = VALUES(cover_image), is_active = VALUES(is_active);

INSERT INTO products (category_id, name, slug, short_description, description, price, stock, sku, cover_image, is_active)
SELECT id, 'Cicekli Kupe', 'cicekli-kupe', 'Yumusak formu ve floral hissiyle daha romantik kombinleri tamamlar.', 'Yumusak formu ve floral hissiyle daha romantik kombinleri tamamlar.', 250.00, 8, 'KPE-003', 'images/cicekli-kupe.jpg', 1
FROM categories WHERE slug = 'kupe'
ON DUPLICATE KEY UPDATE
short_description = VALUES(short_description), description = VALUES(description), price = VALUES(price), stock = VALUES(stock), sku = VALUES(sku), cover_image = VALUES(cover_image), is_active = VALUES(is_active);

INSERT INTO products (category_id, name, slug, short_description, description, price, stock, sku, cover_image, is_active)
SELECT id, 'Kalpli Sahmeran', 'kalpli-sahmeran', 'Bilek ve parmak arasindaki ince baglantisiyla farkli ama zarif bir etki yaratir.', 'Bilek ve parmak arasindaki ince baglantisiyla farkli ama zarif bir etki yaratir.', 250.00, 6, 'BLK-001', 'images/sahmeran.jpg', 1
FROM categories WHERE slug = 'bileklik'
ON DUPLICATE KEY UPDATE
short_description = VALUES(short_description), description = VALUES(description), price = VALUES(price), stock = VALUES(stock), sku = VALUES(sku), cover_image = VALUES(cover_image), is_active = VALUES(is_active);

INSERT INTO products (category_id, name, slug, short_description, description, price, stock, sku, cover_image, is_active)
SELECT id, 'Yeni Yil Bileklik', 'yeni-yil-bileklik', 'Canli gorunumu sayesinde gunluk stillere enerjik ve dikkat cekici bir dokunus ekler.', 'Canli gorunumu sayesinde gunluk stillere enerjik ve dikkat cekici bir dokunus ekler.', 150.00, 11, 'BLK-002', 'images/yeni-yil-bileklik.jpg', 1
FROM categories WHERE slug = 'bileklik'
ON DUPLICATE KEY UPDATE
short_description = VALUES(short_description), description = VALUES(description), price = VALUES(price), stock = VALUES(stock), sku = VALUES(sku), cover_image = VALUES(cover_image), is_active = VALUES(is_active);

INSERT INTO products (category_id, name, slug, short_description, description, price, stock, sku, cover_image, is_active)
SELECT id, 'Ikili Bileklik', 'ikili-bileklik', 'Katmanli hissi sevenler icin tek hamlede daha zengin gorunen bir form sunar.', 'Katmanli hissi sevenler icin tek hamlede daha zengin gorunen bir form sunar.', 350.00, 5, 'BLK-003', 'images/ikili-bileklik.jpg', 1
FROM categories WHERE slug = 'bileklik'
ON DUPLICATE KEY UPDATE
short_description = VALUES(short_description), description = VALUES(description), price = VALUES(price), stock = VALUES(stock), sku = VALUES(sku), cover_image = VALUES(cover_image), is_active = VALUES(is_active);

INSERT INTO products (category_id, name, slug, short_description, description, price, stock, sku, cover_image, is_active)
SELECT id, 'Gumus Bileklik', 'gumus-bileklik', 'Temiz ve klasik durusuyla uzun sure kullanilabilecek zamansiz bir secimdir.', 'Temiz ve klasik durusuyla uzun sure kullanilabilecek zamansiz bir secimdir.', 350.00, 7, 'BLK-004', 'images/gumus-bileklik.jpg', 1
FROM categories WHERE slug = 'bileklik'
ON DUPLICATE KEY UPDATE
short_description = VALUES(short_description), description = VALUES(description), price = VALUES(price), stock = VALUES(stock), sku = VALUES(sku), cover_image = VALUES(cover_image), is_active = VALUES(is_active);

INSERT INTO products (category_id, name, slug, short_description, description, price, stock, sku, cover_image, is_active)
SELECT id, 'Tasli Yuzuk', 'tasli-yuzuk', 'Zarif isiltisi sayesinde hem sade hem de daha ozenli kombinlerde rahatca kullanilir.', 'Zarif isiltisi sayesinde hem sade hem de daha ozenli kombinlerde rahatca kullanilir.', 150.00, 13, 'YZK-004', 'images/tasli-yuzuk.jpg', 1
FROM categories WHERE slug = 'yuzuk'
ON DUPLICATE KEY UPDATE
short_description = VALUES(short_description), description = VALUES(description), price = VALUES(price), stock = VALUES(stock), sku = VALUES(sku), cover_image = VALUES(cover_image), is_active = VALUES(is_active);

INSERT INTO products (category_id, name, slug, short_description, description, price, stock, sku, cover_image, is_active)
SELECT id, 'Yuvarlak Kupe', 'yuvarlak-kupe', 'Dengeli ve sade formu ile her gun rahatca tercih edilebilecek pratik bir modeldir.', 'Dengeli ve sade formu ile her gun rahatca tercih edilebilecek pratik bir modeldir.', 250.00, 10, 'KPE-004', 'images/yuvarlak-kupe.jpg', 1
FROM categories WHERE slug = 'kupe'
ON DUPLICATE KEY UPDATE
short_description = VALUES(short_description), description = VALUES(description), price = VALUES(price), stock = VALUES(stock), sku = VALUES(sku), cover_image = VALUES(cover_image), is_active = VALUES(is_active);

INSERT INTO products (category_id, name, slug, short_description, description, price, stock, sku, cover_image, is_active)
SELECT id, 'Klasik Yuzuk', 'klasik-yuzuk', 'Minimal dokusu sayesinde farkli takilarla kolayca kombinlenebilen sade bir tasarim.', 'Minimal dokusu sayesinde farkli takilarla kolayca kombinlenebilen sade bir tasarim.', 150.00, 16, 'YZK-005', 'images/yuzuk.jpg', 1
FROM categories WHERE slug = 'yuzuk'
ON DUPLICATE KEY UPDATE
short_description = VALUES(short_description), description = VALUES(description), price = VALUES(price), stock = VALUES(stock), sku = VALUES(sku), cover_image = VALUES(cover_image), is_active = VALUES(is_active);

INSERT INTO menus (title, url, target, sort_order, is_active) VALUES
('Ana Sayfa', '/public/index.php', '_self', 1, 1),
('Urunler', '/public/products.php', '_self', 2, 1),
('Giris Yap', '/public/login.php', '_self', 3, 1),
('Kayit Ol', '/public/register.php', '_self', 4, 1);

INSERT INTO sliders (title, description, image_path, button_text, button_url, sort_order, is_active) VALUES
('Isiltinizi Tamamlayan Ozel Secki', 'Gunluk sikligi zarif detaylarla tamamlayan yuzuk, kolye, kupe ve bileklik koleksiyonlarini kesfedin.', 'images/arka foto.jpg', 'Koleksiyonu Kesfet', '/public/products.php', 1, 1);

INSERT INTO settings (setting_key, setting_value) VALUES
('site_title', 'FLORIA'),
('site_tagline', 'Isiltinizi tamamlayan zarif dokunuslar'),
('contact_email', 'iletisim@floria.com'),
('contact_phone', '+90 555 000 00 00'),
('contact_address', 'Istanbul, Turkiye'),
('instagram_url', 'https://instagram.com/floria'),
('facebook_url', 'https://facebook.com/floria'),
('logo_path', 'images/sepetlog.gif'),
('footer_text', 'FLORIA Tum haklari saklidir.'),
('home_intro_title', 'FLORIA''ya Hos Geldiniz'),
('home_intro_text', 'Her detayinda zarafeti hissettiren taki seckileriyle gunluk stilinizi tamamlayin.'),
('products_hero_title', 'Tum koleksiyonu tek ekranda kesfedin.'),
('products_hero_text', 'Yuzuk, kolye, kupe ve bileklik seckilerini filtreleyin; tarziniza en uygun parcayi kolayca bulun.'),
('orders_hero_title', 'Siparislerinizi adim adim takip edin.'),
('orders_hero_text', 'Siparislerinizin guncel durumunu, olusturma tarihini ve toplam tutarini bu ekranda rahatca gorebilirsiniz.')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
