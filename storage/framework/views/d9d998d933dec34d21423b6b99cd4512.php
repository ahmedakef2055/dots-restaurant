<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo e(app()->getLocale() === 'ar' ? 'تقرير حركة المخزون' : 'Inventory Movement Report'); ?></title>
<style>
@page { margin: 11mm; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; color: #2d1a0e; background: #fff; direction: <?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>; }
html[dir="rtl"] body { direction: rtl; }

/* ── Top header ── */
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
.rpt-title  { font-size: 14px; font-weight: 900; color: #5E7D67; }
.rpt-meta   { font-size: 7.5px; color: #6B5744; margin-top: 2px; }

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
.kpi-cell  { text-align:center; padding:7px 10px; border-radius:8px; border:1px solid #A8C89A; background:#F5F2EC; }
.kpi-label { font-size:7px; font-weight:700; color:#6B5648; text-transform:uppercase; letter-spacing:.5px; }
.kpi-val   { font-size:12px; font-weight:900; color:#5E7D67; margin-top:3px; direction:ltr; }
.kpi-records { background:#eff6ff; border-color:#93C5FD; }
.kpi-records .kpi-val { color:#1d4ed8; }
.kpi-warehouse { background:#E4EBDD; border-color:#A8C89A; }
.kpi-date { background:#f0fdf4; border-color:#86EFAC; }
.kpi-date .kpi-val { font-size:9px; color:#15803d; }

/* ── Notice ── */
.notice { margin-bottom:7px; border:1px solid #fcd34d; background:#fffbeb; color:#92400e; padding:5px 8px; border-radius:6px; font-size:8px; }

/* ── Data table ── */
table.data { width:100%; border-collapse:collapse; margin-top:5px; }
table.data thead tr { background:#5E7D67; color:#FAF8F3; }
table.data thead th { padding:5px 7px; font-size:7.5px; font-weight:700; border:none; }
table.data tbody tr:nth-child(even) td { background:#fdf8f0; }
table.data tbody td { padding:4px 7px; border-bottom:1px solid #f0e6d3; font-size:8px; vertical-align:middle; }

/* ── Action badges ── */
.act-badge { display: inline-block; padding: 2px 6px; border-radius: 10px; font-size: 7px; font-weight: 700; }
.act-add        { background: #d1fae5; color: #065f46; }
.act-deduct     { background: #fee2e2; color: #991b1b; }
.act-adjust     { background: #fef3c7; color: #92400e; }
.act-transfer   { background: #dbeafe; color: #1e40af; }
.act-audit      { background: #ede9fe; color: #5b21b6; }
.act-production { background: #cffafe; color: #164e63; }
.act-default    { background: #f3f4f6; color: #374151; }

.num { direction: ltr; unicode-bidi: embed; font-variant-numeric: tabular-nums; white-space: nowrap; }
.note-cell { white-space: pre-wrap; word-break: break-word; }

/* ── Footer ── */
.footer { text-align: center; font-size: 7px; color: #9C8270; margin-top: 8px; }
</style>
</head>
<body>

<?php
    $isAr = app()->getLocale() === 'ar';

    /* ── Embed logos ── */
    $__logoB64     = '';
    $__logoNameB64 = '';
    $__logoFile    = public_path('images/logo.png');
    $__nameFile    = public_path('images/Logo_Name.png');
    $__logoB64     = \App\Support\PdfExportRenderer::logoBase64($__logoFile, 40, 40);
    $__logoNameB64 = \App\Support\PdfExportRenderer::logoBase64($__nameFile, 108, 28);

    $warehouseName   = $selectedWarehouse?->name ?? '-';
    $totalLogsCount  = (int) ($totalLogsCount ?? $logs->count());
    $exportedCount   = (int) $logs->count();
    $isTruncated     = (bool) ($isTruncated ?? false);

    $rptTitle = $isAr ? 'تقرير حركة المخزون' : 'Inventory Movement Report';
?>


<table class="hdr">
<tr>
    <?php if($__logoB64): ?>
    <td style="width:46px"><img src="<?php echo e($__logoB64); ?>" width="40" height="40" style="width:40px;height:40px" alt=""></td>
    <?php endif; ?>
    <?php if($__logoNameB64): ?>
    <td style="width:118px" class="brand-cell">
        <img src="<?php echo e($__logoNameB64); ?>" width="108" height="28" style="width:108px;height:28px" alt="<?php echo e(config('app.name','Point 88')); ?>">
    </td>
    <?php else: ?>
    <td class="brand-cell">
        <div class="brand-name"><?php echo e(config('app.name','Point 88')); ?></div>
        <div class="brand-sub"><?php echo e($isAr ? 'نظام إدارة المطاعم' : 'Restaurant Management System'); ?></div>
    </td>
    <?php endif; ?>
    <td class="title-cell">
        <div class="rpt-title"><?php echo e($rptTitle); ?></div>
        <div class="rpt-meta">
            <?php echo e($isAr ? 'المستودع' : 'Warehouse'); ?>: <?php echo e($warehouseName); ?>

            &nbsp;|&nbsp;
            <?php echo e($isAr ? 'تم الإنشاء في' : 'Generated at'); ?>: <span dir="ltr" style="unicode-bidi:embed"><?php echo e($generatedAt?->format('Y-m-d g:i A')); ?></span>
        </div>
    </td>
</tr>
</table>

<table class="divider"><tr><td class="d1"></td><td class="d2"></td><td class="d3"></td></tr></table>


<table class="kpi-table">
<tr>
    <td class="kpi-cell kpi-records">
        <div class="kpi-label"><?php echo e($isAr ? 'السجلات المُصدَّرة' : 'Exported Records'); ?></div>
        <div class="kpi-val"><?php echo e(number_format($exportedCount)); ?></div>
    </td>
    <td class="kpi-cell" style="background:#F5F2EC;border-color:#A8C89A">
        <div class="kpi-label"><?php echo e($isAr ? 'إجمالي السجلات' : 'Total Records'); ?></div>
        <div class="kpi-val" style="color:#5E7D67"><?php echo e(number_format($totalLogsCount)); ?></div>
    </td>
    <td class="kpi-cell kpi-warehouse">
        <div class="kpi-label"><?php echo e($isAr ? 'المستودع' : 'Warehouse'); ?></div>
        <div class="kpi-val" style="font-size:10px;color:#5E7D67"><?php echo e($warehouseName); ?></div>
    </td>
    <td class="kpi-cell kpi-date">
        <div class="kpi-label"><?php echo e($isAr ? 'تاريخ التصدير' : 'Generated At'); ?></div>
        <div class="kpi-val"><?php echo e($generatedAt?->format('Y-m-d g:i A')); ?></div>
    </td>
</tr>
</table>

<?php if($isTruncated): ?>
<div class="notice">
    <?php if($isAr): ?>
        تم تصدير أحدث <?php echo e(number_format($exportedCount)); ?> من أصل <?php echo e(number_format($totalLogsCount)); ?> سجل لتحسين سرعة إنشاء الملف.
    <?php else: ?>
        Export includes the latest <?php echo e(number_format($exportedCount)); ?> of <?php echo e(number_format($totalLogsCount)); ?> records to keep PDF generation fast.
    <?php endif; ?>
</div>
<?php endif; ?>


<table class="data">
    <thead>
        <tr>
            <?php if($isAr): ?>
            <th style="width:20%"><?php echo e(__('ui.inventory.logs.headers.note')); ?></th>
            <th style="width:12%"><?php echo e(__('ui.inventory.logs.headers.date')); ?></th>
            <th style="width:12%"><?php echo e(__('ui.inventory.logs.headers.by')); ?></th>
            <th style="width:10%"><?php echo e(__('ui.inventory.logs.headers.after')); ?></th>
            <th style="width:10%"><?php echo e(__('ui.inventory.logs.headers.before')); ?></th>
            <th style="width:10%"><?php echo e(__('ui.inventory.logs.headers.quantity')); ?></th>
            <th style="width:10%"><?php echo e(__('ui.inventory.logs.headers.action')); ?></th>
            <th style="width:16%"><?php echo e(__('ui.inventory.logs.headers.material')); ?></th>
            <?php else: ?>
            <th style="width:16%"><?php echo e(__('ui.inventory.logs.headers.material')); ?></th>
            <th style="width:10%"><?php echo e(__('ui.inventory.logs.headers.action')); ?></th>
            <th style="width:10%"><?php echo e(__('ui.inventory.logs.headers.quantity')); ?></th>
            <th style="width:10%"><?php echo e(__('ui.inventory.logs.headers.before')); ?></th>
            <th style="width:10%"><?php echo e(__('ui.inventory.logs.headers.after')); ?></th>
            <th style="width:12%"><?php echo e(__('ui.inventory.logs.headers.by')); ?></th>
            <th style="width:12%"><?php echo e(__('ui.inventory.logs.headers.date')); ?></th>
            <th style="width:20%"><?php echo e(__('ui.inventory.logs.headers.note')); ?></th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $rawAction  = strtolower((string) ($log->action ?: $log->adjustment_type));
            $actionLabel = match ($rawAction) {
                'add'                => __('ui.inventory.logs.actions.add'),
                'adjust'             => __('ui.inventory.logs.actions.adjust'),
                'deduct'             => __('ui.inventory.logs.actions.deduct'),
                'transfer'           => __('ui.inventory.logs.actions.transfer'),
                'audit'              => __('ui.inventory.logs.actions.audit'),
                'production_consume' => __('ui.inventory.logs.actions.production_consume'),
                'in'                 => __('ui.inventory.logs.actions.in'),
                'out'                => __('ui.inventory.logs.actions.out'),
                'set'                => __('ui.inventory.logs.actions.set'),
                default              => strtoupper((string) ($log->action ?? $log->adjustment_type)),
            };
            $badgeClass = match ($rawAction) {
                'add','in'           => 'act-add',
                'deduct','out'       => 'act-deduct',
                'adjust','set'       => 'act-adjust',
                'transfer'           => 'act-transfer',
                'audit'              => 'act-audit',
                'production_consume' => 'act-production',
                default              => 'act-default',
            };

            $noteText  = trim((string) ($log->note ?? ''));
            $noteLabel = $noteText;
            if (strtolower($noteText) === 'initial stock balance') {
                $noteLabel = __('messages.notes.initial_stock_balance');
            } elseif (preg_match('/^Received via\s+(.+)$/i', $noteText, $m)) {
                $noteLabel = __('messages.notes.received_via_purchase', ['purchase_number' => $m[1]]);
            } elseif (preg_match('/^Consumed by\s+(.+)$/i', $noteText, $m)) {
                $noteLabel = __('messages.notes.consumed_by_order', ['order_number' => $m[1]]);
            } elseif (preg_match('/^Transfer out\s*#\s*(.+)$/i', $noteText, $m)) {
                $noteLabel = __('messages.notes.transfer_out', ['name' => $m[1]]);
            } elseif (preg_match('/^Transfer in\s*#\s*(.+)$/i', $noteText, $m)) {
                $noteLabel = __('messages.notes.transfer_in', ['name' => $m[1]]);
            } elseif (preg_match('/^Stock audit adjustment\s*#\s*(.+)$/i', $noteText, $m)) {
                $noteLabel = __('messages.notes.stock_audit_adjustment', ['audit_id' => $m[1]]);
            } elseif (preg_match('/^Consumed by production:\s*(.+)$/i', $noteText, $m)) {
                $noteLabel = __('messages.notes.production_consumption', ['name' => $m[1]]);
            }

            $ingredientName = $log->ingredient?->name ?? '-';
            $unit           = $log->ingredient?->unit ? strtoupper((string) $log->ingredient->unit) : '';
            $dateLabel      = ($log->occurred_at ?? $log->created_at)?->format('Y-m-d g:i A') ?? '-';
        ?>
        <tr>
            <?php if($isAr): ?>
            <td class="note-cell"><?php echo e($noteLabel ?: '-'); ?></td>
            <td class="num"><?php echo e($dateLabel); ?></td>
            <td><?php echo e($log->user?->name ?? __('ui.inventory.logs.system')); ?></td>
            <td class="num"><?php echo e(number_format((float) $log->new_stock, 3)); ?></td>
            <td class="num"><?php echo e(number_format((float) $log->previous_stock, 3)); ?></td>
            <td class="num"><?php echo e(number_format((float) $log->quantity, 3)); ?><?php echo e($unit ? ' '.$unit : ''); ?></td>
            <td><span class="act-badge <?php echo e($badgeClass); ?>"><?php echo e($actionLabel); ?></span></td>
            <td><?php echo e($ingredientName); ?></td>
            <?php else: ?>
            <td><?php echo e($ingredientName); ?></td>
            <td><span class="act-badge <?php echo e($badgeClass); ?>"><?php echo e($actionLabel); ?></span></td>
            <td class="num"><?php echo e(number_format((float) $log->quantity, 3)); ?><?php echo e($unit ? ' '.$unit : ''); ?></td>
            <td class="num"><?php echo e(number_format((float) $log->previous_stock, 3)); ?></td>
            <td class="num"><?php echo e(number_format((float) $log->new_stock, 3)); ?></td>
            <td><?php echo e($log->user?->name ?? __('ui.inventory.logs.system')); ?></td>
            <td class="num"><?php echo e($dateLabel); ?></td>
            <td class="note-cell"><?php echo e($noteLabel ?: '-'); ?></td>
            <?php endif; ?>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="8" style="text-align:center;padding:20px;color:#9C8270;">
                <?php echo e(__('ui.inventory.logs.none')); ?>

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
<?php /**PATH /var/www/dots-main/resources/views/inventory/exports/stock-logs-pdf.blade.php ENDPATH**/ ?>