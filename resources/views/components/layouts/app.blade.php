<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Stock Inventory') }} - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="h-full font-sans antialiased">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-blue-600 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ url('/') }}" class="text-white text-xl font-bold">📦 Stock Inventory</a>
                        <div class="ml-10 flex items-baseline space-x-4">
                            @auth
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.users') }}"
                                       class="text-blue-200 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.*') ? 'bg-blue-700 text-white' : '' }}">
                                        Users
                                    </a>
                                @else
                                    <a href="{{ route('stocks.index') }}"
                                       class="text-blue-200 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('stocks.*') ? 'bg-blue-700 text-white' : '' }}">
                                        Stok Saya
                                    </a>
                                    <a href="{{ route('profile') }}"
                                       class="text-blue-200 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('profile') ? 'bg-blue-700 text-white' : '' }}">
                                        Profil
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <span class="text-blue-200 text-sm">
                                {{ auth()->user()->name }}
                                <span class="ml-1 text-xs bg-blue-800 px-2 py-0.5 rounded-full text-white uppercase">
                                    {{ auth()->user()->role }}
                                </span>
                            </span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="text-blue-200 hover:text-white text-sm font-medium">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-blue-200 hover:text-white text-sm">Login</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 rounded-md bg-red-50 border border-red-200 text-red-800 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>
</html>
