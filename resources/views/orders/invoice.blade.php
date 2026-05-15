<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $order->order_number }}</title>
    <style>
        :root {
            --paper-width: 80mm;
            --brown:  #5E7D67;
            --gold:   #A8C89A;
            --cream:  #EEF3EA;
            --text:   #3E2B23;
            --muted:  #6B4A35;
            --line:   #C4D3BD;
        }

        @page {
            size: 80mm auto;
            margin: 0;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            background: var(--cream);
            color: var(--text);
            font-family: "DejaVu Sans", "Courier New", monospace;
            font-size: 13px;
            line-height: 1.45;
        }

        .screen-toolbar {
            max-width: var(--paper-width);
            margin: 8px auto 0;
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .screen-toolbar button {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--brown);
            color: #fff;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .screen-toolbar button:hover {
            background: var(--gold);
            color: var(--text);
            border-color: var(--gold);
        }

        .receipt {
            width: var(--paper-width);
            margin: 8px auto;
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 10px 8px;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--brown);
        }

        .logo {
            display: block;
            margin: 0 auto 4px;
            max-width: 28mm;
            max-height: 20mm;
            object-fit: contain;
        }

        .logo-name {
            display: block;
            margin: 0 auto 5px;
            max-width: 45mm;
            max-height: 18mm;
            object-fit: contain;
        }

        .restaurant-name {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.2px;
            color: var(--brown);
        }

        .receipt-title {
            margin: 3px 0 0;
            background: var(--gold);
            color: var(--text);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            padding: 2px 6px;
            border-radius: 10px;
            display: inline-block;
        }

        /* ── Meta rows ── */
        .meta, .delivery-block, .totals { margin-bottom: 8px; }

        .row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 3px;
        }

        .row:last-child { margin-bottom: 0; }

        .label { color: var(--muted); white-space: nowrap; }
        .value { font-weight: 700; text-align: end; word-break: break-word; }

        /* ── Section title ── */
        .section-title {
            margin: 0 0 4px;
            padding: 3px 6px;
            background: var(--brown);
            color: #fff;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 800;
        }

        /* ── Items table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        th, td {
            padding: 4px 3px;
            border-bottom: 1px dashed var(--line);
            vertical-align: top;
            font-size: 12px;
        }

        th {
            font-weight: 800;
            color: var(--brown);
            text-align: start;
        }

        th.num, td.num {
            text-align: end;
            white-space: nowrap;
        }

        td.note { color: var(--muted); min-width: 54px; }

        /* ── Totals ── */
        .totals {
            border-top: 2px solid var(--brown);
            padding-top: 6px;
        }

        .totals .row { margin-bottom: 4px; }

        .totals .grand {
            margin-top: 4px;
            padding: 5px 6px;
            background: var(--cream);
            border-radius: 6px;
            font-size: 15px;
            font-weight: 800;
            color: var(--brown);
        }

        /* ── Footer ── */
        .footer {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 2px dashed var(--gold);
            text-align: center;
            color: var(--brown);
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.08em;
        }

        .footer-tagline {
            margin-top: 3px;
            font-size: 11px;
            color: var(--muted);
            font-weight: 400;
        }

        /* ── Print ── */
        @media print {
            html, body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 80mm !important;
                font-size: 13px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .screen-toolbar { display: none !important; }
            .receipt {
                margin: 0 !important;
                border: 0 !important;
                border-radius: 0 !important;
                padding: 6px 6px !important;
                width: 80mm !important;
                box-shadow: none !important;
            }
            th, td { font-size: 12px !important; }
            .totals .grand { font-size: 15px !important; }
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
           THERMAL MODE — used when rendered via wkhtmltoimage for the ESC/POS
           printer. Thermal printers are monochrome: light colors (gold/cream)
           dither to white and disappear. We override to pure black-on-white
           with heavier weights, bigger fonts (legible after 576px raster),
           and solid black blocks for emphasis.
           ────────────────────────────────────────────────────────────────── */
        body.thermal {
            background: #fff !important;
            color: #000 !important;
            font-size: 28px !important;
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
        body.thermal .logo-name {
            max-width: 60mm !important;
            max-height: 24mm !important;
        }
        body.thermal .restaurant-name {
            font-size: 30px !important;
            color: #000 !important;
            font-weight: 900 !important;
        }
        body.thermal .receipt-title {
            background: #000 !important;
            color: #fff !important;
            font-size: 24px !important;
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
            font-size: 26px !important;
            padding: 5px 8px !important;
            border-radius: 0 !important;
            margin: 8px 0 6px !important;
            font-weight: 900 !important;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        body.thermal table { margin-bottom: 8px !important; }
        body.thermal th, body.thermal td {
            font-size: 26px !important;
            padding: 6px 3px !important;
            border-bottom: 1px solid #000 !important;
            color: #000 !important;
        }
        body.thermal th {
            font-weight: 900 !important;
            color: #000 !important;
            border-bottom: 2px solid #000 !important;
            text-transform: uppercase;
            font-size: 24px !important;
        }
        body.thermal td.note { color: #000 !important; }
        body.thermal .totals {
            border-top: 3px solid #000 !important;
            padding-top: 8px !important;
        }
        body.thermal .totals .grand {
            background: #000 !important;
            color: #fff !important;
            font-size: 32px !important;
            font-weight: 900 !important;
            padding: 8px !important;
            border-radius: 0 !important;
            margin-top: 6px !important;
        }
        body.thermal .totals .grand span { color: #fff !important; }
        body.thermal .footer {
            border-top: 2px dashed #000 !important;
            color: #000 !important;
            font-size: 28px !important;
            margin-top: 12px !important;
            padding-top: 8px !important;
        }
        body.thermal .footer-tagline {
            color: #000 !important;
            font-size: 16px !important;
            font-weight: 700 !important;
        }
    </style>
    @if(isset($isDirectPrint) && $isDirectPrint)
    <style>
        html, body.thermal {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        body.thermal .screen-toolbar { display: none !important; }
        body.thermal .receipt {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 8px 6px !important;
            border: none !important;
            border-radius: 0 !important;
            background: #fff !important;
        }
    </style>
    @endif
</head>

<body class="{{ (isset($isDirectPrint) && $isDirectPrint) ? 'thermal' : '' }}">
    @php
    $isArabic = false;

    $labels = $isArabic
        ? [
            'receipt' => 'فاتورة حرارية',
            'order_number' => 'رقم الطلب',
            'order_serial' => 'كود الفاتورة',
            'order_date' => 'تاريخ الأوردر',
            'order_type' => 'نوع الأوردر',
            'table' => 'رقم الترابيزة',
            'cashier' => 'الكاشير',
            'delivery_data' => 'بيانات الدليفري',
            'customer_name' => 'اسم العميل',
            'customer_phone' => 'رقم الموبايل',
            'customer_address' => 'العنوان',
            'items' => 'المنتجات',
            'item_name' => 'الأوردر',
            'item_note' => 'الملاحظة',
            'item_qty' => 'ك',
            'item_unit_price' => 'سعر القطعة',
            'item_total' => 'الإجمالي',
            'subtotal' => 'الإجمالي قبل الخصم',
            'discount' => 'قيمة الخصم',
            'total' => 'المبلغ بعد الخصم',
            'dash' => '-',
            'payment_method' => 'طريقة الدفع',
            'thanks' => 'شكرًا لزيارتكم',
            'type_dine_in' => 'داخل الصالة',
            'type_takeaway' => 'تيك أواي',
            'type_delivery' => 'ديلفري',
        ]
        : [
            'receipt' => 'Thermal Receipt',
            'order_number' => 'Order #',
            'order_serial' => 'Order Ref',
            'order_date' => 'Date',
            'order_type' => 'Type',
            'table' => 'Table',
            'cashier' => 'Cashier',
            'delivery_data' => 'Delivery Details',
            'customer_name' => 'Customer',
            'customer_phone' => 'Mobile',
            'customer_address' => 'Address',
            'items' => 'Items',
            'item_name' => 'Item',
            'item_note' => 'Note',
            'item_qty' => 'Qty',
            'item_unit_price' => 'Unit Price',
            'item_total' => 'Total',
            'subtotal' => 'Subtotal',
            'discount' => 'Discount',
            'total' => 'Total',
            'dash' => '-',
            'payment_method' => 'Payment',
            'thanks' => 'Thank You!',
            'type_dine_in' => 'Dine-in',
            'type_takeaway' => 'Takeaway',
            'type_delivery' => 'Delivery',
        ];

    $orderTypeLabel = match ((string) $order->order_type) {
        'dine_in' => $labels['type_dine_in'],
        'delivery' => $labels['type_delivery'],
        default => $labels['type_takeaway'],
    };

    $tableName = (string) ($order->restaurantTable?->name ?? $labels['dash']);
    $customerName    = trim((string) ($order->customer?->full_name ?? $order->customer?->first_name ?? ''));
    $customerPhone   = trim((string) ($order->customer?->phone ?? ''));
    $customerAddress = trim((string) ($order->customer?->address ?? ''));

    /* Embed logo as base64 for reliable cross-engine rendering */
    $__logoB64  = '';
    $__logoFile = public_path('images/logo.png');
    if (file_exists($__logoFile)) {
        $__logoB64 = 'data:image/png;base64,' . base64_encode(file_get_contents($__logoFile));
    }
    @endphp

    @if(!isset($isDirectPrint) || !$isDirectPrint)
    <div class="screen-toolbar">
        <button type="button" id="btn-print" onclick="triggerDirectPrint()">{{ $isArabic ? 'طباعة' : 'Print' }}</button>
        <button type="button" id="btn-close" onclick="window.close()" style="background:#6B4A35">{{ $isArabic ? 'إغلاق' : 'Close' }}</button>
    </div>
    @endif

    <div id="print-toast"></div>

    <script>
        function showToast(message, type, durationMs) {
            var toast = document.getElementById('print-toast');
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
            btn.innerText = 'Printing\u2026';
            btn.disabled = true;
            showToast('Sending to printer\u2026', 'info', 15000);

            fetch('{{ route('orders.direct-print', $order->order_serial) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    showToast('\u2705 ' + data.message, 'success', 4000);
                } else {
                    var msg = '\u274c ' + (data.message || 'Print failed');
                    if (data.reason) msg += '\n' + data.reason;
                    showToast(msg, 'error', 7000);
                }
            })
            .catch(function(err) {
                showToast('\u274c Network error — could not reach server.', 'error', 5000);
            })
            .finally(function() {
                btn.innerText = originalText;
                btn.disabled = false;
            });
        }

        @if(!isset($isDirectPrint) || !$isDirectPrint)
        document.addEventListener('DOMContentLoaded', function() {
            triggerDirectPrint();
        });
        @endif
    </script>

    <main class="receipt">

        {{-- Brand header --}}
        <header class="header">
            @if($__logoB64)
                <img src="{{ $__logoB64 }}" alt="{{ config('app.name') }}" class="logo">
            @endif
            <span class="receipt-title">{{ $labels['receipt'] }}</span>
        </header>

        {{-- Order meta --}}
        <section class="meta">
            @if($order->order_daily_number)
            <div class="row">
                <span class="label">{{ $labels['order_number'] }}</span>
                <span class="value" style="font-weight:700;font-size:1.1em">#{{ $order->order_daily_number }}</span>
            </div>
            @endif
            <div class="row">
                <span class="label">{{ $labels['order_serial'] ?? 'Serial' }}</span>
                <span class="value">{{ $order->order_number }}</span>
            </div>
            <div class="row">
                <span class="label">{{ $labels['order_date'] }}</span>
                <span class="value">{{ $order->created_at?->format('Y-m-d g:i A') }}</span>
            </div>
            <div class="row">
                <span class="label">{{ $labels['order_type'] }}</span>
                <span class="value">{{ $orderTypeLabel }}</span>
            </div>
            <div class="row">
                <span class="label">{{ $labels['table'] }}</span>
                <span class="value">{{ $order->order_type === 'dine_in' ? $tableName : $labels['dash'] }}</span>
            </div>
            <div class="row">
                <span class="label">{{ $labels['cashier'] }}</span>
                <span class="value">{{ $order->user?->name ?? ($isArabic ? 'النظام' : 'System') }}</span>
            </div>
            @if($order->shift_id)
            <div class="row">
                <span class="label">{{ $isArabic ? 'رقم الشيفت' : 'Shift #' }}</span>
                <span class="value">#{{ $order->shift_id }}</span>
            </div>
            @endif
        </section>

        {{-- Delivery --}}
        @if($order->order_type === 'delivery')
        <section class="delivery-block">
            <p class="section-title">{{ $labels['delivery_data'] }}</p>
            <div class="row">
                <span class="label">{{ $labels['customer_name'] }}</span>
                <span class="value">{{ $customerName !== '' ? $customerName : $labels['dash'] }}</span>
            </div>
            <div class="row">
                <span class="label">{{ $labels['customer_phone'] }}</span>
                <span class="value">{{ $customerPhone !== '' ? $customerPhone : $labels['dash'] }}</span>
            </div>
            <div class="row">
                <span class="label">{{ $labels['customer_address'] }}</span>
                <span class="value">{{ $customerAddress !== '' ? $customerAddress : $labels['dash'] }}</span>
            </div>
        </section>
        @endif

        {{-- Items --}}
        <section>
            <p class="section-title">{{ $labels['items'] }}</p>
            <table>
                <thead>
                    <tr>
                        <th>{{ $labels['item_name'] }}</th>
                        <th>{{ $labels['item_note'] }}</th>
                        <th class="num">{{ $labels['item_qty'] }}</th>
                        <th class="num">{{ $labels['item_unit_price'] }}</th>
                        <th class="num">{{ $labels['item_total'] }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td class="note">{{ trim((string) $item->notes) !== '' ? $item->notes : $labels['dash'] }}</td>
                        <td class="num">{{ (int) $item->quantity }}</td>
                        <td class="num">{{ \App\Support\CurrencyFormatter::format((float) $item->unit_price) }}</td>
                        <td class="num">{{ \App\Support\CurrencyFormatter::format((float) $item->line_total) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        {{-- Totals --}}
        <section class="totals">
            <div class="row">
                <span class="label">{{ $labels['subtotal'] }}</span>
                <span class="value">{{ \App\Support\CurrencyFormatter::format((float) $order->subtotal) }}</span>
            </div>
            @if((float)$order->discount_amount > 0)
            <div class="row">
                <span class="label">{{ $labels['discount'] }}</span>
                <span class="value">{{ \App\Support\CurrencyFormatter::format((float) $order->discount_amount) }}</span>
            </div>
            @endif
            <div class="row grand">
                <span>{{ $labels['total'] }}</span>
                <span>{{ \App\Support\CurrencyFormatter::format((float) $order->total) }}</span>
            </div>
            @if($order->payment_method)
            <div class="row" style="margin-top:4px">
                <span class="label">{{ $labels['payment_method'] }}</span>
                <span class="value" style="text-transform:capitalize">{{ $order->payment_method }}</span>
            </div>
            @endif
        </section>

        {{-- Footer --}}
        <footer class="footer">
            {{ $labels['thanks'] }}
            <div class="footer-tagline">{{ config('app.name', 'Point 88') }}</div>
        </footer>

    </main>
</body>

</html>
