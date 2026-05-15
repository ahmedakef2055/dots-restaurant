<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RestaurantTableController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:available,occupied,reserved'],
        ]);

        $tables = RestaurantTable::query()
            ->when($validated['q'] ?? null, function ($query, string $search): void {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($validated['status'] ?? null, function ($query, string $status): void {
                $query->where('status', $status);
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('tables.index', [
            'tables' => $tables,
            'filters' => [
                'q' => $validated['q'] ?? '',
                'status' => $validated['status'] ?? '',
            ],
        ]);
    }

    public function create(): View
    {
        return view('tables.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateTable($request);

        RestaurantTable::query()->create($validated);

        return redirect()->route('tables.index')->with('success', __('messages.success.table_created'));
    }

    public function edit(RestaurantTable $restaurantTable): View
    {
        return view('tables.edit', [
            'tableItem' => $restaurantTable,
        ]);
    }

    public function update(Request $request, RestaurantTable $restaurantTable): RedirectResponse
    {
        $validated = $this->validateTable($request, $restaurantTable->id);

        $restaurantTable->update($validated);

        return redirect()->route('tables.index')->with('success', __('messages.success.table_updated'));
    }

    public function toggleStatus(RestaurantTable $restaurantTable): RedirectResponse
    {
        $next = match ($restaurantTable->status) {
            'available' => 'reserved',
            'reserved'  => 'occupied',
            default     => 'available',
        };

        $restaurantTable->update(['status' => $next]);

        return back()->with('success', __('messages.success.table_status_toggled'));
    }

    public function destroy(RestaurantTable $restaurantTable): RedirectResponse
    {
        $restaurantTable->delete();

        return redirect()->route('tables.index')->with('success', __('messages.success.table_deleted'));
    }

    private function validateTable(Request $request, ?int $tableId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('restaurant_tables', 'name')->ignore($tableId)],
            'capacity' => ['required', 'integer', 'min:1', 'max:30'],
            'status' => ['required', 'in:available,occupied,reserved'],
        ]);
    }
}
