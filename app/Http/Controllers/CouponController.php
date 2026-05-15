<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $coupons = Coupon::query()
            ->withCount('redemptions')
            ->when($validated['q'] ?? null, function ($query, string $search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($validated['status'] ?? null, fn($query, string $status) => $query->where('is_active', $status === 'active'))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('coupons.index', [
            'coupons' => $coupons,
            'filters' => [
                'q' => $validated['q'] ?? '',
                'status' => $validated['status'] ?? '',
            ],
        ]);
    }

    public function create(): View
    {
        return view('coupons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCoupon($request);
        $validated['code'] = strtoupper($validated['code']);

        Coupon::query()->create($validated);

        return redirect()
            ->route('coupons.index')
            ->with('success', __('messages.success.coupon_created'));
    }

    public function show(Coupon $coupon): View
    {
        $coupon->load(['redemptions' => fn($query) => $query->latest('id')->with('order')->limit(10)]);

        return view('coupons.show', [
            'coupon' => $coupon,
        ]);
    }

    public function edit(Coupon $coupon): View
    {
        return view('coupons.edit', [
            'coupon' => $coupon,
        ]);
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $validated = $this->validateCoupon($request, $coupon->id);
        $validated['code'] = strtoupper($validated['code']);

        $coupon->update($validated);

        return redirect()
            ->route('coupons.show', $coupon)
            ->with('success', __('messages.success.coupon_updated'));
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        if ($coupon->redemptions()->exists()) {
            return back()->with('error', __('messages.errors.cannot_delete_coupon_with_redemptions'));
        }

        $coupon->delete();

        return redirect()
            ->route('coupons.index')
            ->with('success', __('messages.success.coupon_deleted'));
    }

    private function validateCoupon(Request $request, ?int $couponId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($couponId)],
            'discount_type' => ['required', 'in:fixed,percentage'],
            'discount_value' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0.01'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
