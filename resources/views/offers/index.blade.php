<x-layouts.app title="Offers">

    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)">Offers & Promotions</h1>
            <p class="text-sm mt-1" style="color:var(--on-surface-var)">Manage promotional discounts and auto-apply rules</p>
        </div>
        <a href="{{ route('offers.create') }}" class="glass-button-primary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>Create Offer
        </a>
    </div>

    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <p class="text-sm" style="color:var(--on-surface-var)">{{ $offers->total() }} offers total</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        @foreach(['Name','Type','Value','Priority','Status','Expires','Actions'] as $h)
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    @forelse($offers as $offer)
                    <tr class="transition-colors"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3 font-semibold" style="color:var(--primary)">{{ strtoupper($offer->name) }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ ucfirst($offer->discount_type) }}</td>
                        <td class="px-5 py-3 font-semibold font-mono" style="color:var(--on-surface)">
                            {{ $offer->discount_type === 'percentage'
                                ? rtrim(rtrim((string)$offer->discount_value,'0'),'.') . '%'
                                : \App\Support\CurrencyFormatter::format($offer->discount_value) }}
                        </td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $offer->priority ?? '-' }}</td>
                        <td class="px-5 py-3">
                            @if($offer->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)] text-[var(--success)] border border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--success)] animate-pulse"></span>Active
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--error) 10%,transparent);border:1px solid color-mix(in srgb,var(--error) 20%,transparent);color:var(--error)">
                                Inactive
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $offer->ends_at?->format('Y-m-d') ?? ($offer->end_date ?? '-') }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('offers.show', $offer) }}" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">View</a>
                                <a href="{{ route('offers.edit', $offer) }}" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">Edit</a>
                                <form method="POST" action="{{ route('offers.destroy', $offer) }}" class="inline-block">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-medium transition-all"
                                            style="border:1px solid color-mix(in srgb,var(--error) 25%,transparent);color:var(--error)"
                                            onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--error) 8%,transparent)'"
                                            onmouseleave="this.style.backgroundColor=''">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-sm" style="color:var(--on-surface-var)">No offers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($offers->hasPages())
        <div class="px-5 py-4 border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)">{{ $offers->withQueryString()->links() }}</div>
        @endif
    </div>

</x-layouts.app>
