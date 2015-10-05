# İF Yazılım / Dosya Yükleme

PHP ile dosya yükleme işinden sorumlu kütüphanedir. Form üzerinden gelen $_FILES bilgisini dikkate alarak çalışır.

## Örnek HTML Form

```html
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="dosya" />
    <input type="submit" value="Yükle" />
</form>
```

Form gönderildiğinde yani submit edildiğinde, PHP tarafında dosyanın yüklenmesi için gerekli işlemler yapılır.

```php
<?php

// yeni bir dosya yükleme oluşturalım
$dosyaYukleme = new \IfYazilim\DosyaYukleme\DosyaYukleme('resim');

// dosya yüklenmiş mi
if ($dosyaYukleme->count() === 0)
    throw new Exception('Manşet resmi girilmelidir.');
    
// yükleme sırasın hata oluşmuş ise
if ( ! empty($dosyaYukleme->getHatalar()))
    throw new Exception('Dosya yükleme sırasında hata meydana geldi. Hata açıklaması: ' . implode(', ', $dosyaYukleme->getHatalar()));

// yüklenen dosyayı alalım, eğer birden fazla dosya yüklendiyse, $dosyaYukleme->getIterator() kullanılabilir
$dosyaBilgisi = $dosyaYukleme->offsetGet(0);

// yüklenen dosya boyutu en fazla 1M olabilir
if ($dosyaBilgisi->getSize() > 1024 * 1024)
    throw new Exception('Manşet resmi en fazla 1M olabilir.');
    
// yüklenen dosyanın tam adı, ör: merhaba.jpg
echo $dosyaBilgisi->getTamAdi();

// yüklenen dosyanın boyutu
echo $dosyaBilgisi->getSize();

// yüklenen dosyanın mimetype'ı
echo $dosyaBilgisi->getTip();

// yüklenen dosyanın uzantısı
echo $dosyaBilgisi->getUzanti();
```