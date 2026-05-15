<x-layouts.app :title="$purchase->purchase_number">
    @php
    $requestType = strtolower((string) ($purchase->request_type ?: 'inventory'));
    $approvalStatus = strtolower((string) ($purchase->approval_status ?: 'pending'));
    $purchaseStatus = strtolower((string) ($purchase->status ?: 'pending'));
    $currentUser = auth()->user();
    $canApprovePurchases = $currentUser?->hasPermission('purchases.approve') ?? false;
    $canCreatePurchases = $currentUser?->hasPermission('purchases.create') ?? false;
    $isRequestOwner = (int) ($currentUser?->id ?? 0) === (int) ($purchase->user_id ?? 0);
    $isPendingApproval = $approvalStatus === 'pending';
    $isApprovedRequest = $approvalStatus === 'approved';
    $isCompleted = $purchaseStatus === 'completed';
    $canCompleteRequest = $isRequestOwner && $canCreatePurchases && $isApprovedRequest && ! $isCompleted;

    $purchaseStatusLabel = match ($purchaseStatus) {
    'pending' => __('ui.purchases.statuses.pending'),
    'approved' => __('ui.purchases.statuses.approved'),
    'rejected' => __('ui.purchases.statuses.rejected'),
    'completed' => __('ui.purchases.statuses.completed'),
    'paid' => __('ui.purchases.statuses.paid'),
    'cancelled' => __('ui.purchases.statuses.cancelled'),
    default => ucfirst((string) $purchase->status),
    };

    $approvalDisplayStatus = $isCompleted ? 'completed' : $approvalStatus;

    $approvalStatusLabel = match ($approvalDisplayStatus) {
    'completed' => __('ui.purchases.statuses.completed'),
    'approved' => __('ui.purchases.statuses.approved'),
    'rejected' => __('ui.purchases.statuses.rejected'),
    default => __('ui.purchases.statuses.pending'),
    };
    @endphp

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('purchases.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-1.5 text-sm font-semibold text-[var(--on-surface-var)] transition hover:border-[var(--primary)] hover:bg-[color-mix(in_srgb,var(--primary)_8%,transparent_92%)] hover:text-[var(--primary)]">← {{ __('ui.purchases.back') }}</a>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('purchases.invoice', $purchase) }}" target="_blank"><x-ui.button type="button">{{ __('ui.purchases.buttons.print_invoice') }}</x-ui.button></a>
            @if($purchase->supplier)
            <a href="{{ route('suppliers.show', $purchase->supplier) }}"><x-ui.button type="button" variant="secondary">{{ __('ui.purchases.buttons.view_supplier') }}</x-ui.button></a>
            @endif
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-ui.card :title="__('ui.purchases.summary_title')" class="lg:col-span-2">
            @if($requestType === 'inventory')
            <x-ui.table :headers="[__('ui.purchases.table.ingredient'), __('ui.purchases.table.qty'), __('ui.purchases.table.expiry'), __('ui.purchases.table.unit_cost'), __('ui.purchases.table.line_total')]">
                @foreach($purchase->items as $item)
                <tr>
                    <td class="px-4 py-3 text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $item->ingredient_name }}</td>
                    <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">{{ number_format((float)$item->quantity, 3) }}</td>
                    <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">{{ $item->expiry_date?->format('Y-m-d') ?? '-' }}</td>
                    <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">{{ \App\Support\CurrencyFormatter::format($item->unit_cost) }}</td>
                    <td class="px-4 py-3 font-medium text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ \App\Support\CurrencyFormatter::format($item->line_total) }}</td>
                </tr>
                @endforeach
            </x-ui.table>

            @if($purchase->items->isEmpty())
            <p class="rounded-lg border border-[var(--outline-var)] bg-[var(--surface-low)] px-3 py-2 text-sm text-[var(--on-surface-var)] dark:border-[var(--outline-var)]  dark:text-[var(--on-surface-var)]">{{ __('ui.purchases.no_results') }}</p>
            @endif

            <div class="mt-4 ml-auto w-full max-w-sm space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-[var(--on-surface-var)]">{{ __('ui.purchases.subtotal') }}</span><span>{{ \App\Support\CurrencyFormatter::format($purchase->subtotal) }}</span></div>
                <div class="flex justify-between"><span class="text-[var(--on-surface-var)]">{{ __('ui.purchases.tax') }}</span><span>{{ \App\Support\CurrencyFormatter::format($purchase->tax_amount) }}</span></div>
                <div class="flex justify-between"><span class="text-[var(--on-surface-var)]">{{ __('ui.purchases.discount') }}</span><span>{{ \App\Support\CurrencyFormatter::format(-$purchase->discount_amount) }}</span></div>
                <div class="flex justify-between border-t border-[var(--outline-var)] pt-2 text-base font-semibold"><span>{{ __('ui.purchases.total') }}</span><span>{{ \App\Support\CurrencyFormatter::format($purchase->total) }}</span></div>
            </div>
            @else
            <div class="space-y-3 rounded-xl border border-[var(--outline-var)] bg-[var(--surface-low)] p-4 text-sm dark:border-[var(--outline-var)] ">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[var(--on-surface-var)]">{{ __('ui.purchases.expense_title') }}</span>
                    <span class="font-semibold text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $purchase->expense_title ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[var(--on-surface-var)]">{{ __('ui.purchases.expense_invoice_reference') }}</span>
                    <span class="font-semibold text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $purchase->expense_invoice_reference ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 border-t border-[var(--outline-var)] pt-2 dark:border-[var(--outline-var)]">
                    <span class="text-[var(--on-surface-var)]">{{ __('ui.purchases.expense_amount') }}</span>
                    <span class="text-base font-bold text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ \App\Support\CurrencyFormatter::format($purchase->expense_amount ?? $purchase->total) }}</span>
                </div>
            </div>
            @endif

            @if($canApprovePurchases && $isPendingApproval)
            <div class="mt-6 border-t border-[var(--outline-var)] pt-4 dark:border-[var(--outline-var)]">
                <div class="grid gap-3 md:grid-cols-2">
                    <form method="POST" action="{{ route('purchases.approve', $purchase) }}" class="rounded-xl border border-[color-mix(in_srgb,var(--success)_30%,transparent_70%)] bg-[var(--primary-container)]/70 p-3 dark:border-[color-mix(in_srgb,var(--success)_30%,transparent_70%)] dark:bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)]">
                        @csrf
                        @method('PATCH')
                        <label class="mb-1 block text-xs font-semibold text-[var(--success)] ">{{ __('ui.purchases.approval.approve_title') }}</label>
                        <textarea name="approval_comment" rows="3" placeholder="{{ __('ui.purchases.approval.approve_hint') }}" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">{{ old('approval_comment') }}</textarea>
                        <div class="mt-2 flex justify-end">
                            <x-ui.button type="submit" class="rounded-lg px-3 py-1.5 text-xs">{{ __('ui.purchases.buttons.approve') }}</x-ui.button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('purchases.reject', $purchase) }}" class="rounded-xl border border-[var(--error-container)] bg-[var(--error-container)] p-3  ">
                        @csrf
                        @method('PATCH')
                        <label class="mb-1 block text-xs font-semibold text-[var(--error)] dark:text-[var(--error)]">{{ __('ui.purchases.approval.reject_title') }}</label>
                        <textarea name="approval_comment" rows="3" required placeholder="{{ __('ui.purchases.approval.reject_hint') }}" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">{{ old('approval_comment') }}</textarea>
                        @error('approval_comment')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
                        <div class="mt-2 flex justify-end">
                            <x-ui.button type="submit" variant="danger" class="rounded-lg px-3 py-1.5 text-xs">{{ __('ui.purchases.buttons.reject') }}</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            @if($canCompleteRequest)
            <div class="mt-4 border-t border-[var(--outline-var)] pt-4 dark:border-[var(--outline-var)]">
                <form method="POST" action="{{ route('purchases.complete', $purchase) }}" enctype="multipart/form-data" class="rounded-xl border border-[var(--primary-container)] bg-[var(--primary-container)] p-3  ">
                    @csrf
                    @method('PATCH')

                    <label class="mb-1 block text-xs font-semibold text-[var(--primary)] ">{{ __('ui.purchases.completion.title') }}</label>
                    <p class="mb-2 text-xs text-[var(--primary)] ">{{ __('ui.purchases.completion.subtitle') }}</p>

                    <div class="grid gap-2 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-[var(--on-surface-var)]">{{ __('ui.purchases.fields.invoice_number') }}</label>
                            <input name="invoice_number" value="{{ old('invoice_number', $purchase->invoice_number) }}" required class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
                            @error('invoice_number')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-[var(--on-surface-var)]">{{ __('ui.purchases.fields.invoice_file') }}</label>
                            <input type="file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png,.webp" required class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-[var(--surface-low)] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-[var(--on-surface)] hover:file:bg-[var(--surface-container)] dark:border-[var(--outline-var)] dark:bg-[var(--background)] dark:file:bg-slate-800 dark:file:text-[var(--on-surface)] dark:hover:file:bg-slate-700">
                            <p class="mt-1 text-xs text-[var(--on-surface-var)] dark:text-[var(--outline)]">{{ __('ui.purchases.fields.invoice_file_hint') }}</p>
                            @error('invoice_file')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mt-2 flex justify-end">
                        <x-ui.button type="submit" class="rounded-lg px-3 py-1.5 text-xs">{{ __('ui.purchases.buttons.confirm_purchase_done') }}</x-ui.button>
                    </div>
                </form>
            </div>
            @elseif($isApprovedRequest && ! $isCompleted)
            <div class="mt-4 border-t border-[var(--outline-var)] pt-4 dark:border-[var(--outline-var)]">
                <div class="rounded-xl border border-[var(--warning-container)] bg-[var(--warning-container)] p-3 text-sm text-[var(--warning)]   dark:text-[var(--warning)]">
                    {{ __('ui.purchases.completion.waiting_request_owner') }}
                </div>
            </div>
            @endif
        </x-ui.card>

        <x-ui.card :title="__('ui.purchases.details_title')">
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.purchase_number_label') }}</dt>
                    <dd class="font-medium">{{ $purchase->purchase_number }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.supplier') }}</dt>
                    <dd class="font-medium">{{ $purchase->supplier?->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.fields.request_type') }}</dt>
                    <dd class="font-medium">
                        {{ $requestType === 'general_expense' ? __('ui.purchases.request_types.general_expense') : __('ui.purchases.request_types.inventory') }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.date') }}</dt>
                    <dd class="font-medium">{{ $purchase->purchase_date?->format('Y-m-d') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.processed_by') }}</dt>
                    <dd class="font-medium">{{ $purchase->user?->name ?? __('ui.purchases.system') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.status') }}</dt>
                    <dd class="font-medium">{{ $purchaseStatusLabel }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.approval.status') }}</dt>
                    <dd class="font-medium">
                        <span @class([ 'rounded-full px-2 py-1 text-xs font-semibold' , 'border border-[color-mix(in_srgb,var(--success)_35%,transparent_65%)] bg-[var(--primary-container)] text-[var(--success)] dark:border-[color-mix(in_srgb,var(--success)_30%,transparent_70%)] dark:bg-[color-mix(in_srgb,var(--success)_20%,transparent_80%)] dark:text-[var(--success)]'=> $approvalDisplayStatus === 'completed',
                            'border border-[var(--primary-container)] bg-[var(--primary-container)] text-[var(--primary)]' => $approvalDisplayStatus === 'approved',
                            'border border-[var(--error-container)] bg-[var(--error-container)] text-[var(--error)]  dark:bg-[var(--error-container)] ' => $approvalDisplayStatus === 'rejected',
                            'border border-[var(--warning-container)] bg-[var(--warning-container)] text-[var(--warning)]   ' => $approvalDisplayStatus === 'pending',
                            ])>
                            {{ $approvalStatusLabel }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.approval.by') }}</dt>
                    <dd class="font-medium">{{ $purchase->approvalUser?->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.approval.at') }}</dt>
                    <dd class="font-medium">{{ $purchase->approval_at?->format('Y-m-d H:i') ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.completed_by') }}</dt>
                    <dd class="font-medium">{{ $purchase->completedByUser?->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.completed_at') }}</dt>
                    <dd class="font-medium">{{ $purchase->completed_at?->format('Y-m-d H:i') ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.invoice_number') }}</dt>
                    <dd class="font-medium">{{ $purchase->invoice_number ?: '-' }}</dd>
                </div>
                @if($requestType === 'inventory')
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.stock_applied_at') }}</dt>
                    <dd class="font-medium">{{ $purchase->inventory_applied_at?->format('Y-m-d H:i') ?? '-' }}</dd>
                </div>
                @endif
                @if($requestType === 'general_expense')
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.expense_title') }}</dt>
                    <dd class="font-medium">{{ $purchase->expense_title ?: '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.expense_invoice_reference') }}</dt>
                    <dd class="font-medium">{{ $purchase->expense_invoice_reference ?: '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.expense_amount') }}</dt>
                    <dd class="font-medium">{{ \App\Support\CurrencyFormatter::format($purchase->expense_amount ?? $purchase->total) }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-[var(--on-surface-var)]">{{ __('ui.purchases.payment') }}</dt>
                    <dd class="font-medium">
                        @if(($purchase->payment_method ?? 'cash') === 'credit')
                        {{ __('ui.purchases.payment_credit') }}
                        @else
                        {{ __('ui.purchases.payment_cash') }}
                        @endif
                    </dd>
                </div>
            </dl>

            @if($purchase->approval_comment)
            <div class="mt-4 rounded-lg bg-[var(--surface-low)] px-3 py-2 text-sm text-[var(--on-surface)]  dark:text-[var(--on-surface-var)]">
                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--on-surface-var)]">{{ __('ui.purchases.approval.comment') }}</p>
                <p class="mt-1">{{ $purchase->approval_comment }}</p>
            </div>
            @endif

            @if($purchase->notes)
            <div class="mt-4 rounded-lg bg-[var(--surface-low)] px-3 py-2 text-sm text-[var(--on-surface)]  dark:text-[var(--on-surface-var)]">{{ $purchase->notes }}</div>
            @endif

            @if(! empty($supportsInvoiceFile))
            <div class="mt-4 rounded-xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] p-3 dark:border-[var(--outline-var)] ">
                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--on-surface-var)]">{{ __('ui.purchases.invoice_file_label') }}</p>

                @if(! empty($purchase->invoice_file_path))
                <div class="mt-2 flex flex-wrap gap-2">
                    <a href="{{ route('purchases.invoice-file.view', $purchase) }}" target="_blank" class="inline-flex items-center justify-center rounded-lg border border-[var(--outline-var)] px-3 py-2 text-xs font-semibold text-[var(--on-surface)] hover:bg-[var(--surface-low)] dark:border-slate-600 dark:text-[var(--on-surface)] ">{{ __('ui.purchases.buttons.view_invoice_file') }}</a>
                    <a href="{{ route('purchases.invoice-file.download', $purchase) }}" class="inline-flex items-center justify-center rounded-lg border border-[var(--outline-var)] px-3 py-2 text-xs font-semibold text-[var(--on-surface)] hover:bg-[var(--surface-low)] dark:border-slate-600 dark:text-[var(--on-surface)] ">{{ __('ui.purchases.buttons.download_invoice_file') }}</a>
                </div>
                @else
                <p class="mt-1 text-sm text-[var(--on-surface-var)] dark:text-[var(--outline)]">{{ __('ui.purchases.invoice_file_missing') }}</p>
                @endif

                @if($isCompleted)
                <form method="POST" action="{{ route('purchases.invoice-file.update', $purchase) }}" enctype="multipart/form-data" class="mt-3 border-t border-[var(--outline-var)] pt-3 dark:border-[var(--outline-var)]">
                    @csrf
                    @method('PATCH')

                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-[var(--on-surface-var)]">{{ __('ui.purchases.fields.invoice_file_replace') }}</label>
                    <input type="file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png,.webp" required class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-[var(--surface-low)] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-[var(--on-surface)] hover:file:bg-[var(--surface-container)] dark:border-[var(--outline-var)] dark:bg-[var(--background)] dark:file:bg-slate-800 dark:file:text-[var(--on-surface)] dark:hover:file:bg-slate-700">
                    <p class="mt-1 text-xs text-[var(--on-surface-var)] dark:text-[var(--outline)]">{{ __('ui.purchases.fields.invoice_file_hint') }}</p>
                    @error('invoice_file')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror

                    <div class="mt-2">
                        <x-ui.button type="submit" variant="secondary">{{ __('ui.purchases.buttons.upload_invoice_file') }}</x-ui.button>
                    </div>
                </form>
                @else
                <p class="mt-3 border-t border-[var(--outline-var)] pt-3 text-xs text-[var(--on-surface-var)] dark:border-[var(--outline-var)] dark:text-[var(--outline)]">{{ __('messages.errors.purchase_invoice_replace_only_after_completion') }}</p>
                @endif
            </div>
            @endif
        </x-ui.card>
    </div>

    <x-ui.card class="mt-6" :title="__('ui.purchases.audit.title')" :subtitle="__('ui.purchases.audit.subtitle')">
        <x-ui.table :headers="[__('ui.purchases.audit.table.action'), __('ui.purchases.audit.table.before_after_status'), __('ui.purchases.audit.table.before_after_approval'), __('ui.purchases.audit.table.before_after_comment'), __('ui.purchases.audit.table.by'), __('ui.purchases.audit.table.when')]">
            @forelse($purchase->approvalLogs as $log)
            @php
            $actionLabel = match (strtolower((string) $log->action)) {
            'approve' => __('ui.purchases.buttons.approve'),
            'reject' => __('ui.purchases.buttons.reject'),
            default => ucfirst((string) $log->action),
            };

            $previousStatus = $log->previous_status ?: '-';
            $newStatus = $log->new_status ?: '-';
            $previousApprovalStatus = $log->previous_approval_status ?: '-';
            $newApprovalStatus = $log->new_approval_status ?: '-';
            $previousComment = trim((string) ($log->previous_approval_comment ?? ''));
            $newComment = trim((string) ($log->new_approval_comment ?? ''));
            @endphp
            <tr>
                <td class="px-4 py-3 text-[var(--on-surface)] dark:text-[var(--on-surface)]">{{ $actionLabel }}</td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">
                    <span class="font-semibold">{{ $previousStatus }}</span>
                    <span class="mx-1">→</span>
                    <span class="font-semibold">{{ $newStatus }}</span>
                </td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">
                    <span class="font-semibold">{{ $previousApprovalStatus }}</span>
                    <span class="mx-1">→</span>
                    <span class="font-semibold">{{ $newApprovalStatus }}</span>
                </td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">
                    <p><span class="font-semibold">{{ __('ui.purchases.audit.before') }}:</span> {{ $previousComment !== '' ? $previousComment : '-' }}</p>
                    <p class="mt-1"><span class="font-semibold">{{ __('ui.purchases.audit.after') }}:</span> {{ $newComment !== '' ? $newComment : '-' }}</p>
                </td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">{{ $log->actor?->name ?? '-' }}</td>
                <td class="px-4 py-3 text-[var(--on-surface-var)] dark:text-[var(--outline)]">{{ $log->acted_at?->format('Y-m-d H:i') ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-sm text-[var(--on-surface-var)] dark:text-[var(--outline)]">{{ __('ui.purchases.audit.empty') }}</td>
            </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-layouts.app>