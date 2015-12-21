<?php namespace IfYazilim\DosyaYukleme;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\Util;

class DosyaYukleme implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Dosya yükleme sırasında oluşabilecek hata kodları.
     *
     * @var array
     */
    protected static $hataMesajlari = [
        1 => 'Yüklenen dosya boyutu php.ini içinde yer alan upload_max_filesize ayarını aştı',
        2 => 'Yüklenen dosya boyutu HTML formu içinde belirtilen MAX_FILE_SIZE ayarını aştı',
        3 => 'Yüklenen dosyanın bir kısmı yüklenebildi',
        4 => 'Her hangi bir dosya yüklenmemiş',
        6 => 'Geçici yükleme klasörü tanımlanmamış',
        7 => 'Yüklenen dosya disk üzerinde kaydedilemedi',
        8 => 'Bir PHP uzantısı (extension) dosyanın yüklenmesini durdurdu'
    ];

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var DosyaBilgisi[]
     */
    protected $dosyaBilgileri = [];

    /**
     * @var
     */
    protected $hatalar = [];

    /**
     * @param string $adi $_FILES['$adi'] kısmındaki bilgi
     * @param AdapterInterface|null $adapter
     */
    public function __construct($adi, AdapterInterface $adapter = null)
    {
        // dosya yüklemelerine izin veriliyor mu?
        if (ini_get('file_uploads') == false)
            throw new \RuntimeException('Dosya yükleme işlemleri PHP.ini dosyasından pasif yapılmış');

        // dosya gerçekten varsa işlem yapacağız
        if (isset($_FILES[$adi])) {

            // dosya bilgilerini elde edeceğiz

            // birden fazla dosya yüklenmek mi istenmiş?
            if (is_array($_FILES[$adi]['tmp_name']) === true) {

                // yüklenmek istenen dosyalar üzerinde dönelim
                foreach ($_FILES[$adi]['tmp_name'] as $index => $tmpName) {

                    // eğer dosya yüklenmeden boş geçilmediyse
                    if ($_FILES[$adi]['error'][$index] !== UPLOAD_ERR_NO_FILE) {

                        // aktif dosyanın yüklenmesinde sorun var mı?
                        if ($_FILES[$adi]['error'][$index] !== UPLOAD_ERR_OK) {

                            // hatalara yeni kayıt ekleyelim
                            $this->hatalar[] = sprintf(
                                '%s: %s',
                                $_FILES[$adi]['name'][$index],
                                static::$hataMesajlari[$_FILES[$adi]['error'][$index]]
                            );

                            // sonraki foreach ile devam edelim
                            continue;
                        }

                        // dosya bilgisini saklayalım
                        $this->dosyaBilgileri[] = new DosyaBilgisi(
                            $_FILES[$adi]['tmp_name'][$index],
                            $_FILES[$adi]['name'][$index]);
                    }
                }

            } else {

                // tek bir dosya yüklenmek istenmiş

                // eğer dosya yüklenmeden boş geçilmediyse
                if ($_FILES[$adi]['error'] !== UPLOAD_ERR_NO_FILE) {

                    // yüklenen dosya ile ilgili bir problem var mı?
                    if ($_FILES[$adi]['error'] !== UPLOAD_ERR_OK) {

                        // evet, o halde hatalara yeni kayıt ekleyelim
                        $this->hatalar[] = sprintf(
                            '%s: %s',
                            $_FILES[$adi]['name'],
                            static::$hataMesajlari[$_FILES[$adi]['error']]
                        );
                    }

                    // dosya bilgisini saklayalım
                    $this->dosyaBilgileri[] = new DosyaBilgisi(
                        $_FILES[$adi]['tmp_name'],
                        $_FILES[$adi]['name']);
                }
            }

            // adaptörü set edelim
            $this->adapter = $adapter;
        }
    }

    /**
     * @param AdapterInterface $adapter
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Yüklenmek istenen dosyaların yüklenmesi konusunda bir sorun var mı?
     *
     * @return bool
     */
    public function isYuklemeTamam()
    {
        // dosya bilgileri üzerinde dönelim
        foreach ($this->dosyaBilgileri as $dosyaBilgisi) {

            // dosya yüklenmiş mi?
            if ( ! $dosyaBilgisi->isDosyaYuklendi()) {

                // hatalara yeni bir kayıt ekleyelim
                $this->hatalar[] = sprintf(
                    '%s: %s',
                    $dosyaBilgisi->getTamAdi(),
                    'Dosya yüklenmemiş.'
                );
            }
        }

        // hata var mı yok mu?
        return empty($this->hatalar);
    }

    /**
     * @return array
     */
    public function getHatalar()
    {
        return $this->hatalar;
    }

    /**
     * İstenilen bir yere yükleme yapar.
     *
     * @param string $yol
     * @return bool
     */
    public function yukle($yol)
    {
        // dosyaların yüklenmesi tamam mı?
        if ( ! $this->isYuklemeTamam())
            throw new \RuntimeException('Dosyaların yüklenmesi ile ilgili bir sorun var.');

        // adaptör set edilmiş olmalı
        if ( ! $this->adapter instanceof AdapterInterface)
            throw new \RuntimeException('Yükleme yapmak için adaptör tanımlanmamış.');

        // dosyalar üzerinde dönelim
        foreach ($this->dosyaBilgileri as $dosyaBilgisi) {

            // dosyayı yükleyelim
            $this->adapter->write($yol, file_get_contents($dosyaBilgisi->getPathname()), new Config());
        }

        // başarılı bir dönüş yapalım
        return true;
    }

    // ArrayAccess Interface

    public function offsetExists($offset)
    {
        return isset($this->dosyaBilgileri[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->dosyaBilgileri[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->dosyaBilgileri[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->dosyaBilgileri[$offset]);
    }

    // IteratorAggregate Interface

    public function getIterator()
    {
        return new \ArrayIterator($this->dosyaBilgileri);
    }

    // Countable Interface

    public function count()
    {
        return count($this->dosyaBilgileri);
    }
}