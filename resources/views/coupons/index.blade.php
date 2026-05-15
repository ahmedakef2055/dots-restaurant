<x-layouts.app title="Coupons">
    <x-ui.card title="Coupons" subtitle="Manage coupon campaigns" class="mb-6">
        <form method="GET" action="{{ route('coupons.index') }}" class="grid gap-3 md:grid-cols-3">
            <input name="q" value="{{ $filters['q'] }}" placeholder="Search name/code" class="rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
            <select name="status" class="rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
                <option value="">All Statuses</option>
                <option value="active" @selected($filters['status']==='active' )>Active</option>
                <option value="inactive" @selected($filters['status']==='inactive' )>Inactive</option>
            </select>
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.button type="submit">Filter</x-ui.button>
                <a href="{{ route('coupons.index') }}" class="inline-flex items-center justify-center rounded-lg border border-[var(--outline-var)] px-4 py-2 text-sm font-semibold text-[var(--on-surface)] hover:bg-[var(--surface-low)] dark:border-[var(--outline-var)] dark:text-[var(--on-surface)] ">Reset</a>
            </div>
        </form>
    </x-ui.card>

    <div class="mb-4 flex flex-wrap items-center justify-end gap-2">
        <a href="{{ route('offers.index') }}"><x-ui.button type="button" variant="secondary">Offers</x-ui.button></a>
        <a href="{{ route('coupons.create') }}"><x-ui.button>Create Coupon</x-ui.button></a>
    </div>

    <x-ui.card subtitle="{{ $coupons->total() }} coupons total">
        <x-ui.table :headers="['Code', 'Name', 'Discount', 'Usage', 'Status', 'Actions']">
            @forelse($coupons as $coupon)
            <tr class="hover:bg-[var(--surface-low)] ">
                <td class="px-4 py-3 font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $coupon->code }}</td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">{{ $coupon->name }}</td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">{{ $coupon->discount_type === 'percentage' ? rtrim(rtrim((string) $coupon->discount_value, '0'), '.') . '%' : \App\Support\CurrencyFormatter::format($coupon->discount_value) }}</td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}<br><span class="text-xs text-[var(--on-surface-var)]">Redemptions: {{ $coupon->redemptions_count }}</span></td>
                <td class="px-4 py-3"><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $coupon->is_active ? 'bg-[var(--primary-container)] text-[var(--success)] dark:bg-[color-mix(in_srgb,var(--success)_20%,transparent_80%)] dark:text-[var(--success)]' : 'bg-[var(--surface-container)] text-[var(--on-surface)]  dark:text-[var(--on-surface-var)]' }}">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-2"><a href="{{ route('coupons.show', $coupon) }}" class="rounded-md border border-[var(--outline-var)] px-2.5 py-1 text-xs font-semibold text-[var(--warning)] hover:bg-[color-mix(in srgb,var(--primary) 5%,transparent 95%)] border-[var(--outline-var)] text-[var(--warning)] dark:hover:bg-[color-mix(in_srgb,var(--surface-high)_60%,transparent_40%)]">View</a><a href="{{ route('coupons.edit', $coupon) }}" class="rounded-md border border-[var(--outline-var)] px-2.5 py-1 text-xs font-semibold text-[var(--on-surface)] hover:bg-[var(--surface-low)] dark:border-[var(--outline-var)] dark:text-[var(--on-surface-var)] ">Edit</a>
                        <form method="POST" action="{{ route('coupons.destroy', $coupon) }}">@csrf @method('DELETE')<button type="submit" class="rounded-md border border-[var(--error-container)] px-2.5 py-1 text-xs font-semibold text-[var(--error)] hover:bg-[var(--error-container)]   dark:hover:bg-[var(--error-container)]">Delete</button></form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-sm text-[var(--on-surface-var)] dark:text-[var(--outline)]">No coupons found.</td>
            </tr>
            @endforelse
        </x-ui.table>
        <div class="mt-4">{{ $coupons->links() }}</div>
    </x-ui.card>
</x-layouts.app>