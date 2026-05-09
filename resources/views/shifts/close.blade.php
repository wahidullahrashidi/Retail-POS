@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@300;400;500;600;700&family=Nunito+Sans:wght@300;400;500;600&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
    --navy:    #0f1c3a;
    --blue:    #3b82f6;
    --blue2:   #2563eb;
    --green:   #10b981;
    --grdim:   rgba(16,185,129,.12);
    --red:     #ef4444;
    --rdim:    rgba(239,68,68,.12);
    --amber:   #f59e0b;
    --adim:    rgba(245,158,11,.12);
    --mono:    'Roboto Mono', monospace;
    --body:    'Nunito Sans', sans-serif;
    --display: 'Unbounded', sans-serif;
}

.shift-close-page {
    min-height: 100vh;
    background: var(--navy);
    display: flex; align-items: center; justify-content: center;
    padding: 2rem;
    position: relative; overflow: hidden;
    font-family: var(--body);
}
.shift-close-page::before {
    content: '';
    position: absolute; inset: 0;
    background-image:
        linear-gradient(rgba(239,68,68,.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(239,68,68,.03) 1px, transparent 1px);
    background-size: 48px 48px;
}
.shift-close-page::after {
    content: '';
    position: absolute;
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(239,68,68,.08) 0%, transparent 70%);
    bottom: -200px; right: -200px;
    pointer-events: none;
}

.close-card {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 24px;
    width: 100%; max-width: 560px;
    backdrop-filter: blur(20px);
    position: relative; z-index: 1;
    box-shadow: 0 32px 80px rgba(0,0,0,.5), 0 0 0 1px rgba(255,255,255,.05);
    animation: cardReveal .6s cubic-bezier(.2,.8,.36,1) both;
    overflow: hidden;
}
@keyframes cardReveal {
    from { opacity: 0; transform: translateY(24px) scale(.97); }
    to   { opacity: 1; transform: none; }
}

/* Card header strip */
.cc-header {
    padding: 1.75rem 2rem;
    background: rgba(239,68,68,.08);
    border-bottom: 1px solid rgba(239,68,68,.15);
    display: flex; align-items: center; gap: 14px;
}
.cc-header-icon {
    width: 48px; height: 48px;
    background: rgba(239,68,68,.2);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: #fca5a5; flex-shrink: 0;
}
.cc-header h1 {
    font-family: var(--display);
    font-size: 20px; font-weight: 600;
    color: #fff; letter-spacing: -.3px;
    margin-bottom: 4px;
}
.cc-header p { font-size: 12px; color: rgba(255,255,255,.4); }

/* Shift duration badge */
.cc-duration {
    margin-left: auto; text-align: right;
}
.cc-duration-val {
    font-family: var(--mono);
    font-size: 18px; font-weight: 500; color: #fff;
}
.cc-duration-label { font-size: 10px; color: rgba(255,255,255,.3); text-transform: uppercase; letter-spacing: .08em; margin-top: 2px; }

/* Card body */
.cc-body { padding: 1.75rem 2rem; }

/* Summary grid */
.cc-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 1.75rem;
}
.cc-stat {
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 10px;
    padding: .85rem;
}
.cc-stat-label {
    font-size: 9px; font-weight: 600;
    color: rgba(255,255,255,.35);
    text-transform: uppercase; letter-spacing: .1em;
    margin-bottom: 6px;
}
.cc-stat-val {
    font-family: var(--mono);
    font-size: 16px; font-weight: 500;
    color: #fff; letter-spacing: -.3px;
}
.cc-stat-val.green { color: #6ee7b7; }
.cc-stat-val.amber { color: #fcd34d; }

/* Cash reconciliation */
.cc-recon {
    background: rgba(0,0,0,.25);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
.cc-recon-title {
    font-size: 11px; font-weight: 600;
    color: rgba(255,255,255,.4);
    text-transform: uppercase; letter-spacing: .1em;
    margin-bottom: 1rem;
}
.cc-recon-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,.05);
    font-size: 13px;
}
.cc-recon-row:last-child { border-bottom: none; }
.cc-recon-label { color: rgba(255,255,255,.5); }
.cc-recon-val { font-family: var(--mono); font-weight: 500; color: #fff; }

/* Expected vs Actual */
.cc-comparison {
    background: rgba(0,0,0,.2);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    display: flex; gap: 10px;
}
.cc-comp-side {
    flex: 1;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 8px;
    padding: .85rem;
    text-align: center;
}
.cc-comp-label {
    font-size: 10px; color: rgba(255,255,255,.35);
    text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px;
}
.cc-comp-val {
    font-family: var(--mono);
    font-size: 22px; font-weight: 500; color: #fff;
    letter-spacing: -.5px;
}
.cc-comp-val.expected { color: #93c5fd; }

/* Discrepancy indicator */
.cc-discrepancy {
    padding: 10px 14px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1rem; font-size: 13px;
}
.cc-discrepancy.positive { background: rgba(16,185,129,.12); border: 1px solid rgba(16,185,129,.2); }
.cc-discrepancy.negative { background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.2); }
.cc-discrepancy.zero     { background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08); }
.cc-disc-label { color: rgba(255,255,255,.6); font-weight: 500; }
.cc-disc-val { font-family: var(--mono); font-size: 16px; font-weight: 700; }
.cc-discrepancy.positive .cc-disc-val { color: #6ee7b7; }
.cc-discrepancy.negative .cc-disc-val { color: #fca5a5; }
.cc-discrepancy.zero     .cc-disc-val { color: rgba(255,255,255,.6); }

/* Field */
.cc-field { margin-bottom: 1.1rem; }
.cc-label {
    display: block; font-size: 11px; font-weight: 600;
    color: rgba(255,255,255,.45);
    letter-spacing: .08em; text-transform: uppercase;
    margin-bottom: 7px;
}
.cc-input-wrap { position: relative; }
.cc-prefix {
    position: absolute; left: 14px; top: 50%;
    transform: translateY(-50%);
    font-family: var(--mono); font-size: 15px;
    color: rgba(255,255,255,.35); pointer-events: none;
}
.cc-input {
    width: 100%; padding: 13px 14px 13px 50px;
    background: rgba(255,255,255,.06);
    border: 1.5px solid rgba(255,255,255,.1);
    border-radius: 10px;
    font-family: var(--mono);
    font-size: 18px; font-weight: 500;
    color: #fff; outline: none;
    transition: border .15s, box-shadow .15s, background .15s;
}
.cc-input:focus {
    border-color: var(--blue);
    background: rgba(59,130,246,.08);
    box-shadow: 0 0 0 3px rgba(59,130,246,.15);
}
.cc-input::placeholder { color: rgba(255,255,255,.2); }
.cc-textarea {
    width: 100%; padding: 12px 14px;
    background: rgba(255,255,255,.06);
    border: 1.5px solid rgba(255,255,255,.1);
    border-radius: 10px;
    font-family: var(--body); font-size: 13px;
    color: #fff; outline: none; resize: none; min-height: 72px;
    transition: border .15s, box-shadow .15s;
}
.cc-textarea:focus {
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(59,130,246,.15);
}
.cc-textarea::placeholder { color: rgba(255,255,255,.2); }

/* Footer buttons */
.cc-footer {
    padding: 1.25rem 2rem;
    border-top: 1px solid rgba(255,255,255,.07);
    display: flex; gap: 10px;
}
.cc-btn-close {
    flex: 2; padding: 13px;
    background: var(--red);
    border: none; border-radius: 10px;
    font-family: var(--display);
    font-size: 13px; font-weight: 600;
    color: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    box-shadow: 0 4px 20px rgba(239,68,68,.35);
    transition: all .2s;
}
.cc-btn-close:hover { background: #dc2626; transform: translateY(-1px); box-shadow: 0 8px 28px rgba(239,68,68,.45); }
.cc-btn-cancel {
    flex: 1; padding: 13px;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 10px;
    font-family: var(--display);
    font-size: 13px; font-weight: 500;
    color: rgba(255,255,255,.6); cursor: pointer;
    transition: all .15s; text-decoration: none;
    display: flex; align-items: center; justify-content: center;
}
.cc-btn-cancel:hover { background: rgba(255,255,255,.1); color: #fff; }
</style>
@endpush

@section('content')
<div class="shift-close-page" x-data="closeShift()" x-init="init()">
    <div class="close-card">

        {{-- Header --}}
        <div class="cc-header">
            <div class="cc-header-icon"><i class="fas fa-stop-circle"></i></div>
            <div>
                <h1>Close Your Shift</h1>
                <p>Count the cash in your drawer and submit to close.</p>
            </div>
            <div class="cc-duration">
                <div class="cc-duration-val" id="shiftDuration">--:--</div>
                <div class="cc-duration-label">Duration</div>
            </div>
        </div>

        <div class="cc-body">

            {{-- Shift summary stats --}}
            <div class="cc-summary">
                <div class="cc-stat">
                    <div class="cc-stat-label">Starting Cash</div>
                    <div class="cc-stat-val">Af {{ number_format($shift->starting_cash, 0) }}</div>
                </div>
                <div class="cc-stat">
                    <div class="cc-stat-label">Cash Sales</div>
                    <div class="cc-stat-val green">Af {{ number_format($cashSales ?? 0, 0) }}</div>
                </div>
                <div class="cc-stat">
                    <div class="cc-stat-label">Transactions</div>
                    <div class="cc-stat-val amber">{{ $transactionCount ?? 0 }}</div>
                </div>
            </div>

            {{-- Reconciliation breakdown --}}
            <div class="cc-recon">
                <div class="cc-recon-title">Cash Breakdown</div>
                <div class="cc-recon-row">
                    <span class="cc-recon-label">Starting Cash</span>
                    <span class="cc-recon-val">Af {{ number_format($shift->starting_cash, 0) }}</span>
                </div>
                <div class="cc-recon-row">
                    <span class="cc-recon-label">+ Cash Sales Today</span>
                    <span class="cc-recon-val" style="color:#6ee7b7">+ Af {{ number_format($cashSales ?? 0, 0) }}</span>
                </div>
                <div class="cc-recon-row">
                    <span class="cc-recon-label">− Cash Refunds</span>
                    <span class="cc-recon-val" style="color:#fca5a5">− Af {{ number_format($cashRefunds ?? 0, 0) }}</span>
                </div>
            </div>

            {{-- Expected vs Actual --}}
            <div class="cc-comparison">
                <div class="cc-comp-side">
                    <div class="cc-comp-label">Expected in Drawer</div>
                    <div class="cc-comp-val expected">Af {{ number_format($expectedCash, 0) }}</div>
                </div>
                <div style="display:flex;align-items:center;color:rgba(255,255,255,.2);font-size:20px">
                    <i class="fas fa-arrows-left-right"></i>
                </div>
                <div class="cc-comp-side">
                    <div class="cc-comp-label">Actual (you count)</div>
                    <div class="cc-comp-val" style="color:#fff" x-text="actualFormatted">Af —</div>
                </div>
            </div>

            {{-- Live discrepancy --}}
            <div class="cc-discrepancy" :class="discClass" x-show="actualCash !== ''">
                <span class="cc-disc-label" x-text="discLabel"></span>
                <span class="cc-disc-val" x-text="discFormatted"></span>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('shift.close') }}" id="closeForm">
                @csrf

                <div class="cc-field">
                    <label class="cc-label">Actual Cash in Drawer (Af) *</label>
                    <div class="cc-input-wrap">
                        <span class="cc-prefix">Af</span>
                        <input type="number"
                               name="actual_cash"
                               class="cc-input"
                               placeholder="0"
                               min="0" step="0.01"
                               x-model="actualCash"
                               id="actualCashInput"
                               autofocus required>
                    </div>
                    @error('actual_cash')
                        <div style="font-size:12px;color:#fca5a5;margin-top:5px">{{ $message }}</div>
                    @enderror
                </div>

                <div class="cc-field" x-show="discrepancy !== 0 && actualCash !== ''" x-cloak>
                    <label class="cc-label">Discrepancy Note</label>
                    <textarea name="discrepancy_note"
                              class="cc-textarea"
                              placeholder="Explain the discrepancy…"
                              x-model="discrepancyNote"></textarea>
                </div>

            </form>
        </div>

        {{-- Footer --}}
        <div class="cc-footer">
            <a href="{{ route('pos.shifts.page') }}" class="cc-btn-cancel">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" form="closeForm" class="cc-btn-close"
                    :disabled="actualCash === ''">
                <i class="fas fa-stop-circle"></i>
                Close Shift & Logout
            </button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function closeShift() {
    return {
        actualCash:      '',
        discrepancyNote: '',
        expectedCash:    {{ $expectedCash ?? 0 }},
        openedAt:        '{{ $shift->opened_at }}',

        get discrepancy() {
            if (this.actualCash === '' || isNaN(parseFloat(this.actualCash))) return 0;
            return parseFloat(this.actualCash) - this.expectedCash;
        },
        get discClass() {
            if (this.discrepancy > 0) return 'positive';
            if (this.discrepancy < 0) return 'negative';
            return 'zero';
        },
        get discLabel() {
            if (this.discrepancy > 0) return 'Surplus';
            if (this.discrepancy < 0) return 'Shortage';
            return 'Exact Match';
        },
        get discFormatted() {
            const abs = Math.abs(this.discrepancy);
            const prefix = this.discrepancy > 0 ? '+' : this.discrepancy < 0 ? '-' : '';
            return `${prefix} Af ${abs.toLocaleString('en-US')}`;
        },
        get actualFormatted() {
            if (this.actualCash === '') return 'Af —';
            return 'Af ' + parseFloat(this.actualCash || 0).toLocaleString('en-US', { maximumFractionDigits: 0 });
        },

        init() {
            this.startDurationTimer();
        },

        startDurationTimer() {
            const opened = new Date(this.openedAt.replace(' ', 'T'));
            const tick = () => {
                const diff  = Math.floor((Date.now() - opened.getTime()) / 1000);
                const h     = Math.floor(diff / 3600);
                const m     = Math.floor((diff % 3600) / 60);
                const s     = diff % 60;
                const el    = document.getElementById('shiftDuration');
                if (el) el.textContent = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            };
            tick();
            setInterval(tick, 1000);
        },
    };
}
</script>
@endpush