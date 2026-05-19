@extends('layouts.app')

@push('styles')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
@endpush

@section('content')
    <div class="cu" x-data="customersPage()" x-init="init()">

        {{-- ════ TOPBAR ════ --}}
        <div class="cu-top">
            <div class="cu-title">{{ __('messages.afghan_pos') }} — {{ __('messages.customers') }}</div>
            <div class="top-right">
                <button class="btn btn-ghost" @click="exportCsv()">
                    <i class="fas fa-file-export"></i> {{ __('messages.export') }}
                </button>
                <button class="btn btn-primary"
                    @click="$store.customerModal.open(c => { customers.unshift(c); stats.total++; })">
                    <i class="fas fa-user-plus"></i> {{ __('messages.new_customer') }}
                </button>
            </div>
        </div>

        {{-- ════ STATS ════ --}}
        <div class="stat-strip">
            <div class="stat-card" style="--ac:var(--blue)">
                <div class="sc-label">{{ __('messages.total_customers') }} <span class="sc-icon"
                        style="color:var(--blue)"><i class="fas fa-users"></i></span></div>
                <div class="sc-val">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="sc-sub">{{ $stats['active'] ?? 0 }} {{ __('messages.active') }}</div>
            </div>
            <div class="stat-card" style="--ac:var(--red)">
                <div class="sc-label">{{ __('messages.with_active_loans') }} <span class="sc-icon"
                        style="color:var(--red)"><i class="fas fa-file-invoice-dollar"></i></span></div>
                <div class="sc-val" style="color:var(--red)">{{ number_format($stats['with_loans'] ?? 0) }}</div>
                <div class="sc-sub">{{ __('messages.customers_with_balance') }}</div>
            </div>
            <div class="stat-card" style="--ac:var(--amber)">
                <div class="sc-label">{{ __('messages.total_outstanding') }} <span class="sc-icon"
                        style="color:var(--amber)"><i class="fas fa-coins"></i></span></div>
                <div class="sc-val" style="font-size:18px;color:var(--amber)">Af
                    {{ number_format($stats['total_outstanding'] ?? 0) }}</div>
                <div class="sc-sub">{{ __('messages.across_all_active_loans') }}</div>
            </div>
            <div class="stat-card" style="--ac:var(--red)">
                <div class="sc-label">{{ __('messages.overdue_loans') }} <span class="sc-icon" style="color:var(--red)"><i
                            class="fas fa-triangle-exclamation"></i></span></div>
                <div class="sc-val" style="color:var(--red)">{{ number_format($stats['overdue'] ?? 0) }}</div>
                <div class="sc-sub">{{ __('messages.past_due_date') }}</div>
            </div>
            <div class="stat-card" style="--ac:var(--green)">
                <div class="sc-label">{{ __('messages.lifetime_sales') }} <span class="sc-icon"
                        style="color:var(--green)"><i class="fas fa-chart-line"></i></span></div>
                <div class="sc-val" style="font-size:18px;color:var(--green)">Af
                    {{ number_format($stats['lifetime_sales'] ?? 0) }}</div>
                <div class="sc-sub">{{ __('messages.from_registered_customers') }}</div>
            </div>
        </div>

        {{-- ════ TOOLBAR ════ --}}
        <div class="cu-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input class="cu-search" type="search" autocomplete="off" autocapitalize="off" spellcheck="false"
                    x-model="search" @input.debounce.350ms="loadCustomers()"
                    placeholder="{{ __('messages.name_phone_city') }}">
            </div>
            <select class="filter-sel" x-model="filterLoan" @change="loadCustomers()">
                <option value="">{{ __('messages.all_customers') }}</option>
                <option value="has_loan">{{ __('messages.has_active_loan') }}</option>
                <option value="overdue">{{ __('messages.overdue_loan') }}</option>
                <option value="no_loan">{{ __('messages.no_loan') }}</option>
            </select>
            <select class="filter-sel" x-model="filterCity" @change="loadCustomers()">
                <option value="">{{ __('messages.all_cities') }}</option>
                @foreach ($cities ?? [] as $city)
                    <option value="{{ $city }}">{{ $city }}</option>
                @endforeach
            </select>
            <div class="tab-strip">
                <button type="button" class="tab-btn" :class="tab === 'all' ? 'active' : ''"
                    @click="tab='all';loadCustomers()">{{ __('messages.all') }}</button>
                <button type="button" class="tab-btn" :class="tab === 'active' ? 'active' : ''"
                    @click="tab='active';loadCustomers()">{{ __('messages.active') }}</button>
                <button type="button" class="tab-btn" :class="tab === 'inactive' ? 'active' : ''"
                    @click="tab='inactive';loadCustomers()">{{ __('messages.inactive') }}</button>
            </div>
        </div>

        {{-- ════ MAIN ════ --}}
        <div class="cu-main" :class="selected ? 'panel-open' : ''" style="align-items:start">

            {{-- TABLE --}}
            <div class="table-card">

                {{-- loading --}}
                <div class="loading-state" x-show="loading">
                    <i class="fas fa-spinner fa-spin" style="font-size:20px"></i>
                </div>

                <div x-show="!loading">
                    {{-- empty --}}
                    <div class="empty-state" x-show="customers.length === 0">
                        <i class="fas fa-users-slash"></i>
                        <p>{{ __('messages.no_customers_found') }}<<br>{{ __('messages.try_different_search_or_add') }}
                        </p>
                    </div>

                    <table class="cu-table {{ in_array(app()->getLocale(), ['ps', 'dr']) ? 'rtl-table' : '' }}"
                        x-show="customers.length > 0">
                        <thead>
                            <tr>
                                <th @click="sort('name')">{{ __('messages.customer') }} <i class="fas fa-sort"></i></th>
                                <th @click="sort('phone')">{{ __('messages.phone') }} <i class="fas fa-sort"></i></th>
                                <th>{{ __('messages.city') }}</th>
                                <th @click="sort('loan_balance')" class="cell-right">{{ __('messages.loan_balance') }} <i
                                        class="fas fa-sort"></i></th>
                                <th class="cell-right">{{ __('messages.total_purchases') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.last_sale') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="c in customers" :key="c.id">
                                <tr :class="selected?.id === c.id ? 'selected' : ''" @click="openDetail(c)">
                                    <td>
                                        <div class="cust-cell">
                                            <div class="cust-av" :style="`background:${avatarColor(c.name)}`"
                                                x-text="initials(c.name)"></div>
                                            <div>
                                                <div class="cust-name" x-text="c.name"></div>
                                                <div class="cust-city" x-text="c.city || '—'"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="cell-mono" x-text="c.phone"></td>
                                    <td style="color:var(--ink2);font-size:13px" x-text="c.city || '—'"></td>
                                    <td class="cell-right">
                                        <span class="loan-bal" :class="c.loan_balance > 0 ? 'has-loan' : 'no-loan'"
                                            x-text="c.loan_balance > 0 ? 'Af ' + fmt(c.loan_balance) : '—'"></span>
                                    </td>
                                    <td class="cell-right">
                                        <span
                                            style="font-family:var(--mono);font-size:13px;font-weight:500;color:var(--ink)"
                                            x-text="'Af ' + fmt(c.total_purchases)"></span>
                                    </td>
                                    <td>
                                        <span class="pill" :class="c.is_active ? 'pill-green' : 'pill-gray'"
                                            x-text="c.is_active ? '{{ __('messages.active') }}' : '{{ __('messages.inactive') }}'"></span>
                                    </td>
                                    <td style="font-size:12px;color:var(--ink3)"
                                        x-text="c.last_sale_at
? relativeTime(c.last_sale_at, '{{ app()->getLocale() }}')
        : '{{ __('messages.never') }}'">
                                    </td>
                                    <td @click.stop>
                                        <div class="row-acts">
                                            <button type="button" class="btn btn-ghost btn-sm" @click="openEdit(c)"
                                                title="{{ __('messages.edit') }}">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn-teal btn-sm" @click="openPayment(c)"
                                                x-show="c.loan_balance > 0" title="{{ __('messages.record_payment') }}">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" @click="toggleActive(c)"
                                                title="{{ __('messages.toggle_active') }}">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="pag-row" x-show="pagination.last_page > 1">
                        <div class="pag-info">
                            {{ __('messages.showing') }} <span x-text="pagination.from"></span>–<<span
                                x-text="pagination.to"></span>
                                {{ __('messages.of') }} <span x-text="pagination.total"></span>
                        </div>
                        <div class="pag-btns">
                            <button class="pag-btn" @click="goPage(pagination.current_page - 1)"
                                :disabled="pagination.current_page === 1">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <template x-for="p in pagination.last_page" :key="p">
                                <button class="pag-btn" :class="p === pagination.current_page ? '{{ __('messages.active') }}' : ''"
                                    @click="goPage(p)" x-text="p"></button>
                            </template>
                            <button class="pag-btn" @click="goPage(pagination.current_page + 1)"
                                :disabled="pagination.current_page === pagination.last_page">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════ DETAIL PANEL ════ --}}
            <div class="detail-panel" x-show="selected" x-cloak>

                <div class="dp-head">
                    <span
                        style="font-size:12px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.08em">{{ __('messages.customer_detail') }}</span>
                    <button class="dp-close" @click="selected=null"><i class="fas fa-times"></i></button>
                </div>

                {{-- Hero --}}
                <div class="dp-hero">
                    <div class="dp-avatar" :style="`background:${avatarColor(selected?.name)}`"
                        x-text="initials(selected?.name)"></div>
                    <div class="dp-name" x-text="selected?.name"></div>
                    <div class="dp-meta">
                        <span><i class="fas fa-phone"></i> <span x-text="selected?.phone"></span></span>
                        <span x-show="selected?.city">· <i class="fas fa-location-dot"></i> <span
                                x-text="selected?.city"></span></span>
                    </div>
                    <div style="margin-top:8px">
                        <span class="pill" :class="selected?.is_active ? 'pill-green' : 'pill-gray'"
                            x-text="selected?.is_active ? '{{ __('messages.active') }}' : '{{ __('messages.inactive') }}'"></span>
                    </div>
                </div>

                <div class="dp-body">

                    {{-- Loan summary --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-file-invoice-dollar"></i>
                            {{ __('messages.loan_status') }}</div>

                        <div class="loan-bar" :class="selected?.loan_balance > 0 ? '{{ __('messages.active') }}' : '{{ __('messages.clear') }}'">
                            <div>
                                <div class="lb-label"
                                    x-text="selected?.loan_balance > 0 ? '{{ __('messages.outstanding_balance') }}' : '{{ __('messages.no_active_loan') }}'">
                                </div>
                                <div style="font-size:11px;margin-top:2px"
                                    :style="selected?.loan_balance > 0 ? 'color:var(--red)' : 'color:var(--green)'"
                                    x-text="selected?.loan_balance > 0 ? (selected?.loan_count + ' {{ __('messages.loan_s') }}') : '{{ __('messages.all_paid_up') }}'">
                                </div>
                            </div>
                            <div class="lb-val" x-text="'Af ' + fmt(selected?.loan_balance || 0)"></div>
                        </div>

                        <button type="button" class="btn btn-teal" style="width:100%"
                            x-show="selected?.loan_balance > 0" @click="openPayment(selected)">
                            <i class="fas fa-money-bill-wave"></i> {{ __('messages.record_loan_payment') }}
                        </button>
                    </div>

                    {{-- Info grid --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-address-card"></i>
                            {{ __('messages.contact_info') }}</div>
                        <div class="dp-grid">
                            <div class="dp-field">
                                <div class="dp-field-label">{{ __('messages.primary_phone') }}</div>
                                <div class="dp-field-val mono" x-text="selected?.phone || '—'"></div>
                            </div>
                            <div class="dp-field">
                                <div class="dp-field-label">{{ __('messages.secondary_phone') }}</div>
                                <div class="dp-field-val mono" x-text="selected?.phone_secondary || '—'"></div>
                            </div>
                            <div class="dp-field full">
                                <div class="dp-field-label">{{ __('messages.address') }}</div>
                                <div class="dp-field-val" x-text="selected?.address || '—'"></div>
                            </div>
                            <div class="dp-field">
                                <div class="dp-field-label">{{ __('messages.city') }}</div>
                                <div class="dp-field-val" x-text="selected?.city || '—'"></div>
                            </div>
                            <div class="dp-field">
                                <div class="dp-field-label">{{ __('messages.credit_limit') }}</div>
                                <div class="dp-field-val mono"
                                    x-text="selected?.credit_limit ? 'Af ' + fmt(selected.credit_limit) : '{{ __('messages.none') }}'">
                                </div>
                            </div>
                            <div class="dp-field full">
                                <div class="dp-field-label">{{ __('messages.notes') }}</div>
                                <div class="dp-field-val" style="font-size:12px" x-text="selected?.notes || '—'"></div>
                            </div>
                        </div>
                    </div>
                    

                    {{-- Purchase summary --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-receipt"></i>
                            {{ __('messages.purchase_summary') }}</div>
                        <div class="dp-grid">
                            <div class="dp-field">
                                <div class="dp-field-label">{{ __('messages.total_spent') }}</div>
                                <div class="dp-field-val mono" style="color:var(--blue)"
                                    x-text="'Af ' + fmt(selected?.total_purchases || 0)"></div>
                            </div>
                            <div class="dp-field">
                                <div class="dp-field-label">{{ __('messages.transactions') }}</div>
                                <div class="dp-field-val mono" x-text="selected?.sale_count || 0"></div>
                            </div>
                            <div class="dp-field full">
                                <div class="dp-field-label">{{ __('messages.last_purchase') }}</div>
                                <div class="dp-field-val"
                                    x-text="selected?.last_sale_at ? relativeTime(selected.last_sale_at) : '{{ __('messages.never') }}'">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recent transactions --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-clock-rotate-left"></i>
                            {{ __('messages.recent_sales') }}</div>
                        <div x-show="detailLoading"
                            style="text-align:center;padding:1rem;color:var(--ink3);font-size:12px">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <div x-show="!detailLoading">
                            <div x-show="recentSales.length === 0"
                                style="text-align:center;padding:1rem;color:var(--ink4);font-size:12px">
                                {{ __('messages.no_sales_recorded_yet') }}
                            </div>
                            <template x-for="s in recentSales" :key="s.id">
                                <div class="mini-txn">
                                    <div class="mt-left">
                                        <div class="mt-id" x-text="s.local_id"></div>
                                        <div class="mt-date" x-text="s.created_at"></div>
                                    </div>
                                    <div class="mt-right">
                                        <div class="mt-amount" x-text="'Af ' + fmt(s.total_amount)"></div>
                                        <div class="mt-method">
                                            <span class="pill"
                                                :class="s.payment_method === 'loan' ? 'pill-amber' : 'pill-green'"
                                                x-text="s.payment_method"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>

                <div class="dp-foot">
                    <button type="button" class="btn btn-ghost" style="flex:1" @click="openEdit(selected)">
                        <i class="fas fa-pen"></i> {{ __('messages.edit') }}
                    </button>
                    <button type="button" class="btn btn-danger" @click="toggleActive(selected)">
                        <i class="fas fa-power-off"></i>
                        <span
                            x-text="selected?.is_active ? '{{ __('messages.deactivate') }}' : '{{ __('messages.activate') }}'"></span>
                    </button>
                </div>
            </div>

        </div>{{-- /cu-main --}}

        {{-- ═══════════════════════════════════════
     MODAL: ADD / EDIT CUSTOMER
═══════════════════════════════════════ --}}
        <div class="modal-overlay" x-show="showEditModal" x-cloak @click.self="showEditModal=false">
            <div class="modal-card modal-md">
                <div class="modal-head">
                    <div class="modal-title"
                        x-text="editingCustomer ? '{{ __('messages.edit_customer') }}' : '{{ __('messages.new_customer') }}'">
                    </div>
                    <button class="modal-close" @click="showEditModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-grid form-2">
                        <div>
                            <label class="field-label">{{ __('messages.full_name') }} <span
                                    class="field-req">*</span></label>
                            <input type="text" class="field-input" x-model="cf.name"
                                placeholder="{{ __('messages.customer_full_name') }}">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.primary_phone') }} <span
                                    class="field-req">*</span></label>
                            <input type="text" class="field-input" x-model="cf.phone"
                                placeholder="{{ __('messages.phone_number_format') }}">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.secondary_phone') }}</label>
                            <input type="text" class="field-input" x-model="cf.phone_secondary"
                                placeholder="{{ __('messages.optional') }}">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.city') }}</label>
                            <input type="text" class="field-input" x-model="cf.city"
                                placeholder="{{ __('messages.kabul_kandahar') }}">
                        </div>
                        <div style="grid-column:span 2">
                            <label class="field-label">{{ __('messages.address') }}</label>
                            <input type="text" class="field-input" x-model="cf.address"
                                placeholder="{{ __('messages.street_address') }}">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.credit_limit_af') }}</label>
                            <input type="number" class="field-input" x-model.number="cf.credit_limit"
                                placeholder="0 = {{ __('messages.unlimited') }}" min="0">
                            <div class="field-hint">{{ __('messages.max_loan_amount_allowed') }}</div>
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.status') }}</label>
                            <select class="field-input" x-model="cf.is_active">
                                <option :value="true">{{ __('messages.active') }}</option>
                                <option :value="false">{{ __('messages.inactive') }}</option>
                            </select>
                        </div>
                        <div style="grid-column:span 2">
                            <label class="field-label">{{ __('messages.notes') }}</label>
                            <textarea class="field-input" x-model="cf.notes" placeholder="{{ __('messages.optional_notes_about_customer') }}"></textarea>
                        </div>
                    </div>
                    <div class="form-err" x-show="formError" x-text="formError" x-cloak></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost"
                        @click="showEditModal=false">{{ __('messages.cancel') }}</button>
                    <button type="button" class="btn btn-primary" @click="saveCustomer()" :disabled="saving">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span
                            x-text="saving ? '{{ __('messages.saving') }}' : (editingCustomer ? '{{ __('messages.update_customer') }}' : '{{ __('messages.add_customer') }}')"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════
     MODAL: RECORD LOAN PAYMENT
═══════════════════════════════════════ --}}
        <div class="modal-overlay" x-show="showPaymentModal" x-cloak @click.self="showPaymentModal=false">
            <div class="modal-card modal-sm">
                <div class="modal-head">
                    <div class="modal-title">{{ __('messages.record_loan_payment') }}</div>
                    <button class="modal-close" @click="showPaymentModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">

                    {{-- Loan summary --}}
                    <div class="loan-detail-card">
                        <div class="ldc-row">
                            <span>{{ __('messages.customer') }}</span>
                            <span style="font-weight:600" x-text="paymentTarget?.name"></span>
                        </div>
                        <div class="ldc-row">
                            <span>{{ __('messages.original_loan') }}</span>
                            <span style="font-family:var(--mono)"
                                x-text="'Af ' + fmt(activeLoan?.original_amount || 0)"></span>
                        </div>
                        <div class="ldc-row">
                            <span>{{ __('messages.already_paid') }}</span>
                            <span style="font-family:var(--mono);color:var(--green)"
                                x-text="'Af ' + fmt(activeLoan?.amount_paid || 0)"></span>
                        </div>
                        <div class="ldc-row main">
                            <span>{{ __('messages.remaining') }}</span>
                            <span x-text="'Af ' + fmt(activeLoan?.remaining_balance || 0)"></span>
                        </div>
                    </div>

                    {{-- Payment amount --}}
                    <div style="margin-bottom:.9rem">
                        <label class="field-label">{{ __('messages.payment_amount_af') }} <span
                                class="field-req">*</span></label>
                        <input type="number" class="field-input" x-model.number="pf.amount"
                            :max="activeLoan?.remaining_balance" placeholder="0" min="0"
                            style="font-family:var(--mono);font-size:16px">
                        <div class="field-hint">
                            {{ __('messages.remaining_after_payment') }}:
                            <strong style="font-family:var(--mono);color:var(--red)"
                                x-text="'Af ' + fmt(Math.max(0,(activeLoan?.remaining_balance||0) - (pf.amount||0)))">
                            </strong>
                        </div>
                    </div>

                    <div style="margin-bottom:.9rem">
                        <label class="field-label">{{ __('messages.notes') }}</label>
                        <textarea class="field-input" x-model="pf.notes" rows="2"
                            placeholder="{{ __('messages.optional_payment_note') }}"></textarea>
                    </div>

                    {{-- Payment history --}}
                    <div class="payment-history" x-show="loanPayments.length > 0" x-cloak>
                        <div
                            style="font-size:10px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px">
                            {{ __('messages.payment_history') }}
                        </div>
                        <template x-for="p in loanPayments" :key="p.id">
                            <div class="ph-item">
                                <div>
                                    <div style="font-family:var(--mono);font-size:11px;color:var(--ink3)"
                                        x-text="p.receipt_number"></div>
                                    <div style="font-size:11px;color:var(--ink3)" x-text="p.created_at"></div>
                                </div>
                                <span class="ph-amount" x-text="'Af ' + fmt(p.amount)"></span>
                            </div>
                        </template>
                    </div>

                    <div class="form-err" x-show="formError" x-text="formError" x-cloak></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost"
                        @click="showPaymentModal=false">{{ __('messages.cancel') }}</button>
                    <button type="button" class="btn btn-primary" @click="savePayment()" :disabled="saving">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span
                            x-text="saving ? '{{ __('messages.saving') }}' : '{{ __('messages.record_payment') }}'"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- /cu --}}
@endsection

@push('scripts')
    <script>
        function customersPage() {
            return {
                /* list */
                customers: [],
                pagination: {},
                loading: true,
                search: '',
                filterLoan: '',
                filterCity: '',
                tab: 'all',
                sortCol: 'name',
                sortDir: 'asc',
                currentPage: 1,

                /* detail panel */
                selected: null,
                recentSales: [],
                detailLoading: false,

                /* edit modal */
                showEditModal: false,
                editingCustomer: null,
                cf: {},
                formError: '',
                saving: false,

                /* payment modal */
                showPaymentModal: false,
                paymentTarget: null,
                activeLoan: null,
                loanPayments: [],
                pf: {
                    amount: 0,
                    notes: ''
                },

                /* urls */
                urls: {
                    list: '{{ route('pos.customers.index') }}',
                    store: '{{ route('pos.customers.store') }}',
                    payment: '{{ route('pos.customers.payment') }}',
                    detail: '{{ url('pos/customers') }}',
                    toggle: '{{ url('pos/customers') }}',
                    export: '{{ route('pos.customers.export') }}',
                    csrf: document.querySelector('meta[name=csrf-token]').content,
                },

                /* ── init ── */
                async init() {
                    await this.loadCustomers();
                },

                /* ── load list ── */
                async loadCustomers() {
                    this.loading = true;
                    try {
                        const p = new URLSearchParams({
                            q: this.search,
                            loan: this.filterLoan,
                            city: this.filterCity,
                            tab: this.tab,
                            sort: this.sortCol,
                            dir: this.sortDir,
                            page: this.currentPage,
                        });
                        const r = await fetch(this.urls.list + '?' + p, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const d = await r.json();
                        this.customers = (d.data).slice(0, 10);
                        this.pagination = d.meta;
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.loading = false;
                    }
                },

                sort(col) {
                    if (this.sortCol === col) this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                    else {
                        this.sortCol = col;
                        this.sortDir = 'asc';
                    }
                    this.loadCustomers();
                },

                goPage(p) {
                    if (p < 1 || p > this.pagination.last_page) return;
                    this.currentPage = p;
                    this.loadCustomers();
                },

                /* ── detail panel ── */
                async openDetail(c) {
                    this.selected = c;
                    this.recentSales = [];
                    this.detailLoading = true;
                    try {
                        const r = await fetch(`${this.urls.detail}/${c.id}/detail`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const d = await r.json();
                        this.recentSales = d.recent_sales;
                        // merge detail data into selected
                        this.selected = {
                            ...this.selected,
                            ...d.customer
                        };
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.detailLoading = false;
                    }
                },
                relativeTime(date, locale = 'en') {
                    const map = {
                        dr: 'fa',
                        ps: 'ps',
                        en: 'en',
                    };

                    return dayjs(date)
                        .locale(map[locale] || 'en')
                        .fromNow();
                },

                /* ── edit ── */
                openEdit(c) {
                    this.editingCustomer = c;
                    this.cf = {
                        ...c
                    };
                    this.formError = '';
                    this.showEditModal = true;
                },

                resetCf() {
                    this.cf = {
                        name: '',
                        phone: '',
                        phone_secondary: '',
                        address: '',
                        city: '',
                        notes: '',
                        credit_limit: null,
                        is_active: true
                    };
                    this.formError = '';
                },

                async saveCustomer() {
                    if (!this.cf.name?.trim()) {
                        this.formError = 'Name is required.';
                        return;
                    }
                    if (!this.cf.phone?.trim()) {
                        this.formError = 'Phone is required.';
                        return;
                    }
                    this.saving = true;
                    this.formError = '';
                    try {
                        const r = await fetch(this.urls.store, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                ...this.cf,
                                customer_id: this.editingCustomer?.id
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showEditModal = false;
                            this.loadCustomers();
                            if (this.selected?.id === this.editingCustomer?.id) {
                                this.selected = {
                                    ...this.selected,
                                    ...d.customer
                                };
                            }
                        } else {
                            this.formError = d.message ?? '{{ __('messages.failed_to_save') }}';
                        }
                    } catch (e) {
                        this.formError = '{{ __('messages.error_network') }}';
                    } finally {
                        this.saving = false;
                    }
                },

                /* ── toggle active ── */ 
                async toggleActive(c) {
                    if (!confirm(`${c.is_active ? '{{ __('messages.deactivate') }}' : '{{ __('messages.activate') }}'} ${c.name}?`)) return;
                    await fetch(`${this.urls.toggle}/${c.id}/toggle`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.urls.csrf
                        }
                    });
                    this.loadCustomers();
                    if (this.selected?.id === c.id) this.selected = null;
                },

                /* ── payment ── */
                async openPayment(c) {
                    this.paymentTarget = c;
                    this.activeLoan = null;
                    this.loanPayments = [];
                    this.pf = {
                        amount: 0,
                        notes: ''
                    };
                    this.formError = '';
                    this.showPaymentModal = true;

                    try {
                        const r = await fetch(`${this.urls.detail}/${c.id}/loan`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const d = await r.json();
                        this.activeLoan = d.loan;
                        this.loanPayments = d.payments;
                    } catch (e) {
                        console.error(e);
                    }
                },

                async savePayment() {
                    if (!this.pf.amount || this.pf.amount <= 0) {
                        this.formError = '{{ __('messages.enter_a_valid_amount') }}';
                        return;
                    }
                    if (this.pf.amount > this.activeLoan?.remaining_balance) {
                        this.formError = '{{ __('messages.amount_exceeds_remaining_balance') }}';
                        return;
                    }
                    this.saving = true;
                    this.formError = '';
                    try {
                        const r = await fetch(this.urls.payment, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                loan_id: this.activeLoan.id,
                                ...this.pf
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showPaymentModal = false;
                            this.loadCustomers();
                            if (this.selected) await this.openDetail(this.selected);
                        } else {
                            this.formError = d.message ?? '{{ __('messages.payment_failed') }}.';
                        }
                    } catch (e) {
                        this.formError = '{{ __('messages.error_network') }}';
                    } finally {
                        this.saving = false;
                    }
                },

                /* ── export ── */
                exportCsv() {
                    window.location.href = this.urls.export+'?q=' + this.search + '&loan=' + this.filterLoan + '&tab=' +
                        this.tab;
                },

                /* ── helpers ── */
                initials(name) {
                    if (!name) return '?';
                    return name.trim().split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
                },

                avatarColor(name) {
                    const colors = ['#3563e9', '#7c3aed', '#0891b2', '#16a34a', '#d97706', '#dc2626', '#4f46e5', '#0f766e'];
                    if (!name) return colors[0];
                    const i = name.charCodeAt(0) % colors.length;
                    return colors[i];
                },

                fmt(n) {
                    return Number(n || 0).toLocaleString('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                }
            };
        }
    </script>
@endpush
