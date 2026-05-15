<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>{{ __('ui.purchases.exports.title') }}</title>
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
.sc.amber { background:#FEF3C7; border-color:#FCD34D; }
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
.badge { display:inline-block; padding:1px 5px; border-radius:9px; font-size:7.5px; font-weight:700; white-space:nowrap; }
.b-completed { background:#DCFCE7; color:#166534; }
.b-approved  { background:#DBEAFE; color:#1E40AF; }
.b-pending   { background:#FEF3C7; color:#92400E; }
.b-rejected  { background:#FEE2E2; color:#991B1B; }
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
$searchQuery        = trim((string)($filters['q'] ?? ''));
$supplierLabel      = trim((string)($filters['supplier_name'] ?? ''));
if ($supplierLabel === '' && !empty($filters['supplier_id'])) { $supplierLabel = '#'.(int)$filters['supplier_id']; }
$requestTypeFilter  = trim((string)($filters['request_type'] ?? ''));
$requestTypeLabel   = match ($requestTypeFilter) {
    'inventory'       => __('ui.purchases.request_types.inventory'),
    'general_expense' => __('ui.purchases.request_types.general_expense'),
    default           => __('ui.purchases.exports.all_types'),
};
$approvalFilter = trim((string)($filters['approval_status'] ?? ''));
$approvalLabel  = match ($approvalFilter) {
    'pending'   => __('ui.purchases.statuses.pending'),
    'approved'  => __('ui.purchases.statuses.approved'),
    'rejected'  => __('ui.purchases.statuses.rejected'),
    'completed' => __('ui.purchases.statuses.completed'),
    default     => __('ui.purchases.exports.all_statuses'),
};
$fromDate = trim((string)($filters['from'] ?? ''));
$toDate   = trim((string)($filters['to']   ?? ''));
$dateRange = ($fromDate !== '' || $toDate !== '')
    ? (($fromDate ?: __('ui.purchases.exports.any')).' - '.($toDate ?: __('ui.purchases.exports.any')))
    : __('ui.purchases.exports.all');

$totalAmount    = $rows->sum(fn($r) => (float)($r['total'] ?? 0));
$completedCount = $rows->filter(fn($r) => ($r['approval_status'] ?? '') === 'completed')->count();
$pendingCount   = $rows->filter(fn($r) => in_array($r['approval_status'] ?? '', ['pending','approved']))->count();
@endphp

<table class="hdr">
<tr>
    @if($__logoB64)
    <td style="width:46px"><img src="{{ $__logoB64 }}" width="40" height="40" style="width:40px;height:40px" alt=""></td>
    @endif
    <td class="title-cell">
        <div class="rpt-title">{{ __('ui.purchases.exports.title') }}</div>
        <div class="rpt-meta">{{ $isAr ? 'أُنشئ' : 'Generated' }}: <span dir="ltr" style="unicode-bidi:embed">{{ $generatedAt?->format('Y-m-d g:i A') }}</span> &nbsp;|&nbsp; {{ $isAr ? 'السجلات' : 'Records' }}: {{ number_format($exportedCount) }} / {{ number_format($totalCount) }}</div>
    </td>
</tr>
</table>
<table class="divider"><tr><td class="d1"></td><td class="d2"></td><td class="d3"></td></tr></table>

<div class="filter-bar">
    <strong>{{ $isAr ? 'البحث' : 'Search' }}:</strong> {{ $searchQuery !== '' ? $searchQuery : ($isAr ? 'الكل' : 'All') }}
    &nbsp;&nbsp;<strong>{{ $isAr ? 'المورد' : 'Supplier' }}:</strong> {{ $supplierLabel !== '' ? $supplierLabel : ($isAr ? 'الكل' : 'All') }}
    &nbsp;&nbsp;<strong>{{ $isAr ? 'نوع الطلب' : 'Type' }}:</strong> {{ $requestTypeLabel }}
    &nbsp;&nbsp;<strong>{{ $isAr ? 'حالة الموافقة' : 'Approval' }}:</strong> {{ $approvalLabel }}
    &nbsp;&nbsp;<strong>{{ $isAr ? 'التاريخ' : 'Date' }}:</strong> {{ $dateRange }}
</div>

@if($isTruncated)
<div class="notice">{{ str_replace([':exported',':total'],[number_format($exportedCount),number_format($totalCount)], __('ui.purchases.exports.truncated_notice')) }}</div>
@endif

<table class="summary">
<tr>
    <td class="sc"><div class="sv">{{ number_format($exportedCount) }}</div><div class="sl">{{ $isAr ? 'إجمالي الطلبات' : 'Total Orders' }}</div></td>
    <td class="sc green"><div class="sv" style="color:#166534">{{ number_format($completedCount) }}</div><div class="sl" style="color:#166534">{{ $isAr ? 'مكتملة' : 'Completed' }}</div></td>
    <td class="sc amber"><div class="sv" style="color:#92400E">{{ number_format($pendingCount) }}</div><div class="sl" style="color:#92400E">{{ $isAr ? 'قيد الانتظار' : 'Pending' }}</div></td>
    <td class="sc gold"><div class="sv">{{ \App\Support\CurrencyFormatter::format($totalAmount) }}</div><div class="sl">{{ $isAr ? 'إجمالي المبلغ' : 'Total Amount' }}</div></td>
</tr>
</table>

<table class="dt">
<thead>
<tr>
    <th style="width:4%">#</th>
    <th style="width:13%">{{ __('ui.purchases.table.purchase_no') }}</th>
    <th style="width:10%">{{ __('ui.purchases.table.request_type') }}</th>
    <th style="width:17%">{{ __('ui.purchases.table.supplier') }}</th>
    <th style="width:9%">{{ __('ui.purchases.table.date') }}</th>
    <th style="width:10%">{{ __('ui.purchases.table.approval_status') }}</th>
    <th style="width:8%">{{ __('ui.purchases.payment') }}</th>
    <th style="width:6%">{{ __('ui.purchases.table.items') }}</th>
    <th style="width:11%">{{ __('ui.purchases.table.total') }}</th>
    <th style="width:12%">{{ __('ui.purchases.table.by') }}</th>
</tr>
</thead>
<tbody>
@forelse($rows as $i => $row)
@php
$requestTypeText = ($row['request_type'] ?? 'inventory') === 'general_expense'
    ? __('ui.purchases.request_types.general_expense')
    : __('ui.purchases.request_types.inventory');
$approvalStatus = $row['approval_status'] ?? 'pending';
$approvalText = match ($approvalStatus) {
    'completed' => __('ui.purchases.statuses.completed'),
    'approved'  => __('ui.purchases.statuses.approved'),
    'rejected'  => __('ui.purchases.statuses.rejected'),
    default     => __('ui.purchases.statuses.pending'),
};
$approvalBadge = match ($approvalStatus) {
    'completed' => 'b-completed',
    'approved'  => 'b-approved',
    'rejected'  => 'b-rejected',
    default     => 'b-pending',
};
$paymentText = ($row['payment_method'] ?? 'cash') === 'credit'
    ? __('ui.purchases.payment_credit')
    : __('ui.purchases.payment_cash');
$actor = trim((string)($row['user_name'] ?? ''));
@endphp
<tr>
    <td>{{ $i + 1 }}</td>
    <td class="num">{{ $row['purchase_number'] ?? '-' }}</td>
    <td>{{ $requestTypeText }}</td>
    <td>{{ $row['supplier_label'] ?? '-' }}</td>
    <td class="num">{{ ($row['purchase_date'] ?? '') !== '' ? $row['purchase_date'] : '-' }}</td>
    <td><span class="badge {{ $approvalBadge }}">{{ $approvalText }}</span></td>
    <td>{{ $paymentText }}</td>
    <td class="num">{{ number_format((int)($row['items_count'] ?? 0)) }}</td>
    <td class="num">{{ \App\Support\CurrencyFormatter::format((float)($row['total'] ?? 0)) }}</td>
    <td>{{ $actor !== '' ? $actor : __('ui.purchases.system') }}</td>
</tr>
@empty
<tr><td colspan="10" style="text-align:center;padding:14px;color:#4A6352;font-style:italic">{{ __('ui.purchases.exports.no_results') }}</td></tr>
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
