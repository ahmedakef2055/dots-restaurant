<!doctype html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($receipt['labels']['title'] ?? __('ui.pos.shift.receipt_title')); ?></title>
    <style>
        :root {
            --brown: #5E7D67;
            --gold:  #A8C89A;
            --cream: #EEF3EA;
            --text:  #3E2B23;
            --muted: #6B4A35;
            --line:  #C4D3BD;
        }
        * { box-sizing: border-box; }
        body {
            font-family: "DejaVu Sans", sans-serif;
            margin: 0; padding: 16px;
            background: var(--cream); color: var(--text);
        }
        .toolbar {
            max-width: 420px; margin: 0 auto 10px;
            display: flex; justify-content: center; gap: 8px;
        }
        .toolbar button {
            background: var(--brown); color: #fff;
            border: none; border-radius: 8px;
            padding: 7px 18px; font-size: 13px; font-weight: 700; cursor: pointer;
        }
        .receipt {
            max-width: 420px; margin: 0 auto;
            background: #fff; border: 1px solid var(--line);
            border-radius: 12px; overflow: hidden;
            box-shadow: 0 4px 16px rgba(94,125,103,0.10);
        }
        .brand-header {
            background: var(--brown); padding: 14px 16px 12px; text-align: center;
        }
        .brand-logo {
            max-height: 40px; max-width: 90px; object-fit: contain;
            display: block; margin: 0 auto 6px;
        }
        .brand-logo-name {
            max-height: 48px; max-width: 180px; object-fit: contain;
            display: block; margin: 0 auto 5px; filter: brightness(0) invert(1);
        }
        .brand-name { color: #fff; font-size: 16px; font-weight: 800; margin: 0; }
        .brand-badge {
            display: inline-block; background: var(--gold); color: var(--text);
            font-size: 9px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.12em;
            padding: 2px 8px; border-radius: 10px; margin-top: 5px;
        }
        .body { padding: 14px 16px; }
        table { width: 100%; border-collapse: collapse; }
        tr { border-bottom: 1px solid var(--line); }
        tr:last-child { border-bottom: none; }
        td { padding: 8px 0; font-size: 13px; color: var(--text); }
        td:first-child { color: var(--muted); }
        td:last-child { text-align: right; font-weight: 700; }
        html[dir="rtl"] td:last-child { text-align: left; }
        .total-row td {
            border-top: 2px solid var(--brown); border-bottom: none;
            padding-top: 10px; font-size: 15px; font-weight: 800; color: var(--brown);
        }
        .diff-pos td:last-child { color: #5C7A3A; }
        .diff-neg td:last-child { color: #B5341C; }
        .note {
            margin-top: 10px; padding: 8px 10px;
            background: var(--cream);
            border-left: 3px solid var(--gold); border-radius: 4px;
            font-size: 11.5px; color: var(--muted);
        }
        html[dir="rtl"] .note { border-left: none; border-right: 3px solid var(--gold); }
        .footer {
            padding: 10px 16px; background: var(--cream);
            border-top: 2px dashed var(--gold);
            text-align: center; font-size: 12px; font-weight: 800; color: var(--brown);
        }
        
        /* ── Toast notification ── */
        #print-toast {
            display: none;
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            min-width: 260px;
            max-width: 90vw;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            z-index: 9999;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
            transition: opacity 0.3s ease;
        }
        #print-toast.success { background: #1a7f4b; color: #fff; }
        #print-toast.error   { background: #b91c1c; color: #fff; }
        #print-toast.info    { background: #1d4ed8; color: #fff; }

        /* ──────────────────────────────────────────────────────────────────
           THERMAL MODE
           ────────────────────────────────────────────────────────────────── */
        body.thermal {
            background: #fff !important;
            color: #000 !important;
            font-size: 22px !important;
            line-height: 1.4 !important;
            font-weight: 600;
            padding: 0 !important;
            margin: 0 !important;
        }
        body.thermal .receipt {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 8px !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: #fff !important;
            box-shadow: none !important;
        }
        body.thermal .brand-header {
            background: #fff !important;
            border-bottom: 3px solid #000 !important;
            padding: 0 0 10px !important;
            margin-bottom: 10px !important;
        }
        body.thermal .brand-logo {
            max-width: 40mm !important;
            max-height: 30mm !important;
        }
        body.thermal .brand-name {
            font-size: 30px !important;
            color: #000 !important;
            font-weight: 900 !important;
        }
        body.thermal .brand-badge {
            background: #000 !important;
            color: #fff !important;
            font-size: 18px !important;
            padding: 4px 12px !important;
            border-radius: 0 !important;
            font-weight: 900 !important;
            letter-spacing: 0.18em !important;
            margin-top: 10px !important;
        }
        body.thermal .body { padding: 0 !important; }
        body.thermal table { margin-bottom: 8px !important; }
        body.thermal td {
            font-size: 26px !important;
            padding: 6px 0 !important;
            border-bottom: 1px dashed #000 !important;
            color: #000 !important;
        }
        body.thermal td:first-child { font-weight: 700 !important; color: #000 !important; }
        body.thermal td:last-child { font-weight: 900 !important; color: #000 !important; }
        body.thermal .total-row td {
            border-top: 3px solid #000 !important;
            border-bottom: none !important;
            font-size: 26px !important;
            font-weight: 900 !important;
            color: #000 !important;
            padding-top: 10px !important;
        }
        body.thermal .diff-pos td:last-child { color: #000 !important; font-weight: 900 !important; }
        body.thermal .diff-neg td:last-child { color: #000 !important; font-weight: 900 !important; }
        body.thermal .note {
            background: #fff !important;
            border: 2px dashed #000 !important;
            color: #000 !important;
            font-size: 18px !important;
            font-weight: 700 !important;
            border-radius: 0 !important;
            margin-top: 15px !important;
            text-align: center !important;
        }
        body.thermal .footer {
            background: #fff !important;
            border-top: 2px dashed #000 !important;
            color: #000 !important;
            font-size: 22px !important;
            margin-top: 12px !important;
            padding: 10px 0 0 !important;
        }
    </style>
</head>
<body class="<?php echo e((isset($isDirectPrint) && $isDirectPrint) ? 'thermal' : ''); ?>">
    <?php
        $labels    = $receipt['labels'] ?? [];
        $startTime = $receipt['start_time'] ?? null;
        $endTime   = $receipt['end_time']   ?? null;
        $fmt = static function ($v): string {
            if (!$v) return '-';
            try { return \Carbon\Carbon::parse($v)->format('Y-m-d h:i A'); }
            catch (\Throwable) { return '-'; }
        };
        $shiftTime = $fmt($startTime) . ' → ' . $fmt($endTime);
        $diff      = (float) ($receipt['difference'] ?? 0);
        $logoPath     = collect(['images/logo.png','images/logo.jpg','logo.png'])->first(
            fn($c) => file_exists(public_path($c))
        );
    ?>

    <?php if(!isset($isDirectPrint) || !$isDirectPrint): ?>
    <div class="toolbar">
        <button type="button" id="btn-print" onclick="triggerDirectPrint()">
            <?php echo e(app()->getLocale() === 'ar' ? 'طباعة' : 'Print'); ?>

        </button>
        <button type="button" id="btn-close" onclick="window.close()" style="background:#6B4A35; margin-inline-start: 8px;">
            <?php echo e(app()->getLocale() === 'ar' ? 'إغلاق' : 'Close'); ?>

        </button>
    </div>
    <?php endif; ?>

    <div id="print-toast"></div>

    <script>
        function showToast(message, type, durationMs) {
            var toast = document.getElementById('print-toast');
            if(!toast) return;
            toast.textContent = message;
            toast.className = type;          // 'success' | 'error' | 'info'
            toast.style.display = 'block';
            toast.style.opacity = '1';
            clearTimeout(toast._timer);
            toast._timer = setTimeout(function () {
                toast.style.opacity = '0';
                setTimeout(function () { toast.style.display = 'none'; }, 350);
            }, durationMs || 3500);
        }

        function triggerDirectPrint() {
            var btn = document.getElementById('btn-print');
            if (!btn) return;
            var originalText = btn.innerText;
            if (btn.disabled) return;
            btn.disabled = true;
            btn.innerText = '<?php echo e(app()->getLocale() === 'ar' ? 'جاري الطباعة...' : 'Printing...'); ?>';

            fetch('<?php echo e(route('reports.shift-logs.direct-print', request()->route('shiftLog') ?? 0)); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                }
            })
            .then(function(res) {
                if (res.ok) {
                    showToast('<?php echo e(app()->getLocale() === 'ar' ? 'تم إرسال الفاتورة للطابعة بنجاح \u2714' : 'Sent to printer successfully \u2714'); ?>', 'success');
                } else {
                    return res.json().then(function(data) { throw new Error(data.message || 'Server Error'); });
                }
            })
            .catch(function(err) {
                showToast('\u274c ' + err.message, 'error', 5000);
            })
            .finally(function() {
                btn.innerText = originalText;
                btn.disabled = false;
            });
        }

        <?php if(!isset($isDirectPrint) || !$isDirectPrint): ?>
        document.addEventListener('DOMContentLoaded', function() {
            triggerDirectPrint();
        });
        <?php endif; ?>
    </script>

    <div class="receipt">
        <div class="brand-header">
            <?php if($logoPath): ?>
                <img src="<?php echo e(asset($logoPath)); ?>" alt="<?php echo e(config('app.name')); ?>" class="brand-logo">
            <?php endif; ?>
            <span class="brand-badge"><?php echo e($labels['title'] ?? __('ui.pos.shift.receipt_title')); ?></span>
        </div>

        <div class="body">
            <table>
                <?php if(!empty($receipt['shift_id'])): ?>
                <tr>
                    <td><?php echo e(app()->getLocale() === 'ar' ? 'رقم الشيفت' : 'Shift #'); ?></td>
                    <td>#<?php echo e($receipt['shift_id']); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td><?php echo e($labels['cashier'] ?? __('ui.pos.shift.cashier_name')); ?></td>
                    <td><?php echo e($receipt['cashier_name'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><?php echo e($labels['shift_time'] ?? __('ui.pos.shift.shift_time')); ?></td>
                    <td><?php echo e($shiftTime); ?></td>
                </tr>
                <tr>
                    <td><?php echo e($labels['opening_cash'] ?? __('ui.pos.shift.opening_cash')); ?></td>
                    <td><?php echo e(\App\Support\CurrencyFormatter::format((float) ($receipt['opening_cash'] ?? 0))); ?></td>
                </tr>
                <tr>
                    <td><?php echo e($labels['total_sales'] ?? __('ui.pos.shift.total_sales')); ?></td>
                    <td><?php echo e(\App\Support\CurrencyFormatter::format((float) ($receipt['total_sales'] ?? 0))); ?></td>
                </tr>
                <tr>
                    <td><?php echo e($labels['expected_cash'] ?? __('ui.pos.shift.expected_cash')); ?></td>
                    <td><?php echo e(\App\Support\CurrencyFormatter::format((float) ($receipt['expected_cash'] ?? 0))); ?></td>
                </tr>
                <tr>
                    <td><?php echo e($labels['actual_cash'] ?? __('ui.pos.shift.actual_cash')); ?></td>
                    <td><?php echo e(\App\Support\CurrencyFormatter::format((float) ($receipt['actual_cash'] ?? 0))); ?></td>
                </tr>
                <?php if(($receipt['tips'] ?? 0) > 0): ?>
                <tr>
                    <td><?php echo e($labels['tips'] ?? __('ui.pos.shift.tips')); ?></td>
                    <td><?php echo e(\App\Support\CurrencyFormatter::format((float) $receipt['tips'])); ?></td>
                </tr>
                <?php endif; ?>
                <?php if(($receipt['cash_overage'] ?? 0) > 0): ?>
                <tr class="diff-pos">
                    <td><?php echo e($labels['cash_overage'] ?? __('ui.pos.shift.cash_overage')); ?></td>
                    <td>+<?php echo e(\App\Support\CurrencyFormatter::format((float) $receipt['cash_overage'])); ?></td>
                </tr>
                <?php endif; ?>
                <?php if(($receipt['cash_shortage'] ?? 0) > 0): ?>
                <tr class="diff-neg">
                    <td><?php echo e($labels['cash_shortage'] ?? __('ui.pos.shift.cash_shortage')); ?></td>
                    <td>-<?php echo e(\App\Support\CurrencyFormatter::format((float) $receipt['cash_shortage'])); ?></td>
                </tr>
                <?php endif; ?>
                <tr class="total-row">
                    <td><?php echo e(app()->getLocale() === 'ar' ? 'الفرق الكلي' : 'Net Difference'); ?></td>
                    <td><?php echo e(\App\Support\CurrencyFormatter::format(abs($diff))); ?><?php echo e($diff > 0 ? ' ▲' : ($diff < 0 ? ' ▼' : '')); ?></td>
                </tr>
            </table>
            <?php if(!empty($labels['tips_note'])): ?>
            <div class="note"><?php echo e($labels['tips_note']); ?></div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <?php echo e(app()->getLocale() === 'ar' ? 'شكرًا — نهاية الشيفت' : 'Shift Closed — Thank You'); ?>

        </div>
    </div>
</body>
</html>
<?php /**PATH /var/www/dots-main/resources/views/reports/shift-log-receipt.blade.php ENDPATH**/ ?>