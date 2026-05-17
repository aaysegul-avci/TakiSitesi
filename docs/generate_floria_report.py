from __future__ import annotations

import html
import os
import zipfile
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
OUT = Path("/Users/mavlyudaalmazova/Downloads/FLORIA_Rapor_Dolu.docx")
SCREENSHOTS = ROOT / "docs" / "screenshots"


def esc(value: str) -> str:
    return html.escape(value, quote=False)


def run(text: str, bold: bool = False, size: int = 22) -> str:
    props = f"<w:rPr>{'<w:b/>' if bold else ''}<w:sz w:val=\"{size}\"/></w:rPr>"
    return f"<w:r>{props}<w:t xml:space=\"preserve\">{esc(text)}</w:t></w:r>"


def paragraph(text: str = "", bold: bool = False, size: int = 22, align: str | None = None) -> str:
    ppr = ""
    if align:
        ppr = f"<w:pPr><w:jc w:val=\"{align}\"/></w:pPr>"
    return f"<w:p>{ppr}{run(text, bold, size)}</w:p>"


def page_break() -> str:
    return '<w:p><w:r><w:br w:type="page"/></w:r></w:p>'


def heading(text: str, level: int = 1) -> str:
    size = 34 if level == 1 else 28 if level == 2 else 24
    return paragraph(text, bold=True, size=size)


def bullet(text: str) -> str:
    return paragraph("• " + text, size=22)


def cell(text: str, bold: bool = False) -> str:
    return (
        "<w:tc><w:tcPr><w:tcW w:w=\"3000\" w:type=\"dxa\"/></w:tcPr>"
        + paragraph(text, bold=bold, size=20)
        + "</w:tc>"
    )


def table(rows: list[list[str]]) -> str:
    xml = ["<w:tbl><w:tblPr><w:tblW w:w=\"0\" w:type=\"auto\"/><w:tblBorders>"]
    for border in ("top", "left", "bottom", "right", "insideH", "insideV"):
        xml.append(f"<w:{border} w:val=\"single\" w:sz=\"4\" w:space=\"0\" w:color=\"B8A6B0\"/>")
    xml.append("</w:tblBorders></w:tblPr>")
    for row_index, row in enumerate(rows):
        xml.append("<w:tr>")
        for value in row:
            xml.append(cell(value, bold=row_index == 0))
        xml.append("</w:tr>")
    xml.append("</w:tbl>")
    return "".join(xml)


def image_paragraph(rel_id: str, width_px: int = 1440, height_px: int = 1000) -> str:
    width_emu = 6_200_000
    height_emu = int(width_emu * height_px / width_px)
    return f"""
<w:p>
  <w:r>
    <w:drawing>
      <wp:inline distT="0" distB="0" distL="0" distR="0">
        <wp:extent cx="{width_emu}" cy="{height_emu}"/>
        <wp:effectExtent l="0" t="0" r="0" b="0"/>
        <wp:docPr id="{rel_id[3:]}" name="Screenshot {rel_id}"/>
        <wp:cNvGraphicFramePr><a:graphicFrameLocks noChangeAspect="1"/></wp:cNvGraphicFramePr>
        <a:graphic>
          <a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">
            <pic:pic>
              <pic:nvPicPr>
                <pic:cNvPr id="{rel_id[3:]}" name="{rel_id}.png"/>
                <pic:cNvPicPr/>
              </pic:nvPicPr>
              <pic:blipFill>
                <a:blip r:embed="{rel_id}"/>
                <a:stretch><a:fillRect/></a:stretch>
              </pic:blipFill>
              <pic:spPr>
                <a:xfrm><a:off x="0" y="0"/><a:ext cx="{width_emu}" cy="{height_emu}"/></a:xfrm>
                <a:prstGeom prst="rect"><a:avLst/></a:prstGeom>
              </pic:spPr>
            </pic:pic>
          </a:graphicData>
        </a:graphic>
      </wp:inline>
    </w:drawing>
  </w:r>
</w:p>
"""


def caption(text: str) -> str:
    return paragraph(text, bold=True, size=20, align="center")


criteria_rows = [
    ["Madde", "Projede Karşılığı", "İlgili Dosyalar"],
    ["Responsive tasarım", "Public site ve admin paneli mobil, tablet ve masaüstü ekranlara uyumlu hazırlandı.", "styles.css, public/*, admin/*"],
    ["Normalize veritabanı ve foreign key", "Roller, yetkiler, kullanıcılar, ürünler, kategoriler, siparişler, menüler, sliderlar, ayarlar ve loglar ilişkili tablolarla kuruldu.", "sql/floria_schema.sql"],
    ["Prepared Statements", "Veritabanı işlemleri PDO prepared statements ile yazıldı.", "app/repositories/*"],
    ["Türkçe karakter desteği", "utf8mb4, UTF-8 meta etiketleri ve htmlspecialchars kullanıldı.", "sql/floria_schema.sql, app/views/layouts/*"],
    ["Şifre hashleme", "Şifreler password_hash ile saklanır, password_verify ile doğrulanır.", "public/register.php, public/login.php, admin/login.php"],
    ["Modüler mimari", "Config, helper, repository, layout ve component dosyaları ayrıldı.", "app/config, app/helpers, app/repositories, app/views"],
    ["Rol ve yetki sistemi", "Super Admin, Editor, Moderator ve Customer rolleri permission tablosuyla kontrol edilir.", "roles, permissions, role_permissions, app/helpers/auth.php"],
    ["Admin profil güncelleme", "Admin kendi bilgilerini ve şifresini güncelleyebilir.", "admin/profile.php"],
    ["Oturum kontrolü", "Admin sayfalarında login ve yetki kontrolü yapılır.", "app/helpers/auth.php, admin/*"],
    ["Veritabanından içerik", "Ürün, kategori, slider, menü ve ayarlar veritabanından çekilir.", "public/index.php, public/products.php, repositories"],
    ["Dinamik slider", "Slider başlığı, açıklaması, görseli, linki ve sırası panelden yönetilir.", "admin/sliders.php"],
    ["Dinamik menü", "Menü başlıkları, linkleri ve sıraları panelden yönetilir.", "admin/menus.php"],
    ["Merkezi ayarlar", "Site başlığı, slogan, iletişim ve sosyal medya bilgileri panelden değişir.", "admin/settings.php"],
    ["Dashboard", "Toplam kullanıcı, ürün, sipariş, slider ve log sayıları gösterilir.", "admin/index.php"],
    ["Filtreleme/sıralama", "Ürün listeleme ekranlarında arama, kategori, durum ve sıralama vardır.", "public/products.php, admin/products.php"],
    ["Toplu silme", "Ürün, menü ve slider kayıtlarında çoklu seçim ile toplu silme yapılır.", "admin/products.php, admin/menus.php, admin/sliders.php"],
    ["PDF/CSV export", "Ürün, kullanıcı ve sipariş verileri CSV/PDF olarak dışa aktarılır.", "admin/export_*.php, app/helpers/pdf.php"],
    ["Loglama", "Kritik hatalar dosyaya ve logs tablosuna kaydedilir.", "app/helpers/logger.php, storage/logs/system.log"],
    ["Kullanıcı mesajları", "Kayıt, giriş, silme, sipariş ve hata durumlarında anlaşılır mesajlar gösterilir.", "public/*, admin/*"],
]

app_shots = [
    ("Ana Sayfa", "01-public-home.png"),
    ("Ürün Listeleme ve Filtreleme", "02-public-products.png"),
    ("Ürün Detay Sayfası", "03-public-product-detail.png"),
    ("Kullanıcı Giriş Sayfası", "04-public-login.png"),
    ("Kullanıcı Kayıt Sayfası", "05-public-register.png"),
    ("Sepet Sayfası", "06-public-cart.png"),
    ("Admin Giriş Sayfası", "07-admin-login.png"),
    ("Admin Dashboard", "08-admin-dashboard.png"),
    ("Admin Ürün Yönetimi", "09-admin-products.png"),
    ("Admin Slider Yönetimi", "10-admin-sliders.png"),
    ("Admin Merkezi Ayarlar", "11-admin-settings.png"),
]

diagram_shots = [
    ("Şekil 1. Sistem aktörlerinin use-case diyagramı", "12-use-case-diagram.png", 1200, 760),
    ("Şekil 2. Proje Gantt Chart", "13-gantt-chart.png", 1200, 650),
    ("Şekil 3. Proje Sistem Mimarisi", "14-system-architecture.png", 1200, 720),
    ("Şekil 4. Proje E/R Diyagramı", "15-er-diagram.png", 1400, 900),
    ("Şekil 5. İlişkisel Veritabanı Diyagramı", "16-relational-diagram.png", 1400, 850),
]

data_type_rows = [
    ["Veri Tipi", "Anlamı", "Projede Kullanım Örneği"],
    ["Integer", "Tam sayı değerler", "id, role_id, category_id, stock, sort_order"],
    ["String / Varchar", "Metinsel kısa veriler", "name, email, slug, title, url"],
    ["Text / LongText", "Uzun metin alanları", "description, address, setting_value"],
    ["Decimal", "Ondalıklı sayısal değerler", "price, total_amount, line_total"],
    ["Boolean / TinyInt", "Aktif/pasif ve doğru/yanlış bilgisi", "is_active"],
    ["DateTime / Timestamp", "Tarih ve saat bilgileri", "created_at, updated_at, last_login_at"],
    ["JSON", "Yapılandırılmış log içeriği", "logs.context"],
]

db_entity_rows = [
    ["Tablo", "Temel Alanlar", "Açıklama"],
    ["roles", "id, name, slug", "Kullanıcı rollerini tutar."],
    ["permissions", "id, name, slug", "Sistem yetkilerini tutar."],
    ["role_permissions", "role_id, permission_id", "Roller ve yetkiler arasındaki ilişkiyi kurar."],
    ["users", "id, role_id, name, email, password_hash", "Müşteri ve admin kullanıcılarını tutar."],
    ["categories", "id, name, slug", "Ürün kategorilerini tutar."],
    ["products", "id, category_id, name, price, stock", "Ürün bilgilerini tutar."],
    ["orders", "id, user_id, order_number, total_amount", "Sipariş üst bilgilerini tutar."],
    ["order_items", "id, order_id, product_id, quantity", "Sipariş kalemlerini tutar."],
    ["menus", "id, title, url, sort_order", "Dinamik navigasyon menüsünü tutar."],
    ["sliders", "id, title, image_path, sort_order", "Ana sayfa slider kayıtlarını tutar."],
    ["settings", "id, setting_key, setting_value", "Merkezi site ayarlarını tutar."],
    ["logs", "id, log_type, ip_address, message", "Sistem loglarını tutar."],
]

use_case_rows = [
    ["Use-Case", "Aktör", "Senaryo"],
    ["Ürünleri Görüntüle", "Müşteri", "Müşteri ürünler sayfasını açar, kategori veya arama filtresi kullanır ve ürünleri listeler."],
    ["Sepete Ekle", "Müşteri", "Müşteri ürün detayından sepete ekleme işlemi yapar ve sepet sayfasında ürünleri kontrol eder."],
    ["Sipariş Oluştur", "Müşteri", "Giriş yapan müşteri teslimat ve ödeme bilgilerini girerek sipariş oluşturur."],
    ["Sipariş Takip Et", "Müşteri", "Müşteri hesabındaki siparişlerim sayfasından sipariş durumunu görüntüler."],
    ["Ürün Yönet", "Admin/Editör", "Yetkili kullanıcı ürün ekler, filtreler ve toplu silme işlemi yapar."],
    ["Slider ve Menü Yönet", "Admin/Editör", "Yetkili kullanıcı ana sayfa sliderlarını ve menü bağlantılarını panelden düzenler."],
    ["Sipariş Yönet", "Admin", "Yetkili kullanıcı gelen siparişleri listeler ve sipariş durumunu günceller."],
    ["Rapor Dışa Aktar", "Admin", "Yetkili kullanıcı ürün, kullanıcı ve sipariş verilerini PDF veya CSV olarak indirir."],
]

body: list[str] = []
body.append(paragraph("T. C.", bold=True, size=28, align="center"))
body.append(paragraph("KIRKLARELİ ÜNİVERSİTESİ", bold=True, size=28, align="center"))
body.append(paragraph("MÜHENDİSLİK FAKÜLTESİ", bold=True, size=28, align="center"))
body.append(paragraph("YAZILIM MÜHENDİSLİĞİ BÖLÜMÜ", bold=True, size=28, align="center"))
body.append(paragraph("", size=22))
body.append(paragraph("FLORIA TAKI E-TİCARET SİTESİ", bold=True, size=36, align="center"))
body.append(paragraph("YAZ16204 WEB PROGRAMLAMA – II", bold=True, size=28, align="center"))
body.append(paragraph("", size=22))
body.append(paragraph("Proje Üyeleri:", bold=True, size=24, align="center"))
body.append(paragraph("1. Mavlyuda Almazova", size=24, align="center"))
body.append(paragraph("2. ................................", size=24, align="center"))
body.append(paragraph("3. ................................", size=24, align="center"))
body.append(paragraph("", size=22))
body.append(paragraph("KIRKLARELİ, 2026", bold=True, size=24, align="center"))
body.append(page_break())

body.append(heading("ÖZET", 2))
body.append(paragraph("Bu proje kapsamında PHP, MySQL ve PDO kullanılarak dinamik bir takı e-ticaret sitesi geliştirilmiştir. Çalışmada kullanıcı arayüzü, admin paneli, ürün listeleme, sepet, sipariş takibi, rol-yetki yönetimi, dinamik slider, menü yönetimi, merkezi ayarlar, raporlama ve loglama özellikleri uygulanmıştır. Proje sayesinde veritabanı ilişkileri, güvenli sorgu kullanımı, şifre hashleme, session yönetimi ve modüler PHP mimarisi konularında uygulamalı deneyim kazanılmıştır."))
body.append(paragraph("Anahtar Kelimeler: PHP, MySQL, PDO, E-Ticaret, Admin Panel, Responsive Tasarım", bold=True))
body.append(heading("ABSTRACT", 2))
body.append(paragraph("In this project, a dynamic jewelry e-commerce website was developed using PHP, MySQL and PDO. The system includes a public user interface, an admin panel, product listing, cart, order tracking, role-based authorization, dynamic slider, menu management, central settings, reporting and logging features. The project provided practical experience in database relations, secure queries, password hashing, session management and modular PHP architecture."))
body.append(paragraph("Keywords: PHP, MySQL, PDO, E-Commerce, Admin Panel, Responsive Design", bold=True))
body.append(page_break())

body.append(heading("İÇİNDEKİLER", 2))
for item in [
    "1. Giriş",
    "2. Proje Gereksinimleri",
    "3. Proje Analizi",
    "4. Proje Tasarımı",
    "5. Hocanın İstediği Maddelerin Karşılanması",
    "6. Site ve Admin Panel Ekran Görüntüleri",
    "7. Sonuç",
]:
    body.append(paragraph(item))
body.append(heading("ŞEKİLLER LİSTESİ", 2))
figure_names = [item[0] for item in diagram_shots] + [
    f"Şekil {index}. {caption_text}" for index, (caption_text, _) in enumerate(app_shots, start=len(diagram_shots) + 1)
]
for item in figure_names:
    body.append(paragraph(item))
body.append(heading("TABLOLAR LİSTESİ", 2))
for item in [
    "Tablo 1. Proje Veri Tipleri",
    "Tablo 2. Veritabanı Varlık Tipleri",
    "Tablo 3. Use-Case ve Açıklamaları",
    "Tablo 4. Hocanın İstediği Maddelerin Karşılanması",
]:
    body.append(paragraph(item))
body.append(heading("KISALTMALAR", 2))
for item in ["PHP: Hypertext Preprocessor", "PDO: PHP Data Objects", "SQL: Structured Query Language", "CSV: Comma Separated Values", "PDF: Portable Document Format"]:
    body.append(paragraph(item))
body.append(page_break())

body.append(heading("1. Giriş", 2))
body.append(paragraph("FLORIA projesi, PHP, MySQL ve PDO kullanılarak geliştirilen dinamik bir takı e-ticaret sitesidir. Projede kullanıcı arayüzü, üyelik sistemi, ürün listeleme, sepet, sipariş, admin paneli, rol-yetki sistemi, slider yönetimi, menü yönetimi, ayarlar yönetimi, raporlama ve loglama modülleri bulunmaktadır."))
body.append(heading("2. Proje Gereksinimleri", 2))
body.append(paragraph("Projenin temel amacı, müşterilerin ürünleri görüntüleyebildiği ve sipariş oluşturabildiği; yöneticilerin ise ürün, slider, menü, kullanıcı, sipariş ve site ayarlarını panel üzerinden yönetebildiği dinamik bir web sitesi geliştirmektir."))
body.append(heading("2.1. Pazar Araştırması", 3))
body.append(paragraph("Takı ve aksesuar ürünleri internet üzerinden görselliği güçlü şekilde sunulabilen ürün grupları arasında yer alır. Bu nedenle FLORIA projesinde hedef müşteri kitlesi; ürünleri kategoriye göre incelemek, fiyat karşılaştırması yapmak, ürün detaylarını görmek ve hızlıca sipariş oluşturmak isteyen kullanıcılardır. Pazar araştırması sonucunda kullanıcıların sade arayüz, mobil uyumluluk, hızlı ürün erişimi, güvenli üyelik, sepet ve sipariş takibi gibi beklentilere sahip olduğu görülmüştür. Admin tarafında ise ürün, slider, menü, sipariş ve site ayarlarının kod yazmadan yönetilebilmesi temel ihtiyaç olarak belirlenmiştir."))
body.append(paragraph("Proje kapsamında müşteriye sunulan değer; takı ürünlerinin görsel ağırlıklı, düzenli ve kolay gezilebilir bir e-ticaret yapısı içinde listelenmesidir. Ürünler kategori, fiyat ve ad bilgilerine göre filtrelenebilir. Yönetici paneli sayesinde ürün içerikleri, ana sayfa slider görselleri, menü bağlantıları ve site ayarları güncel tutulabilir."))
body.append(heading("2.2. Kullanılan Programlama Dili, Veritabanı ve Teknolojiler", 3))
for item in ["Programlama dili: PHP 8", "Veritabanı: MySQL / MariaDB", "Veritabanı erişimi: PDO ve Prepared Statements", "Arayüz teknolojileri: HTML5, CSS3 ve JavaScript", "Server/çalıştırma ortamı: XAMPP PHP geliştirme sunucusu ve XAMPP MySQL servisi", "Tasarım yaklaşımı: Responsive kullanıcı arayüzü ve admin paneli", "Raporlama: CSV ve PDF dışa aktarma", "Loglama: Dosya ve veritabanı tabanlı log sistemi"]:
    body.append(bullet(item))
body.append(heading("2.3. Fonksiyonel Gereksinimler", 3))
for item in ["Kullanıcı kayıt ve giriş işlemleri", "Ürün listeleme, filtreleme ve detay görüntüleme", "Sepet ve sipariş oluşturma", "Admin panelinde ürün, slider, menü ve ayar yönetimi", "Rol ve yetki tabanlı erişim kontrolü", "PDF ve CSV rapor dışa aktarma"]:
    body.append(bullet(item))
body.append(heading("2.4. Fonksiyonel Olmayan Gereksinimler", 3))
for item in ["Responsive tasarım", "Prepared statements ile güvenli veritabanı işlemleri", "Şifrelerin hashlenmiş saklanması", "UTF-8/Türkçe karakter desteği", "Kritik hataların loglanması"]:
    body.append(bullet(item))

body.append(heading("3. Proje Analizi", 2))
body.append(paragraph("Sistemde müşteri, süper admin, editör ve moderatör olmak üzere farklı kullanıcı rolleri bulunur. Müşteri public site üzerinden ürünleri inceler, sepete ekler ve sipariş oluşturur. Admin rolleri ise yetkilerine göre yönetim panelindeki modüllere erişir."))
body.append(heading("3.1. Veri Sözlüğü", 3))
body.append(heading("3.1.1. Proje Kullanıcıları", 3))
for item in ["Müşteri: Public site üzerinden ürünleri görüntüler, kayıt olur, giriş yapar, sepete ürün ekler ve sipariş verir.", "Super Admin: Tüm admin paneli modüllerini yönetir, kullanıcı ve ayar işlemlerini yapar.", "Editör: Ürün, slider ve menü gibi içerik yönetimi işlemlerini yapabilir.", "Moderatör: Yetki verilen yönetim ekranlarını görüntüler ve takip eder."]:
    body.append(bullet(item))
body.append(heading("3.1.2. Proje Veri Tipleri", 3))
body.append(table(data_type_rows))
body.append(heading("3.1.3. Proje Veritabanı Tipleri", 3))
body.append(table(db_entity_rows))
for item in ["users: Kullanıcı ve admin hesapları", "roles: Kullanıcı rolleri", "permissions: Sistem yetkileri", "categories: Ürün kategorileri", "products: Ürün kayıtları", "orders ve order_items: Sipariş ve sipariş kalemleri", "menus: Dinamik navigasyon menüsü", "sliders: Ana sayfa slider kayıtları", "settings: Merkezi site ayarları", "logs: Sistem logları"]:
    body.append(bullet(item))
body.append(heading("3.2. Use-Case Açıklamaları", 3))
for item in ["Müşteri ürünleri listeler, ürün detayını görüntüler, sepete ekler ve sipariş verir.", "Süper Admin tüm admin modüllerini yönetir.", "Editör ürün, menü ve slider gibi içerik modüllerinde işlem yapabilir.", "Moderatör yetkili olduğu kullanıcı veya dashboard ekranlarını görüntüleyebilir."]:
    body.append(bullet(item))
body.append(heading("3.2.1. Use-Case ve Açıklamaları", 3))
body.append(table(use_case_rows))
body.append(heading("3.3. Use-Case Diyagramları ve Senaryoları", 3))
body.append(paragraph("Aşağıdaki use-case diyagramında müşteri ve admin aktörlerinin sistemde gerçekleştirdiği temel işlemler gösterilmiştir. Müşteri tarafında ürün görüntüleme, sepete ekleme, sipariş oluşturma ve sipariş takibi işlemleri; admin tarafında dashboard görüntüleme, ürün yönetimi, slider/menü yönetimi, sipariş yönetimi ve rapor dışa aktarma işlemleri yer almaktadır."))
body.append(image_paragraph("rId12", 1200, 760))
body.append(caption("Şekil 1. Sistem aktörlerinin use-case diyagramı"))
body.append(heading("3.4. Yazılım Proje Yönetimi Planı", 3))
body.append(paragraph("Proje süreci gereksinim analizi, veritabanı tasarımı, public site geliştirme, admin panel geliştirme, test/hata düzeltme ve raporlama adımlarından oluşmuştur. İş planı dört haftalık bir geliştirme sürecine göre düzenlenmiştir."))
body.append(image_paragraph("rId13", 1200, 650))
body.append(caption("Şekil 2. Proje Gantt Chart"))

body.append(heading("4. Proje Tasarımı", 2))
body.append(paragraph("Proje modüler PHP mimarisiyle hazırlanmıştır. Veritabanı bağlantısı, yardımcı fonksiyonlar, repository dosyaları, layout dosyaları ve component yapıları birbirinden ayrılmıştır. Public site ve admin panel aynı uygulama içinde farklı klasörlerle organize edilmiştir."))
body.append(heading("4.1. Sistem Mimarisi", 3))
for item in ["public/: Kullanıcı tarafı sayfalar", "admin/: Yönetim paneli sayfaları", "app/config/: Uygulama ve veritabanı ayarları", "app/helpers/: Auth, log ve genel yardımcı fonksiyonlar", "app/repositories/: PDO sorgularının bulunduğu veri erişim katmanı", "app/views/: Ortak layout ve component dosyaları"]:
    body.append(bullet(item))
body.append(image_paragraph("rId14", 1200, 720))
body.append(caption("Şekil 3. Proje Sistem Mimarisi"))
body.append(paragraph("Sistem mimarisinde tarayıcı üzerinden gelen kullanıcı ve admin istekleri PHP uygulama katmanında işlenir. Public sayfalar müşteri tarafı işlemlerini, admin sayfaları ise yönetim paneli işlemlerini yürütür. Repository katmanı PDO prepared statements ile MySQL veritabanına erişir. Kritik hatalar storage/logs klasöründeki log dosyasına ve uygun durumda logs tablosuna kaydedilir."))
body.append(heading("4.2. Veritabanı Mimarisi", 3))
body.append(paragraph("Veritabanı tasarımı tekrar eden verileri azaltacak şekilde normalize edilmiştir. Kullanıcılar rollere, ürünler kategorilere, sipariş kalemleri siparişlere ve ürünlere foreign key ile bağlanır. Rol-yetki ilişkisi role_permissions ara tablosuyla kurulmuştur."))
body.append(image_paragraph("rId15", 1400, 900))
body.append(caption("Şekil 4. Proje E/R Diyagramı"))
body.append(paragraph("E/R diyagramında users tablosu roles tablosuna bağlıdır. roles ve permissions tabloları arasında role_permissions ara tablosu bulunur. products tablosu categories tablosuna, orders tablosu users tablosuna, order_items tablosu ise orders ve products tablolarına foreign key ile bağlanır. Menü, slider, settings ve logs tabloları sistemin dinamik içerik ve kayıt altyapısını destekler."))
body.append(image_paragraph("rId16", 1400, 850))
body.append(caption("Şekil 5. İlişkisel Veritabanı Diyagramı"))

body.append(heading("5. Hocanın İstediği Maddelerin Karşılanması", 2))
body.append(table(criteria_rows))
body.append(heading("6. Site ve Admin Panel Ekran Görüntüleri", 2))

relationships = [
    ("rId12", "12-use-case-diagram.png"),
    ("rId13", "13-gantt-chart.png"),
    ("rId14", "14-system-architecture.png"),
    ("rId15", "15-er-diagram.png"),
    ("rId16", "16-relational-diagram.png"),
]
for index, (caption_text, filename) in enumerate(app_shots, start=17):
    rel_id = f"rId{index}"
    relationships.append((rel_id, filename))
    body.append(heading(caption_text, 3))
    body.append(image_paragraph(rel_id))

body.append(heading("7. Sonuç", 2))
body.append(paragraph("FLORIA projesi, Web Programlama 2 proje kriterlerinde istenen kullanıcı arayüzü, admin paneli, veritabanı ilişkileri, güvenli sorgu yapısı, şifre hashleme, rol-yetki kontrolü, dinamik içerik yönetimi, raporlama, loglama ve kullanıcı bilgilendirme özelliklerini içerecek şekilde tamamlanmıştır."))

document_xml = f"""<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document
 xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
 xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"
 xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"
 xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"
 xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">
<w:body>
{''.join(body)}
<w:sectPr>
  <w:pgSz w:w="11906" w:h="16838"/>
  <w:pgMar w:top="900" w:right="900" w:bottom="900" w:left="900" w:header="720" w:footer="720" w:gutter="0"/>
</w:sectPr>
</w:body>
</w:document>
"""

rels_xml = """<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
""" + "\n".join(
    f'<Relationship Id="{rel_id}" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/{filename}"/>'
    for rel_id, filename in relationships
) + "\n</Relationships>"

content_types_xml = """<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Default Extension="png" ContentType="image/png"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
</Types>
"""

root_rels_xml = """<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>
"""

core_xml = """<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"
 xmlns:dc="http://purl.org/dc/elements/1.1/"
 xmlns:dcterms="http://purl.org/dc/terms/"
 xmlns:dcmitype="http://purl.org/dc/dcmitype/"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <dc:title>FLORIA Web Programlama 2 Proje Raporu</dc:title>
  <dc:creator>FLORIA Proje Grubu</dc:creator>
</cp:coreProperties>
"""

app_xml = """<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"
 xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
  <Application>Codex</Application>
</Properties>
"""

OUT.parent.mkdir(parents=True, exist_ok=True)
if OUT.exists():
    OUT.unlink()

with zipfile.ZipFile(OUT, "w", zipfile.ZIP_DEFLATED) as docx:
    docx.writestr("[Content_Types].xml", content_types_xml)
    docx.writestr("_rels/.rels", root_rels_xml)
    docx.writestr("docProps/core.xml", core_xml)
    docx.writestr("docProps/app.xml", app_xml)
    docx.writestr("word/document.xml", document_xml)
    docx.writestr("word/_rels/document.xml.rels", rels_xml)
    for _, filename in relationships:
        docx.write(SCREENSHOTS / filename, f"word/media/{filename}")

print(OUT)
