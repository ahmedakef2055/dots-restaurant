@props(['title' => __('ui.layout.app_name')])

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | {{ __('ui.layout.app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#5E7D67" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#1D2420" media="(prefers-color-scheme: dark)">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Blur app shell when modal is open */
        .app-shell {
            transition: filter 0.2s ease;
        }
        .app-shell.modal-blur {
            filter: blur(4px) brightness(0.7);
            pointer-events: none;
            user-select: none;
        }
        
        /* Global override for dark mode logos */
        html.dark .sidebar-brand-text img {
            filter: brightness(0) invert(1);
        }
    </style>
    <script src="{{ asset('js/qz-tray.js') }}"></script>
</head>

<body
    class="h-full is-loading"
    onload="document.body.classList.remove('is-loading')"
    data-delete-modal-title="{{ __('ui.common.delete_modal_title') }}"
    data-delete-modal-message="{{ __('ui.common.delete_modal_message') }}"
    data-delete-modal-confirm="{{ __('ui.common.delete') }}"
    data-delete-modal-cancel="{{ __('ui.common.cancel') }}">
    <div class="app-shell" id="app-shell">
        @php
        $authUser = auth()->user();
        $notificationsEnabled = false;
        $unreadNotifications = collect();

        if ($authUser) {
            try {
                $notificationsEnabled = \Illuminate\Support\Facades\Schema::hasTable('notifications');
            } catch (\Throwable) {
                $notificationsEnabled = false;
            }

            if ($notificationsEnabled) {
                $unreadNotifications = $authUser
                    ->unreadNotifications()
                    ->latest()
                    ->limit(6)
                    ->get();
            }
        }
        @endphp
        <x-layouts.sidebar />

        <div class="overlay" data-sidebar-close></div>

        <div class="app-content">
            <header class="glass-nav" style="direction:ltr">
                <div class="top-nav-inner mx-auto flex h-14 w-full items-center justify-between px-4 sm:px-6 lg:px-8">
                    {{-- Leading: hamburger --}}
                    <div class="top-nav-leading flex items-center gap-3">
                        <button data-sidebar-toggle type="button" class="top-icon-btn lg:hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>

                    {{-- Trailing: lang + theme + notifications + logout + avatar --}}
                    <div class="top-nav-trailing flex items-center gap-2">
                        <div style="border-left:1px solid color-mix(in srgb,var(--outline-var) 40%,transparent 60%);height:1.5rem" class="mx-1 hidden sm:block"></div>
                        <button type="button" class="top-lang-btn" data-language-toggle aria-label="{{ __('ui.layout.switch_language') }}" title="{{ __('ui.layout.switch_language') }}">
                            {{ app()->getLocale() === 'ar' ? 'EN' : 'AR' }}
                        </button>
                        <button type="button" class="top-icon-btn" title="{{ __('ui.layout.theme') }}" data-theme-toggle>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" aria-hidden="true"><path d="M480-120q-150 0-255-105T120-480q0-150 105-255t255-105q14 0 27.5 1t26.5 3q-41 29-65.5 75.5T444-660q0 90 63 153t153 63q55 0 101-24.5t75-65.5q2 13 3 26.5t1 27.5q0 150-105 255T480-120Zm0-80q88 0 158-48.5T740-375q-20 5-40 8t-40 3q-123 0-209.5-86.5T364-660q0-20 3-40t8-40q-78 32-126.5 102T200-480q0 116 82 198t198 82Zm-10-270Z"/></svg>
                        </button>

                        @if ($notificationsEnabled)
                        <details class="relative">
                            <summary class="top-icon-btn relative list-none cursor-pointer" title="{{ __('ui.layout.notifications') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 0 1-5.714 0A8.967 8.967 0 0 1 6 16.139V11.25a6 6 0 0 1 12 0v4.889a8.967 8.967 0 0 1-3.143.943ZM15 18a3 3 0 1 1-6 0" />
                                </svg>
                                @if ($unreadNotifications->count() > 0)
                                <span class="absolute -top-1 -right-1 inline-flex min-h-4 min-w-4 items-center justify-center rounded-full bg-[var(--error)] px-1 text-[10px] font-bold text-white">
                                    {{ $unreadNotifications->count() > 9 ? '9+' : $unreadNotifications->count() }}
                                </span>
                                @endif
                            </summary>

                            <div class="absolute right-0 z-30 mt-2 w-80 overflow-hidden rounded-xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] shadow-xl dark:border-[var(--outline-var)] " dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                                <div class="flex items-center justify-between border-b border-[var(--outline-var)] px-3 py-2 dark:border-[var(--outline-var)]">
                                    <p class="text-sm font-semibold text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ __('ui.layout.notifications') }}</p>
                                    @if ($unreadNotifications->count() > 0)
                                    <form method="POST" action="{{ route('notifications.read-all') }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-xs font-semibold text-[var(--warning)] hover:underline">{{ __('ui.layout.mark_all_read') }}</button>
                                    </form>
                                    @endif
                                </div>

                                <div class="max-h-80 overflow-auto">
                                    @forelse ($unreadNotifications as $notification)
                                    @php
                                    $notificationAction = strtolower((string) data_get($notification->data, 'action', ''));
                                    $notificationPurchaseNumber = (string) data_get($notification->data, 'purchase_number', '-');
                                    $notificationReviewedBy = (string) data_get($notification->data, 'reviewed_by', '');
                                    $notificationComment = trim((string) data_get($notification->data, 'approval_comment', ''));

                                    $notificationActionLabel = match ($notificationAction) {
                                    'approved' => __('ui.purchases.statuses.approved'),
                                    'rejected' => __('ui.purchases.statuses.rejected'),
                                    default => __('ui.purchases.statuses.pending'),
                                    };
                                    @endphp

                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="border-b border-[var(--outline-var)] last:border-b-0 dark:border-[var(--outline-var)]">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full px-3 py-2 text-start hover:bg-[var(--surface-low)] /60">
                                            <p class="text-xs font-semibold text-[var(--on-surface)] dark:text-[var(--on-surface)]">
                                                {{ __('ui.layout.purchase_notification_title', ['number' => $notificationPurchaseNumber]) }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-[var(--on-surface-var)] dark:text-[var(--outline)]">
                                                {{ __('ui.layout.purchase_notification_body', ['status' => $notificationActionLabel]) }}
                                                @if ($notificationReviewedBy !== '')
                                                • {{ $notificationReviewedBy }}
                                                @endif
                                            </p>
                                            @if ($notificationComment !== '')
                                            <p class="mt-1 text-[11px] text-[var(--on-surface-var)] dark:text-[var(--outline)]">{{ $notificationComment }}</p>
                                            @endif
                                            <p class="mt-1 text-[10px] text-[var(--outline)] dark:text-[var(--on-surface-var)]">{{ $notification->created_at?->format('Y-m-d H:i') }}</p>
                                        </button>
                                    </form>
                                    @empty
                                    <p class="px-3 py-4 text-sm text-[var(--on-surface-var)] dark:text-[var(--outline)]">{{ __('ui.layout.no_notifications') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        </details>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="inline-flex">
                            @csrf
                            <button type="submit" class="top-icon-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" aria-hidden="true"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>
                            </button>
                        </form>
                        <div class="top-avatar">{{ mb_strtoupper(mb_substr(auth()->user()?->name ?? 'U', 0, 2)) }}</div>
                    </div>
                </div>
            </header>

            <main class="mx-auto w-full flex-1 px-4 py-6 sm:px-6 lg:px-8">
                <x-ui.flash-toast />
                {{ $slot }}
            </main>
        </div>
    </div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof startPrintQueuePolling === 'function') {
      startPrintQueuePolling();
    }
  });
</script>
</body>

</html>