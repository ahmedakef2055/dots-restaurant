<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', 'in:main,sub'],
        ]);

        $categories = Category::query()
            ->with('parent:id,name')
            ->withCount('children')
            ->when($validated['q'] ?? null, function ($query, string $search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhereHas('parent', function ($parentQuery) use ($search): void {
                            $parentQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($validated['type'] ?? null, function ($query, string $type): void {
                $query->where('type', $type);
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('categories.index', [
            'categories' => $categories,
            'filters' => [
                'q' => $validated['q'] ?? '',
                'type' => $validated['type'] ?? '',
            ],
        ]);
    }

    public function create(): View
    {
        return view('categories.create', [
            'mainCategories' => $this->mainCategories(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCategory($request);

        Category::query()->create($validated);

        return redirect()->route('categories.index')->with('success', __('messages.success.category_created'));
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', [
            'category' => $category,
            'mainCategories' => $this->mainCategories($category->id),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $this->validateCategory($request, $category->id);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', __('messages.success.category_updated'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->exists()) {
            return back()->with('error', __('messages.errors.cannot_delete_category_with_children'));
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', __('messages.success.category_deleted'));
    }

    private function validateCategory(Request $request, ?int $categoryId = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('categories', 'name')->ignore($categoryId)],
            'type' => ['required', 'in:main,sub'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(function ($query): void {
                    $query->where('type', 'main');
                }),
            ],
        ]);

        if ($validated['type'] === 'main') {
            $validated['parent_id'] = null;
        }

        if (($validated['parent_id'] ?? null) && $categoryId && (int) $validated['parent_id'] === $categoryId) {
            throw ValidationException::withMessages([
                'parent_id' => __('messages.errors.category_cannot_be_own_parent'),
            ]);
        }

        return $validated;
    }

    private function mainCategories(?int $exceptId = null): Collection
    {
        return Category::query()
            ->where('type', 'main')
            ->when($exceptId, function ($query, int $id): void {
                $query->where('id', '!=', $id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
