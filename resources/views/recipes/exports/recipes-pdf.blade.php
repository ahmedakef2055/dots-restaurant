<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>{{ $scope === 'semi_finished' ? __('ui.recipes.exports.semi_finished_title') : __('ui.recipes.exports.products_title') }}</title>
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
.sc.gold .sv { color:#4F6A57; }
.sc.gold .sl { color:#4F6A57; }
table.dt { width:100%; border-collapse:collapse; table-layout:fixed; }
table.dt thead { display:table-header-group; }
table.dt tr { page-break-inside:avoid; }
table.dt th { background:#5E7D67; color:#fff; font-weight:700; padding:5px 6px; font-size:8.5px; text-transform:uppercase; letter-spacing:0.04em; border:1px solid #4F6A57; }
table.dt td { padding:4px 6px; border:1px solid #C4D3BD; font-size:9px; vertical-align:middle; color:#1A2B21; word-break:normal; overflow-wrap:break-word; }
table.dt tr:nth-child(even) td { background:#EEF3EA; }
.num { direction:ltr; unicode-bidi:embed; font-variant-numeric:tabular-nums; white-space:nowrap; font-family:"Courier New",monospace; font-weight:700; }
.note-cell { white-space:pre-wrap; word-break:break-word; }
.empty { text-align:center; color:#4A6352; padding:20px; font-size:10px; background:#EEF3EA; }
.ftr { width:100%; border-collapse:separate; margin-top:8px; border-top:1px solid #C4D3BD; }
.ftr td { border:none; background:transparent; padding:2px 0 0; font-size:7.5px; color:#4A6352; vertical-align:top; }
.ftr-r { text-align:right; }
html[dir="rtl"] .ftr-r { text-align:left; }
.w-name     { width:30%; }
.w-selling  { width:17%; }
.w-recipe-cost { width:17%; }
.w-cost-unit   { width:17%; }
.w-total-cost  { width:17%; }
.w-yield    { width:14%; }
.w-notes    { width:22%; }
</style>
</head>
<body>
@php
$isAr = app()->getLocale() === 'ar';
$__logoB64  = '';
$__logoFile = public_path('images/logo.png');
$__logoB64  = \App\Support\PdfExportRenderer::logoBase64($__logoFile, 40, 40);

$isArabic      = $isAr;
$scope         = ($scope ?? 'products') === 'semi_finished' ? 'semi_finished' : 'products';
$rows          = collect($rows ?? []);
$totalCount    = (int)($totalCount    ?? $rows->count());
$exportedCount = (int)($exportedCount ?? $rows->count());
$isTruncated   = (bool)($isTruncated  ?? false);

$reportTitle = $scope === 'semi_finished'
    ? __('ui.recipes.exports.semi_finished_title')
    : __('ui.recipes.exports.products_title');
$scopeLabel = $scope === 'semi_finished'
    ? __('ui.recipes.tabs.semi_finished')
    : __('ui.recipes.tabs.recipes');

$avgCost = $rows->count() > 0
    ? $rows->avg(fn($r) => (float)($r['cost_per_unit'] ?? 0))
    : 0;
@endphp

{{-- Header --}}
<table class="hdr">
<tr>
    @if($__logoB64)<td style="width:46px"><img src="{{ $__logoB64 }}" width="40" height="40" style="width:40px;height:40px" alt=""></td>@endif
    <td class="title-cell">
        <div class="rpt-title">{{ $reportTitle }}</div>
        <div class="rpt-meta">{{ $isAr ? 'تم الإنشاء' : 'Generated' }}: <span dir="ltr" style="unicode-bidi:embed">{{ $generatedAt?->format('Y-m-d g:i A') }}</span> &nbsp;|&nbsp; {{ $isAr ? 'السجلات' : 'Records' }}: {{ number_format($exportedCount) }} / {{ number_format($totalCount) }}</div>
    </td>
</tr>
</table>

<table class="divider"><tr><td class="d1"></td><td class="d2"></td><td class="d3"></td></tr></table>

<div class="filter-bar">
    <b>{{ $isAr ? 'النوع' : 'Scope' }}:</b> {{ $scopeLabel }} &nbsp;|&nbsp;
    <b>{{ $isAr ? 'السجلات' : 'Records' }}:</b> {{ number_format($exportedCount) }} / {{ number_format($totalCount) }}
</div>

@if($isTruncated)
<div class="notice">{{ str_replace([':exported',':total'],[number_format($exportedCount),number_format($totalCount)],__('ui.recipes.exports.truncated_notice')) }}</div>
@endif

<table class="summary">
<tr>
    <td class="sc"><div class="sv">{{ number_format($exportedCount) }}</div><div class="sl">{{ $isAr ? 'إجمالي السجلات' : 'Total Records' }}</div></td>
    <td class="sc gold"><div class="sv">{{ $scopeLabel }}</div><div class="sl">{{ $isAr ? 'النوع' : 'Scope' }}</div></td>
    <td class="sc green"><div class="sv">{{ number_format($avgCost, 3) }}</div><div class="sl">{{ $isAr ? 'متوسط التكلفة للوحدة' : 'Avg Cost/Unit' }}</div></td>
</tr>
</table>

<table class="dt">
    <thead>
        @if($scope === 'semi_finished')
        <tr>
            @if($isArabic)
            <th class="w-notes">{{ __('ui.recipes.fields.notes') }}</th>
            <th class="w-total-cost">{{ __('ui.recipes.table.cost') }}</th>
            <th class="w-cost-unit">{{ __('ui.recipes.table.cost_per_unit') }}</th>
            <th class="w-yield">{{ __('ui.recipes.table.yield') }}</th>
            <th class="w-name">{{ __('ui.recipes.table.name') }}</th>
            @else
            <th class="w-name">{{ __('ui.recipes.table.name') }}</th>
            <th class="w-yield">{{ __('ui.recipes.table.yield') }}</th>
            <th class="w-cost-unit">{{ __('ui.recipes.table.cost_per_unit') }}</th>
            <th class="w-total-cost">{{ __('ui.recipes.table.cost') }}</th>
            <th class="w-notes">{{ __('ui.recipes.fields.notes') }}</th>
            @endif
        </tr>
        @else
        <tr>
            @if($isArabic)
            <th class="w-cost-unit">{{ __('ui.recipes.table.cost_per_unit') }}</th>
            <th class="w-yield">{{ __('ui.recipes.table.yield') }}</th>
            <th class="w-recipe-cost">{{ __('ui.recipes.table.cost') }}</th>
            <th class="w-selling">{{ __('ui.recipes.table.selling_price') }}</th>
            <th class="w-name">{{ __('ui.recipes.table.name') }}</th>
            @else
            <th class="w-name">{{ __('ui.recipes.table.name') }}</th>
            <th class="w-selling">{{ __('ui.recipes.table.selling_price') }}</th>
            <th class="w-recipe-cost">{{ __('ui.recipes.table.cost') }}</th>
            <th class="w-yield">{{ __('ui.recipes.table.yield') }}</th>
            <th class="w-cost-unit">{{ __('ui.recipes.table.cost_per_unit') }}</th>
            @endif
        </tr>
        @endif
    </thead>
    <tbody>
        @forelse($rows as $row)
        @if($scope === 'semi_finished')
        <tr>
            @if($isArabic)
            <td class="note-cell">{{ ($row['notes'] ?? '') !== '' ? $row['notes'] : '-' }}</td>
            <td class="num">{{ number_format((float)($row['total_cost'] ?? 0), 4) }}</td>
            <td class="num">{{ number_format((float)($row['cost_per_unit'] ?? 0), 4) }}</td>
            <td class="num">{{ number_format((float)($row['yield_quantity'] ?? 0), 3) }}</td>
            <td>{{ $row['name'] ?? '-' }}</td>
            @else
            <td>{{ $row['name'] ?? '-' }}</td>
            <td class="num">{{ number_format((float)($row['yield_quantity'] ?? 0), 3) }}</td>
            <td class="num">{{ number_format((float)($row['cost_per_unit'] ?? 0), 4) }}</td>
            <td class="num">{{ number_format((float)($row['total_cost'] ?? 0), 4) }}</td>
            <td class="note-cell">{{ ($row['notes'] ?? '') !== '' ? $row['notes'] : '-' }}</td>
            @endif
        </tr>
        @else
        <tr>
            @if($isArabic)
            <td class="num">{{ number_format((float)($row['cost_per_unit'] ?? 0), 4) }}</td>
            <td class="num">{{ number_format((float)($row['yield_quantity'] ?? 0), 3) }}</td>
            <td class="num">{{ number_format((float)($row['recipe_cost'] ?? 0), 4) }}</td>
            <td class="num">{{ number_format((float)($row['selling_price'] ?? 0), 2) }}</td>
            <td>{{ $row['name'] ?? '-' }}</td>
            @else
            <td>{{ $row['name'] ?? '-' }}</td>
            <td class="num">{{ number_format((float)($row['selling_price'] ?? 0), 2) }}</td>
            <td class="num">{{ number_format((float)($row['recipe_cost'] ?? 0), 4) }}</td>
            <td class="num">{{ number_format((float)($row['yield_quantity'] ?? 0), 3) }}</td>
            <td class="num">{{ number_format((float)($row['cost_per_unit'] ?? 0), 4) }}</td>
            @endif
        </tr>
        @endif
        @empty
        <tr><td colspan="5" class="empty">{{ $scope === 'semi_finished' ? __('ui.recipes.semi_finished.none') : __('ui.recipes.no_products') }}</td></tr>
        @endforelse
    </tbody>
</table>

<table class="ftr">
<tr>
    <td>{{ config('app.name','Point 88') }} — {{ $isAr ? 'نظام إدارة المطاعم' : 'Restaurant Management System' }}</td>
    <td class="ftr-r" dir="ltr" style="unicode-bidi:embed">{{ $generatedAt?->format('Y-m-d g:i A') }}</td>
</tr>
</table>
</body>
</html>
