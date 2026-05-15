@props(['customer' => null])

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Name <span style="color:var(--error)">*</span></label>
        <input name="first_name" value="{{ old('first_name', $customer?->first_name) }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('first_name')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Phone <span style="color:var(--error)">*</span></label>
        <input name="phone" value="{{ old('phone', $customer?->phone) }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('phone')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Address</label>
        <input name="address" value="{{ old('address', $customer?->address) }}"
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('address')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Notes</label>
        <textarea name="notes" rows="3" class="w-full rounded-xl glass-input px-4 py-3 text-sm resize-none">{{ old('notes', $customer?->notes) }}</textarea>
        @error('notes')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Customer Type</label>
        <div class="relative">
            <select name="customer_type" class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="normal" @selected(old('customer_type', $customer?->customer_type ?? 'normal') === 'normal')>Normal</option>
                <option value="vip"    @selected(old('customer_type', $customer?->customer_type ?? 'normal') === 'vip')>VIP</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        @error('customer_type')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
</div>
