@props(['supplier' => null, 'ingredients' => collect(), 'selectedIngredientIds' => []])

@php
// Pre-compute all values used in Blade directives to avoid complex
// expressions (e.g. casts, nullsafe chains) confusing Blade's parser.
$selectedIngredientIds = collect(old('ingredient_ids', $selectedIngredientIds ?? []))
    ->map(static fn($id): int => (int) $id)
    ->values()
    ->all();

$selectedIngredientNames = $ingredients
    ->filter(fn($ingredient): bool => in_array((int) $ingredient->id, $selectedIngredientIds, true))
    ->pluck('name')
    ->values()
    ->all();

$isActive = old('is_active', (string) (int) ($supplier?->is_active ?? true));

$selectedSummary = count($selectedIngredientNames) === 0
    ? 'No materials selected yet.'
    : 'Selected: ' . implode(', ', array_slice($selectedIngredientNames, 0, 3))
      . (count($selectedIngredientNames) > 3 ? ' +' . (count($selectedIngredientNames) - 3) . ' more' : '');
@endphp

{{-- ── Section: Basic Information ────────────────────────────────── --}}
<div class="px-6 pt-6 pb-5">
    <h3 class="text-sm font-semibold flex items-center gap-2 mb-5" style="color:var(--on-surface)">
        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg shrink-0"
              style="background-color:color-mix(in srgb,var(--primary) 12%,transparent)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--primary)"><path d="M841-518v318q0 33-23.5 56.5T761-120H201q-33 0-56.5-23.5T121-200v-318q-23-21-35.5-54t-.5-72l42-136q8-26 28.5-43t47.5-17h556q27 0 47 16.5t29 43.5l42 136q12 39-.5 71T841-518Zm-272-42q27 0 41-18.5t11-41.5l-22-140h-78v148q0 21 14 36.5t34 15.5Zm-180 0q23 0 37.5-15.5T441-612v-148h-78l-22 140q-4 24 10.5 42t37.5 18Zm-178 0q18 0 31.5-13t16.5-33l22-154h-78l-40 134q-6 20 6.5 43t41.5 23Zm540 0q29 0 42-23t6-43l-42-134h-76l22 154q3 20 16.5 33t31.5 13ZM201-200h560v-282q-5 2-6.5 2H751q-27 0-47.5-9T663-518q-18 18-41 28t-49 10q-27 0-50.5-10T481-518q-17 18-39.5 28T393-480q-29 0-52.5-10T299-518q-21 21-41.5 29.5T211-480h-4.5q-2.5 0-5.5-2v282Zm560 0H201h560Z"/></svg>
        </span>
        Basic Information
    </h3>

    <div class="grid gap-5 md:grid-cols-2">
        {{-- Supplier Name --}}
        <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                Supplier Name <span style="color:var(--error)">*</span>
            </label>
            <input name="name" value="{{ old('name', $supplier?->name) }}" required
                   placeholder="e.g. Glacier Farms Organics"
                   class="w-full rounded-xl glass-input px-4 py-3 text-sm">
            @error('name')
                <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
            @enderror
        </div>

        {{-- Phone --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                Mobile Number <span style="color:var(--error)">*</span>
            </label>
            <input name="phone" required value="{{ old('phone', $supplier?->phone) }}"
                   placeholder="+1 (555) 000-0000"
                   class="w-full rounded-xl glass-input px-4 py-3 text-sm">
            @error('phone')
                <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                Email Address
            </label>
            <input name="email" type="email" value="{{ old('email', $supplier?->email) }}"
                   placeholder="orders@supplier.com"
                   class="w-full rounded-xl glass-input px-4 py-3 text-sm">
            @error('email')
                <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
            @enderror
        </div>

        {{-- Contact Person --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                Contact Person
            </label>
            <input name="contact_person" value="{{ old('contact_person', $supplier?->contact_person) }}"
                   placeholder="Full Name"
                   class="w-full rounded-xl glass-input px-4 py-3 text-sm">
            @error('contact_person')
                <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
            @enderror
        </div>

        {{-- Status --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                Status
            </label>
            <div class="relative">
                <select name="is_active"
                        class="w-full rounded-xl glass-input px-4 py-3 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                    <option value="1" @selected($isActive === '1')>Active</option>
                    <option value="0" @selected($isActive === '0')>Inactive</option>
                </select>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
            </div>
            @error('is_active')
                <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="border-t mx-6" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

{{-- ── Section: Location ──────────────────────────────────────────── --}}
<div class="px-6 py-5">
    <h3 class="text-sm font-semibold flex items-center gap-2 mb-5" style="color:var(--on-surface)">
        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg shrink-0"
              style="background-color:color-mix(in srgb,var(--secondary) 12%,transparent)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--secondary)"><path d="M536.5-503.5Q560-527 560-560t-23.5-56.5Q513-640 480-640t-56.5 23.5Q400-593 400-560t23.5 56.5Q447-480 480-480t56.5-23.5ZM480-186q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Z"/></svg>
        </span>
        Location
    </h3>

    <div class="grid gap-5 md:grid-cols-3">
        {{-- Address --}}
        <div class="md:col-span-3">
            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                Street Address
            </label>
            <input name="address" value="{{ old('address', $supplier?->address) }}"
                   placeholder="123 Market St."
                   class="w-full rounded-xl glass-input px-4 py-3 text-sm">
            @error('address')
                <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
            @enderror
        </div>

        {{-- City --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">City</label>
            <input name="city" value="{{ old('city', $supplier?->city) }}"
                   placeholder="City"
                   class="w-full rounded-xl glass-input px-4 py-3 text-sm">
            @error('city')
                <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
            @enderror
        </div>

        {{-- Country --}}
        <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Country</label>
            <input name="country" value="{{ old('country', $supplier?->country) }}"
                   placeholder="Country"
                   class="w-full rounded-xl glass-input px-4 py-3 text-sm">
            @error('country')
                <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="border-t mx-6" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

{{-- ── Section: Materials Supplied ────────────────────────────────── --}}
<div class="px-6 py-5">
    <h3 class="text-sm font-semibold flex items-center gap-2 mb-5" style="color:var(--on-surface)">
        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg shrink-0"
              style="background-color:color-mix(in srgb,var(--tertiary) 12%,transparent)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--tertiary)"><path d="M200-80q-33 0-56.5-23.5T120-160v-451q-18-11-29-28.5T80-680v-120q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v120q0 23-11 40.5T840-611v451q0 33-23.5 56.5T760-80H200Zm0-520v440h560v-440H200Zm-40-80h640v-120H160v120Zm200 280h240v-80H360v80Zm120 20Z"/></svg>
        </span>
        Materials Supplied
    </h3>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            Raw Materials (select all that apply)
        </label>
        <p class="mb-3 text-xs" style="color:var(--on-surface-var)">
            Choose materials from your inventory that this supplier provides.
        </p>

        {{-- Ingredient multi-select dropdown --}}
        <div class="relative" data-ingredient-dropdown>
            <button type="button" data-dropdown-toggle
                    class="flex w-full items-center justify-between rounded-xl glass-input px-4 py-3 text-left text-sm font-medium transition-all">
                <span data-dropdown-label style="color:var(--on-surface-var)">Choose raw materials</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--on-surface-var)"><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
            </button>

            <div data-dropdown-menu
                 class="absolute z-20 mt-1 hidden w-full rounded-xl border overflow-hidden"
                 style="background-color:var(--surface-container);border-color:color-mix(in srgb,var(--primary) 15%,transparent);box-shadow:0 8px 32px rgba(0,0,0,0.3)">
                <div class="border-b p-2"
                     style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)">
                    <input type="text" data-dropdown-search placeholder="Search material..."
                           class="w-full rounded-lg glass-input px-3 py-2 text-sm">
                </div>
                <div class="max-h-64 space-y-0.5 overflow-auto p-2" data-dropdown-items>
                    @forelse($ingredients as $ingredient)
                    @php $isChecked = in_array((int) $ingredient->id, $selectedIngredientIds, true); @endphp
                    <label data-dropdown-item data-name="{{ strtolower($ingredient->name) }}"
                           class="flex items-center justify-between gap-2 rounded-lg px-3 py-2 text-sm cursor-pointer transition-colors"
                           style="color:var(--on-surface)"
                           onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--primary) 8%,transparent)'"
                           onmouseleave="this.style.backgroundColor=''">
                        <span>
                            {{ $ingredient->name }}
                            <span class="text-xs ml-1" style="color:var(--on-surface-var)">({{ strtoupper($ingredient->unit) }})</span>
                        </span>
                        <input type="checkbox" data-ingredient-checkbox name="ingredient_ids[]"
                               value="{{ $ingredient->id }}"
                               @checked($isChecked)
                               class="h-4 w-4 rounded"
                               style="accent-color:var(--primary)">
                    </label>
                    @empty
                    <p class="px-3 py-2 text-sm" style="color:var(--on-surface-var)">No available raw materials right now.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Summary line — computed in @php above to avoid nested @if --}}
        <p class="mt-2 text-xs" data-dropdown-summary style="color:var(--on-surface-var)">
            {{ $selectedSummary }}
        </p>

        @error('ingredient_ids')
            <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
        @enderror
        @error('ingredient_ids.*')
            <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="border-t mx-6" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

{{-- ── Section: Notes ──────────────────────────────────────────────── --}}
<div class="px-6 py-5">
    <h3 class="text-sm font-semibold flex items-center gap-2 mb-5" style="color:var(--on-surface)">
        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg shrink-0"
              style="background-color:color-mix(in srgb,var(--outline-var) 20%,transparent)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--on-surface-var)"><path d="M120-240v-80h480v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg>
        </span>
        Notes
    </h3>
    <textarea name="notes" rows="3"
              class="w-full rounded-xl glass-input px-4 py-3 text-sm resize-none"
              placeholder="Delivery preferences, special instructions...">{{ old('notes', $supplier?->notes) }}</textarea>
    @error('notes')
        <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
    @enderror
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-ingredient-dropdown]').forEach(function (root) {
        const toggle  = root.querySelector('[data-dropdown-toggle]');
        const menu    = root.querySelector('[data-dropdown-menu]');
        const label   = root.querySelector('[data-dropdown-label]');
        const search  = root.querySelector('[data-dropdown-search]');
        const items   = Array.from(root.querySelectorAll('[data-dropdown-item]'));
        const boxes   = Array.from(root.querySelectorAll('[data-ingredient-checkbox]'));
        const summary = root.parentElement.querySelector('[data-dropdown-summary]');

        if (!toggle || !menu || !label) return;

        const close = function () { menu.classList.add('hidden'); };
        const open  = function () { menu.classList.remove('hidden'); };

        function updateUI() {
            var checked = boxes.filter(function (b) { return b.checked; });
            if (checked.length === 0) {
                label.textContent = 'Choose raw materials';
                if (summary) summary.textContent = 'No materials selected yet.';
                return;
            }
            label.textContent = checked.length + ' material(s) selected';
            if (summary) {
                var names = checked.map(function (b) {
                    var row = b.closest('[data-dropdown-item]');
                    return row ? (row.querySelector('span') ? row.querySelector('span').textContent.trim() : '') : '';
                }).filter(Boolean);
                var extra = names.length > 3 ? ' +' + (names.length - 3) + ' more' : '';
                summary.textContent = 'Selected: ' + names.slice(0, 3).join(', ') + extra;
            }
        }

        toggle.addEventListener('click', function () {
            if (!menu.classList.contains('hidden')) { close(); return; }
            open();
            if (search) search.focus();
        });

        if (search) {
            search.addEventListener('input', function () {
                var q = (search.value || '').toLowerCase().trim();
                items.forEach(function (row) {
                    row.classList.toggle('hidden', q !== '' && !String(row.dataset.name || '').includes(q));
                });
            });
        }

        boxes.forEach(function (b) { b.addEventListener('change', updateUI); });

        document.addEventListener('click', function (e) {
            if (!root.contains(e.target)) close();
        });

        updateUI();
    });
});
</script>
