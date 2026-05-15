<x-layouts.app title="Create Coupon">
    <div class="mb-6 flex flex-wrap items-center gap-3"><a href="{{ route('coupons.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-1.5 text-sm font-semibold text-[var(--on-surface-var)] transition hover:border-[var(--primary)] hover:bg-[color-mix(in_srgb,var(--primary)_8%,transparent_92%)] hover:text-[var(--primary)]">← Back to Coupons</a></div>
    <x-ui.card title="Create Coupon" subtitle="Define coupon validation and discount rules">
        <form method="POST" action="{{ route('coupons.store') }}" class="space-y-4">
            @csrf
            @include('coupons._form')
            <x-ui.button type="submit">Save Coupon</x-ui.button>
        </form>
    </x-ui.card>
</x-layouts.app>