
{{-- ════════ HEADER ════════ --}}
    <header id="app-header">
        <div class="header-left">
            {{-- Hamburger (mobile) --}}
            <button class="hdr-hamburger" @click="mobileOpen = !mobileOpen">
                <i class="fas fa-bars"></i>
            </button>

            {{-- Date --}}
            <div class="text-sm text-gray-500">
            <div class="font-medium text-gray-900">{{ $jalaliDate }}</div>
            <div class="text-xs">{{ $gregorianDate }}</div>
        </div>
        </div>

        <div class="header-right">

            {{-- Theme toggle --}}
            <button class="theme-toggle" @click="toggleTheme()" :title="darkMode ? 'Light mode' : 'Dark mode'">
                <i :class="darkMode ? 'fas fa-sun' : 'fas fa-moon'"></i>
            </button>

            <div class="hdr-divider"></div>

            {{-- Language selector --}}
            <div class="lang-dropdown" x-data="{ open: false }" @click.outside="open=false">
                <button class="lang-btn" @click="open = !open">
                    <span x-text="currentLangLabel()"></span>
                    <i class="fas fa-chevron-down" style="font-size:9px;opacity:.6"></i>
                </button>
                <div class="lang-menu" x-show="open" x-cloak>
                    <button class="lang-option" :class="lang==='en'?'active':''" @click="setLang('en'); open=false">
                        English
                    </button>
                    <button class="lang-option" :class="lang==='ps'?'active':''" @click="setLang('ps'); open=false" style="font-family:serif">
                        پښتو
                    </button>
                    <button class="lang-option" :class="lang==='dr'?'active':''" @click="setLang('dr'); open=false" style="font-family:serif">
                        دری
                    </button>
                </div>
            </div>

            <div class="hdr-divider"></div>

            {{-- Notifications --}}
            {{-- <button class="hdr-btn">
                <i class="fas fa-bell"></i>
                <span class="badge"></span>
            </button> --}}

            {{-- User chip --}}
            @php
                $user  = Auth::user();
                $parts = array_values(array_filter(explode(' ', trim($user->name ?? ''))));
                $initials = count($parts) === 1
                    ? strtoupper(substr($parts[0], 0, 2))
                    : strtoupper(collect($parts)->map(fn($p) => substr($p, 0, 1))->join(''));
            @endphp
            <div class="user-chip">
                @if($user->photo)
                    <img src="{{ asset('storage/' . Auth::user()->photo) }}" class="w-8 h-8 rounded-full">
                @else
                    <div class="user-av" style="background:#3b5bdb;font-size:11px;font-weight:700;color:#fff;display:flex;align-items:center;justify-content:center;font-family:'Plus Jakarta Sans',sans-serif">
                        {{ $initials }}
                    </div>
                @endif
                <span class="user-chip-name">{{ $user->name }}</span>
            </div>

        </div>
    </header>