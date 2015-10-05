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