<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$order = \App\Models\Order::first();
$html = \Illuminate\Support\Facades\View::make('orders.invoice', ['order' => $order, 'isDirectPrint' => true])->render();
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => [80, 297],
    'default_font' => 'sans-serif'
]);
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;
$mpdf->WriteHTML($html);
$mpdf->Output('/tmp/test-mpdf-invoice.pdf', \Mpdf\Output\Destination::FILE);
exec('convert -density 300 /tmp/test-mpdf-invoice.pdf -trim -resize 576x /tmp/test-mpdf-invoice.png 2>&1', $out, $ret);
echo "Ret: $ret\n";
