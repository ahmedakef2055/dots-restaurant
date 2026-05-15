<x-layouts.app :title="__('ui.recipes.title') . ': ' . $product->name">
@php
$ingredientCatalog = $ingredients->map(static function ($ingredient): array {
    return [
        'id'    => (int)    $ingredient->id,
        'name'  => (string) $ingredient->name,
        'unit'  => strtoupper((string) $ingredient->unit),
        'cost'  => round((float) $ingredient->cost, 4),
        'stock' => round((float) $ingredient->quantity, 3),
    ];
})->values();

$semiFinishedCatalog = $semiFinishedOptions->map(static function ($component): array {
    $yield = max((float) $component->yield_quantity, 0.001);
    return [
        'id'             => (int)   $component->id,
        'name'           => (string)$component->name,
        'yield_quantity' => round((float) $component->yield_quantity, 3),
        'cost_per_unit'  => round((float) $component->total_cost / $yield, 4),
    ];
})->values();

$initialIngredientRows = old('ingredient_rows', $existingIngredientRows->toArray());
$initialComponentRows  = old('component_rows',  $existingComponentRows->toArray());
@endphp

{{-- ── Page Header ──────────────────────────────────────────────────── --}}
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('recipes.index') }}"
       class="glass-button-secondary rounded-lg py-2 px-4 text-sm font-medium flex items-center gap-2 shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>
        {{ __('ui.recipes.edit.back') }}
    </a>
    <div class="min-w-0">
        <h1 class="text-2xl font-bold tracking-tight truncate" style="color:var(--on-surface)">{{ $product->name }}</h1>
        <p class="text-sm mt-0.5" style="color:var(--on-surface-var)">{{ __('ui.recipes.edit.builder_subtitle') }}</p>
    </div>
</div>

{{-- Warehouse scope notice --}}
<div class="glass-panel rounded-xl px-4 py-3 mb-5 flex items-center gap-2 border-s-4" style="border-inline-start-color:var(--tertiary)">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--tertiary)"><path d="M160-200h80v-320h480v320h80v-426L480-754 160-626v426Zm-80 80v-560l400-160 400 160v560H640v-320H320v320H80Zm280 0v-80h80v80h-80Zm80-120v-80h80v80h-80Zm80 120v-80h80v80h-80ZM240-520h480-480Z"/></svg>
    <p class="text-sm" style="color:var(--on-surface-var)">
        {{ __('ui.recipes.warehouse_scope', ['warehouse' => $recipeWarehouseName ?? 'Branch Warehouse']) }}
    </p>
</div>

{{-- ── Builder Card ─────────────────────────────────────────────────── --}}
<div class="glass-panel-elevated rounded-2xl overflow-hidden">
    <form method="POST" action="{{ route('recipes.update', $product) }}"
          id="productRecipeBuilderForm" class="space-y-0">
        @csrf
        @method('PUT')
        <input type="hidden" name="is_semi_finished" value="0">
        <input type="hidden" name="selling_price" value="{{ old('selling_price', $currentVersion?->selling_price ?? $product->price) }}">

        {{-- Section: Recipe Settings --}}
        <div class="px-6 pt-6 pb-5">
            <h3 class="text-sm font-semibold flex items-center gap-2 mb-4" style="color:var(--on-surface)">
                <span class="inline-flex h-6 w-6 items-center justify-center rounded-md"
                      style="background-color:color-mix(in srgb,var(--primary) 12%,transparent)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" style="color:var(--primary)"><path d="M440-120v-240h80v80h320v80H520v80h-80Zm-320-80v-80h240v80H120Zm160-160v-80H120v-80h160v-80h80v240h-80Zm160-80v-80h400v80H440Zm160-160v-240h80v80h160v80H680v80h-80Zm-480-80v-80h400v80H120Z"/></svg>
                </span>
                Recipe Settings
            </h3>

            <div class="grid gap-4 md:grid-cols-4">
                {{-- Product selector (autocomplete) --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                        {{ __('ui.recipes.fields.product_name') }}
                    </label>
                    <div id="recipeProductSelectorWrap" class="relative">
                        <input id="productSelectorInput"
                               value="{{ old('selected_product_name', $product->name) }}"
                               autocomplete="off"
                               placeholder="{{ __('ui.recipes.edit.product_selector_placeholder') }}"
                               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
                        <div id="productSelectorMenu"
                             class="absolute left-0 right-0 z-20 mt-1 hidden max-h-56 overflow-auto rounded-xl border shadow-xl"
                             style="background-color:var(--surface-container);border-color:color-mix(in srgb,var(--primary) 15%,transparent)">
                        </div>
                    </div>
                    <p class="mt-1 text-xs" style="color:var(--on-surface-var)">{{ __('ui.recipes.edit.product_selector_hint') }}</p>
                    <p id="productSelectorError" class="mt-1 hidden text-xs" style="color:var(--error)">{{ __('ui.recipes.edit.product_selector_invalid') }}</p>
                </div>

                {{-- Yield --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.recipes.fields.yield_quantity') }}</label>
                    <input id="yieldQuantityInput" name="yield_quantity" type="number" min="0.001" step="0.001"
                           value="{{ old('yield_quantity', $currentVersion?->yield_quantity ?? $prefillYieldQuantity ?? 1) }}" required
                           class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
                </div>

                {{-- Waste % --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.recipes.fields.waste_percent') }}</label>
                    <input id="wasteInput" name="waste_percentage" type="number" min="0" max="100" step="0.01"
                           value="{{ old('waste_percentage', $currentVersion?->waste_percentage ?? $prefillWastePercentage ?? 0) }}"
                           class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
                </div>

                {{-- Loss % --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.recipes.fields.loss_percent') }}</label>
                    <input id="lossInput" name="loss_percentage" type="number" min="0" max="100" step="0.01"
                           value="{{ old('loss_percentage', $currentVersion?->loss_percentage ?? $prefillLossPercentage ?? 0) }}"
                           class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
                </div>
            </div>
        </div>

        <div class="border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

        {{-- Section: Raw Materials --}}
        <div class="overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between"
                 style="background-color:color-mix(in srgb,var(--surface-highest) 25%,transparent)">
                <h3 class="text-sm font-semibold flex items-center gap-2" style="color:var(--on-surface)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--primary)"><path d="M200-80q-33 0-56.5-23.5T120-160v-451q-18-11-29-28.5T80-680v-120q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v120q0 23-11 40.5T840-611v451q0 33-23.5 56.5T760-80H200Zm0-520v440h560v-440H200Zm-40-80h640v-120H160v120Zm200 280h240v-80H360v80Zm120 20Z"/></svg>
                    {{ __('ui.recipes.sections.raw_materials') }}
                </h3>
                <button type="button" id="addIngredientRow"
                        class="glass-button-primary rounded-lg px-3 py-1.5 text-xs font-medium flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[15px]" ><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
                    {{ __('ui.recipes.actions.add_raw_material') }}
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr style="background-color:color-mix(in srgb,var(--surface-highest) 40%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.ingredient') }}</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.available_stock') }}</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.unit') }}</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.unit_cost') }}</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.qty_required') }}</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.line_total') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody id="ingredientRows" class="text-sm"></tbody>
                </table>
            </div>
        </div>

        <div class="border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

        {{-- Section: Semi-Finished Components --}}
        <div class="overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between"
                 style="background-color:color-mix(in srgb,var(--surface-highest) 25%,transparent)">
                <h3 class="text-sm font-semibold flex items-center gap-2" style="color:var(--on-surface)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--tertiary)"><path d="M440-280H280q-83 0-141.5-58.5T80-480q0-83 58.5-141.5T280-680h160v80H280q-50 0-85 35t-35 85q0 50 35 85t85 35h160v80ZM320-440v-80h320v80H320Zm200 160v-80h160q50 0 85-35t35-85q0-50-35-85t-85-35H520v-80h160q83 0 141.5 58.5T880-480q0 83-58.5 141.5T680-280H520Z"/></svg>
                    {{ __('ui.recipes.sections.semi_finished_components') }}
                </h3>
                <button type="button" id="addComponentRow"
                        class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[15px]" ><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
                    {{ __('ui.recipes.actions.add_semi_finished') }}
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr style="background-color:color-mix(in srgb,var(--surface-highest) 40%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.component') }}</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.yield') }}</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.cost_per_unit') }}</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.qty_required') }}</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.line_total') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody id="componentRows" class="text-sm"></tbody>
                </table>
            </div>
        </div>

        <div class="border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

        {{-- Cost Totals --}}
        <div class="px-6 py-5" style="background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="glass-panel rounded-xl p-4">
                    <p class="text-[10px] font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.recipes.totals.ingredients_subtotal') }}</p>
                    <p id="ingredientsSubtotal" class="text-base font-bold font-mono" style="color:var(--on-surface)">0.0000</p>
                </div>
                <div class="glass-panel rounded-xl p-4">
                    <p class="text-[10px] font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.recipes.totals.components_subtotal') }}</p>
                    <p id="componentsSubtotal" class="text-base font-bold font-mono" style="color:var(--on-surface)">0.0000</p>
                </div>
                <div class="glass-panel rounded-xl p-4">
                    <p class="text-[10px] font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.recipes.totals.total_before_waste') }}</p>
                    <p id="baseTotal" class="text-base font-bold font-mono" style="color:var(--on-surface)">0.0000</p>
                </div>
                <div class="rounded-xl p-4" style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);border:1px solid color-mix(in srgb,var(--primary) 20%,transparent)">
                    <p class="text-[10px] font-semibold uppercase tracking-wider mb-1" style="color:var(--primary)">{{ __('ui.recipes.totals.final_total') }}</p>
                    <p id="grandTotal" class="text-xl font-extrabold font-mono" style="color:var(--primary)">0.0000</p>
                </div>
            </div>
        </div>

        <div class="border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

        {{-- Notes --}}
        <div class="px-6 py-5">
            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                {{ __('ui.recipes.fields.notes') }}
            </label>
            <textarea name="notes" rows="3"
                      class="w-full rounded-xl glass-input px-4 py-3 text-sm resize-none">{{ old('notes', $currentVersion?->notes) }}</textarea>
        </div>

        <div class="border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

        {{-- Action Buttons --}}
        <div class="px-6 py-5 flex items-center gap-3"
             style="background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <button type="submit"
                    class="glass-button-primary rounded-xl py-2.5 px-6 text-sm font-semibold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM565-275q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z"/></svg>
                {{ __('ui.recipes.actions.save_recipe') }}
            </button>
            <a href="{{ route('recipes.index') }}"
               class="glass-button-secondary rounded-xl py-2.5 px-5 text-sm font-medium">
                {{ __('ui.recipes.edit.cancel') }}
            </a>
        </div>
    </form>
</div>

@php
$builderData = [
    'ingredientCatalog'          => $ingredientCatalog,
    'componentCatalog'           => $semiFinishedCatalog,
    'initialIngredientRows'      => array_values($initialIngredientRows),
    'initialComponentRows'       => array_values($initialComponentRows),
    'productOptions'             => $recipeProductOptions
                                        ->map(static fn($p): array => ['id' => (int)$p->id, 'name' => (string)$p->name])
                                        ->values(),
    'currentProductName'         => (string) $product->name,
    'productEditUrlTemplate'     => url('/recipes/__PRODUCT__/edit'),
    'productSelectorNoResults'   => __('ui.recipes.edit.product_selector_no_results'),
    'selectRawMaterialLabel'     => __('ui.recipes.fields.select_raw_material'),
    'selectSemiFinishedLabel'    => __('ui.recipes.fields.select_semi_finished'),
    'removeLabel'                => __('ui.recipes.actions.remove'),
];
@endphp

<script id="productRecipeBuilderData" type="application/json">@json($builderData, JSON_UNESCAPED_UNICODE)</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dataEl = document.getElementById('productRecipeBuilderData');
    let bd = {};
    if (dataEl) { try { bd = JSON.parse(dataEl.textContent || '{}'); } catch (e) {} }

    const ingredientCatalog    = bd.ingredientCatalog    || [];
    const componentCatalog     = bd.componentCatalog     || [];
    const initialIngredientRows= bd.initialIngredientRows|| [];
    const initialComponentRows = bd.initialComponentRows || [];
    const productOptions       = bd.productOptions       || [];
    const currentProductName   = String(bd.currentProductName || '');
    const productEditUrlTpl    = bd.productEditUrlTemplate || '';
    const selectRawLabel       = bd.selectRawMaterialLabel   || '';
    const selectSemiLabel      = bd.selectSemiFinishedLabel  || '';
    const removeLabel          = bd.removeLabel || 'Remove';

    const ingredientMap = new Map(ingredientCatalog.map(i => [Number(i.id), i]));
    const componentMap  = new Map(componentCatalog.map(i  => [Number(i.id), i]));

    const ingBody  = document.getElementById('ingredientRows');
    const compBody = document.getElementById('componentRows');
    const addIngBtn  = document.getElementById('addIngredientRow');
    const addCompBtn = document.getElementById('addComponentRow');
    const wasteInput = document.getElementById('wasteInput');
    const lossInput  = document.getElementById('lossInput');
    const prodWrap   = document.getElementById('recipeProductSelectorWrap');
    const prodInput  = document.getElementById('productSelectorInput');
    const prodMenu   = document.getElementById('productSelectorMenu');
    const prodErr    = document.getElementById('productSelectorError');
    const ingSubEl   = document.getElementById('ingredientsSubtotal');
    const compSubEl  = document.getElementById('componentsSubtotal');
    const baseTotalEl= document.getElementById('baseTotal');
    const grandTotalEl=document.getElementById('grandTotal');

    const nameToId = new Map();
    productOptions.forEach(function (p) {
        const k = String(p.name || '').trim().toLowerCase();
        if (k && !nameToId.has(k)) nameToId.set(k, Number(p.id));
    });

    if (prodInput && String(prodInput.value || '').trim() === '' && currentProductName !== '') {
        prodInput.value = currentProductName;
    }

    function fmt(v, d) { return Number(v || 0).toFixed(d); }
    function norm(v) { return String(v || '').trim().toLowerCase(); }

    function ingredientOptionsHtml(selId) {
        let h = '<option value="">' + selectRawLabel + '</option>';
        ingredientCatalog.forEach(i => {
            h += '<option value="' + i.id + '"' + (Number(selId) === Number(i.id) ? ' selected' : '') + '>' + i.name + '</option>';
        });
        return h;
    }

    function componentOptionsHtml(selId) {
        let h = '<option value="">' + selectSemiLabel + '</option>';
        componentCatalog.forEach(i => {
            h += '<option value="' + i.id + '"' + (Number(selId) === Number(i.id) ? ' selected' : '') + '>' + i.name + '</option>';
        });
        return h;
    }

    function recalcTotals() {
        let ingSum = 0, compSum = 0;
        ingBody.querySelectorAll('tr').forEach(r  => ingSum  += Number(r.dataset.lineTotal || 0));
        compBody.querySelectorAll('tr').forEach(r => compSum += Number(r.dataset.lineTotal || 0));
        const base  = ingSum + compSum;
        const waste = 1 + (Number(wasteInput.value || 0) / 100);
        const loss  = 1 + (Number(lossInput.value  || 0) / 100);
        ingSubEl.textContent    = fmt(ingSum,  4);
        compSubEl.textContent   = fmt(compSum, 4);
        baseTotalEl.textContent = fmt(base,    4);
        grandTotalEl.textContent= fmt(base * waste * loss, 4);
    }

    function renumber(body, type) {
        body.querySelectorAll('tr').forEach(function (row, idx) {
            if (type === 'ingredient') {
                row.querySelector('.ingredient-select').name = 'ingredient_rows[' + idx + '][ingredient_id]';
                row.querySelector('.ingredient-qty').name    = 'ingredient_rows[' + idx + '][quantity_required]';
            } else {
                row.querySelector('.component-select').name = 'component_rows[' + idx + '][component_recipe_version_id]';
                row.querySelector('.component-qty').name    = 'component_rows[' + idx + '][quantity_required]';
            }
        });
    }

    const CELL = 'px-4 py-3 text-sm';
    const INP  = 'glass-input rounded-lg px-3 py-2 text-sm';

    function refreshIngRow(row) {
        const sel  = row.querySelector('.ingredient-select');
        const qty  = row.querySelector('.ingredient-qty');
        const item = ingredientMap.get(Number(sel.value));
        const cost = item ? Number(item.cost) : 0;
        const lt   = cost * Number(qty.value || 0);
        row.querySelector('.ingredient-stock').textContent    = item ? fmt(item.stock, 3) : '-';
        row.querySelector('.ingredient-unit').textContent     = item ? item.unit : '-';
        row.querySelector('.ingredient-unit-cost').textContent= fmt(cost, 4);
        row.querySelector('.ingredient-line-total').textContent=fmt(lt, 4);
        row.dataset.lineTotal = String(lt);
        recalcTotals();
    }

    function refreshCompRow(row) {
        const sel  = row.querySelector('.component-select');
        const qty  = row.querySelector('.component-qty');
        const item = componentMap.get(Number(sel.value));
        const cost = item ? Number(item.cost_per_unit) : 0;
        const lt   = cost * Number(qty.value || 0);
        row.querySelector('.component-yield').textContent     = item ? fmt(item.yield_quantity, 3) : '-';
        row.querySelector('.component-unit-cost').textContent = fmt(cost, 4);
        row.querySelector('.component-line-total').textContent= fmt(lt, 4);
        row.dataset.lineTotal = String(lt);
        recalcTotals();
    }

    const RMV_BTN = 'remove-row rounded-lg px-2.5 py-1.5 text-xs font-medium';
    const RMV_STY = 'border:1px solid color-mix(in srgb,var(--error) 30%,transparent);color:var(--error)';

    function addIngRow(data) {
        const row = document.createElement('tr');
        row.style.borderBottom = '1px solid color-mix(in srgb,var(--primary) 6%,transparent)';
        row.innerHTML =
            '<td class="' + CELL + '"><select class="ingredient-select w-full ' + INP + '">' + ingredientOptionsHtml(data.ingredient_id) + '</select></td>' +
            '<td class="ingredient-stock ' + CELL + ' font-mono" style="color:var(--on-surface-var)">-</td>' +
            '<td class="ingredient-unit  ' + CELL + '" style="color:var(--on-surface-var)">-</td>' +
            '<td class="ingredient-unit-cost ' + CELL + ' font-mono" style="color:var(--on-surface-var)">0.0000</td>' +
            '<td class="' + CELL + '"><input type="number" min="0" step="0.001" class="ingredient-qty w-32 ' + INP + ' font-mono" value="' + (data.quantity_required || '') + '"></td>' +
            '<td class="ingredient-line-total ' + CELL + ' font-semibold font-mono" style="color:var(--on-surface)">0.0000</td>' +
            '<td class="' + CELL + ' text-right"><button type="button" class="' + RMV_BTN + '" style="' + RMV_STY + '">' + removeLabel + '</button></td>';
        ingBody.appendChild(row);
        row.querySelector('.ingredient-select').addEventListener('change', () => refreshIngRow(row));
        row.querySelector('.ingredient-qty').addEventListener('input',    () => refreshIngRow(row));
        row.querySelector('.remove-row').addEventListener('click', function () {
            row.remove(); renumber(ingBody, 'ingredient'); recalcTotals();
        });
        renumber(ingBody, 'ingredient');
        refreshIngRow(row);
    }

    function addCompRow(data) {
        const row = document.createElement('tr');
        row.style.borderBottom = '1px solid color-mix(in srgb,var(--primary) 6%,transparent)';
        row.innerHTML =
            '<td class="' + CELL + '"><select class="component-select w-full ' + INP + '">' + componentOptionsHtml(data.component_recipe_version_id) + '</select></td>' +
            '<td class="component-yield     ' + CELL + ' font-mono" style="color:var(--on-surface-var)">-</td>' +
            '<td class="component-unit-cost ' + CELL + ' font-mono" style="color:var(--on-surface-var)">0.0000</td>' +
            '<td class="' + CELL + '"><input type="number" min="0" step="0.001" class="component-qty w-32 ' + INP + ' font-mono" value="' + (data.quantity_required || '') + '"></td>' +
            '<td class="component-line-total ' + CELL + ' font-semibold font-mono" style="color:var(--on-surface)">0.0000</td>' +
            '<td class="' + CELL + ' text-right"><button type="button" class="' + RMV_BTN + '" style="' + RMV_STY + '">' + removeLabel + '</button></td>';
        compBody.appendChild(row);
        row.querySelector('.component-select').addEventListener('change', () => refreshCompRow(row));
        row.querySelector('.component-qty').addEventListener('input',    () => refreshCompRow(row));
        row.querySelector('.remove-row').addEventListener('click', function () {
            row.remove(); renumber(compBody, 'component'); recalcTotals();
        });
        renumber(compBody, 'component');
        refreshCompRow(row);
    }

    addIngBtn.addEventListener('click',  () => addIngRow({}));
    addCompBtn.addEventListener('click', () => addCompRow({}));
    wasteInput.addEventListener('input', recalcTotals);
    lossInput.addEventListener('input',  recalcTotals);

    // ── Product selector autocomplete ────────────────────────────────
    function hideProdMenu() { if (prodMenu) prodMenu.classList.add('hidden'); }
    function showProdMenu() { if (prodMenu) prodMenu.classList.remove('hidden'); }

    function renderProdOptions(options) {
        if (!prodMenu) return;
        if (!Array.isArray(options) || options.length === 0) {
            prodMenu.innerHTML = '<div class="px-3 py-2 text-xs" style="color:var(--on-surface-var)">' + (bd.productSelectorNoResults || 'No products found') + '</div>';
            return;
        }
        prodMenu.innerHTML = options.map(function (p) {
            return '<button type="button" class="w-full border-b px-3 py-2 text-left text-sm last:border-b-0 transition-colors" ' +
                   'style="color:var(--on-surface);border-color:color-mix(in srgb,var(--primary) 8%,transparent)" ' +
                   'onmouseenter="this.style.backgroundColor=\'color-mix(in srgb,var(--primary) 8%,transparent)\'" ' +
                   'onmouseleave="this.style.backgroundColor=\'\'" ' +
                   'data-product-selector-option="1" data-product-id="' + p.id + '" data-product-name="' + p.name.replace(/"/g, '&quot;') + '">' + p.name + '</button>';
        }).join('');
    }

    function filterProdOptions(q) {
        const nq = norm(q);
        return nq === '' ? productOptions : productOptions.filter(p => norm(p.name).includes(nq));
    }

    function openProdMenu(q) { renderProdOptions(filterProdOptions(q)); showProdMenu(); }

    function navigateToProd(id) {
        const pid = Number(id || 0);
        if (!productEditUrlTpl || pid <= 0) return false;
        if (prodErr) prodErr.classList.add('hidden');
        const dest = productEditUrlTpl.replace('__PRODUCT__', String(pid));
        if (window.location.href !== dest) window.location.href = dest;
        return true;
    }

    function openSelectedProd() {
        if (!prodInput || !productEditUrlTpl) return false;
        const id = nameToId.get(norm(prodInput.value || ''));
        if (!id) { if (prodErr) prodErr.classList.remove('hidden'); return false; }
        return navigateToProd(id);
    }

    if (prodInput) {
        prodInput.addEventListener('input',  function () { if (prodErr) prodErr.classList.add('hidden'); openProdMenu(prodInput.value || ''); });
        prodInput.addEventListener('focus',  function () { openProdMenu(prodInput.value || ''); });
        prodInput.addEventListener('click',  function () { openProdMenu(prodInput.value || ''); });
        prodInput.addEventListener('change', openSelectedProd);
        prodInput.addEventListener('keydown',function (e) { if (e.key === 'Enter') { e.preventDefault(); openSelectedProd(); } });
    }

    if (prodMenu) {
        prodMenu.addEventListener('mousedown', function (e) {
            const opt = e.target.closest('[data-product-selector-option]');
            if (!opt) return;
            e.preventDefault();
            if (prodInput) prodInput.value = String(opt.dataset.productName || '');
            hideProdMenu();
            navigateToProd(opt.dataset.productId);
        });
    }

    document.addEventListener('click', function (e) {
        if (prodWrap && !prodWrap.contains(e.target)) hideProdMenu();
    });

    // ── Load initial rows ────────────────────────────────────────────
    if (initialIngredientRows.length > 0) {
        initialIngredientRows.forEach(r => addIngRow(r || {}));
    } else {
        addIngRow({});
    }
    initialComponentRows.forEach(r => addCompRow(r || {}));
    recalcTotals();
});
</script>

</x-layouts.app>
