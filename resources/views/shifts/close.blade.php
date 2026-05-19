@extends('layouts.app')

@push('styles')
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))) 
        @vite(['resources/css/pages/closeShift.css']) 
    @endif
@endpush

@section('content')
<div class="shift-close-page" x-data="closeShift()" x-init="init()">
    <div class="close-card">

        {{-- Header --}}
        <div class="cc-header">
            <div class="cc-header-icon">
                <i class="fas fa-stop-circle"></i>
            </div>

            <div>
                <h1>{{ __('messages.close_your_shift') }}</h1>
                <p>{{ __('messages.count_cash_submit_close') }}</p>
            </div>

            <div class="cc-duration">
                <div class="cc-duration-val" id="shiftDuration">--:--</div>
                <div class="cc-duration-label">
                    {{ __('messages.duration') }}
                </div>
            </div>
        </div>

        <div class="cc-body">

            {{-- Shift summary stats --}}
            <div class="cc-summary">

                <div class="cc-stat">
                    <div class="cc-stat-label">
                        {{ __('messages.starting_cash') }}
                    </div>

                    <div class="cc-stat-val">
                        Af {{ number_format($shift->starting_cash, 0) }}
                    </div>
                </div>

                <div class="cc-stat">
                    <div class="cc-stat-label">
                        {{ __('messages.cash_sales') }}
                    </div>

                    <div class="cc-stat-val green">
                        Af {{ number_format($cashSales ?? 0, 0) }}
                    </div>
                </div>

                <div class="cc-stat">
                    <div class="cc-stat-label">
                        {{ __('messages.transactions') }}
                    </div>

                    <div class="cc-stat-val amber">
                        {{ $transactionCount ?? 0 }}
                    </div>
                </div>

            </div>

            {{-- Reconciliation breakdown --}}
            <div class="cc-recon">

                <div class="cc-recon-title">
                    {{ __('messages.cash_breakdown') }}
                </div>

                <div class="cc-recon-row">
                    <span class="cc-recon-label">
                        {{ __('messages.starting_cash') }}
                    </span>

                    <span class="cc-recon-val">
                        Af {{ number_format($shift->starting_cash, 0) }}
                    </span>
                </div>

                <div class="cc-recon-row">
                    <span class="cc-recon-label">
                        {{ __('messages.cash_sales_today') }}
                    </span>

                    <span class="cc-recon-val" style="color:#6ee7b7">
                        + Af {{ number_format($cashSales ?? 0, 0) }}
                    </span>
                </div>

                <div class="cc-recon-row">
                    <span class="cc-recon-label">
                        {{ __('messages.cash_refunds') }}
                    </span>

                    <span class="cc-recon-val" style="color:#fca5a5">
                        − Af {{ number_format($cashRefunds ?? 0, 0) }}
                    </span>
                </div>

            </div>

            {{-- Expected vs Actual --}}
            <div class="cc-comparison">

                <div class="cc-comp-side">
                    <div class="cc-comp-label">
                        {{ __('messages.expected_in_drawer') }}
                    </div>

                    <div class="cc-comp-val expected">
                        Af {{ number_format($expectedCash, 0) }}
                    </div>
                </div>

                <div style="display:flex;align-items:center;color:rgba(255,255,255,.2);font-size:20px">
                    <i class="fas fa-arrows-left-right"></i>
                </div>

                <div class="cc-comp-side">
                    <div class="cc-comp-label">
                        {{ __('messages.actual_you_count') }}
                    </div>

                    <div class="cc-comp-val" style="color:#fff" x-text="actualFormatted">
                        Af —
                    </div>
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

                    <label class="cc-label">
                        {{ __('messages.actual_cash_in_drawer') }}
                    </label>

                    <div class="cc-input-wrap">

                        <span class="cc-prefix">Af</span>

                        <input type="number"
                               name="actual_cash"
                               class="cc-input"
                               placeholder="0"
                               min="0"
                               step="0.01"
                               x-model="actualCash"
                               id="actualCashInput"
                               autofocus
                               required>

                    </div>

                    @error('actual_cash')
                        <div style="font-size:12px;color:#fca5a5;margin-top:5px">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="cc-field"
                     x-show="discrepancy !== 0 && actualCash !== ''"
                     x-cloak>

                    <label class="cc-label">
                        {{ __('messages.discrepancy_note') }}
                    </label>

                    <textarea name="discrepancy_note"
                              class="cc-textarea"
                              placeholder="{{ __('messages.explain_discrepancy') }}"
                              x-model="discrepancyNote"></textarea>

                </div>

            </form>

        </div>

        {{-- Footer --}}
        <div class="cc-footer">

            <a href="{{ route('pos.shifts.page') }}" class="cc-btn-cancel">
                <i class="fas fa-arrow-left"></i>
                {{ __('messages.back') }}
            </a>

            <button type="submit"
                    form="closeForm"
                    class="cc-btn-close"
                    :disabled="actualCash === ''">

                <i class="fas fa-stop-circle"></i>

                {{ __('messages.close_shift_logout') }}

            </button>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function closeShift() {
    return {
        actualCash: '',
        discrepancyNote: '',
        expectedCash: {{ $expectedCash ?? 0 }},
        openedAt: '{{ $shift->opened_at }}',

        get discrepancy() {
            if (this.actualCash === '' || isNaN(parseFloat(this.actualCash))) {
                return 0;
            }

            return parseFloat(this.actualCash) - this.expectedCash;
        },

        get discClass() {
            if (this.discrepancy > 0) return 'positive';
            if (this.discrepancy < 0) return 'negative';

            return 'zero';
        },

        get discLabel() {
            if (this.discrepancy > 0) {
                return '{{ __("messages.surplus") }}';
            }

            if (this.discrepancy < 0) {
                return '{{ __("messages.shortage") }}';
            }

            return '{{ __("messages.exact_match") }}';
        },

        get discFormatted() {
            const abs = Math.abs(this.discrepancy);

            const prefix =
                this.discrepancy > 0
                    ? '+'
                    : this.discrepancy < 0
                        ? '-'
                        : '';

            return `${prefix} Af ${abs.toLocaleString('en-US')}`;
        },

        get actualFormatted() {
            if (this.actualCash === '') {
                return 'Af —';
            }

            return 'Af ' + parseFloat(this.actualCash || 0)
                .toLocaleString('en-US', {
                    maximumFractionDigits: 0
                });
        },

        init() {
            this.startDurationTimer();
        },

        startDurationTimer() {
            const opened = new Date(this.openedAt.replace(' ', 'T'));

            const tick = () => {

                const diff = Math.floor(
                    (Date.now() - opened.getTime()) / 1000
                );

                const h = Math.floor(diff / 3600);

                const m = Math.floor((diff % 3600) / 60);

                const s = diff % 60;

                const el = document.getElementById('shiftDuration');

                if (el) {
                    el.textContent =
                        `${String(h).padStart(2,'0')}:` +
                        `${String(m).padStart(2,'0')}:` +
                        `${String(s).padStart(2,'0')}`;
                }
            };

            tick();

            setInterval(tick, 1000);
        },
    };
}
</script>
@endpush