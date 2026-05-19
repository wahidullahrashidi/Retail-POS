@extends('layouts.app')

@push('styles')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/pages/users.css'])
    @endif
@endpush

@section('content')
    <div class="um" x-data="usersPage()" x-init="init()">

        {{-- ════ TOPBAR ════ --}}
        <div class="um-top">
            <div class="um-title">Afghan <em>POS</em> — {{ __('messages.user_management') }}</div>
            <div class="top-r">
                <button class="btn btn-primary" @click="openUserModal(null)">
                    <i class="fas fa-user-plus"></i> {{ __('messages.add_user') }}
                </button>
            </div>
        </div>

        {{-- ════ STATS ════ --}}
        <div class="stat-strip">
            <div class="stat-tile" style="--ac:var(--blue)">
                <div class="st-label">{{ __('messages.total_users') }} <span style="color:var(--blue)"><i class="fas fa-users"></i></span></div>
                <div class="st-val">{{ $stats['total'] ?? 0 }}</div>
                <div class="st-sub">{{ $stats['active'] ?? 0 }} {{ __('messages.active_accounts') }}</div>
            </div>
            <div class="stat-tile" style="--ac:var(--violet)">
                <div class="st-label">{{ __('messages.admins') }} <span style="color:var(--violet)"><i class="fas fa-user-shield"></i></span></div>
                <div class="st-val" style="color:var(--violet)">{{ $stats['admins'] ?? 0 }}</div>
                <div class="st-sub">{{ __('messages.full_system_access') }}</div>
            </div>
            <div class="stat-tile" style="--ac:var(--teal)">
                <div class="st-label">{{ __('messages.managers') }} <span style="color:var(--teal)"><i class="fas fa-user-tie"></i></span></div>
                <div class="st-val" style="color:var(--teal)">{{ $stats['managers'] ?? 0 }}</div>
                <div class="st-sub">{{ __('messages.inventory_reports') }}</div>
            </div>
            <div class="stat-tile" style="--ac:var(--green)">
                <div class="st-label">{{ __('messages.cashiers') }} <span style="color:var(--green)"><i class="fas fa-cash-register"></i></span></div>
                <div class="st-val" style="color:var(--green)">{{ $stats['cashiers'] ?? 0 }}</div>
                <div class="st-sub">{{ __('messages.pos_operations') }}</div>
            </div>
        </div>

        {{-- ════ TOOLBAR ════ --}}
        <div class="um-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input class="um-search" type="search" autocomplete="off" autocapitalize="off" spellcheck="false"
                    x-model="search" @input.debounce.350ms="loadUsers()" placeholder="{{ __('messages.name_email') }}">
            </div>
            <select class="f-sel" x-model="filterRole" @change="loadUsers()">
                <option value="">{{ __('messages.all_roles') }}</option>
                @foreach ($roles ?? [] as $role)
                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                @endforeach
            </select>
            <div class="tab-strip">
                <button type="button" class="tab-btn" :class="tab === 'all' ? 'active' : ''"
                    @click="tab='all';loadUsers()">{{ __('messages.all') }}</button>
                <button type="button" class="tab-btn" :class="tab === 'active' ? 'active' : ''"
                    @click="tab='active';loadUsers()">{{ __('messages.active') }}</button>
                <button type="button" class="tab-btn" :class="tab === 'inactive' ? 'active' : ''"
                    @click="tab='inactive';loadUsers()">{{ __('messages.inactive') }}</button>
            </div>
        </div>

        {{-- ════ MAIN ════ --}}
        <div class="um-main" :class="selected ? 'panel-open' : ''">

            {{-- USERS GRID --}}
            <div>
                <div class="users-grid" x-show="loading">
                    <div class="loading-state"><i class="fas fa-spinner fa-spin" style="font-size:20px"></i></div>
                </div>

                <div class="users-grid" x-show="!loading">
                    <div class="empty-state" x-show="users.length===0">
                        <i class="fas fa-users-slash"></i>
                        <p>{{ __('messages.no_users_found') }}<br>{{ __('messages.add_first_user') }}</p>
                    </div>

                    <template x-for="u in users" :key="u.id">
                        <div class="user-card" :class="[selected?.id === u.id ? 'selected' : '', u.is_active ? '' : 'inactive']"
                            @click="openDetail(u)">

                            <div class="user-card-accent"
                                :class="u.role_name === 'admin' ? 'accent-admin' : u.role_name === 'manager' ? 'accent-manager' : 'accent-cashier'">
                            </div>

                            <div class="uc-body">
                                <div class="uc-top">
                                    <div class="uc-avatar" :style="`background:${roleColor(u.role_name)}`">
                                        <template x-if="u.photo">
                                            <img :src="'/storage/' + u.photo" :alt="u.name">
                                        </template>
                                        <template x-if="!u.photo">
                                            <span x-text="initials(u.name)"></span>
                                        </template>
                                        <span class="uc-online" :class="u.is_active ? 'online' : 'offline'"></span>
                                    </div>
                                    <div class="uc-info">
                                        <div class="uc-name" x-text="u.name"></div>
                                        <div class="uc-email" x-text="u.email"></div>
                                        <div>
                                            <span class="role-badge"
                                                :class="u.role_name === 'admin' ? 'role-admin' : u.role_name === 'manager' ? 'role-manager' : 'role-cashier'">
                                                <i :class="u.role_name === 'admin' ? 'fas fa-shield-halved' : u.role_name === 'manager' ? 'fas fa-user-tie' : 'fas fa-cash-register'"></i>
                                                <span x-text="u.role_display"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="uc-stats">
                                    <div class="uc-stat">
                                        <div class="uc-stat-val" x-text="u.sale_count || 0"></div>
                                        <div class="uc-stat-label">{{ __('messages.sales') }}</div>
                                    </div>
                                    <div class="uc-stat">
                                        <div class="uc-stat-val" x-text="u.shift_count || 0"></div>
                                        <div class="uc-stat-label">{{ __('messages.shifts') }}</div>
                                    </div>
                                    <div class="uc-stat">
                                        <div class="uc-stat-val" style="font-size:11px"
                                            x-text="u.total_sales ? 'Af ' + fmtK(u.total_sales) : '—'"></div>
                                        <div class="uc-stat-label">{{ __('messages.revenue') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="uc-footer">
                                <div class="uc-last-login">
                                    <i class="fas fa-clock"></i>
                                    <span x-text="u.last_login ? u.last_login : '{{ __('messages.never_logged_in') }}'"></span>
                                </div>
                                <span class="pill" :class="u.is_active ? 'pill-green' : 'pill-gray'"
                                    x-text="u.is_active ? '{{ __('messages.active') }}' : '{{ __('messages.inactive') }}'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ════ DETAIL PANEL ════ --}}
            <div class="detail-panel" x-show="selected" x-cloak>
                <div class="dp-head">
                    <span class="dp-head-label">{{ __('messages.user_detail') }}</span>
                    <button class="dp-close" @click="selected=null"><i class="fas fa-times"></i></button>
                </div>
                <div class="dp-body">

                    {{-- Hero --}}
                    <div class="dp-hero">
                        <div class="dp-av-wrap">
                            <div class="dp-avatar" :style="`background:${roleColor(selected?.role_name)}`">
                                <template x-if="selected?.photo">
                                    <img :src="'/storage/' + selected.photo" :alt="selected.name">
                                </template>
                                <template x-if="!selected?.photo">
                                    <span x-text="initials(selected?.name)"></span>
                                </template>
                            </div>
                            <span class="dp-online" :class="selected?.is_active ? 'online' : 'offline'"></span>
                        </div>
                        <div class="dp-name" x-text="selected?.name"></div>
                        <div class="dp-email" x-text="selected?.email"></div>
                        <div>
                            <span class="role-badge"
                                :class="selected?.role_name === 'admin' ? 'role-admin' : selected?.role_name === 'manager' ? 'role-manager' : 'role-cashier'">
                                <i :class="selected?.role_name === 'admin' ? 'fas fa-shield-halved' : selected?.role_name === 'manager' ? 'fas fa-user-tie' : 'fas fa-cash-register'"></i>
                                <span x-text="selected?.role_display"></span>
                            </span>
                            <span class="pill" :class="selected?.is_active ? 'pill-green' : 'pill-gray'"
                                style="margin-left:6px"
                                x-text="selected?.is_active ? '{{ __('messages.active') }}' : '{{ __('messages.inactive') }}'"></span>
                        </div>
                    </div>

                    {{-- KPIs --}}
                    <div class="dp-kpi">
                        <div class="dp-kpi-item">
                            <div class="dp-kpi-val" x-text="selected?.sale_count || 0"></div>
                            <div class="dp-kpi-label">{{ __('messages.total_sales') }}</div>
                        </div>
                        <div class="dp-kpi-item">
                            <div class="dp-kpi-val" x-text="selected?.shift_count || 0"></div>
                            <div class="dp-kpi-label">{{ __('messages.shifts') }}</div>
                        </div>
                        <div class="dp-kpi-item">
                            <div class="dp-kpi-val" style="font-size:12px"
                                x-text="selected?.total_sales ? 'Af ' + fmtK(selected.total_sales) : '—'"></div>
                            <div class="dp-kpi-label">{{ __('messages.revenue') }}</div>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-id-card"></i> {{ __('messages.account_info') }}</div>
                        <div class="info-grid">
                            <div class="info-field">
                                <div class="if-label">{{ __('messages.full_name') }}</div>
                                <div class="if-val" x-text="selected?.name"></div>
                            </div>
                            <div class="info-field">
                                <div class="if-label">{{ __('messages.role') }}</div>
                                <div class="if-val" x-text="selected?.role_display"></div>
                            </div>
                            <div class="info-field full">
                                <div class="if-label">{{ __('messages.email') }}</div>
                                <div class="if-val mono" x-text="selected?.email"></div>
                            </div>
                            <div class="info-field">
                                <div class="if-label">{{ __('messages.pin_code') }}</div>
                                <div class="pin-display" x-show="selected?.has_pin">
                                    <template x-for="i in 4" :key="i">
                                        <div class="pin-dot"></div>
                                    </template>
                                </div>
                                <div class="if-val" x-show="!selected?.has_pin" style="color:var(--ink3)">
                                    {{ __('messages.not_set') }}</div>
                            </div>
                            <div class="info-field">
                                <div class="if-label">{{ __('messages.last_login') }}</div>
                                <div class="if-val mono" x-text="selected?.last_login || '{{ __('messages.never') }}'"></div>
                            </div>
                            <div class="info-field">
                                <div class="if-label">{{ __('messages.member_since') }}</div>
                                <div class="if-val mono" x-text="selected?.created_at"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Permissions --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-lock"></i> {{ __('messages.permissions') }}</div>
                        <div class="perm-grid">
                            <template x-for="perm in (selected?.permissions || [])" :key="perm">
                                <div class="perm-item active">
                                    <div class="perm-check"><i class="fas fa-check"></i></div>
                                    <span x-text="perm"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Recent shifts --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-clock-rotate-left"></i> {{ __('messages.recent_shifts') }}</div>
                        <div x-show="detailLoading"
                            style="text-align:center;padding:1rem;color:var(--ink3);font-size:12px">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <div x-show="!detailLoading">
                            <div x-show="recentShifts.length===0"
                                style="text-align:center;padding:.75rem;color:var(--ink4);font-size:12px">
                                {{ __('messages.no_shift_history') }}
                            </div>
                            <template x-for="s in recentShifts" :key="s.id">
                                <div class="mini-shift">
                                    <div>
                                        <div class="ms-date" x-text="s.opened_at"></div>
                                        <div style="font-size:10px;color:var(--ink3)" x-text="s.duration"></div>
                                    </div>
                                    <div style="text-align:right">
                                        <div class="ms-sales" x-text="'Af ' + fmt(s.total_sales)"></div>
                                        <span class="pill" :class="s.is_closed ? 'pill-blue' : 'pill-green'"
                                            x-text="s.is_closed ? '{{ __('messages.closed') }}' : '{{ __('messages.active') }}'"
                                            style="font-size:9px"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
                <div class="dp-foot">
                    <button type="button" class="btn btn-ghost" style="flex:1" @click="openUserModal(selected)">
                        <i class="fas fa-pen"></i> {{ __('messages.edit') }}
                    </button>
                    <button type="button" class="btn btn-amber" @click="openPasswordModal(selected)">
                        <i class="fas fa-key"></i> {{ __('messages.reset_pw') }}
                    </button>
                    <button type="button" class="btn btn-danger" @click="toggleUser(selected)">
                        <i class="fas fa-power-off"></i>
                        <span x-text="selected?.is_active ? '{{ __('messages.deactivate') }}' : '{{ __('messages.activate') }}'"></span>
                    </button>
                </div>
            </div>

        </div>{{-- /um-main --}}

        {{-- ════ MODAL: ADD / EDIT USER ════ --}}
        <div class="modal-overlay" x-show="showUserModal" x-cloak @click.self="showUserModal=false">
            <div class="modal-card modal-md">
                <div class="modal-head">
                    <div class="modal-title" x-text="editingUser ? '{{ __('messages.edit_user') }}' : '{{ __('messages.new_user') }}'"></div>
                    <button class="modal-close" @click="showUserModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">

                    {{-- Photo --}}
                    <div class="form-section-title"><i class="fas fa-image"></i> {{ __('messages.profile_photo') }}</div>
                    <div class="photo-upload" style="margin-bottom:1rem">
                        <div class="photo-preview" @click="$refs.photoInput.click()">
                            <template x-if="photoPreview">
                                <img :src="photoPreview" alt="preview">
                            </template>
                            <template x-if="!photoPreview && uf.photo">
                                <img :src="'/storage/' + uf.photo" alt="photo">
                            </template>
                            <template x-if="!photoPreview && !uf.photo">
                                <span>📷</span>
                            </template>
                        </div>
                        <div>
                            <button type="button" class="btn btn-ghost btn-sm" @click="$refs.photoInput.click()">
                                <i class="fas fa-upload"></i> {{ __('messages.upload_photo') }}
                            </button>
                            <input type="file" x-ref="photoInput" accept="image/*" class="hidden"
                                style="display:none" @change="previewPhoto($event)">
                            <div style="font-size:11px;color:var(--ink3);margin-top:5px">{{ __('messages.photo_requirements') }}</div>
                        </div>
                    </div>

                    {{-- Basic info --}}
                    <div class="form-section-title"><i class="fas fa-user"></i> {{ __('messages.basic_info') }}</div>
                    <div class="form-grid form-2" style="margin-bottom:1rem">
                        <div>
                            <label class="field-label">{{ __('messages.full_name') }} <span class="field-req">*</span></label>
                            <input type="text" class="field-input" x-model="uf.name"
                                placeholder="{{ __('messages.full_name_placeholder') }}">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.email') }} <span class="field-req">*</span></label>
                            <input type="email" class="field-input" x-model="uf.email"
                                placeholder="{{ __('messages.email_placeholder') }}">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.role') }} <span class="field-req">*</span></label>
                            <select class="field-input" x-model="uf.role_id" @change="updatePermissionsFromRole()">
                                <option value="">{{ __('messages.select_role') }}</option>
                                @foreach ($roles ?? [] as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="!editingUser">
                            <label class="field-label">{{ __('messages.password') }} <span class="field-req">*</span></label>
                            <input type="password" class="field-input" x-model="uf.password" @input="checkPwStrength()"
                                placeholder="{{ __('messages.password_placeholder') }}">
                            <div class="pw-strength" x-show="uf.password">
                                <div class="pw-bar">
                                    <div class="pw-fill"
                                        :style="`width:${pwStrength.pct}%;background:${pwStrength.color}`"></div>
                                </div>
                                <span class="pw-label" :style="`color:${pwStrength.color}`"
                                    x-text="pwStrength.label"></span>
                            </div>
                        </div>
                    </div>

                    {{-- PIN --}}
                    <div class="form-section-title"><i class="fas fa-hashtag"></i> {{ __('messages.cashier_pin_code') }}</div>
                    <div style="margin-bottom:1rem">
                        <div style="font-size:12px;color:var(--ink3);margin-bottom:.75rem">{{ __('messages.pin_help_text') }}</div>
                        <div class="pin-input-row">
                            <input class="pin-box" type="password" x-model="pinDigits[0]" maxlength="1"
                                inputmode="numeric" @input="pinNext($event, 0)" @keydown.backspace="pinBack($event, 0)">
                            <input class="pin-box" type="password" x-model="pinDigits[1]" maxlength="1"
                                inputmode="numeric" @input="pinNext($event, 1)" @keydown.backspace="pinBack($event, 1)"
                                id="pin-1">
                            <input class="pin-box" type="password" x-model="pinDigits[2]" maxlength="1"
                                inputmode="numeric" @input="pinNext($event, 2)" @keydown.backspace="pinBack($event, 2)"
                                id="pin-2">
                            <input class="pin-box" type="password" x-model="pinDigits[3]" maxlength="1"
                                inputmode="numeric" @keydown.backspace="pinBack($event, 3)" id="pin-3">
                        </div>
                        <div style="font-size:11px;color:var(--ink3);text-align:center;margin-top:6px">
                            {{ __('messages.pin_leave_blank') }}</div>
                    </div>

                    {{-- Permissions --}}
                    <div class="form-section-title"><i class="fas fa-lock"></i> {{ __('messages.permissions') }}</div>
                    <div class="perm-grid" style="margin-bottom:.5rem">
                        <template x-for="perm in allPermissions" :key="perm.key">
                            <div class="perm-item" :class="uf.permissions.includes(perm.key) ? 'active' : ''"
                                @click="togglePerm(perm.key)">
                                <div class="perm-check">
                                    <i class="fas fa-check" x-show="uf.permissions.includes(perm.key)"></i>
                                </div>
                                <span x-text="perm.label"></span>
                            </div>
                        </template>
                    </div>

                    <div class="form-err" x-show="formError" x-text="formError" x-cloak></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost"
                        @click="showUserModal=false">{{ __('messages.cancel') }}</button>
                    <button type="button" class="btn btn-primary" @click="saveUser()" :disabled="saving">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="saving ? '{{ __('messages.saving') }}' : (editingUser ? '{{ __('messages.update_user') }}' : '{{ __('messages.create_user') }}')"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ════ MODAL: RESET PASSWORD ════ --}}
        <div class="modal-overlay" x-show="showPasswordModal" x-cloak @click.self="showPasswordModal=false">
            <div class="modal-card modal-sm">
                <div class="modal-head">
                    <div class="modal-title">{{ __('messages.reset_password') }}</div>
                    <button class="modal-close" @click="showPasswordModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="warn-box">
                        <i class="fas fa-triangle-exclamation" style="flex-shrink:0;margin-top:1px"></i>
                        <div>{{ __('messages.reset_password_warning') }} <strong x-text="passwordTarget?.name"></strong>.</div>
                    </div>
                    <div class="form-grid">
                        <div>
                            <label class="field-label">{{ __('messages.new_password') }} <span class="field-req">*</span></label>
                            <input type="password" class="field-input" x-model="newPassword"
                                @input="checkNewPwStrength()" placeholder="{{ __('messages.password_placeholder') }}">
                            <div class="pw-strength" x-show="newPassword">
                                <div class="pw-bar">
                                    <div class="pw-fill"
                                        :style="`width:${newPwStrength.pct}%;background:${newPwStrength.color}`"></div>
                                </div>
                                <span class="pw-label" :style="`color:${newPwStrength.color}`"
                                    x-text="newPwStrength.label"></span>
                            </div>
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.confirm_password') }} <span class="field-req">*</span></label>
                            <input type="password" class="field-input" x-model="confirmPassword"
                                placeholder="{{ __('messages.reenter_password') }}">
                        </div>
                    </div>
                    <div class="form-err" x-show="pwError" x-text="pwError" x-cloak></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost"
                        @click="showPasswordModal=false">{{ __('messages.cancel') }}</button>
                    <button type="button" class="btn btn-primary" @click="savePassword()" :disabled="saving">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="saving ? '{{ __('messages.saving') }}' : '{{ __('messages.reset_password_btn') }}'"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- /um --}}
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('usersPage', () => ({
                users: [],
                loading: true,
                search: '',
                filterRole: '',
                tab: 'all',

                selected: null,
                recentShifts: [],
                detailLoading: false,

                showUserModal: false,
                editingUser: null,
                uf: { name: '', email: '', role_id: '', password: '', permissions: [], photo: '' },
                pinDigits: ['', '', '', ''],
                photoPreview: null,
                photoFile: null,
                pwStrength: { pct: 0, color: 'var(--ink4)', label: '' },
                formError: '',
                saving: false,

                showPasswordModal: false,
                passwordTarget: null,
                newPassword: '',
                confirmPassword: '',
                newPwStrength: { pct: 0, color: 'var(--ink4)', label: '' },
                pwError: '',

                rolesData: @json($roles ?? []),

                allPermissions: [
                    { key: 'pos.sale', label: '{{ __('messages.perm_pos_sale') }}' },
                    { key: 'pos.return', label: '{{ __('messages.perm_pos_return') }}' },
                    { key: 'pos.hold', label: '{{ __('messages.perm_pos_hold') }}' },
                    { key: 'inventory.view', label: '{{ __('messages.perm_inventory_view') }}' },
                    { key: 'inventory.edit', label: '{{ __('messages.perm_inventory_edit') }}' },
                    { key: 'customers.view', label: '{{ __('messages.perm_customers_view') }}' },
                    { key: 'customers.edit', label: '{{ __('messages.perm_customers_edit') }}' },
                    { key: 'reports.view', label: '{{ __('messages.perm_reports_view') }}' },
                    { key: 'loans.manage', label: '{{ __('messages.perm_loans_manage') }}' },
                    { key: 'suppliers.view', label: '{{ __('messages.perm_suppliers_view') }}' },
                    { key: 'users.manage', label: '{{ __('messages.perm_users_manage') }}' },
                    { key: 'settings.access', label: '{{ __('messages.perm_settings_access') }}' },
                ],

                urls: {
                    list: '{{ url('/pos/users') }}',
                    store: '{{ url('/pos/users/store') }}',
                    detail: '{{ url('/pos/users') }}/',
                    toggle: '{{ url('/pos/users') }}/',
                    password: '{{ url('/pos/users/password') }}',
                    csrf: document.querySelector('meta[name=csrf-token]').content,
                },

                async init() {
                    await this.loadUsers();
                },

                async loadUsers() {
                    this.loading = true;
                    try {
                        const p = new URLSearchParams({ q: this.search, role: this.filterRole, tab: this.tab });
                        const r = await fetch(this.urls.list + '?' + p, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin'
                        });
                        if (!r.ok) throw new Error(`Failed to load users: ${r.status}`);
                        this.users = await r.json();
                    } catch (e) {
                        console.error(e);
                        this.users = [];
                    } finally {
                        this.loading = false;
                    }
                },

                async openDetail(u) {
                    this.selected = u;
                    this.recentShifts = [];
                    this.detailLoading = true;
                    try {
                        const r = await fetch(`${this.urls.detail}${u.id}/detail`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin'
                        });
                        const d = await r.json();
                        this.recentShifts = d.shifts;
                        this.selected = { ...this.selected, ...d.user };
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.detailLoading = false;
                    }
                },

                openUserModal(u) {
                    this.editingUser = u;
                    this.photoPreview = null;
                    this.photoFile = null;
                    this.pinDigits = ['', '', '', ''];
                    this.formError = '';
                    this.pwStrength = { pct: 0, color: 'var(--ink4)', label: '' };
                    if (u) {
                        this.uf = {
                            name: u.name, email: u.email, role_id: u.role_id,
                            password: '', permissions: u.permissions || [], photo: u.photo || ''
                        };
                    } else {
                        this.uf = { name: '', email: '', role_id: '', password: '', permissions: [], photo: '' };
                    }
                    this.showUserModal = true;
                },

                previewPhoto(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    this.photoFile = file;
                    this.photoPreview = URL.createObjectURL(file);
                },

                updatePermissionsFromRole() {
                    const role = this.rolesData.find(r => r.id == this.uf.role_id);
                    if (role?.permissions) {
                        try {
                            this.uf.permissions = typeof role.permissions === 'string' ?
                                JSON.parse(role.permissions) : role.permissions;
                        } catch (e) {
                            this.uf.permissions = [];
                        }
                    }
                },

                togglePerm(key) {
                    const idx = this.uf.permissions.indexOf(key);
                    if (idx === -1) this.uf.permissions.push(key);
                    else this.uf.permissions.splice(idx, 1);
                },

                checkPwStrength() { this.pwStrength = this.calcStrength(this.uf.password); },
                checkNewPwStrength() { this.newPwStrength = this.calcStrength(this.newPassword); },

                calcStrength(pw) {
                    if (!pw) return { pct: 0, color: 'var(--ink4)', label: '' };
                    let score = 0;
                    if (pw.length >= 8) score++;
                    if (pw.length >= 12) score++;
                    if (/[A-Z]/.test(pw)) score++;
                    if (/[0-9]/.test(pw)) score++;
                    if (/[^A-Za-z0-9]/.test(pw)) score++;
                    const map = [
                        { pct: 20, color: 'var(--red)', label: '{{ __('messages.very_weak') }}' },
                        { pct: 40, color: 'var(--red)', label: '{{ __('messages.weak') }}' },
                        { pct: 60, color: 'var(--amber)', label: '{{ __('messages.fair') }}' },
                        { pct: 80, color: 'var(--green)', label: '{{ __('messages.strong') }}' },
                        { pct: 100, color: 'var(--green)', label: '{{ __('messages.very_strong') }}' }
                    ];
                    return map[score - 1] || map[0];
                },

                pinNext(e, idx) {
                    if (e.target.value.length === 1 && idx < 3) {
                        document.getElementById(`pin-${idx + 1}`)?.focus();
                    }
                },
                pinBack(e, idx) {
                    if (e.key === 'Backspace' && !this.pinDigits[idx] && idx > 0) {
                        document.getElementById(`pin-${idx - 1}`)?.focus();
                    }
                },

                async saveUser() {
                    if (!this.uf.name.trim()) { this.formError = '{{ __('messages.name_required') }}'; return; }
                    if (!this.uf.email.trim()) { this.formError = '{{ __('messages.email_required') }}'; return; }
                    if (!this.uf.role_id) { this.formError = '{{ __('messages.role_required') }}'; return; }
                    if (!this.editingUser && !this.uf.password) { this.formError = '{{ __('messages.password_required') }}'; return; }

                    this.saving = true;
                    this.formError = '';
                    try {
                        const formData = new FormData();
                        formData.append('name', this.uf.name);
                        formData.append('email', this.uf.email);
                        formData.append('role_id', this.uf.role_id);
                        formData.append('permissions', JSON.stringify(this.uf.permissions));
                        if (this.uf.password) formData.append('password', this.uf.password);
                        if (this.editingUser) formData.append('user_id', this.editingUser.id);
                        if (this.photoFile) formData.append('photo', this.photoFile);
                        const pin = this.pinDigits.join('');
                        if (pin.length === 4) formData.append('pin_code', pin);
                        formData.append('_token', this.urls.csrf);

                        const r = await fetch(this.urls.store, {
                            method: 'POST',
                            body: formData,
                            credentials: 'same-origin'
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showUserModal = false;
                            this.loadUsers();
                            if (this.selected?.id === this.editingUser?.id) {
                                this.selected = { ...this.selected, ...d.user };
                            }
                        } else {
                            this.formError = d.message ?? '{{ __('messages.failed_to_save') }}';
                        }
                    } catch (e) {
                        this.formError = '{{ __('messages.network_error') }}';
                    } finally {
                        this.saving = false;
                    }
                },

                openPasswordModal(u) {
                    this.passwordTarget = u;
                    this.newPassword = '';
                    this.confirmPassword = '';
                    this.pwError = '';
                    this.newPwStrength = { pct: 0, color: 'var(--ink4)', label: '' };
                    this.showPasswordModal = true;
                },

                async savePassword() {
                    if (!this.newPassword) { this.pwError = '{{ __('messages.new_password_required') }}'; return; }
                    if (this.newPassword.length < 8) { this.pwError = '{{ __('messages.password_min_length') }}'; return; }
                    if (this.newPassword !== this.confirmPassword) { this.pwError = '{{ __('messages.passwords_mismatch') }}'; return; }

                    this.saving = true;
                    this.pwError = '';
                    try {
                        const r = await fetch(this.urls.password, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                user_id: this.passwordTarget.id,
                                password: this.newPassword
                            }),
                            credentials: 'same-origin'
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showPasswordModal = false;
                        } else {
                            this.pwError = d.message ?? '{{ __('messages.failed_to_save') }}';
                        }
                    } catch (e) {
                        this.pwError = '{{ __('messages.network_error') }}';
                    } finally {
                        this.saving = false;
                    }
                },

                async toggleUser(u) {
                    if (!confirm((u.is_active ? '{{ __('messages.confirm_deactivate') }}' : '{{ __('messages.confirm_activate') }}').replace(':name', u.name))) return;
                    await fetch(`${this.urls.toggle}${u.id}/toggle`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.urls.csrf },
                        credentials: 'same-origin'
                    });
                    this.loadUsers();
                    if (this.selected?.id === u.id) this.selected = null;
                },

                initials(name) {
                    if (!name) return '?';
                    return name.trim().split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
                },
                roleColor(role) {
                    const c = { admin: '#7c3aed', manager: '#2e5fe8', cashier: '#0891b2' };
                    return c[role] ?? '#6b7280';
                },
                fmt(n) {
                    return Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                },
                fmtK(n) {
                    return n >= 1000 ? (n / 1000).toFixed(1) + 'k' : this.fmt(n);
                },
            }));
        });
    </script>
@endpush