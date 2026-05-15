<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'description' => 'Access system dashboard'],
            ['name' => 'Access POS', 'slug' => 'pos.view', 'description' => 'Access the cashier POS screen'],
            ['name' => 'Access Waiter Screen', 'slug' => 'waiter.view', 'description' => 'Access the waiter order screen'],
            ['name' => 'Access KDS', 'slug' => 'kds.view', 'description' => 'Access kitchen display system'],
            ['name' => 'Access Bar Display', 'slug' => 'bar.view', 'description' => 'Access bar display system'],
            ['name' => 'View Orders', 'slug' => 'orders.view', 'description' => 'View orders, invoices, and order details'],
            ['name' => 'Create Orders', 'slug' => 'orders.create', 'description' => 'Create new POS and waiter orders'],
            ['name' => 'Update Orders', 'slug' => 'orders.update', 'description' => 'Update order status, discounts, and quantities'],
            ['name' => 'Delete Orders', 'slug' => 'orders.delete', 'description' => 'Delete orders and order items'],
            ['name' => 'View Inventory', 'slug' => 'inventory.view', 'description' => 'View inventory dashboard and stock state'],
            ['name' => 'Create Inventory Items', 'slug' => 'inventory.create', 'description' => 'Create new inventory materials'],
            ['name' => 'Update Inventory Items', 'slug' => 'inventory.update', 'description' => 'Edit inventory materials and stock data'],
            ['name' => 'Delete Inventory Items', 'slug' => 'inventory.delete', 'description' => 'Delete inventory materials'],
            ['name' => 'Adjust Inventory Stock', 'slug' => 'inventory.adjust', 'description' => 'Adjust inventory stock quantities'],
            ['name' => 'Audit Inventory Stock', 'slug' => 'inventory.audit', 'description' => 'Run inventory audits'],
            ['name' => 'View Recipes', 'slug' => 'recipes.view', 'description' => 'View recipes and production formulas'],
            ['name' => 'Create Recipes', 'slug' => 'recipes.create', 'description' => 'Create recipes and semi-finished formulas'],
            ['name' => 'Update Recipes', 'slug' => 'recipes.update', 'description' => 'Edit recipes and semi-finished formulas'],
            ['name' => 'Delete Recipes', 'slug' => 'recipes.delete', 'description' => 'Delete recipes and semi-finished formulas'],
            ['name' => 'View Tables', 'slug' => 'tables.view', 'description' => 'View restaurant tables'],
            ['name' => 'Create Tables', 'slug' => 'tables.create', 'description' => 'Create restaurant tables'],
            ['name' => 'Update Tables', 'slug' => 'tables.update', 'description' => 'Edit or toggle table status'],
            ['name' => 'Delete Tables', 'slug' => 'tables.delete', 'description' => 'Delete restaurant tables'],
            ['name' => 'View Categories', 'slug' => 'categories.view', 'description' => 'View menu categories'],
            ['name' => 'Create Categories', 'slug' => 'categories.create', 'description' => 'Create menu categories'],
            ['name' => 'Update Categories', 'slug' => 'categories.update', 'description' => 'Edit menu categories'],
            ['name' => 'Delete Categories', 'slug' => 'categories.delete', 'description' => 'Delete menu categories'],
            ['name' => 'View Products', 'slug' => 'products.view', 'description' => 'View products'],
            ['name' => 'Create Products', 'slug' => 'products.create', 'description' => 'Create products'],
            ['name' => 'Update Products', 'slug' => 'products.update', 'description' => 'Edit products'],
            ['name' => 'Delete Products', 'slug' => 'products.delete', 'description' => 'Delete products'],
            ['name' => 'View Customers', 'slug' => 'customers.view', 'description' => 'View customer records'],
            ['name' => 'Create Customers', 'slug' => 'customers.create', 'description' => 'Create customer records'],
            ['name' => 'Update Customers', 'slug' => 'customers.update', 'description' => 'Edit customer records'],
            ['name' => 'Delete Customers', 'slug' => 'customers.delete', 'description' => 'Delete customer records'],
            ['name' => 'View Users', 'slug' => 'users.view', 'description' => 'View system users'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'description' => 'Create system users'],
            ['name' => 'Update Users', 'slug' => 'users.update', 'description' => 'Edit users and toggle account status'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'description' => 'Delete system users'],
            ['name' => 'View Suppliers', 'slug' => 'suppliers.view', 'description' => 'View suppliers'],
            ['name' => 'Create Suppliers', 'slug' => 'suppliers.create', 'description' => 'Create suppliers'],
            ['name' => 'Update Suppliers', 'slug' => 'suppliers.update', 'description' => 'Edit suppliers and supplier financial actions'],
            ['name' => 'Delete Suppliers', 'slug' => 'suppliers.delete', 'description' => 'Delete suppliers'],
            ['name' => 'View Purchases', 'slug' => 'purchases.view', 'description' => 'View purchase records and invoices'],
            ['name' => 'Create Purchases', 'slug' => 'purchases.create', 'description' => 'Create purchase records'],
            ['name' => 'Update Purchases', 'slug' => 'purchases.update', 'description' => 'Update purchase attachments and records'],
            ['name' => 'Approve Purchases', 'slug' => 'purchases.approve', 'description' => 'Approve or reject purchase and general expense requests'],
            ['name' => 'View Coupons', 'slug' => 'coupons.view', 'description' => 'View coupons'],
            ['name' => 'Create Coupons', 'slug' => 'coupons.create', 'description' => 'Create coupons'],
            ['name' => 'Update Coupons', 'slug' => 'coupons.update', 'description' => 'Update coupons'],
            ['name' => 'Delete Coupons', 'slug' => 'coupons.delete', 'description' => 'Delete coupons'],
            ['name' => 'View Offers', 'slug' => 'offers.view', 'description' => 'View offers'],
            ['name' => 'Create Offers', 'slug' => 'offers.create', 'description' => 'Create offers'],
            ['name' => 'Update Offers', 'slug' => 'offers.update', 'description' => 'Update offers'],
            ['name' => 'Delete Offers', 'slug' => 'offers.delete', 'description' => 'Delete offers'],
            ['name' => 'View Employees', 'slug' => 'employees.view', 'description' => 'View employees and employee reports'],
            ['name' => 'Create Employees', 'slug' => 'employees.create', 'description' => 'Create employees'],
            ['name' => 'Update Employees', 'slug' => 'employees.update', 'description' => 'Update employees and salary adjustments'],
            ['name' => 'Delete Employees', 'slug' => 'employees.delete', 'description' => 'Delete employees'],
            ['name' => 'View Attendance', 'slug' => 'attendance.view', 'description' => 'View attendance records'],
            ['name' => 'Create Attendance', 'slug' => 'attendance.create', 'description' => 'Create attendance and quick check-ins'],
            ['name' => 'Update Attendance', 'slug' => 'attendance.update', 'description' => 'Update attendance records'],
            ['name' => 'Delete Attendance', 'slug' => 'attendance.delete', 'description' => 'Delete attendance records'],
            ['name' => 'View Salaries', 'slug' => 'salaries.view', 'description' => 'View salaries'],
            ['name' => 'Create Salaries', 'slug' => 'salaries.create', 'description' => 'Create salary payments'],
            ['name' => 'Update Salaries', 'slug' => 'salaries.update', 'description' => 'Update salary payment status'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'description' => 'Create and update roles'],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'description' => 'Assign and manage permissions'],
            ['name' => 'Manage Billing', 'slug' => 'billing.manage', 'description' => 'Manage invoices and payments'],
            ['name' => 'View Reports', 'slug' => 'reports.view', 'description' => 'Access operational and financial reports'],
            ['name' => 'View Financial Dashboard', 'slug' => 'financial.view', 'description' => 'Access comprehensive financial dashboard'],
        ];

        $permissionIdsBySlug = collect($permissions)
            ->mapWithKeys(function (array $permission): array {
                $permissionModel = Permission::query()->updateOrCreate(
                    ['slug' => $permission['slug']],
                    [
                        'name' => $permission['name'],
                        'description' => $permission['description'],
                    ]
                );

                return [$permission['slug'] => (int) $permissionModel->id];
            });

        $allPermissionSlugs = $permissionIdsBySlug->keys()->all();

        $roleTemplates = [
            [
                'slug' => 'admin',
                'name' => 'الإدارة',
                'description' => 'صلاحية كاملة على النظام',
                'permission_slugs' => $allPermissionSlugs,
            ],
            [
                'slug' => 'cashier',
                'name' => 'الكاشير',
                'description' => 'إدارة المبيعات والطلبات اليومية',
                'permission_slugs' => [
                    'dashboard.view',
                    'pos.view',
                    'orders.view',
                    'orders.create',
                    'orders.update',
                    'tables.view',
                    'customers.view',
                    'customers.create',
                    'customers.update',
                ],
            ],
            [
                'slug' => 'accountant',
                'name' => 'المحاسب',
                'description' => 'الوصول للتقارير والفواتير والمتابعة المالية',
                'permission_slugs' => [
                    'dashboard.view',
                    'billing.manage',
                    'purchases.view',
                    'suppliers.view',
                    'suppliers.update',
                    'salaries.view',
                    'salaries.update',
                    'reports.view',
                    'financial.view',
                ],
            ],
            [
                'slug' => 'warehouse_manager',
                'name' => 'مدير المخزن',
                'description' => 'إدارة المخزون والمشتريات والتوريد',
                'permission_slugs' => [
                    'dashboard.view',
                    'inventory.view',
                    'inventory.create',
                    'inventory.update',
                    'inventory.adjust',
                    'inventory.audit',
                    'recipes.view',
                    'recipes.create',
                    'recipes.update',
                    'suppliers.view',
                    'suppliers.create',
                    'suppliers.update',
                    'purchases.view',
                    'purchases.create',
                    'purchases.update',
                    'purchases.approve',
                    'reports.view',
                ],
            ],
        ];

        foreach ($roleTemplates as $roleTemplate) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $roleTemplate['slug']],
                [
                    'name' => $roleTemplate['name'],
                    'description' => $roleTemplate['description'],
                ]
            );

            $permissionIds = collect($roleTemplate['permission_slugs'])
                ->map(static fn(string $slug): ?int => $permissionIdsBySlug[$slug] ?? null)
                ->filter(static fn(?int $id): bool => is_int($id))
                ->values()
                ->all();

            $role->permissions()->sync($permissionIds);
        }
    }
}
