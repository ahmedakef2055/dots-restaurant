<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Ingredient;
use App\Models\InventoryStockLog;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Services\InventoryForecastService;
use App\Services\InventoryService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\View;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;

class InventoryController extends Controller
{
    private const INVENTORY_LOGS_PDF_LIMIT = 600;

    /**
     * Contextual Arabic presentation forms used to improve DomPDF rendering.
     * Keys are base Arabic codepoints.
     * i = isolated, f = final, b = initial, m = medial.
     */
    private const ARABIC_GLYPH_FORMS = [
        0x0621 => ['i' => "\u{FE80}"],
        0x0622 => ['i' => "\u{FE81}", 'f' => "\u{FE82}"],
        0x0623 => ['i' => "\u{FE83}", 'f' => "\u{FE84}"],
        0x0624 => ['i' => "\u{FE85}", 'f' => "\u{FE86}"],
        0x0625 => ['i' => "\u{FE87}", 'f' => "\u{FE88}"],
        0x0626 => ['i' => "\u{FE89}", 'f' => "\u{FE8A}", 'b' => "\u{FE8B}", 'm' => "\u{FE8C}"],
        0x0627 => ['i' => "\u{FE8D}", 'f' => "\u{FE8E}"],
        0x0628 => ['i' => "\u{FE8F}", 'f' => "\u{FE90}", 'b' => "\u{FE91}", 'm' => "\u{FE92}"],
        0x0629 => ['i' => "\u{FE93}", 'f' => "\u{FE94}"],
        0x062A => ['i' => "\u{FE95}", 'f' => "\u{FE96}", 'b' => "\u{FE97}", 'm' => "\u{FE98}"],
        0x062B => ['i' => "\u{FE99}", 'f' => "\u{FE9A}", 'b' => "\u{FE9B}", 'm' => "\u{FE9C}"],
        0x062C => ['i' => "\u{FE9D}", 'f' => "\u{FE9E}", 'b' => "\u{FE9F}", 'm' => "\u{FEA0}"],
        0x062D => ['i' => "\u{FEA1}", 'f' => "\u{FEA2}", 'b' => "\u{FEA3}", 'm' => "\u{FEA4}"],
        0x062E => ['i' => "\u{FEA5}", 'f' => "\u{FEA6}", 'b' => "\u{FEA7}", 'm' => "\u{FEA8}"],
        0x062F => ['i' => "\u{FEA9}", 'f' => "\u{FEAA}"],
        0x0630 => ['i' => "\u{FEAB}", 'f' => "\u{FEAC}"],
        0x0631 => ['i' => "\u{FEAD}", 'f' => "\u{FEAE}"],
        0x0632 => ['i' => "\u{FEAF}", 'f' => "\u{FEB0}"],
        0x0633 => ['i' => "\u{FEB1}", 'f' => "\u{FEB2}", 'b' => "\u{FEB3}", 'm' => "\u{FEB4}"],
        0x0634 => ['i' => "\u{FEB5}", 'f' => "\u{FEB6}", 'b' => "\u{FEB7}", 'm' => "\u{FEB8}"],
        0x0635 => ['i' => "\u{FEB9}", 'f' => "\u{FEBA}", 'b' => "\u{FEBB}", 'm' => "\u{FEBC}"],
        0x0636 => ['i' => "\u{FEBD}", 'f' => "\u{FEBE}", 'b' => "\u{FEBF}", 'm' => "\u{FEC0}"],
        0x0637 => ['i' => "\u{FEC1}", 'f' => "\u{FEC2}", 'b' => "\u{FEC3}", 'm' => "\u{FEC4}"],
        0x0638 => ['i' => "\u{FEC5}", 'f' => "\u{FEC6}", 'b' => "\u{FEC7}", 'm' => "\u{FEC8}"],
        0x0639 => ['i' => "\u{FEC9}", 'f' => "\u{FECA}", 'b' => "\u{FECB}", 'm' => "\u{FECC}"],
        0x063A => ['i' => "\u{FECD}", 'f' => "\u{FECE}", 'b' => "\u{FECF}", 'm' => "\u{FED0}"],
        0x0641 => ['i' => "\u{FED1}", 'f' => "\u{FED2}", 'b' => "\u{FED3}", 'm' => "\u{FED4}"],
        0x0642 => ['i' => "\u{FED5}", 'f' => "\u{FED6}", 'b' => "\u{FED7}", 'm' => "\u{FED8}"],
        0x0643 => ['i' => "\u{FED9}", 'f' => "\u{FEDA}", 'b' => "\u{FEDB}", 'm' => "\u{FEDC}"],
        0x0644 => ['i' => "\u{FEDD}", 'f' => "\u{FEDE}", 'b' => "\u{FEDF}", 'm' => "\u{FEE0}"],
        0x0645 => ['i' => "\u{FEE1}", 'f' => "\u{FEE2}", 'b' => "\u{FEE3}", 'm' => "\u{FEE4}"],
        0x0646 => ['i' => "\u{FEE5}", 'f' => "\u{FEE6}", 'b' => "\u{FEE7}", 'm' => "\u{FEE8}"],
        0x0647 => ['i' => "\u{FEE9}", 'f' => "\u{FEEA}", 'b' => "\u{FEEB}", 'm' => "\u{FEEC}"],
        0x0648 => ['i' => "\u{FEED}", 'f' => "\u{FEEE}"],
        0x0649 => ['i' => "\u{FEEF}", 'f' => "\u{FEF0}"],
        0x064A => ['i' => "\u{FEF1}", 'f' => "\u{FEF2}", 'b' => "\u{FEF3}", 'm' => "\u{FEF4}"],
    ];

    /**
     * Letters that can connect to the next letter.
     */
    private const ARABIC_CONNECTS_BEFORE = [
        0x0626 => true,
        0x0628 => true,
        0x062A => true,
        0x062B => true,
        0x062C => true,
        0x062D => true,
        0x062E => true,
        0x0633 => true,
        0x0634 => true,
        0x0635 => true,
        0x0636 => true,
        0x0637 => true,
        0x0638 => true,
        0x0639 => true,
        0x063A => true,
        0x0641 => true,
        0x0642 => true,
        0x0643 => true,
        0x0644 => true,
        0x0645 => true,
        0x0646 => true,
        0x0647 => true,
        0x064A => true,
    ];

    /**
     * Letters that can connect to the previous letter.
     */
    private const ARABIC_CONNECTS_AFTER = [
        0x0622 => true,
        0x0623 => true,
        0x0624 => true,
        0x0625 => true,
        0x0626 => true,
        0x0627 => true,
        0x0628 => true,
        0x0629 => true,
        0x062A => true,
        0x062B => true,
        0x062C => true,
        0x062D => true,
        0x062E => true,
        0x062F => true,
        0x0630 => true,
        0x0631 => true,
        0x0632 => true,
        0x0633 => true,
        0x0634 => true,
        0x0635 => true,
        0x0636 => true,
        0x0637 => true,
        0x0638 => true,
        0x0639 => true,
        0x063A => true,
        0x0641 => true,
        0x0642 => true,
        0x0643 => true,
        0x0644 => true,
        0x0645 => true,
        0x0646 => true,
        0x0647 => true,
        0x0648 => true,
        0x0649 => true,
        0x064A => true,
    ];

    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly InventoryForecastService $inventoryForecastService
    ) {}

    public function index(Request $request): View
    {
        $warehouseDirectory = Warehouse::query()
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->get(['id', 'name', 'code', 'location', 'is_active', 'is_default']);

        if ($warehouseDirectory->isEmpty()) {
            $this->inventoryService->defaultWarehouseId();

            $warehouseDirectory = Warehouse::query()
                ->orderByDesc('is_default')
                ->orderBy('id')
                ->get(['id', 'name', 'code', 'location', 'is_active', 'is_default']);
        }

        $mainWarehouse = $warehouseDirectory->firstWhere('code', 'MAIN')
            ?? $warehouseDirectory->firstWhere('is_default', true)
            ?? $warehouseDirectory->first();

        $branchWarehouse = $warehouseDirectory->firstWhere('code', 'BRANCH')
            ?? $warehouseDirectory->first(function (Warehouse $warehouse) use ($mainWarehouse): bool {
                return ! $mainWarehouse || (int) $warehouse->id !== (int) $mainWarehouse->id;
            });

        $warehouseTabs = [
            'main' => $mainWarehouse,
            'branch' => $branchWarehouse,
        ];

        $selectedWarehouseTab = $this->normalizeWarehouseTab((string) $request->query('warehouse_tab', 'main'));

        if (! ($warehouseTabs[$selectedWarehouseTab] instanceof Warehouse)) {
            $selectedWarehouseTab = ($warehouseTabs['main'] instanceof Warehouse) ? 'main' : 'branch';
        }

        $selectedWarehouse = $warehouseTabs[$selectedWarehouseTab] ?? $warehouseDirectory->first();
        $selectedWarehouseId = (int) ($selectedWarehouse?->id ?? $this->inventoryService->defaultWarehouseId());

        $warehouses = $warehouseDirectory
            ->filter(static fn(Warehouse $warehouse): bool => (bool) $warehouse->is_active || (bool) $warehouse->is_default)
            ->values();

        if ($warehouses->isEmpty()) {
            $warehouses = $warehouseDirectory;
        }

        $defaultWarehouseId = (int) ($selectedWarehouse?->id ?? $warehouses->firstWhere('is_default', true)?->id ?? $this->inventoryService->defaultWarehouseId());

        $ingredients = Ingredient::query()
            ->with([
                'supplier:id,name',
                'unitModel:id,name,code',
                'warehouseStocks' => static function ($query) use ($selectedWarehouseId): void {
                    $query
                        ->where('warehouse_id', $selectedWarehouseId)
                        ->select(['id', 'ingredient_id', 'warehouse_id', 'quantity']);
                },
            ])
            ->latest('id')
            ->paginate(12, ['*'], 'ingredients_page')
            ->appends(['warehouse_tab' => $selectedWarehouseTab]);

        $logs = InventoryStockLog::query()
            ->with(['ingredient:id,name,unit', 'user:id,name'])
            ->where('warehouse_id', $selectedWarehouseId)
            ->orderByDesc(DB::raw('COALESCE(occurred_at, created_at)'))
            ->orderByDesc('id')
            ->paginate(12, ['*'], 'logs_page')
            ->appends(['warehouse_tab' => $selectedWarehouseTab]);

        $lowStockCount = (int) DB::table('ingredients')
            ->leftJoin('ingredient_warehouse_stocks as stock', static function ($join) use ($selectedWarehouseId): void {
                $join->on('stock.ingredient_id', '=', 'ingredients.id')
                    ->where('stock.warehouse_id', '=', $selectedWarehouseId);
            })
            ->where('ingredients.is_active', true)
            ->whereRaw('COALESCE(stock.quantity, 0) <= ingredients.threshold')
            ->count();

        $expiryAlerts = $this->inventoryForecastService->buildExpiryAlerts(30)
            ->filter(static function ($alert) use ($selectedWarehouseId): bool {
                $warehouseId = (int) (data_get($alert, 'warehouse_id') ?? data_get($alert, 'warehouse.id') ?? 0);

                return $warehouseId === $selectedWarehouseId;
            })
            ->take(10)
            ->values();

        $smartSuggestions = $this->inventoryForecastService->buildSmartShortageSuggestions(30)
            ->take(8)
            ->values();

        // Ingredients with expiry_date — includes already expired and within 30 days ahead
        $expiringIngredients = Ingredient::query()
            ->where('is_active', true)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays(30)->toDateString())
            ->orderBy('expiry_date')
            ->get(['id', 'name', 'unit', 'expiry_date', 'expiry_alert_days']);

        $usageLast30 = (float) InventoryStockLog::query()
            ->where('warehouse_id', $selectedWarehouseId)
            ->whereIn('adjustment_type', ['out'])
            ->whereRaw('COALESCE(occurred_at, created_at) >= ?', [now()->subDays(30)])
            ->sum('quantity');

        $costDashboard = [
            'total_cost' => (float) DB::table('ingredients')
                ->leftJoin('ingredient_warehouse_stocks as stock', static function ($join) use ($selectedWarehouseId): void {
                    $join->on('stock.ingredient_id', '=', 'ingredients.id')
                        ->where('stock.warehouse_id', '=', $selectedWarehouseId);
                })
                ->selectRaw('COALESCE(SUM(COALESCE(stock.quantity, 0) * ingredients.cost), 0) as aggregate')
                ->value('aggregate'),
            'usage_quantity_30d' => round($usageLast30, 3),
            'profit_impact' => (float) DB::table('recipe_versions')
                ->where('is_active', true)
                ->selectRaw('COALESCE(SUM(selling_price - total_cost), 0) as aggregate')
                ->value('aggregate'),
        ];

        $auditIngredients = Ingredient::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'quantity']);

        return view('inventory.index', [
            'ingredients' => $ingredients,
            'logs' => $logs,
            'lowStockCount' => $lowStockCount,
            'expiryAlerts' => $expiryAlerts,
            'expiringIngredients' => $expiringIngredients,
            'smartSuggestions' => $smartSuggestions,
            'costDashboard' => $costDashboard,
            'auditIngredients' => $auditIngredients,
            'warehouses' => $warehouses,
            'warehouseDirectory' => $warehouseDirectory,
            'warehouseTabs' => $warehouseTabs,
            'selectedWarehouseTab' => $selectedWarehouseTab,
            'selectedWarehouse' => $selectedWarehouse,
            'defaultWarehouseId' => $defaultWarehouseId,
        ]);
    }

    public function exportLogsPdf(Request $request): Response
    {
        $warehouseDirectory = Warehouse::query()
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->get(['id', 'name', 'code', 'location', 'is_active', 'is_default']);

        if ($warehouseDirectory->isEmpty()) {
            $this->inventoryService->defaultWarehouseId();

            $warehouseDirectory = Warehouse::query()
                ->orderByDesc('is_default')
                ->orderBy('id')
                ->get(['id', 'name', 'code', 'location', 'is_active', 'is_default']);
        }

        $mainWarehouse = $warehouseDirectory->firstWhere('code', 'MAIN')
            ?? $warehouseDirectory->firstWhere('is_default', true)
            ?? $warehouseDirectory->first();

        $branchWarehouse = $warehouseDirectory->firstWhere('code', 'BRANCH')
            ?? $warehouseDirectory->first(function (Warehouse $warehouse) use ($mainWarehouse): bool {
                return ! $mainWarehouse || (int) $warehouse->id !== (int) $mainWarehouse->id;
            });

        $warehouseTabs = [
            'main' => $mainWarehouse,
            'branch' => $branchWarehouse,
        ];

        $selectedWarehouseTab = $this->normalizeWarehouseTab((string) $request->query('warehouse_tab', 'main'));

        if (! ($warehouseTabs[$selectedWarehouseTab] instanceof Warehouse)) {
            $selectedWarehouseTab = ($warehouseTabs['main'] instanceof Warehouse) ? 'main' : 'branch';
        }

        $selectedWarehouse = $warehouseTabs[$selectedWarehouseTab] ?? $warehouseDirectory->first();
        $selectedWarehouseId = (int) ($selectedWarehouse?->id ?? $this->inventoryService->defaultWarehouseId());

        $baseLogsQuery = InventoryStockLog::query()
            ->with(['ingredient:id,name,unit', 'user:id,name'])
            ->where('warehouse_id', $selectedWarehouseId)
            ->orderByDesc(DB::raw('COALESCE(occurred_at, created_at)'));

        $totalLogsCount = (clone $baseLogsQuery)->count();

        $logs = $baseLogsQuery
            ->orderByDesc('id')
            ->limit(self::INVENTORY_LOGS_PDF_LIMIT)
            ->get();

        $isTruncated = $totalLogsCount > $logs->count();

        return $this->downloadPdfFromView(
            'inventory.exports.stock-logs-pdf',
            [
                'logs' => $logs,
                'selectedWarehouse' => $selectedWarehouse,
                'selectedWarehouseTab' => $selectedWarehouseTab,
                'totalLogsCount' => $totalLogsCount,
                'isTruncated' => $isTruncated,
                'generatedAt' => now(),
            ],
            'inventory-stock-logs-' . $selectedWarehouseTab . '-' . now()->format('Ymd_His') . '.pdf',
            route('inventory.index', ['warehouse_tab' => $selectedWarehouseTab])
        );
    }

    public function create(): View
    {
        $mainWarehouse = Warehouse::query()
            ->where('code', 'MAIN')
            ->orderBy('id')
            ->first();

        if (! $mainWarehouse) {
            $mainWarehouse = Warehouse::query()->find($this->inventoryService->mainWarehouseId());
        }

        return view('inventory.create', [
            'units' => Unit::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']),
            'suppliers' => Supplier::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'warehouses' => $this->inventoryService->activeWarehouses(),
            'mainWarehouse' => $mainWarehouse,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'cost' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'threshold' => ['required', 'numeric', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'expiry_alert_days' => ['required', 'integer', 'min:1', 'max:180'],
            'is_active' => ['required', 'boolean'],
        ]);

        $unit = Unit::query()->findOrFail((int) $validated['unit_id']);
        $warehouseId = $this->inventoryService->mainWarehouseId();

        DB::transaction(function () use ($validated, $unit, $warehouseId): void {
            $ingredient = Ingredient::query()->create([
                'name' => $validated['name'],
                'supplier_id' => $validated['supplier_id'] ?? null,
                'unit' => $unit->code,
                'unit_id' => $unit->id,
                'default_warehouse_id' => $warehouseId,
                'cost' => round((float) $validated['cost'], 4),
                'quantity' => 0,
                'current_stock' => 0,
                'threshold' => round((float) $validated['threshold'], 3),
                'reorder_level' => round((float) $validated['threshold'], 3),
                'cost_method' => 'fifo',
                'expiry_date' => $validated['expiry_date'] ?? null,
                'expiry_alert_days' => (int) $validated['expiry_alert_days'],
                'is_active' => (bool) $validated['is_active'],
            ]);

            $initialQty = (float) $validated['quantity'];
            $unitCost = (float) $validated['cost'];

            if ($initialQty > 0) {
                $this->inventoryService->addStock(
                    ingredient: $ingredient,
                    quantity: $initialQty,
                    unitCost: $unitCost,
                    warehouseId: $warehouseId,
                    expiryDate: $validated['expiry_date'] ?? null,
                    action: 'add',
                    adjustmentType: 'in',
                    note: __('messages.notes.initial_stock_balance'),
                );
            }
        });

        return redirect()
            ->route('inventory.index')
            ->with('success', __('messages.success.ingredient_created'));
    }

    public function edit(Ingredient $ingredient): View
    {
        return view('inventory.edit', [
            'ingredient' => $ingredient,
            'units' => Unit::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']),
            'suppliers' => Supplier::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'warehouses' => $this->inventoryService->activeWarehouses(),
        ]);
    }

    public function update(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'default_warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'cost' => ['required', 'numeric', 'min:0'],
            'threshold' => ['required', 'numeric', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'expiry_alert_days' => ['required', 'integer', 'min:1', 'max:180'],
            'is_active' => ['required', 'boolean'],
        ]);

        $unit = Unit::query()->findOrFail((int) $validated['unit_id']);
        $defaultWarehouseId = (int) $validated['default_warehouse_id'];

        $ingredient->update([
            'name' => $validated['name'],
            'supplier_id' => $validated['supplier_id'] ?? null,
            'unit' => $unit->code,
            'unit_id' => $unit->id,
            'default_warehouse_id' => $defaultWarehouseId,
            'cost' => round((float) $validated['cost'], 4),
            'threshold' => round((float) $validated['threshold'], 3),
            'reorder_level' => round((float) $validated['threshold'], 3),
            'cost_method' => 'fifo',
            'expiry_date' => $validated['expiry_date'] ?? null,
            'expiry_alert_days' => (int) $validated['expiry_alert_days'],
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()
            ->route('inventory.index')
            ->with('success', __('messages.success.ingredient_updated'));
    }

    public function adjustForm(Ingredient $ingredient): View
    {
        $warehouses = $this->inventoryService->activeWarehouses();

        return view('inventory.adjust', [
            'ingredient' => $ingredient,
            'warehouses' => $warehouses,
            'warehouseStocks' => $this->inventoryService->warehouseStockByIngredient((int) $ingredient->id),
            'defaultWarehouseId' => (int) ($ingredient->default_warehouse_id ?: $warehouses->firstWhere('is_default', true)?->id ?: $this->inventoryService->defaultWarehouseId()),
        ]);
    }

    public function adjustStock(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_tab' => ['nullable', 'in:main,branch'],
            'adjustment_type' => ['required', 'in:in,out,set'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        if (in_array($validated['adjustment_type'], ['in', 'out'], true) && (float) $validated['quantity'] <= 0) {
            return back()->withErrors([
                'quantity' => __('validation.min.numeric', ['attribute' => 'quantity', 'min' => 0.001]),
            ])->withInput();
        }

        $this->inventoryService->adjustStock(
            ingredient: $ingredient,
            type: $validated['adjustment_type'],
            quantity: (float) $validated['quantity'],
            warehouseId: (int) $validated['warehouse_id'],
            note: $validated['note'] ?? null,
        );

        return redirect()
            ->route('inventory.index', ['warehouse_tab' => $this->normalizeWarehouseTab((string) ($validated['warehouse_tab'] ?? 'main'))])
            ->with('success', __('messages.success.stock_adjusted'));
    }

    public function audit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_tab' => ['nullable', 'in:main,branch'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'actual' => ['nullable', 'array', 'min:1'],
            'actual.*' => ['nullable', 'numeric', 'min:0'],
            'ingredient_id' => ['nullable', 'integer', 'exists:ingredients,id'],
            'actual_quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        $actual = collect($validated['actual'] ?? [])
            ->mapWithKeys(static fn($qty, $ingredientId): array => [(int) $ingredientId => (float) ($qty ?? 0)])
            ->all();

        if (empty($actual) && isset($validated['ingredient_id'])) {
            $actual = [
                (int) $validated['ingredient_id'] => (float) ($validated['actual_quantity'] ?? 0),
            ];
        }

        if (empty($actual)) {
            return back()->withErrors([
                'actual' => __('validation.required', ['attribute' => 'actual']),
            ]);
        }

        $this->inventoryService->createStockAudit(
            warehouseId: (int) $validated['warehouse_id'],
            actualQuantitiesByIngredientId: $actual,
            note: $validated['notes'] ?? null,
        );

        return redirect()
            ->route('inventory.index', ['warehouse_tab' => $this->normalizeWarehouseTab((string) ($validated['warehouse_tab'] ?? 'main'))])
            ->with('success', __('messages.success.stock_audit_completed'));
    }

    public function transfer(Request $request): RedirectResponse
    {
        if (Warehouse::query()->where('is_active', true)->orWhere('is_default', true)->count() < 2) {
            return back()->withErrors([
                'to_warehouse_id' => __('messages.errors.transfer_requires_multiple_warehouses'),
            ])->withInput();
        }

        $validated = $request->validate([
            'warehouse_tab' => ['nullable', 'in:main,branch'],
            'from_warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'to_warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->inventoryService->transferStock(
            fromWarehouseId: (int) $validated['from_warehouse_id'],
            toWarehouseId: (int) $validated['to_warehouse_id'],
            items: [[
                'ingredient_id' => (int) $validated['ingredient_id'],
                'quantity' => (float) $validated['quantity'],
            ]],
            notes: $validated['notes'] ?? null,
        );

        return redirect()
            ->route('inventory.index', ['warehouse_tab' => $this->normalizeWarehouseTab((string) ($validated['warehouse_tab'] ?? 'main'))])
            ->with('success', __('messages.success.stock_transferred'));
    }

    public function storeWarehouse(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_tab' => ['nullable', 'in:main,branch'],
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:30', 'alpha_dash', Rule::unique('warehouses', 'code')],
            'location' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'is_default' => ['required', 'boolean'],
        ]);

        $setAsDefault = (bool) $validated['is_default'];
        $isActive = $setAsDefault ? true : (bool) $validated['is_active'];
        $code = strtoupper(trim((string) ($validated['code'] ?? '')));

        DB::transaction(function () use ($validated, $setAsDefault, $isActive, $code): void {
            if ($setAsDefault) {
                Warehouse::query()
                    ->where('is_default', true)
                    ->update([
                        'is_default' => false,
                        'updated_at' => now(),
                    ]);
            }

            $warehouse = Warehouse::query()->create([
                'name' => $validated['name'],
                'code' => $code !== '' ? $code : null,
                'location' => $validated['location'] ?? null,
                'notes' => null,
                'is_active' => $isActive,
                'is_default' => $setAsDefault,
            ]);

            if (! Warehouse::query()->where('id', '!=', $warehouse->id)->where('is_default', true)->exists()) {
                $warehouse->update([
                    'is_default' => true,
                    'is_active' => true,
                ]);
            }
        });

        return redirect()
            ->route('inventory.index', ['warehouse_tab' => $this->normalizeWarehouseTab((string) ($validated['warehouse_tab'] ?? 'main'))])
            ->with('success', __('messages.success.warehouse_created'));
    }

    public function updateWarehouse(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_tab' => ['nullable', 'in:main,branch'],
            'name' => ['required', 'string', 'max:120'],
            'code' => [
                'nullable',
                'string',
                'max:30',
                'alpha_dash',
                Rule::unique('warehouses', 'code')->ignore($warehouse->id),
            ],
            'location' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'is_default' => ['required', 'boolean'],
        ]);

        $setAsDefault = (bool) $validated['is_default'];
        $isActive = $setAsDefault ? true : (bool) $validated['is_active'];
        $code = strtoupper(trim((string) ($validated['code'] ?? '')));

        if (! $setAsDefault) {
            $hasOtherDefault = Warehouse::query()
                ->where('id', '!=', $warehouse->id)
                ->where('is_default', true)
                ->exists();

            if (! $hasOtherDefault) {
                $setAsDefault = true;
                $isActive = true;
            }
        }

        DB::transaction(function () use ($warehouse, $validated, $setAsDefault, $isActive, $code): void {
            if ($setAsDefault) {
                Warehouse::query()
                    ->where('id', '!=', $warehouse->id)
                    ->where('is_default', true)
                    ->update([
                        'is_default' => false,
                        'updated_at' => now(),
                    ]);
            }

            $warehouse->update([
                'name' => $validated['name'],
                'code' => $code !== '' ? $code : null,
                'location' => $validated['location'] ?? null,
                'is_active' => $isActive,
                'is_default' => $setAsDefault,
            ]);
        });

        return redirect()
            ->route('inventory.index', ['warehouse_tab' => $this->normalizeWarehouseTab((string) ($validated['warehouse_tab'] ?? 'main'))])
            ->with('success', __('messages.success.warehouse_updated'));
    }

    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        try {
            DB::transaction(function () use ($ingredient): void {
                $this->deleteIngredientLinkedRecords($ingredient);
                $ingredient->delete();
            });
        } catch (QueryException $exception) {
            if ($this->isForeignKeyConstraintViolation($exception)) {
                return back()->with('error', __('messages.errors.cannot_delete_ingredient_with_records'));
            }

            throw $exception;
        }

        return redirect()
            ->route('inventory.index')
            ->with('success', __('messages.success.ingredient_deleted'));
    }

    private function isForeignKeyConstraintViolation(QueryException $exception): bool
    {
        $sqlState = (string) ($exception->errorInfo[0] ?? '');
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);
        $message = strtolower($exception->getMessage());

        return $sqlState === '23000'
            && ($driverCode === 1451 || str_contains($message, 'foreign key constraint'));
    }

    private function deleteIngredientLinkedRecords(Ingredient $ingredient): void
    {
        $ingredientId = (int) $ingredient->id;

        if (Schema::hasTable('recipe_items')) {
            DB::table('recipe_items')
                ->where('ingredient_id', $ingredientId)
                ->delete();
        }

        if (Schema::hasTable('recipe_version_items')) {
            DB::table('recipe_version_items')
                ->where('ingredient_id', $ingredientId)
                ->delete();
        }

        if (Schema::hasTable('purchase_items')) {
            $affectedPurchaseIds = DB::table('purchase_items')
                ->where('ingredient_id', $ingredientId)
                ->pluck('purchase_id')
                ->map(static fn($id): int => (int) $id)
                ->unique()
                ->values()
                ->all();

            DB::table('purchase_items')
                ->where('ingredient_id', $ingredientId)
                ->delete();

            $this->recalculatePurchaseTotals($affectedPurchaseIds);
        }

        if (Schema::hasTable('inventory_stock_transfer_items')) {
            DB::table('inventory_stock_transfer_items')
                ->where('ingredient_id', $ingredientId)
                ->delete();
        }

        if (Schema::hasTable('stock_audit_items')) {
            DB::table('stock_audit_items')
                ->where('ingredient_id', $ingredientId)
                ->delete();
        }
    }

    private function recalculatePurchaseTotals(array $purchaseIds): void
    {
        if ($purchaseIds === [] || ! Schema::hasTable('purchases') || ! Schema::hasTable('purchase_items')) {
            return;
        }

        $normalizedIds = array_values(array_unique(array_map(static fn($id): int => (int) $id, $purchaseIds)));

        if ($normalizedIds === []) {
            return;
        }

        $subtotalByPurchaseId = DB::table('purchase_items')
            ->selectRaw('purchase_id, COALESCE(SUM(line_total), 0) as subtotal')
            ->whereIn('purchase_id', $normalizedIds)
            ->groupBy('purchase_id')
            ->pluck('subtotal', 'purchase_id');

        $purchases = DB::table('purchases')
            ->whereIn('id', $normalizedIds)
            ->get(['id', 'tax_amount', 'discount_amount']);

        $now = now();

        foreach ($purchases as $purchase) {
            $purchaseId = (int) $purchase->id;
            $subtotal = round((float) $subtotalByPurchaseId->get($purchaseId, 0), 2);
            $taxAmount = (float) $purchase->tax_amount;
            $discountAmount = (float) $purchase->discount_amount;
            $total = max($subtotal + $taxAmount - $discountAmount, 0);

            DB::table('purchases')
                ->where('id', $purchaseId)
                ->update([
                    'subtotal' => $subtotal,
                    'total' => round($total, 2),
                    'updated_at' => $now,
                ]);
        }
    }

    private function normalizeWarehouseTab(string $tab): string
    {
        $normalized = strtolower(trim($tab));

        return in_array($normalized, ['main', 'branch'], true) ? $normalized : 'main';
    }

    private function downloadPdfFromView(string $view, array $data, string $fileName, string $fallbackUrl): Response
    {
        $html = app(ViewFactory::class)->make($view, $data)->render();

        $domPdfResponse = $this->downloadPdfWithDompdfHtml($html, $fileName);
        if ($domPdfResponse instanceof Response) {
            return $domPdfResponse;
        }

        $mPdfResponse = $this->downloadPdfWithMpdfHtml($html, $fileName);
        if ($mPdfResponse instanceof Response) {
            return $mPdfResponse;
        }

        $chromiumResponse = $this->downloadPdfWithChromiumHtml($html, $fileName);
        if ($chromiumResponse instanceof Response) {
            return $chromiumResponse;
        }

        return redirect()
            ->to($fallbackUrl)
            ->with('error', app()->getLocale() === 'ar'
                ? 'تعذر إنشاء ملف PDF حالياً. يرجى المحاولة مرة أخرى.'
                : 'Unable to generate PDF right now. Please try again.');
    }

    private function downloadPdfWithMpdfHtml(string $html, string $fileName): ?Response
    {
        try {
            $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'restv2-mpdf';
            File::ensureDirectoryExists($tempDir);

            $mPdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L',
                'tempDir' => $tempDir,
                'default_font' => 'dejavusans',
                'margin_left' => 12,
                'margin_right' => 12,
                'margin_top' => 12,
                'margin_bottom' => 12,
            ]);

            $mPdf->autoScriptToLang = true;
            $mPdf->autoLangToFont = true;

            if (method_exists($mPdf, 'SetDirectionality')) {
                $mPdf->SetDirectionality(app()->getLocale() === 'ar' ? 'rtl' : 'ltr');
            }

            $mPdf->WriteHTML($html);
            $pdfBinary = $mPdf->Output('', Destination::STRING_RETURN);

            if (! is_string($pdfBinary) || $pdfBinary === '') {
                throw new \RuntimeException('mPDF output is empty.');
            }

            $downloadName = str_replace(["\r", "\n", '"'], '', $fileName);

            return response($pdfBinary, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'X-PDF-Engine' => 'mpdf',
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    private function downloadPdfWithDompdfHtml(string $html, string $fileName): ?Response
    {
        try {
            $preparedHtml = $this->prepareHtmlForDompdf($html);

            $pdfBinary = Pdf::setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'isFontSubsettingEnabled' => true,
                'dpi' => 110,
            ])
                ->loadHTML($preparedHtml)
                ->setPaper('a4', 'landscape')
                ->output();

            if (! is_string($pdfBinary) || $pdfBinary === '') {
                throw new \RuntimeException('Dompdf output is empty.');
            }

            $downloadName = str_replace(["\r", "\n", '"'], '', $fileName);

            return response($pdfBinary, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'X-PDF-Engine' => 'dompdf',
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    private function prepareHtmlForDompdf(string $html): string
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $previousUseInternalErrors = libxml_use_internal_errors(true);

        try {
            $loaded = $document->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            if (! $loaded) {
                return $html;
            }

            $root = $document->documentElement;
            if ($root instanceof \DOMNode) {
                $this->shapeArabicTextNodesForDompdf($root);
            }

            $result = $document->saveHTML();
            if (! is_string($result) || $result === '') {
                return $html;
            }

            return (string) preg_replace('/^<\?xml[^>]*>\s*/', '', $result);
        } catch (\Throwable) {
            return $html;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousUseInternalErrors);
        }
    }

    private function shapeArabicTextNodesForDompdf(\DOMNode $node): void
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            $node->nodeValue = $this->shapeArabicTextForDompdf((string) $node->nodeValue);

            return;
        }

        $nodeName = strtolower((string) $node->nodeName);
        if (in_array($nodeName, ['script', 'style'], true)) {
            return;
        }

        if (! $node->hasChildNodes()) {
            return;
        }

        for ($index = 0; $index < $node->childNodes->length; $index++) {
            $child = $node->childNodes->item($index);

            if ($child instanceof \DOMNode) {
                $this->shapeArabicTextNodesForDompdf($child);
            }
        }
    }

    private function shapeArabicTextForDompdf(string $text): string
    {
        $result = preg_replace_callback(
            '/[\x{0621}-\x{064A}\x{0671}-\x{06D3}\x{06FA}-\x{06FF}]+/u',
            fn(array $matches): string => $this->shapeArabicWordForDompdf($matches[0]),
            $text
        );

        return is_string($result) ? $result : $text;
    }

    private function shapeArabicWordForDompdf(string $word): string
    {
        $chars = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);

        if (! is_array($chars) || $chars === []) {
            return $word;
        }

        $shapedChars = [];
        $charsCount = count($chars);

        for ($index = 0; $index < $charsCount; $index++) {
            $char = $chars[$index];
            $codepoint = mb_ord($char, 'UTF-8');
            $forms = self::ARABIC_GLYPH_FORMS[$codepoint] ?? null;

            if (! is_array($forms)) {
                $shapedChars[] = $char;

                continue;
            }

            $prevCodepoint = null;
            if ($index > 0) {
                $prevCodepoint = mb_ord($chars[$index - 1], 'UTF-8');
            }

            $nextCodepoint = null;
            if ($index + 1 < $charsCount) {
                $nextCodepoint = mb_ord($chars[$index + 1], 'UTF-8');
            }

            $joinsPrevious = $prevCodepoint !== null
                && isset(self::ARABIC_CONNECTS_BEFORE[$prevCodepoint])
                && isset(self::ARABIC_CONNECTS_AFTER[$codepoint]);

            $joinsNext = $nextCodepoint !== null
                && isset(self::ARABIC_CONNECTS_BEFORE[$codepoint])
                && isset(self::ARABIC_CONNECTS_AFTER[$nextCodepoint]);

            if ($joinsPrevious && $joinsNext && isset($forms['m'])) {
                $shapedChars[] = $forms['m'];

                continue;
            }

            if ($joinsPrevious && isset($forms['f'])) {
                $shapedChars[] = $forms['f'];

                continue;
            }

            if ($joinsNext && isset($forms['b'])) {
                $shapedChars[] = $forms['b'];

                continue;
            }

            $shapedChars[] = $forms['i'] ?? $char;
        }

        return implode('', array_reverse($shapedChars));
    }

    private function downloadPdfWithChromiumHtml(string $html, string $fileName): ?Response
    {
        $tempHtmlPath = null;
        $tempPdfPath = null;
        $tempProfileDir = null;

        try {
            $tempDir = sys_get_temp_dir();

            $tempHtmlPath = tempnam($tempDir, 'restv2-pdf-html-');
            $tempPdfPath = tempnam($tempDir, 'restv2-pdf-out-');
            $tempProfileDir = $tempDir . DIRECTORY_SEPARATOR . 'restv2-chromium-profile-' . bin2hex(random_bytes(8));

            File::ensureDirectoryExists($tempProfileDir);

            if (! is_string($tempHtmlPath) || ! is_string($tempPdfPath)) {
                throw new \RuntimeException('Unable to allocate temp files for PDF export.');
            }

            File::put($tempHtmlPath, $html);

            $process = new Process([
                $this->resolveChromiumBinary(),
                '--headless',
                '--disable-gpu',
                '--no-sandbox',
                '--disable-dev-shm-usage',
                '--no-first-run',
                '--no-default-browser-check',
                '--disable-background-networking',
                '--user-data-dir=' . $tempProfileDir,
                '--run-all-compositor-stages-before-draw',
                '--virtual-time-budget=10000',
                '--print-to-pdf=' . $tempPdfPath,
                '--print-to-pdf-no-header',
                '--no-pdf-header-footer',
                '--allow-file-access-from-files',
                'file://' . $tempHtmlPath,
            ], null, [
                'HOME' => $tempDir,
                'XDG_CONFIG_HOME' => $tempDir,
                'XDG_CACHE_HOME' => $tempDir,
            ]);

            $process->setTimeout(90);
            $process->run();

            if (! $process->isSuccessful() || ! File::exists($tempPdfPath)) {
                throw new \RuntimeException('Chromium PDF export failed. ' . $process->getErrorOutput());
            }

            $pdfBinary = File::get($tempPdfPath);

            if (! is_string($pdfBinary) || $pdfBinary === '') {
                throw new \RuntimeException('Generated PDF output is empty.');
            }

            File::delete($tempPdfPath);

            $downloadName = str_replace(["\r", "\n", '"'], '', $fileName);

            return response($pdfBinary, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                'Content-Length' => (string) strlen($pdfBinary),
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'X-PDF-Engine' => 'chromium',
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            if ($tempPdfPath && File::exists($tempPdfPath)) {
                File::delete($tempPdfPath);
            }

            return null;
        } finally {
            if ($tempHtmlPath && File::exists($tempHtmlPath)) {
                File::delete($tempHtmlPath);
            }

            if ($tempProfileDir && File::isDirectory($tempProfileDir)) {
                File::deleteDirectory($tempProfileDir);
            }
        }
    }

    private function resolveChromiumBinary(): string
    {
        $envBinary = trim((string) env('CHROMIUM_BINARY'));
        if ($envBinary !== '') {
            return $envBinary;
        }

        $candidates = [
            '/usr/lib/chromium/chromium',
            '/usr/bin/chromium-browser',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/google-chrome',
            '/usr/bin/chromium',
        ];

        foreach ($candidates as $candidate) {
            if (is_executable($candidate)) {
                return $candidate;
            }
        }

        return 'chromium';
    }
}
