<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>{{ __('ui.attendance.exports.title') }}</title>
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
.sc.red   { background:#FEE2E2; border-color:#FCA5A5; }
.sv { font-size:13px; font-weight:900; color:#1A2B21; }
.sl { font-size:7.5px; color:#5E7D67; text-transform:uppercase; letter-spacing:0.05em; margin-top:2px; }
.sc.gold .sv { color:#4F6A57; }
.sc.gold .sl { color:#4F6A57; }
table.dt { width:100%; border-collapse:collapse; }
table.dt thead { display:table-header-group; }
table.dt tr { page-break-inside:avoid; }
table.dt th { background:#5E7D67; color:#fff; font-weight:700; padding:5px 6px; font-size:8.5px; text-transform:uppercase; letter-spacing:0.04em; border:1px solid #4F6A57; }
table.dt td { padding:4px 6px; border:1px solid #C4D3BD; font-size:9px; vertical-align:middle; color:#1A2B21; }
table.dt tr:nth-child(even) td { background:#EEF3EA; }
.num { direction:ltr; unicode-bidi:embed; font-variant-numeric:tabular-nums; white-space:nowrap; font-family:"Courier New",monospace; font-weight:700; }
.empty { text-align:center; color:#4A6352; padding:20px; font-size:10px; background:#EEF3EA; }
.ftr { width:100%; border-collapse:separate; margin-top:8px; border-top:1px solid #C4D3BD; }
.ftr td { border:none; background:transparent; padding:2px 0 0; font-size:7.5px; color:#4A6352; vertical-align:top; }
.ftr-r { text-align:right; }
html[dir="rtl"] .ftr-r { text-align:left; }
.w-date     { width:10%; }
.w-employee { width:18%; }
.w-status   { width:10%; }
.w-time     { width:16%; }
.w-work     { width:10%; }
.w-note     { width:36%; }
</style>
</head>
<body>
@php
$isAr = app()->getLocale() === 'ar';
$__logoB64  = '';
$__logoFile = public_path('images/logo.png');
$__logoB64  = \App\Support\PdfExportRenderer::logoBase64($__logoFile, 40, 40);

$rows          = collect($rows ?? []);
$filters       = $filters ?? [];
$totalCount    = (int)($totalCount    ?? $rows->count());
$exportedCount = (int)($exportedCount ?? $rows->count());
$isTruncated   = (bool)($isTruncated  ?? false);

$employeeFilterLabel = trim((string)($filters['employee_label'] ?? ''));
if ($employeeFilterLabel === '' && !empty($filters['employee_id'])) { $employeeFilterLabel = '#'.(int)$filters['employee_id']; }
$statusFilter = trim((string)($filters['status'] ?? ''));
$statusFilterLabel = match($statusFilter) {
    'present'  => __('ui.attendance.statuses.present'),
    'late'     => __('ui.attendance.statuses.late'),
    'half_day' => __('ui.attendance.statuses.half_day'),
    'leave'    => __('ui.attendance.statuses.leave'),
    'absent'   => __('ui.attendance.statuses.absent'),
    default    => __('ui.attendance.exports.all_statuses'),
};
$fromDate = trim((string)($filters['from'] ?? ''));
$toDate   = trim((string)($filters['to']   ?? ''));
if ($fromDate !== '' || $toDate !== '') {
    $dateRange = ($fromDate ?: __('ui.attendance.exports.any')) . ' - ' . ($toDate ?: __('ui.attendance.exports.any'));
} else {
    $dateRange = __('ui.attendance.exports.all');
}

$presentCount  = $rows->filter(fn($r) => in_array($r['status'] ?? '', ['present','late','half_day']))->count();
$absentCount   = $rows->filter(fn($r) => ($r['status'] ?? '') === 'absent')->count();
$totalHours    = $rows->sum(fn($r) => (float)($r['work_hours'] ?? 0));
@endphp

{{-- Header --}}
<table class="hdr">
<tr>
    @if($__logoB64)<td style="width:46px"><img src="{{ $__logoB64 }}" width="40" height="40" style="width:40px;height:40px" alt=""></td>@endif
    <td class="title-cell">
        <div class="rpt-title">{{ __('ui.attendance.exports.title') }}</div>
        <div class="rpt-meta">{{ $isAr ? 'تم الإنشاء' : 'Generated' }}: <span dir="ltr" style="unicode-bidi:embed">{{ $generatedAt?->format('Y-m-d g:i A') }}</span></div>
    </td>
</tr>
</table>

<table class="divider"><tr><td class="d1"></td><td class="d2"></td><td class="d3"></td></tr></table>

<div class="filter-bar">
    <b>{{ __('ui.attendance.exports.employee_filter') }}:</b> {{ $employeeFilterLabel !== '' ? $employeeFilterLabel : __('ui.attendance.exports.all') }} &nbsp;|&nbsp;
    <b>{{ __('ui.attendance.exports.status_filter') }}:</b> {{ $statusFilterLabel }} &nbsp;|&nbsp;
    <b>{{ __('ui.attendance.exports.date_range') }}:</b> {{ $dateRange }} &nbsp;|&nbsp;
    <b>{{ $isAr ? 'السجلات' : 'Records' }}:</b> {{ number_format($exportedCount) }} / {{ number_format($totalCount) }}
</div>

@if($isTruncated)
<div class="notice">{{ str_replace([':exported',':total'],[number_format($exportedCount),number_format($totalCount)],__('ui.attendance.exports.truncated_notice')) }}</div>
@endif

<table class="summary">
<tr>
    <td class="sc"><div class="sv">{{ number_format($exportedCount) }}</div><div class="sl">{{ $isAr ? 'إجمالي السجلات' : 'Total Records' }}</div></td>
    <td class="sc green"><div class="sv">{{ number_format($presentCount) }}</div><div class="sl">{{ $isAr ? 'حضور' : 'Present' }}</div></td>
    <td class="sc red"><div class="sv">{{ number_format($absentCount) }}</div><div class="sl">{{ $isAr ? 'غياب' : 'Absent' }}</div></td>
    <td class="sc gold"><div class="sv">{{ number_format($totalHours, 1) }}</div><div class="sl">{{ $isAr ? 'إجمالي ساعات العمل' : 'Total Work Hours' }}</div></td>
</tr>
</table>

<table class="dt">
    <thead>
        <tr>
            @if($isAr)
            <th class="w-note">{{ __('ui.attendance.exports.table.note') }}</th>
            <th class="w-work">{{ __('ui.attendance.exports.table.work_hours') }}</th>
            <th class="w-time">{{ __('ui.attendance.exports.table.time_range') }}</th>
            <th class="w-status">{{ __('ui.attendance.exports.table.status') }}</th>
            <th class="w-employee">{{ __('ui.attendance.exports.table.employee') }}</th>
            <th class="w-date">{{ __('ui.attendance.exports.table.date') }}</th>
            @else
            <th class="w-date">{{ __('ui.attendance.exports.table.date') }}</th>
            <th class="w-employee">{{ __('ui.attendance.exports.table.employee') }}</th>
            <th class="w-status">{{ __('ui.attendance.exports.table.status') }}</th>
            <th class="w-time">{{ __('ui.attendance.exports.table.time_range') }}</th>
            <th class="w-work">{{ __('ui.attendance.exports.table.work_hours') }}</th>
            <th class="w-note">{{ __('ui.attendance.exports.table.note') }}</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($rows as $row)
        @php
        $statusText = match($row['status'] ?? 'absent') {
            'present'  => __('ui.attendance.statuses.present'),
            'late'     => __('ui.attendance.statuses.late'),
            'half_day' => __('ui.attendance.statuses.half_day'),
            'leave'    => __('ui.attendance.statuses.leave'),
            default    => __('ui.attendance.statuses.absent'),
        };
        $noteText = trim((string)($row['note'] ?? ''));
        @endphp
        <tr>
            @if($isAr)
            <td>{{ $noteText !== '' ? $noteText : '-' }}</td>
            <td class="num">{{ $row['work_hours'] !== null ? number_format((float)$row['work_hours'], 2) : '-' }}</td>
            <td class="num">{{ $row['time_label'] ?? '--:-- - --:--' }}</td>
            <td>{{ $statusText }}</td>
            <td>{{ ($row['employee_label'] ?? '') !== '' ? $row['employee_label'] : '-' }}</td>
            <td class="num">{{ ($row['attendance_date'] ?? '') !== '' ? $row['attendance_date'] : '-' }}</td>
            @else
            <td class="num">{{ ($row['attendance_date'] ?? '') !== '' ? $row['attendance_date'] : '-' }}</td>
            <td>{{ ($row['employee_label'] ?? '') !== '' ? $row['employee_label'] : '-' }}</td>
            <td>{{ $statusText }}</td>
            <td class="num">{{ $row['time_label'] ?? '--:-- - --:--' }}</td>
            <td class="num">{{ $row['work_hours'] !== null ? number_format((float)$row['work_hours'], 2) : '-' }}</td>
            <td>{{ $noteText !== '' ? $noteText : '-' }}</td>
            @endif
        </tr>
        @empty
        <tr><td colspan="6" class="empty">{{ __('ui.attendance.exports.no_results') }}</td></tr>
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
