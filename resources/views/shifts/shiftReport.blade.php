@extends('layouts.app')

@push('styles')
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))) @vite(['resources/css/pages/shiftReport.css']) @endif
@endpush

@section('content')
<div class="sr">

{{-- TOPBAR --}}
<div class="sr-top no-print">
    <div class="sr-title">Afghan <em>POS</em> — {{ __('messages.shift_report') }}</div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('pos.shifts.page') }}" class="btn btn-ghost">
            <i class="fas fa-arrow-left"></i> {{ __('messages.all_shifts') }}
        </a>
        <button class="btn btn-ghost" onclick="window.print()">
            <i class="fas fa-print"></i> {{ __('messages.print') }}
        </button>
    </div>
</div>

<div class="sr-body">

    {{-- Z-REPORT CARD --}}
    <div class="zr-card">

        {{-- Header --}}
        <div class="zr-head">
            <div>
                <div class="zr-brand">Afghan POS</div>
                <div class="zr-brand-sub">{{ __('messages.shift_z_report_official_record') }}</div>
                <div class="zr-cashier-row">
                    @php
                        $parts    = array_values(array_filter(explode(' ', trim($shift->user->name))));
                        $initials = count($parts) === 1
                            ? strtoupper(substr($parts[0], 0, 2))
                            : strtoupper(collect($parts)->map(fn($p) => substr($p,0,1))->join(''));
                    @endphp
                    <div class="zr-cashier-av">{{ $initials }}</div>
                    <div>
                        <div class="zr-cashier-name">{{ $shift->user->name }}</div>
                        <div class="zr-cashier-role">{{ $shift->user->role?->display_name ?? __('messages.cashier') }}</div>
                    </div>
                    <span style="margin-left:1rem" class="zr-duration">
                        <i class="fas fa-clock" style="margin-right:4px;opacity:.4"></i>{{ $duration }}
                    </span>
                </div>
            </div>
            <div class="zr-meta">
                <div class="zr-shift-id">{{ __('messages.shift_hash') }} #{{ $shift->id }}</div>
                <div class="zr-date">
                    {{ \Carbon\Carbon::parse($shift->opened_at)->format('d M Y, H:i') }}
                    @if($shift->closed_at)
                    → {{ \Carbon\Carbon::parse($shift->closed_at)->format('H:i') }}
                    @else
                    <span style="color:rgba(16,185,129,.8)">· {{ __('messages.active') }}</span>
                    @endif
                </div>
                <div style="margin-top:8px">
                    <span class="pill" style="font-size:10px"
                          :class="shift.is_closed ? 'pill-blue' : 'pill-green'">
                        {{ $shift->is_closed ? __('messages.closed') : __('messages.active') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- KPI row --}}
        <div class="zr-kpi">
            <div class="zr-kpi-item">
                <div class="zr-kpi-label">{{ __('messages.total_revenue') }}</div>
                <div class="zr-kpi-val" style="font-size:16px">Af {{ number_format($totalSales, 0) }}</div>
            </div>
            <div class="zr-kpi-item">
                <div class="zr-kpi-label">{{ __('messages.transactions') }}</div>
                <div class="zr-kpi-val">{{ $txCount }}</div>
            </div>
            <div class="zr-kpi-item">
                <div class="zr-kpi-label">{{ __('messages.items_sold') }}</div>
                <div class="zr-kpi-val">{{ $itemsSold }}</div>
            </div>
            <div class="zr-kpi-item">
                <div class="zr-kpi-label">{{ __('messages.avg_ticket') }}</div>
                <div class="zr-kpi-val" style="font-size:15px">Af {{ number_format($avgTicket, 0) }}</div>
            </div>
        </div>

        {{-- Breakdown --}}
        <div class="zr-breakdown">
            <div class="zr-section-title">{{ __('messages.sales_breakdown') }}</div>
            <div class="zr-row">
                <span class="zr-row-label">{{ __('messages.starting_cash') }}</span>
                <span class="zr-row-val">Af {{ number_format($shift->starting_cash, 0) }}</span>
            </div>
            <div class="zr-row">
                <span class="zr-row-label">{{ __('messages.cash_sales') }}</span>
                <span class="zr-row-val pos">+ Af {{ number_format($cashSales, 0) }}</span>
            </div>
            <div class="zr-row">
                <span class="zr-row-label">{{ __('messages.loan_credit_sales') }}</span>
                <span class="zr-row-val">Af {{ number_format($loanSales, 0) }}</span>
            </div>
            <div class="zr-row">
                <span class="zr-row-label">{{ __('messages.discounts_given') }}</span>
                <span class="zr-row-val neg">- Af {{ number_format($discounts, 0) }}</span>
            </div>
            <div class="zr-row">
                <span class="zr-row-label">{{ __('messages.returns_refunds') }}</span>
                <span class="zr-row-val neg">- Af {{ number_format($returns, 0) }}</span>
            </div>

            <div class="zr-total-row">
                <span class="zr-total-label">{{ __('messages.expected_cash_in_drawer') }}</span>
                <span class="zr-total-val">
                    <span>Af</span>{{ number_format($shift->expected_cash ?? ($shift->starting_cash + $cashSales - $returns), 0) }}
                </span>
            </div>

            @if($shift->actual_cash !== null)
            <div style="margin-top:1rem">
                <div class="zr-section-title">{{ __('messages.cash_reconciliation') }}</div>
                <div class="zr-row">
                    <span class="zr-row-label">{{ __('messages.actual_cash_counted') }}</span>
                    <span class="zr-row-val">Af {{ number_format($shift->actual_cash, 0) }}</span>
                </div>
                @php
                    $disc = $shift->discrepancy ?? 0;
                    $discClass = $disc > 0 ? 'ok' : ($disc < 0 ? 'err' : 'zero');
                    $discLabel = $disc > 0 ? __('messages.surplus') : ($disc < 0 ? __('messages.shortage') : __('messages.exact_match'));
                    $discStr   = $disc > 0 ? '+Af '.number_format($disc,0) : ($disc < 0 ? '-Af '.number_format(abs($disc),0) : 'Af 0');
                @endphp
                <div class="disc-banner {{ $discClass }}">
                    <span class="disc-label">{{ $discLabel }}</span>
                    <span class="disc-val">{{ $discStr }}</span>
                </div>
                @if($shift->discrepancy_note)
                <div style="margin-top:.75rem;padding:10px 14px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:var(--rsm);font-size:12px;color:rgba(255,255,255,.45)">
                    <i class="fas fa-pen" style="margin-right:6px;opacity:.5"></i>{{ $shift->discrepancy_note }}
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Footer note --}}
        <div class="zr-note">
            {{ __('messages.thank_you_message') }} — {{ __('messages.afghan_pos_management_system') }} · {{ __('messages.shift_closed_by') }} {{ $shift->closer?->name ?? $shift->user->name }} · {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
        </div>

        {{-- Actions --}}
        <div class="zr-actions no-print">
            <button class="zr-btn" onclick="window.print()">
                <i class="fas fa-print"></i> {{ __('messages.print_report') }}
            </button>
            <a href="{{ route('pos.shifts.page') }}" class="zr-btn">
                <i class="fas fa-arrow-left"></i> {{ __('messages.all_shifts') }}
            </a>
        </div>

    </div>

    {{-- TOP ITEMS --}}
    <div class="card">
        <div class="card-head">
            <div class="card-title"><i class="fas fa-trophy"></i> {{ __('messages.top_selling_products_this_shift') }}</div>
        </div>
        <div style="overflow-x:auto">
            <table class="top-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.product') }}</th>
                        <th class="cell-right">{{ __('messages.qty_sold') }}</th>
                        <th class="cell-right">{{ __('messages.revenue') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topItems as $idx => $item)
                    <tr>
                        <td>
                            <span class="rank {{ $idx === 0 ? 'rank-1' : ($idx === 1 ? 'rank-2' : ($idx === 2 ? 'rank-3' : 'rank-n')) }}"
                                  >{{ $idx + 1 }}</span>
                        </td>
                        <td style="font-weight:600">{{ $item->name }}</td>
                        <td class="cell-right cell-mono">{{ number_format($item->qty) }}</td>
                        <td class="cell-right cell-mono" style="color:var(--blue);font-weight:600">
                            Af {{ number_format($item->revenue, 0) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:2rem;color:var(--ink3);font-size:13px">
                            {{ __('messages.no_items_sold_in_shift') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>
@endsection