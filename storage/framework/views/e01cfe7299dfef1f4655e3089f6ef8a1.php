<aside id="app-sidebar" class="app-sidebar">
    <div class="app-logo-bar" style="display:flex; align-items:center; justify-content:center;">
        <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Dots"
             class="h-12 w-12 object-contain rounded-xl shrink-0">
    </div>

    <?php
    $authUser = auth()->user();
    $permissionMap = [
        'dashboard.view' => $authUser?->hasPermission('dashboard.view') ?? false,
        'pos.view'       => $authUser?->hasPermission('pos.view')       ?? false,
        'waiter.view'    => $authUser?->hasPermission('waiter.view')    ?? false,
        'kds.view'       => $authUser?->hasPermission('kds.view')       ?? false,
        'bar.view'       => $authUser?->hasPermission('bar.view')       ?? false,
        'orders.view'    => $authUser?->hasPermission('orders.view')    ?? false,
        'orders.create'  => $authUser?->hasPermission('orders.create')  ?? false,
        'orders.update'  => $authUser?->hasPermission('orders.update')  ?? false,
        'inventory.view' => $authUser?->hasPermission('inventory.view') ?? false,
        'recipes.view'   => $authUser?->hasPermission('recipes.view')   ?? false,
        'suppliers.view' => $authUser?->hasPermission('suppliers.view') ?? false,
        'purchases.view' => $authUser?->hasPermission('purchases.view') ?? false,
        'tables.view'    => $authUser?->hasPermission('tables.view')    ?? false,
        'categories.view'=> $authUser?->hasPermission('categories.view')  ?? false,
        'products.view'  => $authUser?->hasPermission('products.view')  ?? false,
        'customers.view' => $authUser?->hasPermission('customers.view') ?? false,
        'users.view'     => $authUser?->hasPermission('users.view')     ?? false,
        'employees.view' => $authUser?->hasPermission('employees.view') ?? false,
        'attendance.view'=> $authUser?->hasPermission('attendance.view')  ?? false,
        'salaries.view'  => $authUser?->hasPermission('salaries.view')  ?? false,
        'offers.view'    => $authUser?->hasPermission('offers.view')    ?? false,
        'coupons.view'   => $authUser?->hasPermission('coupons.view')   ?? false,
        'reports.view'   => $authUser?->hasPermission('reports.view')   ?? false,
    ];

    $canDashboard = $permissionMap['dashboard.view'];
    $canPos       = $permissionMap['pos.view'];
    $canWaiter    = $permissionMap['waiter.view'];
    $canKds       = $permissionMap['kds.view'];
    $canBar       = $permissionMap['bar.view'];
    $canOrdersView   = $permissionMap['orders.view'];
    $canOrdersCreate = $permissionMap['orders.create'];
    $canOrdersUpdate = $permissionMap['orders.update'];
    $canOrdersAny = $canOrdersView || $canOrdersCreate || $canOrdersUpdate;

    $canInventory = $permissionMap['inventory.view'];
    $canRecipes = $permissionMap['recipes.view'];
    $canSuppliers = $permissionMap['suppliers.view'];
    $canPurchases = $permissionMap['purchases.view'];
    $canTables = $permissionMap['tables.view'];
    $canCategories = $permissionMap['categories.view'];
    $canProducts = $permissionMap['products.view'];
    $canCustomers = $permissionMap['customers.view'];

    $canUsers = $permissionMap['users.view'];
    $canEmployeesView = $permissionMap['employees.view'];
    $canAttendanceView = $permissionMap['attendance.view'];
    $canSalariesView = $permissionMap['salaries.view'];
    $canEmployees = $canEmployeesView || $canAttendanceView || $canSalariesView;

    $canOffersView = $permissionMap['offers.view'];
    $canCouponsView = $permissionMap['coupons.view'];
    $canMarketing = $canOffersView || $canCouponsView;
    $canReports = $permissionMap['reports.view'];

    $employeesNavRoute = $canEmployeesView
        ? route('employees.index')
        : ($canAttendanceView
        ? route('attendance.index')
        : ($canSalariesView ? route('salaries.index') : null));

    $marketingNavRoute = $canOffersView
        ? route('offers.index')
        : ($canCouponsView ? route('coupons.index') : null);

    $showOperationsGroup = $canDashboard || $canPos || $canWaiter || $canKds || $canBar || $canOrdersAny;
    $showManagementGroup = $canInventory || $canRecipes || $canSuppliers || $canPurchases || $canTables || $canCategories || $canProducts || $canCustomers;
    $showAdminGroup = $canEmployees || $canUsers || $canMarketing || $canReports;
    ?>

    <nav class="flex-1 space-y-5 px-3 py-2 text-sm overflow-y-auto">
        <?php if($showOperationsGroup): ?>
        <div>
            <p class="sidebar-group-label"><?php echo e(__('ui.navigation.operations')); ?></p>
            <div class="sidebar-menu">
                <?php if($canDashboard): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('dashboard')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.5h6.75V21H3v-7.5Zm11.25-10.5H21V21h-6.75V3ZM3 3h6.75v7.5H3V3Zm11.25 10.5H21v7.5h-6.75v-7.5Z" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.dashboard')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canPos): ?>
                <a href="<?php echo e(route('pos.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('pos.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5.25A2.25 2.25 0 0 1 5.25 3h12.5A2.25 2.25 0 0 1 20 5.25v13.5A2.25 2.25 0 0 1 17.75 21H5.25A2.25 2.25 0 0 1 3 18.75V5.25ZM7.5 7.5h9m-9 3.75h9m-9 3.75h4.5" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.pos')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canWaiter): ?>
                <a href="<?php echo e(route('waiter.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('waiter.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.waiter')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canKds): ?>
                <a href="<?php echo e(route('kds.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('kds.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 6v9.75m7.5-9.75v9.75M6 3.75h12a2.25 2.25 0 0 1 2.25 2.25v9A2.25 2.25 0 0 1 18 17.25H6A2.25 2.25 0 0 1 3.75 15V6A2.25 2.25 0 0 1 6 3.75Zm3 15h6" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.kds')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canBar): ?>
                <a href="<?php echo e(route('bar.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('bar.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 4.5h7.5m-6.75 0v3.75a5.25 5.25 0 1 0 10.5 0V4.5m-7.5 13.5V21m0 0h6m-6 0H9" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.bar')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canOrdersView): ?>
                <a href="<?php echo e(route('orders.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('orders.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3.75h10.5A2.25 2.25 0 0 1 19.5 6v12a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 18V6a2.25 2.25 0 0 1 2.25-2.25Zm3 4.5h4.5m-4.5 3h7.5m-7.5 3h7.5" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.orders')); ?></span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if($showManagementGroup): ?>
        <div>
            <p class="sidebar-group-label"><?php echo e(__('ui.navigation.management')); ?></p>
            <div class="sidebar-menu">
                <?php if($canInventory): ?>
                <a href="<?php echo e(route('inventory.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('inventory.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 7.5 12 3l8.25 4.5M3.75 7.5V16.5L12 21l8.25-4.5V7.5M3.75 7.5 12 12m0 0 8.25-4.5m-8.25 4.5V21" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.inventory')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canRecipes): ?>
                <a href="<?php echo e(route('recipes.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('recipes.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 4.5h10.5A2.25 2.25 0 0 1 19.5 6.75v10.5A2.25 2.25 0 0 1 17.25 19.5H6.75A2.25 2.25 0 0 1 4.5 17.25V6.75A2.25 2.25 0 0 1 6.75 4.5Zm3 3h4.5m-4.5 3h6.75m-6.75 3h6.75" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.recipes')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canSuppliers): ?>
                <a href="<?php echo e(route('suppliers.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('suppliers.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M1.5 12h12m-12 0V7.5A2.25 2.25 0 0 1 3.75 5.25h9.69a2.25 2.25 0 0 1 1.8.9l2.31 3.08a2.25 2.25 0 0 0 1.8.9h2.16v2.88a2.25 2.25 0 0 1-2.25 2.25M7.5 18a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm12 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.suppliers')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canPurchases): ?>
                <a href="<?php echo e(route('purchases.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('purchases.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 3.75h9A2.25 2.25 0 0 1 18.75 6v12A2.25 2.25 0 0 1 16.5 20.25h-9A2.25 2.25 0 0 1 5.25 18V6A2.25 2.25 0 0 1 7.5 3.75Zm2.25 4.5h4.5m-4.5 3h6m-6 3h6" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.purchases')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canTables): ?>
                <a href="<?php echo e(route('tables.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('tables.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 8.25h16.5m-13.5 0v9m10.5-9v9m-12 0h13.5a.75.75 0 0 0 .75-.75V8.25a.75.75 0 0 0-.75-.75H5.25a.75.75 0 0 0-.75.75v8.25c0 .414.336.75.75.75Z" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.tables')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canCategories): ?>
                <a href="<?php echo e(route('categories.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('categories.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 6.75A2.25 2.25 0 0 1 6.75 4.5h4.5a2.25 2.25 0 0 1 2.25 2.25v4.5a2.25 2.25 0 0 1-2.25 2.25h-4.5A2.25 2.25 0 0 1 4.5 11.25v-4.5Zm9 0A2.25 2.25 0 0 1 15.75 4.5h1.5a2.25 2.25 0 0 1 2.25 2.25v1.5a2.25 2.25 0 0 1-2.25 2.25h-1.5A2.25 2.25 0 0 1 13.5 8.25v-1.5Zm0 9A2.25 2.25 0 0 1 15.75 13.5h1.5a2.25 2.25 0 0 1 2.25 2.25v1.5a2.25 2.25 0 0 1-2.25 2.25h-1.5a2.25 2.25 0 0 1-2.25-2.25v-1.5Zm-9 0A2.25 2.25 0 0 1 6.75 13.5h4.5a2.25 2.25 0 0 1 2.25 2.25v1.5a2.25 2.25 0 0 1-2.25 2.25h-4.5A2.25 2.25 0 0 1 4.5 17.25v-1.5Z" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.categories')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canProducts): ?>
                <a href="<?php echo e(route('products.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('products.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75A2.25 2.25 0 0 1 6 4.5h12a2.25 2.25 0 0 1 2.25 2.25v10.5A2.25 2.25 0 0 1 18 19.5H6a2.25 2.25 0 0 1-2.25-2.25V6.75Zm4.5 2.25h7.5m-7.5 3h7.5m-7.5 3h4.5" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.products')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canCustomers): ?>
                <a href="<?php echo e(route('customers.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('customers.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM3.75 19.5a8.25 8.25 0 0 1 16.5 0" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.customers')); ?></span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if($showAdminGroup): ?>
        <div>
            <p class="sidebar-group-label"><?php echo e(__('ui.navigation.admin')); ?></p>
            <div class="sidebar-menu">
                <?php if($canEmployees && $employeesNavRoute): ?>
                <a href="<?php echo e($employeesNavRoute); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('employees.*') || request()->routeIs('attendance.*') || request()->routeIs('salaries.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.75a3 3 0 0 0 3-3v-1.5a3 3 0 0 0-2.25-2.9M18 18.75H6m12 0v1.5m-12-1.5v1.5m0-1.5a3 3 0 0 1-3-3v-1.5a3 3 0 0 1 2.25-2.9m10.5-4.35a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm-9 1.5a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Zm12 0a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Z" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.employees')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canUsers): ?>
                <a href="<?php echo e(route('printers.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('printers.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.printers')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canUsers): ?>
                <a href="<?php echo e(route('users.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('users.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.75a3 3 0 0 0 3-3v-1.5a3 3 0 0 0-2.25-2.9M18 18.75H6m12 0v1.5m-12-1.5v1.5m0-1.5a3 3 0 0 1-3-3v-1.5a3 3 0 0 1 2.25-2.9m10.5-4.35a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm-9 1.5a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Zm12 0a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Z" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.users')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canMarketing && $marketingNavRoute): ?>
                <a href="<?php echo e($marketingNavRoute); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('offers.*') || request()->routeIs('coupons.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.568 3.756A2.25 2.25 0 0 1 11.355 3h4.09a2.25 2.25 0 0 1 1.788.756l2.998 3.401a2.25 2.25 0 0 1 0 2.986l-7.57 8.59a2.25 2.25 0 0 1-3.18 0L2.91 13.143a2.25 2.25 0 0 1 0-2.986l6.657-6.4Z" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.offers')); ?></span>
                </a>
                <?php endif; ?>

                <?php if($canReports): ?>
                <a href="<?php echo e(route('reports.shift-logs')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('reports.shift-logs*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 6.75h7.5m-7.5 4.5h7.5m-7.5 4.5h4.5M4.5 6A2.25 2.25 0 0 1 6.75 3.75h10.5A2.25 2.25 0 0 1 19.5 6v12a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 18V6Z" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.shift_logs')); ?></span>
                </a>
                <a href="<?php echo e(route('financial.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sidebar-link', 'active'=> request()->routeIs('financial.*')]); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>
                    <span class="sidebar-label"><?php echo e(__('ui.navigation.financial')); ?></span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </nav>

    <div class="mt-auto px-3 py-3" style="border-top:1px solid color-mix(in srgb,var(--outline-var) 30%,transparent 70%)">
        <button data-sidebar-collapse type="button" class="top-icon-btn flex w-full items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 6l-6 6 6 6" />
            </svg>
        </button>
    </div>
</aside>
<?php /**PATH /var/www/dots-main/resources/views/components/layouts/sidebar.blade.php ENDPATH**/ ?>