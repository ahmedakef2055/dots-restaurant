<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title' => __('ui.pos.title')]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['title' => __('ui.pos.title')]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title); ?> | <?php echo e(__('ui.layout.app_name')); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('images/logo.png')); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('apple-touch-icon.png')); ?>">
    <link rel="manifest" href="<?php echo e(asset('site.webmanifest')); ?>">
    <meta name="theme-color" content="#5E7D67" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#1D2420" media="(prefers-color-scheme: dark)">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script src="<?php echo e(asset('js/qz-tray.js')); ?>"></script>
</head>

<body
    class="h-full overflow-hidden is-loading"
    onload="document.body.classList.remove('is-loading')"
    data-delete-modal-title="<?php echo e(__('ui.common.delete_modal_title')); ?>"
    data-delete-modal-message="<?php echo e(__('ui.common.delete_modal_message')); ?>"
    data-delete-modal-confirm="<?php echo e(__('ui.common.delete')); ?>"
    data-delete-modal-cancel="<?php echo e(__('ui.common.cancel')); ?>">

    <div class="pos-shell">

        
        <?php if (isset($component)) { $__componentOriginala12ee38770dfc9ba212665cdb25e4cfd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala12ee38770dfc9ba212665cdb25e4cfd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala12ee38770dfc9ba212665cdb25e4cfd)): ?>
<?php $attributes = $__attributesOriginala12ee38770dfc9ba212665cdb25e4cfd; ?>
<?php unset($__attributesOriginala12ee38770dfc9ba212665cdb25e4cfd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala12ee38770dfc9ba212665cdb25e4cfd)): ?>
<?php $component = $__componentOriginala12ee38770dfc9ba212665cdb25e4cfd; ?>
<?php unset($__componentOriginala12ee38770dfc9ba212665cdb25e4cfd); ?>
<?php endif; ?>

        
        <div class="overlay" data-sidebar-close></div>

        
        <div class="pos-main">
            
            <header class="pos-topbar" style="direction:ltr">
                <div class="flex items-center gap-3 flex-1">
                    <button data-sidebar-toggle type="button" class="top-icon-btn lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

                <span class="text-base font-bold tracking-tight hidden sm:block" style="color:var(--on-surface)">
                    <?php echo e(__('ui.layout.app_name')); ?>

                </span>

                <div class="flex items-center gap-2 flex-1 justify-end">
                    <button type="button" class="top-lang-btn" title="<?php echo e(__('ui.layout.switch_language')); ?>" data-language-toggle>
                        <?php echo e(app()->getLocale() === 'ar' ? 'EN' : 'AR'); ?>

                    </button>
                    <button type="button" class="top-icon-btn" title="<?php echo e(__('ui.layout.theme')); ?>" data-theme-toggle>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M480-120q-150 0-255-105T120-480q0-150 105-255t255-105q14 0 27.5 1t26.5 3q-41 29-65.5 75.5T444-660q0 90 63 153t153 63q55 0 101-24.5t75-65.5q2 13 3 26.5t1 27.5q0 150-105 255T480-120Zm0-80q88 0 158-48.5T740-375q-20 5-40 8t-40 3q-123 0-209.5-86.5T364-660q0-20 3-40t8-40q-78 32-126.5 102T200-480q0 116 82 198t198 82Zm-10-270Z"/></svg>
                    </button>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline-flex">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="top-icon-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>
                        </button>
                    </form>
                    <div class="top-avatar"><?php echo e(mb_strtoupper(mb_substr(auth()->user()?->name ?? 'U', 0, 2))); ?></div>
                </div>
            </header>

            
            <?php echo e($slot); ?>

        </div>
    </div>
</body>
</html>
<?php /**PATH /var/www/dots/resources/views/components/layouts/pos-shell.blade.php ENDPATH**/ ?>