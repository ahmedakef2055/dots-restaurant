@props(['coupon' => null])

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium">Name</label>
        <input name="name" value="{{ old('name', $coupon?->name) }}" required class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
        @error('name')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Code</label>
        <input name="code" value="{{ old('code', $coupon?->code) }}" required class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm uppercase dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
        @error('code')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Discount Type</label>
        <select name="discount_type" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
            <option value="fixed" @selected(old('discount_type', $coupon?->discount_type ?? 'fixed') === 'fixed')>Fixed</option>
            <option value="percentage" @selected(old('discount_type', $coupon?->discount_type) === 'percentage')>Percentage</option>
        </select>
        @error('discount_type')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Discount Value</label>
        <input type="number" min="0.01" step="0.01" name="discount_value" value="{{ old('discount_value', $coupon?->discount_value) }}" required class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
        @error('discount_value')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Min Order Amount</label>
        <input type="number" min="0" step="0.01" name="min_order_amount" value="{{ old('min_order_amount', $coupon?->min_order_amount ?? 0) }}" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
        @error('min_order_amount')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Max Discount Amount</label>
        <input type="number" min="0.01" step="0.01" name="max_discount_amount" value="{{ old('max_discount_amount', $coupon?->max_discount_amount) }}" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
        @error('max_discount_amount')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Starts At</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon?->starts_at?->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
        @error('starts_at')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Ends At</label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $coupon?->ends_at?->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
        @error('ends_at')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Usage Limit</label>
        <input type="number" min="1" step="1" name="usage_limit" value="{{ old('usage_limit', $coupon?->usage_limit) }}" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
        @error('usage_limit')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Per User Limit</label>
        <input type="number" min="1" step="1" name="per_user_limit" value="{{ old('per_user_limit', $coupon?->per_user_limit) }}" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
        @error('per_user_limit')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Status</label>
        <select name="is_active" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
            <option value="1" @selected(old('is_active', (string) (int) ($coupon?->is_active ?? true)) === '1')>Active</option>
            <option value="0" @selected(old('is_active', (string) (int) ($coupon?->is_active ?? true)) === '0')>Inactive</option>
        </select>
        @error('is_active')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium">Notes</label>
        <textarea name="notes" rows="3" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">{{ old('notes', $coupon?->notes) }}</textarea>
        @error('notes')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
    </div>
</div>