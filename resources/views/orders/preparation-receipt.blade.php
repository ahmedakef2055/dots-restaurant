<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preparation #{{ $order->order_number }}</title>
    <style>
        @page { size: 80mm auto; margin: 0; }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #fff;
            color: #000;
            font-family: "DejaVu Sans", "Courier New", monospace;
            font-size: 14px;
            line-height: 1.4;
        }

        /* ─── Screen wrapper ─── */
        .wrap {
            width: 80mm;
            margin: 12px auto;
            border: 3px solid #000;
            background: #fff;
        }

        body.thermal .wrap {
            border: none;
            margin: 0;
            width: 100%;
        }

        /* ─── Logo area ─── */
        .logo-area {
            text-align: center;
            background: #fff;
            padding: 8px 6px 4px;
            border-bottom: 2px solid #000;
        }

        .logo-area img {
            max-width: 65%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        body.thermal .logo-area img {
            max-width: 70%;
        }

        /* Fallback text when no logo file */
        .logo-area .business-text {
            font-size: 16px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        body.thermal .logo-area .business-text {
            font-size: 28px;
        }

        .logo-area .kitchen-tag {
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 2px;
            color: #444;
        }

        body.thermal .logo-area .kitchen-tag {
            font-size: 20px;
        }

        /* ─── Print-label band ─── */
        .label-band {
            background: #000;
            color: #fff;
            text-align: center;
            padding: 6px 4px;
        }

        .label-band .label-text {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        body.thermal .label-band .label-text {
            font-size: 32px;
            letter-spacing: 4px;
        }

        /* ─── Order number hero block ─── */
        .order-hero {
            text-align: center;
            padding: 6px 6px 4px;
            border-bottom: 2px dashed #000;
        }

        .order-hero .daily-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #555;
        }

        body.thermal .order-hero .daily-label {
            font-size: 20px;
        }

        .order-hero .daily-number {
            font-size: 40px;
            font-weight: 900;
            line-height: 1.1;
        }

        body.thermal .order-hero .daily-number {
            font-size: 62px;
        }

        .order-hero .order-ref {
            font-size: 11px;
            color: #555;
            margin-top: 1px;
        }

        body.thermal .order-hero .order-ref {
            font-size: 20px;
        }

        /* ─── Meta section ─── */
        .meta-section {
            padding: 5px 7px 5px;
            border-bottom: 2px dashed #000;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            padding: 2px 0;
        }

        body.thermal .meta-row {
            font-size: 24px;
            padding: 3px 0;
        }

        .meta-key {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            flex-shrink: 0;
            padding-right: 6px;
        }

        .meta-val { text-align: right; }

        /* ─── Items section ─── */
        .items-section {
            padding: 0 7px 6px;
        }

        .items-header {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding: 5px 0 3px;
        }

        body.thermal .items-header {
            font-size: 22px;
            padding: 6px 0 4px;
        }

        .item-row {
            padding: 6px 0;
            border-bottom: 1px dashed #666;
        }

        body.thermal .item-row {
            padding: 8px 0;
        }

        .item-main {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-name {
            font-size: 14px;
            font-weight: 700;
            flex: 1;
            padding-right: 6px;
            word-break: break-word;
        }

        body.thermal .item-name {
            font-size: 28px;
        }

        /* Quantity: boxed hero number */
        .item-qty {
            font-size: 22px;
            font-weight: 900;
            border: 2px solid #000;
            padding: 1px 7px;
            line-height: 1.2;
            white-space: nowrap;
            flex-shrink: 0;
        }

        body.thermal .item-qty {
            font-size: 38px;
            border-width: 3px;
            padding: 2px 10px;
        }

        .item-note {
            font-size: 12px;
            font-style: italic;
            padding: 3px 0 0 6px;
            color: #333;
        }

        body.thermal .item-note {
            font-size: 22px;
        }

        /* ─── Footer ─── */
        .footer-rule {
            border-top: 3px solid #000;
            padding: 4px 6px 6px;
            font-size: 11px;
            text-align: center;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #555;
        }

        body.thermal .footer-rule {
            font-size: 20px;
        }
    </style>
</head>

@php
    /* Embed both logos as base64 — same technique as invoice template */
    $__prepIconB64 = '';
    $__iconFile    = public_path('images/logo.png');
    if (file_exists($__iconFile)) {
        $__prepIconB64 = 'data:image/png;base64,' . base64_encode(file_get_contents($__iconFile));
    }
@endphp

<body class="{{ ($isDirectPrint ?? false) ? 'thermal' : '' }}">
<div class="wrap">

    {{-- ── Logo / header ── --}}
    <div class="logo-area">
        @if ($__prepIconB64)
            <img src="{{ $__prepIconB64 }}" alt="{{ config('app.name') }}" style="max-width:30%; margin-bottom:4px;">
        @endif
        <div class="kitchen-tag">Kitchen / Bar</div>
    </div>

    {{-- ── Print-label band ── --}}
    <div class="label-band">
        <div class="label-text">{{ $label }}</div>
    </div>

    {{-- ── Order number hero ── --}}
    <div class="order-hero">
        <div class="daily-label">Order No.</div>
        <div class="daily-number">#{{ $order->order_daily_number }}</div>
        <div class="order-ref">{{ $order->order_number }} &nbsp;|&nbsp; Serial #{{ $order->order_serial }}</div>
    </div>

    {{-- ── Order meta ── --}}
    <div class="meta-section">
        @if ($order->restaurantTable)
        <div class="meta-row">
            <span class="meta-key">Table</span>
            <span class="meta-val">{{ $order->restaurantTable->name }}</span>
        </div>
        @endif
        <div class="meta-row">
            <span class="meta-key">Type</span>
            <span class="meta-val">
                {{ match ((string) $order->order_type) {
                    'dine_in'  => 'Dine-in',
                    'takeaway' => 'Takeaway',
                    'delivery' => 'Delivery',
                    default    => ucfirst((string) $order->order_type),
                } }}
            </span>
        </div>
        <div class="meta-row">
            <span class="meta-key">Time</span>
            <span class="meta-val">{{ $order->created_at?->format('g:i A  d/m/Y') }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-key">Cashier</span>
            <span class="meta-val">{{ $order->user?->name ?? '—' }}</span>
        </div>
        @if ($order->notes)
        <div class="meta-row">
            <span class="meta-key">Note</span>
            <span class="meta-val" style="font-style:italic">{{ $order->notes }}</span>
        </div>
        @endif
    </div>

    {{-- ── Items ── --}}
    <div class="items-section">
        <div class="items-header">
            <span>Item</span>
            <span>Qty</span>
        </div>

        @foreach ($printItems as $item)
            @php
                $itemName = $item->product_name ?? ($item['product_name'] ?? '');
                $itemQty  = $item->quantity     ?? ($item['quantity']     ?? 1);
                $itemNote = $item->notes        ?? ($item['notes']        ?? null);
            @endphp
            <div class="item-row">
                <div class="item-main">
                    <span class="item-name">{{ $itemName }}</span>
                    <span class="item-qty">{{ $itemQty }}</span>
                </div>
                @if ($itemNote)
                    <div class="item-note">&#8594; {{ $itemNote }}</div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="footer-rule">kitchen copy &mdash; no prices</div>

</div>
</body>
</html>
