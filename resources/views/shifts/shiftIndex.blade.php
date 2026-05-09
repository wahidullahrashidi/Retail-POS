@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;500;600&family=Nunito+Sans:wght@300;400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root{
    --bg:#f0f2f8;--surface:#fff;--s2:#f5f6fb;--s3:#eceff6;
    --border:#dde0ed;--border2:#c3c8dc;
    --ink:#15182a;--ink2:#3d4168;--ink3:#7b82a0;--ink4:#bac0d6;
    --blue:#2f5de8;--blue2:#1f4ccc;--bdim:rgba(47,93,232,.08);--bmid:rgba(47,93,232,.16);
    --green:#15803d;--gdim:rgba(21,128,61,.09);
    --red:#dc2626;--rdim:rgba(220,38,38,.08);
    --amber:#d97706;--adim:rgba(217,119,6,.09);
    --teal:#0891b2;--tdim:rgba(8,145,178,.09);
    --mono:'Roboto Mono',monospace;--body:'Nunito Sans',sans-serif;--display:'Unbounded',sans-serif;
    --r:10px;--rsm:6px;--rlg:16px;
    --sh:0 1px 3px rgba(0,0,0,.05),0 1px 2px rgba(0,0,0,.03);
    --shmd:0 4px 18px rgba(0,0,0,.08),0 2px 6px rgba(0,0,0,.04);
    --shlg:0 20px 56px rgba(0,0,0,.12),0 6px 16px rgba(0,0,0,.06);
}
.sh *{box-sizing:border-box}
.sh{font-family:var(--body);background:var(--bg);min-height:100vh;color:var(--ink)}
[x-cloak]{display:none!important}

/* topbar */
.sh-top{background:var(--surface);border-bottom:1px solid var(--border);height:56px;display:flex;align-items:center;justify-content:space-between;padding:0 1.75rem;position:sticky;top:0;z-index:80;box-shadow:var(--sh)}
.sh-title{font-size:20px;font-weight:700;color:var(--ink);letter-spacing:-.3px}
.sh-title em{color:var(--blue);font-style:italic;font-family:serif}

/* buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:7px 15px;border-radius:var(--rsm);font-family:var(--body);font-size:12.5px;font-weight:600;border:none;cursor:pointer;transition:all .16s;white-space:nowrap}
.btn-ghost{background:var(--s2);border:1px solid var(--border);color:var(--ink2)}
.btn-ghost:hover{background:var(--s3);color:var(--ink)}
.btn-primary{background:var(--blue);color:#fff;box-shadow:0 2px 8px rgba(47,93,232,.28)}
.btn-primary:hover{background:var(--blue2);transform:translateY(-1px)}
.btn-sm{padding:5px 10px;font-size:11.5px}
.btn:active{transform:scale(.97)}

/* stat strip */
.stat-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;padding:1.2rem 1.75rem .75rem}
.stat-tile{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);padding:1rem 1.2rem;position:relative;overflow:hidden;transition:all .2s;cursor:default}
.stat-tile:hover{box-shadow:var(--shmd);transform:translateY(-2px)}
.stat-tile::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--ac,var(--blue));transform:scaleX(0);transform-origin:left;transition:transform .3s}
.stat-tile:hover::before{transform:scaleX(1)}
.st-label{font-size:10px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.1em;margin-bottom:7px;display:flex;align-items:center;justify-content:space-between}
.st-val{font-family:var(--mono);font-size:26px;font-weight:500;color:var(--ink);line-height:1;letter-spacing:-.5px}
.st-sub{font-size:11px;color:var(--ink3);margin-top:5px}

/* toolbar */
.sh-toolbar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:.75rem 1.75rem .9rem}
.search-box{position:relative;flex:1;min-width:200px;max-width:300px}
.search-box i{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--ink3);font-size:13px;pointer-events:none}
.sh-search{width:100%;padding:9px 14px 9px 34px;background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rsm);font-family:var(--body);font-size:13px;color:var(--ink);outline:none;transition:border .15s,box-shadow .15s}
.sh-search:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--bdim);background:#fff}
.sh-search::placeholder{color:var(--ink4)}
.f-sel{padding:9px 12px;background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rsm);font-family:var(--body);font-size:12.5px;color:var(--ink2);outline:none;cursor:pointer;transition:border .15s}
.f-sel:focus{border-color:var(--blue)}
.tab-strip{display:flex;gap:4px;margin-left:auto}
.tab-btn{padding:7px 13px;border:1px solid var(--border);border-radius:var(--rsm);background:var(--surface);font-family:var(--body);font-size:12px;font-weight:600;cursor:pointer;color:var(--ink3);transition:all .15s}
.tab-btn.active{background:var(--blue);color:#fff;border-color:var(--blue)}

/* main layout */
.sh-main{display:grid;grid-template-columns:1fr;gap:0;padding:0 1.75rem 2rem;transition:grid-template-columns .25s;align-items:start}
.sh-main.panel-open{grid-template-columns:1fr 420px;gap:1.25rem}

/* table */
.table-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);overflow:hidden;box-shadow:var(--sh)}
.sh-table{width:100%;border-collapse:collapse;font-size:13px}
.sh-table thead{background:var(--s2);border-bottom:1.5px solid var(--border)}
.sh-table th{padding:10px 14px;text-align:left;font-size:10px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.08em;white-space:nowrap;cursor:pointer;user-select:none;transition:color .15s}
.sh-table th:hover{color:var(--blue)}
.sh-table td{padding:11px 14px;border-bottom:1px solid var(--border);vertical-align:middle}
.sh-table tbody tr:last-child td{border-bottom:none}
.sh-table tbody tr{transition:background .12s;cursor:pointer}
.sh-table tbody tr:hover{background:var(--bdim)}
.sh-table tbody tr.selected{background:var(--bdim);border-left:3px solid var(--blue)}
.cell-mono{font-family:var(--mono);font-size:12px}
.cell-right{text-align:right}

/* pills */
.pill{display:inline-block;padding:3px 9px;border-radius:99px;font-size:10px;font-weight:700;letter-spacing:.04em}
.pill-green{background:var(--gdim);color:var(--green);border:1px solid rgba(21,128,61,.2)}
.pill-red{background:var(--rdim);color:var(--red);border:1px solid rgba(220,38,38,.2)}
.pill-amber{background:var(--adim);color:var(--amber);border:1px solid rgba(217,119,6,.2)}
.pill-blue{background:var(--bdim);color:var(--blue);border:1px solid var(--bmid)}
.pill-gray{background:var(--s3);color:var(--ink3);border:1px solid var(--border)}

/* cashier avatar */
.cashier-cell{display:flex;align-items:center;gap:9px}
.cashier-av{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0}

/* discrepancy cell */
.disc-pos{font-family:var(--mono);font-size:12px;font-weight:600;color:var(--green)}
.disc-neg{font-family:var(--mono);font-size:12px;font-weight:600;color:var(--red)}
.disc-zero{font-family:var(--mono);font-size:12px;color:var(--ink3)}

/* pag */
.pag-row{display:flex;align-items:center;justify-content:space-between;padding:11px 16px;border-top:1px solid var(--border);background:var(--s2)}
.pag-info{font-size:12px;color:var(--ink3)}
.pag-btns{display:flex;gap:4px}
.pag-btn{width:30px;height:30px;border-radius:var(--rsm);border:1px solid var(--border);background:var(--surface);cursor:pointer;font-family:var(--mono);font-size:11px;color:var(--ink2);display:flex;align-items:center;justify-content:center;transition:all .12s}
.pag-btn:hover{background:var(--bdim);border-color:var(--blue);color:var(--blue)}
.pag-btn.active{background:var(--blue);color:#fff;border-color:var(--blue)}
.pag-btn:disabled{opacity:.3;cursor:not-allowed}

/* empty/loading */
.empty-state{text-align:center;padding:4rem 2rem;color:var(--ink3)}
.empty-state i{font-size:36px;margin-bottom:12px;display:block;color:var(--ink4)}
.empty-state p{font-size:13px;line-height:1.7}
.loading-row{text-align:center;padding:3rem;color:var(--ink3)}

/* detail panel */
.detail-panel{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);box-shadow:var(--sh);display:flex;flex-direction:column;max-height:calc(100vh - 200px);position:sticky;top:72px;overflow:hidden;animation:panelIn .2s cubic-bezier(.2,.8,.36,1)}
@keyframes panelIn{from{opacity:0;transform:translateX(14px)}to{opacity:1;transform:none}}
.dp-head{padding:.9rem 1.25rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
.dp-head-label{font-size:11px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.09em}
.dp-close{background:none;border:none;cursor:pointer;color:var(--ink3);font-size:16px;transition:color .15s}
.dp-close:hover{color:var(--ink)}
.dp-body{flex:1;overflow-y:auto}
.dp-body::-webkit-scrollbar{width:4px}
.dp-body::-webkit-scrollbar-thumb{background:var(--border);border-radius:2px}
.dp-foot{padding:.9rem 1.25rem;border-top:1px solid var(--border);display:flex;gap:7px;flex-shrink:0}

/* panel hero — dark gradient */
.dp-hero{background:linear-gradient(135deg,#1a2744 0%,#0f1c38 100%);padding:1.5rem 1.25rem;color:#fff}
.dp-hero-status{display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem}
.dp-cashier{display:flex;align-items:center;gap:10px}
.dp-cashier-av{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#fff;flex-shrink:0}
.dp-cashier-name{font-size:15px;font-weight:600;color:#fff}
.dp-cashier-role{font-size:11px;color:rgba(255,255,255,.45);margin-top:2px}
.dp-duration{font-family:var(--mono);font-size:22px;font-weight:500;color:#fff;text-align:right}
.dp-dur-label{font-size:10px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.08em;margin-top:2px}
.dp-kpi{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:1rem}
.dp-kpi-item{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:.7rem;text-align:center}
.dp-kpi-val{font-family:var(--mono);font-size:14px;font-weight:600;color:#fff}
.dp-kpi-label{font-size:9px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.06em;margin-top:3px}

/* panel sections */
.dp-section{padding:.85rem 1.25rem;border-bottom:1px solid var(--border)}
.dp-section:last-child{border-bottom:none}
.dp-section-title{font-size:10px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.1em;margin-bottom:.6rem;display:flex;align-items:center;gap:6px}
.dp-section-title i{color:var(--blue)}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:7px}
.info-field{background:var(--s2);border:1px solid var(--border);border-radius:var(--rsm);padding:7px 10px}
.info-field.full{grid-column:span 2}
.if-label{font-size:10px;color:var(--ink3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px}
.if-val{font-size:12.5px;font-weight:500;color:var(--ink)}
.if-val.mono{font-family:var(--mono);font-size:12px}

/* cash recon in panel */
.recon-row{display:flex;justify-content:space-between;font-size:12.5px;padding:6px 0;border-bottom:1px solid var(--border);color:var(--ink2)}
.recon-row:last-child{border-bottom:none;font-weight:700;font-size:14px;color:var(--ink);padding-top:10px}
.recon-val{font-family:var(--mono);font-weight:500}

/* top items mini table */
.mini-item{display:flex;align-items:center;justify-content:space-between;padding:7px 10px;background:var(--s2);border:1px solid var(--border);border-radius:var(--rsm);margin-bottom:5px;font-size:12px}
.mi-name{font-weight:500;color:var(--ink);flex:1;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mi-qty{font-family:var(--mono);font-size:11px;color:var(--ink3);margin:0 10px}
.mi-amt{font-family:var(--mono);font-size:12px;font-weight:600;color:var(--blue)}
</style>
@endpush

@section('content')
<div class="sh" x-data="shiftsPage()" x-init="init()">

{{-- TOPBAR --}}
<div class="sh-top">
    <div class="sh-title">Afghan <em>POS</em> — Shifts</div>
    <div style="display:flex;gap:8px">
        @php $active = \App\Models\Shift::where('user_id', auth()->id())->where('is_closed', false)->first(); @endphp
        @if($active)
        <a href="{{ route('shift.close.form') }}" class="btn btn-ghost">
            <i class="fas fa-stop-circle" style="color:var(--red)"></i> Close Current Shift
        </a>
        @else
        <a href="{{ route('shift.open.form') }}" class="btn btn-primary">
            <i class="fas fa-play-circle"></i> Open New Shift
        </a>
        @endif
    </div>
</div>

{{-- STATS --}}
<div class="stat-strip">
    <div class="stat-tile" style="--ac:var(--green)">
        <div class="st-label">Active Shifts <span style="color:var(--green)"><i class="fas fa-circle" style="font-size:8px"></i></span></div>
        <div class="st-val" style="color:var(--green)">{{ $stats['active'] ?? 0 }}</div>
        <div class="st-sub">currently open</div>
    </div>
    <div class="stat-tile" style="--ac:var(--blue)">
        <div class="st-label">Today's Shifts <span style="color:var(--blue)"><i class="fas fa-clock"></i></span></div>
        <div class="st-val">{{ $stats['today'] ?? 0 }}</div>
        <div class="st-sub">shifts opened today</div>
    </div>
    <div class="stat-tile" style="--ac:var(--amber)">
        <div class="st-label">With Discrepancy <span style="color:var(--amber)"><i class="fas fa-triangle-exclamation"></i></span></div>
        <div class="st-val" style="color:var(--amber)">{{ $stats['discrepancies'] ?? 0 }}</div>
        <div class="st-sub">cash mismatches</div>
    </div>
    <div class="stat-tile" style="--ac:var(--teal)">
        <div class="st-label">Avg Duration <span style="color:var(--teal)"><i class="fas fa-hourglass-half"></i></span></div>
        <div class="st-val" style="font-size:18px">{{ $stats['avg_duration'] ?? '—' }}</div>
        <div class="st-sub">per shift this week</div>
    </div>
</div>

{{-- TOOLBAR --}}
<div class="sh-toolbar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input class="sh-search" type="text" x-model="search"
               @input.debounce.350ms="loadShifts()"
               placeholder="Cashier name…">
    </div>
    <input type="date" class="f-sel" x-model="dateFrom" @change="loadShifts()">
    <input type="date" class="f-sel" x-model="dateTo"   @change="loadShifts()">
    <select class="f-sel" x-model="filterUser" @change="loadShifts()">
        <option value="">All Cashiers</option>
        @foreach($cashiers ?? [] as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
    </select>
    <div class="tab-strip">
        <button type="button" class="tab-btn" :class="tab==='all'?'active':''"    @click="tab='all';loadShifts()">All</button>
        <button type="button" class="tab-btn" :class="tab==='active'?'active':''" @click="tab='active';loadShifts()">Active</button>
        <button type="button" class="tab-btn" :class="tab==='closed'?'active':''" @click="tab='closed';loadShifts()">Closed</button>
    </div>
</div>

{{-- MAIN --}}
<div class="sh-main" :class="selected?'panel-open':''" style="align-items:start">

    {{-- TABLE --}}
    <div class="table-card">
        <div class="loading-row" x-show="loading"><i class="fas fa-spinner fa-spin" style="font-size:18px"></i></div>
        <div x-show="!loading">
            <div class="empty-state" x-show="shifts.length===0">
                <i class="fas fa-clock"></i>
                <p>No shifts found.<br>Try adjusting the filters.</p>
            </div>
            <table class="sh-table" x-show="shifts.length>0">
                <thead>
                    <tr>
                        <th>Cashier</th>
                        <th>Opened</th>
                        <th>Closed</th>
                        <th>Duration</th>
                        <th class="cell-right">Starting Cash</th>
                        <th class="cell-right">Cash Sales</th>
                        <th class="cell-right">Discrepancy</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="s in shifts" :key="s.id">
                        <tr :class="selected?.id===s.id?'selected':''" @click="openDetail(s)">
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
                            <td class="cell-mono" style="font-size:11.5px;color:var(--ink3)" x-text="s.closed_at||'—'"></td>
                            <td class="cell-mono" style="font-size:12px" x-text="s.duration||'Active'"></td>
                            <td class="cell-right cell-mono" x-text="'Af '+fmt(s.starting_cash)"></td>
                            <td class="cell-right cell-mono" style="color:var(--green)" x-text="'Af '+fmt(s.cash_sales)"></td>
                            <td class="cell-right">
                                <span x-show="s.discrepancy===null" class="disc-zero">—</span>
                                <span x-show="s.discrepancy!==null && s.discrepancy>0" class="disc-pos"
                                      x-text="'+Af '+fmt(s.discrepancy)"></span>
                                <span x-show="s.discrepancy!==null && s.discrepancy<0" class="disc-neg"
                                      x-text="'-Af '+fmt(Math.abs(s.discrepancy))"></span>
                                <span x-show="s.discrepancy===0" class="disc-zero">Exact</span>
                            </td>
                            <td>
                                <span class="pill" :class="s.is_closed?'pill-blue':'pill-green'"
                                      x-text="s.is_closed?'Closed':'Active'"></span>
                            </td>
                            <td @click.stop>
                                <a :href="'/pos/shifts/'+s.id+'/report'" class="btn btn-ghost btn-sm" title="View Report">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <div class="pag-row" x-show="pagination.last_page>1">
                <div class="pag-info">Showing <span x-text="pagination.from"></span>–<span x-text="pagination.to"></span> of <span x-text="pagination.total"></span></div>
                <div class="pag-btns">
                    <button class="pag-btn" @click="goPage(pagination.current_page-1)" :disabled="pagination.current_page===1"><i class="fas fa-chevron-left"></i></button>
                    <template x-for="p in pagination.last_page" :key="p">
                        <button class="pag-btn" :class="p===pagination.current_page?'active':''" @click="goPage(p)" x-text="p"></button>
                    </template>
                    <button class="pag-btn" @click="goPage(pagination.current_page+1)" :disabled="pagination.current_page===pagination.last_page"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAIL PANEL --}}
    <div class="detail-panel" x-show="selected" x-cloak>
        <div class="dp-head">
            <span class="dp-head-label">Shift Detail</span>
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
                        <div class="dp-duration" x-text="selected?.duration || 'Active'"></div>
                        <div class="dp-dur-label">Duration</div>
                    </div>
                </div>
                <div class="dp-kpi">
                    <div class="dp-kpi-item">
                        <div class="dp-kpi-val" x-text="'Af '+fmt(selected?.cash_sales||0)"></div>
                        <div class="dp-kpi-label">Cash Sales</div>
                    </div>
                    <div class="dp-kpi-item">
                        <div class="dp-kpi-val" x-text="selected?.tx_count||0"></div>
                        <div class="dp-kpi-label">Transactions</div>
                    </div>
                    <div class="dp-kpi-item">
                        <div class="dp-kpi-val"
                             :style="(selected?.discrepancy||0)>0?'color:#6ee7b7':(selected?.discrepancy||0)<0?'color:#fca5a5':''"
                             x-text="selected?.discrepancy!==null ? ((selected.discrepancy>=0?'+':'')+' Af '+fmt(selected.discrepancy)) : '—'">
                        </div>
                        <div class="dp-kpi-label">Discrepancy</div>
                    </div>
                </div>
            </div>

            {{-- Times --}}
            <div class="dp-section">
                <div class="dp-section-title"><i class="fas fa-clock"></i> Shift Times</div>
                <div class="info-grid">
                    <div class="info-field">
                        <div class="if-label">Opened At</div>
                        <div class="if-val mono" x-text="selected?.opened_at"></div>
                    </div>
                    <div class="info-field">
                        <div class="if-label">Closed At</div>
                        <div class="if-val mono" x-text="selected?.closed_at||'Still Active'"></div>
                    </div>
                    <div class="info-field" x-show="selected?.closed_by">
                        <div class="if-label">Closed By</div>
                        <div class="if-val" x-text="selected?.closed_by||'—'"></div>
                    </div>
                </div>
            </div>

            {{-- Cash reconciliation --}}
            <div class="dp-section">
                <div class="dp-section-title"><i class="fas fa-coins"></i> Cash Reconciliation</div>
                <div class="recon-row">
                    <span>Starting Cash</span>
                    <span class="recon-val" x-text="'Af '+fmt(selected?.starting_cash||0)"></span>
                </div>
                <div class="recon-row">
                    <span style="color:var(--green)">+ Cash Sales</span>
                    <span class="recon-val" style="color:var(--green)" x-text="'+ Af '+fmt(selected?.cash_sales||0)"></span>
                </div>
                <div class="recon-row">
                    <span style="color:var(--ink3)">= Expected</span>
                    <span class="recon-val" style="color:var(--blue)" x-text="'Af '+fmt(selected?.expected_cash||0)"></span>
                </div>
                <div class="recon-row" x-show="selected?.actual_cash!==null">
                    <span>Actual (counted)</span>
                    <span class="recon-val" x-text="'Af '+fmt(selected?.actual_cash||0)"></span>
                </div>
                <div class="recon-row" x-show="selected?.discrepancy!==null">
                    <span :style="(selected?.discrepancy||0)>=0?'color:var(--green)':'color:var(--red)'">
                        Discrepancy
                    </span>
                    <span class="recon-val"
                          :style="(selected?.discrepancy||0)>=0?'color:var(--green)':'color:var(--red)'"
                          x-text="((selected?.discrepancy||0)>=0?'+':'')+' Af '+fmt(selected?.discrepancy||0)">
                    </span>
                </div>
            </div>

            {{-- Discrepancy note --}}
            <div class="dp-section" x-show="selected?.discrepancy_note">
                <div class="dp-section-title"><i class="fas fa-pen"></i> Discrepancy Note</div>
                <div style="font-size:12.5px;color:var(--ink2);line-height:1.6;background:var(--adim);border:1px solid rgba(217,119,6,.2);padding:10px 12px;border-radius:var(--rsm)"
                     x-text="selected?.discrepancy_note"></div>
            </div>

            {{-- Top items sold in shift --}}
            <div class="dp-section">
                <div class="dp-section-title"><i class="fas fa-trophy"></i> Top Items This Shift</div>
                <div x-show="detailLoading" style="text-align:center;padding:1rem;color:var(--ink3);font-size:12px">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div x-show="!detailLoading">
                    <div x-show="topItems.length===0" style="text-align:center;padding:.75rem;color:var(--ink4);font-size:12px">
                        No items sold in this shift.
                    </div>
                    <template x-for="item in topItems" :key="item.sku">
                        <div class="mini-item">
                            <span class="mi-name" x-text="item.name"></span>
                            <span class="mi-qty" x-text="'×'+item.qty"></span>
                            <span class="mi-amt" x-text="'Af '+fmt(item.revenue)"></span>
                        </div>
                    </template>
                </div>
            </div>

        </div>
        <div class="dp-foot">
            <a :href="'/pos/shifts/'+selected?.id+'/report'"
               class="btn btn-primary" style="flex:1">
                <i class="fas fa-file-alt"></i> Full Report
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
    shifts: [], pagination: {}, loading: true,
    search: '', dateFrom: '', dateTo: '', filterUser: '', tab: 'all',
    currentPage: 1,
    selected: null, topItems: [], detailLoading: false,

    urls: {
        list:   '{{ route("pos.shifts.index") }}',
        detail: '{{ url("pos/shifts") }}',
        csrf:   document.querySelector('meta[name=csrf-token]').content,
    },

    async init() {
        const today = new Date().toISOString().split('T')[0];
        this.dateFrom = new Date(Date.now() - 30*86400000).toISOString().split('T')[0];
        this.dateTo   = today;
        await this.loadShifts();
    },

    async loadShifts() {
        this.loading = true;
        try {
            const p = new URLSearchParams({ q: this.search, from: this.dateFrom, to: this.dateTo, user: this.filterUser, tab: this.tab, page: this.currentPage });
            const r = await fetch(this.urls.list + '?' + p, { headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' } });
            const d = await r.json();
            this.shifts     = d.data;
            this.pagination = d.meta;
        } catch(e) { console.error(e); }
        finally { this.loading = false; }
    },

    goPage(p) { if (p < 1 || p > this.pagination.last_page) return; this.currentPage = p; this.loadShifts(); },

    async openDetail(s) {
        this.selected      = s;
        this.topItems      = [];
        this.detailLoading = true;
        try {
            const r = await fetch(`${this.urls.detail}/${s.id}/detail`, { headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' } });
            const d = await r.json();
            this.topItems = d.top_items;
            this.selected = { ...this.selected, ...d.shift };
        } catch(e) { console.error(e); }
        finally { this.detailLoading = false; }
    },

    initials(name) { if (!name) return '?'; return name.trim().split(' ').map(w=>w[0]).join('').toUpperCase().slice(0,2); },
    avatarColor(name) { const c=['#2f5de8','#0891b2','#15803d','#d97706','#7c3aed','#dc2626']; return c[(name?.charCodeAt(0)||0)%c.length]; },
    fmt(n) { return Number(n||0).toLocaleString('en-US',{minimumFractionDigits:0,maximumFractionDigits:0}); },
}));
});
</script>
@endpush
