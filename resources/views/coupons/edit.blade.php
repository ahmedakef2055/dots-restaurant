<x-layouts.app title="Edit Coupon">
    <div class="mb-6 flex flex-wrap items-center gap-3"><a href="{{ route('coupons.show', $coupon) }}" class="inline-flex items-center gap-2 rounded-xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-1.5 text-sm font-semibold text-[var(--on-surface-var)] transition hover:border-[var(--primary)] hover:bg-[color-mix(in_srgb,var(--primary)_8%,transparent_92%)] hover:text-[var(--primary)]">← Back to Coupon</a></div>
    <x-ui.card title="Edit Coupon" subtitle="Update coupon rules">
        <form method="POST" action="{{ route('coupons.update', $coupon) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('coupons._form', ['coupon' => $coupon])
            <x-ui.button type="submit">Update Coupon</x-ui.button>
        </form>
    </x-ui.card>
</x-layouts.app>