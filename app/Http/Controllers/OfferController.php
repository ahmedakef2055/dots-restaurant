<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:active,inactive'],
            'order_type' => ['nullable', 'in:dine_in,takeaway,delivery'],
        ]);

        $offers = Offer::query()
            ->when($validated['q'] ?? null, fn($query, string $search) => $query->where('name', 'like', "%{$search}%"))
            ->when($validated['status'] ?? null, fn($query, string $status) => $query->where('is_active', $status === 'active'))
            ->when($validated['order_type'] ?? null, fn($query, string $type) => $query->where('order_type', $type))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('offers.index', [
            'offers' => $offers,
            'filters' => [
                'q' => $validated['q'] ?? '',
                'status' => $validated['status'] ?? '',
                'order_type' => $validated['order_type'] ?? '',
            ],
        ]);
    }

    public function create(): View
    {
        return view('offers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateOffer($request);

        Offer::query()->create($validated);

        return redirect()
            ->route('offers.index')
            ->with('success', __('messages.success.offer_created'));
    }

    public function show(Offer $offer): View
    {
        $offer->load(['orders' => fn($query) => $query->latest('order_serial')->limit(10)]);

        return view('offers.show', [
            'offer' => $offer,
        ]);
    }

    public function edit(Offer $offer): View
    {
        return view('offers.edit', [
            'offer' => $offer,
        ]);
    }

    public function update(Request $request, Offer $offer): RedirectResponse
    {
        $validated = $this->validateOffer($request);

        $offer->update($validated);

        return redirect()
            ->route('offers.show', $offer)
            ->with('success', __('messages.success.offer_updated'));
    }

    public function destroy(Offer $offer): RedirectResponse
    {
        if ($offer->orders()->exists()) {
            return back()->with('error', __('messages.errors.cannot_delete_offer_with_orders'));
        }

        $offer->delete();

        return redirect()
            ->route('offers.index')
            ->with('success', __('messages.success.offer_deleted'));
    }

    private function validateOffer(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'discount_type' => ['required', 'in:fixed,percentage'],
            'discount_value' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0.01'],
            'order_type' => ['nullable', 'in:dine_in,takeaway,delivery'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'priority' => ['required', 'integer', 'min:1', 'max:999'],
            'stackable_with_coupon' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
