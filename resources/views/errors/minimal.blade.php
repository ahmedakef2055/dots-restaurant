<!DOCTYPE html>
<html lang="{{ app()->getLocale() ?? 'en' }}" dir="{{ (app()->getLocale() ?? 'en') === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html.dark .error-logo-name { filter: brightness(0) invert(1); }
    </style>
</head>

<body class="h-full">
    <div class="relative min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">

        {{-- Background blobs --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -left-28 -top-10 h-80 w-80 rounded-full bg-[color-mix(in_srgb,var(--primary)_8%,transparent_92%)] blur-3xl dark:bg-[color-mix(in_srgb,var(--error)_15%,transparent_85%)]"></div>
            <div class="absolute -right-20 top-1/3 h-96 w-96 rounded-full bg-[color-mix(in_srgb,var(--accent-gold)_12%,transparent_88%)] blur-3xl dark:bg-[color-mix(in_srgb,var(--accent-gold)_10%,transparent_90%)]"></div>
            <div class="absolute bottom-0 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-[color-mix(in_srgb,var(--accent-gold)_8%,transparent_92%)] blur-3xl dark:bg-[color-mix(in_srgb,var(--accent-gold)_8%,transparent_92%)]"></div>
        </div>

        {{-- Error card --}}
        <div class="relative z-10 w-full max-w-lg text-center">
            <div class="rounded-3xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] p-10 shadow-2xl backdrop-blur-md dark:border-[var(--outline-var)] dark:bg-[var(--surface-lowest)]">

                {{-- Brand icon --}}
                <div class="mb-8 flex flex-col items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Point 88" class="h-16 w-16 object-contain rounded-2xl shadow-lg">
                    <img src="{{ asset('images/Logo_Name.png') }}" alt="Restaurant Management" class="error-logo-name h-10 object-contain">
                </div>

                <div class="flex items-center justify-center mb-6">
                    <h1 class="text-6xl font-extrabold tracking-tight" style="color:var(--error); text-shadow: 0 4px 12px color-mix(in srgb, var(--error) 30%, transparent);">
                        @yield('code')
                    </h1>
                </div>

                <h2 class="mb-4 text-2xl font-bold tracking-[-0.01em]" style="color:var(--on-surface)">
                    @yield('message')
                </h2>

                <p class="mb-8 text-sm leading-relaxed" style="color:var(--on-surface-var)">
                    @yield('description', 'Sorry, the page you are looking for could not be found or an error occurred.')
                </p>

                <div class="flex justify-center">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-semibold text-white transition-all duration-200 hover:-translate-y-px"
                        style="background:linear-gradient(135deg,var(--primary),var(--accent-gold));box-shadow:0 8px 24px color-mix(in srgb,var(--primary) 30%,transparent)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-5 h-5">
                            <path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z"/>
                        </svg>
                        {{ (app()->getLocale() ?? 'en') === 'ar' ? 'العودة للرئيسية' : 'Return Home' }}
                    </a>
                </div>

            </div>
        </div>
    </div>
</body>

</html>
