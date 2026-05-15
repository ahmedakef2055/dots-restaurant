# توثيق نظام Systemco.me

## نظرة عامة

**Systemco.me** هو نظام متكامل لإدارة المطاعم مبني على **Laravel 13** مع PHP 8.3+. يغطي النظام دورة عمل المطعم بالكامل: نقطة البيع (POS)، شاشة المطبخ (KDS)، إدارة المخزون متعدد المستودعات، الوصفات، المشتريات، الموارد البشرية، الرواتب، والتقارير المالية. يدعم النظام اللغتين العربية والإنجليزية مع دعم كامل لطباعة ESC/POS على الطابعات الحرارية.

---

## جدول المحتويات

1. [البنية التقنية](#1-البنية-التقنية)
2. [هيكل المشروع](#2-هيكل-المشروع)
3. [النماذج (Models) وقاعدة البيانات](#3-النماذج-models-وقاعدة-البيانات)
4. [الخدمات (Services)](#4-الخدمات-services)
5. [المتحكمات (Controllers)](#5-المتحكمات-controllers)
6. [نظام الصلاحيات والأدوار](#6-نظام-الصلاحيات-والأدوار)
7. [نظام الطباعة](#7-نظام-الطباعة)
8. [تصدير PDF والتقارير](#8-تصدير-pdf-والتقارير)
9. [تدفقات العمل الرئيسية](#9-تدفقات-العمل-الرئيسية)
10. [الواجهات (Views)](#10-الواجهات-views)
11. [الإعدادات والبيئة](#11-الإعدادات-والبيئة)
12. [قاعدة البيانات - Migration Timeline](#12-قاعدة-البيانات---migration-timeline)

---

## 1. البنية التقنية

### المكدس التقني

| المكون | التقنية |
|--------|---------|
| Framework | Laravel 13.0 |
| PHP | 8.3+ |
| قاعدة البيانات الافتراضية | SQLite (dev) / MySQL (prod) |
| Cache & Session | Redis / Database |
| Timezone | Africa/Cairo |
| اللغات المدعومة | العربية (ar)، الإنجليزية (en) |

### الحزم الرئيسية

| الحزمة | الغرض |
|--------|--------|
| `ar-php/ar-php` | دعم اللغة العربية ومعالجة النصوص |
| `barryvdh/laravel-dompdf` | توليد PDF سريع |
| `mpdf/mpdf` | توليد PDF مع دعم كامل للعربية |
| `mike42/escpos-php` | طباعة ESC/POS للطابعات الحرارية |
| `wkhtmltoimage` | تحويل HTML إلى صورة للطباعة |
| `ImageMagick` | معالجة الصور للطابعات |

### Feature Flags

| الخاصية | المتغير البيئي | القيمة الافتراضية |
|---------|--------------|------------------|
| ربط الكاشير بالمخزون والوصفات | `CASHIER_INVENTORY_RECIPE_LINK_ENABLED` | `false` |

> عند تفعيل هذه الخاصية، يتحقق النظام من توافر المكونات في المخزون قبل إتمام الطلب، ويخصم المخزون تلقائياً عند الدفع.

---

## 2. هيكل المشروع

```
/var/www/Systemco.me/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # 28 متحكم
│   │   └── Middleware/         # 4 middleware
│   ├── Models/                 # 35+ نموذج Eloquent
│   ├── Services/               # 6 خدمات أعمال
│   ├── Support/                # أدوات مساعدة (PDF, Currency)
│   ├── Notifications/          # إشعارات اعتماد المشتريات
│   └── Providers/
├── config/
│   ├── app.php                 # إعدادات التطبيق
│   ├── features.php            # Feature flags
│   └── database.php
├── database/
│   └── migrations/             # 60+ migration
├── resources/
│   ├── views/                  # Blade templates
│   └── lang/
│       ├── ar/                 # ترجمات عربية
│       └── en/                 # ترجمات إنجليزية
├── routes/
│   └── web.php                 # 727 سطر - كل المسارات
└── storage/
```

---

## 3. النماذج (Models) وقاعدة البيانات

### 3.1 نماذج المستخدمين والصلاحيات

#### `User`
جدول: `users`

| الحقل | النوع | الوصف |
|-------|-------|--------|
| id | bigint PK | معرف |
| name | string | الاسم الكامل |
| username | string unique | اسم المستخدم |
| phone | string nullable | الهاتف |
| email | string nullable | البريد الإلكتروني |
| password | string | كلمة المرور (hashed) |
| role_id | FK roles | الدور الوظيفي |
| employee_id | FK employees | الموظف المرتبط |
| job_title | string | المسمى الوظيفي |
| is_active | boolean | حالة الحساب |

**العلاقات:**
- `roles()` → BelongsToMany (عبر role_user)
- `role()` → BelongsTo
- `permissions()` → BelongsToMany (عبر permission_user)
- `employee()` → BelongsTo

**الدوال الخاصة:**
- `hasPermission($slug)` — يتحقق من الصلاحية عبر الدور أو مباشرة للمستخدم

#### `Role` / `Permission`
نظام RBAC ثنائي المستوى: صلاحيات على مستوى الدور + صلاحيات مباشرة للمستخدم.

---

### 3.2 نماذج الطلبات والمبيعات

#### `Order`
جدول: `orders` — المفتاح الأساسي: `order_serial`

| الحقل | النوع | الوصف |
|-------|-------|--------|
| order_serial | bigint PK | الرقم التسلسلي الفريد |
| order_number | string | رقم الطلب |
| order_daily_number | int | الرقم اليومي (يُصفَّر يومياً) |
| user_id | FK users | الكاشير |
| shift_id | FK cashier_shifts | الوردية |
| customer_id | FK customers | العميل |
| restaurant_table_id | FK restaurant_tables | الطاولة |
| delivery_employee_id | FK employees | موظف التوصيل |
| coupon_id / offer_id | FK | الخصم المطبق |
| discount_type | enum(percentage,fixed) | نوع الخصم |
| discount_value | decimal | قيمة الخصم |
| subtotal | decimal | المجموع قبل الخصم |
| discount_amount | decimal | مبلغ الخصم |
| total | decimal | المبلغ النهائي |
| payment_method | string | طريقة الدفع |
| status | enum | حالة الطلب |
| kitchen_status | enum | حالة المطبخ |
| inventory_deducted_at | timestamp | وقت خصم المخزون |
| active_table_guard | unique nullable | يمنع طلبين نشطين على نفس الطاولة |

**حالات الطلب:**
```
pending → in_progress → open → completed
                              ↘ cancelled
```

**حالات المطبخ:**
```
pending → preparing → ready → served
```

#### `OrderItem`
جدول: `order_items`

| الحقل | الوصف |
|-------|--------|
| order_id | الطلب |
| product_id | المنتج |
| recipe_version_id | إصدار الوصفة المستخدم |
| quantity | الكمية |
| unit_price | السعر الفردي |
| line_total | الإجمالي |
| notes | ملاحظات خاصة |
| kitchen_status | حالة التحضير |
| kitchen_batch | دفعة المطبخ |
| preparation_station | محطة التحضير (kitchen/bar) |

---

### 3.3 نماذج المنتجات والوصفات

#### `Product`
جدول: `products`

| الحقل | الوصف |
|-------|--------|
| name | اسم المنتج |
| sku | كود المنتج |
| price | السعر (decimal:2) |
| is_active | هل نشط؟ |
| image_url | صورة المنتج |
| category_id | الفئة |
| preparation_station | محطة التحضير (kitchen/bar) |
| description | الوصف |

**العلاقات:** category, orderItems, recipeItems, recipeVersions

#### `RecipeVersion` — إصدارات الوصفة
جدول: `recipe_versions`

| الحقل | الوصف |
|-------|--------|
| product_id | المنتج |
| name | اسم الإصدار |
| is_active | الإصدار النشط |
| is_semi_finished | منتج شبه جاهز؟ |
| waste_percentage | نسبة الهدر |
| loss_percentage | نسبة الفقد |
| yield_quantity | الكمية المنتجة |
| total_cost | التكلفة الإجمالية المحسوبة |
| selling_price | سعر البيع المقترح |

#### `RecipeVersionItem` — مكونات الوصفة
- `item_type`: `ingredient` أو `recipe` (للمكونات شبه الجاهزة)
- `quantity_required`: الكمية المطلوبة
- يدعم التداخل حتى 8 مستويات

---

### 3.4 نماذج المخزون

#### `Ingredient`
جدول: `ingredients`

| الحقل | الوصف |
|-------|--------|
| name | الاسم |
| supplier_id | المورد الافتراضي |
| unit_id | وحدة القياس |
| default_warehouse_id | المستودع الافتراضي |
| cost | التكلفة (decimal:4) |
| current_stock | المخزون الحالي |
| threshold / reorder_level | مستوى إعادة الطلب |
| cost_method | طريقة التكلفة (FIFO/Average) |
| expiry_date | تاريخ الانتهاء |
| expiry_alert_days | أيام التنبيه قبل الانتهاء |

**العلاقات:** supplier, unitModel, defaultWarehouse, stockLogs, warehouseStocks, inventoryBatches

#### `IngredientWarehouseStock`
تتبع المخزون لكل مستودع على حدة.

| الحقل | الوصف |
|-------|--------|
| ingredient_id | المكون |
| warehouse_id | المستودع |
| quantity | الكمية |
| average_cost | متوسط التكلفة |

#### `InventoryBatch`
تتبع الدفعات بنظام FIFO.

| الحقل | الوصف |
|-------|--------|
| ingredient_id | المكون |
| warehouse_id | المستودع |
| purchase_item_id | عنصر المشتريات المصدر |
| quantity | الكمية الأصلية |
| remaining_quantity | المتبقي |
| unit_cost | تكلفة الوحدة |
| expiry_date | تاريخ الانتهاء |

#### `InventoryStockLog`
سجل كل عمليات المخزون (in/out/adjust/transfer).

#### `StockAudit` / `StockAuditItem`
جرد المخزون مع تتبع الفروقات بين الكميات المتوقعة والفعلية.

---

### 3.5 نماذج المشتريات

#### `Purchase`
جدول: `purchases`

| الحقل | الوصف |
|-------|--------|
| purchase_number | رقم أمر الشراء |
| type | `purchase` أو `expense` |
| supplier_id | المورد |
| warehouse_id | المستودع المستقبِل |
| status | `draft` / `approved` / `completed` |
| approval_status | `pending` / `approved` / `rejected` |
| approval_user_id | المعتمد |
| approval_at | وقت الاعتماد |
| invoice_number | رقم الفاتورة |
| invoice_file_path | ملف الفاتورة المرفوعة |
| subtotal / tax / discount / total | الأرقام المالية |

**دورة الاعتماد:** draft → طلب اعتماد → approved/rejected → completed (يضاف للمخزون)

#### `PurchaseItem` / `PurchasePayment` / `PurchaseReturn`
تفاصيل الأصناف، سجل الدفعات، والمرتجعات.

---

### 3.6 نماذج الموارد البشرية

#### `Employee`
جدول: `employees`

| الحقل | الوصف |
|-------|--------|
| employee_code | الكود الوظيفي |
| first_name / last_name | الاسم |
| national_id | الرقم القومي |
| position / department | المنصب والقسم |
| hire_date | تاريخ التعيين |
| base_salary | الراتب الأساسي |
| work_hours_per_day | ساعات العمل اليومية |
| attendance_days_per_week | أيام الحضور الأسبوعية |
| shift_start / shift_end | بداية ونهاية الوردية |
| hourly_rate | الأجر بالساعة |
| employment_type | نوع التوظيف |

**الحسابات المُشتقة:**
- `full_name` → first_name + last_name
- `daily_salary` → base_salary ÷ (attendance_days_per_week × 4.33)

#### `Attendance`
تسجيل الحضور والانصراف مع حساب الساعات والتأخير.

#### `SalaryPayment`
سجل صرف الرواتب الشهرية.

#### `EmployeeSalaryAdjustment`
التعديلات على الراتب: مكافآت، خصومات، تحميل منتجات.

#### `EmployeeDeliverySettlement`
تسوية أموال التوصيل مع سائقي الديليفري.

---

### 3.7 نماذج أخرى

#### `CashierShift`
| الحقل | الوصف |
|-------|--------|
| user_id | الكاشير |
| status | `open` / `closed` |
| opening_cash | النقدية الافتتاحية |
| expected_cash | المبلغ المتوقع عند الإغلاق |
| actual_cash | المبلغ الفعلي |
| open_shift_guard | يضمن وردية واحدة نشطة للكاشير |

#### `RestaurantTable`
| الحقل | الوصف |
|-------|--------|
| name | اسم/رقم الطاولة |
| capacity | السعة |
| status | `available` / `occupied` / `reserved` |

#### `Coupon`
كود خصم قابل للاستخدام مع قيود العدد والتاريخ ومبلغ الطلب الأدنى.

#### `Offer`
خصم تلقائي بشروط معينة (مبلغ الطلب، نطاق زمني، صلاحية يومية).

#### `Customer`
ملف عميل مع الهاتف والعنوان وتاريخ الطلبات.

#### `Supplier`
بيانات المورد مع تتبع المدفوعات.

#### `Warehouse`
إدارة مستودعات متعددة (رئيسي + فروع).

#### `Unit`
وحدات القياس مع عائلات التحويل (kg/g، L/ml).

#### `PrintJob` / `PrintLog`
قائمة انتظار الطباعة عبر QZ Tray مع سجل كامل لمحاولات الطباعة.

---

## 4. الخدمات (Services)

### 4.1 `InventoryService` — 1142 سطر
أشمل خدمة في النظام. تتولى كل عمليات المخزون.

**الدوال الرئيسية:**

| الدالة | الوصف |
|--------|--------|
| `defaultWarehouseId()` | إرجاع/إنشاء المستودع الرئيسي |
| `validateOrderStock($items, $warehouseId, $requireRecipe)` | التحقق من توافر المكونات قبل الطلب |
| `deductInventoryForOrder(Order $order)` | خصم المخزون عند اكتمال الطلب (transactional) |
| `addStock($ingredientId, $warehouseId, $qty, $cost, ...)` | استلام بضاعة مع تتبع الدفعات |
| `adjustStock($type, ...)` | تعديل المخزون (in/out/set) مع سجل تدقيق |
| `transferStock(...)` | نقل مخزون بين مستودعات |
| `createStockAudit(...)` | جرد المخزون مع تتبع الفروقات |
| `produceSemiFinishedBatch(...)` | إنتاج دفعة من منتج شبه جاهز مع خصم مكوناته |

**تفاصيل تقنية:**
- جميع عمليات الخصم محاطة بـ `DB::transaction()`
- استهلاك FIFO عبر `lockForUpdate()` لمنع تعارض الوصول المتزامن
- الحساب التكراري للوصفات المتداخلة (نسب الهدر والفقد)

---

### 4.2 `PromotionService` — 155 سطر
إدارة الخصومات والكوبونات.

| الدالة | الوصف |
|--------|--------|
| `resolveOffer($subtotal, $time)` | إيجاد أفضل عرض تلقائي مناسب |
| `validateCoupon($code, $userId, $subtotal)` | التحقق من صلاحية الكوبون |
| `calculateDiscountAmount($type, $value, $subtotal, $cap)` | حساب مبلغ الخصم |

---

### 4.3 `PrintService` — 817 سطر
خدمة الطباعة المتكاملة.

**طرق الطباعة:**

| الطريقة | الوصف |
|---------|--------|
| ESC/POS مباشر | اتصال بـ `/dev/usb/lp0` عبر `FilePrintConnector` |
| QZ Tray | base64 PDF/Image للطابعة عبر المتصفح |
| HTML → Image | `wkhtmltoimage` + ImageMagick |

**الدوال الرئيسية:**

| الدالة | الوصف |
|--------|--------|
| `printOrderInvoice(Order)` | طباعة إيصال العميل الكامل |
| `printCashierReceipt(Order)` | نسخة الكاشير (مع الأسعار) |
| `printPreparationReceipt(Order)` | تذكرة المطبخ (بدون أسعار) |
| `buildOrderReceiptBase64()` | توليد base64 لـ QZ Tray |
| `buildPreparationTicketBase64()` | تذكرة تحضير لـ QZ Tray |

**مميزات الطباعة:**
- إعادة المحاولة تلقائياً (2 مرات مع 800ms تأخير)
- دعم الشعار (تغيير الحجم لـ 384px لأسطوانات 80mm)
- QR Code أصلي عبر ESC/POS
- نصوص عربية عبر صور Raster (ImageMagick)
- تهيئة 3 طابعات: الكاشير، البار، المطبخ

---

### 4.4 `RecipeAnalyticsService` — 92 سطر
تحليل تكلفة الوصفات والربحية.

| الدالة | الوصف |
|--------|--------|
| `calculateRecipeVersionCost($versionId)` | حساب تكلفة وصفة بشكل تكراري مع نسب الهدر/الفقد |
| `refreshActiveVersionCost($productId)` | تحديث total_cost و selling_price للإصدار النشط |
| `profitAnalysisByProduct()` | هوامش الربح لكل منتج |

---

### 4.5 `UnitConversionService` — 37 سطر
تحويل وحدات القياس بين الأفراد من نفس العائلة (kg↔g، L↔ml).

---

### 4.6 `InventoryForecastService` — 97 سطر
التنبؤ بالمخزون.

| الدالة | الوصف |
|--------|--------|
| `buildSmartShortageSuggestions()` | عناصر تحت مستوى الحد مع توقع أيام المتبقية |
| `buildExpiryAlerts()` | دفعات تنتهي خلال N يوم |
| `usageByIngredient()` | تحليل الاستهلاك التاريخي (30 يوم) |

---

## 5. المتحكمات (Controllers)

### 5.1 نقطة البيع والعمليات

| المتحكم | المسؤوليات |
|---------|------------|
| `PosController` | إنشاء الطلبات، إدارة الورديات، عمليات الطاولات، البحث عن عملاء |
| `KdsController` | شاشة المطبخ، انتقالات حالة الطلب في الوقت الفعلي |
| `WaiterController` | واجهة النادل لخدمة الطاولات |
| `OrderController` | CRUD الطلبات، عرض الفواتير، طباعة مباشرة/مجدولة، انتقالات الحالة |
| `PrintJobController` | قائمة انتظار الطباعة لـ QZ Tray |

### 5.2 المخزون والوصفات

| المتحكم | المسؤوليات |
|---------|------------|
| `InventoryController` | CRUD المكونات، تعديلات المخزون، نقل المستودعات، الجرد |
| `RecipeController` | CRUD الوصفات وإصداراتها، إنتاج المكونات شبه الجاهزة |
| `ProductController` | إدارة المنتجات |
| `CategoryController` | تصنيف المنتجات |

### 5.3 المشتريات

| المتحكم | المسؤوليات |
|---------|------------|
| `PurchaseController` | أوامر الشراء، سير العمل للاعتماد، رفع الفواتير، طباعة مباشرة |
| `SupplierController` | إدارة الموردين، تتبع المدفوعات، معالجة المرتجعات |

### 5.4 الترويج والمبيعات

| المتحكم | المسؤوليات |
|---------|------------|
| `CouponController` | CRUD الكوبونات |
| `OfferController` | إدارة العروض الترويجية |

### 5.5 الموارد البشرية والرواتب

| المتحكم | المسؤوليات |
|---------|------------|
| `EmployeeController` | CRUD الموظفين، تسوية التوصيل، تقارير مالية (PDF/Excel) |
| `AttendanceController` | تسجيل الحضور/الانصراف، سجلات التصدير |
| `SalaryController` | إنشاء مدفوعات الرواتب، المعالجة الشهرية |
| `EmployeeSalaryAdjustmentController` | المكافآت، الخصومات، تحميل المنتجات |

### 5.6 الإدارة

| المتحكم | المسؤوليات |
|---------|------------|
| `UserManagementController` | حسابات المستخدمين، تعيين الأدوار/الصلاحيات |
| `RestaurantTableController` | إدارة طاولات الكافيه |
| `CustomerController` | ملفات العملاء، الهاتف، العنوان |
| `DashboardController` | لوحة التحليلات والإحصائيات |
| `ReportController` | تقارير الورديات، تصدير PDF |
| `FinancialController` | الملخصات المالية والتصدير |
| `AuthController` | تسجيل الدخول/الخروج، إدارة الجلسة |

---

## 6. نظام الصلاحيات والأدوار

### الهيكل

```
User ←──── role_user ────→ Role ←── permission_role ──→ Permission
  │                                                           ↑
  └──────── permission_user ─────────────────────────────────┘
             (صلاحيات مباشرة تتجاوز الدور)
```

### Middleware

| Middleware | الوظيفة |
|-----------|---------|
| `AuthenticateUser` | يتحقق من تسجيل الدخول، يمنع المستخدمين المعطلين |
| `EnsureUserHasPermission` | يتحقق من صلاحية المسار (`permission:slug`) |
| `SetLocale` | يضبط اللغة من Session → Cookie → افتراضي |
| `EnsureAuthenticatedPageHasPermission` | حماية إضافية للصفحات |

### صلاحيات المسارات الرئيسية

| الوحدة | صيغة الصلاحية |
|--------|--------------|
| لوحة التحكم | `dashboard.view` |
| نقطة البيع | `pos.view` |
| شاشة المطبخ | `kds.view` |
| شاشة البار | `bar.view` |
| الطلبات | `orders.view`, `orders.update`, `orders.delete` |
| المخزون | `inventory.view`, `inventory.manage` |
| الوصفات | `recipes.view`, `recipes.manage` |
| المشتريات | `purchases.view`, `purchases.manage`, `purchases.approve` |
| الموظفون | `employees.view`, `employees.manage` |
| الرواتب | `salaries.view`, `salaries.manage` |
| التقارير | `reports.view` |
| المالية | `financial.view` |
| المستخدمون | `users.manage` |

> **ملاحظة:** رفض صلاحية `dashboard.view` يعيد التوجيه إلى `pos.index` (لمستخدمي الكاشير الذين لا يحتاجون لوحة التحكم).

---

## 7. نظام الطباعة

### خريطة الطابعات

```
النظام
├── طابعة الكاشير  → إيصال العميل + نسخة الكاشير
├── طابعة المطبخ   → تذاكر التحضير (بدون أسعار)
└── طابعة البار    → تذاكر تحضير المشروبات
```

### تدفق الطباعة

```
طلب الطباعة
│
├─ ESC/POS مباشر?
│   ├── اتصال بـ /dev/usb/lp0
│   ├── بناء أوامر ESC/POS
│   ├── دعم QR Code أصلي
│   └── إعادة المحاولة × 2 (800ms تأخير)
│
└─ QZ Tray (عبر المتصفح)?
    ├── توليد HTML → Image (wkhtmltoimage)
    ├── معالجة ImageMagick (384px عرض)
    └── تشفير base64 → JavaScript → QZ Tray
```

### دعم اللغة العربية في الطباعة

- **ESC/POS:** نصوص أصلية للأحرف البسيطة
- **صور Raster:** تحويل النص العربي إلى صورة عبر ImageMagick لضمان عرض الخطوط

---

## 8. تصدير PDF والتقارير

### `PdfExportRenderer` — 479 سطر

**محركات التوليد (بالأولوية):**

| المحرك | متى يُستخدم |
|--------|-------------|
| **mPDF** | الأفضل للمحتوى العربي، دعم كامل UTF-8 |
| **DomPDF** | سريع، مع تشكيل أحرف عربية مخصص |
| **Chromium** | متصفح headless، احتياطي أخير |

**معالجة العربية في DomPDF:**
- جداول الأشكال السياقية (isolated, final, initial, medial)
- كشف الاتصال بين الأحرف
- عكس الاتجاه لـ RTL

### `CurrencyFormatter`
- إنجليزي: `EGP 110.00`
- عربي: `110.00 ج.م.`

### التقارير المتاحة للتصدير

| التقرير | PDF | Excel |
|---------|-----|-------|
| سجلات الورديات | ✓ | - |
| المخزون (سجلات الحركة) | ✓ | - |
| المشتريات | ✓ | - |
| الموظفون (قائمة نشطة) | ✓ | - |
| التقرير المالي الشهري للموظفين | ✓ | ✓ |
| الرواتب | ✓ | - |
| الحضور | ✓ | - |
| العملاء | ✓ | - |
| الموردون | ✓ | - |
| الوصفات | ✓ | - |
| التقرير المالي العام | ✓ | - |

---

## 9. تدفقات العمل الرئيسية

### 9.1 دورة الطلب الكاملة

```
1. الكاشير يفتح وردية (CashierShift: open)
2. إنشاء طلب جديد (status: pending)
   ├── تحديد نوع: dine-in / takeaway / delivery
   ├── إضافة منتجات
   └── تطبيق كوبون/عرض (اختياري)
3. إرسال الطلب للمطبخ (status: in_progress)
   └── KDS يستقبل الطلب (kitchen_status: pending)
4. المطبخ يبدأ التحضير (kitchen_status: preparing)
5. الطلب جاهز (kitchen_status: ready)
6. تقديم الطلب (kitchen_status: served)
7. الدفع وإتمام الطلب (status: completed)
   └── [إذا مفعّل] خصم المخزون تلقائياً
8. طباعة الإيصال (ESC/POS أو QZ Tray)
9. إغلاق الوردية مع تسوية النقدية
```

### 9.2 دورة المشتريات

```
1. إنشاء طلب شراء (status: draft, approval_status: pending)
2. رفع فاتورة الشراء (PDF/صورة)
3. إرسال للاعتماد
4. المدير يعتمد أو يرفض (approval_status: approved/rejected)
   └── إشعار تلقائي للطالب
5. [إذا معتمد] إتمام الاستلام (status: completed)
   └── إضافة للمخزون مع تتبع الدفعات
6. تسجيل مدفوعات الشراء (جزئية أو كاملة)
```

### 9.3 دورة الوصفات والمخزون

```
1. تعريف مكونات (Ingredients) مع وحدات وتكاليف
2. إنشاء وصفة منتج (RecipeVersion)
   ├── إضافة مكونات مع كميات
   ├── تحديد نسب الهدر والفقد
   └── حساب التكلفة تلقائياً (RecipeAnalyticsService)
3. [اختياري] إنتاج منتج شبه جاهز
   └── خصم مكوناته وإضافة رصيده للمخزون
4. عند تأكيد الدفع:
   └── InventoryService.deductInventoryForOrder()
       ├── بناء متطلبات الوصفة (تكرارياً)
       ├── استهلاك دفعات FIFO
       └── تحديث IngredientWarehouseStock
```

### 9.4 دورة الرواتب الشهرية

```
1. مراجعة سجلات الحضور (AttendanceController)
2. إضافة تعديلات (مكافآت/خصومات) - EmployeeSalaryAdjustment
3. تسوية الديليفري - EmployeeDeliverySettlement
4. توليد كشف الرواتب الشهري (SalaryController)
5. تصدير PDF/Excel للمحاسبة
```

---

## 10. الواجهات (Views)

### هيكل القوالب

```
resources/views/
├── components/
│   ├── layouts/
│   │   ├── app.blade.php          # Layout رئيسي مع sidebar
│   │   ├── pos-shell.blade.php    # Layout نقطة البيع
│   │   └── sidebar.blade.php      # القائمة الجانبية
│   ├── ui/
│   │   ├── button.blade.php       # أزرار متسقة
│   │   ├── card.blade.php         # بطاقات المحتوى
│   │   ├── flash-toast.blade.php  # إشعارات النجاح/الخطأ
│   │   ├── modal.blade.php        # نوافذ حوارية
│   │   └── table.blade.php        # جداول البيانات
│   └── icon.blade.php             # أيقونات SVG
│
├── pos/index.blade.php            # واجهة الكاشير الكاملة
├── kds/index.blade.php            # شاشة المطبخ
├── waiter/index.blade.php         # واجهة النادل
├── orders/
│   ├── index.blade.php            # قائمة الطلبات
│   ├── show.blade.php             # تفاصيل الطلب
│   ├── invoice.blade.php          # الفاتورة
│   └── preparation-receipt.blade.php  # تذكرة التحضير
├── dashboard.blade.php            # لوحة التحكم
├── auth/login.blade.php           # صفحة تسجيل الدخول
│
├── [inventory/recipes/products/categories/]
│   └── index, create, edit, _form
├── [purchases/suppliers/customers/]
│   └── index, create, edit, show, _form, exports/
├── [employees/attendance/salaries/]
│   └── index, create, edit, show, exports/, reports/
├── [users/tables/coupons/offers/]
│   └── CRUD views
├── [reports/financial/]
│   └── index, exports/
└── errors/
    └── 401, 402, 403, 404, 419, 423, 429, 500, 503
```

---

## 11. الإعدادات والبيئة

### متغيرات البيئة المهمة

```env
# التطبيق
APP_NAME=Systemco
APP_ENV=production
APP_KEY=...
APP_TIMEZONE=Africa/Cairo

# قاعدة البيانات
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=systemco
DB_USERNAME=root
DB_PASSWORD=...

# المخزون والكاشير
CASHIER_INVENTORY_RECIPE_LINK_ENABLED=true

# طابعة ESC/POS
CASHIER_PRINTER_PORT=/dev/usb/lp0
BAR_PRINTER_PORT=/dev/usb/lp1
KITCHEN_PRINTER_PORT=/dev/usb/lp2

# Redis (اختياري)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### إعداد المشروع (تطوير)

```bash
composer setup
# يقوم بـ:
# 1. composer install
# 2. cp .env.example .env
# 3. php artisan key:generate
# 4. php artisan migrate
# 5. npm install && npm run build
```

### تشغيل بيئة التطوير

```bash
composer run dev
# يشغّل تزامناً:
# - php artisan serve
# - php artisan queue:listen
# - php artisan pail (logs)
# - npm run dev (Vite HMR)
```

---

## 12. قاعدة البيانات - Migration Timeline

| التاريخ | الـ Migration |
|---------|--------------|
| 2026-04-08 | جداول أساسية: users, roles, permissions, products, orders, ingredients, customers, suppliers, purchases, employees, coupons, offers |
| 2026-04-09 | restaurant_tables, categories، إضافة متابعة المطبخ للطلبات |
| 2026-04-10 | ترقية نظام المخزون والوصفات، نقل المستودعات، مدفوعات المشتريات، المرتجعات |
| 2026-04-11 | رفع فواتير المشتريات، ربط العملاء بالطلبات، جداول تعديلات الرواتب، تسويات التوصيل |
| 2026-04-16 | نظام المستخدمين الكامل: user_profiles, roles, permissions, is_active |
| 2026-04-17 | سير عمل اعتماد المشتريات، جداول الإشعارات، تنظيف الصلاحيات القديمة |
| 2026-04-18 | ورديات الكاشير (cashier_shifts)، ربطها بالطلبات، سجلات الورديات |
| 2026-04-23 | إضافة حالة `served` لـ kitchen_status |
| 2026-04-26 | طريقة الدفع للطلبات، حالة `reserved` للطاولات |
| 2026-04-28 | إعادة تسمية PK من `id` إلى `order_serial` |
| 2026-04-29 | إضافة `order_daily_number` للترقيم اليومي |
| 2026-05-01 | إضافة `job_title` للمستخدمين |
| 2026-05-02 | نظام قائمة انتظار الطباعة (print_jobs, print_logs) |

---

## ملاحظات تقنية مهمة

### القرارات المعمارية الرئيسية

1. **طباعة ESC/POS مباشر** بدلاً من المتصفح — تفادياً لبطء Chromium في بيئات الإنتاج
2. **PDF متعدد المحركات** — DomPDF (سرعة) → mPDF (عربية) → Chromium (احتياطي)
3. **FIFO + قفل تشاؤمي** — `lockForUpdate()` لمنع تعارض الخصم المتزامن
4. **DB::transaction** لكل عمليات المخزون — ضمان الاتساق عند أي خطأ
5. **تداخل الوصفات** — حساب تكراري حتى 8 مستويات مع نسب الهدر والفقد
6. **Guard Fields** — `active_table_guard` و `open_shift_guard` لمنع الإدخال المزدوج
7. **Feature Flag** — `CASHIER_INVENTORY_RECIPE_LINK_ENABLED` للتحكم في ربط المخزون بالكاشير
8. **ترقيم يومي** — `order_daily_number` يُصفَّر كل يوم لسهولة التتبع التشغيلي

### نقاط ضعف محتملة للمراجعة

- ملف `routes/web.php` كبير (727 سطر) — يمكن تقسيمه لـ Route Groups منفصلة
- Feature flag `cashier_inventory_recipe_link_enabled` معطّل افتراضياً — تأكد من تفعيله في الإنتاج إذا كنت تريد تتبع المخزون
- طابعة ESC/POS تحتاج `/dev/usb/lp0` — تأكد من الصلاحيات: `sudo usermod -a -G lp www-data`
- `wkhtmltoimage` يحتاج تثبيتاً منفصلاً على الخادم
