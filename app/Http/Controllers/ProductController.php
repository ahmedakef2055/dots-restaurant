<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'preparation_station' => ['nullable', 'in:kitchen,bar'],
        ]);

        session(['products.index_url' => $request->fullUrl()]);

        $categories = $this->categoriesForFilters();

        $products = Product::query()
            ->with(['category:id,name,type,parent_id', 'category.parent:id,name'])
            ->when($validated['q'] ?? null, function ($query, string $search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($validated['category_id'] ?? null, function ($query, int $categoryId): void {
                $query->where('category_id', $categoryId);
            })
            ->when($validated['preparation_station'] ?? null, function ($query, string $station): void {
                $query->where('preparation_station', $station);
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'q' => $validated['q'] ?? '',
                'category_id' => isset($validated['category_id']) ? (string) $validated['category_id'] : '',
                'preparation_station' => $validated['preparation_station'] ?? '',
            ],
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'categories' => $this->categoriesForForms(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request, null);

        Product::query()->create([
            'name' => $validated['name'],
            'barcode' => $validated['barcode'] ?? null,
            'price' => round((float) $validated['price'], 2),
            'category_id' => (int) $validated['category_id'],
            'preparation_station' => $validated['preparation_station'],
            'description' => $validated['description'] ?? null,
            'stock' => 0,
            'is_active' => true,
        ]);

        return redirect()
            ->to(session('products.index_url', route('products.index')))
            ->with('success', __('messages.success.product_created'));
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => $this->categoriesForForms(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product);

        $product->update([
            'name' => $validated['name'],
            'barcode' => $validated['barcode'] ?? null,
            'price' => round((float) $validated['price'], 2),
            'category_id' => (int) $validated['category_id'],
            'preparation_station' => $validated['preparation_station'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->to(session('products.index_url', route('products.index')))
            ->with('success', __('messages.success.product_updated'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        try {
            $product->delete();
        } catch (QueryException $exception) {
            if ($this->isDeleteConstraintViolation($exception)) {
                return back()->with('error', __('messages.errors.cannot_delete_product_with_orders'));
            }

            throw $exception;
        }

        return redirect()
            ->route('products.index')
            ->with('success', __('messages.success.product_deleted'));
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $productId = $product?->id;

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('products', 'barcode')->ignore($productId)],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'preparation_station' => ['required', 'in:kitchen,bar'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function categoriesForForms()
    {
        return Category::query()
            ->with('parent:id,name')
            ->orderByRaw("CASE WHEN type = 'main' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'parent_id']);
    }

    private function categoriesForFilters()
    {
        return Category::query()
            ->whereHas('products')
            ->with('parent:id,name')
            ->orderByRaw("CASE WHEN type = 'main' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'parent_id']);
    }

    private function isDeleteConstraintViolation(QueryException $exception): bool
    {
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);

        return $driverCode === 1451;
    }
}
