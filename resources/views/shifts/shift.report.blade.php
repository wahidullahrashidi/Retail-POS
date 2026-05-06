@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;500;600&family=Nunito+Sans:wght@300;400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root{
    --bg:#f0f2f8;--surface:#fff;--s2:#f5f6fb;--s3:#eceff6;
    --border:#dde0ed;--ink:#15182a;--ink2:#3d4168;--ink3:#7b82a0;--ink4:#bac0d6;
    --blue:#2f5de8;--blue2:#1f4ccc;--bdim:rgba(47,93,232,.08);
    --green:#15803d;--gdim:rgba(21,128,61,.09);
    --red:#dc2626;--rdim:rgba(220,38,38,.08);
    --amber:#d97706;--adim:rgba(217,119,6,.09);
    --navy:#0f1c3a;
    --mono:'Roboto Mono',monospace;--body:'Nunito Sans',sans-serif;--display:'Unbounded',sans-serif;
    --r:10px;--rsm:6px;--rlg:16px;
    --sh:0 1px 3px rgba(0,0,0,.05),0 1px 2px rgba(0,0,0,.03);
    --shmd:0 4px 18px rgba(0,0,0,.08),0 2px 6px rgba(0,0,0,.04);
}
.sr*{box-sizing:border-box}
.sr{font-family:var(--body);background:var(--bg);min-height:100vh;color:var(--ink)}

/* topbar */
.sr-top{background:var(--surface);border-bottom:1px solid var(--border);height:56px;display:flex;align-items:center;justify-content:space-between;padding:0 1.75rem;position:sticky;top:0;z-index:80;box-shadow:var(--sh)}
.sr-title{font-size:20px;font-weight:700;color:var(--ink);letter-spacing:-.3px}
.sr-title em{color:var(--blue);font-style:italic;font-family:serif}

/* buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:7px 15px;border-radius:var(--rsm);font-family:var(--body);font-size:12.5px;font-weight:600;border:none;cursor:pointer;transition:all .16s;white-space:nowrap;text-decoration:none}
.btn-ghost{background:var(--s2);border:1px solid var(--border);color:var(--ink2)}
.btn-ghost:hover{background:var(--s3);color:var(--ink)}
.btn-primary{background:var(--blue);color:#fff;box-shadow:0 2px 8px rgba(47,93,232,.28)}
.btn-primary:hover{background:var(--blue2);transform:translateY(-1px)}

/* body */
.sr-body{max-width:860px;margin:0 auto;padding:1.75rem}

/* Z-report card */
.zr-card{background:linear-gradient(135deg,var(--navy) 0%,#162040 100%);border:1px solid rgba(255,255,255,.08);border-radius:var(--rlg);overflow:hidden;box-shadow:var(--shmd);margin-bottom:1.5rem}
.zr-head{padding:2rem;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:flex-start;justify-content:space-between;gap:1.5rem}
.zr-brand{font-family:var(--display);font-size:22px;color:#fff;font-weight:600;margin-bottom:5px}
.zr-brand-sub{font-size:12px;color:rgba(255,255,255,.35);letter-spacing:.04em}
.zr-meta{text-align:right}
.zr-shift-id{font-family:var(--mono);font-size:13px;color:rgba(255,255,255,.5);margin-bottom:4px}
.zr-date{font-size:12px;color:rgba(255,255,255,.4)}
.zr-cashier-row{display:flex;align-items:center;gap:10px;margin-top:1rem}
.zr-cashier-av{width:36px;height:36px;border-radius:10px;background:var(--blue);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0}
.zr-cashier-name{font-size:14px;font-weight:600;color:#fff}
.zr-cashier-role{font-size:11px;color:rgba(255,255,255,.35);margin-top:2px}
.zr-duration{font-family:var(--mono);font-size:13px;color:rgba(255,255,255,.5)}

/* kpi grid */
.zr-kpi{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:rgba(255,255,255,.08)}
.zr-kpi-item{background:rgba(255,255,255,.03);padding:1.1rem;text-align:center}
.zr-kpi-label{font-size:9px;font-weight:600;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.1em;margin-bottom:7px}
.zr-kpi-val{font-family:var(--mono);font-size:20px;font-weight:600;color:#fff;letter-spacing:-.3px}

/* breakdown section */
.zr-breakdown{padding:1.5rem 2rem}
.zr-section-title{font-size:10px;font-weight:700;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.12em;margin-bottom:1rem;padding-bottom:.5rem;border-bottom:1px solid rgba(255,255,255,.06)}
.zr-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:13px}
.zr-row:last-child{border-bottom:none}
.zr-row-label{color:rgba(255,255,255,.45)}
.zr-row-val{font-family:var(--mono);font-weight:500;color:#fff}
.zr-row-val.pos{color:#6ee7b7}
.zr-row-val.neg{color:#fca5a5}
.zr-total-row{display:flex;justify-content:space-between;align-items:center;padding:1rem 1.5rem;background:rgba(255,255,255,.06);border-radius:10px;margin-top:1rem}
.zr-total-label{font-size:11px;font-weight:700;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.08em}
.zr-total-val{font-family:var(--mono);font-size:24px;font-weight:600;color:#fff;letter-spacing:-.5px}
.zr-total-val span{font-size:13px;color:rgba(255,255,255,.4);margin-right:3px}

/* discrepancy banner */
.disc-banner{padding:12px 16px;border-radius:var(--rsm);display:flex;align-items:center;justify-content:space-between;margin-top:1rem}
.disc-banner.ok  {background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.2)}
.disc-banner.err {background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.2)}
.disc-banner.zero{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1)}
.disc-label{font-size:12px;font-weight:600;color:rgba(255,255,255,.6)}
.disc-val{font-family:var(--mono);font-size:18px;font-weight:700}
.disc-banner.ok   .disc-val{color:#6ee7b7}
.disc-banner.err  .disc-val{color:#fca5a5}
.disc-banner.zero .disc-val{color:rgba(255,255,255,.5)}

/* footer note */
.zr-note{padding:1rem 2rem;border-top:1px solid rgba(255,255,255,.06);font-size:11px;color:rgba(255,255,255,.25);text-align:center;line-height:1.6}
.zr-actions{padding:1rem 2rem;display:flex;gap:8px;border-top:1px solid rgba(255,255,255,.06)}
.zr-btn{flex:1;padding:10px;border-radius:var(--rsm);border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.07);font-family:var(--body);font-size:12px;font-weight:600;color:rgba(255,255,255,.7);cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:6px;text-decoration:none}
.zr-btn:hover{background:rgba(255,255,255,.12);color:#fff}

/* top items card */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);overflow:hidden;box-shadow:var(--sh);margin-bottom:1.25rem}
.card-head{padding:.9rem 1.25rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.card-title{font-size:13px;font-weight:700;color:var(--ink);display:flex;align-items:center;gap:8px}
.card-title i{color:var(--blue)}

/* top items table */
.top-table{width:100%;border-collapse:collapse;font-size:13px}
.top-table th{padding:9px 14px;text-align:left;font-size:10px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.08em;background:var(--s2);border-bottom:1.5px solid var(--border)}
.top-table td{padding:10px 14px;border-bottom:1px solid var(--border);vertical-align:middle}
.top-table tbody tr:last-child td{border-bottom:none}
.top-table tbody tr:hover{background:var(--bdim)}
.cell-mono{font-family:var(--mono);font-size:12px}
.cell-right{text-align:right}
.rank-1{background:#ffd700;color:#6b4d00}
.rank-2{background:#c0c0c0;color:#3a3a3a}
.rank-3{background:#cd7f32;color:#fff}
.rank-n{background:var(--s3);color:var(--ink3)}
.rank{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;font-family:var(--mono);font-size:11px;font-weight:600}

/* pill */
.pill{display:inline-block;padding:3px 9px;border-radius:99px;font-size:10px;font-weight:700}
.pill-green{background:var(--gdim);color:var(--green);border:1px solid rgba(21,128,61,.2)}
.pill-blue{background:var(--bdim);color:var(--blue);border:1px solid rgba(47,93,232,.2)}

/* print */
@media print {
    .sr-top, .zr-actions, .no-print { display: none !important; }
    .sr-body { padding: 0; max-width: 100%; }
    .zr-card { box-shadow: none; border: 1px solid #ccc; }
}
</style>
@endpush

@section('content')
<div class="sr">

{{-- TOPBAR --}}
<div class="sr-top no-print">
    <div class="sr-title">Afghan <em>POS</em> — Shift Report</div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('pos.shifts.page') }}" class="btn btn-ghost">
            <i class="fas fa-arrow-left"></i> All Shifts
        </a>
        <button class="btn btn-ghost" onclick="window.print()">
            <i class="fas fa-print"></i> Print
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
                <div class="zr-brand-sub">SHIFT Z-REPORT · OFFICIAL RECORD</div>
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
                        <div class="zr-cashier-role">{{ $shift->user->role?->display_name ?? 'Cashier' }}</div>
                    </div>
                    <span style="margin-left:1rem" class="zr-duration">
                        <i class="fas fa-clock" style="margin-right:4px;opacity:.4"></i>{{ $duration }}
                    </span>
                </div>
            </div>
            <div class="zr-meta">
                <div class="zr-shift-id">SHIFT #{{ $shift->id }}</div>
                <div class="zr-date">
                    {{ \Carbon\Carbon::parse($shift->opened_at)->format('d M Y, H:i') }}
                    @if($shift->closed_at)
                    → {{ \Carbon\Carbon::parse($shift->closed_at)->format('H:i') }}
                    @else
                    <span style="color:rgba(16,185,129,.8)">· Active</span>
                    @endif
                </div>
                <div style="margin-top:8px">
                    <span class="pill" style="font-size:10px"
                          :class="shift.is_closed ? 'pill-blue' : 'pill-green'">
                        {{ $shift->is_closed ? 'Closed' : 'Active' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- KPI row --}}
        <div class="zr-kpi">
            <div class="zr-kpi-item">
                <div class="zr-kpi-label">Total Revenue</div>
                <div class="zr-kpi-val" style="font-size:16px">Af {{ number_format($totalSales, 0) }}</div>
            </div>
            <div class="zr-kpi-item">
                <div class="zr-kpi-label">Transactions</div>
                <div class="zr-kpi-val">{{ $txCount }}</div>
            </div>
            <div class="zr-kpi-item">
                <div class="zr-kpi-label">Items Sold</div>
                <div class="zr-kpi-val">{{ $itemsSold }}</div>
            </div>
            <div class="zr-kpi-item">
                <div class="zr-kpi-label">Avg Ticket</div>
                <div class="zr-kpi-val" style="font-size:15px">Af {{ number_format($avgTicket, 0) }}</div>
            </div>
        </div>

        {{-- Breakdown --}}
        <div class="zr-breakdown">
            <div class="zr-section-title">Sales Breakdown</div>
            <div class="zr-row">
                <span class="zr-row-label">Starting Cash</span>
                <span class="zr-row-val">Af {{ number_format($shift->starting_cash, 0) }}</span>
            </div>
            <div class="zr-row">
                <span class="zr-row-label">Cash Sales</span>
                <span class="zr-row-val pos">+ Af {{ number_format($cashSales, 0) }}</span>
            </div>
            <div class="zr-row">
                <span class="zr-row-label">Loan / Credit Sales</span>
                <span class="zr-row-val">Af {{ number_format($loanSales, 0) }}</span>
            </div>
            <div class="zr-row">
                <span class="zr-row-label">Discounts Given</span>
                <span class="zr-row-val neg">- Af {{ number_format($discounts, 0) }}</span>
            </div>
            <div class="zr-row">
                <span class="zr-row-label">Returns / Refunds</span>
                <span class="zr-row-val neg">- Af {{ number_format($returns, 0) }}</span>
            </div>

            <div class="zr-total-row">
                <span class="zr-total-label">Expected Cash in Drawer</span>
                <span class="zr-total-val">
                    <span>Af</span>{{ number_format($shift->expected_cash ?? ($shift->starting_cash + $cashSales - $returns), 0) }}
                </span>
            </div>

            @if($shift->actual_cash !== null)
            <div style="margin-top:1rem">
                <div class="zr-section-title">Cash Reconciliation</div>
                <div class="zr-row">
                    <span class="zr-row-label">Actual Cash (Counted)</span>
                    <span class="zr-row-val">Af {{ number_format($shift->actual_cash, 0) }}</span>
                </div>
                @php
                    $disc = $shift->discrepancy ?? 0;
                    $discClass = $disc > 0 ? 'ok' : ($disc < 0 ? 'err' : 'zero');
                    $discLabel = $disc > 0 ? 'Surplus' : ($disc < 0 ? 'Shortage' : 'Exact Match ✓');
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
            شکریه — Afghan POS Retail Management System · Shift closed by {{ $shift->closer?->name ?? $shift->user->name }} · {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
        </div>

        {{-- Actions --}}
        <div class="zr-actions no-print">
            <button class="zr-btn" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
            <a href="{{ route('pos.shifts.page') }}" class="zr-btn">
                <i class="fas fa-arrow-left"></i> All Shifts
            </a>
        </div>

    </div>

    {{-- TOP ITEMS --}}
    <div class="card">
        <div class="card-head">
            <div class="card-title"><i class="fas fa-trophy"></i> Top Selling Products This Shift</div>
        </div>
        <div style="overflow-x:auto">
            <table class="top-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th class="cell-right">Qty Sold</th>
                        <th class="cell-right">Revenue</th>
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
                            No items sold in this shift.
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
