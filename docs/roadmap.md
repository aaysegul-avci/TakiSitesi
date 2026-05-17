# FLORIA Proje Yol Haritasi

## Faz 1: Temel Mimari
- Statik HTML dosyalarini bozmadan PHP iskeleti kur.
- `app/config`, `app/helpers`, `app/views` yapisini olustur.
- `public` ve `admin` alanlarini ayir.
- UTF-8, session ve loglama altyapisini tanimla.

## Faz 2: Veritabani ve Guvenlik
- Normalize tablo yapisini SQL dosyasi ile kur.
- Tum sorgulari `PDO Prepared Statements` ile yaz.
- Sifreleme icin `password_hash` ve `password_verify` kullan.
- `roles`, `permissions` ve `user_roles` mantigini devreye al.

## Faz 3: Admin Paneli
- Admin login, logout, session kontrolu.
- Dashboard widgetlari.
- Profil guncelleme.
- Urun, slider, menu ve ayarlar modulleri.

## Faz 4: Public Site Dinamiklestirme
- Anasayfa slider verilerini veritabanindan cek.
- Menuleri dinamik tabloyla yonet.
- Urunleri, sayfa metinlerini ve ayarlari veritabanina bagla.
- Sepet ve siparis akislarini veritabanina tasima.

## Faz 5: Ekstra Notlandirma Maddeleri
- Liste ekranlarinda filtreleme ve siralama.
- Coklu secim ve toplu silme.
- CSV/PDF disa aktarma.
- Hata loglama ve dostu bildirim mesajlari.
