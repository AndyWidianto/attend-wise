<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
    sidebarOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true'
}"
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Anti-Flash Script: Mengeksekusi tema sebelum Alpine/HTML dirender -->
    <script>
        if (localStorage.getItem('darkMode') === 'true' ||
            (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Alpine.js (Hapus jika di app.js sudah ada Alpine) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- load chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

    <div class="min-h-screen">
        <!-- Top Navbar -->
        <nav
            class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-800 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 h-16">
            <div class="flex items-center justify-between h-full pr-4">

                <!-- Left: Menu & Logo -->
                <div class="flex items-center sm:justify-center gap-4 sm:w-64 h-full sm:border-r sm:border-gray-200 dark:sm:border-gray-700">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i x-show="!sidebarOpen" data-lucide="menu" class="w-6 h-6"></i>
                        <i x-show="sidebarOpen" data-lucide="x" class="w-6 h-6"></i>
                    </button>

                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="clock" class="w-5 h-5 text-white"></i>
                        </div>
                        <span class="font-bold text-xl hidden sm:block">SmartAttend</span>
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-2">
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">

                        <!-- Ikon Sun muncul saat dark mode aktif -->
                        <i data-lucide="sun" class="w-5 h-5" x-show="darkMode"></i>

                        <!-- Ikon Moon muncul saat dark mode tidak aktif -->
                        <i data-lucide="moon" class="w-5 h-5" x-show="!darkMode"></i>
                    </button>

                    <button class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 relative">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <div
                        class="hidden sm:flex items-center gap-2 ml-2 pl-2 border-l border-gray-200 dark:border-gray-700">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i data-lucide="user" class="w-4 h-4 text-white"></i>
                        </div>
                        <div class="hidden md:block">
                            <p class="text-sm font-medium">{{ Auth::user()->name ?? 'John Doe' }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->role ?? 'Employee' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside
            class="fixed top-16 left-0 bottom-0 z-40 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-transform duration-300"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <div class="flex flex-col h-full">
                <div class="flex-1 overflow-y-auto py-4">
                    <nav class="space-y-1 px-3">
                        @php
                            $menuItems = [
                                ['route' => 'dashboard', 'icon' => 'home', 'label' => 'Dashboard'],
                                ['route' => 'attendanceClock', 'icon' => 'clock', 'label' => 'Presensi'],
                                ['route' => 'history', 'icon' => 'history', 'label' => 'Riwayat'],
                                // ['route' => 'leave.index', 'icon' => 'calendar', 'label' => 'Izin & Cuti'],
                            ];
                        @endphp

                        @foreach ($menuItems as $item)
                            <a href="{{ route($item['route']) }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs($item['route']) ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
                                <span class="font-medium">{{ $item['label'] }}</span>
                            </a>
                        @endforeach

                        @if (Auth::user() && Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                                <span class="font-medium">Admin Panel</span>
                            </a>
                        @endif
                    </nav>
                </div>

                <!-- Logout Button -->
                <div class="p-3 border-t border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 w-full transition-colors">
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                            <span class="font-medium">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="lg:pl-64 pt-16">
            <div class="p-4 lg:p-8">
                {{ $slot }}
            </div>
        </main>

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>
    </div>

    <script>
        // Inisialisasi Ikon Lucide
        lucide.createIcons();
    </script>
</body>

</html>
