<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Support\PdfExportRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    private const CUSTOMERS_PDF_LIMIT = 600;

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $customers = Customer::query()
            ->withCount([
                'orders as orders_count' => static fn($query) => $query->where('status', '!=', 'cancelled'),
            ])
            ->withSum([
                'orders as total_spent' => static fn($query) => $query->where('status', '!=', 'cancelled'),
            ], 'total')
            ->withMax([
                'orders as last_order_at' => static fn($query) => $query->where('status', '!=', 'cancelled'),
            ], 'created_at')
            ->when($validated['q'] ?? null, function ($query, string $search) {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'filters' => [
                'q' => $validated['q'] ?? '',
            ],
        ]);
    }

    public function exportPdf(Request $request, PdfExportRenderer $pdfExportRenderer): Response
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $baseQuery = Customer::query()
            ->withCount([
                'orders as orders_count' => static fn($query) => $query->where('status', '!=', 'cancelled'),
            ])
            ->withSum([
                'orders as total_spent' => static fn($query) => $query->where('status', '!=', 'cancelled'),
            ], 'total')
            ->withMax([
                'orders as last_order_at' => static fn($query) => $query->where('status', '!=', 'cancelled'),
            ], 'created_at')
            ->when($validated['q'] ?? null, function ($query, string $search) {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest('id');

        $totalCount = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->limit(self::CUSTOMERS_PDF_LIMIT)
            ->get([
                'id',
                'first_name',
                'phone',
                'customer_type',
            ])
            ->map(static function (Customer $customer): array {
                return [
                    'name' => (string) $customer->full_name,
                    'phone' => trim((string) ($customer->phone ?? '')),
                    'customer_type' => strtolower((string) ($customer->customer_type ?? 'normal')) === 'vip' ? 'vip' : 'normal',
                    'orders_count' => (int) ($customer->orders_count ?? 0),
                    'total_spent' => (float) ($customer->total_spent ?? 0),
                    'last_order_at' => $customer->last_order_at ? (string) $customer->last_order_at : '',
                ];
            })
            ->values()
            ->all();

        $exportedCount = count($rows);
        $isTruncated = $totalCount > $exportedCount;

        $fallbackFilters = array_filter([
            'q' => $validated['q'] ?? null,
        ], static fn($value): bool => $value !== null && $value !== '');

        return $pdfExportRenderer->downloadPdfFromView(
            'customers.exports.customers-pdf',
            [
                'rows' => $rows,
                'filters' => [
                    'q' => (string) ($validated['q'] ?? ''),
                ],
                'totalCount' => $totalCount,
                'exportedCount' => $exportedCount,
                'isTruncated' => $isTruncated,
                'generatedAt' => now(),
            ],
            'customers-report-' . now()->format('Ymd_His') . '.pdf',
            route('customers.index', $fallbackFilters)
        );
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCustomer($request);

        Customer::query()->create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', __('messages.success.customer_created'));
    }

    public function show(Customer $customer): View
    {
        $recentOrders = $customer->orders()
            ->withCount('items')
            ->latest('order_serial')
            ->limit(8)
            ->get(['order_serial', 'order_number', 'order_type', 'status', 'total', 'created_at']);

        $summary = $customer->orders()
            ->where('status', '!=', 'cancelled')
            ->selectRaw('COUNT(*) as orders_count, COALESCE(SUM(total), 0) as total_spent')
            ->first();

        $favoriteMainCategory = DB::table('orders as o')
            ->join('order_items as oi', 'oi.order_id', '=', 'o.order_serial')
            ->leftJoin('products as p', 'p.id', '=', 'oi.product_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->leftJoin('categories as pc', 'pc.id', '=', 'c.parent_id')
            ->where('o.customer_id', $customer->id)
            ->where('o.status', '!=', 'cancelled')
            ->selectRaw("COALESCE(CASE WHEN c.type = 'main' THEN c.name WHEN pc.name IS NOT NULL THEN pc.name END, 'Uncategorized') as main_category_name")
            ->selectRaw('SUM(oi.quantity) as total_qty')
            ->groupBy('main_category_name')
            ->orderByDesc('total_qty')
            ->first();

        return view('customers.show', [
            'customer' => $customer,
            'recentOrders' => $recentOrders,
            'ordersCount' => (int) ($summary->orders_count ?? 0),
            'totalSpent' => (float) ($summary->total_spent ?? 0),
            'favoriteMainCategory' => (string) ($favoriteMainCategory->main_category_name ?? '-'),
        ]);
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', [
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $this->validateCustomer($request, $customer->id);

        $customer->update($validated);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', __('messages.success.customer_updated'));
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', __('messages.success.customer_deleted'));
    }

    private function validateCustomer(Request $request, ?int $customerId = null): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'phone' => [
                'required',
                'string',
                'max:30',
                Rule::unique('customers', 'phone')->ignore($customerId),
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'customer_type' => ['required', 'in:normal,vip'],
        ]);
    }
}
