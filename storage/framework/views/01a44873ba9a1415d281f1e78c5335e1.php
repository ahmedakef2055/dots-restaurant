<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => $boardTitle]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($boardTitle)]); ?>
    <div
        x-data="displayBoardPage({ stages: <?php echo \Illuminate\Support\Js::from($stageConfigs)->toHtml() ?>, initialBoard: <?php echo \Illuminate\Support\Js::from($initialBoard)->toHtml() ?> }, { fetchUrl: '<?php echo e($fetchEndpoint); ?>', transitionTemplate: '<?php echo e($transitionTemplate); ?>', csrf: '<?php echo e(csrf_token()); ?>', pollingMs: <?php echo e((int) $pollingMs); ?>, transitionSuccessMessage: '<?php echo e(__('messages.success.kds_status_updated')); ?>' })"
        x-init="init()"
        x-on:beforeunload.window="destroy()"
        class="kds-canvas">

        
        <div class="kds-toast-wrap" dir="ltr">
            <div x-cloak x-show="success"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="kds-toast kds-toast-success" role="status">
                <span class="kds-toast-icon" style="background:color-mix(in srgb,var(--success) 15%,transparent 85%);color:var(--success)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="font-variation-settings:'FILL' 1"><path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                </span>
                <span x-text="success" class="text-sm font-medium" style="color:var(--on-surface)"></span>
            </div>
            <div x-cloak x-show="error"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="kds-toast kds-toast-error" role="alert">
                <span class="kds-toast-icon" style="background:color-mix(in srgb,var(--error) 15%,transparent 85%);color:var(--error)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="font-variation-settings:'FILL' 1"><path d="M508.5-291.5Q520-303 520-320t-11.5-28.5Q497-360 480-360t-28.5 11.5Q440-337 440-320t11.5 28.5Q463-280 480-280t28.5-11.5ZM440-440h80v-240h-80v240Zm40 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                </span>
                <span x-text="error" class="text-sm font-medium" style="color:var(--on-surface)"></span>
            </div>
        </div>

        
        <div class="kds-page-header">
            <div>
                <h1 class="kds-page-title"><?php echo e($boardTitle); ?></h1>
                <p class="kds-page-subtitle"><?php echo e($boardSubtitle); ?></p>
            </div>

            
            <div class="kds-stats-row">
                <div class="kds-stat-pill">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--primary)"><path d="M538.5-138.5Q480-197 480-280t58.5-141.5Q597-480 680-480t141.5 58.5Q880-363 880-280t-58.5 141.5Q763-80 680-80t-141.5-58.5ZM747-185l28-28-75-75v-112h-40v128l87 87Zm-547 65q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h167q11-35 43-57.5t70-22.5q40 0 71.5 22.5T594-840h166q33 0 56.5 23.5T840-760v250q-18-13-38-22t-42-16v-212h-80v120H280v-120h-80v560h212q7 22 16 42t22 38H200Zm308.5-651.5Q520-783 520-800t-11.5-28.5Q497-840 480-840t-28.5 11.5Q440-817 440-800t11.5 28.5Q463-760 480-760t28.5-11.5Z"/></svg>
                    <div>
                        <span class="kds-stat-val" x-text="(board['pending']||[]).length"></span>
                        <span class="kds-stat-label"><?php echo e(__('ui.kds.stages.pending')); ?></span>
                    </div>
                </div>
                <div class="kds-stat-pill">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--accent-gold)"><path d="M280-80v-366q-51-14-85.5-56T160-600v-280h80v280h40v-280h80v280h40v-280h80v280q0 56-34.5 98T360-446v366h-80Zm400 0v-320H560v-280q0-83 58.5-141.5T760-880v800h-80Z"/></svg>
                    <div>
                        <span class="kds-stat-val" x-text="(board['preparing']||[]).length"></span>
                        <span class="kds-stat-label"><?php echo e(__('ui.kds.stages.preparing')); ?></span>
                    </div>
                </div>
                <div class="kds-stat-pill">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--success)"><path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                    <div>
                        <span class="kds-stat-val" x-text="(board['ready']||[]).length"></span>
                        <span class="kds-stat-label"><?php echo e(__('ui.kds.stages.ready')); ?></span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="kds-board">
            <template x-for="stage in stages" :key="stage.key">
                <section class="kds-column" :class="`kds-col-${stage.key}`">

                    
                    <div class="kds-col-header">
                        <div class="kds-col-header-left">
                            <div class="kds-col-icon" :class="`kds-col-icon-${stage.key}`">
                                
                                <template x-if="stage.key === 'pending'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="font-variation-settings:'FILL' 1"><path d="M538.5-138.5Q480-197 480-280t58.5-141.5Q597-480 680-480t141.5 58.5Q880-363 880-280t-58.5 141.5Q763-80 680-80t-141.5-58.5ZM747-185l28-28-75-75v-112h-40v128l87 87Zm-547 65q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h167q11-35 43-57.5t70-22.5q40 0 71.5 22.5T594-840h166q33 0 56.5 23.5T840-760v250q-18-13-38-22t-42-16v-212h-80v120H280v-120h-80v560h212q7 22 16 42t22 38H200Zm308.5-651.5Q520-783 520-800t-11.5-28.5Q497-840 480-840t-28.5 11.5Q440-817 440-800t11.5 28.5Q463-760 480-760t28.5-11.5Z"/></svg>
                                </template>
                                
                                <template x-if="stage.key === 'preparing'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="font-variation-settings:'FILL' 1"><path d="M280-80v-366q-51-14-85.5-56T160-600v-280h80v280h40v-280h80v280h40v-280h80v280q0 56-34.5 98T360-446v366h-80Zm400 0v-320H560v-280q0-83 58.5-141.5T760-880v800h-80Z"/></svg>
                                </template>
                                
                                <template x-if="stage.key === 'ready'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="font-variation-settings:'FILL' 1"><path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                                </template>
                            </div>
                            <h2 class="kds-col-title" x-text="stage.title"></h2>
                        </div>
                        <span class="kds-count-badge" x-text="(board[stage.key]||[]).length"></span>
                    </div>

                    
                    <div class="kds-tickets-wrap">
                        
                        <template x-if="!(board[stage.key]||[]).length">
                            <div class="kds-empty">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-3xl mb-1" style="color:var(--outline)"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-120H640q-30 38-71.5 59T480-240q-47 0-88.5-21T320-320H200v120Zm349-142q31-22 43-58h168v-360H200v360h168q12 36 43 58t69 22q38 0 69-22ZM200-200h560-560Z"/></svg>
                                <p><?php echo e(__('ui.kds.empty_stage')); ?></p>
                            </div>
                        </template>

                        <template x-for="ticket in (board[stage.key]||[])" :key="ticket.id">
                            <article
                                class="kds-ticket"
                                :class="getTicketClass(ticket)"
                                x-data="{ elapsed: 0, _timer: null }"
                                x-init="
                                    elapsed = elapsedSeconds(ticket.created_at);
                                    _timer = setInterval(() => { elapsed = elapsedSeconds(ticket.created_at); }, 1000);
                                    $watch('ticket.created_at', v => { elapsed = elapsedSeconds(v); });
                                "
                                x-destroy="clearInterval(_timer)">

                                
                                <div class="kds-ticket-header">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="kds-order-num" x-text="ticket.order_number"></span>
                                        <span class="kds-source-label" x-text="ticket.source_label"></span>
                                    </div>
                                    <div class="kds-timer-wrap" :class="getTimerClass(elapsed)">
                                        <span class="kds-timer-clock" x-text="formatElapsed(elapsed)"></span>
                                        <span class="kds-timer-badge" x-show="elapsed >= 600" x-cloak>
                                            <template x-if="elapsed >= 1200">
                                                <span><?php echo e(__('ui.kds.timer.late')); ?></span>
                                            </template>
                                            <template x-if="elapsed >= 600 && elapsed < 1200">
                                                <span><?php echo e(__('ui.kds.timer.warning')); ?></span>
                                            </template>
                                        </span>
                                    </div>
                                </div>

                                
                                <template x-if="ticket.order_notes">
                                    <div class="kds-notes-banner">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[13px]" style="font-variation-settings:'FILL' 1"><path d="M200-200h360v-200h200v-360H200v560Zm0 80q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v400L600-120H200Zm80-280v-80h200v80H280Zm0-160v-80h400v80H280Zm-80 360v-560 560Z"/></svg>
                                        <span x-text="ticket.order_notes"></span>
                                    </div>
                                </template>

                                
                                <ul class="kds-items-list">
                                    <template x-for="item in ticket.items" :key="item.id">
                                        <li class="kds-item-row">
                                            <span class="kds-item-qty" x-text="item.quantity + 'x'"></span>
                                            <div class="kds-item-detail">
                                                <span class="kds-item-name" x-text="item.product_name"></span>
                                                <template x-if="item.notes">
                                                    <span class="kds-item-note" x-text="'- ' + item.notes"></span>
                                                </template>
                                            </div>
                                        </li>
                                    </template>
                                </ul>

                                
                                <div class="kds-ticket-footer">
                                    
                                    <template x-if="stage.key !== 'pending'">
                                        <button type="button"
                                            class="kds-btn-back"
                                            @click="transition(ticket, 'back')"
                                            :disabled="loading || isTransitionLoading(ticket, 'back')"
                                            :class="(loading || isTransitionLoading(ticket, 'back')) ? 'opacity-50 cursor-not-allowed' : ''">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" ><path d="M280-200v-80h284q63 0 109.5-40T720-420q0-60-46.5-100T564-560H312l104 104-56 56-200-200 200-200 56 56-104 104h252q97 0 166.5 63T800-420q0 94-69.5 157T564-200H280Z"/></svg>
                                        </button>
                                    </template>

                                    
                                    <template x-if="stage.key === 'pending'">
                                        <button type="button"
                                            class="kds-btn-primary kds-btn-prep flex-1"
                                            @click="transition(ticket, 'start')"
                                            :disabled="loading || isTransitionLoading(ticket, 'start')"
                                            :class="(loading || isTransitionLoading(ticket, 'start')) ? 'opacity-50 cursor-not-allowed' : ''">
                                            <template x-if="isTransitionLoading(ticket, 'start')">
                                                <span class="kds-spinner"></span>
                                            </template>
                                            <template x-if="!isTransitionLoading(ticket, 'start')">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" ><path d="M320-200v-560l440 280-440 280Zm80-280Zm0 134 210-134-210-134v268Z"/></svg>
                                            </template>
                                            <?php echo e(__('ui.kds.actions.start')); ?>

                                        </button>
                                    </template>

                                    <template x-if="stage.key === 'preparing'">
                                        <button type="button"
                                            class="kds-btn-primary kds-btn-ready flex-1"
                                            :class="[(loading || isTransitionLoading(ticket, 'done')) ? 'opacity-50 cursor-not-allowed' : '', elapsed >= 1200 ? 'kds-btn-force' : '']"
                                            @click="transition(ticket, 'done')"
                                            :disabled="loading || isTransitionLoading(ticket, 'done')">
                                            <template x-if="isTransitionLoading(ticket, 'done')">
                                                <span class="kds-spinner"></span>
                                            </template>
                                            <template x-if="!isTransitionLoading(ticket, 'done')">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" ><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>
                                            </template>
                                            <span x-text="elapsed >= 1200 ? '<?php echo e(__('ui.kds.actions.force_ready')); ?>' : '<?php echo e(__('ui.kds.actions.done')); ?>'"></span>
                                        </button>
                                    </template>

                                    <template x-if="stage.key === 'ready'">
                                        <button type="button"
                                            class="kds-btn-primary kds-btn-handoff flex-1"
                                            @click="transition(ticket, 'handoff')"
                                            :disabled="loading || isTransitionLoading(ticket, 'handoff')"
                                            :class="(loading || isTransitionLoading(ticket, 'handoff')) ? 'opacity-50 cursor-not-allowed' : ''">
                                            <template x-if="isTransitionLoading(ticket, 'handoff')">
                                                <span class="kds-spinner"></span>
                                            </template>
                                            <template x-if="!isTransitionLoading(ticket, 'handoff')">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" ><path d="M240-80q-33 0-56.5-23.5T160-160v-480q0-33 23.5-56.5T240-720h80q0-66 47-113t113-47q66 0 113 47t47 113h80q33 0 56.5 23.5T800-640v480q0 33-23.5 56.5T720-80H240Zm0-80h480v-480h-80v80q0 17-11.5 28.5T600-520q-17 0-28.5-11.5T560-560v-80H400v80q0 17-11.5 28.5T360-520q-17 0-28.5-11.5T320-560v-80h-80v480Zm160-560h160q0-33-23.5-56.5T480-800q-33 0-56.5 23.5T400-720ZM240-160v-480 480Z"/></svg>
                                            </template>
                                            <?php echo e(__('ui.kds.actions.handed_off')); ?>

                                        </button>
                                    </template>
                                </div>
                            </article>
                        </template>
                    </div>
                </section>
            </template>
        </div>

    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?><?php /**PATH /var/www/dots-main/resources/views/kds/index.blade.php ENDPATH**/ ?>