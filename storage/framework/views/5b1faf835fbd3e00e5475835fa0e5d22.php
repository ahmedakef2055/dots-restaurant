<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => $purchase->purchase_number]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($purchase->purchase_number)]); ?>
    <?php
    $requestType = strtolower((string) ($purchase->request_type ?: 'inventory'));
    $approvalStatus = strtolower((string) ($purchase->approval_status ?: 'pending'));
    $purchaseStatus = strtolower((string) ($purchase->status ?: 'pending'));
    $currentUser = auth()->user();
    $canApprovePurchases = $currentUser?->hasPermission('purchases.approve') ?? false;
    $canCreatePurchases = $currentUser?->hasPermission('purchases.create') ?? false;
    $isRequestOwner = (int) ($currentUser?->id ?? 0) === (int) ($purchase->user_id ?? 0);
    $isPendingApproval = $approvalStatus === 'pending';
    $isApprovedRequest = $approvalStatus === 'approved';
    $isCompleted = $purchaseStatus === 'completed';
    $canCompleteRequest = $isRequestOwner && $canCreatePurchases && $isApprovedRequest && ! $isCompleted;

    $purchaseStatusLabel = match ($purchaseStatus) {
    'pending' => __('ui.purchases.statuses.pending'),
    'approved' => __('ui.purchases.statuses.approved'),
    'rejected' => __('ui.purchases.statuses.rejected'),
    'completed' => __('ui.purchases.statuses.completed'),
    'paid' => __('ui.purchases.statuses.paid'),
    'cancelled' => __('ui.purchases.statuses.cancelled'),
    default => ucfirst((string) $purchase->status),
    };

    $approvalDisplayStatus = $isCompleted ? 'completed' : $approvalStatus;

    $approvalStatusLabel = match ($approvalDisplayStatus) {
    'completed' => __('ui.purchases.statuses.completed'),
    'approved' => __('ui.purchases.statuses.approved'),
    'rejected' => __('ui.purchases.statuses.rejected'),
    default => __('ui.purchases.statuses.pending'),
    };
    ?>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <a href="<?php echo e(route('purchases.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-1.5 text-sm font-semibold text-[var(--on-surface-var)] transition hover:border-[var(--primary)] hover:bg-[color-mix(in_srgb,var(--primary)_8%,transparent_92%)] hover:text-[var(--primary)]">← <?php echo e(__('ui.purchases.back')); ?></a>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('purchases.invoice', $purchase)); ?>" target="_blank"><?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button']); ?><?php echo e(__('ui.purchases.buttons.print_invoice')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?></a>
            <?php if($purchase->supplier): ?>
            <a href="<?php echo e(route('suppliers.show', $purchase->supplier)); ?>"><?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'secondary']); ?><?php echo e(__('ui.purchases.buttons.view_supplier')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?></a>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => __('ui.purchases.summary_title'),'class' => 'lg:col-span-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.purchases.summary_title')),'class' => 'lg:col-span-2']); ?>
            <?php if($requestType === 'inventory'): ?>
            <?php if (isset($component)) { $__componentOriginal793d2b22631f88b8a3d00569a12acf88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal793d2b22631f88b8a3d00569a12acf88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.table','data' => ['headers' => [__('ui.purchases.table.ingredient'), __('ui.purchases.table.qty'), __('ui.purchases.table.expiry'), __('ui.purchases.table.unit_cost'), __('ui.purchases.table.line_total')]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([__('ui.purchases.table.ingredient'), __('ui.purchases.table.qty'), __('ui.purchases.table.expiry'), __('ui.purchases.table.unit_cost'), __('ui.purchases.table.line_total')])]); ?>
                <?php $__currentLoopData = $purchase->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="px-4 py-3 text-[var(--on-surface)] dark:text-[var(--on-surface)]"><?php echo e($item->ingredient_name); ?></td>
                    <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]"><?php echo e(number_format((float)$item->quantity, 3)); ?></td>
                    <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]"><?php echo e($item->expiry_date?->format('Y-m-d') ?? '-'); ?></td>
                    <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]"><?php echo e(\App\Support\CurrencyFormatter::format($item->unit_cost)); ?></td>
                    <td class="px-4 py-3 font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]"><?php echo e(\App\Support\CurrencyFormatter::format($item->line_total)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal793d2b22631f88b8a3d00569a12acf88)): ?>
<?php $attributes = $__attributesOriginal793d2b22631f88b8a3d00569a12acf88; ?>
<?php unset($__attributesOriginal793d2b22631f88b8a3d00569a12acf88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal793d2b22631f88b8a3d00569a12acf88)): ?>
<?php $component = $__componentOriginal793d2b22631f88b8a3d00569a12acf88; ?>
<?php unset($__componentOriginal793d2b22631f88b8a3d00569a12acf88); ?>
<?php endif; ?>

            <?php if($purchase->items->isEmpty()): ?>
            <p class="rounded-lg border border-[var(--outline-var)] bg-[var(--surface-low)] px-3 py-2 text-sm text-[var(--on-surface-var)] dark:border-[var(--outline-var)]  dark:text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.no_results')); ?></p>
            <?php endif; ?>

            <div class="mt-4 ml-auto w-full max-w-sm space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.subtotal')); ?></span><span><?php echo e(\App\Support\CurrencyFormatter::format($purchase->subtotal)); ?></span></div>
                <div class="flex justify-between"><span class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.tax')); ?></span><span><?php echo e(\App\Support\CurrencyFormatter::format($purchase->tax_amount)); ?></span></div>
                <div class="flex justify-between"><span class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.discount')); ?></span><span><?php echo e(\App\Support\CurrencyFormatter::format(-$purchase->discount_amount)); ?></span></div>
                <div class="flex justify-between border-t border-[var(--outline-var)] pt-2 text-base font-semibold"><span><?php echo e(__('ui.purchases.total')); ?></span><span><?php echo e(\App\Support\CurrencyFormatter::format($purchase->total)); ?></span></div>
            </div>
            <?php else: ?>
            <div class="space-y-3 rounded-xl border border-[var(--outline-var)] bg-[var(--surface-low)] p-4 text-sm dark:border-[var(--outline-var)] ">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.expense_title')); ?></span>
                    <span class="font-semibold text-[var(--on-surface)] dark:text-[var(--on-surface)]"><?php echo e($purchase->expense_title ?: '-'); ?></span>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.expense_invoice_reference')); ?></span>
                    <span class="font-semibold text-[var(--on-surface)] dark:text-[var(--on-surface)]"><?php echo e($purchase->expense_invoice_reference ?: '-'); ?></span>
                </div>
                <div class="flex items-center justify-between gap-3 border-t border-[var(--outline-var)] pt-2 dark:border-[var(--outline-var)]">
                    <span class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.expense_amount')); ?></span>
                    <span class="text-base font-bold text-[var(--on-surface)] dark:text-[var(--on-surface)]"><?php echo e(\App\Support\CurrencyFormatter::format($purchase->expense_amount ?? $purchase->total)); ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if($canApprovePurchases && $isPendingApproval): ?>
            <div class="mt-6 border-t border-[var(--outline-var)] pt-4 dark:border-[var(--outline-var)]">
                <div class="grid gap-3 md:grid-cols-2">
                    <form method="POST" action="<?php echo e(route('purchases.approve', $purchase)); ?>" class="rounded-xl border border-[color-mix(in_srgb,var(--success)_30%,transparent_70%)] bg-[var(--primary-container)]/70 p-3 dark:border-[color-mix(in_srgb,var(--success)_30%,transparent_70%)] dark:bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)]">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <label class="mb-1 block text-xs font-semibold text-[var(--success)] "><?php echo e(__('ui.purchases.approval.approve_title')); ?></label>
                        <textarea name="approval_comment" rows="3" placeholder="<?php echo e(__('ui.purchases.approval.approve_hint')); ?>" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]"><?php echo e(old('approval_comment')); ?></textarea>
                        <div class="mt-2 flex justify-end">
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','class' => 'rounded-lg px-3 py-1.5 text-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','class' => 'rounded-lg px-3 py-1.5 text-xs']); ?><?php echo e(__('ui.purchases.buttons.approve')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        </div>
                    </form>

                    <form method="POST" action="<?php echo e(route('purchases.reject', $purchase)); ?>" class="rounded-xl border border-[var(--error-container)] bg-[var(--error-container)] p-3  ">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <label class="mb-1 block text-xs font-semibold text-[var(--error)] dark:text-[var(--error)]"><?php echo e(__('ui.purchases.approval.reject_title')); ?></label>
                        <textarea name="approval_comment" rows="3" required placeholder="<?php echo e(__('ui.purchases.approval.reject_hint')); ?>" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]"><?php echo e(old('approval_comment')); ?></textarea>
                        <?php $__errorArgs = ['approval_comment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-[var(--error)]"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="mt-2 flex justify-end">
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'danger','class' => 'rounded-lg px-3 py-1.5 text-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'danger','class' => 'rounded-lg px-3 py-1.5 text-xs']); ?><?php echo e(__('ui.purchases.buttons.reject')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <?php if($canCompleteRequest): ?>
            <div class="mt-4 border-t border-[var(--outline-var)] pt-4 dark:border-[var(--outline-var)]">
                <form method="POST" action="<?php echo e(route('purchases.complete', $purchase)); ?>" enctype="multipart/form-data" class="rounded-xl border border-[var(--primary-container)] bg-[var(--primary-container)] p-3  ">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>

                    <label class="mb-1 block text-xs font-semibold text-[var(--primary)] "><?php echo e(__('ui.purchases.completion.title')); ?></label>
                    <p class="mb-2 text-xs text-[var(--primary)] "><?php echo e(__('ui.purchases.completion.subtitle')); ?></p>

                    <div class="grid gap-2 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.fields.invoice_number')); ?></label>
                            <input name="invoice_number" value="<?php echo e(old('invoice_number', $purchase->invoice_number)); ?>" required class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
                            <?php $__errorArgs = ['invoice_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-[var(--error)]"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.fields.invoice_file')); ?></label>
                            <input type="file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png,.webp" required class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-[var(--surface-low)] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-[var(--on-surface)] hover:file:bg-[var(--surface-container)] dark:border-[var(--outline-var)] dark:bg-[var(--background)] dark:file:bg-slate-800 dark:file:text-[var(--on-surface)] dark:hover:file:bg-slate-700">
                            <p class="mt-1 text-xs text-[var(--on-surface-var)] dark:text-[var(--outline)]"><?php echo e(__('ui.purchases.fields.invoice_file_hint')); ?></p>
                            <?php $__errorArgs = ['invoice_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-[var(--error)]"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="mt-2 flex justify-end">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','class' => 'rounded-lg px-3 py-1.5 text-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','class' => 'rounded-lg px-3 py-1.5 text-xs']); ?><?php echo e(__('ui.purchases.buttons.confirm_purchase_done')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    </div>
                </form>
            </div>
            <?php elseif($isApprovedRequest && ! $isCompleted): ?>
            <div class="mt-4 border-t border-[var(--outline-var)] pt-4 dark:border-[var(--outline-var)]">
                <div class="rounded-xl border border-[var(--warning-container)] bg-[var(--warning-container)] p-3 text-sm text-[var(--warning)]   dark:text-[var(--warning)]">
                    <?php echo e(__('ui.purchases.completion.waiting_request_owner')); ?>

                </div>
            </div>
            <?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => __('ui.purchases.details_title')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.purchases.details_title'))]); ?>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.purchase_number_label')); ?></dt>
                    <dd class="font-medium"><?php echo e($purchase->purchase_number); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.supplier')); ?></dt>
                    <dd class="font-medium"><?php echo e($purchase->supplier?->name ?? '-'); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.fields.request_type')); ?></dt>
                    <dd class="font-medium">
                        <?php echo e($requestType === 'general_expense' ? __('ui.purchases.request_types.general_expense') : __('ui.purchases.request_types.inventory')); ?>

                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.date')); ?></dt>
                    <dd class="font-medium"><?php echo e($purchase->purchase_date?->format('Y-m-d')); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.processed_by')); ?></dt>
                    <dd class="font-medium"><?php echo e($purchase->user?->name ?? __('ui.purchases.system')); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.status')); ?></dt>
                    <dd class="font-medium"><?php echo e($purchaseStatusLabel); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.approval.status')); ?></dt>
                    <dd class="font-medium">
                        <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([ 'rounded-full px-2 py-1 text-xs font-semibold' , 'border border-[color-mix(in_srgb,var(--success)_35%,transparent_65%)] bg-[var(--primary-container)] text-[var(--success)] dark:border-[color-mix(in_srgb,var(--success)_30%,transparent_70%)] dark:bg-[color-mix(in_srgb,var(--success)_20%,transparent_80%)] dark:text-[var(--success)]'=> $approvalDisplayStatus === 'completed',
                            'border border-[var(--primary-container)] bg-[var(--primary-container)] text-[var(--primary)]' => $approvalDisplayStatus === 'approved',
                            'border border-[var(--error-container)] bg-[var(--error-container)] text-[var(--error)]  dark:bg-[var(--error-container)] ' => $approvalDisplayStatus === 'rejected',
                            'border border-[var(--warning-container)] bg-[var(--warning-container)] text-[var(--warning)]   ' => $approvalDisplayStatus === 'pending',
                            ]); ?>">
                            <?php echo e($approvalStatusLabel); ?>

                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.approval.by')); ?></dt>
                    <dd class="font-medium"><?php echo e($purchase->approvalUser?->name ?? '-'); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.approval.at')); ?></dt>
                    <dd class="font-medium"><?php echo e($purchase->approval_at?->format('Y-m-d H:i') ?? '-'); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.completed_by')); ?></dt>
                    <dd class="font-medium"><?php echo e($purchase->completedByUser?->name ?? '-'); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.completed_at')); ?></dt>
                    <dd class="font-medium"><?php echo e($purchase->completed_at?->format('Y-m-d H:i') ?? '-'); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.invoice_number')); ?></dt>
                    <dd class="font-medium"><?php echo e($purchase->invoice_number ?: '-'); ?></dd>
                </div>
                <?php if($requestType === 'inventory'): ?>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.stock_applied_at')); ?></dt>
                    <dd class="font-medium"><?php echo e($purchase->inventory_applied_at?->format('Y-m-d H:i') ?? '-'); ?></dd>
                </div>
                <?php endif; ?>
                <?php if($requestType === 'general_expense'): ?>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.expense_title')); ?></dt>
                    <dd class="font-medium"><?php echo e($purchase->expense_title ?: '-'); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.expense_invoice_reference')); ?></dt>
                    <dd class="font-medium"><?php echo e($purchase->expense_invoice_reference ?: '-'); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.expense_amount')); ?></dt>
                    <dd class="font-medium"><?php echo e(\App\Support\CurrencyFormatter::format($purchase->expense_amount ?? $purchase->total)); ?></dd>
                </div>
                <?php endif; ?>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.payment')); ?></dt>
                    <dd class="font-medium">
                        <?php if(($purchase->payment_method ?? 'cash') === 'credit'): ?>
                        <?php echo e(__('ui.purchases.payment_credit')); ?>

                        <?php else: ?>
                        <?php echo e(__('ui.purchases.payment_cash')); ?>

                        <?php endif; ?>
                    </dd>
                </div>
            </dl>

            <?php if($purchase->approval_comment): ?>
            <div class="mt-4 rounded-lg bg-[var(--surface-low)] px-3 py-2 text-sm text-[var(--on-surface)]  dark:text-[var(--on-surface-var)]">
                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.approval.comment')); ?></p>
                <p class="mt-1"><?php echo e($purchase->approval_comment); ?></p>
            </div>
            <?php endif; ?>

            <?php if($purchase->notes): ?>
            <div class="mt-4 rounded-lg bg-[var(--surface-low)] px-3 py-2 text-sm text-[var(--on-surface)]  dark:text-[var(--on-surface-var)]"><?php echo e($purchase->notes); ?></div>
            <?php endif; ?>

            <?php if(! empty($supportsInvoiceFile)): ?>
            <div class="mt-4 rounded-xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] p-3 dark:border-[var(--outline-var)] ">
                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.invoice_file_label')); ?></p>

                <?php if(! empty($purchase->invoice_file_path)): ?>
                <div class="mt-2 flex flex-wrap gap-2">
                    <a href="<?php echo e(route('purchases.invoice-file.view', $purchase)); ?>" target="_blank" class="inline-flex items-center justify-center rounded-lg border border-[var(--outline-var)] px-3 py-2 text-xs font-semibold text-[var(--on-surface)] hover:bg-[var(--surface-low)] dark:border-slate-600 dark:text-[var(--on-surface)] "><?php echo e(__('ui.purchases.buttons.view_invoice_file')); ?></a>
                    <a href="<?php echo e(route('purchases.invoice-file.download', $purchase)); ?>" class="inline-flex items-center justify-center rounded-lg border border-[var(--outline-var)] px-3 py-2 text-xs font-semibold text-[var(--on-surface)] hover:bg-[var(--surface-low)] dark:border-slate-600 dark:text-[var(--on-surface)] "><?php echo e(__('ui.purchases.buttons.download_invoice_file')); ?></a>
                </div>
                <?php else: ?>
                <p class="mt-1 text-sm text-[var(--on-surface-var)] dark:text-[var(--outline)]"><?php echo e(__('ui.purchases.invoice_file_missing')); ?></p>
                <?php endif; ?>

                <?php if($isCompleted): ?>
                <form method="POST" action="<?php echo e(route('purchases.invoice-file.update', $purchase)); ?>" enctype="multipart/form-data" class="mt-3 border-t border-[var(--outline-var)] pt-3 dark:border-[var(--outline-var)]">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>

                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-[var(--on-surface-var)]"><?php echo e(__('ui.purchases.fields.invoice_file_replace')); ?></label>
                    <input type="file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png,.webp" required class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-[var(--surface-low)] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-[var(--on-surface)] hover:file:bg-[var(--surface-container)] dark:border-[var(--outline-var)] dark:bg-[var(--background)] dark:file:bg-slate-800 dark:file:text-[var(--on-surface)] dark:hover:file:bg-slate-700">
                    <p class="mt-1 text-xs text-[var(--on-surface-var)] dark:text-[var(--outline)]"><?php echo e(__('ui.purchases.fields.invoice_file_hint')); ?></p>
                    <?php $__errorArgs = ['invoice_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-[var(--error)]"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <div class="mt-2">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'secondary']); ?><?php echo e(__('ui.purchases.buttons.upload_invoice_file')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    </div>
                </form>
                <?php else: ?>
                <p class="mt-3 border-t border-[var(--outline-var)] pt-3 text-xs text-[var(--on-surface-var)] dark:border-[var(--outline-var)] dark:text-[var(--outline)]"><?php echo e(__('messages.errors.purchase_invoice_replace_only_after_completion')); ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
    </div>

    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'mt-6','title' => __('ui.purchases.audit.title'),'subtitle' => __('ui.purchases.audit.subtitle')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-6','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.purchases.audit.title')),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.purchases.audit.subtitle'))]); ?>
        <?php if (isset($component)) { $__componentOriginal793d2b22631f88b8a3d00569a12acf88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal793d2b22631f88b8a3d00569a12acf88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.table','data' => ['headers' => [__('ui.purchases.audit.table.action'), __('ui.purchases.audit.table.before_after_status'), __('ui.purchases.audit.table.before_after_approval'), __('ui.purchases.audit.table.before_after_comment'), __('ui.purchases.audit.table.by'), __('ui.purchases.audit.table.when')]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([__('ui.purchases.audit.table.action'), __('ui.purchases.audit.table.before_after_status'), __('ui.purchases.audit.table.before_after_approval'), __('ui.purchases.audit.table.before_after_comment'), __('ui.purchases.audit.table.by'), __('ui.purchases.audit.table.when')])]); ?>
            <?php $__empty_1 = true; $__currentLoopData = $purchase->approvalLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
            $actionLabel = match (strtolower((string) $log->action)) {
            'approve' => __('ui.purchases.buttons.approve'),
            'reject' => __('ui.purchases.buttons.reject'),
            default => ucfirst((string) $log->action),
            };

            $previousStatus = $log->previous_status ?: '-';
            $newStatus = $log->new_status ?: '-';
            $previousApprovalStatus = $log->previous_approval_status ?: '-';
            $newApprovalStatus = $log->new_approval_status ?: '-';
            $previousComment = trim((string) ($log->previous_approval_comment ?? ''));
            $newComment = trim((string) ($log->new_approval_comment ?? ''));
            ?>
            <tr>
                <td class="px-4 py-3 text-[var(--on-surface)] dark:text-[var(--on-surface)]"><?php echo e($actionLabel); ?></td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">
                    <span class="font-semibold"><?php echo e($previousStatus); ?></span>
                    <span class="mx-1">→</span>
                    <span class="font-semibold"><?php echo e($newStatus); ?></span>
                </td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">
                    <span class="font-semibold"><?php echo e($previousApprovalStatus); ?></span>
                    <span class="mx-1">→</span>
                    <span class="font-semibold"><?php echo e($newApprovalStatus); ?></span>
                </td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">
                    <p><span class="font-semibold"><?php echo e(__('ui.purchases.audit.before')); ?>:</span> <?php echo e($previousComment !== '' ? $previousComment : '-'); ?></p>
                    <p class="mt-1"><span class="font-semibold"><?php echo e(__('ui.purchases.audit.after')); ?>:</span> <?php echo e($newComment !== '' ? $newComment : '-'); ?></p>
                </td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]"><?php echo e($log->actor?->name ?? '-'); ?></td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--outline)]"><?php echo e($log->acted_at?->format('Y-m-d H:i') ?? '-'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-sm text-[var(--on-surface-var)] dark:text-[var(--outline)]"><?php echo e(__('ui.purchases.audit.empty')); ?></td>
            </tr>
            <?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal793d2b22631f88b8a3d00569a12acf88)): ?>
<?php $attributes = $__attributesOriginal793d2b22631f88b8a3d00569a12acf88; ?>
<?php unset($__attributesOriginal793d2b22631f88b8a3d00569a12acf88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal793d2b22631f88b8a3d00569a12acf88)): ?>
<?php $component = $__componentOriginal793d2b22631f88b8a3d00569a12acf88; ?>
<?php unset($__componentOriginal793d2b22631f88b8a3d00569a12acf88); ?>
<?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?><?php /**PATH /var/www/dots-main/resources/views/purchases/show.blade.php ENDPATH**/ ?>