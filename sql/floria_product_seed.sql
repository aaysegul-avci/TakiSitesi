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
