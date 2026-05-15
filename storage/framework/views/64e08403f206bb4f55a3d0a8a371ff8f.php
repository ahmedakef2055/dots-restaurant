<?php if($paginator->hasPages()): ?>
<nav role="navigation" aria-label="<?php echo e(__('Pagination Navigation')); ?>" class="flex items-center justify-between">

    
    <div class="flex justify-between flex-1 sm:hidden">
        <?php if($paginator->onFirstPage()): ?>
            <span class="pagi-btn pagi-disabled"><?php echo __('pagination.previous'); ?></span>
        <?php else: ?>
            <a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" class="pagi-btn"><?php echo __('pagination.previous'); ?></a>
        <?php endif; ?>
        <?php if($paginator->hasMorePages()): ?>
            <a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" class="pagi-btn"><?php echo __('pagination.next'); ?></a>
        <?php else: ?>
            <span class="pagi-btn pagi-disabled"><?php echo __('pagination.next'); ?></span>
        <?php endif; ?>
    </div>

    
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">

        <div>
            <p class="text-sm" style="color:var(--on-surface-var)">
                <?php echo __('Showing'); ?>

                <?php if($paginator->firstItem()): ?>
                    <span class="font-semibold" style="color:var(--on-surface)"><?php echo e($paginator->firstItem()); ?></span>
                    <?php echo __('to'); ?>

                    <span class="font-semibold" style="color:var(--on-surface)"><?php echo e($paginator->lastItem()); ?></span>
                <?php else: ?>
                    <?php echo e($paginator->count()); ?>

                <?php endif; ?>
                <?php echo __('of'); ?>

                <span class="font-semibold" style="color:var(--on-surface)"><?php echo e($paginator->total()); ?></span>
                <?php echo __('results'); ?>

            </p>
        </div>

        <div>
            <span class="pagi-wrap">

                
                <?php if($paginator->onFirstPage()): ?>
                    <span class="pagi-arrow pagi-arrow-disabled" aria-disabled="true" aria-label="<?php echo e(__('pagination.previous')); ?>">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </span>
                <?php else: ?>
                    <a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" class="pagi-arrow" aria-label="<?php echo e(__('pagination.previous')); ?>">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </a>
                <?php endif; ?>

                
                <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(is_string($element)): ?>
                        <span class="pagi-dots"><?php echo e($element); ?></span>
                    <?php endif; ?>
                    <?php if(is_array($element)): ?>
                        <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($page == $paginator->currentPage()): ?>
                                <span class="pagi-page pagi-page-active" aria-current="page"><?php echo e($page); ?></span>
                            <?php else: ?>
                                <a href="<?php echo e($url); ?>" class="pagi-page" aria-label="<?php echo e(__('Go to page :page', ['page' => $page])); ?>"><?php echo e($page); ?></a>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <?php if($paginator->hasMorePages()): ?>
                    <a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" class="pagi-arrow" aria-label="<?php echo e(__('pagination.next')); ?>">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                    </a>
                <?php else: ?>
                    <span class="pagi-arrow pagi-arrow-disabled" aria-disabled="true" aria-label="<?php echo e(__('pagination.next')); ?>">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                    </span>
                <?php endif; ?>

            </span>
        </div>
    </div>
</nav>
<?php endif; ?>
<?php /**PATH /var/www/dots-main/resources/views/vendor/pagination/tailwind.blade.php ENDPATH**/ ?>