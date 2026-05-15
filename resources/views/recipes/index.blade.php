<x-layouts.app :title="__('ui.recipes.title')">

{{-- ═══════════════════════════════════════════════════════════════════
     SEMI-FINISHED CREATE / EDIT FORM
     Shown when $showSemiFinishedCreateForm is true
═══════════════════════════════════════════════════════════════════ --}}
@if(! empty($showSemiFinishedCreateForm))
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

$semiFinishedRecipe       = $semiFinishedRecipe ?? null;
$isSemiFinishedEdit       = ! empty($isSemiFinishedEdit);
$initialIngredientRows    = old('ingredient_rows', $semiFinishedInitialIngredientRows ?? []);
$initialComponentRows     = old('component_rows',  $semiFinishedInitialComponentRows  ?? []);
$formTitle    = $isSemiFinishedEdit ? __('ui.recipes.semi_finished.table_title')        : __('ui.recipes.semi_finished.create_page_title');
$formSubtitle = $isSemiFinishedEdit ? __('ui.recipes.edit.builder_subtitle')            : __('ui.recipes.semi_finished.create_page_subtitle');
@endphp

{{-- Back button --}}
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('recipes.index') }}#semi"
       class="glass-button-secondary rounded-lg py-2 px-4 text-sm font-medium flex items-center gap-2 shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>
        {{ __('ui.recipes.edit.back') }}
    </a>
    <div>
        <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)">{{ $formTitle }}</h1>
        <p class="text-sm mt-0.5" style="color:var(--on-surface-var)">{{ $formSubtitle }}</p>
    </div>
</div>

{{-- Warehouse scope notice --}}
<div class="glass-panel rounded-xl px-4 py-3 mb-5 flex items-center gap-2 border-s-4" style="border-inline-start-color:var(--tertiary)">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--tertiary)"><path d="M160-200h80v-320h480v320h80v-426L480-754 160-626v426Zm-80 80v-560l400-160 400 160v560H640v-320H320v320H80Zm280 0v-80h80v80h-80Zm80-120v-80h80v80h-80Zm80 120v-80h80v80h-80ZM240-520h480-480Z"/></svg>
    <p class="text-sm" style="color:var(--on-surface-var)">
        {{ __('ui.recipes.warehouse_scope', ['warehouse' => $recipeWarehouseName ?? 'Branch Warehouse']) }}
    </p>
</div>

{{-- Main builder card --}}
<div class="glass-panel-elevated rounded-2xl overflow-hidden">
    <form method="POST" action="{{ $semiFinishedFormAction ?? route('recipes.semi-finished.store') }}" class="space-y-0">
        @csrf
        @if(! empty($semiFinishedFormMethod)) @method($semiFinishedFormMethod) @endif

        <input type="hidden" name="selling_price" value="{{ old('selling_price', $semiFinishedRecipe?->selling_price ?? 0) }}">

        {{-- Section: Basic Fields --}}
        <div class="px-6 pt-6 pb-5">
            <h3 class="text-sm font-semibold flex items-center gap-2 mb-4" style="color:var(--on-surface)">
                <span class="inline-flex h-6 w-6 items-center justify-center rounded-md" style="background-color:color-mix(in srgb,var(--secondary) 12%,transparent)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" style="color:var(--secondary)"><path d="m175-120-56-56 410-410q-18-42-5-95t57-95q53-53 118-62t106 32q41 41 32 106t-62 118q-42 44-95 57t-95-5l-50 50 304 304-56 56-304-302-304 302Zm118-342L173-582q-54-54-54-129t54-129l248 250-128 128Z"/></svg>
                </span>
                Basic Information
            </h3>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.recipes.fields.name') }}</label>
                    <input name="name" value="{{ old('name', $semiFinishedRecipe?->name) }}" required
                           class="w-full rounded-xl glass-input px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.recipes.fields.yield_quantity') }}</label>
                    <input name="yield_quantity" type="number" min="0.001" step="0.001"
                           value="{{ old('yield_quantity', $semiFinishedRecipe?->yield_quantity ?? 1) }}" required
                           class="w-full rounded-xl glass-input px-3 py-2.5 text-sm font-mono">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.recipes.fields.notes') }}</label>
                    <input name="notes" value="{{ old('notes', $semiFinishedRecipe?->notes) }}"
                           class="w-full rounded-xl glass-input px-3 py-2.5 text-sm">
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2 mt-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.recipes.fields.waste_percent') }}</label>
                    <input id="wasteInput" name="waste_percentage" type="number" min="0" max="100" step="0.01"
                           value="{{ old('waste_percentage', $semiFinishedRecipe?->waste_percentage ?? 0) }}"
                           class="w-full rounded-xl glass-input px-3 py-2.5 text-sm font-mono">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.recipes.fields.loss_percent') }}</label>
                    <input id="lossInput" name="loss_percentage" type="number" min="0" max="100" step="0.01"
                           value="{{ old('loss_percentage', $semiFinishedRecipe?->loss_percentage ?? 0) }}"
                           class="w-full rounded-xl glass-input px-3 py-2.5 text-sm font-mono">
                </div>
            </div>
        </div>

        <div class="border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

        {{-- Section: Raw Materials --}}
        <div class="overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between" style="background-color:color-mix(in srgb,var(--surface-highest) 25%,transparent)">
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
                    <tbody id="ingredientRows" class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)"></tbody>
                </table>
            </div>
        </div>

        <div class="border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

        {{-- Section: Semi-Finished Components --}}
        <div class="overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between" style="background-color:color-mix(in srgb,var(--surface-highest) 25%,transparent)">
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
                    <tbody id="componentRows" class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)"></tbody>
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

        {{-- Action Buttons --}}
        <div class="px-6 py-5 flex items-center gap-3">
            <button type="submit" class="glass-button-primary rounded-xl py-2.5 px-6 text-sm font-semibold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM565-275q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z"/></svg>
                {{ $isSemiFinishedEdit ? __('ui.recipes.actions.save_recipe') : __('ui.recipes.actions.save_semi_finished') }}
            </button>
            <a href="{{ route('recipes.index') }}#semi" class="glass-button-secondary rounded-xl py-2.5 px-5 text-sm font-medium">
                {{ __('ui.recipes.edit.cancel') }}
            </a>
        </div>
    </form>
</div>

@php
$builderData = [
    'ingredientCatalog'      => $ingredientCatalog,
    'componentCatalog'       => $semiFinishedCatalog,
    'initialIngredientRows'  => array_values($initialIngredientRows),
    'initialComponentRows'   => array_values($initialComponentRows),
    'selectRawMaterialLabel' => __('ui.recipes.fields.select_raw_material'),
    'selectSemiFinishedLabel'=> __('ui.recipes.fields.select_semi_finished'),
    'removeLabel'            => __('ui.recipes.actions.remove'),
];
@endphp

<script id="semiFinishedBuilderData" type="application/json">{!! json_encode($builderData, JSON_UNESCAPED_UNICODE) !!}</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dataEl = document.getElementById('semiFinishedBuilderData');
    const bd     = dataEl ? JSON.parse(dataEl.textContent || '{}') : {};

    const ingredientCatalog    = bd.ingredientCatalog    || [];
    const componentCatalog     = bd.componentCatalog     || [];
    const initialIngredientRows= bd.initialIngredientRows|| [];
    const initialComponentRows = bd.initialComponentRows || [];
    const selectRawLabel       = bd.selectRawMaterialLabel   || '';
    const selectSemiLabel      = bd.selectSemiFinishedLabel  || '';
    const removeLabel          = bd.removeLabel || 'Remove';

    const ingredientMap = new Map(ingredientCatalog.map(i => [Number(i.id), i]));
    const componentMap  = new Map(componentCatalog.map(i => [Number(i.id), i]));

    const ingredientRowsBody = document.getElementById('ingredientRows');
    const componentRowsBody  = document.getElementById('componentRows');
    const addIngBtn          = document.getElementById('addIngredientRow');
    const addCompBtn         = document.getElementById('addComponentRow');
    const wasteInput         = document.getElementById('wasteInput');
    const lossInput          = document.getElementById('lossInput');
    const ingSubEl           = document.getElementById('ingredientsSubtotal');
    const compSubEl          = document.getElementById('componentsSubtotal');
    const baseTotalEl        = document.getElementById('baseTotal');
    const grandTotalEl       = document.getElementById('grandTotal');

    function fmt(v, d) { return Number(v || 0).toFixed(d); }

    function ingredientOptionsHtml(selId) {
        let h = '<option value="">' + selectRawLabel + '</option>';
        ingredientCatalog.forEach(function (i) {
            h += '<option value="' + i.id + '"' + (Number(selId) === Number(i.id) ? ' selected' : '') + '>' + i.name + '</option>';
        });
        return h;
    }

    function componentOptionsHtml(selId) {
        let h = '<option value="">' + selectSemiLabel + '</option>';
        componentCatalog.forEach(function (i) {
            h += '<option value="' + i.id + '"' + (Number(selId) === Number(i.id) ? ' selected' : '') + '>' + i.name + '</option>';
        });
        return h;
    }

    function recalcTotals() {
        let ingSum = 0, compSum = 0;
        ingredientRowsBody.querySelectorAll('tr').forEach(r => ingSum  += Number(r.dataset.lineTotal || 0));
        componentRowsBody.querySelectorAll('tr').forEach(r  => compSum += Number(r.dataset.lineTotal || 0));
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

    function refreshIngRow(row) {
        const sel     = row.querySelector('.ingredient-select');
        const qty     = row.querySelector('.ingredient-qty');
        const stockEl = row.querySelector('.ingredient-stock');
        const unitEl  = row.querySelector('.ingredient-unit');
        const ucEl    = row.querySelector('.ingredient-unit-cost');
        const ltEl    = row.querySelector('.ingredient-line-total');
        const item    = ingredientMap.get(Number(sel.value));
        const cost    = item ? Number(item.cost) : 0;
        const lt      = cost * Number(qty.value || 0);
        stockEl.textContent = item ? fmt(item.stock, 3) : '-';
        unitEl.textContent  = item ? item.unit : '-';
        ucEl.textContent    = fmt(cost, 4);
        ltEl.textContent    = fmt(lt, 4);
        row.dataset.lineTotal = String(lt);
        recalcTotals();
    }

    function refreshCompRow(row) {
        const sel   = row.querySelector('.component-select');
        const qty   = row.querySelector('.component-qty');
        const yEl   = row.querySelector('.component-yield');
        const ucEl  = row.querySelector('.component-unit-cost');
        const ltEl  = row.querySelector('.component-line-total');
        const item  = componentMap.get(Number(sel.value));
        const cost  = item ? Number(item.cost_per_unit) : 0;
        const lt    = cost * Number(qty.value || 0);
        yEl.textContent  = item ? fmt(item.yield_quantity, 3) : '-';
        ucEl.textContent = fmt(cost, 4);
        ltEl.textContent = fmt(lt, 4);
        row.dataset.lineTotal = String(lt);
        recalcTotals();
    }

    const ROW_CELL  = 'px-4 py-3 text-sm';
    const INPUT_CLS = 'glass-input rounded-lg px-3 py-2 text-sm';

    function addIngRow(data) {
        const row = document.createElement('tr');
        row.innerHTML =
            '<td class="' + ROW_CELL + '"><select class="ingredient-select w-full ' + INPUT_CLS + '">' + ingredientOptionsHtml(data.ingredient_id) + '</select></td>' +
            '<td class="ingredient-stock ' + ROW_CELL + '" style="color:var(--on-surface-var)">-</td>' +
            '<td class="ingredient-unit  ' + ROW_CELL + '" style="color:var(--on-surface-var)">-</td>' +
            '<td class="ingredient-unit-cost ' + ROW_CELL + ' font-mono" style="color:var(--on-surface-var)">0.0000</td>' +
            '<td class="' + ROW_CELL + '"><input type="number" min="0" step="0.001" class="ingredient-qty w-32 ' + INPUT_CLS + ' font-mono" value="' + (data.quantity_required || '') + '"></td>' +
            '<td class="ingredient-line-total ' + ROW_CELL + ' font-semibold font-mono" style="color:var(--on-surface)">0.0000</td>' +
            '<td class="' + ROW_CELL + ' text-right"><button type="button" class="remove-row rounded-lg px-2.5 py-1 text-xs font-medium" style="border:1px solid color-mix(in srgb,var(--error) 30%,transparent);color:var(--error)">' + removeLabel + '</button></td>';
        ingredientRowsBody.appendChild(row);
        row.querySelector('.ingredient-select').addEventListener('change', () => refreshIngRow(row));
        row.querySelector('.ingredient-qty').addEventListener('input',    () => refreshIngRow(row));
        row.querySelector('.remove-row').addEventListener('click', function () {
            row.remove(); renumber(ingredientRowsBody, 'ingredient'); recalcTotals();
        });
        renumber(ingredientRowsBody, 'ingredient');
        refreshIngRow(row);
    }

    function addCompRow(data) {
        const row = document.createElement('tr');
        row.innerHTML =
            '<td class="' + ROW_CELL + '"><select class="component-select w-full ' + INPUT_CLS + '">' + componentOptionsHtml(data.component_recipe_version_id) + '</select></td>' +
            '<td class="component-yield     ' + ROW_CELL + ' font-mono" style="color:var(--on-surface-var)">-</td>' +
            '<td class="component-unit-cost ' + ROW_CELL + ' font-mono" style="color:var(--on-surface-var)">0.0000</td>' +
            '<td class="' + ROW_CELL + '"><input type="number" min="0" step="0.001" class="component-qty w-32 ' + INPUT_CLS + ' font-mono" value="' + (data.quantity_required || '') + '"></td>' +
            '<td class="component-line-total ' + ROW_CELL + ' font-semibold font-mono" style="color:var(--on-surface)">0.0000</td>' +
            '<td class="' + ROW_CELL + ' text-right"><button type="button" class="remove-row rounded-lg px-2.5 py-1 text-xs font-medium" style="border:1px solid color-mix(in srgb,var(--error) 30%,transparent);color:var(--error)">' + removeLabel + '</button></td>';
        componentRowsBody.appendChild(row);
        row.querySelector('.component-select').addEventListener('change', () => refreshCompRow(row));
        row.querySelector('.component-qty').addEventListener('input',    () => refreshCompRow(row));
        row.querySelector('.remove-row').addEventListener('click', function () {
            row.remove(); renumber(componentRowsBody, 'component'); recalcTotals();
        });
        renumber(componentRowsBody, 'component');
        refreshCompRow(row);
    }

    addIngBtn.addEventListener('click',  () => addIngRow({}));
    addCompBtn.addEventListener('click', () => addCompRow({}));
    wasteInput.addEventListener('input', recalcTotals);
    lossInput.addEventListener('input',  recalcTotals);

    if (initialIngredientRows.length > 0) {
        initialIngredientRows.forEach(r => addIngRow(r || {}));
    } else {
        addIngRow({});
    }
    initialComponentRows.forEach(r => addCompRow(r || {}));
    recalcTotals();
});
</script>

{{-- ═══════════════════════════════════════════════════════════════════
     MAIN RECIPES INDEX
═══════════════════════════════════════════════════════════════════ --}}
@else

{{-- Page Header --}}
<div class="mb-6">
    <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)">{{ __('ui.recipes.title') }}</h1>
    <p class="text-sm mt-1" style="color:var(--on-surface-var)">{{ __('ui.recipes.subtitle') }}</p>
</div>

{{-- Tab Switcher --}}
<div class="inline-flex items-center gap-1 p-1 rounded-xl mb-6 glass-panel">
    <button type="button" id="recipesTabTrigger"
            class="rounded-lg px-4 py-2 text-sm font-semibold transition-all">
        <span class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M204-420q8-10 12-24.5t4-35.5q0-30-20-76t-20-69q0-12 2.5-25t13.5-30h60q-11 17-13.5 30t-2.5 25q0 23 20 69t20 76q0 21-4 34.5T264-420h-60Zm260 0q8-10 12-24.5t4-35.5q0-30-20-76t-20-69q0-12 2.5-25t13.5-30h60q-11 17-13.5 30t-2.5 25q0 23 20 69t20 76q0 21-4 34.5T524-420h-60Zm-130 0q8-10 12-24.5t4-35.5q0-30-20-76t-20-69q0-12 2.5-25t13.5-30h60q-11 17-13.5 30t-2.5 25q0 23 20 69t20 76q0 21-4 34.5T394-420h-60Zm56 340q-101 0-178-67.5T120-315q-3-18 9.5-31.5T160-360h421l44-414q5-45 38.5-75.5T744-880q50 0 85 35t35 85q0 14-2.5 37l-2.5 23-79-10 2-20.5q2-20.5 2-29.5 0-17-11.5-28.5T744-800q-16 0-27 10.5T704-764l-46 435q-11 106-87 177.5T390-80Zm0-80q59 0 106-33t68-87H213q23 54 70.5 87T390-160Zm0-120Z"/></svg>
            {{ __('ui.recipes.tabs.recipes') }}
        </span>
    </button>
    <button type="button" id="semiTabTrigger"
            class="rounded-lg px-4 py-2 text-sm font-semibold transition-all">
        <span class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M440-280H280q-83 0-141.5-58.5T80-480q0-83 58.5-141.5T280-680h160v80H280q-50 0-85 35t-35 85q0 50 35 85t85 35h160v80ZM320-440v-80h320v80H320Zm200 160v-80h160q50 0 85-35t35-85q0-50-35-85t-85-35H520v-80h160q83 0 141.5 58.5T880-480q0 83-58.5 141.5T680-280H520Z"/></svg>
            {{ __('ui.recipes.tabs.semi_finished') }}
        </span>
    </button>
</div>

{{-- ── Recipes Tab Panel ─────────────────────────────────────────── --}}
<section id="recipesTabPanel">
    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b flex flex-wrap items-center justify-between gap-4"
             style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <div>
                <h2 class="text-base font-semibold" style="color:var(--on-surface)">{{ __('ui.recipes.product_section.title') }}</h2>
                <p class="text-xs mt-0.5" style="color:var(--on-surface-var)">{{ __('ui.recipes.product_section.subtitle') }}</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute left-3 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--on-surface-var)"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
                    <input id="recipesSearchInput" type="text"
                           placeholder="{{ __('ui.recipes.filters.recipes_search_placeholder') }}"
                           class="glass-input rounded-xl pl-9 pr-4 py-2 text-sm w-56">
                </div>
                <a href="{{ route('recipes.create') }}" class="glass-button-primary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
                    {{ __('ui.recipes.product_section.add_recipe_button') }}
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 35%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.name') }}</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.selling_price') }}</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-right" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    @forelse($products as $product)
                    <tr data-recipe-row data-name="{{ strtolower($product->name) }}"
                        class="transition-colors"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                     style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);border:1px solid color-mix(in srgb,var(--primary) 15%,transparent)">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--primary)"><path d="M204-420q8-10 12-24.5t4-35.5q0-30-20-76t-20-69q0-12 2.5-25t13.5-30h60q-11 17-13.5 30t-2.5 25q0 23 20 69t20 76q0 21-4 34.5T264-420h-60Zm260 0q8-10 12-24.5t4-35.5q0-30-20-76t-20-69q0-12 2.5-25t13.5-30h60q-11 17-13.5 30t-2.5 25q0 23 20 69t20 76q0 21-4 34.5T524-420h-60Zm-130 0q8-10 12-24.5t4-35.5q0-30-20-76t-20-69q0-12 2.5-25t13.5-30h60q-11 17-13.5 30t-2.5 25q0 23 20 69t20 76q0 21-4 34.5T394-420h-60Zm56 340q-101 0-178-67.5T120-315q-3-18 9.5-31.5T160-360h421l44-414q5-45 38.5-75.5T744-880q50 0 85 35t35 85q0 14-2.5 37l-2.5 23-79-10 2-20.5q2-20.5 2-29.5 0-17-11.5-28.5T744-800q-16 0-27 10.5T704-764l-46 435q-11 106-87 177.5T390-80Zm0-80q59 0 106-33t68-87H213q23 54 70.5 87T390-160Zm0-120Z"/></svg>
                                </div>
                                <span class="font-medium" style="color:var(--on-surface)">{{ $product->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 font-mono" style="color:var(--on-surface-var)">{{ \App\Support\CurrencyFormatter::format($product->price) }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('recipes.edit', ['product' => $product]) }}"
                                   class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">
                                    {{ __('ui.common.edit') }}
                                </a>
                                <form method="POST" action="{{ route('recipes.destroy', $product) }}" class="inline-block">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="rounded-lg px-3 py-1.5 text-xs font-medium transition-all"
                                            style="border:1px solid color-mix(in srgb,var(--error) 25%,transparent);color:var(--error)"
                                            onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--error) 8%,transparent)'"
                                            onmouseleave="this.style.backgroundColor='transparent'">
                                        {{ __('ui.common.delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-5 py-10 text-center text-sm" style="color:var(--on-surface-var)">{{ __('ui.recipes.no_products') }}</td>
                    </tr>
                    @endforelse

                    <tr id="recipesFilterNoResults" class="hidden">
                        <td colspan="3" class="px-5 py-8 text-center text-sm" style="color:var(--on-surface-var)">{{ __('ui.recipes.filters.no_recipe_matches') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="px-5 py-4 border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</section>

{{-- ── Semi-Finished Tab Panel ───────────────────────────────────── --}}
<section id="semiTabPanel" class="hidden">
    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b flex flex-wrap items-center justify-between gap-4"
             style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <div>
                <h2 class="text-base font-semibold" style="color:var(--on-surface)">{{ __('ui.recipes.semi_finished.table_title') }}</h2>
                <p class="text-xs mt-0.5" style="color:var(--on-surface-var)">{{ __('ui.recipes.semi_finished.table_subtitle') }}</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute left-3 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--on-surface-var)"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
                    <input id="semiSearchInput" type="text"
                           placeholder="{{ __('ui.recipes.filters.semi_search_placeholder') }}"
                           class="glass-input rounded-xl pl-9 pr-4 py-2 text-sm w-56">
                </div>
                <a href="{{ route('recipes.semi-finished.create') }}" class="glass-button-primary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
                    {{ __('ui.recipes.semi_finished.add_button') }}
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 35%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.name') }}</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.yield') }}</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.cost_per_unit') }}</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.cost') }}</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-right" style="color:var(--on-surface-var)">{{ __('ui.recipes.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    @forelse($semiFinishedRecipes as $recipe)
                    @php
                    $yield       = max((float) $recipe->yield_quantity, 0.001);
                    $costPerUnit = (float) $recipe->total_cost / $yield;
                    @endphp
                    <tr data-semi-row data-name="{{ strtolower($recipe->name) }}"
                        class="transition-colors"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                     style="background-color:color-mix(in srgb,var(--tertiary) 10%,transparent);border:1px solid color-mix(in srgb,var(--tertiary) 15%,transparent)">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--tertiary)"><path d="M440-280H280q-83 0-141.5-58.5T80-480q0-83 58.5-141.5T280-680h160v80H280q-50 0-85 35t-35 85q0 50 35 85t85 35h160v80ZM320-440v-80h320v80H320Zm200 160v-80h160q50 0 85-35t35-85q0-50-35-85t-85-35H520v-80h160q83 0 141.5 58.5T880-480q0 83-58.5 141.5T680-280H520Z"/></svg>
                                </div>
                                <span class="font-medium" style="color:var(--on-surface)">{{ $recipe->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 font-mono" style="color:var(--on-surface-var)">{{ number_format((float) $recipe->yield_quantity, 3) }}</td>
                        <td class="px-5 py-3 font-mono" style="color:var(--on-surface-var)">{{ number_format($costPerUnit, 4) }}</td>
                        <td class="px-5 py-3 font-mono font-semibold" style="color:var(--on-surface)">{{ number_format((float) $recipe->total_cost, 4) }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('recipes.semi-finished.edit', $recipe) }}"
                                   class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">
                                    {{ __('ui.common.edit') }}
                                </a>
                                <form method="POST" action="{{ route('recipes.semi-finished.destroy', $recipe) }}" class="inline-block">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="rounded-lg px-3 py-1.5 text-xs font-medium transition-all"
                                            style="border:1px solid color-mix(in srgb,var(--error) 25%,transparent);color:var(--error)"
                                            onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--error) 8%,transparent)'"
                                            onmouseleave="this.style.backgroundColor='transparent'">
                                        {{ __('ui.common.delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm" style="color:var(--on-surface-var)">{{ __('ui.recipes.semi_finished.none') }}</td>
                    </tr>
                    @endforelse

                    <tr id="semiFilterNoResults" class="hidden">
                        <td colspan="5" class="px-5 py-8 text-center text-sm" style="color:var(--on-surface-var)">{{ __('ui.recipes.filters.no_semi_matches') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<style>
.tab-active {
    background-color: color-mix(in srgb, var(--primary) 12%, transparent);
    border: 1px solid color-mix(in srgb, var(--primary) 25%, transparent);
    color: var(--primary);
}
.tab-inactive {
    background-color: transparent;
    border: 1px solid transparent;
    color: var(--on-surface-var);
}
.tab-inactive:hover {
    background-color: rgba(255,255,255,0.05);
    color: var(--on-surface);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const recipesTrigger  = document.getElementById('recipesTabTrigger');
    const semiTrigger     = document.getElementById('semiTabTrigger');
    const recipesPanel    = document.getElementById('recipesTabPanel');
    const semiPanel       = document.getElementById('semiTabPanel');
    const recipesSearch   = document.getElementById('recipesSearchInput');
    const semiSearch      = document.getElementById('semiSearchInput');
    const recipeRows      = Array.from(document.querySelectorAll('[data-recipe-row]'));
    const semiRows        = Array.from(document.querySelectorAll('[data-semi-row]'));
    const recipesNoResult = document.getElementById('recipesFilterNoResults');
    const semiNoResult    = document.getElementById('semiFilterNoResults');

    function activateTab(tab) {
        if (tab === 'semi') {
            recipesPanel.classList.add('hidden');
            semiPanel.classList.remove('hidden');
            recipesTrigger.className = recipesTrigger.className.replace(/tab-active|tab-inactive/g, '').trim() + ' tab-inactive';
            semiTrigger.className    = semiTrigger.className.replace(/tab-active|tab-inactive/g, '').trim()    + ' tab-active';
        } else {
            semiPanel.classList.add('hidden');
            recipesPanel.classList.remove('hidden');
            semiTrigger.className    = semiTrigger.className.replace(/tab-active|tab-inactive/g, '').trim()    + ' tab-inactive';
            recipesTrigger.className = recipesTrigger.className.replace(/tab-active|tab-inactive/g, '').trim() + ' tab-active';
        }
    }

    function filterRows(rows, query, noResultEl) {
        let visible = 0;
        rows.forEach(function (row) {
            const match = query === '' || String(row.dataset.name || '').includes(query);
            row.classList.toggle('hidden', !match);
            if (match) visible++;
        });
        noResultEl.classList.toggle('hidden', rows.length === 0 || visible > 0);
    }

    recipesTrigger.addEventListener('click', () => activateTab('recipes'));
    semiTrigger.addEventListener('click',    () => activateTab('semi'));

    recipesSearch.addEventListener('input', function () {
        filterRows(recipeRows, recipesSearch.value.toLowerCase().trim(), recipesNoResult);
    });
    semiSearch.addEventListener('input', function () {
        filterRows(semiRows, semiSearch.value.toLowerCase().trim(), semiNoResult);
    });

    activateTab(window.location.hash === '#semi' ? 'semi' : 'recipes');
    filterRows(recipeRows, '', recipesNoResult);
    filterRows(semiRows,   '', semiNoResult);
});
</script>
@endif

</x-layouts.app>
