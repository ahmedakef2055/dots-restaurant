<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>{{ __('ui.suppliers.exports.title') }}</title>
<style>
@page { margin: 11mm; }
* { box-sizing: border-box; }
body { margin:0; font-family:"DejaVu Sans","Noto Naskh Arabic","Noto Sans Arabic",sans-serif; font-size:10px; line-height:1.4; color:#1A2B21; background:#ffffff; }
html[dir="rtl"] body { direction:rtl; }
.hdr { width:100%; border-collapse:separate; }
.hdr td { border:none; background:transparent; padding:0; vertical-align:middle; }
.logo      { max-height:40px; max-width:44px; object-fit:contain; }
.logo-name { max-height:32px; max-width:110px; object-fit:contain; }
.brand-cell { padding-left:8px !important; }
html[dir="rtl"] .brand-cell { padding-left:0 !important; padding-right:8px !important; }
.brand-name { font-size:15px; font-weight:900; color:#5E7D67; }
.brand-sub  { font-size:8px; color:#4A6352; margin-top:1px; }
.title-cell { text-align:right; }
html[dir="rtl"] .title-cell { text-align:left; }
.rpt-title { font-size:14px; font-weight:800; color:#5E7D67; }
.rpt-meta  { font-size:8px; color:#4A6352; margin-top:2px; }
.divider { width:100%; border-collapse:collapse; margin:6px 0 8px; }
.divider td { height:3px; border:none; padding:0; }
.d1 { background:#5E7D67; width:45%; }
.d2 { background:#A8C89A; width:35%; }
.d3 { background:#EEF3EA; width:20%; }
html[dir="rtl"] .d1 { background:#EEF3EA; }
html[dir="rtl"] .d3 { background:#5E7D67; }
.filter-bar { background:#EEF3EA; border:1px solid #C4D3BD; border-radius:5px; padding:4px 9px; margin-bottom:7px; font-size:8.5px; color:#5E7D67; line-height:1.8; }
.notice { background:#FFFBEB; border:1px solid #FCD34D; border-radius:4px; padding:4px 8px; font-size:8.5px; font-weight:700; color:#92400E; margin-bottom:7px; }
.summary { width:100%; border-collapse:separate; border-spacing:5px 0; margin-bottom:8px; }
.sc { border-radius:5px; padding:5px 8px; text-align:center; vertical-align:top; border:1px solid #C4D3BD; background:#FAF8F3; }
.sc.gold  { background:#A8C89A; border-color:#BFD1BC; }
.sc.green { background:#DCFCE7; border-color:#86EFAC; }
.sv { font-size:13px; font-weight:900; color:#1A2B21; }
.sl { font-size:7.5px; color:#5E7D67; text-transform:uppercase; letter-spacing:0.05em; margin-top:2px; }
.sc.gold .sl { color:#4F6A57; }
table.dt { width:100%; border-collapse:collapse; }
table.dt thead { display:table-header-group; }
table.dt tr { page-break-inside:avoid; }
table.dt th { background:#5E7D67; color:#fff; font-weight:700; padding:5px 6px; font-size:8.5px; text-transform:uppercase; letter-spacing:0.04em; border:1px solid #4F6A57; }
table.dt td { padding:4px 6px; border:1px solid #C4D3BD; font-size:9px; vertical-align:middle; color:#1A2B21; }
table.dt tr:nth-child(even) td { background:#EEF3EA; }
.num { direction:ltr; unicode-bidi:embed; font-variant-numeric:tabular-nums; white-space:nowrap; font-family:"Courier New",monospace; font-weight:700; text-align:right; }
html[dir="rtl"] .num { text-align:left; }
.contact-cell { word-break:break-word; overflow-wrap:break-word; }
.badge { display:inline-block; padding:1px 5px; border-radius:9px; font-size:7.5px; font-weight:700; white-space:nowrap; }
.b-active   { background:#DCFCE7; color:#166534; }
.b-inactive { background:#F1F5F9; color:#475569; }
.ftr { width:100%; border-collapse:separate; margin-top:8px; border-top:1px solid #C4D3BD; }
.ftr td { border:none; background:transparent; padding:2px 0 0; font-size:7.5px; color:#4A6352; vertical-align:top; }
.ftr-r { text-align:right; }
html[dir="rtl"] .ftr-r { text-align:left; }
</style>
</head>
<body>
@php
$isAr     = app()->getLocale() === 'ar';
/* ── Embed logos as base64 for cross-engine compatibility ── */
$__logoB64  = '';
$__logoFile = public_path('images/logo.png');
$__logoB64  = \App\Support\PdfExportRenderer::logoBase64($__logoFile, 40, 40);
$rows          = collect($rows ?? []);
$filters       = $filters ?? [];
$totalCount    = (int)($totalCount    ?? $rows->count());
$exportedCount = (int)($exportedCount ?? $rows->count());
$isTruncated   = (bool)($isTruncated  ?? false);
$searchQuery   = trim((string)($filters['q'] ?? ''));
$statusFilter  = trim((string)($filters['status'] ?? ''));
$statusLabel   = match ($statusFilter) {
    'active'   => __('ui.suppliers.status.active'),
    'inactive' => __('ui.suppliers.status.inactive'),
    default    => __('ui.suppliers.exports.all_statuses'),
};
$activeCount    = $rows->filter(fn($r) => (bool)($r['is_active'] ?? false))->count();
$inactiveCount  = $exportedCount - $activeCount;
$totalPurchases = $rows->sum(fn($r) => (int)($r['purchases_count'] ?? 0));
@endphp

<table class="hdr">
<tr>
    @if($__logoB64)
    <td style="width:46px"><img src="{{ $__logoB64 }}" width="40" height="40" style="width:40px;height:40px" alt=""></td>
    @endif
    <td class="title-cell">
        <div class="rpt-title">{{ __('ui.suppliers.exports.title') }}</div>
        <div class="rpt-meta">{{ $isAr ? 'أُنشئ' : 'Generated' }}: <span dir="ltr" style="unicode-bidi:embed">{{ $generatedAt?->format('Y-m-d g:i A') }}</span> &nbsp;|&nbsp; {{ $isAr ? 'السجلات' : 'Records' }}: {{ number_format($exportedCount) }} / {{ number_format($totalCount) }}</div>
    </td>
</tr>
</table>
<table class="divider"><tr><td class="d1"></td><td class="d2"></td><td class="d3"></td></tr></table>

<div class="filter-bar">
    <strong>{{ $isAr ? 'البحث' : 'Search' }}:</strong> {{ $searchQuery !== '' ? $searchQuery : ($isAr ? 'الكل' : 'All') }}
    &nbsp;&nbsp;<strong>{{ $isAr ? 'الحالة' : 'Status' }}:</strong> {{ $statusLabel }}
</div>

@if($isTruncated)
<div class="notice">{{ str_replace([':exported',':total'],[number_format($exportedCount),number_format($totalCount)], __('ui.suppliers.exports.truncated_notice')) }}</div>
@endif

<table class="summary">
<tr>
    <td class="sc"><div class="sv">{{ number_format($exportedCount) }}</div><div class="sl">{{ $isAr ? 'إجمالي الموردين' : 'Total Suppliers' }}</div></td>
    <td class="sc green"><div class="sv" style="color:#166534">{{ number_format($activeCount) }}</div><div class="sl" style="color:#166534">{{ $isAr ? 'نشط' : 'Active' }}</div></td>
    <td class="sc"><div class="sv" style="color:#475569">{{ number_format($inactiveCount) }}</div><div class="sl">{{ $isAr ? 'غير نشط' : 'Inactive' }}</div></td>
    <td class="sc gold"><div class="sv">{{ number_format($totalPurchases) }}</div><div class="sl">{{ $isAr ? 'إجمالي المشتريات' : 'Total Purchases' }}</div></td>
</tr>
</table>

<table class="dt">
<thead>
<tr>
    <th style="width:4%">#</th>
    <th style="width:20%">{{ __('ui.suppliers.labels.supplier_name') }}</th>
    <th style="width:28%">{{ __('ui.suppliers.exports.contact') }}</th>
    <th style="width:24%">{{ __('ui.suppliers.labels.location') }}</th>
    <th style="width:12%">{{ __('ui.suppliers.labels.total_purchases') }}</th>
    <th style="width:12%">{{ __('ui.suppliers.status.title') }}</th>
</tr>
</thead>
<tbody>
@forelse($rows as $i => $row)
@php
$contactParts = array_filter([
    trim((string)($row['contact_person'] ?? '')),
    trim((string)($row['phone'] ?? '')),
    trim((string)($row['email'] ?? '')),
], static fn($v): bool => $v !== '');
$contactText = $contactParts !== [] ? implode(' | ', $contactParts) : '-';

$locationParts = array_filter([
    trim((string)($row['location'] ?? '')),
    trim((string)($row['address'] ?? '')),
], static fn($v): bool => $v !== '');
$locationText = $locationParts !== [] ? implode(' - ', $locationParts) : '-';

$isActive   = (bool)($row['is_active'] ?? false);
$statusText = $isActive ? __('ui.suppliers.status.active') : __('ui.suppliers.status.inactive');
@endphp
<tr>
    <td>{{ $i + 1 }}</td>
    <td>{{ $row['name'] ?? '-' }}</td>
    <td class="contact-cell">{{ $contactText }}</td>
    <td class="contact-cell">{{ $locationText }}</td>
    <td class="num">{{ number_format((int)($row['purchases_count'] ?? 0)) }}</td>
    <td><span class="badge {{ $isActive ? 'b-active' : 'b-inactive' }}">{{ $statusText }}</span></td>
</tr>
@empty
<tr><td colspan="6" style="text-align:center;padding:14px;color:#4A6352;font-style:italic">{{ __('ui.suppliers.exports.no_results') }}</td></tr>
@endforelse
</tbody>
</table>

<table class="ftr">
<tr>
    <td>{{ config('app.name','Point 88') }} &mdash; {{ $isAr ? 'نظام إدارة المطاعم' : 'Restaurant Management System' }}</td>
    <td class="ftr-r">{{ $generatedAt?->format('Y-m-d g:i A') }}</td>
</tr>
</table>
</body>
</html>
