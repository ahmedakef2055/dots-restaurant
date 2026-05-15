<x-layouts.app title="Add Supplier">

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('suppliers.index') }}"
           class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>
            Back to Suppliers
        </a>
        <div>
            <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)">Add New Supplier</h1>
            <p class="text-sm mt-0.5" style="color:var(--on-surface-var)">Register a new vendor in the system.</p>
        </div>
    </div>

    {{-- ── Form Card ───────────────────────────────────────────────────── --}}
    <div class="glass-panel-elevated rounded-2xl overflow-hidden relative max-w-4xl mx-auto">
        {{-- Decorative glow --}}
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full blur-3xl pointer-events-none"
             style="background-color:color-mix(in srgb,var(--primary) 5%,transparent)"></div>

        <form method="POST" action="{{ route('suppliers.store') }}" class="relative z-10">
            @csrf
            @include('suppliers._form', ['ingredients' => $ingredients, 'selectedIngredientIds' => $selectedIngredientIds])

            {{-- ── Action Footer ────────────────────────────────────────── --}}
            <div class="border-t px-6 py-5 flex items-center justify-end gap-3"
                 style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
                <a href="{{ route('suppliers.index') }}"
                   class="glass-button-secondary rounded-xl py-2.5 px-6 text-sm font-medium">
                    Cancel
                </a>
                <button type="submit"
                        class="glass-button-primary rounded-xl py-2.5 px-8 text-sm font-semibold flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM565-275q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z"/></svg>
                    Save Supplier
                </button>
            </div>
        </form>
    </div>

</x-layouts.app>
