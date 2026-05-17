# Kurulum Rehberi

## 1. Veritabani
- MySQL icinde `sql/floria_schema.sql` dosyasini import edin.
- Veritabani adi varsayilan olarak `floria_db` olarak olusur.

## 2. Uygulama Ayarlari
- `app/config/config.php` icinde:
  - host
  - port
  - database
  - username
  - password
  alanlarini kendi local ortamınıza gore guncelleyin.

## 3. PHP Sunucusu
- Ornek olarak proje kok dizininde su komut kullanilabilir:

```bash
php -S localhost:8000
```

## 4. Acilacak Adresler
- Public ana sayfa: `http://localhost:8000/public/index.php`
- Public urunler: `http://localhost:8000/public/products.php`
- Admin setup: `http://localhost:8000/admin/setup.php`
- Admin giris: `http://localhost:8000/admin/login.php`

## 5. Test Senaryolari
- Super Admin olustur
- Admin login ol
- Urun ekle
- Slider ekle
- Menu ekle
- Ayar guncelle
- Public index sayfasinda bu verilerin yansidigini kontrol et
- Public register/login test et
- Urun sepet/checkout akisini test et
- Siparisin `orders` ve `order_items` tablolarina yazildigini kontrol et
