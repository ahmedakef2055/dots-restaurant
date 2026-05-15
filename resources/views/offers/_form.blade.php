@props(['offer' => null])

@php
$stackable  = old('stackable_with_coupon', (string)(int)($offer?->stackable_with_coupon ?? false));
$isActiveVal = old('is_active', (string)(int)($offer?->is_active ?? true));
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Name <span style="color:var(--error)">*</span></label>
        <input name="name" value="{{ old('name', $offer?->name) }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('name')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Order Type</label>
        <div class="relative">
            <select name="order_type" class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="">All Order Types</option>
                <option value="dine_in"  @selected(old('order_type', $offer?->order_type) === 'dine_in')>Dine In</option>
                <option value="takeaway" @selected(old('order_type', $offer?->order_type) === 'takeaway')>Takeaway</option>
                <option value="delivery" @selected(old('order_type', $offer?->order_type) === 'delivery')>Delivery</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        @error('order_type')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Discount Type</label>
        <div class="relative">
            <select name="discount_type" class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="fixed"      @selected(old('discount_type', $offer?->discount_type ?? 'fixed') === 'fixed')>Fixed Amount</option>
                <option value="percentage" @selected(old('discount_type', $offer?->discount_type) === 'percentage')>Percentage</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        @error('discount_type')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Discount Value <span style="color:var(--error)">*</span></label>
        <input type="number" min="0.01" step="0.01" name="discount_value"
               value="{{ old('discount_value', $offer?->discount_value) }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
        @error('discount_value')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Min Order Amount</label>
        <input type="number" min="0" step="0.01" name="min_order_amount"
               value="{{ old('min_order_amount', $offer?->min_order_amount ?? 0) }}"
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
        @error('min_order_amount')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Max Discount Amount</label>
        <input type="number" min="0.01" step="0.01" name="max_discount_amount"
               value="{{ old('max_discount_amount', $offer?->max_discount_amount) }}"
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
        @error('max_discount_amount')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Starts At</label>
        <input type="datetime-local" name="starts_at"
               value="{{ old('starts_at', $offer?->starts_at?->format('Y-m-d\TH:i')) }}"
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('starts_at')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Ends At</label>
        <input type="datetime-local" name="ends_at"
               value="{{ old('ends_at', $offer?->ends_at?->format('Y-m-d\TH:i')) }}"
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('ends_at')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Priority (lower = higher)</label>
        <input type="number" min="1" max="999" name="priority"
               value="{{ old('priority', $offer?->priority ?? 100) }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
        @error('priority')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Stackable with Coupon</label>
        <div class="relative">
            <select name="stackable_with_coupon" class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="0" @selected($stackable === '0')>No</option>
                <option value="1" @selected($stackable === '1')>Yes</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Status</label>
        <div class="relative">
            <select name="is_active" class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="1" @selected($isActiveVal === '1')>Active</option>
                <option value="0" @selected($isActiveVal === '0')>Inactive</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        @error('is_active')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Notes</label>
        <textarea name="notes" rows="3" class="w-full rounded-xl glass-input px-4 py-3 text-sm resize-none">{{ old('notes', $offer?->notes) }}</textarea>
        @error('notes')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
</div>
