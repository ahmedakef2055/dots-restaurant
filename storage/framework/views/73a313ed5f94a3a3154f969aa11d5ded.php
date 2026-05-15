<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo e(__('ui.customers.exports.title')); ?></title>
<style>
@page { margin: 11mm; }
* { box-sizing: border-box; }
body {
    margin: 0;
    font-family: "DejaVu Sans", "Noto Naskh Arabic", "Noto Sans Arabic", sans-serif;
    font-size: 10px;
    line-height: 1.4;
    color: #3E2B23;
    background: #ffffff;
}
html[dir="rtl"] body { direction: rtl; }
.hdr { width: 100%; border-collapse: separate; }
.hdr td { border: none; background: transparent; padding: 0; vertical-align: middle; }
.logo      { max-height: 40px; max-width: 44px; object-fit: contain; }
.logo-name { max-height: 32px; max-width: 110px; object-fit: contain; }
.brand-cell { padding-left: 8px !important; }
html[dir="rtl"] .brand-cell { padding-left: 0 !important; padding-right: 8px !important; }
.brand-name { font-size: 15px; font-weight: 900; color: #5E7D67; letter-spacing: -0.2px; }
.brand-sub  { font-size: 8px; color: #6B5648; margin-top: 1px; }
.title-cell { text-align: right; }
html[dir="rtl"] .title-cell { text-align: left; }
.rpt-title { font-size: 14px; font-weight: 800; color: #5E7D67; }
.rpt-meta  { font-size: 8px; color: #6B5648; margin-top: 2px; }
.divider { width: 100%; border-collapse: collapse; margin: 6px 0 8px; }
.divider td { height: 3px; border: none; padding: 0; }
.d1 { background: #5E7D67; width: 45%; }
.d2 { background: #A8C89A; width: 35%; }
.d3 { background: #F5F2EC; width: 20%; }
html[dir="rtl"] .d1 { background: #F5F2EC; }
html[dir="rtl"] .d3 { background: #5E7D67; }
.filter-bar {
    background: #F5F2EC;
    border: 1px solid #D8CCBC;
    border-radius: 5px;
    padding: 4px 9px;
    margin-bottom: 7px;
    font-size: 8.5px;
    color: #5E7D67;
    line-height: 1.8;
}
.notice {
    background: #FFFBEB;
    border: 1px solid #FCD34D;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 8.5px;
    font-weight: 700;
    color: #92400E;
    margin-bottom: 7px;
}
.summary { width: 100%; border-collapse: separate; border-spacing: 5px 0; margin-bottom: 8px; }
.sc { border-radius: 5px; padding: 5px 8px; text-align: center; vertical-align: top; border: 1px solid #D8CCBC; background: #FAF8F3; }
.sc.gold { background: #A8C89A; border-color: #BFD1BC; }
.sv { font-size: 13px; font-weight: 900; color: #3E2B23; }
.sl { font-size: 7.5px; color: #5E7D67; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; }
table.dt { width: 100%; border-collapse: collapse; }
table.dt thead { display: table-header-group; }
table.dt tr { page-break-inside: avoid; }
table.dt th {
    background: #5E7D67; color: #fff; font-weight: 700;
    padding: 5px 6px; font-size: 8.5px;
    text-transform: uppercase; letter-spacing: 0.04em;
    border: 1px solid #4F6A57;
}
table.dt td { padding: 4px 6px; border: 1px solid #D8CCBC; font-size: 9px; vertical-align: middle; color: #3E2B23; }
table.dt tr:nth-child(even) td { background: #F5F2EC; }
.num { direction: ltr; unicode-bidi: embed; font-variant-numeric: tabular-nums; white-space: nowrap; font-family: "Courier New", monospace; font-weight: 700; text-align: right; }
html[dir="rtl"] .num { text-align: left; }
.badge { display: inline-block; padding: 1px 5px; border-radius: 9px; font-size: 7.5px; font-weight: 700; white-space: nowrap; }
.b-vip  { background: #A8C89A; color: #3E2B23; }
.b-norm { background: #FAF8F3; color: #5E7D67; }
.ftr { width: 100%; border-collapse: separate; margin-top: 8px; border-top: 1px solid #D8CCBC; }
.ftr td { border: none; background: transparent; padding: 2px 0 0; font-size: 7.5px; color: #6B5648; vertical-align: top; }
.ftr-r { text-align: right; }
html[dir="rtl"] .ftr-r { text-align: left; }
</style>
</head>
<body>
<?php
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

$totalOrders  = $rows->sum(fn($r) => (int)($r['orders_count'] ?? 0));
$totalRevenue = $rows->sum(fn($r) => (float)($r['total_spent'] ?? 0));
$vipCount     = $rows->filter(fn($r) => ($r['customer_type'] ?? '') === 'vip')->count();
?>

<table class="hdr">
<tr>
    <?php if($__logoB64): ?>
    <td style="width:46px"><img src="<?php echo e($__logoB64); ?>" width="40" height="40" style="width:40px;height:40px" alt=""></td>
    <?php endif; ?>
    <td class="title-cell">
        <div class="rpt-title"><?php echo e(__('ui.customers.exports.title')); ?></div>
        <div class="rpt-meta">
            <?php echo e($isAr ? 'أُنشئ' : 'Generated'); ?>: <span dir="ltr" style="unicode-bidi:embed"><?php echo e($generatedAt?->format('Y-m-d g:i A')); ?></span>
            &nbsp;|&nbsp;
            <?php echo e($isAr ? 'السجلات' : 'Records'); ?>: <?php echo e(number_format($exportedCount)); ?> / <?php echo e(number_format($totalCount)); ?>

        </div>
    </td>
</tr>
</table>
<table class="divider"><tr><td class="d1"></td><td class="d2"></td><td class="d3"></td></tr></table>

<div class="filter-bar">
    <strong><?php echo e($isAr ? 'البحث' : 'Search'); ?>:</strong> <?php echo e($searchQuery !== '' ? $searchQuery : ($isAr ? 'الكل' : 'All')); ?>

    &nbsp;&nbsp;
    <strong><?php echo e($isAr ? 'السجلات المُصدَّرة' : 'Exported'); ?>:</strong> <?php echo e(number_format($exportedCount)); ?> / <?php echo e(number_format($totalCount)); ?>

</div>

<?php if($isTruncated): ?>
<div class="notice">
    <?php echo e(str_replace([':exported',':total'],[number_format($exportedCount),number_format($totalCount)], __('ui.customers.exports.truncated_notice'))); ?>

</div>
<?php endif; ?>

<table class="summary">
<tr>
    <td class="sc"><div class="sv"><?php echo e(number_format($exportedCount)); ?></div><div class="sl"><?php echo e($isAr ? 'إجمالي العملاء' : 'Total Customers'); ?></div></td>
    <td class="sc gold"><div class="sv"><?php echo e(number_format($vipCount)); ?></div><div class="sl">VIP</div></td>
    <td class="sc"><div class="sv"><?php echo e(number_format($totalOrders)); ?></div><div class="sl"><?php echo e($isAr ? 'إجمالي الطلبات' : 'Total Orders'); ?></div></td>
    <td class="sc gold"><div class="sv"><?php echo e(\App\Support\CurrencyFormatter::format($totalRevenue)); ?></div><div class="sl"><?php echo e($isAr ? 'إجمالي الإيرادات' : 'Total Revenue'); ?></div></td>
</tr>
</table>

<table class="dt">
<thead>
<tr>
    <th style="width:4%">#</th>
    <th style="width:22%"><?php echo e(__('ui.customers.table.name')); ?></th>
    <th style="width:13%"><?php echo e(__('ui.customers.table.phone')); ?></th>
    <th style="width:9%"><?php echo e(__('ui.customers.table.type')); ?></th>
    <th style="width:8%"><?php echo e(__('ui.customers.table.orders')); ?></th>
    <th style="width:16%"><?php echo e(__('ui.customers.table.total_spent')); ?></th>
    <th style="width:28%"><?php echo e(__('ui.customers.table.last_order')); ?></th>
</tr>
</thead>
<tbody>
<?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<?php
$typeText = ($row['customer_type'] ?? 'normal') === 'vip' ? __('ui.customers.types.vip') : __('ui.customers.types.normal');
$isVip    = ($row['customer_type'] ?? 'normal') === 'vip';
$lastOrderRaw = trim((string)($row['last_order_at'] ?? ''));
$lastOrderTxt = '-';
if ($lastOrderRaw !== '') {
    try { $lastOrderTxt = \Illuminate\Support\Carbon::parse($lastOrderRaw)->format('Y-m-d g:i A'); } catch (\Throwable) { $lastOrderTxt = $lastOrderRaw; }
}
?>
<tr>
    <td><?php echo e($i + 1); ?></td>
    <td><?php echo e(($row['name'] ?? '') !== '' ? $row['name'] : '-'); ?></td>
    <td class="num"><?php echo e(($row['phone'] ?? '') !== '' ? $row['phone'] : '-'); ?></td>
    <td><span class="badge <?php echo e($isVip ? 'b-vip' : 'b-norm'); ?>"><?php echo e($typeText); ?></span></td>
    <td class="num"><?php echo e(number_format((int)($row['orders_count'] ?? 0))); ?></td>
    <td class="num"><?php echo e(\App\Support\CurrencyFormatter::format((float)($row['total_spent'] ?? 0))); ?></td>
    <td class="num"><?php echo e($lastOrderTxt); ?></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="7" style="text-align:center;padding:14px;color:#6B5648;font-style:italic"><?php echo e(__('ui.customers.exports.no_results')); ?></td></tr>
<?php endif; ?>
</tbody>
</table>

<table class="ftr">
<tr>
    <td><?php echo e(config('app.name','Point 88')); ?> &mdash; <?php echo e($isAr ? 'نظام إدارة المطاعم' : 'Restaurant Management System'); ?></td>
    <td class="ftr-r"><?php echo e($generatedAt?->format('Y-m-d g:i A')); ?></td>
</tr>
</table>
</body>
</html>
<?php /**PATH /var/www/dots-main/resources/views/customers/exports/customers-pdf.blade.php ENDPATH**/ ?>