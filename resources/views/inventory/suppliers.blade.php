@extends('layouts.app')

@push('styles')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/pages/suppliers.css'])
    @endif
@endpush

@section('content')
    <div class="sp" x-data="suppliersPage()" x-init="init()">

        {{-- ════ TOPBAR ════ --}}
        <div class="sp-top">
            <div class="sp-title">Afghan <em>POS</em> — {{ __('messages.suppliers_and_purchases') }}</div>
            <div class="top-r">
                <button class="btn btn-ghost" x-show="activeTab==='suppliers'" @click="openSupplierModal(null)">
                    <i class="fas fa-plus"></i> {{ __('messages.add_supplier') }}
                </button>
            </div>
        </div>

        {{-- ════ STATS ════ --}}
        <div class="stat-strip">
            <div class="stat-tile" style="--ac:var(--blue)">
                <div class="st-label">{{ __('messages.total_suppliers') }} <span style="color:var(--blue)"><i
                            class="fas fa-truck"></i></span>
                </div>
                <div class="st-val">{{ $stats['total'] ?? 0 }}</div>
                <div class="st-sub">{{ $stats['active'] ?? 0 }} {{ __('messages.active') }}</div>
            </div>
            <div class="stat-tile" style="--ac:var(--amber)">
                <div class="st-label">{{ __('messages.open_pos') }} <span style="color:var(--amber)"><i
                            class="fas fa-file-invoice"></i></span>
                </div>
                <div class="st-val" style="color:var(--amber)">{{ $stats['open_pos'] ?? 0 }}</div>
                <div class="st-sub">{{ __('messages.awaiting_delivery') }}</div>
            </div>
            <div class="stat-tile" style="--ac:var(--red)">
                <div class="st-label">{{ __('messages.unpaid_balance') }} <span style="color:var(--red)"><i
                            class="fas fa-coins"></i></span>
                </div>
                <div class="st-val" style="font-size:18px;color:var(--red)">{{ __('messages.af') }}
                    {{ number_format($stats['unpaid'] ?? 0) }}
                </div>
                <div class="st-sub">{{ __('messages.owed_to_suppliers') }}</div>
            </div>
            <div class="stat-tile" style="--ac:var(--green)">
                <div class="st-label">{{ __('messages.total_purchased') }} <span style="color:var(--green)"><i
                            class="fas fa-chart-line"></i></span></div>
                <div class="st-val" style="font-size:18px">{{ __('messages.af') }}
                    {{ number_format($stats['total_purchased'] ?? 0) }}</div>
                <div class="st-sub">{{ __('messages.lifetime_purchase_value') }}</div>
            </div>
        </div>

        {{-- ════ TABS ════ --}}
        <div class="sp-tabs">
            <button type="button" class="sp-tab" :class="activeTab === 'suppliers' ? 'active' : ''"
                @click="switchTab('suppliers')">
                <i class="fas fa-truck"></i> {{ __('messages.suppliers') }}
            </button>
            <button type="button" class="sp-tab" :class="activeTab === 'purchases' ? 'active' : ''"
                @click="switchTab('purchases')">
                <i class="fas fa-file-invoice"></i> {{ __('messages.purchase_orders') }}
            </button>
        </div>

        {{-- ══════════════════════════════════════════
     TAB: SUPPLIERS
══════════════════════════════════════════ --}}
        <div class="sp-panel" :class="activeTab === 'suppliers' ? 'active' : ''">

            <div class="sp-toolbar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input class="sp-search" type="text" x-model="supSearch" @input.debounce.350ms="loadSuppliers()"
                        placeholder="{{ __('messages.search_placeholder') }}">
                </div>
                <select class="f-sel" x-model="supFilterStatus" @change="loadSuppliers()">
                    <option value="">{{ __('messages.all_suppliers') }}</option>
                    <option value="active">{{ __('messages.active') }}</option>
                    <option value="inactive">{{ __('messages.inactive') }}</option>
                </select>
                <select class="f-sel" x-model="supFilterCity" @change="loadSuppliers()">
                    <option value="">{{ __('messages.all_cities') }}</option>
                    @foreach ($cities ?? [] as $city)
                        <option value="{{ $city }}">{{ $city }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-primary" style="margin-left:auto" @click="openSupplierModal(null)">
                    <i class="fas fa-plus"></i> {{ __('messages.add_supplier') }}
                </button>
            </div>

            <div class="sp-main" :class="selectedSupplier ? 'panel-open' : ''">

                {{-- TABLE --}}
                <div class="table-card">
                    <div class="loading-row" x-show="supLoading"><i class="fas fa-spinner fa-spin"
                            style="font-size:18px"></i></div>
                    <div x-show="!supLoading">
                        <div class="empty-state" x-show="suppliers.length===0">
                            <i class="fas fa-truck-slash"></i>
                            <p>{{ __('messages.no_suppliers_found') }}<br>{{ __('messages.add_first_supplier') }}</p>
                        </div>
                        <table
    class="sp-table {{ in_array(app()->getLocale(), ['ps', 'dr']) ? 'rtl-table' : '' }}"
    x-show="suppliers.length > 0">
                            <thead>
                                <tr>
                                    <th @click="supSort('name')">{{ __('messages.supplier') }} <i class="fas fa-sort"></i>
                                    </th>
                                    <th>{{ __('messages.contact_person') }}</th>
                                    <th>{{ __('messages.phone') }}</th>
                                    <th>{{ __('messages.city') }}</th>
                                    <th class="cell-right" @click="supSort('total_purchases')">
                                        {{ __('messages.total_purchased') }} <i class="fas fa-sort"></i></th>
                                    <th class="cell-right">{{ __('messages.open_pos') }}</th>
                                    <th>{{ __('messages.payment_terms') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="s in suppliers" :key="s.id">
                                    <tr :class="selectedSupplier?.id === s.id ? 'selected' : ''"
                                        @click="openSupplierDetail(s)">
                                        <td>
                                            <div class="sup-cell">
                                                <div class="sup-av" :style="`background:${avatarColor(s.name)}`"
                                                    x-text="initials(s.name)"></div>
                                                <div>
                                                    <div class="sup-name" x-text="s.name"></div>
                                                    <div class="sup-contact" x-text="s.email || '—'"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="font-size:12.5px;color:var(--ink2)" x-text="s.contact_person||'—'">
                                        </td>
                                        <td class="cell-mono" x-text="s.phone"></td>
                                        <td style="font-size:12.5px;color:var(--ink2)" x-text="s.city||'—'"></td>
                                        <td class="cell-right">
                                            <span class="cell-mono" style="font-weight:600"
                                                x-text="'{{ __('messages.af') }} ' + fmt(s.total_purchases||0)"></span>
                                        </td>
                                        <td class="cell-right">
                                            <span class="pill" :class="s.open_pos > 0 ? 'pill-amber' : 'pill-gray'"
                                                x-text="s.open_pos||0"></span>
                                        </td>
                                        <td style="font-size:12px;color:var(--ink3)" x-text="s.payment_terms||'—'"></td>
                                        <td>
                                            <span class="pill" :class="s.is_active ? 'pill-green' : 'pill-gray'"
                                                x-text="s.is_active?'{{ __('messages.active') }}':'{{ __('messages.inactive') }}'"></span>
                                        </td>
                                        <td @click.stop>
                                            <div class="row-acts">
                                                <button type="button" class="btn btn-ghost btn-sm"
                                                    @click="openSupplierModal(s)">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    @click="toggleSupplier(s)">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div class="pag-row" x-show="supPagination.last_page>1">
                            <div class="pag-info">{{ __('messages.showing') }} <span
                                    x-text="supPagination.from"></span>–<span x-text="supPagination.to"></span>
                                {{ __('messages.of') }} <span x-text="supPagination.total"></span></div>
                            <div class="pag-btns">
                                <button class="pag-btn" @click="supGoPage(supPagination.current_page-1)"
                                    :disabled="supPagination.current_page === 1"><i
                                        class="fas fa-chevron-left"></i></button>
                                <template x-for="p in supPagination.last_page" :key="p">
                                    <button class="pag-btn" :class="p === supPagination.current_page ? 'active' : ''"
                                        @click="supGoPage(p)" x-text="p"></button>
                                </template>
                                <button class="pag-btn" @click="supGoPage(supPagination.current_page+1)"
                                    :disabled="supPagination.current_page === supPagination.last_page"><i
                                        class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SUPPLIER DETAIL PANEL --}}
                <div class="detail-panel" x-show="selectedSupplier" x-cloak>
                    <div class="dp-head">
                        <span class="dp-head-label">{{ __('messages.supplier_detail') }}</span>
                        <button class="dp-close" @click="selectedSupplier=null"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="dp-body">

                        {{-- Hero --}}
                        <div class="sup-hero">
                            <div class="hero-av" :style="`background:${avatarColor(selectedSupplier?.name)}`"
                                x-text="initials(selectedSupplier?.name)"></div>
                            <div class="hero-name" x-text="selectedSupplier?.name"></div>
                            <div class="hero-meta">
                                <span x-show="selectedSupplier?.city"><i class="fas fa-location-dot"></i> <span
                                        x-text="selectedSupplier?.city"></span></span>
                                <span class="pill" :class="selectedSupplier?.is_active ? 'pill-green' : 'pill-gray'"
                                    x-text="selectedSupplier?.is_active?'{{ __('messages.active') }}':'{{ __('messages.inactive') }}'"></span>
                            </div>
                        </div>

                        {{-- KPI strip --}}
                        <div class="sup-kpi-strip">
                            <div class="sk-item">
                                <div class="sk-label">{{ __('messages.total_purchased') }}</div>
                                <div class="sk-val" style="color:var(--blue)"
                                    x-text="'{{ __('messages.af') }} ' + fmt(selectedSupplier?.total_purchases||0)"></div>
                            </div>
                            <div class="sk-item">
                                <div class="sk-label">{{ __('messages.open_pos') }}</div>
                                <div class="sk-val" style="color:var(--amber)" x-text="selectedSupplier?.open_pos||0">
                                </div>
                            </div>
                            <div class="sk-item">
                                <div class="sk-label">{{ __('messages.unpaid') }}</div>
                                <div class="sk-val" style="color:var(--red)"
                                    x-text="'{{ __('messages.af') }} ' + fmt(selectedSupplier?.unpaid||0)"></div>
                            </div>
                        </div>

                        {{-- Contact info --}}
                        <div class="dp-section">
                            <div class="dp-section-title"><i class="fas fa-address-card"></i>
                                {{ __('messages.contact_info') }}</div>
                            <div class="info-grid">
                                <div class="info-field">
                                    <div class="if-label">{{ __('messages.contact_person') }}</div>
                                    <div class="if-val" x-text="selectedSupplier?.contact_person||'—'"></div>
                                </div>
                                <div class="info-field">
                                    <div class="if-label">{{ __('messages.phone') }}</div>
                                    <div class="if-val mono" x-text="selectedSupplier?.phone"></div>
                                </div>
                                <div class="info-field" x-show="selectedSupplier?.phone_secondary">
                                    <div class="if-label">{{ __('messages.phone_2') }}</div>
                                    <div class="if-val mono" x-text="selectedSupplier?.phone_secondary"></div>
                                </div>
                                <div class="info-field" x-show="selectedSupplier?.email">
                                    <div class="if-label">{{ __('messages.email') }}</div>
                                    <div class="if-val" x-text="selectedSupplier?.email"></div>
                                </div>
                                <div class="info-field full" x-show="selectedSupplier?.address">
                                    <div class="if-label">{{ __('messages.address') }}</div>
                                    <div class="if-val" x-text="selectedSupplier?.address"></div>
                                </div>
                                <div class="info-field">
                                    <div class="if-label">{{ __('messages.payment_terms') }}</div>
                                    <div class="if-val" x-text="selectedSupplier?.payment_terms||'—'"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="dp-section" x-show="selectedSupplier?.notes">
                            <div class="dp-section-title"><i class="fas fa-pen"></i> {{ __('messages.notes') }}</div>
                            <div style="font-size:12.5px;color:var(--ink2);line-height:1.6"
                                x-text="selectedSupplier?.notes"></div>
                        </div>

                        {{-- Purchase history --}}
                        <div class="dp-section">
                            <div class="dp-section-title"><i class="fas fa-clock-rotate-left"></i>
                                {{ __('messages.recent_purchase_orders') }}
                            </div>
                            <div x-show="supDetailLoading"
                                style="text-align:center;padding:1rem;color:var(--ink3);font-size:12px">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                            <div x-show="!supDetailLoading">
                                <div x-show="supplierPOs.length===0"
                                    style="text-align:center;padding:1rem;color:var(--ink4);font-size:12px">
                                    {{ __('messages.no_purchase_orders_yet') }}
                                </div>
                                <template x-for="po in supplierPOs" :key="po.id">
                                    <div class="mini-po">
                                        <div>
                                            <div class="mpo-id" x-text="po.local_id"></div>
                                            <div class="mpo-date" x-text="po.purchase_date"></div>
                                        </div>
                                        <div style="text-align:right">
                                            <div class="mpo-amt"
                                                x-text="'{{ __('messages.af') }} ' + fmt(po.total_cost)"></div>
                                            <span class="pill"
                                                :class="{
                                                    'pill-amber': po.status==='ordered',
                                                    'pill-teal': po.status==='partial',
                                                    'pill-green': po.status==='received',
                                                    'pill-gray': po.status==='cancelled',
                                                }"
                                                x-text="po.status"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="dp-foot">
                        <button type="button" class="btn btn-ghost" style="flex:1"
                            @click="openSupplierModal(selectedSupplier)">
                            <i class="fas fa-pen"></i> {{ __('messages.edit') }}
                        </button>
                        <button type="button" class="btn btn-danger" @click="toggleSupplier(selectedSupplier)">
                            <i class="fas fa-power-off"></i>
                            <span
                                x-text="selectedSupplier?.is_active?'{{ __('messages.deactivate') }}':'{{ __('messages.activate') }}'"></span>
                        </button>
                    </div>
                </div>

            </div>{{-- /sp-main suppliers --}}
        </div>

        {{-- ══════════════════════════════════════════
     TAB: PURCHASE ORDERS
══════════════════════════════════════════ --}}
        <div class="sp-panel" :class="activeTab === 'purchases' ? 'active' : ''">

            <div class="sp-toolbar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input class="sp-search" type="text" x-model="poSearch" @input.debounce.350ms="loadPOs()"
                        placeholder="{{ __('messages.po_search_placeholder') }}">
                </div>
                <select class="f-sel" x-model="poFilterStatus" @change="loadPOs()">
                    <option value="">{{ __('messages.all_statuses') }}</option>
                    <option value="ordered">{{ __('messages.ordered') }}</option>
                    <option value="partial">{{ __('messages.partial') }}</option>
                    <option value="received">{{ __('messages.received') }}</option>
                    <option value="cancelled">{{ __('messages.cancelled') }}</option>
                </select>
                <select class="f-sel" x-model="poFilterPayment" @change="loadPOs()">
                    <option value="">{{ __('messages.all_payments') }}</option>
                    <option value="unpaid">{{ __('messages.unpaid') }}</option>
                    <option value="partial">{{ __('messages.partial') }}</option>
                    <option value="paid">{{ __('messages.paid') }}</option>
                </select>
            </div>

            <div class="sp-main" :class="selectedPO ? 'panel-open' : ''">

                {{-- PO TABLE --}}
                <div class="table-card">
                    <div class="loading-row" x-show="poLoading"><i class="fas fa-spinner fa-spin"
                            style="font-size:18px"></i></div>
                    <div x-show="!poLoading">
                        <div class="empty-state" x-show="purchaseOrders.length===0">
                            <i class="fas fa-file-circle-xmark"></i>
                            <p>{{ __('messages.no_purchase_orders_found') }}</p>
                        </div>
                        <table 
    class="sp-table"
    :class="{ 'rtl-table': ['ps', 'dr'].includes('{{ app()->getLocale() }}') }"
    x-show="purchaseOrders.length > 0">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.po_id') }}</th>
                                    <th>{{ __('messages.supplier') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.delivery') }}</th>
                                    <th class="cell-right">{{ __('messages.total_cost') }}</th>
                                    <th class="cell-right">{{ __('messages.paid') }}</th>
                                    <th>{{ __('messages.received') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.payment') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="po in purchaseOrders" :key="po.id">
                                    <tr :class="selectedPO?.id === po.id ? 'selected' : ''" @click="openPODetail(po)">
                                        <td><span class="cell-mono" style="color:var(--blue);font-weight:500"
                                                x-text="po.local_id"></span></td>
                                        <td>
                                            <div style="font-weight:600;font-size:12.5px" x-text="po.supplier"></div>
                                            <div style="font-size:11px;color:var(--ink3)" x-show="po.reference_number"
                                                x-text="'{{ __('messages.ref') }}: ' + po.reference_number"></div>
                                        </td>
                                        <td class="cell-mono" style="font-size:11.5px" x-text="po.purchase_date"></td>
                                        <td class="cell-mono" style="font-size:11.5px;color:var(--ink3)"
                                            x-text="po.delivery_date||'—'"></td>
                                        <td class="cell-right">
                                            <span class="cell-mono" style="font-weight:600"
                                                x-text="'{{ __('messages.af') }} ' + fmt(po.total_cost)"></span>
                                        </td>
                                        <td class="cell-right">
                                            <span class="cell-mono" style="color:var(--green)"
                                                x-text="'{{ __('messages.af') }} ' + fmt(po.amount_paid)"></span>
                                        </td>
                                        <td>
                                            <div class="po-progress">
                                                <div class="po-prog-bar">
                                                    <div class="po-prog-fill" :style="`width:${po.received_pct}%`"></div>
                                                </div>
                                                <span class="po-prog-val" x-text="po.received_pct + '%'"></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="pill"
                                                :class="{
                                                    'pill-navy': po.status==='ordered',
                                                    'pill-teal': po.status==='partial',
                                                    'pill-green': po.status==='received',
                                                    'pill-gray': po.status==='cancelled',
                                                }"
                                                x-text="po.status"></span>
                                        </td>
                                        <td>
                                            <span class="pill"
                                                :class="{
                                                    'pill-red': po.payment_status==='unpaid',
                                                    'pill-amber': po.payment_status==='partial',
                                                    'pill-green': po.payment_status==='paid',
                                                }"
                                                x-text="po.payment_status"></span>
                                        </td>
                                        <td @click.stop>
                                            <div class="row-acts">
                                                <button type="button" class="btn btn-teal btn-sm"
                                                    x-show="po.status!=='received' && po.status!=='cancelled'"
                                                    @click="openPODetail(po)" title="{{ __('messages.receive_stock') }}">
                                                    <i class="fas fa-boxes-stacked"></i>
                                                </button>
                                                <button type="button" class="btn btn-teal btn-sm"
                                                    x-show="po.payment_status!=='paid' && po.status!=='cancelled'"
                                                    @click="openPaymentModal(po)"
                                                    title="{{ __('messages.record_payment') }}">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    x-show="po.status==='ordered'" @click="cancelPO(po)"
                                                    title="{{ __('messages.cancel_po') }}">
                                                    <i class="fas fa-times"></i>
                                                </button>

                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div class="pag-row" x-show="poPagination.last_page>1">
                            <div class="pag-info">{{ __('messages.showing') }} <span
                                    x-text="poPagination.from"></span>–<span x-text="poPagination.to"></span>
                                {{ __('messages.of') }} <span x-text="poPagination.total"></span></div>
                            <div class="pag-btns">
                                <button class="pag-btn" @click="poGoPage(poPagination.current_page-1)"
                                    :disabled="poPagination.current_page === 1"><i
                                        class="fas fa-chevron-left"></i></button>
                                <template x-for="p in poPagination.last_page" :key="p">
                                    <button class="pag-btn" :class="p === poPagination.current_page ? 'active' : ''"
                                        @click="poGoPage(p)" x-text="p"></button>
                                </template>
                                <button class="pag-btn" @click="poGoPage(poPagination.current_page+1)"
                                    :disabled="poPagination.current_page === poPagination.last_page"><i
                                        class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PO DETAIL + RECEIVE PANEL --}}
                <div class="detail-panel" x-show="selectedPO" x-cloak>
                    <div class="dp-head">
                        <span class="dp-head-label">{{ __('messages.purchase_order') }}</span>
                        <button class="dp-close" @click="selectedPO=null"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="dp-body">

                        {{-- PO Header strip --}}
                        <div style="background:linear-gradient(135deg,#1e2d4f 0%,#172240 100%);padding:1.25rem;color:#fff">
                            <div style="font-family:var(--mono);font-size:12px;color:rgba(255,255,255,.5);margin-bottom:3px"
                                x-text="selectedPO?.local_id"></div>
                            <div style="font-family:var(--display);font-size:20px;font-weight:500;margin-bottom:8px"
                                x-text="selectedPO?.supplier"></div>
                            <div style="display:flex;gap:1rem;flex-wrap:wrap">
                                <div style="font-size:11px;color:rgba(255,255,255,.5)">
                                    <i class="fas fa-calendar"></i> <span x-text="selectedPO?.purchase_date"></span>
                                </div>
                                <div style="font-size:11px;color:rgba(255,255,255,.5)"
                                    x-show="selectedPO?.reference_number">
                                    <i class="fas fa-hashtag"></i> <span x-text="selectedPO?.reference_number"></span>
                                </div>
                            </div>
                            <div style="display:flex;gap:8px;margin-top:10px">
                                <span class="pill"
                                    :class="{
                                        'pill-navy': selectedPO?.status==='ordered',
                                        'pill-teal': selectedPO?.status==='partial',
                                        'pill-green': selectedPO?.status==='received',
                                        'pill-gray': selectedPO?.status==='cancelled',
                                    }"
                                    x-text="selectedPO?.status"></span>
                                <span class="pill"
                                    :class="{
                                        'pill-red': selectedPO?.payment_status==='unpaid',
                                        'pill-amber': selectedPO?.payment_status==='partial',
                                        'pill-green': selectedPO?.payment_status==='paid',
                                    }"
                                    x-text="selectedPO?.payment_status"></span>
                            </div>
                        </div>

                        {{-- Cost summary --}}
                        <div class="sup-kpi-strip">
                            <div class="sk-item">
                                <div class="sk-label">{{ __('messages.order_total') }}</div>
                                <div class="sk-val" x-text="'{{ __('messages.af') }} ' + fmt(selectedPO?.total_cost||0)">
                                </div>
                            </div>
                            <div class="sk-item">
                                <div class="sk-label">{{ __('messages.paid') }}</div>
                                <div class="sk-val" style="color:var(--green)"
                                    x-text="'{{ __('messages.af') }} ' + fmt(selectedPO?.amount_paid||0)"></div>
                            </div>
                            <div class="sk-item">
                                <div class="sk-label">{{ __('messages.balance') }}</div>
                                <div class="sk-val" style="color:var(--red)"
                                    x-text="'{{ __('messages.af') }} ' + fmt((selectedPO?.total_cost||0)-(selectedPO?.amount_paid||0))">
                                </div>
                            </div>
                        </div>

                        {{-- Receive stock section --}}
                        <div class="receive-section"
                            x-show="selectedPO?.status!=='received' && selectedPO?.status!=='cancelled'">
                            <div class="dp-section-title" style="margin-bottom:.75rem">
                                <i class="fas fa-boxes-stacked" style="color:var(--teal)"></i>
                                {{ __('messages.receive_stock') }}
                                <span
                                    style="font-size:10px;color:var(--ink3);font-weight:400;margin-left:auto">{{ __('messages.enter_qty_received') }}</span>
                            </div>
                            <div x-show="poItemsLoading"
                                style="text-align:center;padding:1rem;color:var(--ink3);font-size:12px">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                            <div x-show="!poItemsLoading">
                                <template x-for="item in poItems" :key="item.id">
                                    <div class="receive-item"
                                        :class="item.quantity_received >= item.quantity_ordered ? 'fully-received' : ''">
                                        <div class="ri-info">
                                            <div class="ri-name" x-text="item.product_name"></div>
                                            <div class="ri-detail"
                                                x-text="item.sku + ' · {{ __('messages.ordered_colon') }}: ' + item.quantity_ordered + ' · {{ __('messages.received_colon') }}: ' + item.quantity_received">
                                            </div>
                                        </div>
                                        <div class="ri-qty-input">
                                            <span class="ri-ordered"
                                                x-text="'{{ __('messages.max') }}: ' + (item.quantity_ordered - item.quantity_received)"></span>
                                            <input class="ri-input" type="number" x-model.number="item.receive_qty"
                                                :max="item.quantity_ordered - item.quantity_received" min="0"
                                                placeholder="0"
                                                :disabled="item.quantity_received >= item.quantity_ordered">
                                        </div>
                                    </div>
                                </template>
                                <button type="button" class="btn btn-teal" style="width:100%;margin-top:.5rem"
                                    @click="receiveStock()"
                                    :disabled="receiveSaving || poItems.every(i => i.quantity_received >= i.quantity_ordered)">
                                    <i class="fas fa-spinner fa-spin" x-show="receiveSaving"></i>
                                    <i class="fas fa-check" x-show="!receiveSaving"></i>
                                    <span
                                        x-text="receiveSaving?'{{ __('messages.receiving') }}':'{{ __('messages.receive_selected_stock') }}'"></span>
                                </button>
                                <div x-show="receiveError" x-cloak
                                    style="margin-top:6px;padding:8px 10px;background:var(--rdim);border:1px solid rgba(220,38,38,.2);border-radius:var(--rsm);font-size:12px;color:var(--red)"
                                    x-text="receiveError"></div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="dp-section" x-show="selectedPO?.notes">
                            <div class="dp-section-title"><i class="fas fa-pen"></i> {{ __('messages.notes') }}</div>
                            <div style="font-size:12.5px;color:var(--ink2);line-height:1.6" x-text="selectedPO?.notes">
                            </div>
                        </div>

                    </div>
                    <div class="dp-foot">
                        <button type="button" class="btn btn-danger" x-show="selectedPO?.status==='ordered'"
                            @click="cancelPO(selectedPO)">
                            <i class="fas fa-times"></i> {{ __('messages.cancel_po') }}
                        </button>
                    </div>
                </div>

            </div>{{-- /sp-main purchases --}}
        </div>

        {{-- ════ PURCHASE PAYMENT MODAL ════ --}}
        <div class="modal-overlay" x-show="showPaymentModal" x-cloak @click.self="showPaymentModal=false">
            <div class="modal-card modal-sm">
                <div class="modal-head">
                    <div class="modal-title">{{ __('messages.record_purchase_payment') }}</div>
                    <button class="modal-close" @click="showPaymentModal=false">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">

                    {{-- PO summary --}}
                    <div
                        style="padding:10px 14px;background:var(--s2);border:1px solid var(--border);border-radius:var(--rsm);margin-bottom:1rem">
                        <div
                            style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;color:var(--ink2)">
                            <span>{{ __('messages.purchase_order') }}</span>
                            <span style="font-family:var(--mono);font-weight:600" x-text="paymentPO?.local_id"></span>
                        </div>
                        <div
                            style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;color:var(--ink2)">
                            <span>{{ __('messages.supplier') }}</span>
                            <span style="font-weight:600" x-text="paymentPO?.supplier"></span>
                        </div>
                        <div
                            style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;color:var(--ink2)">
                            <span>{{ __('messages.total_cost') }}</span>
                            <span style="font-family:var(--mono)"
                                x-text="'{{ __('messages.af') }} ' + fmt(paymentPO?.total_cost||0)"></span>
                        </div>
                        <div
                            style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;color:var(--green)">
                            <span>{{ __('messages.already_paid') }}</span>
                            <span style="font-family:var(--mono);font-weight:600"
                                x-text="'{{ __('messages.af') }} ' + fmt(paymentPO?.amount_paid||0)"></span>
                        </div>
                        <div
                            style="display:flex;justify-content:space-between;font-size:13px;font-weight:700;color:var(--red);border-top:1px solid var(--border);padding-top:6px;margin-top:6px">
                            <span>{{ __('messages.remaining_balance') }}</span>
                            <span style="font-family:var(--mono)"
                                x-text="'{{ __('messages.af') }} ' + fmt((paymentPO?.total_cost||0) - (paymentPO?.amount_paid||0))"></span>
                        </div>
                    </div>

                    {{-- Amount input --}}
                    <div style="margin-bottom:.9rem">
                        <label class="field-label">{{ __('messages.payment_amount') }} <span
                                class="field-req">*</span></label>
                        <div style="position:relative">
                            <span
                                style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-family:var(--mono);font-size:14px;color:var(--ink3);pointer-events:none">{{ __('messages.af') }}</span>
                            <input type="number" class="field-input" x-model.number="paymentAmount"
                                :max="(paymentPO?.total_cost || 0) - (paymentPO?.amount_paid || 0)" min="0"
                                placeholder="0" style="padding-left:36px;font-family:var(--mono);font-size:16px">
                        </div>
                        <div style="font-size:11px;color:var(--ink3);margin-top:4px">
                            {{ __('messages.remaining_after_payment') }}:
                            <strong style="font-family:var(--mono);color:var(--red)"
                                x-text="'{{ __('messages.af') }} ' + fmt(Math.max(0, (paymentPO?.total_cost||0) - (paymentPO?.amount_paid||0) - (paymentAmount||0)))">
                            </strong>
                        </div>
                    </div>

                    {{-- Quick amount buttons --}}
                    <div style="display:flex;gap:6px;margin-bottom:.9rem;flex-wrap:wrap">
                        <button type="button" class="btn btn-ghost btn-sm"
                            @click="paymentAmount = (paymentPO?.total_cost||0) - (paymentPO?.amount_paid||0)">
                            {{ __('messages.pay_full_balance') }}
                        </button>
                        <button type="button" class="btn btn-ghost btn-sm"
                            @click="paymentAmount = Math.round(((paymentPO?.total_cost||0) - (paymentPO?.amount_paid||0)) / 2)">
                            {{ __('messages.pay_half') }}
                        </button>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="field-label">{{ __('messages.notes') }}</label>
                        <textarea class="field-input" x-model="paymentNote" rows="2"
                            placeholder="{{ __('messages.optional_payment_note') }}"></textarea>
                    </div>

                    <div x-show="poPaymentError" x-cloak
                        style="margin-top:.75rem;padding:9px 12px;background:var(--rdim);border:1px solid rgba(220,38,38,.2);border-radius:var(--rsm);font-size:12px;color:var(--red)"
                        x-text="poPaymentError"></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost"
                        @click="showPaymentModal=false">{{ __('messages.cancel') }}</button>
                    <button type="button" class="btn btn-primary" @click="savePOPayment()" :disabled="saving">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span
                            x-text="saving ? '{{ __('messages.saving') }}' : '{{ __('messages.record_payment') }}'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
     MODAL: ADD / EDIT SUPPLIER
══════════════════════════════════════════ --}}
        <div class="modal-overlay" x-show="showSupplierModal" x-cloak @click.self="showSupplierModal=false">
            <div class="modal-card modal-md">
                <div class="modal-head">
                    <div class="modal-title"
                        x-text="editingSupplier ? '{{ __('messages.edit_supplier') }}' : '{{ __('messages.new_supplier') }}'">
                    </div>
                    <button class="modal-close" @click="showSupplierModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">

                    <div class="form-section-title"><i class="fas fa-building"></i> {{ __('messages.supplier_info') }}
                    </div>
                    <div class="form-grid form-2" style="margin-bottom:1rem">
                        <div>
                            <label class="field-label">{{ __('messages.company_name') }} <span
                                    class="field-req">*</span></label>
                            <input type="text" class="field-input" x-model="sf.name"
                                placeholder="{{ __('messages.company_name') }}">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.contact_person') }}</label>
                            <input type="text" class="field-input" x-model="sf.contact_person"
                                placeholder="{{ __('messages.main_contact_name') }}">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.phone') }} <span
                                    class="field-req">*</span></label>
                            <input type="text" class="field-input" x-model="sf.phone" placeholder="07XX XXX XXXX">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.secondary_phone') }}</label>
                            <input type="text" class="field-input" x-model="sf.phone_secondary"
                                placeholder="{{ __('messages.optional') }}">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.email') }}</label>
                            <input type="email" class="field-input" x-model="sf.email"
                                placeholder="{{ __('messages.supplier_email_placeholder') }}">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.city') }}</label>
                            <input type="text" class="field-input" x-model="sf.city"
                                placeholder="{{ __('messages.city_placeholder') }}">
                        </div>
                        <div style="grid-column:span 2">
                            <label class="field-label">{{ __('messages.address') }}</label>
                            <input type="text" class="field-input" x-model="sf.address"
                                placeholder="{{ __('messages.full_address') }}">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.payment_terms') }}</label>
                            <input type="text" class="field-input" x-model="sf.payment_terms"
                                placeholder="{{ __('messages.payment_terms_placeholder') }}">
                            <div class="field-hint">{{ __('messages.payment_schedule_hint') }}</div>
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.status') }}</label>
                            <select class="field-input" x-model="sf.is_active">
                                <option :value="true">{{ __('messages.active') }}</option>
                                <option :value="false">{{ __('messages.inactive') }}</option>
                            </select>
                        </div>
                        <div style="grid-column:span 2">
                            <label class="field-label">{{ __('messages.notes') }}</label>
                            <textarea class="field-input" x-model="sf.notes" placeholder="{{ __('messages.optional_notes') }}"></textarea>
                        </div>
                    </div>

                    <div class="form-err" x-show="formError" x-text="formError" x-cloak></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost"
                        @click="showSupplierModal=false">{{ __('messages.cancel') }}</button>
                    <button type="button" class="btn btn-primary" @click="saveSupplier()" :disabled="saving">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span
                            x-text="saving?'{{ __('messages.saving') }}':(editingSupplier?'{{ __('messages.update_supplier') }}':'{{ __('messages.add_supplier') }}')"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- /sp --}}
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('suppliersPage', () => ({

                activeTab: 'suppliers',

                /* suppliers */
                suppliers: [],
                supPagination: {},
                supLoading: true,
                supSearch: '',
                supFilterStatus: '',
                supFilterCity: '',
                supSortCol: 'name',
                supSortDir: 'asc',
                supPage: 1,
                selectedSupplier: null,
                supplierPOs: [],
                supDetailLoading: false,

                /* purchase orders */
                purchaseOrders: [],
                poPagination: {},
                poLoading: true,
                poSearch: '',
                poFilterStatus: '',
                poFilterPayment: '',
                poPage: 1,
                selectedPO: null,
                poItems: [],
                poItemsLoading: false,
                receiveSaving: false,
                receiveError: '',

                /* supplier modal */
                showSupplierModal: false,
                editingSupplier: null,
                sf: {},
                formError: '',
                saving: false,

                // purchase payment:
                showPaymentModal: false,
                paymentPO: null,
                paymentAmount: 0,
                paymentNote: '',
                poPaymentError: '',

                /* urls */
                urls: {
                    suppliers: '{{ route('pos.suppliers.index') }}',
                    supStore: '{{ route('pos.suppliers.store') }}',
                    supToggle: '{{ url('pos/suppliers') }}',
                    supDetail: '{{ url('pos/suppliers') }}',
                    pos: '{{ route('pos.purchases.index') }}',
                    poItems: '{{ url('pos/purchases') }}',
                    poReceive: '{{ route('pos.purchases.receive') }}',
                    poCancel: '{{ url('pos/purchases') }}',
                    poPayment: '{{ route('pos.purchases.payment') }}',
                    csrf: document.querySelector('meta[name=csrf-token]').content,
                },

                /* ── Init ── */
                init() {
                    this.loadSuppliers();
                    this.loadPOs();
                },

                switchTab(t) {
                    this.activeTab = t;
                    this.selectedSupplier = null;
                    this.selectedPO = null;
                },

                openPaymentModal(po) {
                    this.paymentPO = po;
                    this.paymentAmount = 0;
                    this.paymentNote = '';
                    this.poPaymentError = '';
                    this.showPaymentModal = true;
                },

                async savePOPayment() {
                    if (!this.paymentAmount || this.paymentAmount <= 0) {
                        this.poPaymentError = '{{ __('messages.enter_a_valid_amount') }}';
                        return;
                    }
                    const remaining = (this.paymentPO?.total_cost || 0) - (this.paymentPO
                        ?.amount_paid || 0);
                    if (this.paymentAmount > remaining) { 
                        this.poPaymentError = '{{ __('messages.amount_exceeds_remaining_balance') }}';
                        return;
                    }
                    this.saving = true;
                    this.poPaymentError = '';
                    try {
                        const r = await fetch(this.urls.poPayment, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                purchase_id: this.paymentPO.id,
                                amount: this.paymentAmount,
                                notes: this.paymentNote,
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showPaymentModal = false;
                            this.loadPOs();
                        } else { 
                            this.poPaymentError = d.message ?? '{{ __('messages.payment_failed') }}';
                        }
                    } catch (e) {
                        this.poPaymentError = '{{ __('messages.network_error') }}';
                    } finally {
                        this.saving = false;
                    }
                },

                /* ══════════════════════════════
                   SUPPLIERS
                ══════════════════════════════ */
                async loadSuppliers() {
                    this.supLoading = true;
                    try {
                        const p = new URLSearchParams({
                            q: this.supSearch,
                            status: this.supFilterStatus,
                            city: this.supFilterCity,
                            sort: this.supSortCol,
                            dir: this.supSortDir,
                            page: this.supPage
                        });
                        const r = await fetch(this.urls.suppliers + '?' + p, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const d = await r.json();
                        this.suppliers = d.data;
                        this.supPagination = d.meta;
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.supLoading = false;
                    }
                },

                supSort(col) {
                    if (this.supSortCol === col) this.supSortDir = this.supSortDir === 'asc' ? 'desc' :
                        'asc';
                    else {
                        this.supSortCol = col;
                        this.supSortDir = 'asc';
                    }
                    this.loadSuppliers();
                },

                supGoPage(p) {
                    if (p < 1 || p > this.supPagination.last_page) return;
                    this.supPage = p;
                    this.loadSuppliers();
                },


                async openSupplierDetail(s) {
                    this.selectedSupplier = s;
                    this.supplierPOs = [];
                    this.supDetailLoading = true;
                    try {
                        const r = await fetch(`${this.urls.supDetail}/${s.id}/purchases`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        this.supplierPOs = await r.json();
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.supDetailLoading = false;
                    }
                },

                openSupplierModal(s) {
                    this.editingSupplier = s;
                    this.sf = s ? {
                        ...s
                    } : {
                        name: '',
                        contact_person: '',
                        phone: '',
                        phone_secondary: '',
                        email: '',
                        address: '',
                        city: '',
                        payment_terms: '',
                        notes: '',
                        is_active: true
                    };
                    this.formError = '';
                    this.showSupplierModal = true;
                },

                async saveSupplier() {
                    if (!this.sf.name?.trim()) {
                        this.formError = '{{ __('messages.name_is_required') }}';
                        return;
                    }
                    if (!this.sf.phone?.trim()) { 
                        this.formError = '{{ __('messages.phone_is_required') }}';
                        return;
                    }
                    this.saving = true;
                    this.formError = '';
                    try {
                        const r = await fetch(this.urls.supStore, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                ...this.sf,
                                supplier_id: this.editingSupplier?.id
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showSupplierModal = false;
                            this.loadSuppliers();
                            if (this.selectedSupplier?.id === this.editingSupplier?.id) this
                                .selectedSupplier = {
                                    ...this.selectedSupplier,
                                    ...d.supplier
                                };
                        } else this.formError = d.message ?? '{{ __('messages.failed_to_save') }}'; 
                    } catch (e) {
                        this.formError = '{{ __('messages.network_error') }}';
                    } finally {
                        this.saving = false;
                    }
                },

                async toggleSupplier(s) {
                    if (!confirm(`${s.is_active ? '{{ __('messages.deactivate') }}' : '{{ __('messages.activate') }}'} ${s.name}?`)) return;
                    await fetch(`${this.urls.supToggle}/${s.id}/toggle`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.urls.csrf
                        }
                    });
                    this.loadSuppliers();
                    if (this.selectedSupplier?.id === s.id) this.selectedSupplier = null;
                },

                /* ══════════════════════════════
                   PURCHASE ORDERS
                ══════════════════════════════ */
                async loadPOs() {
                    this.poLoading = true;
                    try {
                        const p = new URLSearchParams({
                            q: this.poSearch,
                            status: this.poFilterStatus,
                            payment: this.poFilterPayment,
                            page: this.poPage
                        });
                        const r = await fetch(this.urls.pos + '?' + p, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const d = await r.json();
                        this.purchaseOrders = d.data;
                        this.poPagination = d.meta;
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.poLoading = false;
                    }
                },

                poGoPage(p) {
                    if (p < 1 || p > this.poPagination.last_page) return;
                    this.poPage = p;
                    this.loadPOs();
                },

                async openPODetail(po) {
                    this.selectedPO = po;
                    this.poItems = [];
                    this.receiveError = '';
                    this.poItemsLoading = true;
                    try {
                        const r = await fetch(`${this.urls.poItems}/${po.id}/items`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const items = await r.json();
                        this.poItems = items.map(i => ({
                            ...i,
                            receive_qty: Math.max(0, i.quantity_ordered - i
                                .quantity_received)
                        }));
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.poItemsLoading = false;
                    }
                },

                async receiveStock() {
                    const toReceive = this.poItems.filter(i => i.receive_qty > 0);
                    if (!toReceive.length) {
                        this.receiveError = '{{ __('messages.enter_at_least_one_quantity_to_receive') }}.';
                        return;
                    }
                    this.receiveSaving = true;
                    this.receiveError = '';
                    try {
                        const r = await fetch(this.urls.poReceive, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                purchase_id: this.selectedPO.id,
                                items: toReceive.map(i => ({
                                    purchase_item_id: i.id,
                                    qty: i.receive_qty
                                }))
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.loadPOs();
                            this.selectedPO = null;
                        } else this.receiveError = d.message ?? '{{ __('messages.failed_to_receive_stock') }}.';
                    } catch (e) {
                        this.receiveError = '{{ __('messages.network_error') }}';
                    } finally {
                        this.receiveSaving = false;
                    }
                },

                async cancelPO(po) {
                    if (!confirm(`Cancel PO ${po.local_id}? {{ __('messages.this_cannot_be_undone') }}`)) return;
                    const r = await fetch(`${this.urls.poCancel}/${po.id}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.urls.csrf
                        }
                    });
                    const d = await r.json();
                    if (d.success) {
                        this.loadPOs();
                        this.selectedPO = null;
                    }
                },

                /* ── Helpers ── */
                initials(name) {
                    if (!name) return '?';
                    return name.trim().split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
                },
                avatarColor(name) {
                    const c = ['#2658e8', '#0891b2', '#15803d', '#d97706', '#7c3aed', '#dc2626',
                        '#1e2d4f'
                    ];
                    return c[(name?.charCodeAt(0) || 0) % c.length];
                },
                fmt(n) {
                    return Number(n || 0).toLocaleString('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                },
            }));
        });
    </script>
@endpush
