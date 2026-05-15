const root = document.documentElement;
const LANGUAGE_STORAGE_KEY = "app-language";
const DEFAULT_LANGUAGE = "en";
const ARABIC_LANGUAGE = "ar";
const TRANSLATABLE_ATTRIBUTES = ["placeholder", "title", "aria-label"];
const LOCALE_ENDPOINT = "/locale";
const LOCALE_TRANSLATIONS_ENDPOINT = "/locale/translations";

const trackedTextNodes = [];
const trackedTextNodeSet = new WeakSet();
const trackedElements = new Set();
const elementOriginalAttributes = new WeakMap();
const OBSERVER_OPTIONS = {
  subtree: true,
  childList: true,
  characterData: true,
  attributes: true,
  attributeFilter: TRANSLATABLE_ATTRIBUTES,
};

let originalDocumentTitle = "";
let activeLanguage = DEFAULT_LANGUAGE;
let isApplyingTranslations = false;
let localizationObserver = null;
let arabicRefreshScheduled = false;
let dynamicTranslationsPromise = null;
let dynamicTranslationsLoaded = false;

const dynamicTranslationMaps = {
  enToArExact: Object.create(null),
  arToEnExact: Object.create(null),
  enToArPatterns: [],
  arToEnPatterns: [],
};

const normalizeKey = (value) => value.replace(/\s+/g, " ").trim();
const toLookupKey = (value) => normalizeKey(value).toLowerCase();
const containsArabic = (value) => /[\u0600-\u06FF]/.test(value);

const getServerLanguage = () =>
  root.getAttribute("lang") === ARABIC_LANGUAGE
    ? ARABIC_LANGUAGE
    : DEFAULT_LANGUAGE;

const getCsrfToken = () =>
  document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") ??
  "";

const syncServerLanguage = async (language) => {
  const csrfToken = getCsrfToken();

  if (!csrfToken) {
    return;
  }

  try {
    await fetch(LOCALE_ENDPOINT, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN": csrfToken,
      },
      body: JSON.stringify({ locale: language }),
      credentials: "same-origin",
    });
  } catch {
    // The UI remains responsive even when locale sync fails.
  }
};

const RAW_AR_TRANSLATIONS = {
  "Account Info": "معلومات الحساب",
  Active: "نشط",
  "Active Tables": "الطاولات النشطة",
  Absent: "غائب",
  Action: "إجراء",
  Add: "إضافة",
  "Add a new customer profile": "إضافة ملف عميل جديد",
  "Add a new staff profile": "إضافة ملف موظف جديد",
  "Add a new supplier profile": "إضافة ملف مورد جديد",
  "Add Attendance": "إضافة حضور",
  "Add Customer": "إضافة عميل",
  "Add Employee": "إضافة موظف",
  "Add Product Charge": "إضافة تحميل منتج",
  "Add Ingredient": "إضافة مكون",
  "Add Item": "إضافة صنف",
  "Add Table": "إضافة طاولة",
  "Add Category": "إضافة تصنيف",
  "Add Supplier": "إضافة مورد",
  Address: "العنوان",
  Adjust: "تسوية",
  "Adjust Stock": "تسوية المخزون",
  "Adjustment Type": "نوع التسوية",
  "Adjustment would result in negative stock, which is not allowed.":
    "هذه التسوية ستؤدي إلى مخزون سالب، وهذا غير مسموح.",
  Admin: "الإدارة",
  All: "الكل",
  "All Departments": "كل الأقسام",
  "All Employees": "كل الموظفين",
  "All Categories": "كل التصنيفات",
  "All Products": "كل المنتجات",
  "All Order Types": "كل أنواع الطلبات",
  "All Statuses": "كل الحالات",
  "All Suppliers": "كل الموردين",
  "All Types": "كل الأنواع",
  AM: "ص",
  Amount: "المبلغ",
  "Apply Adjustment": "تطبيق التسوية",
  "Apply Payment": "تسجيل الدفع",
  Attendance: "الحضور",
  "Attendance Deduction": "خصم الحضور",
  "Attendance record deleted successfully.": "تم حذف سجل الحضور بنجاح.",
  "Attendance recorded successfully.": "تم تسجيل الحضور بنجاح.",
  "Attendance updated successfully.": "تم تحديث الحضور بنجاح.",
  "Auto-apply promotional discounts": "تطبيق خصومات العروض تلقائيا",
  "Avg Order Value": "متوسط قيمة الطلب",
  "Back to Attendance": "العودة إلى الحضور",
  "Back to Coupon": "العودة إلى القسيمة",
  "Back to Coupons": "العودة إلى القسائم",
  "Back to Customers": "العودة إلى العملاء",
  "Back to Employees": "العودة إلى الموظفين",
  "Back to Inventory": "العودة إلى المخزون",
  "Back to Offer": "العودة إلى العرض",
  "Back to Offers": "العودة إلى العروض",
  "Back to Orders": "العودة إلى الطلبات",
  "Back to Profile": "العودة إلى الملف الشخصي",
  "Back to Purchases": "العودة إلى المشتريات",
  "Back to Recipes": "العودة إلى الوصفات",
  "Back to Tables": "العودة إلى الطاولات",
  "Back to Categories": "العودة إلى التصنيفات",
  "Back to Salaries": "العودة إلى الرواتب",
  "Back to Supplier": "العودة إلى المورد",
  "Back to Suppliers": "العودة إلى الموردين",
  Actions: "الإجراءات",
  "Base Salary": "الراتب الأساسي",
  "Base Salary (optional override)": "الراتب الأساسي (تعديل اختياري)",
  Bonus: "المكافأة",
  "Bonus Amount": "قيمة المكافأة",
  Burgers: "برجر",
  "Business insights & performance": "رؤى الأداء والأعمال",
  Cancel: "إلغاء",
  Cancelled: "ملغي",
  Cart: "السلة",
  "Cart is empty": "السلة فارغة",
  Cashier: "الكاشير",
  "Cashier:": "الكاشير:",
  "Check In": "تسجيل دخول",
  "Check-in recorded successfully.": "تم تسجيل وقت الحضور بنجاح.",
  "Confirm Check In": "تأكيد الحضور",
  "Check Out": "تسجيل خروج",
  "Check-out recorded successfully.": "تم تسجيل وقت الانصراف بنجاح.",
  "Confirm Check Out": "تأكيد الانصراف",
  City: "المدينة",
  Code: "الكود",
  Confirm: "تأكيد",
  "Contact Person": "الشخص المسؤول",
  Country: "الدولة",
  "Coupon Code": "كود القسيمة",
  "Coupon created successfully.": "تم إنشاء القسيمة بنجاح.",
  "Coupon deleted successfully.": "تم حذف القسيمة بنجاح.",
  "Coupon Details": "تفاصيل القسيمة",
  "Coupon discount is not applicable.": "خصم القسيمة غير قابل للتطبيق.",
  "Coupon has expired.": "انتهت صلاحية القسيمة.",
  "Coupon is invalid or inactive.": "القسيمة غير صحيحة أو غير مفعلة.",
  "Coupon is not active yet.": "القسيمة ليست مفعلة بعد.",
  "Coupon per-user limit reached.": "تم الوصول لحد الاستخدام لكل مستخدم.",
  "Coupon updated successfully.": "تم تحديث القسيمة بنجاح.",
  "Coupon usage limit reached.": "تم الوصول لحد استخدام القسيمة.",
  "Create a stock-managed ingredient": "إضافة مكون يتم تتبع مخزونه",
  "Create Coupon": "إنشاء قسيمة",
  "Create Customer": "إنشاء عميل",
  "Create Employee": "إنشاء موظف",
  "Create Ingredient": "إنشاء مكون",
  "Create Offer": "إنشاء عرض",
  "Create Purchase": "إنشاء عملية شراء",
  "Create Table": "إنشاء طاولة",
  "Create Category": "إنشاء تصنيف",
  "Create Salary": "إنشاء راتب",
  "Create Salary Record": "إنشاء سجل راتب",
  "Create Supplier": "إنشاء مورد",
  Created: "تم الإنشاء",
  "CRM & customer management": "إدارة العملاء",
  "Current Stock": "المخزون الحالي",
  "Current Stock:": "المخزون الحالي:",
  "Customer created successfully.": "تم إنشاء العميل بنجاح.",
  "Customer deleted successfully.": "تم حذف العميل بنجاح.",
  "Customer ID": "رقم العميل",
  Customers: "العملاء",
  "Customer updated successfully.": "تم تحديث العميل بنجاح.",
  Dashboard: "لوحة التحكم",
  Date: "التاريخ",
  "Date of Birth": "تاريخ الميلاد",
  "Define coupon validation and discount rules":
    "تحديد قواعد التحقق والخصم للقسائم",
  Delete: "حذف",
  "Delete Customer": "حذف العميل",
  "Delete Order": "حذف الطلب",
  Delivery: "توصيل",
  Department: "القسم",
  Details: "التفاصيل",
  "Dine-in": "داخل المطعم",
  "Dine In": "داخل المطعم",
  Discount: "الخصم",
  "Discount Amount": "قيمة الخصم",
  "Discount Type": "نوع الخصم",
  "Discount Value": "قيمة الخصم",
  Documentation: "التوثيق",
  Drinks: "مشروبات",
  Edit: "تعديل",
  "Edit Attendance": "تعديل الحضور",
  "Edit Coupon": "تعديل القسيمة",
  "Edit Employee": "تعديل الموظف",
  "Edit Ingredient": "تعديل المكون",
  "Edit Offer": "تعديل العرض",
  "Edit Profile": "تعديل الملف الشخصي",
  "Edit Supplier": "تعديل المورد",
  "Edit Table": "تعديل الطاولة",
  "Edit Category": "تعديل التصنيف",
  Email: "البريد الإلكتروني",
  "Email:": "البريد الإلكتروني:",
  Employee: "الموظف",
  "Employee Code": "كود الموظف",
  "Employee created successfully.": "تم إنشاء الموظف بنجاح.",
  "Employee deduction added successfully.": "تم إضافة خصم الموظف بنجاح.",
  "Employee deductions and product charges in this period are added automatically.":
    "خصومات الموظف وتحميلات المنتجات خلال هذه الفترة تضاف تلقائيا.",
  "Employee deductions and product charges are disabled until the latest migrations are applied.":
    "خصومات الموظفين وتحميلات المنتجات غير مفعلة حتى تشغيل آخر مايجريشن.",
  "Employee deleted successfully.": "تم حذف الموظف بنجاح.",
  "Employee salary transaction deleted successfully.":
    "تم حذف معاملة راتب الموظف بنجاح.",
  "Employee salary transaction updated successfully.":
    "تم تحديث معاملة راتب الموظف بنجاح.",
  "Employee Profile": "ملف الموظف",
  Employees: "الموظفون",
  "Employees & HR": "الموظفون والموارد البشرية",
  "Employee updated successfully.": "تم تحديث الموظف بنجاح.",
  "Employment Type": "نوع التوظيف",
  "Ends At": "ينتهي في",
  "Enter coupon code": "أدخل كود القسيمة",
  Export: "تصدير",
  Filter: "تصفية",
  "First Name": "الاسم الأول",
  Fixed: "ثابت",
  "Full Name": "الاسم الكامل",
  Gender: "النوع",
  "Generate payroll for a selected period": "إصدار الرواتب لفترة محددة",
  "Gross Amount": "الإجمالي",
  "Half Day": "نصف يوم",
  "Hire Date": "تاريخ التعيين",
  "Hourly Rate": "أجر الساعة",
  "hrs/day": "ساعة/يوم",
  "Load Product Price": "تحميل سعر منتج",
  Inactive: "غير نشط",
  Ingredient: "مكون",
  "Ingredient created successfully.": "تم إنشاء المكون بنجاح.",
  "Ingredient deleted successfully.": "تم حذف المكون بنجاح.",
  "Ingredients below minimum levels need replenishment.":
    "المكونات الأقل من حد إعادة الطلب تحتاج تموين.",
  "Ingredient updated successfully.": "تم تحديث المكون بنجاح.",
  "Initial Paid Amount": "المبلغ المدفوع مبدئيا",
  "Insufficient stock for ingredient": "مخزون غير كاف للمكون",
  Inventory: "المخزون",
  "Invalid login credentials.": "بيانات تسجيل الدخول غير صحيحة.",
  Item: "صنف",
  "Last Name": "اسم العائلة",
  Late: "متأخر",
  Leave: "إجازة",
  "Let's get started": "لنبدأ",
  "Live Ops": "تشغيل مباشر",
  "Live Subtotal:": "الإجمالي الفرعي المباشر:",
  "Login | Restaurant Management": "تسجيل الدخول | إدارة المطعم",
  Logout: "تسجيل الخروج",
  "Low Stock Alert": "تنبيه انخفاض المخزون",
  "Low Stock Items": "أصناف منخفضة المخزون",
  "Manage all orders": "إدارة كل الطلبات",
  "Manage coupon campaigns": "إدارة حملات القسائم",
  "Manage promotions": "إدارة العروض",
  Management: "الإدارة",
  "Manage Recipe": "إدارة الوصفة",
  "Manage suppliers and purchase requests": "إدارة الموردين وطلبات الشراء",
  "Manage table capacity and current availability":
    "إدارة سعة الطاولات وحالتها الحالية",
  "Manage product categories and hierarchy":
    "إدارة تصنيفات المنتجات والتدرج بينها",
  "Manual discount cannot be combined with a coupon code.":
    "لا يمكن الجمع بين الخصم اليدوي وكود القسيمة.",
  "Mark Payment": "تسجيل الدفع",
  "Max Discount": "أقصى خصم",
  "Max Discount Amount": "أقصى قيمة خصم",
  "Min Order": "أقل طلب",
  "Min Order Amount": "أقل قيمة طلب",
  Name: "الاسم",
  "National ID": "الرقم القومي",
  "Negative stock is automatically blocked": "يتم منع المخزون السالب تلقائيا",
  "Net Amount": "الصافي",
  "New Ingredient": "مكون جديد",
  "Here's what's happening at your restaurant today.":
    "إليك ما يحدث في مطعمك اليوم.",
  "+ New Table": "+ طاولة جديدة",
  "+ New Category": "+ تصنيف جديد",
  "New Order": "طلب جديد",
  "New Purchase": "شراء جديد",
  "New Purchase Transaction": "عملية شراء جديدة",
  "No attendance records found.": "لا توجد سجلات حضور.",
  "No attendance records yet.": "لا توجد سجلات حضور بعد.",
  "No coupons found.": "لا توجد قسائم.",
  "No customers found.": "لا يوجد عملاء.",
  "No employees found.": "لا يوجد موظفون.",
  "No ingredients found.": "لا توجد مكونات.",
  "No offers found.": "لا توجد عروض.",
  "No orders found.": "لا توجد طلبات.",
  "No products found.": "لا توجد منتجات.",
  "No purchases found.": "لا توجد مشتريات.",
  "No purchases yet.": "لا توجد مشتريات بعد.",
  "No redemptions yet.": "لا توجد استخدامات بعد.",
  "No salary records found.": "لا توجد سجلات رواتب.",
  "No salary adjustments yet.": "لا توجد تعديلات راتب بعد.",
  "No salary records yet.": "لا توجد سجلات رواتب بعد.",
  "No stock logs yet.": "لا توجد سجلات مخزون بعد.",
  "No tables found.": "لا توجد طاولات.",
  "No categories found.": "لا توجد تصنيفات.",
  "No categories available.": "لا توجد تصنيفات متاحة.",
  "No products match this filter.": "لا توجد منتجات مطابقة لهذا الفلتر.",
  "No suppliers found.": "لا يوجد موردون.",
  Note: "ملاحظة",
  "Note (optional)": "ملاحظة (اختياري)",
  Notes: "ملاحظات",
  "Offer created successfully.": "تم إنشاء العرض بنجاح.",
  "Offer deleted successfully.": "تم حذف العرض بنجاح.",
  "Offer Details": "تفاصيل العرض",
  Offers: "العروض",
  "Offers & Coupons": "العروض والقسائم",
  "Offer updated successfully.": "تم تحديث العرض بنجاح.",
  "One or more products are not available.": "منتج واحد أو أكثر غير متاح.",
  Operations: "العمليات",
  "Order #": "رقم الطلب",
  Today: "اليوم",
  "Order Actions": "إجراءات الطلب",
  "Order Date:": "تاريخ الطلب:",
  "Order deleted successfully.": "تم حذف الطلب بنجاح.",
  "Order does not meet coupon minimum amount.":
    "الطلب لا يحقق الحد الأدنى لاستخدام القسيمة.",
  Orders: "الطلبات",
  "Orders per Hour": "الطلبات لكل ساعة",
  "Order placed successfully.": "تم إنشاء الطلب بنجاح.",
  "Order status updated successfully.": "تم تحديث حالة الطلب بنجاح.",
  "Order Summary": "ملخص الطلب",
  "Order Type": "نوع الطلب",
  "Order Type:": "نوع الطلب:",
  "Orders Using Offer:": "الطلبات التي استخدمت العرض:",
  "Other Deduction": "استقطاع آخر",
  "Other Deduction (manual)": "استقطاع آخر (يدوي)",
  "Out for delivery": "خرج للتوصيل",
  Paid: "مدفوع",
  "Paid Amount": "المبلغ المدفوع",
  Partial: "جزئي",
  Password: "كلمة المرور",
  "Payment Date": "تاريخ الدفع",
  "Payroll and payment records": "سجلات الرواتب والمدفوعات",
  "Payroll Summary": "ملخص الرواتب",
  Pending: "قيد الانتظار",
  Percentage: "نسبة مئوية",
  Period: "الفترة",
  "Period End": "نهاية الفترة",
  "Period Start": "بداية الفترة",
  "Per User Limit": "الحد لكل مستخدم",
  Phone: "الهاتف",
  Pizza: "بيتزا",
  POS: "نقطة البيع",
  Position: "المنصب",
  "Product charge": "تحميل منتج",
  "Product charge added to employee successfully.":
    "تم تحميل سعر المنتج على الموظف بنجاح.",
  "Quick Attendance": "تسجيل حضور سريع",
  Present: "حاضر",
  Print: "طباعة",
  "Print Invoice": "طباعة الفاتورة",
  Priority: "الأولوية",
  "Priority (lower = higher)": "الأولوية (الأقل أعلى)",
  "Processed By": "تمت المعالجة بواسطة",
  "Processing...": "جارٍ المعالجة...",
  "Product to ingredient mappings": "ربط المنتجات بالمكونات",
  Profile: "الملف الشخصي",
  "Profit Trend": "اتجاه الربح",
  "Purchase #": "رقم الشراء",
  "Purchase Date": "تاريخ الشراء",
  "Purchase number": "رقم الشراء",
  "Purchase Summary": "ملخص الشراء",
  "Purchase transaction created successfully.": "تم إنشاء عملية الشراء بنجاح.",
  Purchases: "المشتريات",
  Qty: "الكمية",
  "Qty Required": "الكمية المطلوبة",
  Quantity: "الكمية",
  "Reason for adjustment": "سبب التسوية",
  "Recent Attendance": "آخر الحضور",
  "Recent inventory movements": "آخر حركات المخزون",
  "Recent Orders": "آخر الطلبات",
  "Recent Purchases": "آخر المشتريات",
  "Recent Redemptions": "آخر الاستخدامات",
  "Recent Salary Records": "آخر سجلات الرواتب",
  "Recipe updated successfully.": "تم تحديث الوصفة بنجاح.",
  Recipes: "الوصفات",
  "Record Attendance": "تسجيل الحضور",
  "Recent Salary Adjustments": "آخر تعديلات الراتب",
  "Register Employee": "تسجيل موظف",
  "+ Register Employee": "+ تسجيل موظف",
  Reason: "السبب",
  "Reason for deduction": "سبب الخصم",
  "Remaining:": "المتبقي:",
  Remove: "إزالة",
  "Reorder Level": "حد إعادة الطلب",
  Reports: "التقارير",
  "Reports & Analytics": "التقارير والتحليلات",
  Reset: "إعادة تعيين",
  "Restaurant Invoice": "فاتورة المطعم",
  "Restaurant Management": "إدارة المطعم",
  RestoHub: "ريستو هَب",
  "Revenue Today": "إيرادات اليوم",
  "Revenue vs Expenses": "الإيرادات مقابل المصروفات",
  Roles: "الأدوار",
  Salads: "سلطات",
  Salaries: "الرواتب",
  "Salary payment record created successfully.": "تم إنشاء سجل دفع راتب بنجاح.",
  "Salary payment updated successfully.": "تم تحديث دفع الراتب بنجاح.",
  "Salary Deductions": "خصومات الراتب",
  "Please run latest migration first": "يرجى تشغيل آخر مايجريشن أولا",
  "Salary Record": "سجل راتب",
  "Salary Tracking": "متابعة الرواتب",
  "Sales by Category": "المبيعات حسب التصنيف",
  "Sales Trend": "اتجاه المبيعات",
  "Sales (EGP)": "المبيعات (ج.م.)",
  "Revenue (EGP)": "الإيراد (ج.م.)",
  Profit: "الربح",
  "Save Attendance": "حفظ الحضور",
  "Save Changes": "حفظ التعديلات",
  "Save Coupon": "حفظ القسيمة",
  "Save Customer": "حفظ العميل",
  "Save Employee": "حفظ الموظف",
  "Save Offer": "حفظ العرض",
  "Save Purchase Transaction": "حفظ عملية الشراء",
  "Save Recipe": "حفظ الوصفة",
  "Save Table": "حفظ الطاولة",
  "Save Category": "حفظ التصنيف",
  "Save Status": "حفظ الحالة",
  "Save Supplier": "حفظ المورد",
  Search: "بحث",
  "Search code/name/contact": "ابحث بالكود أو الاسم أو التواصل",
  "Search name/phone/national ID": "ابحث بالاسم أو الهاتف أو الرقم القومي",
  "Search customers...": "ابحث عن عميل...",
  "Search name/code": "ابحث بالاسم أو الكود",
  "Search orders...": "ابحث عن طلب...",
  "Search orders, products...": "ابحث في الطلبات والمنتجات...",
  "Search products...": "ابحث عن منتج...",
  "Search suppliers...": "ابحث عن مورد...",
  Select: "اختر",
  "Select Employee": "اختر موظف",
  "Select Product": "اختر منتج",
  "Select ingredient": "اختر مكون",
  "Select supplier": "اختر مورد",
  "Send to Kitchen": "إرسال إلى المطبخ",
  "Set Exact Value": "تحديد قيمة دقيقة",
  "Set Available": "جعلها متاحة",
  "Sign in to your account": "سجل الدخول إلى حسابك",
  "Stackable with Coupon": "يمكن دمجه مع القسيمة",
  "Staff management & attendance": "إدارة الموظفين والحضور",
  "Shift / Schedule": "الشيفت / الجدول",
  "Shift Start": "موعد الحضور",
  "Shift End": "موعد الانصراف",
  "Check in / check out from this page": "تسجيل الحضور والانصراف من نفس الصفحة",
  "Charge product value to employee salary":
    "تحميل قيمة المنتج على راتب الموظف",
  "Comment (optional)": "تعليق (اختياري)",
  "Apply Deduction": "تطبيق الخصم",
  "Apply a manual deduction with reason": "تسجيل خصم يدوي مع السبب",
  "Starts At": "يبدأ في",
  Status: "الحالة",
  "Status:": "الحالة:",
  "Stock adjusted successfully.": "تمت تسوية المخزون بنجاح.",
  "Stock amount is changed using Adjust Stock.":
    "يتم تغيير كمية المخزون من خلال تسوية المخزون.",
  "Stock Adjustment Logs": "سجلات تسوية المخزون",
  "Stock In (+)": "إضافة مخزون (+)",
  "Stock-in inventory through supplier purchases":
    "تموين المخزون من خلال مشتريات الموردين",
  "Stock Out (-)": "سحب مخزون (-)",
  Subtotal: "الإجمالي الفرعي",
  Supplier: "المورد",
  "Supplier created successfully.": "تم إنشاء المورد بنجاح.",
  "Supplier deleted successfully.": "تم حذف المورد بنجاح.",
  "Supplier Name": "اسم المورد",
  "Supplier Profile": "ملف المورد",
  Suppliers: "الموردون",
  "Suppliers & Purchases": "الموردون والمشتريات",
  "Supplier updated successfully.": "تم تحديث المورد بنجاح.",
  "Table created successfully.": "تم إنشاء الطاولة بنجاح.",
  "Table updated successfully.": "تم تحديث الطاولة بنجاح.",
  "Table deleted successfully.": "تم حذف الطاولة بنجاح.",
  "Table status updated successfully.": "تم تحديث حالة الطاولة بنجاح.",
  "Category created successfully.": "تم إنشاء التصنيف بنجاح.",
  "Category updated successfully.": "تم تحديث التصنيف بنجاح.",
  "Category deleted successfully.": "تم حذف التصنيف بنجاح.",
  "Cannot delete category with linked subcategories.":
    "لا يمكن حذف تصنيف مرتبط بتصنيفات فرعية.",
  "A category cannot be its own parent.": "لا يمكن أن يكون التصنيف أبًا لنفسه.",
  "Search tables...": "ابحث عن الطاولات...",
  "Search categories...": "ابحث عن التصنيفات...",
  Reserve: "حجز",
  "Category Name": "اسم التصنيف",
  "Category Type": "نوع التصنيف",
  "Main Category": "تصنيف رئيسي",
  "Main Categories": "التصنيفات الرئيسية",
  "Sub Category": "تصنيف فرعي",
  "Sub Categories": "التصنيفات الفرعية",
  "Parent Category": "التصنيف الأب",
  "No parent": "بدون أب",
  "Select a parent only when the category type is Sub Category.":
    "اختر تصنيفًا أب فقط عند اختيار النوع تصنيف فرعي.",
  "Parent category is optional for Sub Category.":
    "اختيار التصنيف الأب اختياري عند اختيار النوع تصنيف فرعي.",
  "Table Name": "اسم الطاولة",
  "Capacity (seats)": "السعة (عدد الأفراد)",
  Takeaway: "استلام خارجي",
  Tax: "الضريبة",
  "Tax Amount": "قيمة الضريبة",
  Terminated: "منتهي الخدمة",
  Theme: "المظهر",
  "Toggle Theme": "تبديل المظهر",
  "This salary record is fully paid.": "هذا السجل مدفوع بالكامل.",
  "Top Products": "أعلى المنتجات",
  Total: "الإجمالي",
  "Total Expenses": "إجمالي المصروفات",
  "Total Orders": "إجمالي الطلبات",
  "Total Revenue": "إجمالي الإيرادات",
  "Track attendance records": "متابعة سجلات الحضور",
  "Track employee attendance status": "متابعة حالة حضور الموظفين",
  "Track ingredients & stock levels": "متابعة المكونات ومستويات المخزون",
  "Transaction history": "سجل المعاملات",
  Type: "النوع",
  Unit: "الوحدة",
  "Unit Cost": "تكلفة الوحدة",
  "Unit (e.g. kg, g, l, pcs)": "الوحدة (مثل كجم، جم، لتر، قطعة)",
  "Unit Price": "سعر الوحدة",
  Unpaid: "غير مدفوع",
  "Monthly Salary": "الراتب الشهري",
  "Net Monthly Salary": "صافي الراتب الشهري",
  "Net Salary": "صافي الراتب",
  "Daily Salary (Auto)": "الراتب اليومي (تلقائي)",
  "Work Hours Per Day": "عدد ساعات العمل يوميا",
  "Attendance Days Per Week": "أيام الحضور في الأسبوع",
  "Calculated from monthly salary and attendance days.":
    "يتم حسابه من الراتب الشهري وأيام الحضور.",
  "Current month deductions:": "خصومات الشهر الحالي:",
  "Current month deductions": "خصومات الشهر الحالي",
  "Current Month Deductions": "خصومات الشهر الحالي",
  Deductions: "الخصومات",
  "Edit Salary Transaction": "تعديل معاملة الراتب",
  "Delete this transaction?": "هل تريد حذف هذه المعاملة؟",
  "Manual deduction": "خصم يدوي",
  "Optional note": "ملاحظة اختيارية",
  "days/week": "يوم/أسبوع",
  "Update Attendance": "تحديث الحضور",
  "Update attendance entry": "تحديث سجل الحضور",
  "Update Coupon": "تحديث القسيمة",
  "Update coupon rules": "تحديث قواعد القسيمة",
  "Update Customer": "تحديث العميل",
  "Update customer information": "تحديث بيانات العميل",
  Updated: "تم التحديث",
  "Update Employee": "تحديث الموظف",
  "Update employee information": "تحديث بيانات الموظف",
  "Update Ingredient": "تحديث المكون",
  "Update ingredient settings": "تحديث إعدادات المكون",
  "Update Offer": "تحديث العرض",
  "Update offer conditions": "تحديث شروط العرض",
  "Update Status": "تحديث الحالة",
  "Update Supplier": "تحديث المورد",
  "Update supplier information": "تحديث بيانات المورد",
  "Usage Limit": "حد الاستخدام",
  "Usage Snapshot": "ملخص الاستخدام",
  "Usage Summary": "إجمالي الاستخدام",
  "Used Count:": "عدد الاستخدام:",
  View: "عرض",
  "View changelog": "عرض سجل التغييرات",
  "View Supplier": "عرض المورد",
  "Welcome back! Here's what's happening today.":
    "مرحبا بعودتك! هذا ما يحدث اليوم.",
  "With so many options available to you,": "مع كل هذه الخيارات المتاحة،",
  "we suggest you start with the following:": "نقترح أن تبدأ بما يلي:",
  "You do not have permission to access this resource.":
    "لا تملك صلاحية الوصول إلى هذا المورد.",
  "Read the": "اقرأ",
  "Watch video tutorials at": "شاهد الدروس على",
  Laracasts: "لاراكاستس",
  "Deploy now": "انشر الآن",
  Register: "تسجيل",
  "Log in": "تسجيل الدخول",
  "Unable to place order.": "تعذر إنشاء الطلب.",
  "Order {number} placed successfully.": "تم إنشاء الطلب رقم {number} بنجاح.",
  "Order {number} placed ({tags}).": "تم إنشاء الطلب رقم {number} ({tags}).",
  "Switch to English": "التبديل إلى الإنجليزية",
  "Switch language": "تبديل اللغة",
  "Order Summary": "ملخص الطلب",
  "Dine In": "داخل المطعم",
  delivery: "توصيل",
  takeaway: "استلام خارجي",
  dine_in: "داخل المطعم",
  full_time: "دوام كامل",
  part_time: "دوام جزئي",
  contract: "تعاقد",
  unpaid: "غير مدفوع",
  paid: "مدفوع",
  cancelled: "ملغي",
  active: "نشط",
  inactive: "غير نشط",
  preparing: "قيد التحضير",
  completed: "مكتمل",
  present: "حاضر",
  absent: "غائب",
  half_day: "نصف يوم",
  leave: "إجازة",
  late: "متأخر",
  terminated: "منتهي الخدمة",
  fixed: "ثابت",
  percentage: "نسبة مئوية",
};

const AR_TRANSLATIONS = Object.freeze(
  Object.entries(RAW_AR_TRANSLATIONS).reduce((carry, [key, value]) => {
    carry[toLookupKey(String(key))] = value;
    return carry;
  }, {}),
);

const EN_TRANSLATIONS = Object.freeze(
  Object.entries(RAW_AR_TRANSLATIONS).reduce((carry, [en, ar]) => {
    carry[toLookupKey(String(ar))] = en;
    return carry;
  }, {}),
);

const WORD_TRANSLATIONS = Object.freeze({
  add: "إضافة",
  edit: "تعديل",
  update: "تحديث",
  delete: "حذف",
  save: "حفظ",
  create: "إنشاء",
  view: "عرض",
  orders: "الطلبات",
  order: "الطلب",
  products: "المنتجات",
  product: "منتج",
  suppliers: "الموردون",
  supplier: "مورد",
  tables: "الطاولات",
  table: "طاولة",
  categories: "التصنيفات",
  category: "تصنيف",
  customers: "العملاء",
  customer: "عميل",
  employees: "الموظفون",
  employee: "موظف",
  attendance: "الحضور",
  inventory: "المخزون",
  recipes: "الوصفات",
  reports: "التقارير",
  salaries: "الرواتب",
  offers: "العروض",
  coupons: "القسائم",
  search: "بحث",
  status: "الحالة",
  total: "الإجمالي",
  amount: "المبلغ",
  date: "التاريخ",
  type: "النوع",
  name: "الاسم",
  code: "الكود",
  discount: "الخصم",
  tax: "الضريبة",
  subtotal: "الإجمالي الفرعي",
  quantity: "الكمية",
  unit: "الوحدة",
  price: "السعر",
  cost: "التكلفة",
  payment: "الدفع",
  profile: "الملف",
  note: "ملاحظة",
  notes: "ملاحظات",
  active: "نشط",
  inactive: "غير نشط",
  available: "متاحة",
  occupied: "محجوزة",
  pending: "قيد الانتظار",
  paid: "مدفوع",
  cancelled: "ملغي",
  completed: "مكتمل",
  processing: "جارٍ المعالجة",
});

const REVERSE_WORD_TRANSLATIONS = Object.freeze(
  Object.entries(WORD_TRANSLATIONS).reduce((carry, [en, ar]) => {
    carry[toLookupKey(ar)] = en;
    return carry;
  }, {}),
);

const escapeRegExp = (value) => value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

const compileTemplatePattern = (sourceTemplate, targetTemplate) => {
  const tokenRegex = /:([a-zA-Z_][a-zA-Z0-9_]*)/g;
  const tokens = [];
  let pattern = "^";
  let lastIndex = 0;
  let match = null;

  while ((match = tokenRegex.exec(sourceTemplate)) !== null) {
    pattern += escapeRegExp(sourceTemplate.slice(lastIndex, match.index));
    pattern += "(.+?)";
    tokens.push(match[1]);
    lastIndex = match.index + match[0].length;
  }

  if (!tokens.length) {
    return null;
  }

  pattern += escapeRegExp(sourceTemplate.slice(lastIndex));
  pattern += "$";

  return {
    regex: new RegExp(pattern, "i"),
    tokens,
    targetTemplate,
  };
};

const applyTemplateTokens = (template, tokens, captures) => {
  let output = template;

  tokens.forEach((token, index) => {
    const replacement = captures[index] ?? "";
    output = output.replace(new RegExp(`:${token}\\b`, "g"), replacement);
  });

  return output;
};

const resolveDynamicPattern = (normalizedValue, patterns) => {
  for (const pattern of patterns) {
    const match = normalizedValue.match(pattern.regex);

    if (!match) {
      continue;
    }

    return applyTemplateTokens(
      pattern.targetTemplate,
      pattern.tokens,
      match.slice(1),
    );
  }

  return null;
};

const resolveDynamicTranslation = (normalizedValue, language) => {
  if (!dynamicTranslationsLoaded || !normalizedValue) {
    return null;
  }

  const lookup = toLookupKey(normalizedValue);

  if (language === ARABIC_LANGUAGE) {
    const exact = dynamicTranslationMaps.enToArExact[lookup];

    if (exact) {
      return exact;
    }

    return resolveDynamicPattern(
      normalizedValue,
      dynamicTranslationMaps.enToArPatterns,
    );
  }

  const exact = dynamicTranslationMaps.arToEnExact[lookup];

  if (exact) {
    return exact;
  }

  return resolveDynamicPattern(
    normalizedValue,
    dynamicTranslationMaps.arToEnPatterns,
  );
};

const registerDynamicPair = (source, target, sourceLanguage) => {
  const sourceNormalized = normalizeKey(source);
  const targetNormalized = normalizeKey(target);

  if (!sourceNormalized || !targetNormalized) {
    return;
  }

  const sourceLookup = toLookupKey(sourceNormalized);

  if (sourceLanguage === DEFAULT_LANGUAGE) {
    dynamicTranslationMaps.enToArExact[sourceLookup] = targetNormalized;

    const compiled = compileTemplatePattern(sourceNormalized, targetNormalized);

    if (compiled) {
      dynamicTranslationMaps.enToArPatterns.push(compiled);
    }

    return;
  }

  dynamicTranslationMaps.arToEnExact[sourceLookup] = targetNormalized;

  const compiled = compileTemplatePattern(sourceNormalized, targetNormalized);

  if (compiled) {
    dynamicTranslationMaps.arToEnPatterns.push(compiled);
  }
};

const loadDynamicTranslations = async () => {
  if (dynamicTranslationsLoaded) {
    return;
  }

  if (dynamicTranslationsPromise) {
    await dynamicTranslationsPromise;
    return;
  }

  dynamicTranslationsPromise = (async () => {
    try {
      const response = await fetch(LOCALE_TRANSLATIONS_ENDPOINT, {
        method: "GET",
        headers: {
          Accept: "application/json",
        },
        credentials: "same-origin",
      });

      if (!response.ok) {
        return;
      }

      const payload = await response.json();
      const pairs = Array.isArray(payload?.pairs) ? payload.pairs : [];

      pairs.forEach((pair) => {
        const en = typeof pair?.en === "string" ? pair.en : "";
        const ar = typeof pair?.ar === "string" ? pair.ar : "";

        if (!en || !ar) {
          return;
        }

        registerDynamicPair(en, ar, DEFAULT_LANGUAGE);
        registerDynamicPair(ar, en, ARABIC_LANGUAGE);
      });

      dynamicTranslationsLoaded = true;
    } catch {
      // Falls back to the built-in map when endpoint is unavailable.
    } finally {
      dynamicTranslationsPromise = null;
    }
  })();

  await dynamicTranslationsPromise;
};

const preserveWhitespace = (original, translated) => {
  const leading = original.match(/^\s*/)?.[0] ?? "";
  const trailing = original.match(/\s*$/)?.[0] ?? "";

  return `${leading}${translated}${trailing}`;
};

const normalizeCurrencyToken = (value, language = activeLanguage) => {
  if (typeof value !== "string" || !value) {
    return value;
  }

  if (language === ARABIC_LANGUAGE) {
    return value.replace(
      /(^|[^A-Za-z0-9])EGP(?=[^A-Za-z0-9]|$)/gi,
      (_match, prefix) => `${prefix}ج.م.`,
    );
  }

  return value.replace(
    /(^|[^\p{L}\p{N}])ج\s*\.?\s*م\s*\.?(?=[^\p{L}\p{N}]|$)/gu,
    (_match, prefix) => `${prefix}EGP`,
  );
};

const containsLatin = (value) => /[A-Za-z]/.test(value);

const shouldSkipText = (value) => {
  if (!value) {
    return true;
  }

  if (value.length > 220) {
    return true;
  }

  if (/^[\d\s.,:+\-/%()#]+$/.test(value)) {
    return true;
  }

  if (/[{}<>@]/.test(value)) {
    return true;
  }

  if (/https?:\/\//i.test(value)) {
    return true;
  }

  if (/^[A-Za-z0-9_.-]+\.[A-Za-z]{2,}$/.test(value)) {
    return true;
  }

  return false;
};

const translateByPattern = (value) => {
  const orderWithTags = /^Order\s+(.+)\s+placed\s+\((.+)\)\.$/i.exec(value);

  if (orderWithTags) {
    return `تم إنشاء الطلب رقم ${orderWithTags[1]} (${orderWithTags[2]}).`;
  }

  const orderSuccess = /^Order\s+(.+)\s+placed successfully\.$/i.exec(value);

  if (orderSuccess) {
    return `تم إنشاء الطلب رقم ${orderSuccess[1]} بنجاح.`;
  }

  const invoiceMatch = /^Invoice\s+(.+)$/i.exec(value);

  if (invoiceMatch) {
    return `فاتورة ${invoiceMatch[1]}`;
  }

  const backMatch = /^←\s*Back to\s+(.+)$/i.exec(value);

  if (backMatch) {
    return `← العودة إلى ${translateText(backMatch[1], ARABIC_LANGUAGE)}`;
  }

  const versusWeek = /^(.+)\s+vs last week$/i.exec(value);

  if (versusWeek) {
    return `${versusWeek[1]} مقارنة بالأسبوع الماضي`;
  }

  const versusPeriod = /^([+-]?\d+%?)\s+vs last period$/i.exec(value);

  if (versusPeriod) {
    return `${versusPeriod[1]} مقارنة بالفترة السابقة`;
  }

  return null;
};

const translateWordByWord = (value) =>
  value.replace(/[A-Za-z_]+/g, (word) => {
    const lookup = WORD_TRANSLATIONS[word.toLowerCase()];

    return lookup ?? word;
  });

const translateWordByWordToEnglish = (value) => {
  const normalized = normalizeKey(value);
  const exact = REVERSE_WORD_TRANSLATIONS[toLookupKey(normalized)];

  if (exact) {
    return preserveWhitespace(value, exact);
  }

  return value;
};

export const translateText = (value, language = activeLanguage) => {
  if (typeof value !== "string") {
    return value;
  }

  const normalized = normalizeKey(value);

  if (!normalized) {
    return normalizeCurrencyToken(value, language);
  }

  const dynamicTranslation = resolveDynamicTranslation(normalized, language);

  if (dynamicTranslation) {
    return normalizeCurrencyToken(
      preserveWhitespace(value, dynamicTranslation),
      language,
    );
  }

  if (language !== ARABIC_LANGUAGE) {
    const reverseExact = EN_TRANSLATIONS[toLookupKey(normalized)];

    if (reverseExact) {
      return normalizeCurrencyToken(
        preserveWhitespace(value, reverseExact),
        language,
      );
    }

    if (containsArabic(normalized)) {
      const reverseWord = translateWordByWordToEnglish(normalized);

      if (reverseWord !== normalized) {
        return normalizeCurrencyToken(
          preserveWhitespace(value, reverseWord),
          language,
        );
      }
    }

    return normalizeCurrencyToken(value, language);
  }

  if (!normalized || shouldSkipText(normalized) || !containsLatin(normalized)) {
    return normalizeCurrencyToken(value, language);
  }

  const exact = AR_TRANSLATIONS[toLookupKey(normalized)];

  if (exact) {
    return normalizeCurrencyToken(preserveWhitespace(value, exact), language);
  }

  const pattern = translateByPattern(normalized);

  if (pattern) {
    return normalizeCurrencyToken(preserveWhitespace(value, pattern), language);
  }

  const wordByWord = translateWordByWord(normalized);

  if (wordByWord !== normalized) {
    return normalizeCurrencyToken(
      preserveWhitespace(value, wordByWord),
      language,
    );
  }

  return normalizeCurrencyToken(value, language);
};

export const t = (key, replacements = {}, language = activeLanguage) => {
  const translated = translateText(String(key), language);

  return translated.replace(/\{([a-zA-Z0-9_]+)\}/g, (match, token) => {
    if (!(token in replacements)) {
      return match;
    }

    return String(replacements[token]);
  });
};

const registerTextNode = (node) => {
  if (!(node instanceof Text) || trackedTextNodeSet.has(node)) {
    return;
  }

  if (!node.parentElement) {
    return;
  }

  const blockedTags = new Set([
    "SCRIPT",
    "STYLE",
    "NOSCRIPT",
    "CODE",
    "PRE",
    "TITLE",
    "SVG",
    "PATH",
  ]);

  if (blockedTags.has(node.parentElement.tagName)) {
    return;
  }

  const content = node.nodeValue ?? "";

  if (!normalizeKey(content)) {
    return;
  }

  node.__originalI18nText = content;
  trackedTextNodeSet.add(node);
  trackedTextNodes.push(node);
};

const registerTextNodesInTree = (rootNode) => {
  if (!rootNode) {
    return;
  }

  if (rootNode instanceof Text) {
    registerTextNode(rootNode);
    return;
  }

  const walker = document.createTreeWalker(rootNode, NodeFilter.SHOW_TEXT);

  while (walker.nextNode()) {
    registerTextNode(walker.currentNode);
  }
};

const registerElementAttributes = (element) => {
  if (!(element instanceof Element)) {
    return;
  }

  let attributes = elementOriginalAttributes.get(element);

  if (!attributes) {
    attributes = {};
    elementOriginalAttributes.set(element, attributes);
    trackedElements.add(element);
  }

  TRANSLATABLE_ATTRIBUTES.forEach((attribute) => {
    if (!element.hasAttribute(attribute)) {
      return;
    }

    if (!(attribute in attributes) || !isApplyingTranslations) {
      attributes[attribute] = element.getAttribute(attribute) ?? "";
    }
  });
};

const registerAttributesInTree = (rootNode) => {
  if (!rootNode || !(rootNode instanceof Element)) {
    return;
  }

  registerElementAttributes(rootNode);

  const selector = TRANSLATABLE_ATTRIBUTES.map(
    (attribute) => `[${attribute}]`,
  ).join(",");

  rootNode.querySelectorAll(selector).forEach((element) => {
    registerElementAttributes(element);
  });
};

const applyTextTranslations = (language) => {
  trackedTextNodes.forEach((node) => {
    if (!(node instanceof Text) || !node.isConnected) {
      return;
    }

    const original =
      typeof node.__originalI18nText === "string"
        ? node.__originalI18nText
        : (node.nodeValue ?? "");

    node.nodeValue = translateText(original, language);
  });
};

const applyAttributeTranslations = (language) => {
  trackedElements.forEach((element) => {
    if (!(element instanceof Element) || !element.isConnected) {
      return;
    }

    const originalAttributes = elementOriginalAttributes.get(element);

    if (!originalAttributes) {
      return;
    }

    TRANSLATABLE_ATTRIBUTES.forEach((attribute) => {
      if (!(attribute in originalAttributes)) {
        return;
      }

      const originalValue = originalAttributes[attribute];

      const translatedValue = translateText(originalValue, language);

      element.setAttribute(attribute, translatedValue);
    });
  });
};

const updateLanguageButtons = (language) => {
  const isArabic = language === ARABIC_LANGUAGE;

  document.querySelectorAll("[data-language-toggle]").forEach((button) => {
    if (button.dataset.languageBound !== "true") {
      button.addEventListener("click", () => {
        toggleLanguage();
      });
      button.dataset.languageBound = "true";
    }

    button.textContent = isArabic ? "EN" : "AR";
    button.setAttribute(
      "aria-label",
      isArabic ? "Switch to English" : "التبديل إلى العربية",
    );
    button.setAttribute(
      "title",
      isArabic ? "Switch to English" : "التبديل إلى العربية",
    );
  });
};

const broadcastLanguageChange = () => {
  window.dispatchEvent(
    new CustomEvent("app-language-change", {
      detail: { language: activeLanguage },
    }),
  );
};

const resumeLocalizationObserver = () => {
  if (!localizationObserver || !document.body) {
    return;
  }

  localizationObserver.observe(document.body, OBSERVER_OPTIONS);
};

const scheduleArabicRefresh = () => {
  if (activeLanguage !== ARABIC_LANGUAGE || arabicRefreshScheduled) {
    return;
  }

  arabicRefreshScheduled = true;

  requestAnimationFrame(() => {
    arabicRefreshScheduled = false;
    applyLanguage(activeLanguage, false);
  });
};

const applyLanguage = (language, persist = true) => {
  activeLanguage =
    language === ARABIC_LANGUAGE ? ARABIC_LANGUAGE : DEFAULT_LANGUAGE;

  if (localizationObserver) {
    localizationObserver.disconnect();
  }

  const isArabic = activeLanguage === ARABIC_LANGUAGE;

  root.setAttribute("lang", isArabic ? ARABIC_LANGUAGE : DEFAULT_LANGUAGE);
  root.setAttribute("dir", isArabic ? "rtl" : "ltr");

  if (document.body) {
    document.body.classList.toggle("lang-ar", isArabic);
  }

  if (!originalDocumentTitle) {
    originalDocumentTitle = document.title;
  }

  isApplyingTranslations = true;
  applyTextTranslations(activeLanguage);
  applyAttributeTranslations(activeLanguage);
  document.title = translateText(originalDocumentTitle, activeLanguage);
  isApplyingTranslations = false;

  updateLanguageButtons(activeLanguage);

  localStorage.setItem(LANGUAGE_STORAGE_KEY, activeLanguage);

  if (persist) {
    void syncServerLanguage(activeLanguage);
  }

  resumeLocalizationObserver();
  broadcastLanguageChange();
};

const initializeObserver = () => {
  if (localizationObserver) {
    return;
  }

  localizationObserver = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === "childList") {
        mutation.addedNodes.forEach((node) => {
          registerTextNodesInTree(node);
          registerAttributesInTree(node);
        });

        return;
      }

      if (mutation.type === "characterData") {
        if (isApplyingTranslations) {
          return;
        }

        const node = mutation.target;

        if (!(node instanceof Text)) {
          return;
        }

        registerTextNode(node);
        node.__originalI18nText = node.nodeValue ?? "";
        return;
      }

      if (mutation.type === "attributes") {
        if (isApplyingTranslations) {
          return;
        }

        const element = mutation.target;

        if (!(element instanceof Element)) {
          return;
        }

        registerElementAttributes(element);
      }
    });

    scheduleArabicRefresh();
  });

  resumeLocalizationObserver();
};

export const getActiveLanguage = () => activeLanguage;

export const toggleLanguage = () => {
  const nextLanguage =
    activeLanguage === ARABIC_LANGUAGE ? DEFAULT_LANGUAGE : ARABIC_LANGUAGE;

  applyLanguage(nextLanguage, true);

  void loadDynamicTranslations().then(() => {
    if (activeLanguage === nextLanguage) {
      forceLocalizationRefresh();
    }
  });
};

export const forceLocalizationRefresh = () => {
  applyLanguage(activeLanguage, false);
};

export const initializeLocalization = () => {
  if (!document.body) {
    return;
  }

  activeLanguage = getServerLanguage();
  originalDocumentTitle = document.title;

  registerTextNodesInTree(document.body);
  registerAttributesInTree(document.body);
  updateLanguageButtons(activeLanguage);
  initializeObserver();

  applyLanguage(activeLanguage, false);

  void loadDynamicTranslations().then(() => {
    forceLocalizationRefresh();
  });
};
