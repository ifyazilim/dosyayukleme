<?php

require_once '../vendor/autoload.php';

$fileName = '/tmp/' . mt_rand(111111, 999999) . '.txt';

touch($fileName);

$dosyaBilsisi = new \IfYazilim\DosyaYukleme\DosyaBilgisi($fileName);

echo $dosyaBilsisi->getBasename() . "\n";
echo $dosyaBilsisi->getExtension() . "\n";
echo $dosyaBilsisi->getFilename() . "\n";
echo $dosyaBilsisi->getPath() . "\n";
echo $dosyaBilsisi->getPathname() . "\n";
echo $dosyaBilsisi->getRealPath() . "\n";
echo $dosyaBilsisi->getSize() . "\n";
echo $dosyaBilsisi->getType() . "\n";

print_r(pathinfo($fileName));

unlink($fileName);
