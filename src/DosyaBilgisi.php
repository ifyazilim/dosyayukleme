<?php namespace IfYazilim\DosyaYukleme;

class DosyaBilgisi extends \SplFileInfo
{
    /**
     * Uzantısız dosya adı
     * @var string
     */
    protected $adi;

    /**
     * Dosya uzantısı, ör: gif
     * @var string
     */
    protected $uzanti;

    /**
     * Dosya tipi
     * @var string
     */
    protected $tip;

    /**
     * @param string $dosyaYolu dosyanın kaydedileceği yol
     * @param string $yeniDosyaAdi dosyanın kaydedileceği tercih edilen bir isim
     */
    public function __construct($dosyaYolu, $yeniDosyaAdi = null)
    {
        $tercihEdilenDosyaAdi = is_null($yeniDosyaAdi) ? $dosyaYolu : $yeniDosyaAdi;

        $this->setAdi(pathinfo($tercihEdilenDosyaAdi, PATHINFO_FILENAME));
        $this->setUzanti(pathinfo($tercihEdilenDosyaAdi, PATHINFO_EXTENSION));

        parent::__construct($dosyaYolu);
    }

    /**
     * @return string
     */
    public function getAdi()
    {
        return $this->adi;
    }

    /**
     * @param string $adi
     * @return $this
     */
    public function setAdi($adi)
    {
        $this->adi = $adi;

        return $this;
    }

    /**
     * @return string
     */
    public function getUzanti()
    {
        return $this->uzanti;
    }

    /**
     * @param $uzanti
     * @return $this
     */
    public function setUzanti($uzanti)
    {
        $this->uzanti = strtolower($uzanti);

        return $this;
    }

    /**
     * Dosyanın adını uzantısı ile birlikte verir.
     *
     * @return string
     */
    public function getTamAdi()
    {
        return empty($this->uzanti) ? $this->adi : sprintf('%s.%s', $this->adi, $this->uzanti);
    }

    /**
     * @return string
     */
    public function getTip()
    {
        if (empty($this->tip)) {

            $finfo = new \finfo(FILEINFO_MIME);
            $mimetype = $finfo->file($this->getPathname());
            $mimetypeParts = preg_split('/\s*[;,]\s*/', $mimetype);
            $this->tip = strtolower($mimetypeParts[0]);
            unset($finfo);
        }

        return $this->tip;
    }

    public function isDosyaYuklendi()
    {
        return is_uploaded_file($this->getPathname());
    }
}