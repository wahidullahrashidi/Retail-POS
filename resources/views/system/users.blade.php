@extends('layouts.app')

@push('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endpush

@section('content')
    <div class="um" x-data="usersPage()" x-init="init()">

        {{-- ════ TOPBAR ════ --}}
        <div class="um-top">
            <div class="um-title">Afghan <em>POS</em> — User Management</div>
            <div class="top-r">
                <button class="btn btn-primary" @click="openUserModal(null)">
                    <i class="fas fa-user-plus"></i> Add User
                </button>
            </div>
        </div>

        {{-- ════ STATS ════ --}}
        <div class="stat-strip">
            <div class="stat-tile" style="--ac:var(--blue)">
                <div class="st-label">Total Users <span style="color:var(--blue)"><i class="fas fa-users"></i></span></div>
                <div class="st-val">{{ $stats['total'] ?? 0 }}</div>
                <div class="st-sub">{{ $stats['active'] ?? 0 }} active accounts</div>
            </div>
            <div class="stat-tile" style="--ac:var(--violet)">
                <div class="st-label">Admins <span style="color:var(--violet)"><i class="fas fa-user-shield"></i></span>
                </div>
                <div class="st-val" style="color:var(--violet)">{{ $stats['admins'] ?? 0 }}</div>
                <div class="st-sub">full system access</div>
            </div>
            <div class="stat-tile" style="--ac:var(--teal)">
                <div class="st-label">Managers <span style="color:var(--teal)"><i class="fas fa-user-tie"></i></span></div>
                <div class="st-val" style="color:var(--teal)">{{ $stats['managers'] ?? 0 }}</div>
                <div class="st-sub">inventory + reports</div>
            </div>
            <div class="stat-tile" style="--ac:var(--green)">
                <div class="st-label">Cashiers <span style="color:var(--green)"><i class="fas fa-cash-register"></i></span>
                </div>
                <div class="st-val" style="color:var(--green)">{{ $stats['cashiers'] ?? 0 }}</div>
                <div class="st-sub">POS operations</div>
            </div>
        </div>

        {{-- ════ TOOLBAR ════ --}}
        <div class="um-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input class="um-search" type="text" x-model="search" @input.debounce.350ms="loadUsers()"
                    placeholder="Name, email…">
            </div>
            <select class="f-sel" x-model="filterRole" @change="loadUsers()">
                <option value="">All Roles</option>
                @foreach ($roles ?? [] as $role)
                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                @endforeach
            </select>
            <div class="tab-strip">
                <button type="button" class="tab-btn" :class="tab === 'all' ? 'active' : ''"
                    @click="tab='all';loadUsers()">All</button>
                <button type="button" class="tab-btn" :class="tab === 'active' ? 'active' : ''"
                    @click="tab='active';loadUsers()">Active</button>
                <button type="button" class="tab-btn" :class="tab === 'inactive' ? 'active' : ''"
                    @click="tab='inactive';loadUsers()">Inactive</button>
            </div>
        </div>

        {{-- ════ MAIN ════ --}}
        <div class="um-main" :class="selected ? 'panel-open' : ''">

            {{-- USERS GRID --}}
            <div>
                {{-- Loading --}}
                <div class="users-grid" x-show="loading">
                    <div class="loading-state"><i class="fas fa-spinner fa-spin" style="font-size:20px"></i></div>
                </div>

                <div class="users-grid" x-show="!loading">
                    <div class="empty-state" x-show="users.length===0">
                        <i class="fas fa-users-slash"></i>
                        <p>No users found.<br>Add your first user to get started.</p>
                    </div>

                    <template x-for="u in users" :key="u.id">
                        <div class="user-card"
                            :class="[selected?.id === u.id ? 'selected' : '', u.is_active ? '' : 'inactive']"
                            @click="openDetail(u)">

                            {{-- Role color accent --}}
                            <div class="user-card-accent"
                                :class="u.role_name === 'admin' ? 'accent-admin' : u.role_name === 'manager' ?
                                    'accent-manager' : 'accent-cashier'">
                            </div>

                            <div class="uc-body">
                                <div class="uc-top">
                                    <div class="uc-avatar" :style="`background:${roleColor(u.role_name)}`">
                                        <template x-if="u.photo">
                                            <img :src="'/storage/users/' + u.photo" :alt="u.name">
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
                                                :class="u.role_name === 'admin' ? 'role-admin' : u.role_name === 'manager' ?
                                                    'role-manager' : 'role-cashier'">
                                                <i
                                                    :class="u.role_name === 'admin' ? 'fas fa-shield-halved' : u
                                                        .role_name ===
                                                        'manager' ?
                                                        'fas fa-user-tie' : 'fas fa-cash-register'"></i>
                                                <span x-text="u.role_display"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="uc-stats">
                                    <div class="uc-stat">
                                        <div class="uc-stat-val" x-text="u.sale_count || 0"></div>
                                        <div class="uc-stat-label">Sales</div>
                                    </div>
                                    <div class="uc-stat">
                                        <div class="uc-stat-val" x-text="u.shift_count || 0"></div>
                                        <div class="uc-stat-label">Shifts</div>
                                    </div>
                                    <div class="uc-stat">
                                        <div class="uc-stat-val" style="font-size:11px"
                                            x-text="u.total_sales ? 'Af ' + fmtK(u.total_sales) : '—'"></div>
                                        <div class="uc-stat-label">Revenue</div>
                                    </div>
                                </div>
                            </div>

                            <div class="uc-footer">
                                <div class="uc-last-login">
                                    <i class="fas fa-clock"></i>
                                    <span x-text="u.last_login ? u.last_login : 'Never logged in'"></span>
                                </div>
                                <span class="pill" :class="u.is_active ? 'pill-green' : 'pill-gray'"
                                    x-text="u.is_active?'Active':'Inactive'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ════ DETAIL PANEL ════ --}}
            <div class="detail-panel" x-show="selected" x-cloak>
                <div class="dp-head">
                    <span class="dp-head-label">User Detail</span>
                    <button class="dp-close" @click="selected=null"><i class="fas fa-times"></i></button>
                </div>
                <div class="dp-body">

                    {{-- Hero --}}
                    <div class="dp-hero">
                        <div class="dp-av-wrap">
                            <div class="dp-avatar" :style="`background:${roleColor(selected?.role_name)}`">
                                <template x-if="selected?.photo">
                                    <img :src="'/storage/users/' + selected.photo" :alt="selected.name">
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
                                :class="selected?.role_name === 'admin' ? 'role-admin' : selected
                                    ?.role_name === 'manager' ?
                                    'role-manager' : 'role-cashier'">
                                <i
                                    :class="selected?.role_name === 'admin' ? 'fas fa-shield-halved' : selected
                                        ?.role_name ===
                                        'manager' ?
                                        'fas fa-user-tie' : 'fas fa-cash-register'"></i>
                                <span x-text="selected?.role_display"></span>
                            </span>
                            <span class="pill" :class="selected?.is_active ? 'pill-green' : 'pill-gray'"
                                style="margin-left:6px" x-text="selected?.is_active?'Active':'Inactive'"></span>
                        </div>
                    </div>

                    {{-- KPIs --}}
                    <div class="dp-kpi">
                        <div class="dp-kpi-item">
                            <div class="dp-kpi-val" x-text="selected?.sale_count || 0"></div>
                            <div class="dp-kpi-label">Total Sales</div>
                        </div>
                        <div class="dp-kpi-item">
                            <div class="dp-kpi-val" x-text="selected?.shift_count || 0"></div>
                            <div class="dp-kpi-label">Shifts</div>
                        </div>
                        <div class="dp-kpi-item">
                            <div class="dp-kpi-val" style="font-size:12px"
                                x-text="selected?.total_sales ? 'Af ' + fmtK(selected.total_sales) : '—'"></div>
                            <div class="dp-kpi-label">Revenue</div>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-id-card"></i> Account Info</div>
                        <div class="info-grid">
                            <div class="info-field">
                                <div class="if-label">Full Name</div>
                                <div class="if-val" x-text="selected?.name"></div>
                            </div>
                            <div class="info-field">
                                <div class="if-label">Role</div>
                                <div class="if-val" x-text="selected?.role_display"></div>
                            </div>
                            <div class="info-field full">
                                <div class="if-label">Email</div>
                                <div class="if-val mono" x-text="selected?.email"></div>
                            </div>
                            <div class="info-field">
                                <div class="if-label">PIN Code</div>
                                <div class="pin-display" x-show="selected?.has_pin">
                                    <template x-for="i in 4" :key="i">
                                        <div class="pin-dot"></div>
                                    </template>
                                </div>
                                <div class="if-val" x-show="!selected?.has_pin" style="color:var(--ink3)">Not set</div>
                            </div>
                            <div class="info-field">
                                <div class="if-label">Last Login</div>
                                <div class="if-val mono" x-text="selected?.last_login || 'Never'"></div>
                            </div>
                            <div class="info-field">
                                <div class="if-label">Member Since</div>
                                <div class="if-val mono" x-text="selected?.created_at"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Permissions --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-lock"></i> Permissions</div>
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
                        <div class="dp-section-title"><i class="fas fa-clock-rotate-left"></i> Recent Shifts</div>
                        <div x-show="detailLoading"
                            style="text-align:center;padding:1rem;color:var(--ink3);font-size:12px">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <div x-show="!detailLoading">
                            <div x-show="recentShifts.length===0"
                                style="text-align:center;padding:.75rem;color:var(--ink4);font-size:12px">
                                No shift history yet.
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
                                            x-text="s.is_closed?'Closed':'Active'" style="font-size:9px"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
                <div class="dp-foot">
                    <button type="button" class="btn btn-ghost" style="flex:1" @click="openUserModal(selected)">
                        <i class="fas fa-pen"></i> Edit
                    </button>
                    <button type="button" class="btn btn-amber" @click="openPasswordModal(selected)">
                        <i class="fas fa-key"></i> Reset PW
                    </button>
                    <button type="button" class="btn btn-danger" @click="toggleUser(selected)">
                        <i class="fas fa-power-off"></i>
                        <span x-text="selected?.is_active?'Deactivate':'Activate'"></span>
                    </button>
                </div>
            </div>

        </div>{{-- /um-main --}}

        {{-- ════════════════════════════════════════
     MODAL: ADD / EDIT USER
════════════════════════════════════════ --}}
        <div class="modal-overlay" x-show="showUserModal" x-cloak @click.self="showUserModal=false">
            <div class="modal-card modal-md">
                <div class="modal-head">
                    <div class="modal-title" x-text="editingUser ? 'Edit User' : 'New User'"></div>
                    <button class="modal-close" @click="showUserModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">

                    {{-- Photo --}}
                    <div class="form-section-title"><i class="fas fa-image"></i> Profile Photo</div>
                    <div class="photo-upload" style="margin-bottom:1rem">
                        <div class="photo-preview" @click="$refs.photoInput.click()">
                            <template x-if="photoPreview">
                                <img :src="photoPreview" alt="preview">
                            </template>
                            <template x-if="!photoPreview && uf.photo">
                                <img :src="'/storage/users/' + uf.photo" alt="photo">
                            </template>
                            <template x-if="!photoPreview && !uf.photo">
                                <span>📷</span>
                            </template>
                        </div>
                        <div>
                            <button type="button" class="btn btn-ghost btn-sm" @click="$refs.photoInput.click()">
                                <i class="fas fa-upload"></i> Upload Photo
                            </button>
                            <input type="file" x-ref="photoInput" accept="image/*" class="hidden"
                                style="display:none" @change="previewPhoto($event)">
                            <div style="font-size:11px;color:var(--ink3);margin-top:5px">JPG, PNG up to 2MB</div>
                        </div>
                    </div>

                    {{-- Basic info --}}
                    <div class="form-section-title"><i class="fas fa-user"></i> Basic Info</div>
                    <div class="form-grid form-2" style="margin-bottom:1rem">
                        <div>
                            <label class="field-label">Full Name <span class="field-req">*</span></label>
                            <input type="text" class="field-input" x-model="uf.name" placeholder="Full name">
                        </div>
                        <div>
                            <label class="field-label">Email <span class="field-req">*</span></label>
                            <input type="email" class="field-input" x-model="uf.email"
                                placeholder="email@example.com">
                        </div>
                        <div>
                            <label class="field-label">Role <span class="field-req">*</span></label>
                            <select class="field-input" x-model="uf.role_id" @change="updatePermissionsFromRole()">
                                <option value="">Select role</option>
                                @foreach ($roles ?? [] as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="!editingUser">
                            <label class="field-label">Password <span class="field-req">*</span></label>
                            <input type="password" class="field-input" x-model="uf.password" @input="checkPwStrength()"
                                placeholder="Min 8 characters">
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
                    <div class="form-section-title"><i class="fas fa-hashtag"></i> Cashier PIN Code</div>
                    <div style="margin-bottom:1rem">
                        <div style="font-size:12px;color:var(--ink3);margin-bottom:.75rem">4-digit PIN for quick login at
                            POS terminal</div>
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
                        <div style="font-size:11px;color:var(--ink3);text-align:center;margin-top:6px">Leave blank to keep
                            existing PIN</div>
                    </div>

                    {{-- Permissions --}}
                    {{-- Permissions (read-only, inherited from role) --}}
                    <div class="form-section-title">
                        <i class="fas fa-lock"></i> Permissions (inherited from role)
                    </div>
                    <div style="font-size:11px;color:var(--ink3);margin-bottom:.75rem">
                        Permissions are set per role. Change the role to update access.
                    </div>
                    <div class="perm-grid">
                        <template x-for="perm in uf.permissions" :key="perm">
                            <div class="perm-item active" style="cursor:default">
                                <div class="perm-check"><i class="fas fa-check"></i></div>
                                <span x-text="perm"></span>
                            </div>
                        </template>
                        <div x-show="uf.permissions.length===0"
                            style="grid-column:span 2;font-size:12px;color:var(--ink3);padding:.5rem">
                            Select a role to see its permissions.
                        </div>
                    </div>

                    <div class="form-err" x-show="formError" x-text="formError" x-cloak></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" @click="showUserModal=false">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="saveUser()" :disabled="saving">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="saving?'Saving…':(editingUser?'Update User':'Create User')"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════
     MODAL: RESET PASSWORD
════════════════════════════════════════ --}}
        <div class="modal-overlay" x-show="showPasswordModal" x-cloak @click.self="showPasswordModal=false">
            <div class="modal-card modal-sm">
                <div class="modal-head">
                    <div class="modal-title">Reset Password</div>
                    <button class="modal-close" @click="showPasswordModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="warn-box">
                        <i class="fas fa-triangle-exclamation" style="flex-shrink:0;margin-top:1px"></i>
                        <div>You are resetting the password for <strong x-text="passwordTarget?.name"></strong>. They will
                            need to use this new password on their next login.</div>
                    </div>
                    <div class="form-grid">
                        <div>
                            <label class="field-label">New Password <span class="field-req">*</span></label>
                            <input type="password" class="field-input" x-model="newPassword"
                                @input="checkNewPwStrength()" placeholder="Min 8 characters">
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
                            <label class="field-label">Confirm Password <span class="field-req">*</span></label>
                            <input type="password" class="field-input" x-model="confirmPassword"
                                placeholder="Re-enter password">
                        </div>
                    </div>
                    <div class="form-err" x-show="pwError" x-text="pwError" x-cloak></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" @click="showPasswordModal=false">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="savePassword()" :disabled="saving">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="saving?'Saving…':'Reset Password'"></span>
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

                /* list */
                users: [],
                loading: true,
                search: '',
                filterRole: '',
                tab: 'all',

                /* detail */
                selected: null,
                recentShifts: [],
                detailLoading: false,

                /* user modal */
                showUserModal: false,
                editingUser: null,
                uf: {
                    name: '',
                    email: '',
                    role_id: '',
                    password: '',
                    permissions: [],
                    photo: ''
                },
                pinDigits: ['', '', '', ''],
                photoPreview: null,
                photoFile: null,
                pwStrength: {
                    pct: 0,
                    color: var (--ink4),
                    label: ''
                },
                formError: '',
                saving: false,

                /* password modal */
                showPasswordModal: false,
                passwordTarget: null,
                newPassword: '',
                confirmPassword: '',
                newPwStrength: {
                    pct: 0,
                    color: 'var(--ink4)',
                    label: ''
                },
                pwError: '',

                /* roles from server */
                rolesData: @json($roles ?? []),

                /* all permissions */
                allPermissions: [{
                        key: 'pos.sale',
                        label: 'Process Sales'
                    },
                    {
                        key: 'pos.return',
                        label: 'Process Returns'
                    },
                    {
                        key: 'pos.hold',
                        label: 'Hold Sales'
                    },
                    {
                        key: 'inventory.view',
                        label: 'View Inventory'
                    },
                    {
                        key: 'inventory.edit',
                        label: 'Edit Inventory'
                    },
                    {
                        key: 'customers.view',
                        label: 'View Customers'
                    },
                    {
                        key: 'customers.edit',
                        label: 'Edit Customers'
                    },
                    {
                        key: 'reports.view',
                        label: 'View Reports'
                    },
                    {
                        key: 'loans.manage',
                        label: 'Manage Loans'
                    },
                    {
                        key: 'suppliers.view',
                        label: 'View Suppliers'
                    },
                    {
                        key: 'users.manage',
                        label: 'Manage Users'
                    },
                    {
                        key: 'settings.access',
                        label: 'System Settings'
                    },
                ],

                /* urls */
                urls: {
                    list: '{{ route('pos.users.index') }}',
                    store: '{{ route('pos.users.store') }}',
                    detail: '{{ url('pos/users') }}',
                    toggle: '{{ url('pos/users') }}',
                    password: '{{ route('pos.users.password') }}',
                    csrf: document.querySelector('meta[name=csrf-token]').content,
                },

                /* ── Init ── */
                async init() {
                    await this.loadUsers();
                },

                /* ── Load users ── */
                async loadUsers() {
                    this.loading = true;
                    try {
                        const p = new URLSearchParams({
                            q: this.search,
                            role: this.filterRole,
                            tab: this.tab
                        });
                        const r = await fetch(this.urls.list + '?' + p, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        this.users = await r.json();
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.loading = false;
                    }
                },

                /* ── Detail panel ── */
                async openDetail(u) {
                    this.selected = u;
                    this.recentShifts = [];
                    this.detailLoading = true;
                    try {
                        const r = await fetch(`${this.urls.detail}/${u.id}/detail`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const d = await r.json();
                        this.recentShifts = d.shifts;
                        this.selected = {
                            ...this.selected,
                            ...d.user
                        };
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.detailLoading = false;
                    }
                },

                /* ── User modal ── */
                openUserModal(u) {
                    this.editingUser = u;
                    this.photoPreview = null;
                    this.photoFile = null;
                    this.pinDigits = ['', '', '', ''];
                    this.formError = '';
                    this.pwStrength = {
                        pct: 0,
                        color: 'var(--ink4)',
                        label: ''
                    };

                    if (u) {
                        this.uf = {
                            name: u.name,
                            email: u.email,
                            role_id: u.role_id,
                            password: '',
                            permissions: u.permissions || [], // these come from the role, read-only
                            photo: u.photo || '',
                        };
                    } else {
                        this.uf = {
                            name: '',
                            email: '',
                            role_id: '',
                            password: '',
                            permissions: u.permissions || [], // these come from the role, read-only
                            photo: ''
                        };
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
                                JSON.parse(role.permissions) :
                                role.permissions;
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

                checkPwStrength() {
                    this.pwStrength = this.calcStrength(this.uf.password);
                },
                checkNewPwStrength() {
                    this.newPwStrength = this.calcStrength(this.newPassword);
                },

                calcStrength(pw) {
                    if (!pw) return {
                        pct: 0,
                        color: 'var(--ink4)',
                        label: ''
                    };
                    let score = 0;
                    if (pw.length >= 8) score++;
                    if (pw.length >= 12) score++;
                    if (/[A-Z]/.test(pw)) score++;
                    if (/[0-9]/.test(pw)) score++;
                    if (/[^A-Za-z0-9]/.test(pw)) score++;
                    const map = [{
                            pct: 20,
                            color: 'var(--red)',
                            label: 'Very Weak'
                        },
                        {
                            pct: 40,
                            color: 'var(--red)',
                            label: 'Weak'
                        },
                        {
                            pct: 60,
                            color: 'var(--amber)',
                            label: 'Fair'
                        },
                        {
                            pct: 80,
                            color: 'var(--green)',
                            label: 'Strong'
                        },
                        {
                            pct: 100,
                            color: 'var(--green)',
                            label: 'Very Strong'
                        },
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
                    if (!this.uf.name.trim()) {
                        this.formError = 'Name is required.';
                        return;
                    }
                    if (!this.uf.email.trim()) {
                        this.formError = 'Email is required.';
                        return;
                    }
                    if (!this.uf.role_id) {
                        this.formError = 'Role is required.';
                        return;
                    }
                    if (!this.editingUser && !this.uf.password) {
                        this.formError = 'Password is required for new users.';
                        return;
                    }

                    this.saving = true;
                    this.formError = '';

                    try {
                        const formData = new FormData();
                        formData.append('name', this.uf.name);
                        formData.append('email', this.uf.email);
                        formData.append('role_id', this.uf.role_id);
                        if (this.uf.password) formData.append('password', this.uf.password);
                        if (this.editingUser) formData.append('user_id', this.editingUser.id);
                        if (this.photoFile) formData.append('photo', this.photoFile);

                        // PIN
                        const pin = this.pinDigits.join('');
                        if (pin.length === 4) formData.append('pin_code', pin);

                        formData.append('_token', this.urls.csrf);

                        const r = await fetch(this.urls.store, {
                            method: 'POST',
                            body: formData
                        });
                        const d = await r.json();

                        if (d.success) {
                            this.showUserModal = false;
                            this.loadUsers();
                            if (this.selected?.id === this.editingUser?.id) {
                                this.selected = {
                                    ...this.selected,
                                    ...d.user
                                };
                            }
                        } else {
                            this.formError = d.message ?? 'Failed to save.';
                        }
                    } catch (e) {
                        this.formError = 'Network error.';
                    } finally {
                        this.saving = false;
                    }
                },

                /* ── Password reset ── */
                openPasswordModal(u) {
                    this.passwordTarget = u;
                    this.newPassword = '';
                    this.confirmPassword = '';
                    this.pwError = '';
                    this.newPwStrength = {
                        pct: 0,
                        color: 'var(--ink4)',
                        label: ''
                    };
                    this.showPasswordModal = true;
                },

                async savePassword() {
                    if (!this.newPassword) {
                        this.pwError = 'New password is required.';
                        return;
                    }
                    if (this.newPassword.length < 8) {
                        this.pwError = 'Password must be at least 8 characters.';
                        return;
                    }
                    if (this.newPassword !== this.confirmPassword) {
                        this.pwError = 'Passwords do not match.';
                        return;
                    }

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
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showPasswordModal = false;
                        } else {
                            this.pwError = d.message ?? 'Failed.';
                        }
                    } catch (e) {
                        this.pwError = 'Network error.';
                    } finally {
                        this.saving = false;
                    }
                },

                /* ── Toggle active ── */
                async toggleUser(u) {
                    if (!confirm(`${u.is_active?'Deactivate':'Activate'} ${u.name}?`)) return;
                    await fetch(`${this.urls.toggle}/${u.id}/toggle`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.urls.csrf
                        }
                    });
                    this.loadUsers();
                    if (this.selected?.id === u.id) this.selected = null;
                },

                /* ── Helpers ── */
                initials(name) {
                    if (!name) return '?';
                    return name.trim().split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
                },
                roleColor(role) {
                    const c = {
                        admin: '#7c3aed',
                        manager: '#2e5fe8',
                        cashier: '#0891b2'
                    };
                    return c[role] ?? '#6b7280';
                },
                fmt(n) {
                    return Number(n || 0).toLocaleString('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                },
                fmtK(n) {
                    return n >= 1000 ? (n / 1000).toFixed(1) + 'k' : this.fmt(n);
                },
            }));
        });
    </script>
@endpush
