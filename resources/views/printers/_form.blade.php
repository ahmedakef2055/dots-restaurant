<div class="space-y-5">

    {{-- Name --}}
    <div>
        <label class="form-label">{{ __('ui.printers.name') }} <span class="text-[var(--error)]">*</span></label>
        <input type="text" name="name" value="{{ old('name', $printer?->name) }}"
            class="form-input" placeholder="مثال: طابعة الكاشير" required>
    </div>

    {{-- Windows printer name (from QZ Tray) --}}
    <div>
        <label class="form-label">
            اسم الطابعة (QZ Tray) <span class="text-[var(--error)]">*</span>
        </label>

        {{-- QZ status bar --}}
        <div id="qz-status-{{ $printer?->id ?? 'new' }}" class="mb-2 flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium"
            style="background-color:color-mix(in srgb,var(--surface-highest) 60%,transparent);color:var(--on-surface-var)">
            <span class="h-2 w-2 rounded-full bg-[var(--outline-var)] flex-shrink-0" id="qz-dot-{{ $printer?->id ?? 'new' }}"></span>
            <span id="qz-label-{{ $printer?->id ?? 'new' }}">جاري الاتصال بـ QZ Tray...</span>
        </div>

        {{-- Select populated by JS --}}
        <select name="printer_name" id="printer-select-{{ $printer?->id ?? 'new' }}"
            class="form-input hidden" required>
            <option value="">— اختر طابعة —</option>
            @if($printer?->printer_name)
            <option value="{{ $printer->printer_name }}" selected>{{ $printer->printer_name }}</option>
            @endif
        </select>

        {{-- Fallback text input if QZ not available --}}
        <input type="text" id="printer-input-{{ $printer?->id ?? 'new' }}"
            name="printer_name"
            value="{{ old('printer_name', $printer?->printer_name) }}"
            class="form-input font-mono"
            placeholder="اسم الطابعة كما يظهر في Windows">

        <p class="mt-1 text-xs" style="color:var(--on-surface-var)">
            اسم الطابعة كما يظهر في Windows — يُجلب تلقائياً من QZ Tray
        </p>
    </div>

    {{-- Active --}}
    <div>
        <label class="flex items-center gap-2 cursor-pointer select-none mt-1">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded"
                {{ old('is_active', $printer?->is_active ?? true) ? 'checked' : '' }}>
            <span class="text-sm font-medium" style="color:var(--on-surface)">{{ __('ui.printers.active') }}</span>
        </label>
    </div>

    {{-- Job assignments --}}
    <div>
        <label class="form-label mb-3 block">{{ __('ui.printers.handles') }}</label>
        <div class="space-y-2 rounded-xl border p-4"
            style="border-color:color-mix(in srgb,var(--outline-var) 40%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 40%,transparent)">
            @foreach($allJobs as $jobKey => $jobInfo)
            @php
                $checked = old('handles') !== null
                    ? in_array($jobKey, old('handles', []))
                    : ($printer ? $printer->handles($jobKey) : false);
            @endphp
            <label class="flex items-center gap-3 cursor-pointer rounded-lg px-2 py-1.5 transition hover:bg-[var(--surface-lowest)]">
                <input type="checkbox" name="handles[]" value="{{ $jobKey }}"
                    class="h-4 w-4 rounded accent-blue-600 flex-shrink-0"
                    {{ $checked ? 'checked' : '' }}>
                <div class="flex-1 min-w-0">
                    <span class="text-sm font-medium" style="color:var(--on-surface)">{{ $jobInfo['label'] }}</span>
                    <span class="ms-2 text-xs" style="color:var(--on-surface-var)">{{ $jobInfo['label_en'] }}</span>
                </div>
            </label>
            @endforeach
        </div>
        <p class="mt-1.5 text-xs" style="color:var(--on-surface-var)">
            كل نوع طباعة لطابعة واحدة فقط — عند اختياره هنا يُزال من الطابعة الأخرى تلقائياً.
        </p>
    </div>

    {{-- Notes --}}
    <div>
        <label class="form-label">{{ __('ui.printers.notes') }}</label>
        <input type="text" name="notes" value="{{ old('notes', $printer?->notes) }}"
            class="form-input" placeholder="ملاحظة اختيارية...">
    </div>

</div>

<script>
(function () {
    const uid     = '{{ $printer?->id ?? "new" }}';
    const dot     = document.getElementById('qz-dot-' + uid);
    const label   = document.getElementById('qz-label-' + uid);
    const select  = document.getElementById('printer-select-' + uid);
    const input   = document.getElementById('printer-input-' + uid);
    const current = '{{ old("printer_name", $printer?->printer_name ?? "") }}';

    async function loadPrinters() {
        if (typeof qz === 'undefined') {
            label.textContent = 'QZ Tray غير مثبت — أدخل الاسم يدوياً';
            dot.className = dot.className.replace('bg-[var(--outline-var)]', 'bg-[var(--warning)]');
            // Show text input as active, disable the hidden select's required
            select.removeAttribute('required');
            input.setAttribute('required', '');
            return;
        }

        try {
            qz.security.setCertificatePromise(r => r(''));
            qz.security.setSignatureAlgorithm('SHA512');
            qz.security.setSignaturePromise(() => r => r(''));

            if (!qz.websocket.isActive()) {
                await qz.websocket.connect({ usingSecure: false });
            }

            const printers = await qz.printers.find();

            // Switch from text input to select
            input.name = '';
            input.removeAttribute('required');
            input.classList.add('hidden');
            select.classList.remove('hidden');
            select.name = 'printer_name';
            select.setAttribute('required', '');

            // Clear and populate
            select.innerHTML = '<option value="">— اختر طابعة —</option>';
            printers.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p;
                opt.textContent = p;
                if (p === current) opt.selected = true;
                select.appendChild(opt);
            });

            dot.className = dot.className.replace('bg-[var(--outline-var)]', 'bg-[var(--success-container)]');
            label.textContent = 'متصل بـ QZ Tray — ' + printers.length + ' طابعة متاحة';

        } catch (e) {
            dot.className = dot.className.replace('bg-[var(--outline-var)]', 'bg-[var(--error)]');
            label.textContent = 'تعذّر الاتصال بـ QZ Tray — أدخل الاسم يدوياً';
            select.removeAttribute('required');
            input.setAttribute('required', '');
        }
    }

    loadPrinters();
})();
</script>
