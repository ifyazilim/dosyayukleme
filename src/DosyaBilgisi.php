<?php namespace IfYazilim\DosyaYukleme;

use Pekkis\MimeTypes\MimeTypes;

class DosyaBilgisi extends \SplFileInfo
{
    /**
     * @deprecated
     *
     * Uzantısız dosya adı
     * @var string
     */
    protected $adi;

    /**
     * Dosyanın tam adı
     *
     * @var string
     */
    protected $name;

    /**
     * @deprecated use extension
     *
     * @var string
     */
    protected $uzanti;

    /**
     * Dosya uzantısı, ör: gif
     *
     * @var string
     */
    protected $extension;

    /**
     * Dosya tipi
     * @var string
     */
    protected $tip;

    /**
     * Dosya mime type bilgisi.
     *
     * @var string
     */
    protected $mimeType;

    /**
     * @param string $file dosyanın kaydedileceği yol
     * @param string $name dosyanın adı
     */
    public function __construct($file, $name = null)
    {
        $mt = new MimeTypes();

        // bilgileri set edelim
        $mimeType = $mt->resolveMimeType($file);
        $extension = $mt->mimeTypeToExtension($mimeType);

        $this->mimeType = $mimeType;
        $this->extension = $extension;
        $this->uzanti = $extension;

        parent::__construct($file);

        $this->name = is_null($name) ? $this->getFilename() : $name;
    }

    /**
     * @deprecated use getBasename
     *
     * Uzantı olmadan dosyanın adını verir.
     *
     * @return string
     */
    public function getAdi()
    {
        return $this->getBasename();
    }

    /**
     * Uzantı olmadan dosyanın adını verir.
     *
     * @param null $suffix
     * @return string
     */
    public function getBasename($suffix = null)
    {
        return parent::getBasename(is_null($suffix) ? ('.' . $this->getExtension()) : $suffix);
    }

    /**
     * @deprecated
     *
     * @param string $adi
     * @return $this
     */
    public function setAdi($adi)
    {
        $this->adi = $adi;

        return $this;
    }

    /**
     * @deprecated use getExtension
     *
     * @return string
     */
    public function getUzanti()
    {
        return $this->getExtension();
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @deprecated
     *
     * @param $extension
     * @return $this
     */
    public function setUzanti($extension)
    {
        $this->extension = strtolower($extension);

        return $this;
    }

    /**
     * @deprecated use getFilename
     *
     * Dosyanın adını uzantısı ile birlikte verir.
     *
     * @return string
     */
    public function getTamAdi()
    {
        return empty($this->extension) ? $this->adi : sprintf('%s.%s', $this->adi, $this->extension);
    }

    /**
     * @deprecated use getMimeType
     *
     * @return string
     */
    public function getTip()
    {
        return $this->getMimeType();
    }

    /**
     * @deprecated use isFileUploded
     *
     * @return bool
     */
    public function isDosyaYuklendi()
    {
        return $this->isFileUploded();
    }

    /**
     * @return bool
     */
    public function isFileUploded()
    {
        return is_uploaded_file($this->getPathname());
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
