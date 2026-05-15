<?php
require __DIR__ . '/../vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => [80, 297], // 80mm width
    'default_font' => 'sans-serif'
]);
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;
$html = '<h1 style="color:red; text-align:center;">مرحبا بك</h1><p>Invoice 123</p>';
$mpdf->WriteHTML($html);
$mpdf->Output('/tmp/test-mpdf.pdf', \Mpdf\Output\Destination::FILE);
exec('convert -density 300 /tmp/test-mpdf.pdf -trim /tmp/test-mpdf.png 2>&1', $out, $ret);
echo "Ret: $ret\n";
echo implode("\n", $out);
