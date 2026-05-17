# FLORIA Web Programlama 2 Proje Teslim Raporu

Bu rapor, FLORIA taki e-ticaret projesinin hocanin proje maddelerine gore hangi dosyalar ve modullerle karsilandigini ozetler.

## 1. Responsive kullanici arayuzu ve admin paneli

Public site ve admin paneli ortak CSS yapisi ile mobil, tablet ve masaustu ekranlara uyumlu olacak sekilde hazirlandi.

Ilgili dosyalar:
- `styles.css`
- `public/*`
- `admin/*`
- `app/views/layouts/*`
- `app/views/components/*`

## 2. Normalize veritabani ve foreign key iliskileri

Veritabani; kullanicilar, roller, yetkiler, kategoriler, urunler, siparisler, siparis kalemleri, slider, menu, ayarlar ve loglar gibi ayri tablolara bolundu. Tablolar arasinda foreign key iliskileri tanimlandi.

Ilgili dosya:
- `sql/floria_schema.sql`

Onemli tablolar:
- `roles`
- `permissions`
- `role_permissions`
- `users`
- `categories`
- `products`
- `orders`
- `order_items`
- `menus`
- `sliders`
- `settings`
- `logs`

## 3. Prepared Statements kullanimi

Veritabani islemleri PDO uzerinden prepared statements ile yazildi. Bu sayede SQL Injection riskine karsi temel koruma saglandi.

Ilgili dosyalar:
- `app/config/database.php`
- `app/repositories/*`

## 4. Turkce karakter ve UTF-8 destegi

Veritabani `utf8mb4` karakter setiyle olusturuldu. HTML sayfalarinda UTF-8 meta etiketleri kullanildi. Ekrana basilan kullanici/veritabani verileri `htmlspecialchars` yardimci fonksiyonu ile guvenli hale getirildi.

Ilgili dosyalar:
- `sql/floria_schema.sql`
- `app/views/layouts/public_header.php`
- `app/views/layouts/admin_header.php`
- `app/helpers/functions.php`

## 5. Sifrelerin hashlenmesi

Kullanici ve admin sifreleri veritabanina acik metin olarak kaydedilmez. Kayit ve admin olusturma islemlerinde `password_hash`, giris islemlerinde `password_verify` kullanilir.

Ilgili dosyalar:
- `public/register.php`
- `public/login.php`
- `admin/login.php`
- `admin/setup.php`
- `admin/users.php`
- `admin/profile.php`

## 6. Moduler mimari

Header, footer, sidebar, veritabani baglantisi, yardimci fonksiyonlar ve repository dosyalari ayrildi. Sayfalar bu dosyalari ihtiyac duydugunda dahil eder.

Ilgili klasorler:
- `app/config`
- `app/helpers`
- `app/repositories`
- `app/views/layouts`
- `app/views/components`

## 7. Rol ve yetki sistemi

Projede Super Admin, Editor, Moderator ve Customer rolleri bulunur. Admin paneline erisim ve islemler veritabanindaki `roles`, `permissions` ve `role_permissions` tablolari uzerinden kontrol edilir.

Ilgili dosyalar:
- `sql/floria_schema.sql`
- `app/helpers/auth.php`
- `app/repositories/PermissionRepository.php`
- `admin/login.php`
- `admin/products.php`
- `admin/settings.php`
- `admin/menus.php`
- `admin/sliders.php`
- `admin/users.php`

## 8. Admin profil guncelleme

Admin paneline giren kullanici ad-soyad, e-posta, telefon ve sifre bilgilerini profil ekranindan guncelleyebilir.

Ilgili dosya:
- `admin/profile.php`

## 9. Oturum ve yetki kontrolu

Admin sayfalarinda aktif oturum kontrolu yapilir. Giris yapmayan kullanicilar login ekranina yonlendirilir. Yetkisi olmayan kullanicilar ana panele geri gonderilir ve log kaydi olusturulur.

Ilgili dosyalar:
- `app/helpers/auth.php`
- `admin/*`

## 10. Metinlerin, gorsellerin ve sayfa verilerinin veritabanindan cekilmesi

Public sitede urunler, kategoriler, slider gorselleri, menu linkleri ve site ayarlari veritabanindan cekilir. Eski statik HTML dosyalari `eski/` klasorunde referans olarak korunmustur.

Ilgili dosyalar:
- `public/index.php`
- `public/products.php`
- `public/product.php`
- `app/repositories/ProductRepository.php`
- `app/repositories/CategoryRepository.php`
- `app/repositories/SliderRepository.php`
- `app/repositories/MenuRepository.php`
- `app/repositories/SettingRepository.php`

## 11. Dinamik slider modulu

Slider basligi, aciklamasi, gorseli, buton metni, linki, aktiflik durumu ve sira numarasi admin panelinden yonetilebilir. Public ana sayfa aktif slider kayitlarini veritabanindan ceker.

Ilgili dosyalar:
- `admin/sliders.php`
- `public/index.php`
- `app/repositories/SliderRepository.php`

## 12. Dinamik navigasyon menusu

Menu isimleri, link adresleri, hedef bilgisi, aktiflik durumu ve sira numarasi admin panelinden yonetilebilir. Public header menuleri veritabanindan ceker.

Ilgili dosyalar:
- `admin/menus.php`
- `app/views/components/public_site_header.php`
- `app/repositories/MenuRepository.php`

## 13. Merkezi site ayarlari

Logo/metin, site basligi, slogan, iletisim bilgileri ve sosyal medya alanlari merkezi ayarlar ekranindan guncellenebilir.

Ilgili dosyalar:
- `admin/settings.php`
- `app/repositories/SettingRepository.php`
- `app/views/components/public_site_header.php`
- `app/views/components/public_site_footer.php`

## 14. Dashboard widgetlari

Admin ana sayfasinda toplam kullanici, urun, siparis, aktif slider ve log sayilari gibi sistem ozetleri gosterilir. Son log kayitlari da dashboard uzerinde listelenir.

Ilgili dosyalar:
- `admin/index.php`
- `app/repositories/DashboardRepository.php`
- `app/repositories/LogRepository.php`

## 15. Gelismis filtreleme ve siralama

Public urun sayfasinda ve admin urun yonetiminde arama, kategori, durum ve siralama filtreleri bulunur.

Ilgili dosyalar:
- `public/products.php`
- `admin/products.php`
- `app/repositories/ProductRepository.php`

## 16. Coklu secim ve toplu silme

Admin panelinde urun, menu ve slider kayitlari coklu secilerek toplu silinebilir.

Ilgili dosyalar:
- `admin/products.php`
- `admin/menus.php`
- `admin/sliders.php`

## 17. PDF ve CSV disa aktarma

Admin panelinde urunler, kullanicilar ve siparisler CSV ve PDF olarak disa aktarilabilir.

Ilgili dosyalar:
- `admin/export_products.php`
- `admin/export_products_pdf.php`
- `admin/export_users.php`
- `admin/export_users_pdf.php`
- `admin/export_orders.php`
- `admin/export_orders_pdf.php`
- `app/helpers/pdf.php`

## 18. Kritik hatalarin loglanmasi

Veritabani baglanti hatalari, yetkisiz erisim denemeleri, giris hatalari ve uygulama hatalari kullaniciya teknik detay gosterilmeden loglanir. Loglar hem dosyaya hem de uygun durumda veritabanindaki `logs` tablosuna yazilir.

Ilgili dosyalar:
- `app/helpers/logger.php`
- `app/config/database.php`
- `app/helpers/auth.php`
- `sql/floria_schema.sql`

Log dosyasi:
- `storage/logs/system.log`

## 19. Kullanici dostu uyari mesajlari

Kayit, giris, urun ekleme, toplu silme, ayar guncelleme, siparis olusturma ve hata durumlarinda kullaniciya anlasilir mesajlar gosterilir.

Ilgili dosyalar:
- `app/helpers/functions.php`
- `public/*`
- `admin/*`

## Calistirma bilgileri

PHP sunucusu XAMPP PHP ile baslatilabilir:

```bash
/Applications/XAMPP/xamppfiles/bin/php -S 127.0.0.1:8000
```

Public site:

```text
http://127.0.0.1:8000/public/index.php
```

Admin panel:

```text
http://127.0.0.1:8000/admin/login.php
```

Veritabani icin XAMPP Manager uzerinden MySQL Database servisinin calisir durumda olmasi gerekir.

## Teslim notu

Teslim icin `site/` klasoru, `sql/floria_schema.sql`, `sql/floria_product_seed.sql` ve bu rapor dosyasi birlikte arsivlenmelidir. Istenen adlandirma grup numarasina gore `grupno.rar` seklinde yapilmalidir.
