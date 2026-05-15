<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<title>{{ __('ui.employees.financial_report.title') }} - {{ $employee->full_name }}</title>
<style>
@page { size: A4 portrait; margin: 12mm; }
* { box-sizing: border-box; }
body { margin:0; font-family:"DejaVu Sans","Noto Naskh Arabic","Noto Sans Arabic",sans-serif; font-size:10px; line-height:1.4; color:#1A2B21; background:#ffffff; }
html[dir="rtl"] body { direction:rtl; }

/* Header */
.hdr { width:100%; border-collapse:separate; }
.hdr td { border:none; background:transparent; padding:0; vertical-align:middle; }
.logo { max-height:44px; max-width:56px; object-fit:contain; }
.brand-cell { padding-left:9px !important; }
html[dir="rtl"] .brand-cell { padding-left:0 !important; padding-right:9px !important; }
.brand-name { font-size:16px; font-weight:900; color:#5E7D67; }
.brand-sub  { font-size:8px; color:#4A6352; margin-top:2px; }
.title-cell { text-align:right; }
html[dir="rtl"] .title-cell { text-align:left; }
.rpt-title { font-size:13px; font-weight:800; color:#5E7D67; }
.rpt-meta  { font-size:8px; color:#4A6352; margin-top:2px; }

/* Divider */
.divider { width:100%; border-collapse:collapse; margin:7px 0 9px; }
.divider td { height:3px; border:none; padding:0; }
.d1 { background:#5E7D67; width:45%; }
.d2 { background:#A8C89A; width:35%; }
.d3 { background:#E4EBDD; width:20%; }
html[dir="rtl"] .d1 { background:#E4EBDD; }
html[dir="rtl"] .d3 { background:#5E7D67; }

/* Employee info card */
.emp-card {
    background:#E4EBDD;
    border:1px solid #C4D3BD;
    border-radius:6px;
    padding:8px 12px;
    margin-bottom:9px;
    font-size:9px;
    color:#5E7D67;
    line-height:1.9;
}
.emp-card strong { color:#1A2B21; font-size:10px; }
.emp-name { font-size:13px; font-weight:900; color:#5E7D67; margin-bottom:3px; }

/* Summary cards */
.summary { width:100%; border-collapse:separate; border-spacing:6px 0; margin-bottom:9px; }
.sc { border-radius:6px; padding:7px 10px; text-align:center; vertical-align:top; border:1px solid #C4D3BD; background:#FAF8F3; }
.sc.gold  { background:#A8C89A; border-color:#BFD1BC; }
.sc.green { background:#DCFCE7; border-color:#86EFAC; }
.sc.red   { background:#FEE2E2; border-color:#FCA5A5; }
.sv { font-size:14px; font-weight:900; color:#1A2B21; direction:ltr; unicode-bidi:embed; }
.sl { font-size:7.5px; color:#5E7D67; text-transform:uppercase; letter-spacing:0.05em; margin-top:2px; }
.sc.gold .sl { color:#4F6A57; }
.sc.green .sl { color:#166534; }
.sc.green .sv { color:#166534; }

/* Table */
table.dt { width:100%; border-collapse:collapse; }
table.dt thead { display:table-header-group; }
table.dt tr { page-break-inside:avoid; }
table.dt th { background:#5E7D67; color:#fff; font-weight:700; padding:5px 6px; font-size:8.5px; text-transform:uppercase; letter-spacing:0.04em; border:1px solid #4F6A57; }
table.dt td { padding:4px 6px; border:1px solid #C4D3BD; font-size:9px; vertical-align:middle; color:#1A2B21; }
table.dt tr:nth-child(even) td { background:#EEF3EA; }
.num { direction:ltr; unicode-bidi:embed; font-variant-numeric:tabular-nums; white-space:nowrap; font-family:"Courier New",monospace; font-weight:700; text-align:right; }
html[dir="rtl"] .num { text-align:left; }
.badge { display:inline-block; padding:1px 5px; border-radius:9px; font-size:7.5px; font-weight:700; white-space:nowrap; }
.b-bonus    { background:#DCFCE7; color:#166534; }
.b-deduct   { background:#FEE2E2; color:#991B1B; }
.b-neutral  { background:#F1F5F9; color:#475569; }

/* Note */
.note { margin-top:8px; font-size:8.5px; color:#4A6352; font-style:italic; }

/* Print button */
.print-btn {
    display:inline-block;
    background:#5E7D67;
    color:#fff;
    border:none;
    border-radius:6px;
    padding:6px 14px;
    font-size:11px;
    font-weight:700;
    cursor:pointer;
}
.print-btn:hover { background:#A8C89A; color:#1A2B21; }

/* Footer */
.ftr { width:100%; border-collapse:separate; margin-top:10px; border-top:1px solid #C4D3BD; }
.ftr td { border:none; background:transparent; padding:3px 0 0; font-size:7.5px; color:#4A6352; vertical-align:top; }
.ftr-r { text-align:right; }
html[dir="rtl"] .ftr-r { text-align:left; }

@media print {
    .print-only-hidden { display:none !important; }
    body { margin:0; }
}
</style>
</head>
<body>
@php
$isAr     = app()->getLocale() === 'ar';
$logoPath = collect(['images/logo.png','images/logo.jpg','logo.png'])
    ->first(fn($c) => file_exists(public_path($c)));

$netSalary       = (float)($report['net_salary'] ?? 0);
$baseSalary      = (float)($report['base_salary'] ?? 0);
$totalAdj        = (float)($report['total_adjustments'] ?? 0);
$isPositiveNet   = $netSalary >= $baseSalary;
@endphp

{{-- Print button (browser only) --}}
@if($showPrintButton ?? false)
<div class="print-only-hidden" style="text-align:right;margin-bottom:10px">
    <button class="print-btn" onclick="window.print()">{{ __('ui.employees.financial_report.actions.print') }}</button>
</div>
@endif

{{-- Header --}}
<table class="hdr">
<tr>
    @if($logoPath)<td style="width:56px"><img src="{{ public_path($logoPath) }}" class="logo" alt=""></td>@endif
    <td class="title-cell">
        <div class="rpt-title">{{ __('ui.employees.financial_report.title') }}</div>
        <div class="rpt-meta">{{ $isAr ? 'أُنشئ' : 'Generated' }}: {{ $generatedAt?->format('Y-m-d H:i') }}</div>
    </td>
</tr>
</table>
<table class="divider"><tr><td class="d1"></td><td class="d2"></td><td class="d3"></td></tr></table>

{{-- Employee info --}}
<div class="emp-card">
    <div class="emp-name">{{ $employee->full_name }}</div>
    <strong>{{ __('ui.employees.financial_report.month') }}:</strong> {{ $report['month_label'] ?? '-' }}
    &nbsp;&nbsp;&nbsp;
    <strong>{{ __('ui.employees.financial_report.period') }}:</strong>
    <span dir="ltr" style="unicode-bidi:embed">{{ $report['period_start'] ?? '-' }} &ndash; {{ $report['period_end'] ?? '-' }}</span>
</div>

{{-- Summary --}}
<table class="summary">
<tr>
    <td class="sc"><div class="sv">{{ number_format($baseSalary, 2, '.', ',') }}</div><div class="sl">{{ __('ui.employees.financial_report.summary.base_salary') }}</div></td>
    <td class="sc {{ $totalAdj >= 0 ? 'green' : 'red' }}">
        <div class="sv">{{ ($totalAdj >= 0 ? '+' : '') . number_format($totalAdj, 2, '.', ',') }}</div>
        <div class="sl">{{ __('ui.employees.financial_report.summary.total_adjustments') }}</div>
    </td>
    <td class="sc gold"><div class="sv">{{ number_format($netSalary, 2, '.', ',') }}</div><div class="sl">{{ __('ui.employees.financial_report.summary.net_salary') }}</div></td>
</tr>
</table>

{{-- Transactions table --}}
<table class="dt">
<thead>
<tr>
    <th style="width:11%">{{ __('ui.employees.financial_report.table.date') }}</th>
    <th style="width:12%">{{ __('ui.employees.financial_report.table.type') }}</th>
    <th style="width:22%">{{ __('ui.employees.financial_report.table.reason') }}</th>
    <th style="width:22%">{{ __('ui.employees.financial_report.table.product_name') }}</th>
    <th style="width:11%">{{ __('ui.employees.financial_report.table.unit_price') }}</th>
    <th style="width:9%">{{ __('ui.employees.financial_report.table.quantity') }}</th>
    <th style="width:13%">{{ __('ui.employees.financial_report.table.amount') }}</th>
</tr>
</thead>
<tbody>
@forelse($report['rows'] as $row)
@php
$typeLabel = $row['type_label'] ?? '';
$amount    = (float)($row['amount'] ?? 0);
$isBonus   = $amount > 0;
$badgeClass = $amount > 0 ? 'b-bonus' : ($amount < 0 ? 'b-deduct' : 'b-neutral');
@endphp
<tr>
    <td class="num">{{ $row['date'] }}</td>
    <td><span class="badge {{ $badgeClass }}">{{ $typeLabel }}</span></td>
    <td>{{ $row['reason'] }}</td>
    <td>{{ $row['product_name'] }}</td>
    <td class="num">{{ number_format((float)$row['unit_price'], 2, '.', ',') }}</td>
    <td class="num">{{ number_format((float)$row['quantity'], 2, '.', ',') }}</td>
    <td class="num" style="{{ $isBonus ? 'color:#166534' : ($amount < 0 ? 'color:#991B1B' : '') }}">{{ number_format($amount, 2, '.', ',') }}</td>
</tr>
@empty
<tr><td colspan="7" style="text-align:center;padding:14px;color:#4A6352;font-style:italic">{{ __('ui.employees.financial_report.empty') }}</td></tr>
@endforelse
</tbody>
</table>

@if(!($report['salary_adjustments_enabled'] ?? true))
<p class="note">{{ __('ui.employees.financial_report.feature_disabled_note') }}</p>
@endif

<table class="ftr">
<tr>
    <td>{{ config('app.name','Point 88') }} &mdash; {{ $isAr ? 'نظام إدارة المطاعم' : 'Restaurant Management System' }}</td>
    <td class="ftr-r">{{ $generatedAt?->format('Y-m-d H:i:s') }}</td>
</tr>
</table>
</body>
</html>
