<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

/**
 * PrintService — Plug-and-play ESC/POS receipt printing layer.
 *
 * Design goals:
 *  - Zero dependency on Chromium, ImageMagick, or CUPS
 *  - Direct USB via /dev/usb/lp0 using FilePrintConnector
 *  - Native ESC/POS formatting (text, bold, alignment, QR, logo raster)
 *  - Retry on transient printer errors
 *  - Structured failure logging
 *
 * Permission requirement (already satisfied on this server):
 *   www-data must be in the `lp` group:
 *   sudo usermod -aG lp www-data && sudo systemctl restart php-fpm (or apache2)
 */
class PrintService
{
    // ─── Configuration ────────────────────────────────────────────────────────

    /** Absolute path to the USB thermal printer device */
    private const DEVICE = '/dev/usb/lp0';

    /** Paper width in characters at 42-column mode (standard 80mm roll) */
    private const COLS = 42;

    /** Maximum retry attempts when the printer errors out */
    private const MAX_RETRIES = 2;

    /** Milliseconds to wait between retries */
    private const RETRY_DELAY_MS = 800;

    // ─── Public API ───────────────────────────────────────────────────────────

    /**
     * Print a formatted ESC/POS invoice for an Order model.
     *
     * Usage from any controller or view:
     *   $printService = app(\App\Services\PrintService::class);
     *   $success = $printService->printOrderInvoice($order);
     *
     * @param Order $order  Eloquent Order model (items, user, restaurantTable,
     *                      customer relations must be loaded before calling)
     * @return array{0: bool, 1: ?string}  [true, null] on success;
     *                                      [false, 'error message'] on failure
     */
    public function printOrderInvoice(Order $order): array
    {
        $device = $this->deviceForJob('cashier_receipt');

        $order->loadMissing([
            'items',
            'user:id,name',
            'restaurantTable:id,name',
            'customer:id,first_name,phone,address',
        ]);

        return $this->printWithRetry(
            fn () => $this->doPrintHtml(
                View::make('orders.invoice', ['order' => $order, 'isDirectPrint' => true])->render(),
                $device,
            ),
            (int) $order->order_serial,
            'cashier',
            'cashier_receipt',
        );
    }

    /**
     * Print any generated HTML using the headless browser to thermal printer pipeline.
     * Routes to the cashier printer by default.
     */
    public function printHtml(string $html, array $context = []): array
    {
        $device = $this->deviceForJob('cashier_receipt');

        $lastException = null;

        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                $this->doPrintHtml($html, $device);
                return [true, null];

            } catch (Exception $e) {
                $lastException = $e;

                Log::warning("PrintService: attempt {$attempt} failed for custom HTML", [
                    'context' => $context,
                    'error'   => $e->getMessage(),
                    'attempt' => $attempt,
                ]);

                if ($attempt < self::MAX_RETRIES) {
                    usleep(self::RETRY_DELAY_MS * 1000);
                }
            }
        }

        $reason = $lastException?->getMessage() ?? 'Unknown error';
        return [false, $reason];
    }

    /**
     * Generic printInvoice() accepting a plain array.
     *
     * Use this when you want to print without an Eloquent model, or from
     * any legacy PHP file outside of Laravel's DI container.
     *
     * Array shape:
     * [
     *   'id'         => 'ORD-20260430-0001',  // displayed as order number
     *   'store_name' => 'Point 88',
     *   'date'       => '2026-04-30 14:30',
     *   'cashier'    => 'Ahmed',               // optional
     *   'table'      => 'Table 5',             // optional
     *   'customer'   => 'Mohamed Ali',         // optional
     *   'payment'    => 'Cash',                // optional
     *   'notes'      => 'Extra sauce',         // optional
     *   'items'      => [
     *       ['name' => 'Burger',  'qty' => 2, 'price' => 45.00],
     *       ['name' => 'Juice',   'qty' => 1, 'price' => 20.00],
     *   ],
     *   'subtotal'   => 110.00,
     *   'discount'   => 10.00,                 // optional, 0 = no discount row
     *   'total'      => 100.00,
     * ]
     *
     * @param array $invoice
     * @return bool
     */
    public function printInvoice(array $invoice): bool
    {
        $device       = $this->deviceForJob('cashier_receipt');
        $lastException = null;

        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                $this->doPrintArray($invoice, $device);
                return true;

            } catch (Exception $e) {
                $lastException = $e;

                Log::warning("PrintService: attempt {$attempt} failed for invoice array", [
                    'invoice_id' => $invoice['id'] ?? 'unknown',
                    'error'      => $e->getMessage(),
                    'attempt'    => $attempt,
                ]);

                if ($attempt < self::MAX_RETRIES) {
                    usleep(self::RETRY_DELAY_MS * 1000);
                }
            }
        }

        Log::error('PrintService: printInvoice() all retries exhausted', [
            'invoice_id'  => $invoice['id'] ?? 'unknown',
            'final_error' => $lastException?->getMessage(),
            'device'      => $device,
        ]);

        return false;
    }

    // ─── Private: Eloquent model path ─────────────────────────────────────────

    /**
     * Build and send the receipt from an Order Eloquent model using Firefox headless.
     * This preserves the exact CSS design and Arabic text layout.
     */
    private function doPrint(Order $order, string $device = self::DEVICE): void
    {
        $html = View::make('orders.invoice', ['order' => $order, 'isDirectPrint' => true])->render();
        $this->doPrintHtml($html, $device);
    }

    private function doPrintHtml(string $html, string $device = self::DEVICE): void
    {
        $id = uniqid('inv_');
        $htmlFile = "/tmp/{$id}.html";
        $pngFile = "/tmp/{$id}.png";
        $finalPng = "/tmp/{$id}_final.png";

        try {
            // 1. Save HTML
            file_put_contents($htmlFile, $html);

            // 2. Take a screenshot using wkhtmltoimage
            $firefoxCmd = sprintf(
                '/usr/local/bin/wkhtmltoimage --width 576 --disable-smart-width "file://%s" %s 2>&1',
                $htmlFile,
                escapeshellarg($pngFile)
            );
            exec($firefoxCmd, $output, $returnVar);

            if (!file_exists($pngFile)) {
                Log::error("wkhtmltoimage output: " . implode("\n", $output));
                throw new Exception("wkhtmltoimage failed to generate screenshot.");
            }

            // 3. Detect the exact content bounding box (eliminates trailing
            //    whitespace that overflows the printer buffer → garbage chars),
            //    then crop and resize to 576px wide.
            $geometry   = $this->detectContentGeometry($pngFile);
            $cropArg    = $geometry ?: '576x99999+0+0'; // fallback: keep full width
            $convertCmd = sprintf(
                '/usr/bin/convert %s -crop %s +repage -resize 576x -crop 576x250 +repage %s_part_%%03d.png 2>&1',
                escapeshellarg($pngFile),
                $cropArg,
                escapeshellarg($finalPng)
            );
            exec($convertCmd, $output2, $returnVar2);

            $parts = glob($finalPng . '_part_*.png');
            if (is_array($parts)) {
                sort($parts);
            }

            if ($returnVar2 !== 0 || empty($parts)) {
                Log::error("Convert output: " . implode("\n", $output2));
                throw new Exception("ImageMagick convert failed.");
            }

            // 4. Print via Escpos
            if (! file_exists($device)) {
                throw new Exception('Printer device not found: ' . $device);
            }

            if (! is_writable($device)) {
                throw new Exception('Printer device not writable (permission denied): ' . $device);
            }

            $connector = new FilePrintConnector($device);
            $printer = new Printer($connector);

            try {
                $printer->initialize();
                $printer->setJustification(Printer::JUSTIFY_CENTER);

                foreach ($parts as $part) {
                    $escposImg = EscposImage::load($part, false);
                    $printer->bitImage($escposImg);

                    // Pause slightly between chunks to prevent buffer overflow on generic
                    // thermal printers, which causes them to restart and duplicate prints.
                    usleep(350000); // 350ms
                }

                $printer->feed(3);
                $printer->cut();
            } finally {
                $printer->close();
            }

        } finally {
            // Cleanup temp files
            @unlink($htmlFile);
            @unlink($pngFile);
            @unlink($finalPng);
            if (isset($parts) && is_array($parts)) {
                foreach ($parts as $part) {
                    @unlink($part);
                }
            }
        }
    }

    // ─── Private: Array path (core renderer) ──────────────────────────────────

    /**
     * Core print routine — connects to printer, renders all sections, closes.
     * Throws Exception on any failure so the retry loop can handle it.
     */
    private function doPrintArray(array $inv, string $device = self::DEVICE): void
    {
        if (! file_exists($device)) {
            throw new Exception('Printer device not found: ' . $device);
        }

        if (! is_writable($device)) {
            throw new Exception('Printer device not writable (permission denied): ' . $device
                . ' — ensure www-data is in the lp group.');
        }

        $connector = new FilePrintConnector($device);
        $printer   = new Printer($connector);

        try {
            $printer->initialize();

            // ── 1. Logo (raster image via GD) ─────────────────────────────
            $this->printLogo($printer);

            // ── 2. Store name header ───────────────────────────────────────
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
            $printer->setEmphasis(true);
            $printer->text($this->truncate($inv['store_name'] ?? 'Point 88', 20) . "\n");
            $printer->setEmphasis(false);
            $printer->selectPrintMode();

            $printer->text("==========================================\n");

            // ── 3. Invoice metadata ────────────────────────────────────────
            $printer->setJustification(Printer::JUSTIFY_LEFT);

            $this->metaRow($printer, 'Order #',   $inv['id']       ?? '—');
            $this->metaRow($printer, 'Date',       $inv['date']     ?? '');
            $this->metaRow($printer, 'Cashier',    $inv['cashier']  ?? '');

            if (! empty($inv['table'])) {
                $this->metaRow($printer, 'Table', $inv['table']);
            }

            if (! empty($inv['customer'])) {
                $this->metaRow($printer, 'Customer', $inv['customer']);
            }

            if (! empty($inv['payment'])) {
                $this->metaRow($printer, 'Payment', $inv['payment']);
            }

            $printer->text("------------------------------------------\n");

            // ── 4. Column headers ──────────────────────────────────────────
            $printer->setEmphasis(true);
            $printer->text($this->itemLine('Item', 'Qty', 'Total'));
            $printer->setEmphasis(false);
            $printer->text("------------------------------------------\n");

            // ── 5. Line items ──────────────────────────────────────────────
            foreach ($inv['items'] as $item) {
                $printer->text($this->itemLine(
                    $item['name'],
                    (string) ($item['qty'] ?? 1),
                    $this->formatMoney($item['price'] ?? 0)
                ));

                // Per-item note (if any)
                if (! empty($item['notes'])) {
                    $printer->setFont(Printer::FONT_B);
                    $printer->text('  > ' . $this->truncate($item['notes'], 36) . "\n");
                    $printer->setFont(Printer::FONT_A);
                }
            }

            $printer->text("==========================================\n");

            // ── 6. Totals ──────────────────────────────────────────────────
            $subtotal = (float) ($inv['subtotal'] ?? $inv['total'] ?? 0);
            $discount = (float) ($inv['discount'] ?? 0);
            $total    = (float) ($inv['total']    ?? 0);

            if ($discount > 0) {
                $this->totalRow($printer, 'Subtotal', $this->formatMoney($subtotal), false);
                $this->totalRow($printer, 'Discount', '- ' . $this->formatMoney($discount), false);
                $printer->text("------------------------------------------\n");
            }

            // Grand total — bold, double-height
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->setEmphasis(true);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            $printer->text("TOTAL: " . $this->formatMoney($total) . "\n");
            $printer->selectPrintMode();
            $printer->setEmphasis(false);

            // ── 7. Order notes ─────────────────────────────────────────────
            if (! empty($inv['notes'])) {
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("------------------------------------------\n");
                $printer->setFont(Printer::FONT_B);
                $printer->text("Note: " . $this->truncate($inv['notes'], 36) . "\n");
                $printer->setFont(Printer::FONT_A);
            }

            // ── 8. QR code (native ESC/POS — no external lib needed) ──────
            $qrData = $inv['serial'] ?? $inv['id'] ?? '';
            if ($qrData !== '') {
                $printer->feed(1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->qrCode($qrData, Printer::QR_ECLEVEL_M, 4);
                $printer->setFont(Printer::FONT_B);
                $printer->text($qrData . "\n");
                $printer->setFont(Printer::FONT_A);
            }

            // ── 9. Footer ──────────────────────────────────────────────────
            $printer->feed(1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setFont(Printer::FONT_B);
            $printer->text("Thank you for your visit!\n");
            $printer->text(config('app.name', 'Point 88') . "\n");
            $printer->setFont(Printer::FONT_A);

            // ── 10. Feed & cut ─────────────────────────────────────────────
            $printer->feed(3);
            $printer->cut();

        } finally {
            // Always close — even if an exception was thrown — to release the device
            $printer->close();
        }
    }


    // ─── QZ Tray: ESC/POS data for browser printing ───────────────────────────

    /**
     * Generate ESC/POS receipt data for an Order and return it as base64.
     * Used by the browser-side QZ Tray integration to print to a local USB printer.
     */
    public function buildOrderReceiptBase64(Order $order): string
    {
        $tmpDevice = tempnam(sys_get_temp_dir(), 'escpos_');
        try {
            $html = View::make('orders.invoice', ['order' => $order, 'isDirectPrint' => true])->render();
            $this->doPrintHtml($html, $tmpDevice);
            return base64_encode((string) file_get_contents($tmpDevice));
        } finally {
            @unlink($tmpDevice);
        }
    }


    public function buildPreparationTicketBase64(Order $order, string $label = 'NEW ORDER', ?array $printItems = null): string
    {
        $tmpDevice = tempnam(sys_get_temp_dir(), 'escpos_');
        try {
            $printItems = $printItems ?? $order->items->all();
            $html = View::make('orders.preparation-receipt', [
                'order'         => $order,
                'label'         => $label,
                'printItems'    => $printItems,
                'isDirectPrint' => true,
            ])->render();
            $this->doPrintHtml($html, $tmpDevice);
            return base64_encode((string) file_get_contents($tmpDevice));
        } finally {
            @unlink($tmpDevice);
        }
    }

    public function buildHtmlBase64(string $html): string
    {
        $tmpDevice = tempnam(sys_get_temp_dir(), 'escpos_');
        try {
            $this->doPrintHtml($html, $tmpDevice);
            return base64_encode((string) file_get_contents($tmpDevice));
        } finally {
            @unlink($tmpDevice);
        }
    }

    private function orderToArray(Order $order): array
    {
        return [
            'id'         => (string) ($order->order_number ?? $order->order_serial),
            'serial'     => (string) $order->order_serial,
            'store_name' => config('app.name', 'Point 88'),
            'date'       => $order->created_at->format('d/m/Y H:i'),
            'cashier'    => $order->user?->name ?? '',
            'table'      => $order->restaurantTable?->name ?? '',
            'customer'   => $order->customer?->first_name ?? '',
            'payment'    => $order->payment_method ?? '',
            'notes'      => $order->notes ?? '',
            'items'      => $order->items->map(fn($item) => [
                'name'  => (string) ($item->product_name ?? ''),
                'qty'   => (int) $item->quantity,
                'price' => (float) $item->line_total,
                'notes' => (string) ($item->notes ?? ''),
            ])->all(),
            'subtotal'   => (float) $order->subtotal,
            'discount'   => (float) ($order->discount_amount ?? 0),
            'total'      => (float) $order->total,
        ];
    }

    // ─── Private: Logo printing ───────────────────────────────────────────────

    /**
     * Print the store logo as an ESC/POS raster image using GD.
     * Falls back silently if no logo file exists or GD is unavailable.
     */
    private function printLogo(Printer $printer): void
    {
        if (! extension_loaded('gd')) {
            return;
        }

        // Prefer the named logo file, fall back to the icon-only logo
        $candidates = [
            public_path('images/Logo_Name.png'),
            public_path('images/logo.png'),
        ];

        $logoPath = null;
        foreach ($candidates as $path) {
            if (file_exists($path) && is_readable($path)) {
                $logoPath = $path;
                break;
            }
        }

        if ($logoPath === null) {
            return;
        }

        try {
            // Resize to exactly 576 px wide (80mm @203dpi) using GD
            // The height is calculated to maintain aspect ratio
            $src = imagecreatefrompng($logoPath);

            if ($src === false) {
                return; // Not a valid PNG — skip logo silently
            }

            $srcW = imagesx($src);
            $srcH = imagesy($src);

            $targetW = 384; // A safe logo width (67% of 576) to avoid cropping
            $targetH = (int) round($srcH * ($targetW / $srcW));

            $dst = imagecreatetruecolor($targetW, $targetH);

            // Fill with white background (thermal printers need white = off)
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefilledrectangle($dst, 0, 0, $targetW - 1, $targetH - 1, $white);

            // Handle PNG transparency
            imagealphablending($dst, true);
            imagesavealpha($dst, true);

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);

            // Write resized PNG to a temp file for EscposImage to consume
            $tmpPath = sys_get_temp_dir() . '/escpos_logo_' . getmypid() . '.png';
            imagepng($dst, $tmpPath);

            imagedestroy($src);
            imagedestroy($dst);

            $escImg = EscposImage::load($tmpPath, false);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            // Use bitImage instead of graphics for better compatibility with generic Chinese printers like ZKP8001
            $printer->bitImage($escImg);
            $printer->feed(1);

            @unlink($tmpPath);

        } catch (Exception $e) {
            // Logo printing is non-critical — log and continue
            Log::info('PrintService: logo skipped', ['reason' => $e->getMessage()]);
        }
    }

    // ─── Private: Formatting helpers ──────────────────────────────────────────

    /**
     * Use ImageMagick to find the bounding box of non-white content after
     * virtual-trim. Returns a WxH+X+Y geometry string with 14px bottom padding
     * added so the last glyph is never clipped, or '' on failure (caller falls
     * back to the full image).
     */
    private function detectContentGeometry(string $pngPath): string
    {
        // Step 1 – ask ImageMagick for the trimmed geometry
        $infoCmd = sprintf(
            '/usr/bin/convert %s -virtual-pixel white -trim -format "%%wx%%h%%X%%Y\n" info: 2>/dev/null',
            escapeshellarg($pngPath)
        );
        exec($infoCmd, $infoOut, $infoRet);

        if ($infoRet !== 0 || empty($infoOut[0])) {
            return '';
        }

        if (! preg_match('/^(\d+)x(\d+)([+-]\d+)([+-]\d+)/', trim($infoOut[0]), $m)) {
            return '';
        }

        [$_full, $w, $h, $x, $y] = $m;

        // Step 2 – get original image height so we don't exceed it
        $origCmd = sprintf('/usr/bin/identify -format "%%h" %s 2>/dev/null', escapeshellarg($pngPath));
        exec($origCmd, $origOut);
        $origH = isset($origOut[0]) && is_numeric($origOut[0]) ? (int) $origOut[0] : PHP_INT_MAX;

        $croppedH = min((int) $h + 14, $origH - (int) $y);

        return "{$w}x{$croppedH}{$x}{$y}";
    }

    /**
     * Render a left-label / right-value metadata row within COLS characters.
     * Example: "Date           30/04/2026  14:30"
     */
    private function metaRow(Printer $printer, string $label, string $value): void
    {
        $label = $label . ':';
        $maxVal = self::COLS - mb_strlen($label) - 1;
        $value  = $this->truncate($value, $maxVal);
        $pad    = self::COLS - mb_strlen($label) - mb_strlen($value);

        $printer->text($label . str_repeat(' ', max(1, $pad)) . $value . "\n");
    }

    /**
     * Render a right-aligned total row.
     * Example: "                Subtotal     EGP 110.00"
     */
    private function totalRow(Printer $printer, string $label, string $value, bool $bold = false): void
    {
        $right = $label . '  ' . $value;
        $line  = str_pad($right, self::COLS, ' ', STR_PAD_LEFT);

        if ($bold) {
            $printer->setEmphasis(true);
        }
        $printer->text($line . "\n");
        if ($bold) {
            $printer->setEmphasis(false);
        }
    }

    /**
     * Render a 3-column item line: [name ... qty ... price]
     * Total width = COLS.  Name is left-truncated to fit.
     *
     * Layout:  <name(24)> <qty(5)> <price(12)>  = 41 chars + 1 separator = 42
     */
    private function itemLine(string $name, string $qty, string $price): string
    {
        $priceW = 12;
        $qtyW   = 5;
        $nameW  = self::COLS - $priceW - $qtyW - 1; // 1 separator space

        $name  = $this->truncate($name, $nameW);
        $name  = str_pad($name,  $nameW, ' ', STR_PAD_RIGHT);
        $qty   = str_pad($qty,   $qtyW,  ' ', STR_PAD_LEFT);
        $price = str_pad($price, $priceW, ' ', STR_PAD_LEFT);

        return $name . $qty . $price . "\n";
    }

    /**
     * Format a monetary value. Always in English (no locale switching).
     * "EGP 110.00"
     */
    private function formatMoney(float $amount): string
    {
        return 'EGP ' . number_format(abs($amount), 2);
    }

    /**
     * Truncate a multi-byte string to $maxLen characters.
     */
    private function truncate(string $str, int $maxLen): string
    {
        if (mb_strlen($str) <= $maxLen) {
            return $str;
        }

        return mb_substr($str, 0, $maxLen - 1) . '…';
    }

    // ─── Multi-printer public API ─────────────────────────────────────────────

    /**
     * Print the customer receipt to the cashier printer.
     * Reuses the existing invoice template — layout is untouched.
     */
    public function printCashierReceipt(Order $order): array
    {
        $order->loadMissing([
            'items',
            'user:id,name',
            'restaurantTable:id,name',
            'customer:id,first_name,phone,address',
        ]);

        $device = $this->deviceForJob('cashier_receipt');

        return $this->printWithRetry(
            fn () => $this->doPrintHtml(
                View::make('orders.invoice', ['order' => $order, 'isDirectPrint' => true])->render(),
                $device,
            ),
            (int) $order->order_serial,
            'cashier',
            'cashier_receipt',
        );
    }

    /**
     * Print a preparation ticket (no prices) to the bar / kitchen printer.
     *
     * @param  Order       $order     Order model (relations loaded on demand)
     * @param  string      $label     'NEW ORDER' | 'ADD ITEMS' | 'REPRINT'
     * @param  array|null  $newItems  Specific OrderItem objects to print; null = all items
     */
    public function printPreparationReceipt(Order $order, string $label = 'NEW ORDER', ?array $newItems = null): array
    {
        $order->loadMissing([
            'items',
            'user:id,name',
            'restaurantTable:id,name',
        ]);

        $printItems = $newItems ?? $order->items->all();
        $jobType    = match ($label) { 'ADD ITEMS' => 'add_items', 'REPRINT' => 'reprint', default => 'new_order' };
        $device     = $this->deviceForJob($jobType);

        return $this->printWithRetry(
            fn () => $this->doPrintHtml(
                View::make('orders.preparation-receipt', [
                    'order'         => $order,
                    'label'         => $label,
                    'printItems'    => $printItems,
                    'isDirectPrint' => true,
                ])->render(),
                $device,
            ),
            (int) $order->order_serial,
            'bar',
            match ($label) {
                'ADD ITEMS' => 'add_items',
                'REPRINT'   => 'reprint',
                default     => 'new_order',
            },
        );
    }

    // ─── Printer management helpers ───────────────────────────────────────────

    /**
     * Resolve the device path for a given job type.
     * Reads from the printers table; falls back to config/env.
     */
    private function deviceForJob(string $job): string
    {
        try {
            $printer = \App\Models\Printer::active()->handlesJob($job)->first();
            if ($printer) {
                return $printer->device;
            }
        } catch (\Throwable) {}

        return match ($job) {
            'cashier_receipt' => (string) config('printers.cashier', self::DEVICE),
            default           => (string) config('printers.bar', '/dev/usb/lp1'),
        };
    }

    /**
     * Print a test page to verify a printer is working.
     */
    public function printTestPage(string $device): array
    {
        try {
            if (! file_exists($device)) {
                return [false, "Device not found: {$device}"];
            }
            if (! is_writable($device)) {
                return [false, "Device not writable: {$device}"];
            }

            $connector = new FilePrintConnector($device);
            $printer   = new Printer($connector);

            $printer->initialize();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("=== TEST PAGE ===\n");
            $printer->setEmphasis(false);
            $printer->text("Systemco Printer Test\n");
            $printer->text(now()->format('Y-m-d H:i:s') . "\n");
            $printer->text("Device: {$device}\n");
            $printer->text("=================\n");
            $printer->feed(3);
            $printer->cut();
            $printer->close();

            return [true, null];
        } catch (\Throwable $e) {
            return [false, $e->getMessage()];
        }
    }

    // ─── Multi-printer private helpers ───────────────────────────────────────

    /**
     * Run $fn with retries, log the outcome to print_logs, and return [bool, ?string].
     */
    private function printWithRetry(
        callable $fn,
        int $orderId,
        string $printer,
        string $printType,
    ): array {
        $lastException = null;

        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                $fn();
                $this->logPrint($orderId, $printer, $printType, true);
                return [true, null];

            } catch (Exception $e) {
                $lastException = $e;

                Log::warning("PrintService: attempt {$attempt} failed [{$printer}/{$printType}]", [
                    'order_id' => $orderId,
                    'error'    => $e->getMessage(),
                    'attempt'  => $attempt,
                ]);

                if ($attempt < self::MAX_RETRIES) {
                    usleep(self::RETRY_DELAY_MS * 1000);
                }
            }
        }

        $reason = $lastException?->getMessage() ?? 'Unknown error';

        Log::error("PrintService: all retries exhausted [{$printer}/{$printType}]", [
            'order_id' => $orderId,
            'error'    => $reason,
        ]);

        $this->logPrint($orderId, $printer, $printType, false, $reason);

        return [false, $reason];
    }

    /**
     * Persist a row to print_logs (best-effort — never throws).
     */
    private function logPrint(
        ?int $orderId,
        string $printer,
        string $printType,
        bool $success,
        ?string $error = null,
    ): void {
        try {
            \App\Models\PrintLog::query()->create([
                'order_id'      => $orderId,
                'printer'       => $printer,
                'print_type'    => $printType,
                'status'        => $success ? 'success' : 'failed',
                'error_message' => $error,
                'printed_at'    => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('PrintService: could not write print_log', ['error' => $e->getMessage()]);
        }
    }
}
