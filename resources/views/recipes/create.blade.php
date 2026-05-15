<x-layouts.app :title="__('ui.recipes.add_recipe_picker.title')">

    {{-- Back + Page Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('recipes.index') }}"
           class="glass-button-secondary rounded-lg py-2 px-4 text-sm font-medium flex items-center gap-2 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>
            {{ __('ui.recipes.edit.back') }}
        </a>
        <div>
            <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)">
                {{ __('ui.recipes.add_recipe_picker.title') }}
            </h1>
            <p class="text-sm mt-0.5" style="color:var(--on-surface-var)">
                {{ __('ui.recipes.add_recipe_picker.subtitle') }}
            </p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="glass-panel-elevated rounded-2xl overflow-hidden max-w-4xl mx-auto">

        @if($availableProducts->isEmpty())

        {{-- Empty state: all products already have recipes --}}
        <div class="p-8 flex flex-col items-center text-center gap-4">
            <div class="w-16 h-16 rounded-full flex items-center justify-center"
                 style="background-color:color-mix(in srgb,var(--tertiary) 12%,transparent)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[32px]" style="color:var(--tertiary)"><path d="M204-420q8-10 12-24.5t4-35.5q0-30-20-76t-20-69q0-12 2.5-25t13.5-30h60q-11 17-13.5 30t-2.5 25q0 23 20 69t20 76q0 21-4 34.5T264-420h-60Zm260 0q8-10 12-24.5t4-35.5q0-30-20-76t-20-69q0-12 2.5-25t13.5-30h60q-11 17-13.5 30t-2.5 25q0 23 20 69t20 76q0 21-4 34.5T524-420h-60Zm-130 0q8-10 12-24.5t4-35.5q0-30-20-76t-20-69q0-12 2.5-25t13.5-30h60q-11 17-13.5 30t-2.5 25q0 23 20 69t20 76q0 21-4 34.5T394-420h-60Zm56 340q-101 0-178-67.5T120-315q-3-18 9.5-31.5T160-360h421l44-414q5-45 38.5-75.5T744-880q50 0 85 35t35 85q0 14-2.5 37l-2.5 23-79-10 2-20.5q2-20.5 2-29.5 0-17-11.5-28.5T744-800q-16 0-27 10.5T704-764l-46 435q-11 106-87 177.5T390-80Zm0-80q59 0 106-33t68-87H213q23 54 70.5 87T390-160Zm0-120Z"/></svg>
            </div>
            <div>
                <p class="text-base font-semibold mb-1" style="color:var(--on-surface)">
                    {{ __('messages.errors.all_products_have_recipes') }}
                </p>
            </div>
            <a href="{{ route('recipes.index') }}" class="glass-button-secondary rounded-xl py-2.5 px-6 text-sm font-medium">
                {{ __('ui.recipes.edit.back') }}
            </a>
        </div>

        @else

        <div class="px-6 pt-6 pb-5 border-b"
             style="background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent);border-color:color-mix(in srgb,var(--primary) 8%,transparent)">
            <h2 class="text-sm font-semibold flex items-center gap-2" style="color:var(--on-surface)">
                <span class="inline-flex h-6 w-6 items-center justify-center rounded-md"
                      style="background-color:color-mix(in srgb,var(--primary) 12%,transparent)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" style="color:var(--primary)"><path d="M440-120v-240h80v80h320v80H520v80h-80Zm-320-80v-80h240v80H120Zm160-160v-80H120v-80h160v-80h80v240h-80Zm160-80v-80h400v80H440Zm160-160v-240h80v80h160v80H680v80h-80Zm-480-80v-80h400v80H120Z"/></svg>
                </span>
                Recipe Configuration
            </h2>
        </div>

        <form id="recipeCreateStepForm" class="px-6 py-6 space-y-5" novalidate>

            <div class="grid gap-5 md:grid-cols-4">

                {{-- Product Select --}}
                <div class="md:col-span-2">
                    <label for="recipeProductSelect" class="mb-1.5 block text-sm font-medium"
                           style="color:var(--on-surface-var)">
                        {{ __('ui.recipes.add_recipe_picker.select_product') }}
                        <span style="color:var(--error)">*</span>
                    </label>
                    <div class="relative">
                        <select id="recipeProductSelect" required
                                class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                            <option value="">{{ __('ui.recipes.add_recipe_picker.select_product') }}</option>
                            @foreach($availableProducts as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                    </div>
                </div>

                {{-- Yield Quantity --}}
                <div>
                    <label for="createYieldQuantity" class="mb-1.5 block text-sm font-medium"
                           style="color:var(--on-surface-var)">
                        {{ __('ui.recipes.fields.yield_quantity') }}
                    </label>
                    <input id="createYieldQuantity" type="number" min="0.001" step="0.001" value="1" required
                           class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
                </div>

                {{-- Waste % --}}
                <div>
                    <label for="createWastePercentage" class="mb-1.5 block text-sm font-medium"
                           style="color:var(--on-surface-var)">
                        {{ __('ui.recipes.fields.waste_percent') }}
                    </label>
                    <input id="createWastePercentage" type="number" min="0" max="100" step="0.01" value="0"
                           class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
                </div>

                {{-- Loss % --}}
                <div>
                    <label for="createLossPercentage" class="mb-1.5 block text-sm font-medium"
                           style="color:var(--on-surface-var)">
                        {{ __('ui.recipes.fields.loss_percent') }}
                    </label>
                    <input id="createLossPercentage" type="number" min="0" max="100" step="0.01" value="0"
                           class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
                </div>

            </div>

            <p id="recipeCreateRequiredError" class="hidden text-sm" style="color:var(--error)">
                {{ __('ui.recipes.add_recipe_picker.required') }}
            </p>

            <div class="pt-1 flex items-center gap-3">
                <button type="submit"
                        class="glass-button-primary rounded-xl py-2.5 px-6 text-sm font-semibold flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h560v-280h80v280q0 33-23.5 56.5T760-120H200Zm188-212-56-56 372-372H560v-80h280v280h-80v-144L388-332Z"/></svg>
                    {{ __('ui.recipes.add_recipe_picker.open_button') }}
                </button>
                <a href="{{ route('recipes.index') }}"
                   class="glass-button-secondary rounded-xl py-2.5 px-5 text-sm font-medium">
                    {{ __('ui.recipes.edit.cancel') }}
                </a>
            </div>

        </form>

        @php
        $createData = ['editUrlTemplate' => url('/recipes/__PRODUCT__/edit')];
        @endphp
        <script id="recipeCreateStepData" type="application/json">{!! json_encode($createData, JSON_UNESCAPED_UNICODE) !!}</script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dataEl    = document.getElementById('recipeCreateStepData');
            const cd        = dataEl ? JSON.parse(dataEl.textContent || '{}') : {};
            const form      = document.getElementById('recipeCreateStepForm');
            const prodSel   = document.getElementById('recipeProductSelect');
            const yieldInp  = document.getElementById('createYieldQuantity');
            const wasteInp  = document.getElementById('createWastePercentage');
            const lossInp   = document.getElementById('createLossPercentage');
            const errEl     = document.getElementById('recipeCreateRequiredError');
            const urlTpl    = String(cd.editUrlTemplate || '');

            if (!form || !prodSel || !yieldInp || !wasteInp || !lossInp || urlTpl === '') return;

            function clamp(v) {
                const n = Number(v || 0);
                return Number.isFinite(n) ? Math.min(Math.max(n, 0), 100) : 0;
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const id = Number(prodSel.value || 0);
                if (id <= 0) { if (errEl) errEl.classList.remove('hidden'); prodSel.focus(); return; }
                if (errEl) errEl.classList.add('hidden');
                const params = new URLSearchParams({
                    yield_quantity:   String(Math.max(Number(yieldInp.value || 1), 0.001)),
                    waste_percentage: String(clamp(wasteInp.value)),
                    loss_percentage:  String(clamp(lossInp.value)),
                });
                window.location.href = urlTpl.replace('__PRODUCT__', String(id)) + '?' + params.toString();
            });

            prodSel.addEventListener('change', function () {
                if (errEl && Number(prodSel.value || 0) > 0) errEl.classList.add('hidden');
            });
        });
        </script>
        @endif

    </div>

</x-layouts.app>
