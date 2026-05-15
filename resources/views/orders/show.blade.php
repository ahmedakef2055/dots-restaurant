<x-layouts.app :title="__('ui.orders.col.number', ['default' => 'Order #']) . ($order->order_daily_number ?: $order->order_number)">
<div
    x-data="{
        showDeleteModal: false,
        showItemDeleteModal: false,
        pendingDeleteBtn: null,
        deleteAction: '',
        deleteOrderNumber: '',
        discountType: '{{ old('discount_type', $order->discount_type ?: 'none') }}',
        loading: false,

        async updateQty(btn) {
            if (this.loading) return;
            this.loading = true;
            const row = btn.closest('tr');
            btn.disabled = true;
            try {
                const res = await fetch(btn.dataset.url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ action: btn.dataset.action }),
                });
                const json = await res.json();
                if (!res.ok) {
                    const msg = json.message || json.errors?.quantity?.[0] || 'Error';
                    this.showToast(msg, 'error');
                    return;
                }
                const d = json.data;
                row.querySelector('.qty-val').textContent = d.quantity;
                row.querySelector('.line-total-val').textContent = d.line_total_formatted;
                document.getElementById('subtotal-val').textContent = d.subtotal_formatted;
                document.getElementById('total-val').textContent = d.total_formatted;
                const decBtn = row.querySelector('[data-action=decrement]');
                if (decBtn) decBtn.disabled = d.quantity <= 1;
            } catch (e) {
                this.showToast('Request failed', 'error');
            } finally {
                this.loading = false;
                row.querySelectorAll('.order-qty-btn').forEach(b => b.disabled = false);
            }
        },

        confirmDeleteItem(btn) {
            this.pendingDeleteBtn = btn;
            this.showItemDeleteModal = true;
        },

        async executeDeleteItem() {
            this.showItemDeleteModal = false;
            const btn = this.pendingDeleteBtn;
            if (!btn || this.loading) return;
            this.loading = true;
            const row = btn.closest('tr');
            row.style.opacity = '0.4';
            try {
                const res = await fetch(btn.dataset.url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                });
                const json = await res.json();
                if (!res.ok) {
                    row.style.opacity = '';
                    this.showToast(json.message || 'Error', 'error');
                    return;
                }
                if (json.order_deleted) {
                    window.location.href = '{{ route('orders.index') }}';
                    return;
                }
                row.remove();
                document.getElementById('subtotal-val').textContent = json.subtotal_formatted;
                document.getElementById('total-val').textContent = json.total_formatted;
                this.showToast('Item removed successfully', 'success');
            } catch (e) {
                row.style.opacity = '';
                this.showToast('Request failed', 'error');
            } finally {
                this.loading = false;
                this.pendingDeleteBtn = null;
            }
        },

        showToast(msg, type) {
            const t = document.getElementById('order-toast');
            if (!t) return;
            t.querySelector('.toast-msg').textContent = msg;
            const icon = type === 'error' ? 'error' : 'check_circle';
            t.querySelector('.toast-icon').textContent = icon;
            t.style.background = type === 'error'
                ? 'linear-gradient(135deg,var(--error),color-mix(in srgb,var(--error) 80%,#000 20%))'
                : 'linear-gradient(135deg,var(--success),color-mix(in srgb,var(--success) 80%,#000 20%))';
            t.style.opacity = '1';
            t.style.transform = 'translateX(-50%) translateY(0)';
            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(() => {
                t.style.opacity = '0';
                t.style.transform = 'translateX(-50%) translateY(16px)';
            }, 3500);
        }
    }"
>

{{-- Toast --}}
<div id="order-toast"
     class="fixed bottom-8 left-1/2 z-[200] pointer-events-none"
     style="transform:translateX(-50%) translateY(16px);opacity:0;transition:opacity .3s ease,transform .3s ease;border-radius:1rem;box-shadow:0 20px 60px rgba(0,0,0,.3);min-width:240px;max-width:400px;padding:14px 20px;display:flex;align-items:center;gap:10px;background:linear-gradient(135deg,var(--success),color-mix(in srgb,var(--success) 80%,#000 20%));color:#fff">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 toast-icon  text-[20px]" style="font-variation-settings:'FILL' 1"><path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
    <span class="toast-msg text-sm font-bold"></span>
</div>

{{-- ── Top Bar ── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('orders.index') }}"
           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border text-sm font-semibold transition-all"
           style="border-color:color-mix(in srgb,var(--outline-var) 50%,transparent);background:var(--surface-lowest);color:var(--on-surface-var)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>
            {{ __('ui.common.back', ['default' => 'Back']) }}
        </a>
        <div>
            <h1 class="text-xl font-extrabold tracking-tight" style="color:var(--on-surface)">
                @if($order->order_daily_number)
                    {{ __('ui.orders.col.number', ['default' => 'Order #']) }}{{ $order->order_daily_number }}
                @else
                    {{ $order->order_number }}
                @endif
            </h1>
            <p class="text-xs mt-0.5 font-mono" style="color:var(--outline)">
                {{ $order->order_number }} · {{ $order->created_at->format('M d, Y · H:i') }}
            </p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        @php
            $statusColors = [
                'paid'      => ['bg'=>'color-mix(in srgb,var(--success) 12%,transparent 88%)','color'=>'var(--success)','dot'=>'bg-[var(--success)]'],
                'cancelled' => ['bg'=>'color-mix(in srgb,var(--error) 12%,transparent 88%)','color'=>'var(--error)','dot'=>'bg-[var(--error)]'],
                'pending'   => ['bg'=>'color-mix(in srgb,var(--warning) 12%,transparent 88%)','color'=>'var(--warning)','dot'=>'bg-[var(--warning)] animate-pulse'],
            ];
            $sc = $statusColors[$order->status] ?? $statusColors['pending'];
        @endphp
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold"
              style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
            {{ ucfirst($order->status) }}
        </span>
        @if($order->status === 'paid')
        <button
            type="button"
            onclick="queuePrintJob({{ $order->order_serial }}, this)"
            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border text-sm font-semibold transition-all hover:opacity-80"
            style="border-color:color-mix(in srgb,var(--primary) 40%,transparent);background:color-mix(in srgb,var(--primary) 10%,transparent);color:var(--primary)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[15px]"><path d="M640-640v-120H320v120h-80v-200h480v200h-80Zm-480 80h640-640Zm560 100q17 0 28.5-11.5T760-500q0-17-11.5-28.5T720-540q-17 0-28.5 11.5T680-500q0 17 11.5 28.5T720-460Zm-80 260v-160H320v160h320Zm80 80H240v-160H80v-240q0-51 35-85.5t85-34.5h560q51 0 85.5 34.5T880-520v240H720v160Zm80-240v-160q0-17-11.5-28.5T760-560H200q-17 0-28.5 11.5T160-520v160h80v-80h480v80h80Z"/></svg>
            طباعة الفاتورة
        </button>
        @endif
    </div>
</div>

{{-- ── Main Grid ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

    {{-- ─── LEFT: Items Table ─── --}}
    <div class="lg:col-span-2 rounded-2xl border shadow-sm overflow-hidden"
         style="border-color:color-mix(in srgb,var(--outline-var) 40%,transparent);background:var(--surface-lowest)">

        <div class="flex items-center gap-2 px-5 py-4 border-b"
             style="border-color:color-mix(in srgb,var(--outline-var) 30%,transparent)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--primary)"><path d="M240-80q-50 0-85-35t-35-85v-120h120v-560l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v680q0 50-35 85t-85 35H240Zm480-80q17 0 28.5-11.5T760-200v-560H320v440h360v120q0 17 11.5 28.5T720-160ZM360-600v-80h240v80H360Zm0 120v-80h240v80H360Zm320-120q-17 0-28.5-11.5T640-640q0-17 11.5-28.5T680-680q17 0 28.5 11.5T720-640q0 17-11.5 28.5T680-600Zm0 120q-17 0-28.5-11.5T640-520q0-17 11.5-28.5T680-560q17 0 28.5 11.5T720-520q0 17-11.5 28.5T680-480ZM240-160h360v-80H200v40q0 17 11.5 28.5T240-160Zm-40 0v-80 80Z"/></svg>
            <span class="font-bold text-sm" style="color:var(--on-surface)">Order Items</span>
            <span class="ml-auto text-xs px-2 py-0.5 rounded-full font-bold"
                  style="background:color-mix(in srgb,var(--primary) 12%,transparent);color:var(--primary)">
                {{ $order->items->count() }} items
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr style="background:color-mix(in srgb,var(--surface-high) 60%,transparent)">
                        <th class="px-5 py-3 text-[10px] font-extrabold uppercase tracking-widest" style="color:var(--outline)">Product</th>
                        <th class="px-4 py-3 text-[10px] font-extrabold uppercase tracking-widest text-center" style="color:var(--outline)">Qty</th>
                        <th class="px-4 py-3 text-[10px] font-extrabold uppercase tracking-widest" style="color:var(--outline)">Unit Price</th>
                        <th class="px-4 py-3 text-[10px] font-extrabold uppercase tracking-widest text-right" style="color:var(--outline)">Line Total</th>
                        <th class="px-4 py-3 text-[10px] font-extrabold uppercase tracking-widest" style="color:var(--outline)">Notes</th>
                        @if($order->status === 'pending')
                        <th class="px-4 py-3 text-[10px] font-extrabold uppercase tracking-widest text-center w-16" style="color:var(--outline)"></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr class="border-t group transition-all duration-150"
                        style="border-color:color-mix(in srgb,var(--outline-var) 25%,transparent)"
                        onmouseenter="this.style.background='color-mix(in srgb,var(--primary) 6%,transparent)';this.style.boxShadow='inset 3px 0 0 var(--primary)'"
                        onmouseleave="this.style.background='';this.style.boxShadow=''"
                        data-item-id="{{ $item->id }}">

                        <td class="px-5 py-3.5 font-semibold" style="color:var(--on-surface)">
                            {{ $item->product->name }}
                        </td>

                        <td class="px-4 py-3.5 text-center">
                            @if($order->status === 'pending')
                            <div class="inline-flex items-center gap-1">
                                <button type="button"
                                        class="order-qty-btn w-7 h-7 flex items-center justify-center rounded-lg border transition-all hover:border-[color:var(--primary)] hover:text-[color:var(--primary)] disabled:opacity-40 disabled:cursor-not-allowed"
                                        style="border-color:color-mix(in srgb,var(--outline-var) 60%,transparent);background:var(--surface-low);color:var(--on-surface-var)"
                                        data-url="{{ route('orders.items.quantity.update', [$order, $item]) }}"
                                        data-action="decrement"
                                        @click="updateQty($el)"
                                        {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="M200-440v-80h560v80H200Z"/></svg>
                                </button>
                                <span class="qty-val w-8 text-center font-bold text-sm" style="color:var(--on-surface)">{{ $item->quantity }}</span>
                                <button type="button"
                                        class="order-qty-btn w-7 h-7 flex items-center justify-center rounded-lg border transition-all hover:border-[color:var(--primary)] hover:text-[color:var(--primary)]"
                                        style="border-color:color-mix(in srgb,var(--outline-var) 60%,transparent);background:var(--surface-low);color:var(--on-surface-var)"
                                        data-url="{{ route('orders.items.quantity.update', [$order, $item]) }}"
                                        data-action="increment"
                                        @click="updateQty($el)">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
                                </button>
                            </div>
                            @else
                            <span class="font-bold text-sm" style="color:var(--on-surface)">{{ $item->quantity }}</span>
                            @endif
                        </td>

                        <td class="px-4 py-3.5 text-sm" style="color:var(--on-surface-var)">
                            {{ \App\Support\CurrencyFormatter::format($item->unit_price) }}
                        </td>

                        <td class="px-4 py-3.5 text-right font-bold text-sm" style="color:var(--on-surface)">
                            <span class="line-total-val">{{ \App\Support\CurrencyFormatter::format($item->subtotal) }}</span>
                        </td>

                        <td class="px-4 py-3.5 text-xs" style="color:var(--outline)">
                            {{ $item->notes ?: '—' }}
                        </td>

                        @if($order->status === 'pending')
                        <td class="px-4 py-3.5 text-center">
                            <button type="button"
                                    class="w-7 h-7 inline-flex items-center justify-center rounded-lg border transition-all opacity-0 group-hover:opacity-100 hover:bg-[var(--error-container)] hover:border-[var(--error-container)] hover:text-[var(--error)]"
                                    style="border-color:color-mix(in srgb,var(--outline-var) 40%,transparent);color:var(--outline)"
                                    data-url="{{ route('orders.items.destroy', [$order, $item]) }}"
                                    @click="confirmDeleteItem($el)"
                                    title="Remove item">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[15px]" ><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
                            </button>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div class="px-5 py-4 border-t" style="border-color:color-mix(in srgb,var(--outline-var) 30%,transparent)">
            <div class="flex flex-col items-end gap-2 text-sm">
                <div class="flex justify-between w-56">
                    <span style="color:var(--on-surface-var)">Subtotal</span>
                    <span class="font-semibold" style="color:var(--on-surface)" id="subtotal-val">
                        {{ \App\Support\CurrencyFormatter::format($order->subtotal) }}
                    </span>
                </div>
                @if($order->discount_amount > 0)
                <div class="flex justify-between w-56">
                    <span style="color:var(--on-surface-var)">Discount</span>
                    <span class="font-semibold text-[var(--success)]">
                        -{{ \App\Support\CurrencyFormatter::format($order->discount_amount) }}
                    </span>
                </div>
                @endif
                <div class="flex justify-between w-56 pt-2 border-t" style="border-color:color-mix(in srgb,var(--outline-var) 40%,transparent)">
                    <span class="font-extrabold text-base" style="color:var(--on-surface)">Total</span>
                    <span class="font-extrabold text-base" style="color:var(--primary)" id="total-val">
                        {{ \App\Support\CurrencyFormatter::format($order->total) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── RIGHT: Actions Panel ─── --}}
    <div class="flex flex-col gap-4">

        {{-- Order Info Card --}}
        <div class="rounded-2xl border shadow-sm overflow-hidden"
             style="border-color:color-mix(in srgb,var(--outline-var) 40%,transparent);background:var(--surface-lowest)">
            <div class="flex items-center gap-2 px-5 py-4 border-b"
                 style="border-color:color-mix(in srgb,var(--outline-var) 30%,transparent)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--primary)"><path d="M440-280h80v-240h-80v240Zm68.5-331.5Q520-623 520-640t-11.5-28.5Q497-680 480-680t-28.5 11.5Q440-657 440-640t11.5 28.5Q463-600 480-600t28.5-11.5ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                <span class="font-bold text-sm" style="color:var(--on-surface)">Order Details</span>
            </div>
            <div class="px-5 py-4 flex flex-col gap-3 text-sm">
                @if($order->order_daily_number)
                <div class="flex justify-between items-center gap-2">
                    <span style="color:var(--outline)">{{ __('ui.orders.col.number', ['default' => 'Order #']) }}</span>
                    <span class="font-bold text-right" style="color:var(--primary)">#{{ $order->order_daily_number }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center gap-2">
                    <span style="color:var(--outline)">{{ __('ui.orders.detail.serial', ['default' => 'Order Serial']) }}</span>
                    <span class="font-bold text-right font-mono text-xs" style="color:var(--on-surface)">{{ $order->order_number }}</span>
                </div>
                @foreach([
                    ['Type',     str($order->order_type)->replace('_',' ')->title(), ''],
                    ['Table',    $order->restaurantTable?->name ?? '—', ''],
                    ['Status',   ucfirst($order->status), ''],
                    ['Date',     $order->created_at->format('Y-m-d H:i'), 'text-xs'],
                    ['Cashier',  $order->cashier?->name ?? '—', ''],
                ] as [$label, $value, $cls])
                <div class="flex justify-between items-center gap-2">
                    <span style="color:var(--outline)">{{ $label }}</span>
                    <span class="font-bold text-right {{ $cls }}" style="color:var(--on-surface)">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Discount Card (pending only) --}}
        @if($order->status === 'pending')
        <div class="rounded-2xl border shadow-sm overflow-hidden"
             style="border-color:color-mix(in srgb,var(--outline-var) 40%,transparent);background:var(--surface-lowest)">
            <div class="flex items-center gap-2 px-5 py-4 border-b"
                 style="border-color:color-mix(in srgb,var(--outline-var) 30%,transparent)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--primary)"><path d="m520-260 140-140q11-11 17.5-26t6.5-32q0-34-24-58t-58-24q-19 0-37.5 11T520-492q-30-28-47-38t-35-10q-34 0-58 24t-24 58q0 17 6.5 32t17.5 26l140 140Zm336-130L570-104q-12 12-27 18t-30 6q-15 0-30-6t-27-18L103-457q-11-11-17-25.5T80-513v-287q0-33 23.5-56.5T160-880h287q16 0 31 6.5t26 17.5l352 353q12 12 17.5 27t5.5 30q0 15-5.5 29.5T856-390ZM513-160l286-286-353-354H160v286l353 354ZM260-640q25 0 42.5-17.5T320-700q0-25-17.5-42.5T260-760q-25 0-42.5 17.5T200-700q0 25 17.5 42.5T260-640Zm220 160Z"/></svg>
                <span class="font-bold text-sm" style="color:var(--on-surface)">Discount & Coupon</span>
            </div>
            <div class="px-5 py-4">
                <form action="{{ route('orders.discount.update', $order) }}" method="POST" class="flex flex-col gap-3">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest mb-1.5" style="color:var(--outline)">Discount Type</label>
                        <select name="discount_type" x-model="discountType"
                                class="w-full h-9 px-3 rounded-xl border text-sm transition-all outline-none focus:ring-2"
                                style="border-color:color-mix(in srgb,var(--outline-var) 60%,transparent);background:var(--surface-low);color:var(--on-surface);--tw-ring-color:var(--primary)">
                            <option value="none">Without Discount</option>
                            <option value="fixed">Fixed Amount</option>
                            <option value="percentage">Percentage %</option>
                        </select>
                    </div>
                    <div x-show="discountType !== 'none'" x-cloak>
                        <label class="block text-[10px] font-bold uppercase tracking-widest mb-1.5" style="color:var(--outline)">Discount Value</label>
                        <input type="number" step="0.01" min="0" name="discount_value"
                               value="{{ old('discount_value', $order->discount_value) }}"
                               class="w-full h-9 px-3 rounded-xl border text-sm outline-none focus:ring-2"
                               style="border-color:color-mix(in srgb,var(--outline-var) 60%,transparent);background:var(--surface-low);color:var(--on-surface);--tw-ring-color:var(--primary)">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest mb-1.5" style="color:var(--outline)">Coupon Code</label>
                        <input type="text" name="coupon_code" value="{{ old('coupon_code', $order->coupon_code) }}"
                               placeholder="ENTER CODE"
                               class="w-full h-9 px-3 rounded-xl border text-sm uppercase outline-none focus:ring-2 placeholder:normal-case placeholder:tracking-normal"
                               style="border-color:color-mix(in srgb,var(--outline-var) 60%,transparent);background:var(--surface-low);color:var(--on-surface);--tw-ring-color:var(--primary)">
                    </div>
                    <button type="submit"
                            class="w-full h-9 rounded-xl border text-sm font-bold transition-all hover:opacity-90"
                            style="border-color:color-mix(in srgb,var(--primary) 30%,transparent);background:color-mix(in srgb,var(--primary) 10%,transparent);color:var(--primary)">
                        Apply Discount
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Status Update (hidden when paid) --}}
        @if($order->status !== 'paid')
        <div class="rounded-2xl border shadow-sm overflow-hidden"
             style="border-color:color-mix(in srgb,var(--outline-var) 40%,transparent);background:var(--surface-lowest)">
            <div class="flex items-center gap-2 px-5 py-4 border-b"
                 style="border-color:color-mix(in srgb,var(--outline-var) 30%,transparent)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--primary)"><path d="M160-160v-80h110l-16-14q-52-46-73-105t-21-119q0-111 66.5-197.5T400-790v84q-72 26-116 88.5T240-478q0 45 17 87.5t53 78.5l10 10v-98h80v240H160Zm400-10v-84q72-26 116-88.5T720-482q0-45-17-87.5T650-648l-10-10v98h-80v-240h240v80H690l16 14q49 49 71.5 106.5T800-482q0 111-66.5 197.5T560-170Z"/></svg>
                <span class="font-bold text-sm" style="color:var(--on-surface)">Update Status</span>
            </div>
            <div class="px-5 py-4">
                <form action="{{ route('orders.status.update', $order) }}" method="POST" class="flex flex-col gap-3" x-data="{ updateStatusVal: '{{ $order->status }}', paymentMethod: 'cash' }">
                    @csrf
                    @method('PATCH')
                    <select name="status" x-model="updateStatusVal"
                            class="w-full h-9 px-3 rounded-xl border text-sm outline-none focus:ring-2"
                            style="border-color:color-mix(in srgb,var(--outline-var) 60%,transparent);background:var(--surface-low);color:var(--on-surface);--tw-ring-color:var(--primary)">
                        <option value="pending"   @selected($order->status === 'pending')>Pending</option>
                        <option value="paid"      @selected($order->status === 'paid')>Paid</option>
                        <option value="cancelled" @selected($order->status === 'cancelled')>Cancelled</option>
                    </select>

                    {{-- Payment method appears when Paid is selected --}}
                    <div x-cloak x-show="updateStatusVal === 'paid'" class="mt-1">
                        <label class="block text-[10px] font-bold uppercase tracking-widest mb-1.5" style="color:var(--outline)">Payment Method</label>
                        <div class="grid grid-cols-4 gap-1.5">
                            @php
                                $pmMethods = [
                                    'cash'     => ['label_en' => 'Cash',     'label_ar' => 'كاش',   'svg' => 'M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z'],
                                    'visa'     => ['label_en' => 'Visa',     'label_ar' => 'فيزا',  'svg' => 'M880-720v480q0 33-23.5 56.5T800-160H160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720Zm-720 80h480v-80H160v80Zm0 160v240h640v-240H160Zm0 240v-480 480Z'],
                                    'instapay' => ['label_en' => 'Instapay', 'label_ar' => 'إنستاباي', 'svg' => 'M280-40q-33 0-56.5-23.5T200-120v-720q0-33 23.5-56.5T280-920h400q33 0 56.5 23.5T760-840v720q0 33-23.5 56.5T680-40H280Zm0-200v120h400v-120H280Zm200 100q17 0 28.5-11.5T520-180q0-17-11.5-28.5T480-220q-17 0-28.5 11.5T440-180q0 17 11.5 28.5T480-140ZM280-320h400v-400H280v400Zm0-480h400v-40H280v40Zm0 560v120-120Zm0-560v-40 40Z'],
                                    'wallet'   => ['label_en' => 'Wallet',   'label_ar' => 'محفظة', 'svg' => 'M240-160q-66 0-113-47T80-320v-320q0-66 47-113t113-47h480q66 0 113 47t47 113v320q0 66-47 113t-113 47H240Zm0-480h480q22 0 42 5t38 15v-20q0-33-23.5-56.5T720-720H240q-33 0-56.5 23.5T160-640v20q18-10 38-15t42-5Zm-74 130 445 108q9 2 18 0t16-8l139-139q-11-15-28-23t-36-8H240q-26 0-46 13.5T166-510Z'],
                                ];
                                $isAr = app()->getLocale() === 'ar';
                            @endphp
                            @foreach($pmMethods as $pmKey => $pm)
                            <button type="button" @click="paymentMethod = '{{ $pmKey }}'"
                                :style="paymentMethod === '{{ $pmKey }}'
                                    ? 'border-color:var(--primary);background:color-mix(in srgb,var(--primary) 10%,transparent 90%);color:var(--primary)'
                                    : 'border-color:var(--outline-var);color:var(--on-surface-var)'"
                                class="flex flex-col items-center gap-1 rounded-lg border py-2 text-[10px] font-semibold transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-4 h-4"><path d="{{ $pm['svg'] }}"/></svg>
                                <span>{{ $isAr ? $pm['label_ar'] : $pm['label_en'] }}</span>
                            </button>
                            @endforeach
                            <input type="hidden" name="payment_method" :value="paymentMethod">
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full h-9 rounded-xl text-sm font-bold text-white transition-all hover:opacity-90"
                            style="background:var(--primary)">
                        Save Status
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Delete Order --}}
        <button type="button"
                class="w-full h-10 rounded-xl text-sm font-bold text-white transition-all hover:opacity-90"
                style="background:var(--error);box-shadow:0 4px 14px color-mix(in srgb,var(--error) 25%,transparent 75%)"
                @click="deleteAction='{{ route('orders.destroy', $order) }}'; deleteOrderNumber='{{ $order->order_number }}'; showDeleteModal=true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[15px] align-middle mr-1" ><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
            Delete Order
        </button>
    </div>
</div>

{{-- Item Delete Modal (teleported to body for full-screen blur) --}}
<template x-teleport="body">
    <div x-cloak x-show="showItemDeleteModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center"
         style="backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);background:rgba(0,0,0,.6)">
        <div class="absolute inset-0" @click="showItemDeleteModal=false"></div>
        <div x-show="showItemDeleteModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             class="relative rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-8 text-center"
             style="background:var(--surface-lowest);border:1px solid color-mix(in srgb,var(--outline-var) 40%,transparent);box-shadow:0 32px 80px rgba(0,0,0,.5)">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:color-mix(in srgb,var(--error) 12%,transparent 88%)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-3xl text-[var(--error)]" style="font-variation-settings:'FILL' 1"><path d="M360-640v-80h240v80H360ZM223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM40-800v-80h131l170 360h280l156-280h91L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68.5-39t-1.5-79l54-98-144-304H40Z"/></svg>
            </div>
            <h3 class="text-lg font-bold mb-2" style="color:var(--on-surface)">Remove Item</h3>
            <p class="text-sm mb-6" style="color:var(--on-surface-var)">Are you sure you want to remove this item from the order?</p>
            <div class="flex gap-3">
                <button type="button"
                        class="flex-1 px-4 py-2.5 rounded-xl border text-sm font-semibold transition-all hover:opacity-80"
                        style="border-color:color-mix(in srgb,var(--outline-var) 60%,transparent);background:var(--surface-low);color:var(--on-surface-var)"
                        @click="showItemDeleteModal=false">
                    Cancel
                </button>
                <button type="button"
                        class="flex-1 py-2.5 rounded-xl font-bold text-white text-sm transition-all hover:opacity-90 active:scale-95"
                        style="background:linear-gradient(135deg,var(--error),var(--error));box-shadow:0 4px 16px color-mix(in srgb,var(--error) 35%,transparent 65%)"
                        @click="executeDeleteItem()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[15px] align-middle mr-1" style="font-variation-settings:'FILL' 1"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
                    Remove
                </button>
            </div>
        </div>
    </div>
</template>

{{-- Order Delete Modal (teleported to body for full-screen blur) --}}
<template x-teleport="body">
    <div x-cloak x-show="showDeleteModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center"
         style="backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);background:rgba(0,0,0,.6)">
        <div class="absolute inset-0" @click="showDeleteModal=false"></div>
        <div x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             class="relative rounded-2xl w-full max-w-sm mx-4 p-8 text-center"
             style="background:var(--surface-lowest);border:1px solid color-mix(in srgb,var(--outline-var) 40%,transparent);box-shadow:0 32px 80px rgba(0,0,0,.5)">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:color-mix(in srgb,var(--error) 12%,transparent 88%)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-3xl text-[var(--error)]" style="font-variation-settings:'FILL' 1"><path d="m376-300 104-104 104 104 56-56-104-104 104-104-56-56-104 104-104-104-56 56 104 104-104 104 56 56Zm-96 180q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520Zm-400 0v520-520Z"/></svg>
            </div>
            <h3 class="text-lg font-bold mb-2" style="color:var(--on-surface)">Delete Order</h3>
            <p class="text-sm mb-6" style="color:var(--on-surface-var)">
                Delete order <strong x-text="deleteOrderNumber" class="font-mono" style="color:var(--on-surface)"></strong>?
                <br><span class="text-xs text-[var(--error)] font-medium">This action cannot be undone.</span>
            </p>
            <div class="flex gap-3">
                <button type="button"
                        class="flex-1 px-4 py-2.5 rounded-xl border text-sm font-semibold transition-all hover:opacity-80"
                        style="border-color:color-mix(in srgb,var(--outline-var) 60%,transparent);background:var(--surface-low);color:var(--on-surface-var)"
                        @click="showDeleteModal=false">
                    Cancel
                </button>
                <form :action="deleteAction" method="POST" data-delete-confirm-skip="true" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full py-2.5 rounded-xl font-bold text-white text-sm transition-all hover:opacity-90 active:scale-95"
                            style="background:linear-gradient(135deg,var(--error),var(--error));box-shadow:0 4px 16px color-mix(in srgb,var(--error) 35%,transparent 65%)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[15px] align-middle mr-1" style="font-variation-settings:'FILL' 1"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
                        Delete Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

</div>
</x-layouts.app>