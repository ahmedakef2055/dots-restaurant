<x-layouts.app :title="$coupon->code">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('coupons.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-1.5 text-sm font-semibold text-[var(--on-surface-var)] transition hover:border-[var(--primary)] hover:bg-[color-mix(in_srgb,var(--primary)_8%,transparent_92%)] hover:text-[var(--primary)]">← Back to Coupons</a>
        <a href="{{ route('coupons.edit', $coupon) }}"><x-ui.button>Edit Coupon</x-ui.button></a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-ui.card title="Coupon Details" class="lg:col-span-2">
            <dl class="grid gap-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-[var(--on-surface-var)]">Name</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $coupon->name }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Code</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $coupon->code }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Discount</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $coupon->discount_type === 'percentage' ? rtrim(rtrim((string) $coupon->discount_value, '0'), '.') . '%' : \App\Support\CurrencyFormatter::format($coupon->discount_value) }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Min Order</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ \App\Support\CurrencyFormatter::format($coupon->min_order_amount) }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Max Discount</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $coupon->max_discount_amount ? \App\Support\CurrencyFormatter::format($coupon->max_discount_amount) : 'No cap' }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Status</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Starts At</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $coupon->starts_at?->format('Y-m-d H:i') ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Ends At</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $coupon->ends_at?->format('Y-m-d H:i') ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Usage Limit</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $coupon->usage_limit ?? '∞' }}</dd>
                </div>
                <div>
                    <dt class="text-[var(--on-surface-var)]">Per User Limit</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $coupon->per_user_limit ?? '∞' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-[var(--on-surface-var)]">Notes</dt>
                    <dd class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $coupon->notes ?: '-' }}</dd>
                </div>
            </dl>
        </x-ui.card>

        <x-ui.card title="Usage Summary">
            <p class="text-sm text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">Used Count: <span class="font-semibold">{{ $coupon->used_count }}</span></p>
            <p class="mt-1 text-sm text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">Remaining: <span class="font-semibold">{{ $coupon->usage_limit ? max($coupon->usage_limit - $coupon->used_count, 0) : '∞' }}</span></p>
        </x-ui.card>
    </div>

    <x-ui.card title="Recent Redemptions" class="mt-6">
        <x-ui.table :headers="['Order', 'Discount', 'Redeemed At']">
            @forelse($coupon->redemptions as $redemption)
            <tr class="hover:bg-[var(--surface-low)] ">
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">{{ $redemption->order?->order_number ?: '#' . $redemption->order_id }}</td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">{{ \App\Support\CurrencyFormatter::format($redemption->discount_amount) }}</td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">{{ $redemption->redeemed_at?->format('Y-m-d H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-4 py-6 text-center text-sm text-[var(--on-surface-var)] dark:text-[var(--outline)]">No redemptions yet.</td>
            </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-layouts.app>