# System UI/UX Documentation

## Table of Contents

- 1. Introduction (System Overview)

- 2. User Roles

- 3. Modules Overview

- 4. Detailed Page Catalog

- 5. User Flows

- 6. Special Systems

- 7. Export and Print Surfaces

- 8. Final Handoff Notes for UI/UX Designer

## 1. Introduction (System Overview)

This document is a complete as-built UI/UX product documentation for the current Restaurant OS web system.

The system is a role-based Laravel application for restaurant operations, sales, inventory, purchasing, HR, and reporting.

It supports bilingual interface (English + Arabic), with RTL/LTR behavior, and permission-based navigation. The goal of this document is to organize all current screens and behavior in a clean format ready for a UI/UX designer handoff.

Important scope note: this document only describes existing implementation. No new features, no logic changes.

---

## 2. User Roles

### Admin (الإدارة)

- Access Level: full.

- Can See/Do: all modules, all CRUD actions, approvals, reports, users and permissions management.

### Cashier (الكاشير)

- Access Level: front operations.

- Can See/Do: Dashboard, POS, Waiter, Orders, table-linked order actions, customer create/update.

### Accountant (المحاسب)

- Access Level: financial operations.

- Can See/Do: purchases viewing, supplier financial actions, salaries updates, reports.

### Warehouse Manager (مدير المخزن)

- Access Level: stock and procurement.

- Can See/Do: inventory management, recipes, suppliers, purchases create/update/approve, reports.

### Other Operational Users (permission-based)

- Access Level: based on assigned permissions.

- Can See/Do: waiter flow, kitchen/bar screens, selected reports, selected management pages.

---

## 3. Modules Overview

| Module | Main Objective | Key Pages |

|---|---|---|

| Authentication | User login/session entry | Login |

| Dashboard | Daily overview and KPIs | Dashboard |

| Sales Operations | Take and process orders | POS, Waiter, KDS, Bar, Orders |

| CRM & Catalog | Manage customers and menu structures | Customers, Categories, Products, Tables |

| Inventory & Recipes | Stock control and production formulas | Inventory, Recipes |

| Suppliers & Purchases | Procurement and approval workflow | Suppliers, Purchases |

| Employees & HR | Employee profile, attendance, payroll | Employees, Attendance, Salaries |

| Marketing | Promotion management | Offers, Coupons |

| Reports | Operational and financial insights | Reports, Shift Logs |

| User Administration | User, role, permission access | Users |

| Print/Export Surfaces | Printable and exportable views | Invoices, receipts, PDF/CSV/Excel exports |

---

## 4. Detailed Page Catalog

## 4.1 Authentication

### Login

- Page Name: Login

- Purpose: Authenticate users and start system session.

- UI Components: username/email input, password input, submit button, language toggle, theme toggle, hero panel.

- User Actions: sign in, switch language, switch theme.

- States (Loading / Empty / Error): Loading on submit button; Empty initial form; Error for invalid credentials and field validation.

---

## 4.2 Shared Experience

### App Shell (Global Layout)

- Page Name: Shared App Layout

- Purpose: Provide consistent navigation and global actions across authenticated pages.

- UI Components: sidebar groups (permission-based), top search bar, language toggle, theme toggle, notifications dropdown, logout button, flash toast area.

- User Actions: navigate modules, mark notifications read, mark all read, logout.

- States (Loading / Empty / Error): Loading on page transitions/actions; Empty notification list message; Error via toast/session messages.

---

## 4.3 Dashboard & Sales Operations

### Dashboard

- Page Name: Dashboard

- Purpose: Show operational KPIs and recent performance snapshot.

- UI Components: KPI cards, sales trend chart, orders distribution chart, recent orders table, top products table.

- User Actions: review KPIs and trends, navigate to relevant modules.

- States (Loading / Empty / Error): Loading while charts render; Empty not commonly shown because seeded stats exist; Error via global feedback.

### POS (Cashier)

- Page Name: POS

- Purpose: Main cashier interface for shift handling and order creation.

- UI Components: shift start/end panel, opening cash input, settlement modal (actual cash + tips), product search, category filters, product grid, order type tabs, table selector, active table order summary, transfer table controls, cart list, qty controls, item notes, discount/coupon section, order notes, totals block, submit button, delivery customer modal, print confirmation modal.

- User Actions: start shift, end shift, choose order type, select/transfer table, add/remove/update items, apply discount/coupon, assign delivery customer/employee, place order, print invoice.

- States (Loading / Empty / Error): Loading for start/end shift and order submit; Empty for no products in filter and empty cart; Error for validation, occupied table conflicts, shift restrictions, delivery data issues.

### Waiter

- Page Name: Waiter

- Purpose: Fast dine-in order entry by table for floor staff.

- UI Components: table selection grid, active order alert card, quick product search, category filters, product quick-add buttons, cart, qty controls, item notes, subtotal area, order notes, send button.

- User Actions: choose table, add/remove items, adjust quantities, add notes, send to kitchen.

- States (Loading / Empty / Error): Loading table fetch indicator; Empty cart and empty products text; Error/success inline messages.

### KDS

- Page Name: Kitchen Display System

- Purpose: Move kitchen tickets through pending/preparing/ready stages.

- UI Components: three stage columns, stage count badges, ticket cards, item notes and order notes, timer labels, transition buttons (start/back/done), polling updates, toast alerts.

- User Actions: transition ticket between stages.

- States (Loading / Empty / Error): Loading state on transition button; Empty stage placeholder; Error toast on invalid transition.

### Bar

- Page Name: Bar Display System

- Purpose: Same staged workflow as KDS for bar-related items.

- UI Components: same structure as KDS, bar-specific title and endpoints.

- User Actions: transition ticket states.

- States (Loading / Empty / Error): Same as KDS.

### Orders List

- Page Name: Orders

- Purpose: View and filter all orders with management actions.

- UI Components: search input, status filter, order type filter, date range filters, filter button, orders table, status badges, action buttons (view/print/delete), delete modal, pagination.

- User Actions: filter list, open details, print paid invoice, delete order.

- States (Loading / Empty / Error): Loading on filtering and pagination; Empty row for no results; Error on delete or invalid operations.

### Order Details

- Page Name: Order Details

- Purpose: Inspect one order and perform allowed edits.

- UI Components: order item table, qty plus/minus controls, delete item button, totals summary, order meta block, discount form, status actions, delete order action, print button for paid orders.

- User Actions: update item quantities, remove items, update discount/coupon, update order status, delete order, print invoice.

- States (Loading / Empty / Error): Loading during item update; Empty optional data placeholders; Error for locked statuses (paid/cancelled) and validation issues.

### Order Invoice

- Page Name: Order Invoice (Print)

- Purpose: Printable invoice for paid order.

- UI Components: invoice header, order and customer info, line items, totals.

- User Actions: print from browser.

- States (Loading / Empty / Error): Loading on render; Empty fallback values for optional fields; Error route blocked when order is not paid.

---

## 4.4 CRM & Catalog

### Customers List

- Page Name: Customers

- Purpose: Customer CRM list with summary metrics.

- UI Components: search box, export PDF button, add customer button, customers table, type badge, orders count, total spent, last order date, action buttons, pagination.

- User Actions: search, export, create, view profile, edit, delete.

- States (Loading / Empty / Error): Loading on filtering/pagination; Empty row for no customers; Error on validation/session.

### Customer Create

- Page Name: Create Customer

- Purpose: Add new customer profile.

- UI Components: name input, phone input, address input, notes textarea, customer type select, save button.

- User Actions: submit new customer.

- States (Loading / Empty / Error): Loading on submit; Empty initial form; Error field validation.

### Customer Profile

- Page Name: Customer Profile

- Purpose: Show customer details and order behavior.

- UI Components: KPI cards (orders, spent, favorite category), profile details card, account info card, latest orders table, edit/delete controls.

- User Actions: edit customer, delete customer, navigate to related orders.

- States (Loading / Empty / Error): Loading page data; Empty latest orders list; Error session feedback.

### Customer Edit

- Page Name: Edit Customer

- Purpose: Update existing customer.

- UI Components: prefilled customer form fields and update controls.

- User Actions: save updates, cancel.

- States (Loading / Empty / Error): Loading on submit; Empty optional fields; Error field validation.

### Categories List

- Page Name: Categories

- Purpose: Manage main/sub category hierarchy.

- UI Components: search input, type filter, categories table, type badges, parent label, subcategories count, edit/delete actions, pagination.

- User Actions: filter, create, edit, delete category.

- States (Loading / Empty / Error): Loading on filter/pagination; Empty no-results row; Error when deleting category with children.

### Category Create

- Page Name: Create Category

- Purpose: Add category (main or sub).

- UI Components: name field, type select, parent select, save button.

- User Actions: submit category.

- States (Loading / Empty / Error): Loading on submit; Empty initial form; Error validation.

### Category Edit

- Page Name: Edit Category

- Purpose: Update category data and relation.

- UI Components: prefilled category form, update button, cancel button.

- User Actions: save updates.

- States (Loading / Empty / Error): Loading on submit; Empty optional parent; Error validation.

### Products List

- Page Name: Products

- Purpose: Manage sellable product catalog.

- UI Components: search input, category filter, preparation station filter, products table, station badges, edit/delete actions, delete confirmation modal, pagination.

- User Actions: filter, create, edit, delete product.

- States (Loading / Empty / Error): Loading on filter and pagination; Empty no-results row; Error on delete constraints and validation.

### Product Create

- Page Name: Create Product

- Purpose: Add product to menu.

- UI Components: name, price, category, preparation station, description, save/cancel buttons.

- User Actions: submit product.

- States (Loading / Empty / Error): Loading on submit; Empty default form; Error validation.

### Product Edit

- Page Name: Edit Product

- Purpose: Update product details.

- UI Components: prefilled product form.

- User Actions: save updates, cancel.

- States (Loading / Empty / Error): Loading on submit; Empty optional description; Error validation.

### Tables List

- Page Name: Tables

- Purpose: Manage table list and availability/occupancy status.

- UI Components: search input, status filter, tables table, capacity label, status badge, toggle status button (reserve/set available), edit/delete actions, pagination.

- User Actions: filter, toggle status, create table, edit table, delete table.

- States (Loading / Empty / Error): Loading on actions; Empty no-results row; Error via validation/session.

### Table Create

- Page Name: Create Table

- Purpose: Register a new restaurant table.

- UI Components: name, capacity, status fields, save.

- User Actions: submit table.

- States (Loading / Empty / Error): Loading on submit; Empty initial form; Error validation.

### Table Edit

- Page Name: Edit Table

- Purpose: Update table data.

- UI Components: prefilled table form.

- User Actions: update table.

- States (Loading / Empty / Error): Loading on submit; Empty optional values; Error validation.

---

## 4.5 Inventory & Recipes

### Inventory Dashboard

- Page Name: Inventory

- Purpose: Central stock management by warehouse.

- UI Components: warehouse tabs, selected warehouse banner, KPI cards, low-stock alert, materials table, adjust/edit/delete actions, warehouse management accordion (create/update), transfer form, audit form, forecast section, stock logs table, export logs PDF action.

- User Actions: switch warehouse, create/edit/delete ingredient, adjust stock, transfer stock, run audit, create/update warehouses, export logs.

- States (Loading / Empty / Error): Loading on tab switch and submits; Empty rows for materials/logs/warehouse lists; Error validation and transactional errors.

### Inventory Create

- Page Name: Create Material

- Purpose: Add raw material/ingredient.

- UI Components: name, supplier, unit, default warehouse display, cost, opening quantity, threshold, additional settings (expiry date, alert days, status), submit button.

- User Actions: save new material.

- States (Loading / Empty / Error): Loading on submit; Empty default values; Error field validation.

### Inventory Edit

- Page Name: Edit Material

- Purpose: Update material configuration.

- UI Components: prefilled material fields, default warehouse selector, read-only current quantity, optional settings, submit button, adjust stock shortcut.

- User Actions: update material, navigate to adjust stock page.

- States (Loading / Empty / Error): Loading on submit; Empty optional settings; Error validation.

### Inventory Adjust

- Page Name: Adjust Stock

- Purpose: Apply stock in/out/set operation for one ingredient.

- UI Components: total quantity summary, warehouse stock table, warehouse select, adjustment type select, quantity input, note input, submit.

- User Actions: submit stock adjustment.

- States (Loading / Empty / Error): Loading on submit; Empty warehouse stock rows message; Error validation.

### Recipes Index

- Page Name: Recipes

- Purpose: Manage product recipes and semi-finished formulas.

- UI Components: recipes listing section, semi-finished listing section, create and edit actions, delete actions, export PDF.

- User Actions: open builder pages, edit, delete, export.

- States (Loading / Empty / Error): Loading on list navigation; Empty list sections; Error validation/session.

### Recipe Create Selector (Product)

- Page Name: Create Recipe (Step 1)

- Purpose: Select product and starter parameters before builder.

- UI Components: product dropdown, yield quantity input, waste percent input, loss percent input, open builder button.

- User Actions: select product and continue to recipe builder.

- States (Loading / Empty / Error): Loading during redirect; Empty state when all products already have recipes; Error required field warning.

### Recipe Builder (Product Edit)

- Page Name: Product Recipe Builder

- Purpose: Build/edit full product formula and cost.

- UI Components: product selector input, yield/waste/loss inputs, raw materials dynamic table, semi-finished components dynamic table, add/remove row buttons, live totals cards, notes field, save/cancel.

- User Actions: add/remove rows, edit quantities, calculate totals automatically, save recipe.

- States (Loading / Empty / Error): Loading on builder initialization and submit; Empty rows before adding lines; Error validation and consistency checks.

### Recipe Builder (Semi-finished Create)

- Page Name: Semi-finished Recipe Builder (Create)

- Purpose: Create semi-finished recipe formula.

- UI Components: name/yield/notes inputs, waste/loss inputs, raw materials table, semi-finished components table, totals panel, save.

- User Actions: build formula and save.

- States (Loading / Empty / Error): Loading on submit; Empty row placeholders; Error validation.

### Recipe Builder (Semi-finished Edit)

- Page Name: Semi-finished Recipe Builder (Edit)

- Purpose: Update existing semi-finished recipe.

- UI Components: same as create builder with prefilled values.

- User Actions: edit lines and save updates.

- States (Loading / Empty / Error): Loading on submit; Empty optional rows; Error validation.

---

## 4.6 Suppliers & Purchases

### Suppliers List

- Page Name: Suppliers

- Purpose: Manage supplier records.

- UI Components: search input, status filter, export PDF, add supplier button, suppliers table, status badges, view/edit/delete actions, pagination.

- User Actions: search/filter, export, create, view, edit, delete.

- States (Loading / Empty / Error): Loading on filter/pagination; Empty row for no suppliers; Error session feedback.

### Supplier Create

- Page Name: Create Supplier

- Purpose: Register new supplier and linked raw materials.

- UI Components: supplier form fields, raw materials searchable multi-select dropdown, selected materials summary, status select.

- User Actions: fill form and submit.

- States (Loading / Empty / Error): Loading on submit; Empty material selection state; Error validation including material array.

### Supplier Profile

- Page Name: Supplier Profile

- Purpose: 360 supplier details + financial tracking.

- UI Components: profile cards, account summary cards, linked materials list, recent purchases, supply history, payment form, return form.

- User Actions: add payment, add return, edit supplier, navigate purchase links.

- States (Loading / Empty / Error): Loading on forms; Empty linked materials/history rows; Error validation.

### Supplier Edit

- Page Name: Edit Supplier

- Purpose: Update supplier profile and material mapping.

- UI Components: prefilled supplier form.

- User Actions: save updates.

- States (Loading / Empty / Error): Loading on submit; Empty optional fields; Error validation.

### Purchases List

- Page Name: Purchases

- Purpose: Track purchase and general expense requests.

- UI Components: query/request type/approval status/supplier/date filters, reset, export PDF, create button, purchases table, approval badges, payment badges.

- User Actions: filter, reset, export, create request, view request.

- States (Loading / Empty / Error): Loading on filters and pagination; Empty no-results row; Error validation/session.

### Purchase Create

- Page Name: Create Purchase Request

- Purpose: Create inventory purchase request or general expense request.

- UI Components: request type selector, supplier/date/payment controls, tax/discount fields, dynamic items table (inventory type), expense title/amount fields (general expense type), live subtotal text, notes.

- User Actions: add/remove item lines, switch request type, submit request.

- States (Loading / Empty / Error): Loading on submit; Empty initial line setup; Error validation for fields and lines.

### Purchase Detail

- Page Name: Purchase Details

- Purpose: Review full request and perform approval/completion actions.

- UI Components: summary table/card, details card, approve form, reject form, complete form (invoice number + file), approval logs timeline, invoice view/download actions.

- User Actions: approve, reject, complete approved request, update uploaded invoice, print invoice.

- States (Loading / Empty / Error): Loading per action form; Empty item/history sections if no data; Error lifecycle rules (already reviewed, not owner, not approved).

### Purchase Invoice

- Page Name: Purchase Invoice (Print)

- Purpose: Printable purchase receipt.

- UI Components: receipt header, metadata rows, items/expense section, totals, print controls.

- User Actions: print.

- States (Loading / Empty / Error): Loading on render; Empty fallback for optional fields; Error via access restrictions.

---

## 4.7 Employees, Attendance, Salaries

### Employees List

- Page Name: Employees

- Purpose: Manage employee records and HR navigation.

- UI Components: search/status filters, export PDF, export excel, links to attendance and salaries, employees table, status badges, actions.

- User Actions: filter, export, create, view, edit, delete.

- States (Loading / Empty / Error): Loading on filtering/pagination; Empty row if no employees; Error session/validation.

### Employee Create

- Page Name: Create Employee

- Purpose: Register staff profile.

- UI Components: personal fields, national ID field, phone, position, hire date, salary, daily salary preview, optional shift schedule fields, status, address, notes.

- User Actions: submit new employee.

- States (Loading / Empty / Error): Loading on submit; Empty defaults for new form; Error validation.

### Employee Profile

- Page Name: Employee Profile

- Purpose: Show profile, attendance, salary adjustments, and delivery settlements.

- UI Components: profile details card, recent attendance card, salary deductions form, product charge form, adjustments list with edit/delete, delivery settlement section (if role matches), delivered orders table, settlement history table, report export buttons.

- User Actions: add deduction, add product charge, edit/delete adjustment, settle delivery account, view linked orders, export report.

- States (Loading / Empty / Error): Loading on actions; Empty attendance/settlement/history states; Error validation and inventory lock rules.

### Employee Edit

- Page Name: Edit Employee

- Purpose: Update employee profile data.

- UI Components: prefilled employee form.

- User Actions: save updates.

- States (Loading / Empty / Error): Loading on submit; Empty optional values; Error validation.

### Employee Financial Report (Print)

- Page Name: Employee Monthly Financial Report

- Purpose: Printable monthly salary-adjustment report.

- UI Components: report header/meta, summary cards (base salary, adjustments, net salary), adjustments table, print button.

- User Actions: print and export file endpoints.

- States (Loading / Empty / Error): Loading on render; Empty table message if no rows; Error if feature unavailable.

### Attendance List

- Page Name: Attendance

- Purpose: Manage attendance records and quick check operations.

- UI Components: employee/status/date filters, export PDF and excel, quick check-in form, quick check-out form, attendance table with edit/delete actions.

- User Actions: filter, quick check in, quick check out, create attendance, edit attendance, delete attendance, export.

- States (Loading / Empty / Error): Loading on filters/forms; Empty no-records row; Error validation.

### Attendance Create

- Page Name: Create Attendance

- Purpose: Add attendance entry manually.

- UI Components: employee selector, date, status selector, check-in time, check-out time, note field.

- User Actions: submit attendance.

- States (Loading / Empty / Error): Loading on submit; Empty default values; Error validation.

### Attendance Edit

- Page Name: Edit Attendance

- Purpose: Update attendance entry.

- UI Components: prefilled attendance form.

- User Actions: update record.

- States (Loading / Empty / Error): Loading on submit; Empty optional values; Error validation.

### Salaries List

- Page Name: Salaries

- Purpose: Track salary payments and payroll status.

- UI Components: employee/status/date filters, export PDF and excel, create salary button, salaries table with gross/net/paid/status, view action.

- User Actions: filter, export, create record, open salary details.

- States (Loading / Empty / Error): Loading on filters/pagination; Empty no-records row; Error validation.

### Salary Create

- Page Name: Create Salary Record

- Purpose: Create payroll record for period.

- UI Components: employee select, status select, period start/end, base salary override, bonus, other deduction, paid amount, payment date, note, submit.

- User Actions: submit payroll record.

- States (Loading / Empty / Error): Loading on submit; Empty defaults for numeric fields; Error validation.

### Salary Details

- Page Name: Salary Record Details

- Purpose: Show payroll breakdown and allow payment update.

- UI Components: payroll summary card with amounts and metadata, mark payment form (amount/date), status display.

- User Actions: apply payment if not fully paid.

- States (Loading / Empty / Error): Loading on payment submit; Empty optional fields fallback; Error validation.

---

## 4.8 Marketing

### Offers List

- Page Name: Offers

- Purpose: Manage offers and promotions.

- UI Components: create offer button, offers table, status badge, view/edit/delete actions, pagination.

- User Actions: create, view, edit, delete.

- States (Loading / Empty / Error): Loading on actions/pagination; Empty no-offers row; Error delete restrictions.

### Offer Create

- Page Name: Create Offer

- Purpose: Add new offer rule set.

- UI Components: name, order type, discount fields, min/max constraints, schedule fields, priority, stackable flag, status, notes.

- User Actions: save offer.

- States (Loading / Empty / Error): Loading on submit; Empty defaults; Error validation.

### Offer Details

- Page Name: Offer Details

- Purpose: Review offer configuration and usage.

- UI Components: offer details grid, usage snapshot card, recent orders table.

- User Actions: navigate to edit.

- States (Loading / Empty / Error): Loading on page open; Empty recent orders row; Error session messages.

### Offer Edit

- Page Name: Edit Offer

- Purpose: Update existing offer.

- UI Components: prefilled offer form.

- User Actions: save changes.

- States (Loading / Empty / Error): Loading on submit; Empty optional fields; Error validation.

### Coupons List

- Page Name: Coupons

- Purpose: Manage coupon campaigns and usage.

- UI Components: search and status filters, reset button, link to offers, create coupon button, coupons table with usage and redemptions, view/edit/delete actions, pagination.

- User Actions: filter, create, view, edit, delete.

- States (Loading / Empty / Error): Loading on filters/pagination; Empty no-coupons row; Error delete blocked when coupon has redemptions.

### Coupon Create

- Page Name: Create Coupon

- Purpose: Add new coupon rule set.

- UI Components: name, code, discount fields, limits, schedule, status, notes.

- User Actions: save coupon.

- States (Loading / Empty / Error): Loading on submit; Empty defaults; Error validation.

### Coupon Details

- Page Name: Coupon Details

- Purpose: Review coupon details and redemption history.

- UI Components: coupon details grid, usage summary card, recent redemptions table.

- User Actions: navigate to edit.

- States (Loading / Empty / Error): Loading on page open; Empty no-redemption row; Error session messages.

### Coupon Edit

- Page Name: Edit Coupon

- Purpose: Update existing coupon.

- UI Components: prefilled coupon form.

- User Actions: save updates.

- States (Loading / Empty / Error): Loading on submit; Empty optional fields; Error validation.

---

## 4.9 Reports

### Reports Dashboard

- Page Name: Reports

- Purpose: Show period-based business analytics.

- UI Components: date filter form, KPI cards, revenue vs expenses chart, sales by category chart, profit trend chart.

- User Actions: apply date filters, review analytics.

- States (Loading / Empty / Error): Loading on chart rendering and filter apply; Empty not dominant due aggregated data; Error validation/session.

### Shift Logs List

- Page Name: Shift Logs

- Purpose: List cashier shift records for auditing.

- UI Components: from/to date filters, cashier filter, apply button, logs table, profile action button, pagination.

- User Actions: filter and open shift profile.

- States (Loading / Empty / Error): Loading on filter/pagination; Empty no-logs row; Error permission/session.

### Shift Log Profile

- Page Name: Shift Log Profile

- Purpose: Detailed settlement and order breakdown for one shift.

- UI Components: identity card, financial KPI cards (opening, sales, expected, actual, overage, shortage, tips), settlement table, order stats cards, orders table, print button.

- User Actions: review settlement, print receipt.

- States (Loading / Empty / Error): Loading on page open; Empty order table if no orders; Error access denied for unauthorized users.

### Shift Log Receipt

- Page Name: Shift Receipt (Print)

- Purpose: Compact printable shift settlement receipt.

- UI Components: receipt table with cashier, shift time, opening cash, sales, expected cash, actual cash, overage, shortage, tips, note.

- User Actions: print.

- States (Loading / Empty / Error): Loading on render; Empty fallback values for missing data; Error if access denied upstream.

---

## 4.10 User Administration

### Users List

- Page Name: Users

- Purpose: Manage system user accounts and access status.

- UI Components: search input, role filter, users table, role and permission count display, status badge, action buttons (view/edit/enable-disable/delete), create user button.

- User Actions: filter users, create, view, edit, toggle active status, delete.

- States (Loading / Empty / Error): Loading on filters/pagination/actions; Empty no-results row; Error permission and validation feedback.

### User Create

- Page Name: Create User

- Purpose: Add new system account with role and permissions.

- UI Components: identity fields, password and confirmation, role dropdown, grouped permissions matrix.

- User Actions: assign role, assign permission set, save user.

- States (Loading / Empty / Error): Loading on submit; Empty initial form; Error validation.

### User Details

- Page Name: User Details

- Purpose: View account profile and effective permissions.

- UI Components: user details card, account status display, direct permissions panel, effective permissions panel, edit/toggle/delete actions.

- User Actions: edit user, toggle active status, delete user.

- States (Loading / Empty / Error): Loading on action submit; Empty permissions messages if none; Error permission/session.

### User Edit

- Page Name: Edit User

- Purpose: Update user profile, role, and permissions.

- UI Components: prefilled user form, optional password reset fields, role and permissions sections, submit.

- User Actions: save updates.

- States (Loading / Empty / Error): Loading on submit; Empty optional password fields; Error validation.

---

## 5. User Flows

## 5.1 Cashier Flow (open shift → order → close shift)

1. User logs in as الكاشير role (or equivalent permission).

2. POS opens with shift panel.

3. User starts shift by entering opening cash.

4. User builds orders (dine in / takeaway / delivery).

5. For dine-in, user selects table and can transfer table when needed.

6. Order is sent to kitchen/bar.

7. Paid orders can print invoice.

8. User ends shift with actual cash and optional tips.

9. System computes expected cash, difference, overage/shortage, stores shift receipt payload.

10. System logs out cashier and redirects to login.

## 5.2 Admin Flow

1. Admin logs in and lands on dashboard.

2. Admin navigates to users, operations, inventory, purchases, HR, and reports.

3. Admin monitors orders, shift logs, and financial reports.

4. Admin manages accounts and permissions.

5. Admin reviews and audits operational data and exceptions.

## 5.3 Inventory Flow

1. User enters inventory module.

2. User selects active warehouse tab (main/branch).

3. User creates or edits ingredients.

4. User applies adjustments (in/out/set) per warehouse.

5. User transfers stock between warehouses when needed.

6. User runs stock audits and reviews stock logs.

7. User updates recipes/semi-finished formulas as production reference.

## 5.4 Purchase Flow

1. Requester creates purchase request (inventory or general expense).

2. Request enters pending approval state.

3. Approver reviews request and approves or rejects.

4. System writes approval log and sends notification to requester.

5. If approved, request owner completes request by uploading supplier invoice file and invoice number.

6. For inventory requests, stock posting is applied during completion.

7. Request status moves to completed.

---

## 6. Special Systems

## 6.1 Shift System (نظام الورديات)

1. One open shift per cashier user is enforced.

2. Shift start requires opening cash.

3. Shift close requires actual cash and accepts optional tips.

4. System calculates total paid sales, expected cash, difference, overage, shortage.

5. Shift logs are linked for reporting and printable receipt.

## 6.2 Tips Handling

1. Tips are captured at shift close.

2. Tips are stored on cashier shift record.

3. Tips are visible in POS last closed summary.

4. Tips are shown in shift profile and shift receipt print view.

## 6.3 Approvals (Purchase Workflow)

1. Purchase requests start as pending.

2. Approval and rejection are allowed only while pending.

3. Reject action requires comment.

4. Approval actions are audited in purchase approval logs.

5. Request owner receives review notification.

6. Completion is restricted by status and request ownership rules.

## 6.4 Table Management

1. Tables have available/occupied states.

2. Table status can be toggled from tables module.

3. POS/Waiter also sync table status from active dine-in orders.

4. Dine-in order uniqueness per table is enforced.

5. Table transfer is validated and synchronized transactionally.

---

## 7. Export and Print Surfaces

### Order Invoice Print

- Page Name: Order Invoice

- Purpose: printable receipt for paid order.

- UI Components: order metadata, line items, totals.

- User Actions: print.

- States (Loading / Empty / Error): Loading render; Empty optional placeholders; Error if order not eligible.

### Purchase Invoice Print

- Page Name: Purchase Invoice

- Purpose: printable purchase receipt.

- UI Components: purchase metadata, items/expense block, totals.

- User Actions: print.

- States (Loading / Empty / Error): Loading render; Empty optional placeholders; Error via access checks.

### Shift Receipt Print

- Page Name: Shift Receipt

- Purpose: printable shift settlement summary.

- UI Components: cashier/times/cash totals/tips table.

- User Actions: print.

- States (Loading / Empty / Error): Loading render; Empty fallback values; Error via permission checks.

### PDF/CSV/Excel Exports

- Page Name: Export Views (Customers, Suppliers, Purchases, Inventory Logs, Recipes, Employees, Attendance, Salaries)

- Purpose: generate downloadable reporting files.

- UI Components: report header meta, data table, truncation notice when export limit is reached.

- User Actions: download and share reports.

- States (Loading / Empty / Error): Loading generation/download; Empty when no rows; Error on invalid filters or unavailable features.

---

## 8. Final Handoff Notes for UI/UX Designer

1. This document is a clean, complete, as-is product documentation.

2. Keep all described workflows and states unchanged when preparing wireframes.

3. Respect role-based visibility in navigation and page actions.

4. Respect bilingual behavior (EN/AR, RTL/LTR).

5. No feature logic change should be assumed from this document.
