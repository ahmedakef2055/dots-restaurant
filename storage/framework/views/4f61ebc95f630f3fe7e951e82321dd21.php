<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo e(__('ui.financial.exports.title')); ?></title>
<style>
@page { margin: 11mm; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; color: #1A2B21; background: #fff; direction: <?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>; }
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
.rpt-title  { font-size: 14px; font-weight: 900; color: #5E7D67; }
.rpt-meta   { font-size: 7.5px; color: #4A6352; margin-top: 2px; }

/* ── Divider ── */
.divider { width:100%; border-collapse:collapse; margin:6px 0 8px; }
.divider td { height:3px; border:none; padding:0; }
.d1 { background:#5E7D67; width:45%; }
.d2 { background:#A8C89A; width:35%; }
.d3 { background:#F5F2EC; width:20%; }
html[dir="rtl"] .d1 { background:#F5F2EC; }
html[dir="rtl"] .d3 { background:#5E7D67; }

/* ── KPI strip ── */
.kpi-table { width:100%; border-collapse:separate; border-spacing:6px 0; margin-bottom:8px; }
.kpi-cell  { text-align:center; padding:7px 10px; border-radius:8px; border:1px solid #A8C89A; }
.kpi-label { font-size:7px; font-weight:700; color:#4A6352; text-transform:uppercase; letter-spacing:.5px; }
.kpi-val   { font-size:13px; font-weight:900; color:#5E7D67; margin-top:3px; direction:ltr; }
.kpi-income  { background:#f0faf0; border-color:#86EFAC; }
.kpi-income .kpi-val { color:#15803d; }
.kpi-expense { background:#fef2f2; border-color:#FCA5A5; }
.kpi-expense .kpi-val { color:#dc2626; }
.kpi-net-pos { background:#eff6ff; border-color:#93C5FD; }
.kpi-net-pos .kpi-val { color:#1d4ed8; }
.kpi-net-neg { background:#fef2f2; border-color:#FCA5A5; }
.kpi-net-neg .kpi-val { color:#dc2626; }
.kpi-period  { background:#E4EBDD; border-color:#A8C89A; }
.kpi-period .kpi-val { font-size:10px; color:#5E7D67; }

/* ── Data table ── */
table.data { width:100%; border-collapse:collapse; margin-top:5px; }
table.data thead tr { background:#5E7D67; color:#FAF8F3; }
table.data thead th { padding:5px 7px; font-size:7.5px; font-weight:700; border:none; }
table.data tbody tr:nth-child(even) td { background:#fdf8f0; }
table.data tbody td { padding:4px 7px; border-bottom:1px solid #f0e6d3; font-size:8px; vertical-align:middle; }

/* ── Badges ── */
.type-badge { display: inline-block; padding: 2px 7px; border-radius: 12px; font-size: 7px; font-weight: 700; }
.type-order    { background: #d1fae5; color: #065f46; }
.type-purchase { background: #fee2e2; color: #991b1b; }
.type-salary   { background: #ede9fe; color: #5b21b6; }

.pay-badge { display: inline-block; padding: 1px 6px; border-radius: 12px; font-size: 7px; font-weight: 600; }
.pay-cash          { background: #dcfce7; color: #166534; }
.pay-card          { background: #dbeafe; color: #1e40af; }
.pay-credit        { background: #fef3c7; color: #92400e; }
.pay-bank_transfer { background: #ede9fe; color: #5b21b6; }
.pay-wallet        { background: #cffafe; color: #164e63; }

.status-paid    { background: #d1fae5; color: #065f46; }
.status-partial { background: #fef3c7; color: #92400e; }
.status-default { background: #f3f4f6; color: #374151; }

.amount-income  { color: #15803d; font-weight: 700; }
.amount-expense { color: #dc2626; font-weight: 700; }

/* ── Footer ── */
.footer { text-align: center; font-size: 7px; color: #527060; margin-top: 8px; }
</style>
</head>
<body>

<?php
    $isAr = app()->getLocale() === 'ar';
    /* ── Embed logos ── */
    $__logoB64  = '';
    $__logoFile = public_path('images/logo.png');
    $__logoB64  = \App\Support\PdfExportRenderer::logoBase64($__logoFile, 40, 40);

    $formatter = \App\Support\CurrencyFormatter::class;
    $incomeTotal  = $transactions->where('category','income')->sum('raw_amount');
    $expenseTotal = $transactions->where('category','expense')->sum('raw_amount');
    $net          = $incomeTotal - $expenseTotal;
?>


<table class="hdr">
<tr>
    <?php if($__logoB64): ?>
    <td style="width:46px"><img src="<?php echo e($__logoB64); ?>" width="40" height="40" style="width:40px;height:40px" alt=""></td>
    <?php endif; ?>
    <td class="title-cell">
        <div class="rpt-title"><?php echo e(__('ui.financial.exports.title')); ?></div>
        <div class="rpt-meta">
            <?php echo e(__('ui.financial.exports.period')); ?>: <span dir="ltr" style="unicode-bidi:embed"><?php echo e($from); ?> → <?php echo e($to); ?></span>
            &nbsp;|&nbsp;
            <?php echo e(__('ui.financial.exports.generated_at')); ?>: <span dir="ltr" style="unicode-bidi:embed"><?php echo e($generatedAt?->format('Y-m-d g:i A')); ?></span>
        </div>
    </td>
</tr>
</table>

<table class="divider"><tr><td class="d1"></td><td class="d2"></td><td class="d3"></td></tr></table>


<table class="kpi-table">
<tr>
    <td class="kpi-cell kpi-income">
        <div class="kpi-label"><?php echo e(__('ui.financial.summary.total_income')); ?></div>
        <div class="kpi-val"><?php echo e($formatter::format($incomeTotal)); ?></div>
    </td>
    <td class="kpi-cell kpi-expense">
        <div class="kpi-label"><?php echo e(__('ui.financial.summary.total_expenses')); ?></div>
        <div class="kpi-val"><?php echo e($formatter::format($expenseTotal)); ?></div>
    </td>
    <td class="kpi-cell <?php echo e($net >= 0 ? 'kpi-net-pos' : 'kpi-net-neg'); ?>">
        <div class="kpi-label"><?php echo e(__('ui.financial.summary.net')); ?></div>
        <div class="kpi-val"><?php echo e($formatter::format($net)); ?></div>
    </td>
    <td class="kpi-cell kpi-period">
        <div class="kpi-label"><?php echo e($isAr ? 'الفترة' : 'Period'); ?></div>
        <div class="kpi-val" dir="ltr"><?php echo e($from); ?> → <?php echo e($to); ?></div>
    </td>
    <td class="kpi-cell" style="background:#F5F2EC;border-color:#A8C89A">
        <div class="kpi-label"><?php echo e($isAr ? 'عدد المعاملات' : 'Transactions'); ?></div>
        <div class="kpi-val" style="color:#5E7D67"><?php echo e($transactions->count()); ?></div>
    </td>
</tr>
</table>


<table class="data">
    <thead>
        <tr>
            <th><?php echo e(__('ui.financial.table.type')); ?></th>
            <th><?php echo e(__('ui.financial.table.reference')); ?></th>
            <th><?php echo e(__('ui.financial.table.description')); ?></th>
            <th><?php echo e(__('ui.financial.table.payment_method')); ?></th>
            <th><?php echo e(__('ui.financial.table.amount')); ?></th>
            <th><?php echo e(__('ui.financial.table.remaining')); ?></th>
            <th><?php echo e(__('ui.financial.table.actor')); ?></th>
            <th><?php echo e(__('ui.financial.table.status')); ?></th>
            <th><?php echo e(__('ui.financial.table.date')); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td><span class="type-badge type-<?php echo e($row['type']); ?>"><?php echo e($row['type_label']); ?></span></td>
            <td dir="ltr" style="unicode-bidi:embed"><?php echo e($row['reference']); ?></td>
            <td><?php echo e($row['description']); ?></td>
            <td><span class="pay-badge pay-<?php echo e($row['payment_method']); ?>"><?php echo e($row['payment_method_label']); ?></span></td>
            <td class="<?php echo e($row['category'] === 'income' ? 'amount-income' : 'amount-expense'); ?>" dir="ltr" style="unicode-bidi:embed">
                <?php echo e($row['category'] === 'income' ? '+' : '−'); ?><?php echo e($row['amount']); ?>

            </td>
            <td dir="ltr" style="unicode-bidi:embed"><?php echo e($row['remaining'] ?? '—'); ?></td>
            <td><?php echo e($row['actor']); ?></td>
            <td>
                <span class="pay-badge
                    <?php echo e($row['status'] === 'paid'    ? 'status-paid'
                    : ($row['status'] === 'partial' ? 'status-partial'
                    : 'status-default')); ?>">
                    <?php echo e($row['status_label']); ?>

                </span>
            </td>
            <td dir="ltr" style="unicode-bidi:embed"><?php echo e($row['date']); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="9" style="text-align:center;padding:20px;color:#527060;">
                <?php echo e(__('ui.financial.table.no_results')); ?>

            </td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="footer">
    <?php echo e(config('app.name','Point 88')); ?> &nbsp;·&nbsp;
    <?php echo e($isAr ? 'تم الإنشاء في' : 'Generated at'); ?>: <span dir="ltr" style="unicode-bidi:embed"><?php echo e($generatedAt?->format('Y-m-d g:i A')); ?></span>
</div>

</body>
</html>
<?php /**PATH /var/www/dots-main/resources/views/financial/exports/financial-pdf.blade.php ENDPATH**/ ?>