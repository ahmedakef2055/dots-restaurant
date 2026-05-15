<x-layouts.app :title="$customer->full_name">

    {{-- Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <div>
            <nav class="flex items-center gap-1 text-sm mb-2" style="color:var(--on-surface-var)">
                <a href="{{ route('customers.index') }}" class="hover:underline" style="color:var(--on-surface-var)"
                   onmouseenter="this.style.color='var(--primary)'" onmouseleave="this.style.color='var(--on-surface-var)'">Customers</a>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" ><path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/></svg>
                <span style="color:var(--on-surface)">{{ $customer->full_name }}</span>
            </nav>
            <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)">Customer Profile</h1>
        </div>
        <a href="{{ route('customers.edit', $customer) }}" class="glass-button-primary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"/></svg>Edit Profile
        </a>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <div class="glass-panel rounded-xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center border shrink-0"
                 style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);border-color:color-mix(in srgb,var(--primary) 20%,transparent)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[22px]" style="color:var(--primary)"><path d="M240-80q-33 0-56.5-23.5T160-160v-480q0-33 23.5-56.5T240-720h80q0-66 47-113t113-47q66 0 113 47t47 113h80q33 0 56.5 23.5T800-640v480q0 33-23.5 56.5T720-80H240Zm0-80h480v-480h-80v80q0 17-11.5 28.5T600-520q-17 0-28.5-11.5T560-560v-80H400v80q0 17-11.5 28.5T360-520q-17 0-28.5-11.5T320-560v-80h-80v480Zm160-560h160q0-33-23.5-56.5T480-800q-33 0-56.5 23.5T400-720ZM240-160v-480 480Z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">Orders Count</p>
                <p class="text-2xl font-bold" style="color:var(--on-surface)">{{ $ordersCount }}</p>
            </div>
        </div>
        <div class="glass-panel rounded-xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center border shrink-0"
                 style="background-color:color-mix(in srgb,var(--tertiary) 10%,transparent);border-color:color-mix(in srgb,var(--tertiary) 20%,transparent)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[22px]" style="color:var(--tertiary)"><path d="M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">Total Value</p>
                <p class="text-2xl font-bold font-mono" style="color:var(--tertiary)">{{ \App\Support\CurrencyFormatter::format($totalSpent) }}</p>
            </div>
        </div>
        <div class="glass-panel rounded-xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center border shrink-0"
                 style="background-color:color-mix(in srgb,var(--secondary) 10%,transparent);border-color:color-mix(in srgb,var(--secondary) 20%,transparent)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[22px]" style="color:var(--secondary)"><path d="m260-520 220-360 220 360H260ZM700-80q-75 0-127.5-52.5T520-260q0-75 52.5-127.5T700-440q75 0 127.5 52.5T880-260q0 75-52.5 127.5T700-80Zm-580-20v-320h320v320H120Zm580-60q42 0 71-29t29-71q0-42-29-71t-71-29q-42 0-71 29t-29 71q0 42 29 71t71 29Zm-500-20h160v-160H200v160Zm202-420h156l-78-126-78 126Zm78 0ZM360-340Zm340 80Z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">Favorite Category</p>
                <p class="text-base font-bold" style="color:var(--on-surface)">{{ $favoriteMainCategory }}</p>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="grid gap-6 lg:grid-cols-3 mb-6">

        {{-- Profile details --}}
        <div class="glass-panel-elevated rounded-2xl p-6 lg:col-span-2">
            <h2 class="text-base font-semibold mb-5 flex items-center gap-2" style="color:var(--on-surface)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--primary)"><path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z"/></svg>
                Profile
            </h2>
            <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">Full Name</dt>
                    <dd class="font-medium" style="color:var(--on-surface)">{{ $customer->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">Phone</dt>
                    <dd class="font-medium" style="color:var(--on-surface)">{{ $customer->phone ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">Customer Type</dt>
                    <dd>
                        @if(($customer->customer_type ?? 'normal') === 'vip')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              style="background-color:color-mix(in srgb,var(--tertiary) 10%,transparent);border:1px solid color-mix(in srgb,var(--tertiary) 20%,transparent);color:var(--tertiary)">VIP</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              style="background-color:color-mix(in srgb,var(--outline-var) 15%,transparent);border:1px solid color-mix(in srgb,var(--outline-var) 30%,transparent);color:var(--on-surface-var)">Normal</span>
                        @endif
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">Address</dt>
                    <dd class="font-medium" style="color:var(--on-surface)">{{ $customer->address ?: '-' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">Notes</dt>
                    <dd class="text-sm italic" style="color:var(--on-surface-var)">{{ $customer->notes ?: '-' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Account info + delete --}}
        <div class="glass-panel rounded-2xl p-6">
            <h2 class="text-base font-semibold mb-5 flex items-center gap-2" style="color:var(--on-surface)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--secondary)"><path d="M440-280h80v-240h-80v240Zm68.5-331.5Q520-623 520-640t-11.5-28.5Q497-680 480-680t-28.5 11.5Q440-657 440-640t11.5 28.5Q463-600 480-600t28.5-11.5ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                Account Info
            </h2>
            <dl class="space-y-3 text-sm mb-6">
                <div class="flex justify-between">
                    <dt style="color:var(--on-surface-var)">Customer ID</dt>
                    <dd class="font-mono font-medium" style="color:var(--on-surface)">#{{ $customer->id }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt style="color:var(--on-surface-var)">Created</dt>
                    <dd class="font-medium" style="color:var(--on-surface)">{{ $customer->created_at->format('Y-m-d') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt style="color:var(--on-surface-var)">Updated</dt>
                    <dd class="font-medium" style="color:var(--on-surface)">{{ $customer->updated_at->format('Y-m-d') }}</dd>
                </div>
            </dl>
            <form method="POST" action="{{ route('customers.destroy', $customer) }}">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full rounded-xl py-2.5 text-sm font-medium transition-all"
                        style="background-color:color-mix(in srgb,var(--error) 10%,transparent);border:1px solid color-mix(in srgb,var(--error) 25%,transparent);color:var(--error)"
                        onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--error) 18%,transparent)'"
                        onmouseleave="this.style.backgroundColor='color-mix(in srgb,var(--error) 10%,transparent)'">
                    Delete Customer
                </button>
            </form>
        </div>
    </div>

    {{-- Orders table --}}
    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <h3 class="text-base font-semibold" style="color:var(--on-surface)">Latest Orders</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        @foreach(['Order #','Type','Status','Items','Total','Date','Action'] as $h)
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    @forelse($recentOrders as $order)
                    <tr class="transition-colors"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3 font-semibold" style="color:var(--on-surface)">{{ $order->order_number }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ str($order->order_type)->replace('_',' ')->title() }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ str($order->status)->title() }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ (int)$order->items_count }}</td>
                        <td class="px-5 py-3 font-semibold font-mono" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($order->total) }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('orders.show', $order) }}" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-sm" style="color:var(--on-surface-var)">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.app>
