<?php

namespace App\Support;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\View\Factory as ViewFactory;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;

class PdfExportRenderer
{
    /**
     * Contextual Arabic presentation forms used to improve DomPDF rendering.
     * Keys are base Arabic codepoints.
     * i = isolated, f = final, b = initial, m = medial.
     */
    private const ARABIC_GLYPH_FORMS = [
        0x0621 => ['i' => "\u{FE80}"],
        0x0622 => ['i' => "\u{FE81}", 'f' => "\u{FE82}"],
        0x0623 => ['i' => "\u{FE83}", 'f' => "\u{FE84}"],
        0x0624 => ['i' => "\u{FE85}", 'f' => "\u{FE86}"],
        0x0625 => ['i' => "\u{FE87}", 'f' => "\u{FE88}"],
        0x0626 => ['i' => "\u{FE89}", 'f' => "\u{FE8A}", 'b' => "\u{FE8B}", 'm' => "\u{FE8C}"],
        0x0627 => ['i' => "\u{FE8D}", 'f' => "\u{FE8E}"],
        0x0628 => ['i' => "\u{FE8F}", 'f' => "\u{FE90}", 'b' => "\u{FE91}", 'm' => "\u{FE92}"],
        0x0629 => ['i' => "\u{FE93}", 'f' => "\u{FE94}"],
        0x062A => ['i' => "\u{FE95}", 'f' => "\u{FE96}", 'b' => "\u{FE97}", 'm' => "\u{FE98}"],
        0x062B => ['i' => "\u{FE99}", 'f' => "\u{FE9A}", 'b' => "\u{FE9B}", 'm' => "\u{FE9C}"],
        0x062C => ['i' => "\u{FE9D}", 'f' => "\u{FE9E}", 'b' => "\u{FE9F}", 'm' => "\u{FEA0}"],
        0x062D => ['i' => "\u{FEA1}", 'f' => "\u{FEA2}", 'b' => "\u{FEA3}", 'm' => "\u{FEA4}"],
        0x062E => ['i' => "\u{FEA5}", 'f' => "\u{FEA6}", 'b' => "\u{FEA7}", 'm' => "\u{FEA8}"],
        0x062F => ['i' => "\u{FEA9}", 'f' => "\u{FEAA}"],
        0x0630 => ['i' => "\u{FEAB}", 'f' => "\u{FEAC}"],
        0x0631 => ['i' => "\u{FEAD}", 'f' => "\u{FEAE}"],
        0x0632 => ['i' => "\u{FEAF}", 'f' => "\u{FEB0}"],
        0x0633 => ['i' => "\u{FEB1}", 'f' => "\u{FEB2}", 'b' => "\u{FEB3}", 'm' => "\u{FEB4}"],
        0x0634 => ['i' => "\u{FEB5}", 'f' => "\u{FEB6}", 'b' => "\u{FEB7}", 'm' => "\u{FEB8}"],
        0x0635 => ['i' => "\u{FEB9}", 'f' => "\u{FEBA}", 'b' => "\u{FEBB}", 'm' => "\u{FEBC}"],
        0x0636 => ['i' => "\u{FEBD}", 'f' => "\u{FEBE}", 'b' => "\u{FEBF}", 'm' => "\u{FEC0}"],
        0x0637 => ['i' => "\u{FEC1}", 'f' => "\u{FEC2}", 'b' => "\u{FEC3}", 'm' => "\u{FEC4}"],
        0x0638 => ['i' => "\u{FEC5}", 'f' => "\u{FEC6}", 'b' => "\u{FEC7}", 'm' => "\u{FEC8}"],
        0x0639 => ['i' => "\u{FEC9}", 'f' => "\u{FECA}", 'b' => "\u{FECB}", 'm' => "\u{FECC}"],
        0x063A => ['i' => "\u{FECD}", 'f' => "\u{FECE}", 'b' => "\u{FECF}", 'm' => "\u{FED0}"],
        0x0641 => ['i' => "\u{FED1}", 'f' => "\u{FED2}", 'b' => "\u{FED3}", 'm' => "\u{FED4}"],
        0x0642 => ['i' => "\u{FED5}", 'f' => "\u{FED6}", 'b' => "\u{FED7}", 'm' => "\u{FED8}"],
        0x0643 => ['i' => "\u{FED9}", 'f' => "\u{FEDA}", 'b' => "\u{FEDB}", 'm' => "\u{FEDC}"],
        0x0644 => ['i' => "\u{FEDD}", 'f' => "\u{FEDE}", 'b' => "\u{FEDF}", 'm' => "\u{FEE0}"],
        0x0645 => ['i' => "\u{FEE1}", 'f' => "\u{FEE2}", 'b' => "\u{FEE3}", 'm' => "\u{FEE4}"],
        0x0646 => ['i' => "\u{FEE5}", 'f' => "\u{FEE6}", 'b' => "\u{FEE7}", 'm' => "\u{FEE8}"],
        0x0647 => ['i' => "\u{FEE9}", 'f' => "\u{FEEA}", 'b' => "\u{FEEB}", 'm' => "\u{FEEC}"],
        0x0648 => ['i' => "\u{FEED}", 'f' => "\u{FEEE}"],
        0x0649 => ['i' => "\u{FEEF}", 'f' => "\u{FEF0}"],
        0x064A => ['i' => "\u{FEF1}", 'f' => "\u{FEF2}", 'b' => "\u{FEF3}", 'm' => "\u{FEF4}"],
    ];

    /**
     * Letters that can connect to the next letter.
     */
    private const ARABIC_CONNECTS_BEFORE = [
        0x0626 => true,
        0x0628 => true,
        0x062A => true,
        0x062B => true,
        0x062C => true,
        0x062D => true,
        0x062E => true,
        0x0633 => true,
        0x0634 => true,
        0x0635 => true,
        0x0636 => true,
        0x0637 => true,
        0x0638 => true,
        0x0639 => true,
        0x063A => true,
        0x0641 => true,
        0x0642 => true,
        0x0643 => true,
        0x0644 => true,
        0x0645 => true,
        0x0646 => true,
        0x0647 => true,
        0x064A => true,
    ];

    /**
     * Letters that can connect to the previous letter.
     */
    private const ARABIC_CONNECTS_AFTER = [
        0x0622 => true,
        0x0623 => true,
        0x0624 => true,
        0x0625 => true,
        0x0626 => true,
        0x0627 => true,
        0x0628 => true,
        0x0629 => true,
        0x062A => true,
        0x062B => true,
        0x062C => true,
        0x062D => true,
        0x062E => true,
        0x062F => true,
        0x0630 => true,
        0x0631 => true,
        0x0632 => true,
        0x0633 => true,
        0x0634 => true,
        0x0635 => true,
        0x0636 => true,
        0x0637 => true,
        0x0638 => true,
        0x0639 => true,
        0x063A => true,
        0x0641 => true,
        0x0642 => true,
        0x0643 => true,
        0x0644 => true,
        0x0645 => true,
        0x0646 => true,
        0x0647 => true,
        0x0648 => true,
        0x0649 => true,
        0x064A => true,
    ];

    public function downloadPdfFromView(string $view, array $data, string $fileName, string $fallbackUrl): Response
    {
        $previousMemory = ini_get('memory_limit');
        $memoryBytes = $this->parseMemoryLimit((string) $previousMemory);
        if ($memoryBytes !== -1 && $memoryBytes < 256 * 1024 * 1024) {
            ini_set('memory_limit', '256M');
        }

        $previousTimeout = (int) ini_get('max_execution_time');
        if ($previousTimeout > 0 && $previousTimeout < 120) {
            set_time_limit(120);
        }

        $html = app(ViewFactory::class)->make($view, $data)->render();

        // mPDF first — more reliable for Arabic/RTL and complex tables
        $mPdfResponse = $this->downloadPdfWithMpdfHtml($html, $fileName);
        if ($mPdfResponse instanceof Response) {
            return $mPdfResponse;
        }

        $domPdfResponse = $this->downloadPdfWithDompdfHtml($html, $fileName);
        if ($domPdfResponse instanceof Response) {
            return $domPdfResponse;
        }

        $chromiumResponse = $this->downloadPdfWithChromiumHtml($html, $fileName);
        if ($chromiumResponse instanceof Response) {
            return $chromiumResponse;
        }

        return redirect()
            ->to($fallbackUrl)
            ->with('error', app()->getLocale() === 'ar'
                ? 'تعذر إنشاء ملف PDF حالياً. يرجى المحاولة مرة أخرى.'
                : 'Unable to generate PDF right now. Please try again.');
    }

    private function downloadPdfWithMpdfHtml(string $html, string $fileName): ?Response
    {
        try {
            $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'restv2-mpdf';
            File::ensureDirectoryExists($tempDir);

            $mPdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L',
                'tempDir' => $tempDir,
                'default_font' => 'dejavusans',
                'margin_left' => 12,
                'margin_right' => 12,
                'margin_top' => 12,
                'margin_bottom' => 12,
            ]);

            $mPdf->autoScriptToLang = true;
            $mPdf->autoLangToFont = true;

            if (method_exists($mPdf, 'SetDirectionality')) {
                $mPdf->SetDirectionality(app()->getLocale() === 'ar' ? 'rtl' : 'ltr');
            }

            $mPdf->WriteHTML($html);
            $pdfBinary = $mPdf->Output('', Destination::STRING_RETURN);

            if (! is_string($pdfBinary) || $pdfBinary === '') {
                throw new \RuntimeException('mPDF output is empty.');
            }

            $downloadName = str_replace(["\r", "\n", '"'], '', $fileName);

            return response($pdfBinary, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'X-PDF-Engine' => 'mpdf',
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    private function downloadPdfWithDompdfHtml(string $html, string $fileName): ?Response
    {
        try {
            $preparedHtml = $this->prepareHtmlForDompdf($html);

            $pdfBinary = Pdf::setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'isFontSubsettingEnabled' => true,
                'dpi' => 110,
            ])
                ->loadHTML($preparedHtml)
                ->setPaper('a4', 'landscape')
                ->output();

            if (! is_string($pdfBinary) || $pdfBinary === '') {
                throw new \RuntimeException('Dompdf output is empty.');
            }

            $downloadName = str_replace(["\r", "\n", '"'], '', $fileName);

            return response($pdfBinary, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'X-PDF-Engine' => 'dompdf',
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    private function prepareHtmlForDompdf(string $html): string
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $previousUseInternalErrors = libxml_use_internal_errors(true);

        try {
            $loaded = $document->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            if (! $loaded) {
                return $html;
            }

            $root = $document->documentElement;
            if ($root instanceof \DOMNode) {
                $this->shapeArabicTextNodesForDompdf($root);
            }

            $result = $document->saveHTML();
            if (! is_string($result) || $result === '') {
                return $html;
            }

            return (string) preg_replace('/^<\?xml[^>]*>\s*/', '', $result);
        } catch (\Throwable) {
            return $html;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousUseInternalErrors);
        }
    }

    private function shapeArabicTextNodesForDompdf(\DOMNode $node): void
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            $node->nodeValue = $this->shapeArabicTextForDompdf((string) $node->nodeValue);

            return;
        }

        $nodeName = strtolower((string) $node->nodeName);
        if (in_array($nodeName, ['script', 'style'], true)) {
            return;
        }

        if (! $node->hasChildNodes()) {
            return;
        }

        for ($index = 0; $index < $node->childNodes->length; $index++) {
            $child = $node->childNodes->item($index);

            if ($child instanceof \DOMNode) {
                $this->shapeArabicTextNodesForDompdf($child);
            }
        }
    }

    private function shapeArabicTextForDompdf(string $text): string
    {
        $result = preg_replace_callback(
            '/[\x{0621}-\x{064A}\x{0671}-\x{06D3}\x{06FA}-\x{06FF}]+/u',
            fn(array $matches): string => $this->shapeArabicWordForDompdf($matches[0]),
            $text
        );

        return is_string($result) ? $result : $text;
    }

    private function shapeArabicWordForDompdf(string $word): string
    {
        $chars = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);

        if (! is_array($chars) || $chars === []) {
            return $word;
        }

        $shapedChars = [];
        $charsCount = count($chars);

        for ($index = 0; $index < $charsCount; $index++) {
            $char = $chars[$index];
            $codepoint = mb_ord($char, 'UTF-8');
            $forms = self::ARABIC_GLYPH_FORMS[$codepoint] ?? null;

            if (! is_array($forms)) {
                $shapedChars[] = $char;

                continue;
            }

            $prevCodepoint = null;
            if ($index > 0) {
                $prevCodepoint = mb_ord($chars[$index - 1], 'UTF-8');
            }

            $nextCodepoint = null;
            if ($index + 1 < $charsCount) {
                $nextCodepoint = mb_ord($chars[$index + 1], 'UTF-8');
            }

            $joinsPrevious = $prevCodepoint !== null
                && isset(self::ARABIC_CONNECTS_BEFORE[$prevCodepoint])
                && isset(self::ARABIC_CONNECTS_AFTER[$codepoint]);

            $joinsNext = $nextCodepoint !== null
                && isset(self::ARABIC_CONNECTS_BEFORE[$codepoint])
                && isset(self::ARABIC_CONNECTS_AFTER[$nextCodepoint]);

            if ($joinsPrevious && $joinsNext && isset($forms['m'])) {
                $shapedChars[] = $forms['m'];

                continue;
            }

            if ($joinsPrevious && isset($forms['f'])) {
                $shapedChars[] = $forms['f'];

                continue;
            }

            if ($joinsNext && isset($forms['b'])) {
                $shapedChars[] = $forms['b'];

                continue;
            }

            $shapedChars[] = $forms['i'] ?? $char;
        }

        return implode('', array_reverse($shapedChars));
    }

    /**
     * Resize an image to fit within maxW×maxH and return as base64 data URI.
     * Falls back to raw base64 if GD is unavailable.
     */
    public static function logoBase64(string $path, int $maxW, int $maxH): string
    {
        if (! file_exists($path)) {
            return '';
        }

        $raw = file_get_contents($path);
        if ($raw === false || $raw === '') {
            return '';
        }

        if (! function_exists('imagecreatefromstring')) {
            return 'data:image/png;base64,' . base64_encode($raw);
        }

        $src = @imagecreatefromstring($raw);
        if (! $src) {
            return 'data:image/png;base64,' . base64_encode($raw);
        }

        $w = imagesx($src);
        $h = imagesy($src);

        $scale = min($maxW / max($w, 1), $maxH / max($h, 1), 1.0);
        $nw    = max(1, (int) round($w * $scale));
        $nh    = max(1, (int) round($h * $scale));

        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefill($dst, 0, 0, $transparent);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        ob_start();
        imagepng($dst, null, 6);
        $data = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        return 'data:image/png;base64,' . base64_encode((string) $data);
    }

    private function parseMemoryLimit(string $limit): int
    {
        if ($limit === '-1') {
            return -1;
        }
        $unit  = strtolower(substr($limit, -1));
        $value = (int) $limit;
        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value,
        };
    }

    private function downloadPdfWithChromiumHtml(string $html, string $fileName): ?Response
    {
        $tempHtmlPath = null;
        $tempPdfPath = null;
        $tempProfileDir = null;

        try {
            $tempDir = sys_get_temp_dir();

            $tempHtmlPath = tempnam($tempDir, 'restv2-pdf-html-');
            $tempPdfPath = tempnam($tempDir, 'restv2-pdf-out-');
            $tempProfileDir = $tempDir . DIRECTORY_SEPARATOR . 'restv2-chromium-profile-' . bin2hex(random_bytes(8));

            File::ensureDirectoryExists($tempProfileDir);

            if (! is_string($tempHtmlPath) || ! is_string($tempPdfPath)) {
                throw new \RuntimeException('Unable to allocate temp files for PDF export.');
            }

            File::put($tempHtmlPath, $html);

            $process = new Process([
                $this->resolveChromiumBinary(),
                '--headless',
                '--disable-gpu',
                '--no-sandbox',
                '--disable-dev-shm-usage',
                '--no-first-run',
                '--no-default-browser-check',
                '--disable-background-networking',
                '--user-data-dir=' . $tempProfileDir,
                '--run-all-compositor-stages-before-draw',
                '--virtual-time-budget=10000',
                '--print-to-pdf=' . $tempPdfPath,
                '--print-to-pdf-no-header',
                '--no-pdf-header-footer',
                '--allow-file-access-from-files',
                'file://' . $tempHtmlPath,
            ], null, [
                'HOME' => $tempDir,
                'XDG_CONFIG_HOME' => $tempDir,
                'XDG_CACHE_HOME' => $tempDir,
            ]);

            $process->setTimeout(90);
            $process->run();

            if (! $process->isSuccessful() || ! File::exists($tempPdfPath)) {
                throw new \RuntimeException('Chromium PDF export failed. ' . $process->getErrorOutput());
            }

            $pdfBinary = File::get($tempPdfPath);

            if (! is_string($pdfBinary) || $pdfBinary === '') {
                throw new \RuntimeException('Generated PDF output is empty.');
            }

            File::delete($tempPdfPath);

            $downloadName = str_replace(["\r", "\n", '"'], '', $fileName);

            return response($pdfBinary, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                'Content-Length' => (string) strlen($pdfBinary),
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'X-PDF-Engine' => 'chromium',
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            if ($tempPdfPath && File::exists($tempPdfPath)) {
                File::delete($tempPdfPath);
            }

            return null;
        } finally {
            if ($tempHtmlPath && File::exists($tempHtmlPath)) {
                File::delete($tempHtmlPath);
            }

            if ($tempProfileDir && File::isDirectory($tempProfileDir)) {
                File::deleteDirectory($tempProfileDir);
            }
        }
    }

    private function resolveChromiumBinary(): string
    {
        $envBinary = trim((string) env('CHROMIUM_BINARY'));
        if ($envBinary !== '') {
            return $envBinary;
        }

        $candidates = [
            '/usr/lib/chromium/chromium',
            '/usr/bin/chromium-browser',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/google-chrome',
            '/usr/bin/chromium',
        ];

        foreach ($candidates as $candidate) {
            if (is_executable($candidate)) {
                return $candidate;
            }
        }

        return 'chromium';
    }
}
