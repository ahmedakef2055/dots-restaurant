<?php
$successMessage = session('success');
$errorMessage = session('error');
?>

<?php if($successMessage || $errorMessage): ?>
<div class="<?php echo \Illuminate\Support\Arr::toCssClasses([ 'pointer-events-none fixed top-4 z-[70] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-2' , 'left-4 sm:left-6'=> app()->getLocale() === 'ar',
    'right-4 sm:right-6' => app()->getLocale() !== 'ar',
    ]); ?>">
    <?php if($successMessage): ?>
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => { show = false }, 3500)"
        x-show="show"
        x-cloak
        x-transition:enter="transform ease-out duration-250"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transform ease-in duration-250"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="pointer-events-auto flex items-start gap-3 rounded-xl border px-4 py-3 text-sm shadow-lg backdrop-blur-sm"
        style="border-color:color-mix(in srgb,var(--accent-gold) 40%,transparent 60%);background-color:var(--surface-lowest);"
        role="status">
        <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[var(--success-container)] text-[var(--success)]  dark:text-[var(--success)]">✓</span>
        <div class="min-w-0">
            <p class="font-semibold" style="color:var(--primary)"><?php echo e(__('ui.toast.success')); ?></p>
            <p style="color:var(--on-surface-var)"><?php echo e($successMessage); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if($errorMessage): ?>
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => { show = false }, 4200)"
        x-show="show"
        x-cloak
        x-transition:enter="transform ease-out duration-250"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transform ease-in duration-250"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="pointer-events-auto flex items-start gap-3 rounded-xl border px-4 py-3 text-sm shadow-lg backdrop-blur-sm"
        style="border-color:color-mix(in srgb,var(--error) 40%,transparent 60%);background-color:var(--surface-lowest);"
        role="alert">
        <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full" style="background-color:var(--error-container);color:var(--error)">!</span>
        <div class="min-w-0">
            <p class="font-semibold" style="color:var(--error)"><?php echo e(__('ui.toast.error')); ?></p>
            <p style="color:var(--on-surface-var)"><?php echo e($errorMessage); ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?><?php /**PATH /var/www/dots/resources/views/components/ui/flash-toast.blade.php ENDPATH**/ ?>