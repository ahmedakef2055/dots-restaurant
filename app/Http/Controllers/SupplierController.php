<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\PurchasePayment;
use App\Models\PurchaseReturn;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Support\PdfExportRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SupplierController extends Controller
{
    private const SUPPLIERS_PDF_LIMIT = 600;

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $suppliers = Supplier::query()
            ->withCount('purchases')
            ->when($validated['q'] ?? null, function ($query, string $search) {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%");
                });
            })
            ->when($validated['status'] ?? null, function ($query, string $status): void {
                $query->where('is_active', $status === 'active');
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('suppliers.index', [
            'suppliers' => $suppliers,
            'filters' => [
                'q' => $validated['q'] ?? '',
                'status' => $validated['status'] ?? '',
            ],
        ]);
    }

    public function exportPdf(Request $request, PdfExportRenderer $pdfExportRenderer): Response
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $baseQuery = Supplier::query()
            ->withCount('purchases')
            ->when($validated['q'] ?? null, function ($query, string $search) {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($validated['status'] ?? null, function ($query, string $status): void {
                $query->where('is_active', $status === 'active');
            })
            ->orderBy('name');

        $totalCount = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->limit(self::SUPPLIERS_PDF_LIMIT)
            ->get([
                'id',
                'name',
                'contact_person',
                'email',
                'phone',
                'city',
                'country',
                'address',
                'is_active',
            ])
            ->map(static function (Supplier $supplier): array {
                $city = trim((string) ($supplier->city ?? ''));
                $country = trim((string) ($supplier->country ?? ''));

                return [
                    'name' => (string) $supplier->name,
                    'contact_person' => trim((string) ($supplier->contact_person ?? '')),
                    'email' => trim((string) ($supplier->email ?? '')),
                    'phone' => trim((string) ($supplier->phone ?? '')),
                    'location' => trim(implode(' ', array_filter([$city, $country]))),
                    'address' => trim((string) ($supplier->address ?? '')),
                    'purchases_count' => (int) ($supplier->purchases_count ?? 0),
                    'is_active' => (bool) $supplier->is_active,
                ];
            })
            ->values()
            ->all();

        $exportedCount = count($rows);
        $isTruncated = $totalCount > $exportedCount;

        $fallbackFilters = array_filter([
            'q' => $validated['q'] ?? null,
            'status' => $validated['status'] ?? null,
        ], static fn($value): bool => $value !== null && $value !== '');

        return $pdfExportRenderer->downloadPdfFromView(
            'suppliers.exports.suppliers-pdf',
            [
                'rows' => $rows,
                'filters' => [
                    'q' => (string) ($validated['q'] ?? ''),
                    'status' => (string) ($validated['status'] ?? ''),
                ],
                'totalCount' => $totalCount,
                'exportedCount' => $exportedCount,
                'isTruncated' => $isTruncated,
                'generatedAt' => now(),
            ],
            'suppliers-report-' . now()->format('Ymd_His') . '.pdf',
            route('suppliers.index', $fallbackFilters)
        );
    }

    public function create(): View
    {
        $ingredients = Ingredient::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'unit']);

        return view('suppliers.create', [
            'ingredients' => $ingredients,
            'selectedIngredientIds' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateSupplier($request);
        $ingredientIds = collect($validated['ingredient_ids'] ?? [])
            ->map(static fn($id): int => (int) $id)
            ->filter(static fn($id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        $supplier = DB::transaction(function () use ($validated, $ingredientIds): Supplier {
            $supplier = Supplier::query()->create(collect($validated)->except('ingredient_ids')->all());

            $this->syncSupplierIngredients((int) $supplier->id, $ingredientIds);

            Ingredient::query()
                ->whereIn('id', $ingredientIds)
                ->where('is_active', true)
                ->update(['supplier_id' => $supplier->id]);

            return $supplier;
        });

        return redirect()->route('suppliers.show', $supplier)->with('success', __('messages.success.supplier_created'));
    }

    public function show(Supplier $supplier): View
    {
        $supportsIngredientPivot = Schema::hasTable('ingredient_supplier');
        $supportsPurchasePayments = Schema::hasTable('purchase_payments');
        $supportsPurchaseReturns = Schema::hasTable('purchase_returns');
        $supportsPurchasePaymentMethod = Schema::hasColumn('purchases', 'payment_method');

        $recentPurchases = $supplier->purchases()
            ->withCount('items')
            ->latest('purchase_date')
            ->latest('id')
            ->limit(8)
            ->get();

        $ingredients = $supportsIngredientPivot
            ? $supplier->ingredients()
            ->select(['ingredients.id', 'ingredients.name', 'ingredients.unit'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            : Ingredient::query()
            ->select(['id', 'supplier_id', 'name', 'unit'])
            ->where('supplier_id', $supplier->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $supplier->setRelation('ingredients', $ingredients);

        $supplier->loadCount('purchases');

        $supplyHistory = PurchaseItem::query()
            ->select([
                'purchase_items.id',
                'purchase_items.purchase_id',
                'purchase_items.ingredient_name',
                'purchase_items.quantity',
                'purchase_items.unit_cost',
                'purchase_items.line_total',
                'purchase_items.expiry_date',
                'purchases.purchase_number',
                'purchases.purchase_date',
            ])
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->where('purchases.supplier_id', $supplier->id)
            ->orderByDesc('purchases.purchase_date')
            ->orderByDesc('purchase_items.id')
            ->limit(50)
            ->get();

        $supplyTotals = PurchaseItem::query()
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->where('purchases.supplier_id', $supplier->id)
            ->selectRaw('COALESCE(SUM(purchase_items.quantity), 0) as total_quantity')
            ->selectRaw('COALESCE(SUM(purchase_items.line_total), 0) as total_value')
            ->selectRaw('COUNT(purchase_items.id) as total_lines')
            ->first();

        $totalPurchases = (float) ($supplier->purchases()->sum('total') ?? 0);
        $totalPayments = $supportsPurchasePayments
            ? (float) (PurchasePayment::query()->where('supplier_id', $supplier->id)->sum('amount') ?? 0)
            : 0.0;
        $totalReturns = $supportsPurchaseReturns
            ? (float) (PurchaseReturn::query()->where('supplier_id', $supplier->id)->sum('amount') ?? 0)
            : 0.0;
        $supplierBalance = max($totalPurchases - $totalPayments - $totalReturns, 0);

        $purchaseTransactionsQuery = $supplier->purchases()
            ->selectRaw("'purchase' as transaction_type")
            ->selectRaw('purchase_number as reference_number')
            ->selectRaw('purchase_date as transaction_date')
            ->selectRaw('total as amount')
            ->selectRaw('notes')
            ->latest('purchase_date')
            ->latest('id')
            ->limit(30);

        if ($supportsPurchasePaymentMethod) {
            $purchaseTransactionsQuery->selectRaw('payment_method');
        } else {
            $purchaseTransactionsQuery->selectRaw("'cash' as payment_method");
        }

        $purchaseTransactions = $purchaseTransactionsQuery->get();

        $paymentTransactions = $supportsPurchasePayments
            ? PurchasePayment::query()
            ->where('supplier_id', $supplier->id)
            ->selectRaw("'payment' as transaction_type")
            ->selectRaw('payment_number as reference_number')
            ->selectRaw('payment_date as transaction_date')
            ->selectRaw('amount')
            ->selectRaw('method as payment_method')
            ->selectRaw('notes')
            ->latest('payment_date')
            ->latest('id')
            ->limit(30)
            ->get()
            : collect();

        $returnTransactions = $supportsPurchaseReturns
            ? PurchaseReturn::query()
            ->where('supplier_id', $supplier->id)
            ->selectRaw("'return' as transaction_type")
            ->selectRaw('return_number as reference_number')
            ->selectRaw('return_date as transaction_date')
            ->selectRaw('amount')
            ->selectRaw("'return' as payment_method")
            ->selectRaw('notes')
            ->latest('return_date')
            ->latest('id')
            ->limit(30)
            ->get()
            : collect();

        $transactions = $purchaseTransactions
            ->concat($paymentTransactions)
            ->concat($returnTransactions)
            ->sortByDesc(function ($row): int {
                $date = $row->transaction_date;

                return $date ? strtotime((string) $date) : 0;
            })
            ->values();

        $supplierPrices = DB::table('purchase_items')
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->join('ingredients', 'ingredients.id', '=', 'purchase_items.ingredient_id')
            ->selectRaw('purchase_items.ingredient_id')
            ->selectRaw('ingredients.name as ingredient_name')
            ->selectRaw('suppliers.id as supplier_id')
            ->selectRaw('suppliers.name as supplier_name')
            ->selectRaw('MIN(purchase_items.unit_cost) as best_unit_cost')
            ->groupBy('purchase_items.ingredient_id', 'ingredients.name', 'suppliers.id', 'suppliers.name')
            ->orderBy('ingredients.name')
            ->orderBy('best_unit_cost')
            ->get()
            ->groupBy('ingredient_id')
            ->values();

        return view('suppliers.show', [
            'supplier' => $supplier,
            'supplyHistory' => $supplyHistory,
            'supplyTotals' => $supplyTotals,
            'supplierAccount' => [
                'total_purchases' => round($totalPurchases, 2),
                'total_payments' => round($totalPayments, 2),
                'total_returns' => round($totalReturns, 2),
                'balance_due' => round($supplierBalance, 2),
            ],
            'transactions' => $transactions,
            'supplierPrices' => $supplierPrices,
            'recentPurchases' => $recentPurchases,
        ]);
    }

    public function edit(Supplier $supplier): View
    {
        $supportsIngredientPivot = Schema::hasTable('ingredient_supplier');

        $ingredients = Ingredient::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'unit']);

        $selectedIngredientIds = $supportsIngredientPivot
            ? DB::table('ingredient_supplier')
            ->where('supplier_id', $supplier->id)
            ->pluck('ingredient_id')
            ->map(static fn($id): int => (int) $id)
            ->all()
            : Ingredient::query()
            ->where('supplier_id', $supplier->id)
            ->pluck('id')
            ->map(static fn($id): int => (int) $id)
            ->all();

        return view('suppliers.edit', [
            'supplier' => $supplier,
            'ingredients' => $ingredients,
            'selectedIngredientIds' => $selectedIngredientIds,
        ]);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $this->validateSupplier($request, $supplier->id);
        $ingredientIds = collect($validated['ingredient_ids'] ?? [])
            ->map(static fn($id): int => (int) $id)
            ->filter(static fn($id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        DB::transaction(function () use ($supplier, $validated, $ingredientIds): void {
            $supplier->update(collect($validated)->except('ingredient_ids')->all());

            $this->syncSupplierIngredients((int) $supplier->id, $ingredientIds);

            if (empty($ingredientIds)) {
                Ingredient::query()
                    ->where('supplier_id', $supplier->id)
                    ->update(['supplier_id' => null]);

                return;
            }

            Ingredient::query()
                ->where('supplier_id', $supplier->id)
                ->whereNotIn('id', $ingredientIds)
                ->update(['supplier_id' => null]);

            Ingredient::query()
                ->whereIn('id', $ingredientIds)
                ->where('is_active', true)
                ->update(['supplier_id' => $supplier->id]);
        });

        return redirect()->route('suppliers.show', $supplier)->with('success', __('messages.success.supplier_updated'));
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchases()->exists()) {
            return back()->with('error', __('messages.errors.cannot_delete_supplier_with_purchases'));
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', __('messages.success.supplier_deleted'));
    }

    public function addPayment(Request $request, Supplier $supplier): RedirectResponse
    {
        if (! Schema::hasTable('purchase_payments')) {
            return back()->with('error', __('messages.errors.supplier_payments_migration_required'));
        }

        $validated = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cash,bank_transfer,wallet,cheque,other'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'purchase_id' => ['nullable', 'integer', 'exists:purchases,id'],
        ]);

        $paymentNumber = $this->generateNumber('PAY');

        PurchasePayment::query()->create([
            'payment_number' => $paymentNumber,
            'supplier_id' => $supplier->id,
            'purchase_id' => $validated['purchase_id'] ?? null,
            'user_id' => Auth::id(),
            'payment_date' => $validated['payment_date'],
            'amount' => round((float) $validated['amount'], 2),
            'method' => $validated['method'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('suppliers.show', $supplier)->with('success', __('messages.success.supplier_payment_added'));
    }

    public function addReturn(Request $request, Supplier $supplier): RedirectResponse
    {
        if (! Schema::hasTable('purchase_returns')) {
            return back()->with('error', __('messages.errors.supplier_returns_migration_required'));
        }

        $validated = $request->validate([
            'purchase_id' => ['required', 'integer', 'exists:purchases,id'],
            'return_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $purchaseId = (int) $validated['purchase_id'];
        $belongsToSupplier = $supplier->purchases()->whereKey($purchaseId)->exists();

        if (! $belongsToSupplier) {
            return back()->withErrors([
                'purchase_id' => __('messages.errors.purchase_not_for_supplier'),
            ])->withInput();
        }

        $returnNumber = $this->generateNumber('RET');

        PurchaseReturn::query()->create([
            'return_number' => $returnNumber,
            'supplier_id' => $supplier->id,
            'purchase_id' => $purchaseId,
            'user_id' => Auth::id(),
            'return_date' => $validated['return_date'],
            'amount' => round((float) $validated['amount'], 2),
            'reason' => $validated['reason'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('suppliers.show', $supplier)->with('success', __('messages.success.supplier_return_added'));
    }

    private function validateSupplier(Request $request, ?int $supplierId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:190', Rule::unique('suppliers', 'email')->ignore($supplierId)],
            'phone' => ['required', 'string', 'max:30', Rule::unique('suppliers', 'phone')->ignore($supplierId)],
            'ingredient_ids' => ['required', 'array', 'min:1'],
            'ingredient_ids.*' => ['integer', 'exists:ingredients,id'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['required', 'boolean'],
        ]);
    }

    private function generateNumber(string $prefix): string
    {
        return sprintf('%s-%s-%s', $prefix, now()->format('YmdHis'), strtoupper(bin2hex(random_bytes(2))));
    }

    private function syncSupplierIngredients(int $supplierId, array $ingredientIds): void
    {
        if (! Schema::hasTable('ingredient_supplier')) {
            return;
        }

        DB::table('ingredient_supplier')
            ->where('supplier_id', $supplierId)
            ->delete();

        if (empty($ingredientIds)) {
            return;
        }

        $rows = collect($ingredientIds)
            ->map(static fn($ingredientId): array => [
                'supplier_id' => $supplierId,
                'ingredient_id' => (int) $ingredientId,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->all();

        DB::table('ingredient_supplier')->insert($rows);
    }
}
