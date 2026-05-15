<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>{{ app()->getLocale() === 'ar' ? 'سجلات الشيفت' : 'Shift Logs Report' }}</title>
<style>
@page { margin: 11mm; }
* { box-sizing: border-box; }
body {
    margin: 0;
    font-family: "DejaVu Sans", "Noto Naskh Arabic", "Noto Sans Arabic", sans-serif;
    font-size: 10px;
    line-height: 1.4;
    color: #1A2B21;
    background: #ffffff;
}
html[dir="rtl"] body { direction: rtl; }
/* ── Top header ── */
.hdr { width: 100%; border-collapse: separate; }
.hdr td { border: none; background: transparent; padding: 0; vertical-align: middle; }
.logo      { max-height: 40px; max-width: 44px; object-fit: contain; }
.logo-name { max-height: 32px; max-width: 110px; object-fit: contain; }
.brand-cell { padding-left: 8px !important; }
html[dir="rtl"] .brand-cell { padding-left: 0 !important; padding-right: 8px !important; }
.brand-name { font-size: 15px; font-weight: 900; color: #5E7D67; letter-spacing: -0.2px; }
.brand-sub  { font-size: 8px; color: #4A6352; margin-top: 1px; }
.title-cell { text-align: right; }
html[dir="rtl"] .title-cell { text-align: left; }
.rpt-title { font-size: 14px; font-weight: 800; color: #5E7D67; }
.rpt-meta  { font-size: 8px; color: #4A6352; margin-top: 2px; }
/* ── Divider ── */
.divider { width: 100%; border-collapse: collapse; margin: 6px 0 8px; }
.divider td { height: 3px; border: none; padding: 0; }
.d1 { background: #5E7D67; width: 45%; }
.d2 { background: #A8C89A; width: 35%; }
.d3 { background: #EEF3EA; width: 20%; }
html[dir="rtl"] .d1 { background: #EEF3EA; }
html[dir="rtl"] .d3 { background: #5E7D67; }
/* ── Filter bar ── */
.filter-bar {
    background: #EEF3EA;
    border: 1px solid #C4D3BD;
    border-radius: 5px;
    padding: 4px 9px;
    margin-bottom: 7px;
    font-size: 8.5px;
    color: #5E7D67;
    line-height: 1.7;
}
/* ── Summary ── */
.summary { width: 100%; border-collapse: separate; border-spacing: 5px 0; margin-bottom: 8px; }
.sc {
    border-radius: 5px;
    padding: 5px 8px;
    text-align: center;
    vertical-align: top;
    border: 1px solid #C4D3BD;
    background: #FAF8F3;
    width: 25%;
}
.sc.gold { background: #A8C89A; border-color: #BFD1BC; }
.sc.green { background: #DCFCE7; border-color: #86EFAC; }
.sc.red   { background: #FEE2E2; border-color: #FCA5A5; }
.sv { font-size: 14px; font-weight: 900; color: #1A2B21; }
.sl { font-size: 7.5px; color: #5E7D67; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; }
.sc.gold .sl { color: #4F6A57; }
/* ── Table ── */
table.dt { width: 100%; border-collapse: collapse; }
table.dt thead { display: table-header-group; }
table.dt tr { page-break-inside: avoid; }
table.dt th {
    background: #5E7D67;
    color: #fff;
    font-weight: 700;
    padding: 5px 6px;
    font-size: 8.5px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border: 1px solid #4F6A57;
}
table.dt td {
    padding: 4px 6px;
    border: 1px solid #C4D3BD;
    font-size: 9px;
    vertical-align: middle;
    color: #1A2B21;
}
table.dt tr:nth-child(even) td { background: #EEF3EA; }
.num {
    direction: ltr;
    unicode-bidi: embed;
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
    font-family: "Courier New", monospace;
    font-weight: 700;
    text-align: right;
}
html[dir="rtl"] .num { text-align: left; }
.pos { color: #166534; font-weight: 700; }
.neg { color: #991B1B; font-weight: 700; }
.badge {
    display: inline-block;
    padding: 1px 5px;
    border-radius: 9px;
    font-size: 7.5px;
    font-weight: 700;
    white-space: nowrap;
}
.b-open   { background: #FEF3C7; color: #92400E; }
.b-closed { background: #5E7D67; color: #fff; }
/* ── Footer ── */
.ftr { width: 100%; border-collapse: separate; margin-top: 10px; border-top: 1px solid #C4D3BD; padding-top: 4px; }
.ftr td { border: none; background: transparent; padding: 0; font-size: 7.5px; color: #4A6352; vertical-align: top; }
.ftr-r { text-align: right; }
html[dir="rtl"] .ftr-r { text-align: left; }
</style>
</head>
<body>
@php
    $isAr = app()->getLocale() === 'ar';
    /* ── Embed logos as base64 for cross-engine compatibility ── */
    $__logoB64  = '';
    $__logoFile = public_path('images/logo.png');
    $__logoB64  = \App\Support\PdfExportRenderer::logoBase64($__logoFile, 40, 40);
    $totalShifts  = $shiftLogs->count();
    $openShifts   = $shiftLogs->whereNull('shift_end')->count();
    $closedShifts = $shiftLogs->whereNotNull('shift_end')->count();
    $totalSales   = $shiftLogs->sum(fn($l) => (float)($l->total_sales ?? 0));
@endphp

{{-- Header --}}
<table class="hdr">
<tr>
    @if($__logoB64)
    <td style="width:46px">
        <img src="{{ $__logoB64 }}" width="40" height="40" style="width:40px;height:40px" alt="">
    </td>
    @endif
    <td class="title-cell">
        <div class="rpt-title">{{ $isAr ? 'تقرير سجلات الشيفت' : 'Shift Logs Report' }}</div>
        <div class="rpt-meta">
            {{ $isAr ? 'من' : 'From' }}: {{ $filters['from'] ?? '-' }}
            &nbsp;|&nbsp;
            {{ $isAr ? 'إلى' : 'To' }}: {{ $filters['to'] ?? '-' }}
            &nbsp;|&nbsp;
            {{ $isAr ? 'أُنشئ' : 'Generated' }}: <span dir="ltr" style="unicode-bidi:embed">{{ $generatedAt?->format('Y-m-d g:i A') }}</span>
        </div>
    </td>
</tr>
</table>

{{-- Divider --}}
<table class="divider"><tr><td class="d1"></td><td class="d2"></td><td class="d3"></td></tr></table>

{{-- Summary --}}
<table class="summary">
<tr>
    <td class="sc">
        <div class="sv">{{ $totalShifts }}</div>
        <div class="sl">{{ $isAr ? 'إجمالي الشيفتات' : 'Total Shifts' }}</div>
    </td>
    <td class="sc" style="background:#EEF3EA">
        <div class="sv">{{ $closedShifts }}</div>
        <div class="sl">{{ $isAr ? 'مغلقة' : 'Closed' }}</div>
    </td>
    <td class="sc" style="background:#FEF3C7;border-color:#FCD34D">
        <div class="sv" style="color:#92400E">{{ $openShifts }}</div>
        <div class="sl" style="color:#92400E">{{ $isAr ? 'مفتوحة' : 'Open' }}</div>
    </td>
    <td class="sc gold">
        <div class="sv">{{ \App\Support\CurrencyFormatter::format($totalSales) }}</div>
        <div class="sl">{{ $isAr ? 'إجمالي المبيعات' : 'Total Sales' }}</div>
    </td>
</tr>
</table>

{{-- Table --}}
<table class="dt">
<thead>
<tr>
    <th style="width:4%">#</th>
    <th style="width:13%">{{ $isAr ? 'الكاشير' : 'Cashier' }}</th>
    <th style="width:13%">{{ $isAr ? 'بداية الشيفت' : 'Shift Start' }}</th>
    <th style="width:13%">{{ $isAr ? 'نهاية الشيفت' : 'Shift End' }}</th>
    <th style="width:8%">{{ $isAr ? 'الحالة' : 'Status' }}</th>
    <th style="width:12%">{{ $isAr ? 'النقدية الافتتاحية' : 'Opening Cash' }}</th>
    <th style="width:12%">{{ $isAr ? 'إجمالي المبيعات' : 'Total Sales' }}</th>
    <th style="width:12%">{{ $isAr ? 'النقدية المتوقعة' : 'Expected Cash' }}</th>
    <th style="width:12%">{{ $isAr ? 'النقدية الفعلية' : 'Actual Cash' }}</th>
    <th style="width:11%">{{ $isAr ? 'الفرق' : 'Difference' }}</th>
</tr>
</thead>
<tbody>
@forelse($shiftLogs as $i => $log)
@php
    $isClosed = $log->shift_end !== null;
    $diff = (float)($log->cash_difference ?? 0);
@endphp
<tr>
    <td>{{ $i + 1 }}</td>
    <td>{{ $log->user?->name ?? ($isAr ? 'النظام' : 'System') }}</td>
    <td class="num">{{ $log->shift_start ? \Carbon\Carbon::parse($log->shift_start)->format('Y-m-d g:i A') : '-' }}</td>
    <td class="num">{{ $log->shift_end  ? \Carbon\Carbon::parse($log->shift_end)->format('Y-m-d g:i A')  : '-' }}</td>
    <td><span class="badge {{ $isClosed ? 'b-closed' : 'b-open' }}">{{ $isClosed ? ($isAr ? 'مغلق' : 'Closed') : ($isAr ? 'مفتوح' : 'Open') }}</span></td>
    <td class="num">{{ \App\Support\CurrencyFormatter::format((float)($log->opening_cash ?? 0)) }}</td>
    <td class="num">{{ \App\Support\CurrencyFormatter::format((float)($log->total_sales ?? 0)) }}</td>
    <td class="num">{{ \App\Support\CurrencyFormatter::format((float)($log->expected_cash ?? 0)) }}</td>
    <td class="num">{{ $log->actual_cash !== null ? \App\Support\CurrencyFormatter::format((float)$log->actual_cash) : '-' }}</td>
    <td class="num {{ $diff > 0 ? 'pos' : ($diff < 0 ? 'neg' : '') }}">
        {{ $log->cash_difference !== null ? \App\Support\CurrencyFormatter::format(abs($diff)) . ($diff > 0 ? ' ▲' : ($diff < 0 ? ' ▼' : '')) : '-' }}
    </td>
</tr>
@empty
<tr><td colspan="10" style="text-align:center;padding:14px;color:#4A6352;font-style:italic">{{ $isAr ? 'لا توجد بيانات.' : 'No records found.' }}</td></tr>
@endforelse
</tbody>
</table>

{{-- Footer --}}
<table class="ftr" style="margin-top:8px">
<tr>
    <td>{{ config('app.name','Point 88') }} &mdash; {{ $isAr ? 'نظام إدارة المطاعم' : 'Restaurant Management System' }}</td>
    <td class="ftr-r">{{ $generatedAt?->format('Y-m-d g:i A') }}</td>
</tr>
</table>
</body>
</html>
