<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="StockPro - Sistem Manajemen Stok & Inventaris Barang berbasis web modern dengan Livewire dan Sleek Dark Theme.">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <title>{{ config('app.name', 'StockPro') }} - @yield('title', 'Sistem Manajemen Stok & Inventaris')</title>

    <!-- DNS-Prefetch & Preconnect untuk memotong rantai request kritis -->
    <link rel="preconnect" href="https://uicdn.toast.com" crossorigin>
    <link rel="dns-prefetch" href="https://uicdn.toast.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <style>
        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #020617;
            color: #f8fafc;
        }
        .toastui-editor-defaultUI {
            border: 1px solid #334155 !important;
            background-color: #0f172a !important;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .toastui-editor-defaultUI-toolbar {
            background-color: #1e293b !important;
            border-bottom: 1px solid #334155 !important;
        }
        .toastui-editor-toolbar-icons {
            filter: invert(1) brightness(0.9);
        }
        .toastui-editor-main-container {
            background-color: #0f172a !important;
        }
        .toastui-editor-contents {
            color: #e2e8f0 !important;
            font-family: inherit !important;
        }
        .toastui-editor-contents p,
        .toastui-editor-contents h1,
        .toastui-editor-contents h2,
        .toastui-editor-contents h3,
        .toastui-editor-contents h4,
        .toastui-editor-contents li {
            color: #e2e8f0 !important;
        }
        .toastui-editor-contents code {
            background-color: #1e293b !important;
            color: #38bdf8 !important;
        }
        .toastui-editor-contents pre {
            background-color: #090d16 !important;
            border: 1px solid #334155 !important;
        }
        .toastui-editor-defaultUI .toastui-editor-mode-switch,
        .toastui-editor-defaultUI .toastui-editor-mode-switch .tab-item {
            background-color: #0f172a !important;
            color: #cbd5e1 !important;
            border-color: #334155 !important;
        }
        .toastui-editor-defaultUI .toastui-editor-mode-switch .tab-item.active {
            background-color: #1e293b !important;
            color: #ffffff !important;
        }
        .toastui-editor-defaultUI .toastui-editor-md-tab-container .tab-item,
        .toastui-editor-defaultUI .toastui-editor-tabs .tab-item {
            background-color: #1e293b !important;
            color: #cbd5e1 !important;
        }
        .toastui-editor-defaultUI .toastui-editor-md-tab-container .tab-item.active,
        .toastui-editor-defaultUI .toastui-editor-tabs .tab-item.active {
            color: #ffffff !important;
        }
        .toastui-editor-defaultUI .ProseMirror,
        .toastui-editor-md-container .toastui-editor-md-preview,
        .toastui-editor-ww-container .toastui-editor-contents {
            background-color: #0f172a !important;
            color: #e2e8f0 !important;
        }
        .toastui-editor-defaultUI .ProseMirror .placeholder,
        .toastui-editor-defaultUI .placeholder {
            color: #cbd5e1 !important;
        }
        .toastui-editor-md-container .ProseMirror [class*="toastui-editor-md-"] {
            color: #e2e8f0 !important;
        }
        .toastui-editor-md-container .ProseMirror .toastui-editor-md-heading,
        .toastui-editor-md-container .ProseMirror .toastui-editor-md-strong { color: #93c5fd !important; }
        .toastui-editor-md-container .ProseMirror .toastui-editor-md-link,
        .toastui-editor-md-container .ProseMirror .toastui-editor-md-link-url { color: #a5b4fc !important; }
        .toastui-editor-md-container .ProseMirror .toastui-editor-md-code,
        .toastui-editor-md-container .ProseMirror .toastui-editor-md-code-block { color: #7dd3fc !important; }
        .toastui-editor-md-container .ProseMirror .toastui-editor-md-delimiter,
        .toastui-editor-md-container .ProseMirror .toastui-editor-md-meta { color: #cbd5e1 !important; }
        .toastui-editor-contents a { color: #a5b4fc !important; }
        .toastui-editor-contents blockquote,
        .toastui-editor-contents blockquote p { color: #cbd5e1 !important; }
        .toastui-editor-contents table th { background-color: #1e293b !important; color: #f8fafc !important; }
        .toastui-editor-contents table td { color: #e2e8f0 !important; }
        .toastui-editor-popup, .toastui-editor-popup-body { background-color: #0f172a !important; color: #e2e8f0 !important; }
        .toastui-editor-popup label, .toastui-editor-popup-body label { color: #e2e8f0 !important; }
        .toastui-editor-popup input[type="text"], .toastui-editor-popup input[type="number"] {
            background-color: #020617 !important; color: #f8fafc !important; border-color: #475569 !important;
        }
        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }
    </style>

    @livewireStyles
</head>
<body class="h-full bg-slate-950 text-slate-100 antialiased selection:bg-indigo-600 selection:text-white flex flex-col min-h-screen">
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[60] focus:px-4 focus:py-2 focus:rounded-lg focus:bg-indigo-700 focus:text-white focus:font-semibold focus:outline-none focus:ring-2 focus:ring-white">
        Lewati ke konten utama
    </a>
    <div class="min-h-full flex flex-col flex-1">
        <!-- Navigation -->
        <nav class="sticky top-0 z-40 bg-slate-900/90 backdrop-blur-md border-b border-slate-800 shadow-sm" aria-label="Navigasi Utama">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center gap-8">
                        <a href="{{ url('/') }}" class="flex items-center gap-2.5 text-white font-bold tracking-tight text-lg group" aria-label="StockPro Beranda">
                            <span class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-sm shadow-indigo-500/20 shadow-md group-hover:bg-indigo-700 transition">
                                ⚡
                            </span>
                            <span class="tracking-wider">Stock<span class="text-indigo-400">Pro</span></span>
                        </a>

                        <div class="hidden sm:flex items-center space-x-1.5">
                            @auth
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.users') }}"
                                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.*') ? 'bg-indigo-600/15 text-indigo-200 border border-indigo-500/30 font-semibold' : 'text-slate-100 hover:text-white hover:bg-slate-800/60' }}">
                                        Pengguna &amp; Stok
                                    </a>
                                @else
                                    <a href="{{ route('stocks.index') }}"
                                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('stocks.*') ? 'bg-indigo-600/15 text-indigo-200 border border-indigo-500/30 font-semibold' : 'text-slate-100 hover:text-white hover:bg-slate-800/60' }}">
                                        Manajemen Stok
                                    </a>
                                    <a href="{{ route('profile') }}"
                                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('profile') ? 'bg-indigo-600/15 text-indigo-200 border border-indigo-500/30 font-semibold' : 'text-slate-100 hover:text-white hover:bg-slate-800/60' }}">
                                        Profil Akun
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        @auth
                            <div class="flex items-center gap-3">
                                <!-- TANPA aria-label agar tidak konflik dengan visible text nama user -->
                                <a href="{{ route('profile') }}" class="flex items-center gap-2.5 py-1 px-2 rounded-lg hover:bg-slate-800/60 transition group">
                                    <img src="{{ auth()->user()->profilePhotoUrl() }}" alt="{{ auth()->user()->name }}"
                                         class="w-7 h-7 rounded-full object-cover ring-1 ring-slate-700 group-hover:ring-indigo-500 transition">
                                    <div class="hidden md:block text-left text-xs">
                                        <div class="text-slate-100 font-medium leading-none">{{ auth()->user()->name }}</div>
                                        <div class="text-slate-200 text-[10px] leading-tight font-mono mt-0.5">&#64;<span>{{ auth()->user()->username }}</span></div>
                                    </div>
                                    <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded border border-indigo-400 bg-indigo-950 text-indigo-100 font-bold">
                                        {{ auth()->user()->role }}
                                    </span>
                                </a>

                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <!-- Tombol logout memiliki teks "Keluar", jadi tidak perlu aria-label tambahan -->
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-200 hover:text-white hover:bg-rose-950/60 border border-rose-800 transition">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="px-3.5 py-1.5 text-xs font-semibold text-slate-100 hover:text-white rounded-lg hover:bg-slate-800 transition">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="px-3.5 py-1.5 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm transition">
                                Daftar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Workspace -->
        <main id="main-content" tabindex="-1" class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 flex-1 focus:outline-none">
            @if(session('success'))
                <div role="status" class="mb-6 p-4 rounded-xl bg-emerald-950/80 border border-emerald-500 text-emerald-100 text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <span class="text-emerald-300 font-bold">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div role="alert" class="mb-6 p-4 rounded-xl bg-rose-950/80 border border-rose-500 text-rose-100 text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <span class="text-rose-300 font-bold">✕</span>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <!-- Footer High Contrast -->
        <footer class="border-t border-slate-800 bg-slate-950 py-6 text-center text-xs text-slate-200 font-mono">
            StockPro &copy; {{ date('Y') }} — Sleek Dark Theme Inventory Management System
        </footer>
    </div>


    <script>
        /* ===== A11y helper ===== */
        (function () {
            function normalizeHeadings(container) {
                var prev = 2;
                container.querySelectorAll('h1,h2,h3,h4,h5,h6').forEach(function (h) {
                    var orig = parseInt(h.tagName.charAt(1), 10);
                    var level = Math.min(Math.max(orig + 2, 3), prev + 1, 6);
                    if (h.getAttribute('aria-level') !== String(level)) {
                        h.setAttribute('role', 'heading');
                        h.setAttribute('aria-level', String(level));
                    }
                    prev = level;
                });
            }

            function labelEditorInternals(root) {
                root.querySelectorAll('.toastui-editor-md-container .ProseMirror').forEach(function (el) {
                    if (!el.getAttribute('aria-label')) {
                        el.setAttribute('role', 'textbox');
                        el.setAttribute('aria-multiline', 'true');
                        el.setAttribute('aria-label', 'Editor deskripsi (Markdown)');
                    }
                });
                root.querySelectorAll('.toastui-editor-ww-container .ProseMirror').forEach(function (el) {
                    if (!el.getAttribute('aria-label')) {
                        el.setAttribute('role', 'textbox');
                        el.setAttribute('aria-multiline', 'true');
                        el.setAttribute('aria-label', 'Editor deskripsi (WYSIWYG)');
                    }
                });
                root.querySelectorAll('.toastui-editor-defaultUI input, .toastui-editor-defaultUI textarea, .toastui-editor-defaultUI select').forEach(function (el) {
                    if (el.type === 'hidden' || el.getAttribute('aria-label') || el.getAttribute('aria-labelledby') || el.id && document.querySelector('label[for="' + el.id + '"]')) return;
                    el.setAttribute('aria-label', el.getAttribute('placeholder') || el.getAttribute('title') || 'Input editor');
                });
                root.querySelectorAll('.toastui-editor-defaultUI button').forEach(function (el) {
                    if (el.getAttribute('aria-label') || el.textContent.trim() !== '') return;
                    var t = el.getAttribute('title') || el.getAttribute('data-tooltip') || el.className.replace(/toastui-editor-|\s+/g, ' ').trim();
                    el.setAttribute('aria-label', t || 'Tombol editor');
                });
            }

            var timer = null;
            function run() {
                document.querySelectorAll('.toastui-editor-contents').forEach(normalizeHeadings);
                labelEditorInternals(document);
            }
            function schedule() { clearTimeout(timer); timer = setTimeout(run, 50); }

            document.addEventListener('DOMContentLoaded', function () {
                run();
                new MutationObserver(schedule).observe(document.body, { childList: true, subtree: true });
            });
        })();

        // bfcache (Back/Forward Cache) handler
        window.addEventListener('pageshow', function (event) {
            if (event.persisted && window.Livewire) {
                window.Livewire.dispatch('$refresh');
            }
        });
    </script>

    @stack('scripts')
    @livewireScripts
</body>
</html>