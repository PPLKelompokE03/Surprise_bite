@props([
    'title' => 'SurpriseBite Test',
    'variant' => 'default',
    'activeNav' => null,
])

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    @if ($variant === 'marketing')
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    @else
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @endif

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    @if ($variant === 'marketing')
        <x-styles.marketing-animations />
    @endif
    <style>
        /* Smooth Global Hover Animasi */
        a, button, input[type="submit"], input[type="button"], .group, .card, .hover-target {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
            transition-timing-function: cubic-bezier(0.25, 0.8, 0.25, 1);
            transition-duration: 300ms;
        }
    </style>
</head>
<body
    @if ($variant === 'marketing')
        class="sb-marketing-canvas min-h-screen bg-[#fafafa] text-[#1e2939] antialiased [font-family:Inter,ui-sans-serif,system-ui,sans-serif]"
    @else
        class="min-h-screen bg-slate-50 text-slate-900 antialiased"
        style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif"
    @endif
>
    @php
        $auth = session('auth', []);
        $isAdmin = ($auth['role'] ?? null) === 'admin';
        $isLoggedIn = is_array($auth) && isset($auth['id']);
    @endphp

    @if ($variant === 'marketing')
        <header class="sticky top-0 z-50 border-b-2 border-[#dcfce7] bg-white/90 shadow-md shadow-black/10 backdrop-blur-md transition-[background-color,box-shadow] duration-500 ease-out">
            <div class="mx-auto flex h-20 w-full max-w-none items-center justify-between gap-3 px-2 sm:px-3 md:px-5 lg:px-8 xl:px-10 2xl:px-12">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3 transition-transform duration-300 hover:scale-[1.02]">
                    <span class="sb-logo-pulse relative flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-lg shadow-orange-500/25 ring-2 ring-orange-100/50 p-1">
                        <img src="{{ asset('images/logo.png') }}?v={{ time() }}" alt="SurpriseBite Logo" class="h-full w-full object-contain" />
                    </span>
                    <span class="text-[30px] font-black leading-none tracking-tight">
                        <span class="text-[#00a63e]">Surprise</span><span class="text-[#ff6900]">Bite</span>
                    </span>
                </a>

                @php
                    $navActive = fn (?string $key) => $activeNav === $key
                        ? 'text-[#00a63e] sb-nav-item--active'
                        : 'text-[#364153]';
                @endphp
                <nav class="hidden items-center gap-8 text-base font-bold lg:gap-10 md:flex">
                    <a href="{{ route('browse') }}" class="sb-nav-item {{ $navActive('browse') }} inline-flex items-center gap-1.5 transition hover:text-[#00a63e]">
                        <x-sb.icon name="search" class="h-4 w-4 opacity-80" /> Browse
                    </a>
                    <a href="{{ route('impact') }}" class="sb-nav-item {{ $navActive('impact') }} inline-flex items-center gap-1.5 transition hover:text-[#00a63e]">
                        <x-sb.icon name="globe" class="h-4 w-4 opacity-80" /> Impact
                    </a>
                    <a href="{{ route('about') }}" class="sb-nav-item {{ $navActive('about') }} inline-flex items-center gap-1.5 transition hover:text-[#00a63e]">
                        <x-sb.icon name="book-open" class="h-4 w-4 opacity-80" /> About
                    </a>
                </nav>

                <div class="flex items-center gap-3">
                    <button type="button" class="sb-hover-icon-btn flex h-12 w-12 items-center justify-center rounded-full bg-[#f3f4f6] text-[#364153] ring-1 ring-black/5 hover:bg-[#e5e7eb]" aria-label="Cari">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                        </svg>
                    </button>
                    @if ($isLoggedIn && !$isAdmin)
                        <a href="{{ route('cart.index') }}" class="sb-hover-icon-btn flex h-12 w-12 items-center justify-center rounded-full bg-[#f3f4f6] text-[#364153] ring-1 ring-black/5 hover:bg-[#e5e7eb] relative" aria-label="Keranjang">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </a>
                    @endif
                    @if ($isAdmin)
                        <div class="relative hidden sm:block">
                            <a href="{{ route('admin.dashboard') }}" class="sb-hover-header-btn inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-[#00a63e] to-[#00bc7d] px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-emerald-900/15">
                                <svg class="h-5 w-5 text-white/90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Admin
                                <svg class="h-4 w-4 text-white/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </a>
                        </div>
                    @endif
                    @if ($isLoggedIn)
                        <form method="post" action="{{ route('logout') }}" class="hidden sm:block">
                            @csrf
                            <button type="submit" class="sb-hover-header-btn rounded-full bg-gradient-to-r from-[#00a63e] to-[#00bc7d] px-6 py-2.5 text-base font-bold text-white shadow-md shadow-emerald-900/15">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('register') }}" class="sb-hover-header-outline inline-flex rounded-full border-2 border-[#00a63e] bg-white px-4 py-2.5 text-sm font-bold text-[#00a63e] shadow-sm hover:bg-[#f0fdf4] sm:px-5">
                            Daftar
                        </a>
                        <a href="{{ route('login') }}" class="sb-hover-header-btn inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-[#00a63e] to-[#00bc7d] px-5 py-2.5 text-base font-bold text-white shadow-md shadow-emerald-900/15 sm:px-6">
                            <svg class="h-5 w-5 text-white/90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Login
                        </a>
                    @endif
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-none px-2 sm:px-3 md:px-5 lg:px-8 xl:px-10 2xl:px-12">
            @if (session('status'))
                <div class="mb-4 mt-4 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 ring-1 ring-emerald-100">
                    {{ session('status') }}
                </div>
            @endif
            {{ $slot }}
        </main>
    @else
        <div class="bg-gradient-to-b from-emerald-600 to-emerald-500">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <header class="flex items-center justify-between py-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-white transition hover:opacity-90">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white p-1 ring-1 ring-white/30 shadow-md">
                            <img src="{{ asset('images/logo.png') }}?v={{ time() }}" alt="SurpriseBite Logo" class="h-full w-full object-contain" />
                        </span>
                        <div class="leading-tight">
                            <div class="text-lg font-semibold tracking-tight">SurpriseBite</div>
                            <div class="text-xs text-white/80">Save food, reduce waste</div>
                        </div>
                    </a>

                    <nav class="hidden items-center gap-6 text-sm font-medium text-white/90 sm:flex">
                        <a href="{{ route('browse') }}" @class(['hover:text-white', 'font-bold text-white underline decoration-2 underline-offset-4' => $activeNav === 'browse'])>Browse</a>
                        <a href="{{ route('impact') }}" @class(['hover:text-white', 'font-bold text-white underline decoration-2 underline-offset-4' => $activeNav === 'impact'])>Impact</a>
                        <a href="{{ route('about') }}" @class(['hover:text-white', 'font-bold text-white underline decoration-2 underline-offset-4' => $activeNav === 'about'])>About</a>
                    </nav>

                    <div class="flex items-center gap-2">
                        @if ($isAdmin)
                            <a href="{{ route('admin.dashboard') }}"
                               class="hidden rounded-full bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-white/20 sm:inline-flex">
                                Admin
                            </a>
                        @endif

                        @if ($isLoggedIn)
                            <span class="hidden rounded-full bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/20 sm:inline-flex">
                                {{ $auth['name'] ?? 'User' }}
                            </span>
                            <form method="post" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-full bg-slate-900/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-slate-900/20">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('register') }}"
                               class="hidden rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/25 hover:bg-white/15 sm:inline-flex">
                                Daftar
                            </a>
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center gap-2 rounded-full bg-slate-900/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-slate-900/20">
                                Login
                            </a>
                        @endif
                    </div>
                </header>
            </div>
        </div>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
            @if (session('status'))
                <div class="mb-4 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 ring-1 ring-emerald-100">
                    {{ session('status') }}
                </div>
            @endif
            {{ $slot }}
        </main>
    @endif

    <footer class="mt-16 border-t border-slate-200 bg-[#f9f9f9] text-[#1e2939]">
        <div class="mx-auto w-full max-w-[1400px] px-4 py-12 sm:px-6 lg:px-8 xl:px-12">
            <div class="mb-10 flex items-center gap-6">
                <!-- Social Media Icons (Facebook, Instagram, Twitter/X) -->
                <a href="#" class="text-slate-700 transition hover:text-emerald-700 hover:scale-110" aria-label="Facebook">
                    <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/></svg>
                </a>
                <a href="#" class="text-slate-700 transition hover:text-emerald-700 hover:scale-110" aria-label="Instagram">
                    <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                </a>
                <a href="#" class="text-slate-700 transition hover:text-emerald-700 hover:scale-110" aria-label="Twitter">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-12 md:grid-cols-3">
                <!-- Eksplorasi -->
                <div>
                    <h3 class="mb-5 text-sm font-bold tracking-widest text-[#1e2939] uppercase">Eksplorasi</h3>
                    <ul class="space-y-4 text-[15px] font-medium text-slate-600">
                        <li><a href="{{ route('home') }}" class="transition hover:text-emerald-700 hover:underline hover:underline-offset-4">Beranda Utama</a></li>
                        <li><a href="{{ route('browse') }}" class="transition hover:text-emerald-700 hover:underline hover:underline-offset-4">Cari Mystery Box</a></li>
                    </ul>
                </div>
                
                <!-- Tentang Kami -->
                <div>
                    <h3 class="mb-5 text-sm font-bold tracking-widest text-[#1e2939] uppercase">Perusahaan</h3>
                    <ul class="space-y-4 text-[15px] font-medium text-slate-600">
                        <li><a href="{{ route('about') }}" class="transition hover:text-emerald-700 hover:underline hover:underline-offset-4">Profil SurpriseBite</a></li>
                        <li><a href="{{ route('impact') }}" class="transition hover:text-emerald-700 hover:underline hover:underline-offset-4">Dampak Lingkungan</a></li>
                    </ul>
                </div>
                
                <!-- Akun & Akses -->
                <div>
                    <h3 class="mb-5 text-sm font-bold tracking-widest text-[#1e2939] uppercase">Akun Member</h3>
                    <ul class="space-y-4 text-[15px] font-medium text-slate-600">
                        @if ($isLoggedIn)
                            <li><a href="{{ route('home') }}" class="transition hover:text-emerald-700 hover:underline hover:underline-offset-4">Dashboard Akun</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="transition hover:text-emerald-700 hover:underline hover:underline-offset-4">Login Pengguna</a></li>
                            <li><a href="{{ route('register') }}" class="transition hover:text-emerald-700 hover:underline hover:underline-offset-4">Daftar Akun Baru</a></li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="mt-14 border-t border-slate-300 pt-8">
                <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                    <div class="flex items-center gap-2">
                        <x-sb.icon name="leaf" class="h-4 w-4 text-emerald-600" />
                        <p class="text-[15px] font-medium text-slate-600">Proyek Inovasi Food Waste</p>
                    </div>
                    <p class="text-sm font-semibold text-slate-500">© {{ date('Y') }} SurpriseBite - Tubes Project. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    @if ($variant === 'marketing')
        <x-scripts.marketing-motion />
    @endif
</body>
</html>
