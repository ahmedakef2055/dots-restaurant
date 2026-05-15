<x-layouts.app :title="$offer->name">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('offers.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-1.5 text-sm font-semibold text-[var(--on-surface-var)] transition hover:border-[var(--primary)] hover:bg-[color-mix(in_srgb,var(--primary)_8%,transparent_92%)] hover:text-[var(--primary)]">← Back to Offers</a>
        <a href="{{ route('offers.edit', $offer) }}"><x-ui.button>Edit Offer</x-ui.button></a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-ui.card title="Offer Details" class="lg:col-span-2">
            <dl class="grid gap-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-[var(--on-surface-var)]">Name</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $offer->name }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Order Type</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $offer->order_type ? ucfirst(str_replace('_', ' ', $offer->order_type)) : 'All' }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Discount</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $offer->discount_type === 'percentage' ? rtrim(rtrim((string) $offer->discount_value, '0'), '.') . '%' : \App\Support\CurrencyFormatter::format($offer->discount_value) }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Min Order</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ \App\Support\CurrencyFormatter::format($offer->min_order_amount) }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Max Discount</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $offer->max_discount_amount ? \App\Support\CurrencyFormatter::format($offer->max_discount_amount) : 'No cap' }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Priority</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $offer->priority }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Stackable with Coupon</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $offer->stackable_with_coupon ? 'Yes' : 'No' }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Status</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $offer->is_active ? 'Active' : 'Inactive' }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Starts At</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $offer->starts_at?->format('Y-m-d H:i') ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Ends At</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $offer->ends_at?->format('Y-m-d H:i') ?: '-' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-[var(--on-surface-var)]">Notes</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $offer->notes ?: '-' }}</dd>
                </div>
            </dl>
        </x-ui.card>

        <x-ui.card title="Usage Snapshot">
            <p class="text-sm text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">Orders Using Offer: <span class="font-semibold">{{ $offer->orders->count() }}</span></p>
        </x-ui.card>
    </div>

    <x-ui.card title="Recent Orders" class="mt-6">
        <x-ui.table :headers="['Order', 'Total', 'Created At']">
            @forelse($offer->orders as $order)
            <tr class="hover:bg-[var(--surface-low)] ">
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">{{ $order->order_number }}</td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">{{ \App\Support\CurrencyFormatter::format($order->total) }}</td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">{{ $order->created_at?->format('Y-m-d H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-4 py-6 text-center text-sm text-[var(--on-surface-var)] dark:text-[var(--outline)]">No orders found.</td>
            </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-layouts.app>