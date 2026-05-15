@php
$roles                    = $roles ?? collect();
$permissions              = $permissions ?? collect();
$rolePermissionsMap       = $rolePermissionsMap ?? [];
$user                     = $user ?? null;
$selectedRoleId           = $selectedRoleId ?? null;
$selectedPermissions      = $selectedPermissions ?? [];
$isEdit                   = $isEdit ?? false;
$autoApplyRolePermissions = $autoApplyRolePermissions ?? true;
@endphp

@php
$selectedPermissions = collect(old('permission_ids', $selectedPermissions))->map(static fn($id) => (int) $id)->all();
$hasOldSelectedPermissions = old('permission_ids') !== null;
$nameValue      = old('name',      $user?->name);
$usernameValue  = old('username',  $user?->username);
$phoneValue     = old('phone',     $user?->phone);
$emailValue     = old('email',     $user?->email);
$roleValue      = old('role_id',   $selectedRoleId);
$newRoleName    = old('new_role_name', '');
$roleDisplayVal = '';
if ($roleValue) {
    $roleDisplayVal = $roles->firstWhere('id', (int)$roleValue)?->name ?? '';
} elseif ($newRoleName) {
    $roleDisplayVal = $newRoleName;
}
$jobTitleValue  = old('job_title', $user?->job_title);
@endphp

<div class="grid gap-5 md:grid-cols-2"
     data-user-permission-form
     data-has-old-permissions="{{ $hasOldSelectedPermissions ? '1' : '0' }}"
     data-auto-apply-role-permissions="{{ $autoApplyRolePermissions ? '1' : '0' }}">

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.users.fields.name') }} <span style="color:var(--error)">*</span></label>
        <input name="name" value="{{ $nameValue }}" required placeholder="Full name"
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('name')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.users.fields.username') }} <span style="color:var(--error)">*</span></label>
        <input name="username" value="{{ $usernameValue }}" required placeholder="e.g. john.doe"
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('username')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.users.fields.phone') }} <span style="color:var(--error)">*</span></label>
        <input name="phone" value="{{ $phoneValue }}" required placeholder="+20 123 456 7890"
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('phone')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.users.fields.email') }} <span style="color:var(--error)">*</span></label>
        <input name="email" type="email" value="{{ $emailValue }}" required placeholder="user@example.com"
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('email')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            {{ __('ui.users.fields.password') }}
            @if(!$isEdit)<span style="color:var(--error)">*</span>@endif
        </label>
        <input name="password" type="password" @required(!$isEdit)
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @if($isEdit)
        <p class="mt-1.5 text-xs" style="color:var(--on-surface-var)">{{ __('ui.users.password_optional_hint') }}</p>
        @endif
        @error('password')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            {{ __('ui.users.fields.password_confirmation') }}
            @if(!$isEdit)<span style="color:var(--error)">*</span>@endif
        </label>
        <input name="password_confirmation" type="password" @required(!$isEdit)
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
    </div>

    {{-- Role combobox: pick existing OR type a new custom role name --}}
    <div class="md:col-span-2" id="role-combobox-wrap">
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            {{ __('ui.users.fields.role') }} <span style="color:var(--error)">*</span>
        </label>

        {{-- Hidden inputs carrying the actual values --}}
        <input type="hidden" name="role_id"       id="role-id-hidden"   value="{{ $roleValue }}">
        <input type="hidden" name="new_role_name"  id="new-role-hidden"  value="{{ $newRoleName }}">

        {{-- Visible combobox --}}
        <div class="relative">
            <input type="text"
                   id="role-combobox-input"
                   list="roles-datalist"
                   autocomplete="off"
                   required
                   value="{{ $roleDisplayVal }}"
                   placeholder="{{ __('ui.users.select_or_type_role') }}"
                   data-role-permissions='@json($rolePermissionsMap)'
                   data-roles='@json($roles->map(fn($r)=>["id"=>$r->id,"name"=>$r->name]))'
                   class="w-full rounded-xl glass-input px-4 py-2.5 text-sm pr-9">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"
                 class="w-[1.25em] h-[1.25em] absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]"
                 style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>

        {{-- Datalist with existing role names --}}
        <datalist id="roles-datalist">
            @foreach($roles as $role)
            <option value="{{ $role->name }}">
            @endforeach
        </datalist>

        @error('role_id')    <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
        @error('new_role_name') <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror

        {{-- Badge shown when a NEW (not yet saved) custom role is typed --}}
        <p id="new-role-badge" class="mt-1.5 text-xs hidden"
           style="color:var(--tertiary)">
            ✦ {{ __('ui.users.new_role_will_be_created') }}
        </p>
        <p class="mt-1 text-xs" style="color:var(--on-surface-var)">{{ __('ui.users.role_template_hint') }}</p>

        <script>
        (function () {
            var input      = document.getElementById('role-combobox-input');
            var roleIdHid  = document.getElementById('role-id-hidden');
            var newRoleHid = document.getElementById('new-role-hidden');
            var badge      = document.getElementById('new-role-badge');
            var roles      = JSON.parse(input.dataset.roles || '[]');
            var permMap    = JSON.parse(input.dataset.rolePermissions || '{}');
            var permForm   = document.querySelector('[data-user-permission-form]');

            function syncRole() {
                var val   = input.value.trim();
                var match = roles.find(function(r) {
                    return r.name.toLowerCase() === val.toLowerCase();
                });
                if (match) {
                    roleIdHid.value  = match.id;
                    newRoleHid.value = '';
                    badge.classList.add('hidden');
                    // Auto-apply permissions for existing role
                    if (permForm && permForm.dataset.hasOldPermissions !== '1') {
                        var ids = permMap[match.id] || [];
                        permForm.querySelectorAll('input[name="permission_ids[]"]').forEach(function(cb) {
                            cb.checked = ids.includes(parseInt(cb.value));
                        });
                    }
                } else if (val.length > 0) {
                    roleIdHid.value  = '';
                    newRoleHid.value = val;
                    badge.classList.remove('hidden');
                } else {
                    roleIdHid.value  = '';
                    newRoleHid.value = '';
                    badge.classList.add('hidden');
                }
            }

            input.addEventListener('input', syncRole);
            input.addEventListener('change', syncRole);
            // Run once on load to handle edit-mode restored values
            syncRole();
        })();
        </script>
    </div>

    {{-- Job title — freetext label separate from the permission role --}}
    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            {{ __('ui.users.fields.job_title') }}
            <span class="text-xs font-normal opacity-60">({{ __('ui.common.optional') }})</span>
        </label>
        <input name="job_title"
               value="{{ $jobTitleValue }}"
               list="job-title-suggestions"
               placeholder="{{ __('ui.users.job_title_placeholder') }}"
               maxlength="120"
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        <datalist id="job-title-suggestions">
            @foreach($roles as $role)
            <option value="{{ $role->name }}">
            @endforeach
            <option value="مدير الفرع">
            <option value="مدير النطام">
            <option value="طباخ">
            <option value="نادل">
            <option value="بار">
            <option value="مساعد مدير">
        </datalist>
        @error('job_title')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
        <p class="mt-1.5 text-xs" style="color:var(--on-surface-var)">{{ __('ui.users.job_title_hint') }}</p>
    </div>

    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.users.fields.permissions') }}</label>
        <p class="mb-3 text-xs" style="color:var(--on-surface-var)">{{ __('ui.users.permissions_hint') }}</p>

        @if($permissions->isEmpty())
        <div class="rounded-xl px-4 py-3 text-sm border"
             style="border-color:color-mix(in srgb,var(--tertiary) 20%,transparent);background-color:color-mix(in srgb,var(--tertiary) 8%,transparent);color:var(--tertiary)">
            {{ __('ui.users.no_permissions') }}
        </div>
        @else
        @php
        $permissionGroupOrder = ['dashboard','orders','tables','categories','products','customers','users','inventory','recipes','suppliers','purchases','employees','attendance','salaries','offers','coupons','roles','permissions','billing','reports','financial','other'];
        $permissionPriorityOrder = ['dashboard.view','orders.view','orders.create','orders.update','orders.delete','tables.view','tables.create','tables.update','tables.delete','categories.view','categories.create','categories.update','categories.delete','products.view','products.create','products.update','products.delete','customers.view','customers.create','customers.update','customers.delete','users.view','users.create','users.update','users.delete','inventory.view','inventory.create','inventory.update','inventory.adjust','inventory.audit','inventory.delete','recipes.view','recipes.create','recipes.update','recipes.delete','suppliers.view','suppliers.create','suppliers.update','suppliers.delete','purchases.view','purchases.create','purchases.update','purchases.approve','employees.view','employees.create','employees.update','employees.delete','attendance.view','attendance.create','attendance.update','attendance.delete','salaries.view','salaries.create','salaries.update','offers.view','offers.create','offers.update','offers.delete','coupons.view','coupons.create','coupons.update','coupons.delete','roles.manage','permissions.manage','billing.manage','reports.view','financial.view'];
        $permissionPriorityMap   = array_flip($permissionPriorityOrder);
        $permissionActionLabels  = __('ui.users.permission_actions');
        $permissionHelpDefaults  = __('ui.users.permission_help_defaults');

        $groupedPermissions = $permissions
            ->sortBy(fn($p) => sprintf('%03d-%s', $permissionPriorityMap[(string)($p->slug??'')] ?? 999, (string)($p->name??$p->slug??'')))
            ->groupBy(fn($p): string => (explode('.', (string)($p->slug??''))[0] ?? '') ?: 'other')
            ->sortBy(fn($_, string $g): int => ($i = array_search($g, $permissionGroupOrder, true)) === false ? 999 : $i);
        @endphp

        <div class="rounded-2xl border p-3"
             style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <div class="grid max-h-[58vh] gap-3 overflow-y-auto pr-1 lg:grid-cols-2 2xl:grid-cols-3">
                @foreach($groupedPermissions as $group => $groupPermissions)
                @php
                $groupNameKey = 'ui.users.permission_groups.' . $group;
                $groupName    = __($groupNameKey);
                if ($groupName === $groupNameKey) { $groupName = ucfirst(str_replace('_', ' ', (string)$group)); }
                @endphp
                <section class="flex h-full flex-col rounded-xl border p-2.5"
                         style="border-color:color-mix(in srgb,var(--primary) 12%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent)">
                    <div class="mb-2 flex items-center justify-between gap-2 border-b pb-2"
                         style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)">
                        <h4 class="text-sm font-semibold" style="color:var(--on-surface)">{{ $groupName }}</h4>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold"
                              style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);color:var(--primary)">
                            {{ $groupPermissions->count() }}
                        </span>
                    </div>
                    <div class="grid gap-1.5">
                        @foreach($groupPermissions as $permission)
                        @php
                        $pSlug       = (string)($permission->slug ?? '');
                        $transSlug   = str_replace('.', '_', $pSlug);
                        $actionSlug  = explode('.', $pSlug)[1] ?? 'manage';
                        $fnKey       = 'ui.users.permission_labels.' . $transSlug;
                        $fhKey       = 'ui.users.permission_help.'   . $transSlug;
                        $fName       = __($fnKey);
                        $fHelp       = __($fhKey);
                        $actionLabel = is_array($permissionActionLabels) ? ($permissionActionLabels[$actionSlug] ?? ucfirst($actionSlug)) : ucfirst($actionSlug);
                        if ($fName === $fnKey) { $fName = trim($actionLabel . ' ' . $groupName); }
                        if ($fHelp === $fhKey) {
                            if (is_array($permissionHelpDefaults) && isset($permissionHelpDefaults[$actionSlug])) {
                                $fHelp = str_replace(':group', $groupName, (string)$permissionHelpDefaults[$actionSlug]);
                            } else { $fHelp = $permission->description ?: $pSlug; }
                        }
                        @endphp
                        <label class="group flex items-start gap-2 rounded-lg border px-2.5 py-2 text-sm transition-all cursor-pointer"
                               style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"
                               onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--primary) 8%,transparent)';this.style.borderColor='color-mix(in srgb,var(--primary) 20%,transparent)'"
                               onmouseleave="this.style.backgroundColor='';this.style.borderColor='color-mix(in srgb,var(--primary) 8%,transparent)'">
                            <input type="checkbox" name="permission_ids[]"
                                   value="{{ $permission->id }}"
                                   data-permission-checkbox
                                   @checked(in_array((int)$permission->id, $selectedPermissions, true))
                                   class="mt-0.5 h-4 w-4 rounded"
                                   style="accent-color:var(--primary)">
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-xs font-semibold" style="color:var(--on-surface)" title="{{ $fName }}">{{ $fName }}</span>
                                <span class="mt-0.5 block truncate text-[11px] leading-4" style="color:var(--on-surface-var)" title="{{ $fHelp }}">{{ $fHelp }}</span>
                            </span>
                        </label>
                        @endforeach
                    </div>
                </section>
                @endforeach
            </div>
        </div>
        @endif

        @error('permission_ids')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
        @error('permission_ids.*')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
</div>

@once
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-user-permission-form]').forEach((form) => {
        const roleSelect = form.querySelector('[data-role-select]');
        const permissionInputs = Array.from(form.querySelectorAll('[data-permission-checkbox]'));
        if (!roleSelect || permissionInputs.length === 0) return;
        let rolePermissionsMap = {};
        try { rolePermissionsMap = JSON.parse(roleSelect.dataset.rolePermissions || '{}'); } catch { rolePermissionsMap = {}; }
        const hasOldPermissions       = form.dataset.hasOldPermissions === '1';
        const autoApplyRolePermissions = form.dataset.autoApplyRolePermissions === '1';
        const applyRolePermissions = () => {
            const allowedIds = (Array.isArray(rolePermissionsMap[roleSelect.value]) ? rolePermissionsMap[roleSelect.value] : []).map(Number);
            permissionInputs.forEach((input) => { input.checked = allowedIds.includes(Number(input.value)); });
        };
        roleSelect.addEventListener('change', applyRolePermissions);
        if (autoApplyRolePermissions && !hasOldPermissions && roleSelect.value !== '') { applyRolePermissions(); }
    });
});
</script>
@endonce
