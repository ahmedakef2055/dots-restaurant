<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\PurchaseApprovalLog;
use App\Models\Supplier;
use App\Models\User;
use App\Notifications\PurchaseRequestReviewedNotification;
use App\Support\PdfExportRenderer;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PurchaseController extends Controller
{
    private const PURCHASES_PDF_LIMIT = 600;

    public function __construct(private readonly InventoryService $inventoryService) {}

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'request_type' => ['nullable', Rule::in(['inventory', 'general_expense'])],
            'approval_status' => ['nullable', Rule::in(['pending', 'approved', 'rejected', 'completed'])],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $purchases = Purchase::query()
            ->with(['supplier:id,name', 'user:id,name', 'approvalUser:id,name'])
            ->withCount('items')
            ->when($validated['q'] ?? null, fn($query, string $search) => $query->where('purchase_number', 'like', "%{$search}%"))
            ->when($validated['supplier_id'] ?? null, fn($query, int $supplierId) => $query->where('supplier_id', $supplierId))
            ->when($validated['request_type'] ?? null, fn($query, string $type) => $query->where('request_type', $type))
            ->when($validated['approval_status'] ?? null, function ($query, string $approvalStatus) {
                if ($approvalStatus === 'completed') {
                    return $query->where('status', 'completed');
                }

                if ($approvalStatus === 'approved') {
                    return $query
                        ->where('approval_status', 'approved')
                        ->where(function ($statusQuery) {
                            $statusQuery
                                ->whereNull('status')
                                ->orWhere('status', '!=', 'completed');
                        });
                }

                return $query->where('approval_status', $approvalStatus);
            })
            ->when($validated['from'] ?? null, fn($query, string $from) => $query->whereDate('purchase_date', '>=', $from))
            ->when($validated['to'] ?? null, fn($query, string $to) => $query->whereDate('purchase_date', '<=', $to))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $suppliers = Supplier::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('purchases.index', [
            'purchases' => $purchases,
            'suppliers' => $suppliers,
            'filters' => [
                'q' => $validated['q'] ?? '',
                'supplier_id' => $validated['supplier_id'] ?? '',
                'request_type' => $validated['request_type'] ?? '',
                'approval_status' => $validated['approval_status'] ?? '',
                'from' => $validated['from'] ?? '',
                'to' => $validated['to'] ?? '',
            ],
        ]);
    }

    public function exportPdf(Request $request, PdfExportRenderer $pdfExportRenderer): Response
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'request_type' => ['nullable', Rule::in(['inventory', 'general_expense'])],
            'approval_status' => ['nullable', Rule::in(['pending', 'approved', 'rejected', 'completed'])],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $baseQuery = Purchase::query()
            ->with(['supplier:id,name', 'user:id,name'])
            ->withCount('items')
            ->when($validated['q'] ?? null, fn($query, string $search) => $query->where('purchase_number', 'like', "%{$search}%"))
            ->when($validated['supplier_id'] ?? null, fn($query, int $supplierId) => $query->where('supplier_id', $supplierId))
            ->when($validated['request_type'] ?? null, fn($query, string $type) => $query->where('request_type', $type))
            ->when($validated['approval_status'] ?? null, function ($query, string $approvalStatus) {
                if ($approvalStatus === 'completed') {
                    return $query->where('status', 'completed');
                }

                if ($approvalStatus === 'approved') {
                    return $query
                        ->where('approval_status', 'approved')
                        ->where(function ($statusQuery) {
                            $statusQuery
                                ->whereNull('status')
                                ->orWhere('status', '!=', 'completed');
                        });
                }

                return $query->where('approval_status', $approvalStatus);
            })
            ->when($validated['from'] ?? null, fn($query, string $from) => $query->whereDate('purchase_date', '>=', $from))
            ->when($validated['to'] ?? null, fn($query, string $to) => $query->whereDate('purchase_date', '<=', $to))
            ->latest('id');

        $totalCount = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->limit(self::PURCHASES_PDF_LIMIT)
            ->get([
                'id',
                'purchase_number',
                'request_type',
                'supplier_id',
                'expense_title',
                'purchase_date',
                'approval_status',
                'status',
                'payment_method',
                'total',
                'user_id',
            ])
            ->map(static function (Purchase $purchase): array {
                $requestType = strtolower((string) ($purchase->request_type ?: 'inventory'));
                $approvalStatus = strtolower((string) ($purchase->approval_status ?: 'pending'));
                $purchaseStatus = strtolower((string) ($purchase->status ?: 'pending'));
                $displayStatus = match (true) {
                    $purchaseStatus === 'completed' => 'completed',
                    $approvalStatus === 'approved' => 'approved',
                    $approvalStatus === 'rejected' => 'rejected',
                    default => 'pending',
                };

                $supplierLabel = $requestType === 'general_expense'
                    ? trim((string) ($purchase->expense_title ?? ''))
                    : trim((string) ($purchase->supplier?->name ?? ''));

                return [
                    'purchase_number' => (string) $purchase->purchase_number,
                    'request_type' => $requestType,
                    'supplier_label' => $supplierLabel !== '' ? $supplierLabel : '-',
                    'purchase_date' => $purchase->purchase_date?->format('Y-m-d') ?? '',
                    'approval_status' => $displayStatus,
                    'payment_method' => strtolower((string) ($purchase->payment_method ?? 'cash')) === 'credit' ? 'credit' : 'cash',
                    'items_count' => (int) ($purchase->items_count ?? 0),
                    'total' => (float) ($purchase->total ?? 0),
                    'user_name' => trim((string) ($purchase->user?->name ?? '')),
                ];
            })
            ->values()
            ->all();

        $supplierId = $validated['supplier_id'] ?? null;
        $supplierName = null;

        if ($supplierId !== null) {
            $supplierName = (string) (Supplier::query()->whereKey((int) $supplierId)->value('name') ?? '');
        }

        $exportedCount = count($rows);
        $isTruncated = $totalCount > $exportedCount;

        $fallbackFilters = array_filter([
            'q' => $validated['q'] ?? null,
            'supplier_id' => $supplierId,
            'request_type' => $validated['request_type'] ?? null,
            'approval_status' => $validated['approval_status'] ?? null,
            'from' => $validated['from'] ?? null,
            'to' => $validated['to'] ?? null,
        ], static fn($value): bool => $value !== null && $value !== '');

        return $pdfExportRenderer->downloadPdfFromView(
            'purchases.exports.purchases-pdf',
            [
                'rows' => $rows,
                'filters' => [
                    'q' => (string) ($validated['q'] ?? ''),
                    'supplier_id' => $supplierId !== null ? (int) $supplierId : null,
                    'supplier_name' => $supplierName,
                    'request_type' => (string) ($validated['request_type'] ?? ''),
                    'approval_status' => (string) ($validated['approval_status'] ?? ''),
                    'from' => (string) ($validated['from'] ?? ''),
                    'to' => (string) ($validated['to'] ?? ''),
                ],
                'totalCount' => $totalCount,
                'exportedCount' => $exportedCount,
                'isTruncated' => $isTruncated,
                'generatedAt' => now(),
            ],
            'purchases-report-' . now()->format('Ymd_His') . '.pdf',
            route('purchases.index', $fallbackFilters)
        );
    }

    public function create(): View
    {
        $suppliers = Supplier::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $ingredients = Ingredient::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'unit']);
        $supportsPaymentMethod = Schema::hasTable('purchases') && Schema::hasColumn('purchases', 'payment_method');

        return view('purchases.create', [
            'suppliers' => $suppliers,
            'ingredients' => $ingredients,
            'supportsPaymentMethod' => $supportsPaymentMethod,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $supportsPaymentMethod = Schema::hasTable('purchases') && Schema::hasColumn('purchases', 'payment_method');

        $validationRules = [
            'request_type' => ['required', Rule::in(['inventory', 'general_expense'])],
            'supplier_id' => ['required_if:request_type,inventory', 'nullable', 'integer', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'expense_title' => ['required_if:request_type,general_expense', 'nullable', 'string', 'max:180'],
            'expense_amount' => ['required_if:request_type,general_expense', 'nullable', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required_if:request_type,inventory', 'nullable', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required_if:request_type,inventory', 'nullable', 'integer', 'exists:ingredients,id'],
            'items.*.quantity' => ['required_if:request_type,inventory', 'nullable', 'numeric', 'min:0.001'],
            'items.*.unit_cost' => ['required_if:request_type,inventory', 'nullable', 'numeric', 'min:0.01'],
            'items.*.expiry_date' => ['nullable', 'date'],
        ];

        if ($supportsPaymentMethod) {
            $validationRules['payment_method'] = ['required', 'in:cash,credit'];
        }

        $validated = $request->validate($validationRules);

        $requestType = (string) ($validated['request_type'] ?? 'inventory');
        $taxAmount = $requestType === 'inventory' ? (float) ($validated['tax_amount'] ?? 0) : 0;
        $discountAmount = $requestType === 'inventory' ? (float) ($validated['discount_amount'] ?? 0) : 0;

        $purchase = DB::transaction(function () use ($validated, $requestType, $taxAmount, $discountAmount, $supportsPaymentMethod) {
            $purchaseNumber = $this->generatePurchaseNumber();
            $warehouseId = $requestType === 'inventory'
                ? $this->inventoryService->defaultWarehouseId()
                : null;
            $supplierId = $requestType === 'inventory'
                ? (int) $validated['supplier_id']
                : null;

            $preparedItems = [];
            $subtotal = 0.0;

            if ($requestType === 'inventory') {
                $ingredientIds = collect($validated['items'] ?? [])->pluck('ingredient_id')->unique()->values();

                $ingredients = Ingredient::query()
                    ->whereIn('id', $ingredientIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($validated['items'] as $item) {
                    $ingredient = $ingredients->get($item['ingredient_id']);

                    if (! $ingredient) {
                        throw ValidationException::withMessages([
                            'items' => [__('messages.errors.recipe_ingredient_not_found')],
                        ]);
                    }

                    $quantity = (float) $item['quantity'];
                    $unitCost = (float) $item['unit_cost'];
                    $lineTotal = $quantity * $unitCost;

                    $subtotal += $lineTotal;

                    $preparedItems[] = [
                        'ingredient_id' => (int) $ingredient->id,
                        'ingredient_name' => $ingredient->name,
                        'quantity' => round($quantity, 3),
                        'unit_cost' => round($unitCost, 2),
                        'line_total' => round($lineTotal, 2),
                        'expiry_date' => $item['expiry_date'] ?? null,
                        'warehouse_id' => $warehouseId,
                    ];
                }
            } else {
                $subtotal = round((float) $validated['expense_amount'], 2);
            }

            $total = $requestType === 'inventory'
                ? max(($subtotal + $taxAmount) - $discountAmount, 0)
                : $subtotal;

            $purchasePayload = [
                'purchase_number' => $purchaseNumber,
                'supplier_id' => $supplierId,
                'warehouse_id' => $warehouseId,
                'user_id' => Auth::id(),
                'purchase_date' => $validated['purchase_date'],
                'request_type' => $requestType,
                'expense_title' => $requestType === 'general_expense' ? $validated['expense_title'] : null,
                'expense_invoice_reference' => null,
                'expense_amount' => $requestType === 'general_expense' ? round((float) $validated['expense_amount'], 2) : null,
                'subtotal' => round($subtotal, 2),
                'tax_amount' => round($taxAmount, 2),
                'discount_amount' => round($discountAmount, 2),
                'total' => round($total, 2),
                'status' => 'pending',
                'approval_status' => 'pending',
                'approval_comment' => null,
                'approval_user_id' => null,
                'approval_at' => null,
                'inventory_applied_at' => null,
                'notes' => $validated['notes'] ?? null,
            ];

            if ($supportsPaymentMethod) {
                $purchasePayload['payment_method'] = $validated['payment_method'];
            }

            $purchase = Purchase::query()->create($purchasePayload);

            foreach ($preparedItems as $prepared) {
                $purchase->items()->create([
                    'ingredient_id' => $prepared['ingredient_id'],
                    'warehouse_id' => $prepared['warehouse_id'],
                    'ingredient_name' => $prepared['ingredient_name'],
                    'unit_cost' => $prepared['unit_cost'],
                    'quantity' => $prepared['quantity'],
                    'expiry_date' => $prepared['expiry_date'],
                    'line_total' => $prepared['line_total'],
                ]);
            }

            return $purchase;
        });

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', __('messages.success.purchase_request_created'));
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load([
            'supplier',
            'user',
            'approvalUser',
            'completedByUser',
            'items',
            'approvalLogs' => fn($query) => $query
                ->with(['actor:id,name', 'previousApprovalUser:id,name', 'newApprovalUser:id,name'])
                ->orderByDesc('acted_at')
                ->orderByDesc('id'),
        ]);
        $supportsInvoiceFile = Schema::hasTable('purchases') && Schema::hasColumn('purchases', 'invoice_file_path');
        $supportsInvoiceNumber = Schema::hasTable('purchases') && Schema::hasColumn('purchases', 'invoice_number');

        return view('purchases.show', [
            'purchase' => $purchase,
            'supportsInvoiceFile' => $supportsInvoiceFile,
            'supportsInvoiceNumber' => $supportsInvoiceNumber,
        ]);
    }

    public function updateUploadedInvoice(Request $request, Purchase $purchase): RedirectResponse
    {
        $supportsInvoiceFile = Schema::hasTable('purchases') && Schema::hasColumn('purchases', 'invoice_file_path');

        if (! $supportsInvoiceFile) {
            return back()->with('error', __('messages.errors.purchase_invoice_feature_unavailable'));
        }

        if (strtolower((string) ($purchase->status ?: 'pending')) !== 'completed') {
            return back()->with('error', __('messages.errors.purchase_invoice_replace_only_after_completion'));
        }

        $validated = $request->validate([
            'invoice_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:51200'],
        ]);

        $newInvoicePath = $request->file('invoice_file')->store('purchase-invoices', 'local');
        $oldInvoicePath = (string) ($purchase->invoice_file_path ?? '');

        try {
            $purchase->update([
                'invoice_file_path' => $newInvoicePath,
            ]);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($newInvoicePath);

            throw $exception;
        }

        if ($oldInvoicePath !== '' && $oldInvoicePath !== $newInvoicePath) {
            Storage::disk('local')->delete($oldInvoicePath);
        }

        return back()->with('success', __('messages.success.purchase_invoice_file_uploaded'));
    }

    public function approve(Request $request, Purchase $purchase): RedirectResponse
    {
        $validated = $request->validate([
            'approval_comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $approvalComment = trim((string) ($validated['approval_comment'] ?? ''));
        $notificationRecipientId = null;
        $notificationPayload = null;

        DB::transaction(function () use ($purchase, $approvalComment, &$notificationRecipientId, &$notificationPayload): void {
            $lockedPurchase = Purchase::query()
                ->whereKey($purchase->id)
                ->with('items')
                ->lockForUpdate()
                ->firstOrFail();

            $previousApprovalStatus = strtolower((string) ($lockedPurchase->approval_status ?: 'pending'));
            $previousStatus = strtolower((string) ($lockedPurchase->status ?: 'pending'));
            $previousApprovalUserId = $lockedPurchase->approval_user_id ? (int) $lockedPurchase->approval_user_id : null;
            $previousApprovalAt = $lockedPurchase->approval_at;
            $previousApprovalComment = $lockedPurchase->approval_comment;

            if ($previousApprovalStatus !== 'pending') {
                throw ValidationException::withMessages([
                    'approval_comment' => [__('messages.errors.purchase_already_reviewed')],
                ]);
            }

            $actionAt = now();

            $lockedPurchase->approval_status = 'approved';
            $lockedPurchase->approval_comment = $approvalComment !== '' ? $approvalComment : null;
            $lockedPurchase->approval_user_id = Auth::id();
            $lockedPurchase->approval_at = $actionAt;
            $lockedPurchase->status = 'approved';
            $lockedPurchase->save();

            PurchaseApprovalLog::query()->create([
                'purchase_id' => (int) $lockedPurchase->id,
                'action' => 'approve',
                'previous_approval_status' => $previousApprovalStatus,
                'new_approval_status' => 'approved',
                'previous_status' => $previousStatus,
                'new_status' => 'approved',
                'previous_approval_user_id' => $previousApprovalUserId,
                'new_approval_user_id' => Auth::id(),
                'previous_approval_at' => $previousApprovalAt,
                'new_approval_at' => $lockedPurchase->approval_at,
                'previous_approval_comment' => $previousApprovalComment,
                'new_approval_comment' => $lockedPurchase->approval_comment,
                'acted_by_user_id' => Auth::id(),
                'acted_at' => $actionAt,
            ]);

            $notificationRecipientId = $lockedPurchase->user_id ? (int) $lockedPurchase->user_id : null;
            $notificationPayload = [
                'purchase_id' => (int) $lockedPurchase->id,
                'purchase_number' => (string) $lockedPurchase->purchase_number,
                'action' => 'approved',
                'reviewed_by' => (string) (Auth::user()?->name ?? ''),
                'approval_comment' => $lockedPurchase->approval_comment,
                'reviewed_at' => $actionAt->toIso8601String(),
            ];
        });

        if ($notificationRecipientId && $notificationPayload) {
            $recipient = User::query()->find((int) $notificationRecipientId);

            if ($recipient) {
                $recipient->notify(new PurchaseRequestReviewedNotification(
                    purchaseId: (int) $notificationPayload['purchase_id'],
                    purchaseNumber: (string) $notificationPayload['purchase_number'],
                    action: (string) $notificationPayload['action'],
                    reviewedBy: (string) ($notificationPayload['reviewed_by'] ?? ''),
                    approvalComment: (string) ($notificationPayload['approval_comment'] ?? ''),
                    reviewedAt: (string) ($notificationPayload['reviewed_at'] ?? ''),
                ));
            }
        }

        return back()->with('success', __('messages.success.purchase_approved'));
    }

    public function reject(Request $request, Purchase $purchase): RedirectResponse
    {
        $validated = $request->validate([
            'approval_comment' => ['required', 'string', 'max:1000'],
        ]);

        $notificationRecipientId = null;
        $notificationPayload = null;

        DB::transaction(function () use ($purchase, $validated, &$notificationRecipientId, &$notificationPayload): void {
            $lockedPurchase = Purchase::query()
                ->whereKey($purchase->id)
                ->lockForUpdate()
                ->firstOrFail();

            $previousApprovalStatus = strtolower((string) ($lockedPurchase->approval_status ?: 'pending'));
            $previousStatus = strtolower((string) ($lockedPurchase->status ?: 'pending'));
            $previousApprovalUserId = $lockedPurchase->approval_user_id ? (int) $lockedPurchase->approval_user_id : null;
            $previousApprovalAt = $lockedPurchase->approval_at;
            $previousApprovalComment = $lockedPurchase->approval_comment;

            if ($previousApprovalStatus !== 'pending') {
                throw ValidationException::withMessages([
                    'approval_comment' => [__('messages.errors.purchase_already_reviewed')],
                ]);
            }

            $actionAt = now();

            $lockedPurchase->approval_status = 'rejected';
            $lockedPurchase->approval_comment = trim((string) $validated['approval_comment']);
            $lockedPurchase->approval_user_id = Auth::id();
            $lockedPurchase->approval_at = $actionAt;
            $lockedPurchase->status = 'rejected';
            $lockedPurchase->save();

            PurchaseApprovalLog::query()->create([
                'purchase_id' => (int) $lockedPurchase->id,
                'action' => 'reject',
                'previous_approval_status' => $previousApprovalStatus,
                'new_approval_status' => 'rejected',
                'previous_status' => $previousStatus,
                'new_status' => 'rejected',
                'previous_approval_user_id' => $previousApprovalUserId,
                'new_approval_user_id' => Auth::id(),
                'previous_approval_at' => $previousApprovalAt,
                'new_approval_at' => $lockedPurchase->approval_at,
                'previous_approval_comment' => $previousApprovalComment,
                'new_approval_comment' => $lockedPurchase->approval_comment,
                'acted_by_user_id' => Auth::id(),
                'acted_at' => $actionAt,
            ]);

            $notificationRecipientId = $lockedPurchase->user_id ? (int) $lockedPurchase->user_id : null;
            $notificationPayload = [
                'purchase_id' => (int) $lockedPurchase->id,
                'purchase_number' => (string) $lockedPurchase->purchase_number,
                'action' => 'rejected',
                'reviewed_by' => (string) (Auth::user()?->name ?? ''),
                'approval_comment' => $lockedPurchase->approval_comment,
                'reviewed_at' => $actionAt->toIso8601String(),
            ];
        });

        if ($notificationRecipientId && $notificationPayload) {
            $recipient = User::query()->find((int) $notificationRecipientId);

            if ($recipient) {
                $recipient->notify(new PurchaseRequestReviewedNotification(
                    purchaseId: (int) $notificationPayload['purchase_id'],
                    purchaseNumber: (string) $notificationPayload['purchase_number'],
                    action: (string) $notificationPayload['action'],
                    reviewedBy: (string) ($notificationPayload['reviewed_by'] ?? ''),
                    approvalComment: (string) ($notificationPayload['approval_comment'] ?? ''),
                    reviewedAt: (string) ($notificationPayload['reviewed_at'] ?? ''),
                ));
            }
        }

        return back()->with('success', __('messages.success.purchase_rejected'));
    }

    public function complete(Request $request, Purchase $purchase): RedirectResponse
    {
        $supportsInvoiceFile = Schema::hasTable('purchases') && Schema::hasColumn('purchases', 'invoice_file_path');
        $supportsInvoiceNumber = Schema::hasTable('purchases') && Schema::hasColumn('purchases', 'invoice_number');
        $supportsCompletedBy = Schema::hasTable('purchases') && Schema::hasColumn('purchases', 'completed_by_user_id');
        $supportsCompletedAt = Schema::hasTable('purchases') && Schema::hasColumn('purchases', 'completed_at');

        if (! $supportsInvoiceFile || ! $supportsInvoiceNumber || ! $supportsCompletedBy || ! $supportsCompletedAt) {
            return back()->with('error', __('messages.errors.purchase_completion_feature_unavailable'));
        }

        if ((int) ($purchase->user_id ?? 0) !== (int) Auth::id()) {
            return back()->with('error', __('messages.errors.purchase_completion_only_request_owner'));
        }

        $validated = $request->validate([
            'invoice_number' => ['required', 'string', 'max:120'],
            'invoice_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:51200'],
        ]);

        $uploadedInvoicePath = $request->file('invoice_file')->store('purchase-invoices', 'local');

        try {
            DB::transaction(function () use ($purchase, $validated, $uploadedInvoicePath): void {
                $lockedPurchase = Purchase::query()
                    ->whereKey($purchase->id)
                    ->with('items')
                    ->lockForUpdate()
                    ->firstOrFail();

                if (strtolower((string) ($lockedPurchase->approval_status ?: 'pending')) !== 'approved') {
                    throw ValidationException::withMessages([
                        'invoice_number' => [__('messages.errors.purchase_completion_requires_approval')],
                    ]);
                }

                if (strtolower((string) ($lockedPurchase->status ?: 'pending')) === 'completed') {
                    throw ValidationException::withMessages([
                        'invoice_number' => [__('messages.errors.purchase_already_completed')],
                    ]);
                }

                $oldInvoicePath = (string) ($lockedPurchase->invoice_file_path ?? '');

                if (strtolower((string) ($lockedPurchase->request_type ?: 'inventory')) === 'inventory' && $lockedPurchase->inventory_applied_at === null) {
                    $warehouseId = (int) ($lockedPurchase->warehouse_id ?: $this->inventoryService->defaultWarehouseId());

                    $ingredientIds = $lockedPurchase->items
                        ->pluck('ingredient_id')
                        ->filter(static fn($id): bool => (int) $id > 0)
                        ->unique()
                        ->values();

                    $ingredients = Ingredient::query()
                        ->whereIn('id', $ingredientIds)
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');

                    foreach ($lockedPurchase->items as $item) {
                        $ingredient = $ingredients->get((int) $item->ingredient_id);

                        if (! $ingredient) {
                            throw ValidationException::withMessages([
                                'invoice_number' => [__('messages.errors.recipe_ingredient_not_found')],
                            ]);
                        }

                        if ($lockedPurchase->supplier_id) {
                            $ingredient->update([
                                'supplier_id' => (int) $lockedPurchase->supplier_id,
                                'default_warehouse_id' => $warehouseId,
                            ]);
                        }

                        $this->inventoryService->addStock(
                            ingredient: $ingredient,
                            quantity: (float) $item->quantity,
                            unitCost: (float) $item->unit_cost,
                            warehouseId: $warehouseId,
                            expiryDate: $item->expiry_date?->toDateString(),
                            action: 'add',
                            adjustmentType: 'in',
                            note: __('messages.notes.received_via_purchase', [
                                'purchase_number' => $lockedPurchase->purchase_number,
                            ]),
                            referenceType: Purchase::class,
                            referenceId: (int) $lockedPurchase->id,
                        );
                    }

                    $lockedPurchase->inventory_applied_at = now();
                }

                $lockedPurchase->invoice_number = trim((string) $validated['invoice_number']);
                $lockedPurchase->invoice_file_path = $uploadedInvoicePath;
                $lockedPurchase->status = 'completed';
                $lockedPurchase->completed_by_user_id = Auth::id();
                $lockedPurchase->completed_at = now();

                if ($lockedPurchase->request_type === 'general_expense') {
                    $lockedPurchase->expense_invoice_reference = $lockedPurchase->invoice_number;
                }

                $lockedPurchase->save();

                if ($oldInvoicePath !== '' && $oldInvoicePath !== $uploadedInvoicePath) {
                    Storage::disk('local')->delete($oldInvoicePath);
                }
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($uploadedInvoicePath);

            throw $exception;
        }

        return back()->with('success', __('messages.success.purchase_completed'));
    }

    public function invoice(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'user', 'items']);

        return view('purchases.invoice', [
            'purchase' => $purchase,
        ]);
    }

    public function directPrint(Purchase $purchase, \App\Services\PrintService $printService): \Illuminate\Http\JsonResponse
    {
        $purchase->load(['supplier', 'user', 'items']);

        $html = view('purchases.invoice', [
            'purchase'      => $purchase,
            'isDirectPrint' => true,
        ])->render();

        \App\Models\PrintJob::create([
            'printer_type' => 'cashier',
            'payload'      => $printService->buildHtmlBase64($html),
            'payload_type' => 'base64',
            'status'       => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sent to printer successfully',
        ]);
    }

    public function viewUploadedInvoice(Purchase $purchase): BinaryFileResponse
    {
        $invoiceFilePath = $this->resolveInvoiceFilePath($purchase);

        return response()->file(Storage::disk('local')->path($invoiceFilePath));
    }

    public function downloadUploadedInvoice(Purchase $purchase): BinaryFileResponse
    {
        $invoiceFilePath = $this->resolveInvoiceFilePath($purchase);
        $extension = pathinfo($invoiceFilePath, PATHINFO_EXTENSION);
        $downloadName = Str::slug((string) $purchase->purchase_number) . '-supplier-invoice';

        if ($extension !== '') {
            $downloadName .= '.' . $extension;
        }

        return response()->download(Storage::disk('local')->path($invoiceFilePath), $downloadName);
    }

    private function generatePurchaseNumber(): string
    {
        do {
            $purchaseNumber = 'PUR-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));
        } while (Purchase::query()->where('purchase_number', $purchaseNumber)->exists());

        return $purchaseNumber;
    }

    private function resolveInvoiceFilePath(Purchase $purchase): string
    {
        $supportsInvoiceFile = Schema::hasTable('purchases') && Schema::hasColumn('purchases', 'invoice_file_path');
        $invoiceFilePath = (string) ($purchase->invoice_file_path ?? '');

        abort_unless($supportsInvoiceFile && $invoiceFilePath !== '' && Storage::disk('local')->exists($invoiceFilePath), 404);

        return $invoiceFilePath;
    }
}
