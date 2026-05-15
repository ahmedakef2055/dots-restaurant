<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(app()->getLocale() === 'ar' ? 'فاتورة مشتريات' : 'Purchase Receipt'); ?> <?php echo e($purchase->purchase_number); ?></title>
    <style>
        :root {
            --paper-width: 80mm;
            --brown:  #5E7D67;
            --gold:   #A8C89A;
            --cream:  #F5F2EC;
            --text:   #3E2B23;
            --muted:  #6B4A35;
            --line:   #D8CCBC;
        }

        @page { size: 80mm auto; margin: 4mm; }
        * { box-sizing: border-box; }

        body {
            margin: 0; padding: 0;
            background: var(--cream);
            color: var(--text);
            font-family: "DejaVu Sans", "Courier New", monospace;
            font-size: 11px; line-height: 1.35;
        }

        .screen-toolbar {
            max-width: var(--paper-width); margin: 8px auto 0;
            display: flex; justify-content: center; gap: 8px;
        }
        .screen-toolbar button {
            border: 1px solid var(--line); border-radius: 8px;
            background: var(--brown); color: #fff;
            padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer;
        }

        .receipt {
            width: var(--paper-width); margin: 8px auto;
            background: #ffffff; border: 1px solid var(--line);
            border-radius: 8px; padding: 10px 8px;
        }

        .header {
            text-align: center; margin-bottom: 8px;
            padding-bottom: 8px; border-bottom: 2px solid var(--brown);
        }
        .logo { display: block; margin: 0 auto 6px; max-width: 28mm; max-height: 18mm; object-fit: contain; }
        .logo-name { display: block; margin: 0 auto 5px; max-width: 42mm; max-height: 16mm; object-fit: contain; }
        .restaurant-name { margin: 0; font-size: 14px; font-weight: 800; color: var(--brown); }
        .receipt-title {
            margin: 3px 0 0; display: inline-block;
            background: var(--gold); color: var(--text);
            font-size: 9px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.12em;
            padding: 2px 6px; border-radius: 10px;
        }

        .meta, .totals, .expense-block { margin-bottom: 8px; }

        .row { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin-bottom: 3px; }
        .row:last-child { margin-bottom: 0; }
        .label { color: var(--muted); white-space: nowrap; }
        .value { font-weight: 700; text-align: end; word-break: break-word; }

        .section-title {
            margin: 0 0 4px; padding: 3px 6px;
            background: var(--brown); color: #fff;
            border-radius: 4px; font-size: 10px; font-weight: 800;
        }

        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { padding: 4px 3px; border-bottom: 1px dashed var(--line); vertical-align: top; font-size: 10px; }
        th { font-weight: 800; color: var(--brown); text-align: start; }
        th.num, td.num { text-align: end; white-space: nowrap; }

        .totals { border-top: 2px solid var(--brown); padding-top: 6px; }
        .totals .row { margin-bottom: 4px; }
        .totals .grand {
            margin-top: 4px; padding: 5px 6px;
            background: var(--cream); border-radius: 6px;
            font-size: 12px; font-weight: 800; color: var(--brown);
        }

        .footer {
            margin-top: 8px; padding-top: 6px;
            border-top: 2px dashed var(--gold);
            text-align: center; color: var(--brown);
            font-size: 11px; font-weight: 800; letter-spacing: 0.08em;
        }
        .footer-tagline { margin-top: 3px; font-size: 9px; color: var(--muted); font-weight: 400; }

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
           THERMAL MODE — used when rendered via wkhtmltoimage for the ESC/POS
           printer. Thermal printers are monochrome: light colors (gold/cream)
           dither to white and disappear. We override to pure black-on-white
           with heavier weights, bigger fonts (legible after 576px raster),
           and solid black blocks for emphasis.
           ────────────────────────────────────────────────────────────────── */
        body.thermal {
            background: #fff !important;
            color: #000 !important;
            font-size: 22px !important;
            line-height: 1.4 !important;
            font-weight: 600;
            padding-bottom: 0 !important;
            margin-bottom: 0 !important;
        }
        body.thermal .receipt {
            width: 100% !important;
            margin: 0 !important;
            padding: 8px !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: #fff !important;
        }
        body.thermal .header {
            border-bottom: 3px solid #000 !important;
            padding-bottom: 10px !important;
            margin-bottom: 10px !important;
        }
        body.thermal .logo {
            max-width: 40mm !important;
            max-height: 30mm !important;
        }
        body.thermal .restaurant-name {
            font-size: 30px !important;
            color: #000 !important;
            font-weight: 900 !important;
        }
        body.thermal .receipt-title {
            background: #000 !important;
            color: #fff !important;
            font-size: 18px !important;
            padding: 4px 12px !important;
            border-radius: 0 !important;
            font-weight: 900 !important;
            letter-spacing: 0.18em !important;
        }
        body.thermal .row { margin-bottom: 5px !important; }
        body.thermal .label {
            color: #000 !important;
            font-weight: 700 !important;
        }
        body.thermal .value {
            color: #000 !important;
            font-weight: 800 !important;
        }
        body.thermal .section-title {
            background: #000 !important;
            color: #fff !important;
            font-size: 20px !important;
            padding: 5px 8px !important;
            border-radius: 0 !important;
            margin: 8px 0 6px !important;
            font-weight: 900 !important;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        body.thermal table { margin-bottom: 8px !important; }
        body.thermal th, body.thermal td {
            font-size: 20px !important;
            padding: 6px 3px !important;
            border-bottom: 1px solid #000 !important;
            color: #000 !important;
        }
        body.thermal th {
            font-weight: 900 !important;
            color: #000 !important;
            border-bottom: 2px solid #000 !important;
            text-transform: uppercase;
            font-size: 18px !important;
        }
        body.thermal .totals {
            border-top: 3px solid #000 !important;
            padding-top: 8px !important;
        }
        body.thermal .totals .grand {
            background: #000 !important;
            color: #fff !important;
            font-size: 26px !important;
            font-weight: 900 !important;
            padding: 8px !important;
            border-radius: 0 !important;
            margin-top: 6px !important;
        }
        body.thermal .totals .grand span { color: #fff !important; }
        body.thermal .footer {
            border-top: 2px dashed #000 !important;
            color: #000 !important;
            font-size: 22px !important;
            margin-top: 12px !important;
            padding-top: 8px !important;
        }
        body.thermal .footer-tagline {
            color: #000 !important;
            font-size: 16px !important;
            font-weight: 700 !important;
        }
    </style>
</head>

<body class="<?php echo e((isset($isDirectPrint) && $isDirectPrint) ? 'thermal' : ''); ?>">
    <?php
    $isArabic = app()->getLocale() === 'ar';
    $requestType = strtolower((string) ($purchase->request_type ?: 'inventory'));

    $labels = $isArabic
        ? [
            'receipt' => 'فاتورة مشتريات حرارية',
            'purchase_number' => 'رقم الشراء',
            'purchase_date' => 'تاريخ الشراء',
            'request_type' => 'نوع الطلب',
            'supplier' => 'المورد',
            'processed_by' => 'تمت بواسطة',
            'payment' => 'طريقة الدفع',
            'invoice_number' => 'رقم الفاتورة',
            'items' => 'عناصر الشراء',
            'item_name' => 'الصنف',
            'item_qty' => 'ك',
            'item_unit_price' => 'سعر الوحدة',
            'item_expiry' => 'الصلاحية',
            'item_total' => 'الإجمالي',
            'expense_details' => 'تفاصيل المصروف',
            'expense_title' => 'بند المصروف',
            'expense_reference' => 'مرجع/رقم الفاتورة',
            'expense_amount' => 'قيمة المصروف',
            'subtotal' => 'الإجمالي قبل الإضافات',
            'tax' => 'الضريبة',
            'discount' => 'الخصم',
            'total' => 'الإجمالي النهائي',
            'dash' => '-',
            'thanks' => 'شكرا لتعاملكم',
            'type_inventory' => 'شراء مخزون',
            'type_expense' => 'مصروف عام',
            'payment_cash' => 'كاش',
            'payment_credit' => 'آجل',
            'system' => 'النظام',
        ]
        : [
            'receipt' => 'Thermal Purchase Receipt',
            'purchase_number' => 'Purchase #',
            'purchase_date' => 'Purchase Date',
            'request_type' => 'Request Type',
            'supplier' => 'Supplier',
            'processed_by' => 'Processed By',
            'payment' => 'Payment',
            'invoice_number' => 'Invoice Number',
            'items' => 'Purchased Items',
            'item_name' => 'Item',
            'item_qty' => 'Qty',
            'item_unit_price' => 'Unit Price',
            'item_expiry' => 'Expiry',
            'item_total' => 'Total',
            'expense_details' => 'Expense Details',
            'expense_title' => 'Expense Title',
            'expense_reference' => 'Invoice Reference',
            'expense_amount' => 'Expense Amount',
            'subtotal' => 'Subtotal Before Additions',
            'tax' => 'Tax',
            'discount' => 'Discount',
            'total' => 'Grand Total',
            'dash' => '-',
            'thanks' => 'Thank you',
            'type_inventory' => 'Inventory Purchase',
            'type_expense' => 'General Expense',
            'payment_cash' => 'Cash',
            'payment_credit' => 'Credit',
            'system' => 'System',
        ];

    $requestTypeLabel = $requestType === 'general_expense'
        ? $labels['type_expense']
        : $labels['type_inventory'];

    $paymentLabel = ($purchase->payment_method ?? 'cash') === 'credit'
        ? $labels['payment_credit']
        : $labels['payment_cash'];

    $logoPath = collect(['images/logo.png','images/logo.jpg','logo.png','storage/logo.png'])->first(
        static fn(string $c): bool => file_exists(public_path($c))
    );
    ?>

    <?php if(!isset($isDirectPrint) || !$isDirectPrint): ?>
    <div class="screen-toolbar">
        <button type="button" id="btn-print" onclick="triggerDirectPrint()"><?php echo e($isArabic ? 'طباعة' : 'Print'); ?></button>
        <button type="button" id="btn-close" onclick="window.close()" style="background:#6B4A35"><?php echo e($isArabic ? 'إغلاق' : 'Close'); ?></button>
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
            btn.innerText = '<?php echo e($isArabic ? 'جاري الطباعة...' : 'Printing...'); ?>';

            fetch('<?php echo e(route('purchases.direct-print', $purchase->id)); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                }
            })
            .then(function(res) {
                if (res.ok) {
                    showToast('<?php echo e($isArabic ? 'تم إرسال الفاتورة للطابعة بنجاح \u2714' : 'Sent to printer successfully \u2714'); ?>', 'success');
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

    <main class="receipt">
        <header class="header">
            <?php if($logoPath): ?>
            <img src="<?php echo e(asset($logoPath)); ?>" alt="<?php echo e(config('app.name')); ?>" class="logo">
            <?php endif; ?>
            <span class="receipt-title"><?php echo e($labels['receipt']); ?></span>
        </header>

        <section class="meta">
            <div class="row">
                <span class="label"><?php echo e($labels['purchase_number']); ?></span>
                <span class="value"><?php echo e($purchase->purchase_number); ?></span>
            </div>
            <div class="row">
                <span class="label"><?php echo e($labels['purchase_date']); ?></span>
                <span class="value"><?php echo e($purchase->purchase_date?->format('Y-m-d') ?? $labels['dash']); ?></span>
            </div>
            <div class="row">
                <span class="label"><?php echo e($labels['request_type']); ?></span>
                <span class="value"><?php echo e($requestTypeLabel); ?></span>
            </div>
            <div class="row">
                <span class="label"><?php echo e($labels['supplier']); ?></span>
                <span class="value"><?php echo e($purchase->supplier?->name ?? $labels['dash']); ?></span>
            </div>
            <div class="row">
                <span class="label"><?php echo e($labels['processed_by']); ?></span>
                <span class="value"><?php echo e($purchase->user?->name ?? $labels['system']); ?></span>
            </div>
            <div class="row">
                <span class="label"><?php echo e($labels['payment']); ?></span>
                <span class="value"><?php echo e($paymentLabel); ?></span>
            </div>
            <div class="row">
                <span class="label"><?php echo e($labels['invoice_number']); ?></span>
                <span class="value"><?php echo e($purchase->invoice_number ?: $labels['dash']); ?></span>
            </div>
        </section>

        <?php if($requestType === 'inventory'): ?>
        <section>
            <p class="section-title"><?php echo e($labels['items']); ?></p>
            <table>
                <thead>
                    <tr>
                        <th><?php echo e($labels['item_name']); ?></th>
                        <th class="num"><?php echo e($labels['item_qty']); ?></th>
                        <th class="num"><?php echo e($labels['item_unit_price']); ?></th>
                        <th><?php echo e($labels['item_expiry']); ?></th>
                        <th class="num"><?php echo e($labels['item_total']); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $purchase->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($item->ingredient_name); ?></td>
                        <td class="num"><?php echo e(number_format((float) $item->quantity, 3)); ?></td>
                        <td class="num"><?php echo e(\App\Support\CurrencyFormatter::format((float) $item->unit_cost)); ?></td>
                        <td><?php echo e($item->expiry_date?->format('Y-m-d') ?? $labels['dash']); ?></td>
                        <td class="num"><?php echo e(\App\Support\CurrencyFormatter::format((float) $item->line_total)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5"><?php echo e($labels['dash']); ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
        <?php else: ?>
        <section class="expense-block">
            <p class="section-title"><?php echo e($labels['expense_details']); ?></p>
            <div class="row">
                <span class="label"><?php echo e($labels['expense_title']); ?></span>
                <span class="value"><?php echo e($purchase->expense_title ?: $labels['dash']); ?></span>
            </div>
            <div class="row">
                <span class="label"><?php echo e($labels['expense_reference']); ?></span>
                <span class="value"><?php echo e($purchase->expense_invoice_reference ?: $labels['dash']); ?></span>
            </div>
            <div class="row">
                <span class="label"><?php echo e($labels['expense_amount']); ?></span>
                <span class="value"><?php echo e(\App\Support\CurrencyFormatter::format((float) ($purchase->expense_amount ?? $purchase->total))); ?></span>
            </div>
        </section>
        <?php endif; ?>

        <section class="totals">
            <div class="row">
                <span class="label"><?php echo e($labels['subtotal']); ?></span>
                <span class="value"><?php echo e(\App\Support\CurrencyFormatter::format((float) $purchase->subtotal)); ?></span>
            </div>
            <div class="row">
                <span class="label"><?php echo e($labels['tax']); ?></span>
                <span class="value"><?php echo e(\App\Support\CurrencyFormatter::format((float) $purchase->tax_amount)); ?></span>
            </div>
            <div class="row">
                <span class="label"><?php echo e($labels['discount']); ?></span>
                <span class="value"><?php echo e(\App\Support\CurrencyFormatter::format((float) $purchase->discount_amount)); ?></span>
            </div>
            <div class="row grand">
                <span><?php echo e($labels['total']); ?></span>
                <span><?php echo e(\App\Support\CurrencyFormatter::format((float) $purchase->total)); ?></span>
            </div>
        </section>

        <footer class="footer">
            <?php echo e($labels['thanks']); ?>

            <div class="footer-tagline"><?php echo e(config('app.name', 'Point 88')); ?></div>
        </footer>
    </main>
</body>

</html><?php /**PATH /var/www/dots-main/resources/views/purchases/invoice.blade.php ENDPATH**/ ?>