<?php
require __DIR__ . '/../vendor/autoload.php';
$Arabic = new \I18N_Arabic('Glyphs');
$text = "مرحبا العالم";
$text = $Arabic->utf8Glyphs($text);
echo "Shaped: $text\n";
