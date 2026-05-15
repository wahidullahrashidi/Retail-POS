@extends('layouts.app')

@push('styles')
    @vite(['resources/css/pages/openShift.css'])
@endpush

@section('content')
<div class="shift-open-page">
    <div class="open-card">

        {{-- Logo --}}
        <div class="oc-logo">
            <div class="oc-logo-mark">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2L14.5 8.5L21 10L14.5 13.5L12 20L9.5 13.5L3 10L9.5 8.5Z" fill="white"/>
                </svg>
            </div>
            <div>
                <div class="oc-logo-text">Afghan POS</div>
                <div class="oc-logo-sub">Retail Management</div>
            </div>
        </div>

        {{-- Greeting --}}
        <div class="oc-greeting">
            <h1>Open Your Shift</h1>
            <p>Enter your starting cash to begin processing sales for today.</p>
        </div>

        {{-- Live clock --}}
        <div class="oc-clock">
            <div class="oc-clock-time" id="openShiftClock">--:--:--</div>
            <div class="oc-clock-date">
                <div class="oc-clock-gregorian">{{ \Carbon\Carbon::now()->format('l, F d') }}</div>
                <div class="oc-clock-hijri">{{ $hijriDate ?? '' }}</div>
            </div>
        </div>

        {{-- User strip --}}
        @php
            $user  = Auth::user();
            $parts = array_values(array_filter(explode(' ', trim($user->name))));
            $initials = count($parts) === 1
                ? strtoupper(substr($parts[0], 0, 2))
                : strtoupper(collect($parts)->map(fn($p) => substr($p,0,1))->join(''));
        @endphp
        <div class="oc-user-strip">
            <div class="oc-user-av">{{ $initials }}</div>
            <div>
                <div class="oc-user-name">{{ $user->name }}</div>
                <div class="oc-user-role">{{ $user->role?->display_name ?? 'Cashier' }}</div>
            </div>
            <div class="oc-user-time">{{ \Carbon\Carbon::now()->format('H:i') }}</div>
        </div>

        {{-- Error --}}
        @if(session('error'))
        <div class="oc-error">
            <i class="fas fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('shift.open') }}">
            @csrf

            <div class="oc-field">
                <label class="oc-label">Starting Cash in Drawer (Af)</label>
                <div class="oc-input-wrap">
                    <span class="oc-prefix">Af</span>
                    <input type="number"
                           name="starting_cash"
                           class="oc-input"
                           placeholder="0"
                           min="0" step="0.01"
                           value="{{ old('starting_cash') }}"
                           id="startingCashInput"
                           autofocus required>
                </div>
                @error('starting_cash')
                    <div style="font-size:12px;color:#fca5a5;margin-top:5px">{{ $message }}</div>
                @enderror

                {{-- Quick amounts --}}
                <div class="oc-quick">
                    @foreach([0, 1000, 2000, 5000, 10000] as $amt)
                    <button type="button" class="oc-quick-btn"
                            onclick="document.getElementById('startingCashInput').value = {{ $amt }}">
                        {{ $amt === 0 ? 'Zero' : number_format($amt) }}
                    </button>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="oc-submit">
                <i class="fas fa-play-circle"></i>
                Start Shift
            </button>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function tick() {
    const el = document.getElementById('openShiftClock');
    if (el) el.textContent = new Date().toLocaleTimeString('en-GB');
    setTimeout(tick, 1000);
})();
</script>
@endpush