<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.auth.login.page_title') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#5E7D67">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html.dark .login-logo-name { filter: brightness(0) invert(1); }
    </style>
</head>

<body class="h-full">
    <div class="relative min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">

        {{-- Background blobs --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -left-28 -top-10 h-80 w-80 rounded-full bg-[color-mix(in_srgb,var(--primary)_8%,transparent_92%)] blur-3xl dark:bg-[color-mix(in_srgb,var(--success)_15%,transparent_85%)]"></div>
            <div class="absolute -right-20 top-1/3 h-96 w-96 rounded-full bg-[color-mix(in_srgb,var(--accent-gold)_12%,transparent_88%)] blur-3xl dark:bg-[color-mix(in_srgb,var(--accent-gold)_10%,transparent_90%)]"></div>
            <div class="absolute bottom-0 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-[color-mix(in_srgb,var(--accent-gold)_8%,transparent_92%)] blur-3xl dark:bg-[color-mix(in_srgb,var(--accent-gold)_8%,transparent_92%)]"></div>
        </div>

        {{-- Top controls: language + theme --}}
        <div class="absolute inset-e-4 top-4 z-20 flex items-center gap-2 sm:inset-e-6">
            <button data-language-toggle type="button" class="top-lang-btn"
                aria-label="{{ __('messages.auth.login.switch_language') }}"
                title="{{ __('messages.auth.login.switch_language') }}">
                {{ app()->getLocale() === 'ar' ? 'EN' : 'AR' }}
            </button>
            <button data-theme-toggle type="button" class="top-lang-btn min-w-28">
                {{ __('messages.auth.login.toggle_theme') }}
            </button>
        </div>

        {{-- Login card --}}
        <div class="relative z-10 w-full max-w-sm">
            <div class="rounded-3xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] p-8 shadow-2xl backdrop-blur-md dark:border-[var(--outline-var)] dark:bg-[var(--surface-lowest)]">

                {{-- Brand icon --}}
                <div class="mb-8 flex flex-col items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Dots" class="h-24 w-24 object-contain rounded-2xl shadow-lg">
                </div>


                @if ($errors->any())
                <div class="mt-5 rounded-xl border border-[var(--error-container)] bg-[var(--error-container)] p-3 text-sm text-[var(--error)]">
                    {{ $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" class="mt-7 space-y-4">
                    @csrf

                    <div>
                        <label for="username" class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                            {{ __('messages.auth.login.username') }}
                        </label>
                        <input id="username" name="username" type="text" required autofocus
                            value="{{ old('username') }}" dir="ltr"
                            class="w-full rounded-xl border px-3 py-2.5 text-sm transition focus:outline-none focus:ring-2"
                            style="border-color:var(--outline-var);background:var(--surface-lowest);color:var(--on-surface);--tw-ring-color:color-mix(in srgb,var(--primary) 40%,transparent)" />
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                            {{ __('messages.auth.login.password') }}
                        </label>
                        <input id="password" name="password" type="password" required dir="ltr"
                            class="w-full rounded-xl border px-3 py-2.5 text-sm transition focus:outline-none focus:ring-2"
                            style="border-color:var(--outline-var);background:var(--surface-lowest);color:var(--on-surface);--tw-ring-color:color-mix(in srgb,var(--primary) 40%,transparent)" />
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm" style="color:var(--on-surface-var)">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))
                            class="rounded" style="accent-color:var(--primary)" />
                        <span>{{ __('messages.auth.login.remember_me') }}</span>
                    </label>

                    <button type="submit"
                        class="mt-1 w-full rounded-xl py-2.5 text-sm font-semibold text-white transition-all duration-200 hover:-translate-y-px"
                        style="background:linear-gradient(135deg,var(--primary),var(--accent-gold));box-shadow:0 8px 24px color-mix(in srgb,var(--primary) 30%,transparent)">
                        {{ __('messages.auth.login.submit') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
