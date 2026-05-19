@extends('layouts.app')

@push('styles')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/pages/shiftIndex.css'])
    @endif
@endpush

@section('content')
    <div class="sh" x-data="shiftsPage()" x-init="init()">

        {{-- TOPBAR --}}
        <div class="sh-top">
            <div class="sh-title">Afghan <em>POS</em> — {{ __('messages.shifts') }}</div>
            <div style="display:flex;gap:8px">
                @php
                    $active = \App\Models\Shift::where('user_id', auth()->id())
                        ->where('is_closed', false)
                        ->first();
                @endphp
                @if ($active)
                    <a href="{{ route('shift.close.form') }}" class="btn btn-ghost">
                        <i class="fas fa-stop-circle" style="color:var(--red)"></i> {{ __('messages.close_current_shift') }}
                    </a>
                @else
                    <a href="{{ route('shift.open.form') }}" class="btn btn-primary">
                        <i class="fas fa-play-circle"></i> {{ __('messages.open_new_shift') }}
                    </a>
                @endif
            </div>
        </div>

        {{-- STATS --}}
        <div class="stat-strip">
            <div class="stat-tile" style="--ac:var(--green)">
                <div class="st-label">{{ __('messages.active_shifts') }} <span style="color:var(--green)"><i class="fas fa-circle"
                            style="font-size:8px"></i></span></div>
                <div class="st-val" style="color:var(--green)">{{ $stats['active'] ?? 0 }}</div>
                <div class="st-sub">{{ __('messages.currently_open') }}</div>
            </div>
            <div class="stat-tile" style="--ac:var(--blue)">
                <div class="st-label">{{ __('messages.todays_shifts') }} <span style="color:var(--blue)"><i class="fas fa-clock"></i></span>
                </div>
                <div class="st-val">{{ $stats['today'] ?? 0 }}</div>
                <div class="st-sub">{{ __('messages.shifts_opened_today') }}</div>
            </div>
            <div class="stat-tile" style="--ac:var(--amber)">
                <div class="st-label">{{ __('messages.with_discrepancy') }} <span style="color:var(--amber)"><i
                            class="fas fa-triangle-exclamation"></i></span></div>
                <div class="st-val" style="color:var(--amber)">{{ $stats['discrepancies'] ?? 0 }}</div>
                <div class="st-sub">{{ __('messages.cash_mismatches') }}</div>
            </div>
            <div class="stat-tile" style="--ac:var(--teal)">
                <div class="st-label">{{ __('messages.avg_duration') }} <span style="color:var(--teal)"><i
                            class="fas fa-hourglass-half"></i></span></div>
                <div class="st-val" style="font-size:18px">{{ $stats['avg_duration'] ?? '—' }}</div>
                <div class="st-sub">{{ __('messages.per_shift_this_week') }}</div>
            </div>
        </div>

        {{-- TOOLBAR --}}
        <div class="sh-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input class="sh-search" type="search" autocomplete="off" autocapitalize="off" spellcheck="false"
                    x-model="search" @input.debounce.350ms="loadShifts()" placeholder="{{ __('messages.cashier_name_placeholder') }}">
            </div>
            <input type="date" class="f-sel" x-model="dateFrom" @change="loadShifts()">
            <input type="date" class="f-sel" x-model="dateTo" @change="loadShifts()">
            <select class="f-sel" x-model="filterUser" @change="loadShifts()">
                <option value="">{{ __('messages.all_cashiers') }}</option>
                @foreach ($cashiers ?? [] as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
            <div class="tab-strip">
                <button type="button" class="tab-btn" :class="tab === 'all' ? 'active' : ''"
                    @click="tab='all';loadShifts()">{{ __('messages.all') }}</button>
                <button type="button" class="tab-btn" :class="tab === 'active' ? 'active' : ''"
                    @click="tab='active';loadShifts()">{{ __('messages.active') }}</button>
                <button type="button" class="tab-btn" :class="tab === 'closed' ? 'active' : ''"
                    @click="tab='closed';loadShifts()">{{ __('messages.closed') }}</button>
            </div>
        </div>

        {{-- MAIN --}}
        <div class="sh-main" :class="selected ? 'panel-open' : ''" style="align-items:start">

            {{-- TABLE --}}
            <div class="table-card">
                <div class="loading-row" x-show="loading"><i class="fas fa-spinner fa-spin" style="font-size:18px"></i>
                </div>
                <div x-show="!loading">
                    <div class="empty-state" x-show="shifts.length===0">
                        <i class="fas fa-clock"></i>
                        <p>{{ __('messages.no_shifts_found') }}<br>{{ __('messages.adjust_filters') }}</p>
                    </div>
                    <table class="sh-table {{ in_array(app()->getLocale(), ['ps', 'dr']) ? 'rtl-table' : '' }}" x-show="shifts.length>0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.cashier') }}</th>
                                <th>{{ __('messages.opened') }}</th>
                                <th>{{ __('messages.closed_colon') }}</th>
                                <th>{{ __('messages.duration') }}</th>
                                <th class="cell-right">{{ __('messages.starting_cash') }}</th>
                                <th class="cell-right">{{ __('messages.cash_sales') }}</th>
                                <th class="cell-right">{{ __('messages.discrepancy') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="s in shifts" :key="s.id">
                                <tr :class="selected?.id === s.id ? 'selected' : ''" @click="openDetail(s)">
                                    <td>
                                        <div class="cashier-cell">
                                            <div class="cashier-av" :style="`background:${avatarColor(s.cashier)}`"
                                                x-text="initials(s.cashier)"></div>
                                            <div>
                                                <div style="font-weight:600;font-size:13px" x-text="s.cashier"></div>
                                                <div style="font-size:11px;color:var(--ink3)" x-text="s.role"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="cell-mono" style="font-size:11.5px" x-text="s.opened_at"></td>
                                    <td class="cell-mono" style="font-size:11.5px;color:var(--ink3)"
                                        x-text="s.closed_at||'—'"></td>
                                    <td class="cell-mono" style="font-size:12px" x-text="s.duration||'{{ __('messages.active_status') }}'"></td>
                                    <td class="cell-right cell-mono" x-text="'{{ __('messages.af') }} '+fmt(s.starting_cash)"></td>
                                    <td class="cell-right cell-mono" style="color:var(--green)"
                                        x-text="'{{ __('messages.af') }} '+fmt(s.cash_sales)"></td>
                                    <td class="cell-right">
                                        <span x-show="s.discrepancy===null" class="disc-zero">—</span>
                                        <span x-show="s.discrepancy!==null && s.discrepancy>0" class="disc-pos"
                                            x-text="'+{{ __('messages.af') }} '+fmt(s.discrepancy)"></span>
                                        <span x-show="s.discrepancy!==null && s.discrepancy<0" class="disc-neg"
                                            x-text="'-{{ __('messages.af') }} '+fmt(Math.abs(s.discrepancy))"></span>
                                        <span x-show="s.discrepancy===0" class="disc-zero">{{ __('messages.exact') }}</span>
                                    </td>
                                    <td>
                                        <span class="pill" :class="s.is_closed ? 'pill-blue' : 'pill-green'"
                                            x-text="s.is_closed?'{{ __('messages.closed_status') }}':'{{ __('messages.active_status') }}'"></span>
                                    </td>
                                    <td @click.stop>
                                        <a :href="'/pos/shifts/' + s.id + '/report'" class="btn btn-ghost btn-sm"
                                            title="{{ __('messages.view_report') }}">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div class="pag-row" x-show="pagination.last_page>1">
                        <div class="pag-info">{{ __('messages.showing') }} <span x-text="pagination.from"></span>–<span
                                x-text="pagination.to"></span> {{ __('messages.of') }} <span x-text="pagination.total"></span></div>
                        <div class="pag-btns">
                            <button class="pag-btn" @click="goPage(pagination.current_page-1)"
                                :disabled="pagination.current_page === 1"><i class="fas fa-chevron-left"></i></button>
                            <template x-for="p in pagination.last_page" :key="p">
                                <button class="pag-btn" :class="p === pagination.current_page ? 'active' : ''"
                                    @click="goPage(p)" x-text="p"></button>
                            </template>
                            <button class="pag-btn" @click="goPage(pagination.current_page+1)"
                                :disabled="pagination.current_page === pagination.last_page"><i
                                    class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETAIL PANEL --}}
            <div class="detail-panel" x-show="selected" x-cloak>
                <div class="dp-head">
                    <span class="dp-head-label">{{ __('messages.shift_detail') }}</span>
                    <button class="dp-close" @click="selected=null"><i class="fas fa-times"></i></button>
                </div>
                <div class="dp-body">

                    {{-- Hero --}}
                    <div class="dp-hero">
                        <div class="dp-hero-status">
                            <div class="dp-cashier">
                                <div class="dp-cashier-av" :style="`background:${avatarColor(selected?.cashier)}`"
                                    x-text="initials(selected?.cashier)"></div>
                                <div>
                                    <div class="dp-cashier-name" x-text="selected?.cashier"></div>
                                    <div class="dp-cashier-role" x-text="selected?.role"></div>
                                </div>
                            </div>
                            <div>
                                <div class="dp-duration" x-text="selected?.duration || '{{ __('messages.active_status') }}'"></div>
                                <div class="dp-dur-label">{{ __('messages.duration') }}</div>
                            </div>
                        </div>
                        <div class="dp-kpi">
                            <div class="dp-kpi-item">
                                <div class="dp-kpi-val" x-text="'{{ __('messages.af') }} '+fmt(selected?.cash_sales||0)"></div>
                                <div class="dp-kpi-label">{{ __('messages.cash_sales') }}</div>
                            </div>
                            <div class="dp-kpi-item">
                                <div class="dp-kpi-val" x-text="selected?.tx_count||0"></div>
                                <div class="dp-kpi-label">{{ __('messages.transactions') }}</div>
                            </div>
                            <div class="dp-kpi-item">
                                <div class="dp-kpi-val"
    :style="(selected?.discrepancy || 0) > 0 ? 'color:#6ee7b7' : (selected?.discrepancy || 0) < 0 ? 'color:#fca5a5' : ''"
    x-text="selected?.discrepancy != null ? ((selected?.discrepancy >= 0 ? '+' : '') + ' {{ __('messages.af') }} ' + fmt(selected?.discrepancy)) : '—'">
                                </div>
                                <div class="dp-kpi-label">{{ __('messages.discrepancy') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Times --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-clock"></i> {{ __('messages.shift_times') }}</div>
                        <div class="info-grid">
                            <div class="info-field">
                                <div class="if-label">{{ __('messages.opened_at') }}</div>
                                <div class="if-val mono" x-text="selected?.opened_at"></div>
                            </div>
                            <div class="info-field">
                                <div class="if-label">{{ __('messages.closed_at') }}</div>
                                <div class="if-val mono" x-text="selected?.closed_at||'{{ __('messages.still_active') }}'"></div>
                            </div>
                            <div class="info-field" x-show="selected?.closed_by">
                                <div class="if-label">{{ __('messages.closed_by') }}</div>
                                <div class="if-val" x-text="selected?.closed_by||'—'"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Cash reconciliation --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-coins"></i> {{ __('messages.cash_reconciliation') }}</div>
                        <div class="recon-row">
                            <span>{{ __('messages.starting_cash') }}</span>
                            <span class="recon-val" x-text="'{{ __('messages.af') }} '+fmt(selected?.starting_cash||0)"></span>
                        </div>
                        <div class="recon-row">
                            <span style="color:var(--green)">+ {{ __('messages.cash_sales') }}</span>
                            <span class="recon-val" style="color:var(--green)"
                                x-text="'+ {{ __('messages.af') }} '+fmt(selected?.cash_sales||0)"></span>
                        </div>
                        <div class="recon-row">
                            <span style="color:var(--ink3)">= {{ __('messages.expected') }}</span>
                            <span class="recon-val" style="color:var(--blue)"
                                x-text="'{{ __('messages.af') }} '+fmt(selected?.expected_cash||0)"></span>
                        </div>
                        <div class="recon-row" x-show="selected?.actual_cash!==null">
                            <span>{{ __('messages.actual_counted') }}</span>
                            <span class="recon-val" x-text="'{{ __('messages.af') }} '+fmt(selected?.actual_cash||0)"></span>
                        </div>
                        <div class="recon-row" x-show="selected?.discrepancy!==null">
                            <span :style="(selected?.discrepancy || 0) >= 0 ? 'color:var(--green)' : 'color:var(--red)'">
                                {{ __('messages.discrepancy') }}
                            </span>
                            <span class="recon-val"
                                :style="(selected?.discrepancy || 0) >= 0 ? 'color:var(--green)' : 'color:var(--red)'"
                                x-text="((selected?.discrepancy||0)>=0?'+':'')+' {{ __('messages.af') }} '+fmt(selected?.discrepancy||0)">
                            </span>
                        </div>
                    </div>

                    {{-- Discrepancy note --}}
                    <div class="dp-section" x-show="selected?.discrepancy_note">
                        <div class="dp-section-title"><i class="fas fa-pen"></i> {{ __('messages.discrepancy_note') }}</div>
                        <div style="font-size:12.5px;color:var(--ink2);line-height:1.6;background:var(--adim);border:1px solid rgba(217,119,6,.2);padding:10px 12px;border-radius:var(--rsm)"
                            x-text="selected?.discrepancy_note"></div>
                    </div>

                    {{-- Top items sold in shift --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-trophy"></i> {{ __('messages.top_items_this_shift') }}</div>
                        <div x-show="detailLoading"
                            style="text-align:center;padding:1rem;color:var(--ink3);font-size:12px">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <div x-show="!detailLoading">
                            <div x-show="topItems.length===0"
                                style="text-align:center;padding:.75rem;color:var(--ink4);font-size:12px">
                                {{ __('messages.no_items_sold_in_shift') }}
                            </div>
                            <template x-for="item in topItems" :key="item.sku">
                                <div class="mini-item">
                                    <span class="mi-name" x-text="item.name"></span>
                                    <span class="mi-qty" x-text="'×'+item.qty"></span>
                                    <span class="mi-amt" x-text="'{{ __('messages.af') }} '+fmt(item.revenue)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
                <div class="dp-foot">
                    <a :href="'/pos/shifts/' + selected?.id + '/report'" class="btn btn-primary" style="flex:1">
                        <i class="fas fa-file-alt"></i> {{ __('messages.full_report') }}
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('shiftsPage', () => ({
                shifts: [],
                pagination: {},
                loading: true,
                search: '',
                dateFrom: '',
                dateTo: '',
                filterUser: '',
                tab: 'all',
                currentPage: 1,
                selected: null,
                topItems: [],
                detailLoading: false,

                urls: {
                    list: '{{ route('pos.shifts.index') }}',
                    detail: '{{ url('pos/shifts') }}',
                    csrf: document.querySelector('meta[name=csrf-token]').content,
                },

                async init() {
                    const today = new Date().toISOString().split('T')[0];
                    this.dateFrom = new Date(Date.now() - 30 * 86400000).toISOString().split('T')[
                    0];
                    this.dateTo = today;
                    await this.loadShifts();
                },

                async loadShifts() {
                    this.loading = true;
                    try {
                        const p = new URLSearchParams({
                            q: this.search,
                            from: this.dateFrom,
                            to: this.dateTo,
                            user: this.filterUser,
                            tab: this.tab,
                            page: this.currentPage
                        });
                        const r = await fetch(this.urls.list + '?' + p, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const d = await r.json();
                        this.shifts = d.data;
                        this.pagination = d.meta;
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.loading = false;
                    }
                },

                goPage(p) {
                    if (p < 1 || p > this.pagination.last_page) return;
                    this.currentPage = p;
                    this.loadShifts();
                },

                async openDetail(s) {
                    this.selected = s;
                    this.topItems = [];
                    this.detailLoading = true;
                    try {
                        const r = await fetch(`${this.urls.detail}/${s.id}/detail`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const d = await r.json();
                        this.topItems = d.top_items;
                        this.selected = {
                            ...this.selected,
                            ...d.shift
                        };
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.detailLoading = false;
                    }
                },

                initials(name) {
                    if (!name) return '?';
                    return name.trim().split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
                },
                avatarColor(name) {
                    const c = ['#2f5de8', '#0891b2', '#15803d', '#d97706', '#7c3aed', '#dc2626'];
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
