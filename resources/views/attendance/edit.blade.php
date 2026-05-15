<x-layouts.app title="Edit Attendance">

    {{-- Header --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.index') }}" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>Back to Attendance
            </a>
        </div>
    </div>

    <div class="max-w-3xl mx-auto">
        <div class="glass-panel-elevated rounded-2xl overflow-hidden">
            {{-- Card header --}}
            <div class="px-6 py-5 border-b"
                 style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background:linear-gradient(135deg,color-mix(in srgb,var(--tertiary) 4%,transparent),color-mix(in srgb,var(--primary) 4%,transparent))">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl"
                          style="background-color:color-mix(in srgb,var(--tertiary) 12%,transparent);color:var(--tertiary)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[22px]" ><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v200h-80v-40H200v400h280v80H200Zm0-560h560v-80H200v80Zm0 0v-80 80ZM560-80v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T903-300L683-80H560Zm300-263-37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38Zm141-141-19-18 37 37-18-19Z"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold" style="color:var(--on-surface)">Edit Attendance</h2>
                        <p class="text-xs mt-0.5" style="color:var(--on-surface-var)">Update attendance entry for {{ $attendance->employee?->full_name ?? 'Employee' }}</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('attendance.update', $attendance) }}" class="px-6 py-5">
                @csrf
                @method('PUT')
                @include('attendance._form', ['attendance' => $attendance, 'employees' => $employees])

                {{-- Submit --}}
                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="rounded-xl py-2.5 px-6 text-sm font-semibold transition-all flex items-center gap-2"
                            style="background:linear-gradient(135deg,var(--primary),var(--accent-gold));color:var(--on-primary);box-shadow:0 4px 14px color-mix(in srgb,var(--primary) 30%,transparent)"
                            onmouseenter="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 20px color-mix(in srgb,var(--primary) 40%,transparent)'"
                            onmouseleave="this.style.transform='';this.style.boxShadow='0 4px 14px color-mix(in srgb,var(--primary) 30%,transparent)'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM565-275q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z"/></svg>Update Attendance
                    </button>
                    <a href="{{ route('attendance.index') }}" class="glass-button-secondary rounded-xl py-2.5 px-5 text-sm font-medium">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>