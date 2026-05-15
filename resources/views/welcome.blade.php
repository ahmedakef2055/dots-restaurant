<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Point 88') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full">
    <div class="relative min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-24 top-4 h-72 w-72 rounded-full bg-[color-mix(in_srgb,var(--primary)_10%,transparent_90%)] blur-3xl dark:bg-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]"></div>
            <div class="absolute right-0 top-1/3 h-80 w-80 rounded-full bg-[color-mix(in_srgb,var(--accent-gold)_12%,transparent_88%)] blur-3xl dark:bg-[color-mix(in_srgb,var(--accent-gold)_10%,transparent_90%)]"></div>
            <div class="absolute bottom-0 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-[color-mix(in_srgb,var(--accent-gold)_8%,transparent_92%)] blur-3xl dark:bg-[color-mix(in_srgb,var(--accent-gold)_8%,transparent_92%)]"></div>
            <div class="absolute left-1/3 top-1/2 h-64 w-64 -translate-y-1/2 rounded-full bg-[color-mix(in_srgb,var(--accent-gold)_8%,transparent_92%)] blur-3xl dark:bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)]"></div>
        </div>

        <header class="relative z-20 mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Point 88" class="h-10 w-10 object-contain rounded-xl shrink-0">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.14em]" style="color:var(--outline)">{{ __('ui.layout.app_name') }}</p>
                    <p class="text-base font-extrabold" style="color:var(--on-surface)">Point 88</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button data-language-toggle type="button" class="top-lang-btn" aria-label="Switch language" title="Switch language">
                    {{ app()->getLocale() === 'ar' ? 'EN' : 'AR' }}
                </button>
                <button data-theme-toggle type="button" class="top-lang-btn min-w-24">
                    Toggle Theme
                </button>
                <a href="{{ route('login') }}" class="top-order-btn">Sign In</a>
            </div>
        </header>

        <main class="relative z-10 mx-auto flex w-full max-w-7xl flex-1 flex-col px-4 pb-12 pt-8 sm:px-6 lg:px-8">
            <section class="grid items-center gap-8 lg:grid-cols-[1.06fr_0.94fr]">
                <div>
                    <span class="inline-flex items-center rounded-full border border-[color-mix(in_srgb,var(--accent-gold)_30%,transparent_70%)] bg-[color-mix(in_srgb,var(--surface-lowest)_75%,transparent_25%)] px-3 py-1 text-xs font-bold uppercase tracking-[0.15em] text-[var(--primary)] dark:border-[color-mix(in_srgb,var(--accent-gold)_30%,transparent_70%)] dark:bg-[color-mix(in_srgb,var(--surface-container)_45%,transparent_55%)] dark:text-[var(--secondary)]">
                        Next Generation Operations
                    </span>
                    <h1 class="mt-5 text-4xl font-extrabold leading-tight tracking-[-0.03em] text-[var(--on-surface)] sm:text-5xl ">
                        One command center
                        <span class="block text-[var(--primary)] dark:text-[var(--secondary)]">for your entire restaurant workflow.</span>
                    </h1>
                    <p class="mt-4 max-w-xl text-base leading-8 text-[var(--on-surface-var)] dark:text-[var(--outline-var)]">
                        Connect front-of-house, kitchen, inventory, suppliers, and payroll in one clean interface designed for speed, clarity, and low visual fatigue in both light and dark mode.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <a href="{{ route('login') }}" class="top-order-btn">Launch Workspace</a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-5 py-2.5 text-sm font-semibold text-[var(--on-surface-var)] transition hover:bg-[var(--surface-low)] dark:border-[var(--outline-var)] dark:bg-[var(--surface-container)] dark:text-[var(--on-surface-var)] dark:hover:bg-[var(--surface-high)]">
                            View Demo Flow
                        </a>
                    </div>

                    <div class="mt-10 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-4 py-3 shadow-sm backdrop-blur-sm dark:border-[var(--outline-var)] dark:bg-[var(--surface-container)]">
                            <p class="text-xs uppercase tracking-[0.12em] text-[var(--outline)]">POS Throughput</p>
                            <p class="mt-1 text-xl font-extrabold text-[var(--on-surface)] dark:text-[var(--on-surface)]">+32%</p>
                        </div>
                        <div class="rounded-2xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-4 py-3 shadow-sm backdrop-blur-sm dark:border-[var(--outline-var)] dark:bg-[var(--surface-container)]">
                            <p class="text-xs uppercase tracking-[0.12em] text-[var(--outline)]">Food Cost Alerts</p>
                            <p class="mt-1 text-xl font-extrabold text-[var(--on-surface)] dark:text-[var(--on-surface)]">Realtime</p>
                        </div>
                        <div class="rounded-2xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-4 py-3 shadow-sm backdrop-blur-sm dark:border-[var(--outline-var)] dark:bg-[var(--surface-container)]">
                            <p class="text-xs uppercase tracking-[0.12em] text-[var(--outline)]">Shift Accuracy</p>
                            <p class="mt-1 text-xl font-extrabold text-[var(--on-surface)] dark:text-[var(--on-surface)]">99.2%</p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4">
                    <div class="rounded-3xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] p-6 shadow-2xl backdrop-blur-sm dark:border-[var(--outline-var)] dark:bg-[var(--surface-lowest)]">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-bold uppercase tracking-[0.13em] text-[var(--outline)]">Operations Pulse</p>
                            <span class="rounded-full border border-[color-mix(in_srgb,var(--accent-gold)_30%,transparent_70%)] bg-[color-mix(in_srgb,var(--primary)_8%,var(--surface-lowest)_92%)] px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-[var(--primary)] dark:border-[color-mix(in_srgb,var(--accent-gold)_30%,transparent_70%)] dark:bg-[color-mix(in_srgb,var(--success)_15%,transparent_85%)] dark:text-[var(--accent-gold)]">Live</span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-xl border border-[var(--outline-var)] bg-[var(--surface-low)] p-3 dark:border-[var(--outline-var)] dark:bg-[var(--surface-high)]">
                                <p class="text-xs uppercase tracking-widest text-[var(--outline)]">Active Orders</p>
                                <p class="mt-1 text-2xl font-extrabold text-[var(--on-surface)] dark:text-[var(--on-surface)]">128</p>
                            </div>
                            <div class="rounded-xl border border-[var(--outline-var)] bg-[var(--surface-low)] p-3 dark:border-[var(--outline-var)] dark:bg-[var(--surface-high)]">
                                <p class="text-xs uppercase tracking-widest text-[var(--outline)]">Kitchen Load</p>
                                <p class="mt-1 text-2xl font-extrabold text-[var(--primary)] dark:text-[var(--secondary)]">62%</p>
                            </div>
                            <div class="rounded-xl border border-[var(--outline-var)] bg-[var(--surface-low)] p-3 dark:border-[var(--outline-var)] dark:bg-[var(--surface-high)]">
                                <p class="text-xs uppercase tracking-widest text-[var(--outline)]">Inventory Alerts</p>
                                <p class="mt-1 text-2xl font-extrabold text-[var(--warning)] ">9</p>
                            </div>
                            <div class="rounded-xl border border-[var(--outline-var)] bg-[var(--surface-low)] p-3 dark:border-[var(--outline-var)] dark:bg-[var(--surface-high)]">
                                <p class="text-xs uppercase tracking-widest text-[var(--outline)]">Revenue Today</p>
                                <p class="mt-1 text-2xl font-extrabold text-[var(--success)] dark:text-[var(--success)]">EGP 32K</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] p-5 shadow-lg backdrop-blur-sm dark:border-[var(--outline-var)] dark:bg-[var(--surface-lowest)]">
                        <p class="text-sm font-semibold text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">Built for high-speed service teams</p>
                        <p class="mt-2 text-sm leading-7 text-[var(--outline)] dark:text-[var(--outline-var)]">
                            POS, waiter workflow, kitchen board, purchasing, and analytics are unified in one consistent design language for faster action and lower visual fatigue.
                        </p>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>

</html>