<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-6" x-data="{ searchQuery: '' }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-1">Admin Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Monitoring & Manajemen Kehadiran Real-Time
                </p>
            </div>
            <div class="flex gap-2">
                <button
                    class="bg-gray-200 dark:bg-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors flex items-center gap-2">
                    <i data-lucide="filter" class="w-5 h-5"></i>
                    Filter
                </button>
                <button
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <i data-lucide="download" class="w-5 h-5"></i>
                    Export
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        @php
            $stats = [
                [
                    'label' => 'Total Karyawan',
                    'value' => '245',
                    'change' => '+5',
                    'trend' => 'up',
                    'icon' => 'users',
                    'color' => 'blue',
                ],
                [
                    'label' => 'Sudah Hadir',
                    'value' => '198',
                    'change' => '80.8%',
                    'trend' => 'up',
                    'icon' => 'user-check',
                    'color' => 'green',
                ],
                [
                    'label' => 'Belum Hadir',
                    'value' => '35',
                    'change' => '14.3%',
                    'trend' => 'down',
                    'icon' => 'user-x',
                    'color' => 'orange',
                ],
                [
                    'label' => 'Terlambat',
                    'value' => '12',
                    'change' => '4.9%',
                    'trend' => 'down',
                    'icon' => 'clock',
                    'color' => 'red',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($stats as $stat)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/20 rounded-lg flex items-center justify-center">
                            <i data-lucide="{{ $stat['icon'] }}"
                                class="w-6 h-6 text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400"></i>
                        </div>
                        <div
                            class="flex items-center gap-1 text-sm font-medium {{ $stat['trend'] === 'up' ? 'text-green-600' : 'text-red-600' }}">
                            <i data-lucide="{{ $stat['trend'] === 'up' ? 'trending-up' : 'trending-down' }}"
                                class="w-4 h-4"></i>
                            {{ $stat['change'] }}
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold mb-1">{{ $stat['value'] }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Weekly Trend -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold">Tren Kehadiran Mingguan</h3>
                    <select
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700">
                        <option>Minggu Ini</option>
                        <option>Bulan Ini</option>
                    </select>
                </div>
                <div class="h-[300px]">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>

            <!-- Attendance Distribution -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold mb-6">Distribusi Hari Ini</h3>
                <div class="h-[200px] flex justify-center">
                    <canvas id="pieChart"></canvas>
                </div>
                <div class="mt-8 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-green-500"></div><span class="text-sm">Hadir</span>
                        </div>
                        <span class="font-medium">198</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-orange-500"></div><span class="text-sm">Terlambat</span>
                        </div>
                        <span class="font-medium">12</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-gray-500"></div><span class="text-sm">Belum Hadir</span>
                        </div>
                        <span class="font-medium">35</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Attendance & Pending Leaves -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- List Real-time -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold">Aktivitas Real-Time</h3>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm text-gray-600">Live</span>
                        </div>
                    </div>
                    <div class="relative">
                        <i data-lucide="search"
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                        <input type="text" x-model="searchQuery" placeholder="Cari karyawan..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg outline-none bg-white dark:bg-gray-700">
                    </div>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Loop Employee Data -->
                    @foreach ($realtimeAttendance as $emp)
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ $emp['initials'] }}</div>
                                <div class="flex-1">
                                    <h4 class="font-medium">{{ $emp['name'] }}</h4>
                                    <p class="text-sm text-gray-500">{{ $emp['department'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium mb-1">{{ $emp['clock_in'] }}</p>
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium bg-{{ $emp['status_color'] }}-100 text-{{ $emp['status_color'] }}-600">
                                        {{ $emp['status'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold">Pengajuan Cuti Pending</h3>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($pendingLeaves as $leave)
                        <div class="p-4">
                            <h4 class="font-medium">{{ $leave['employee'] }}</h4>
                            <p class="text-sm text-gray-500 mb-3">{{ $leave['type'] }} • {{ $leave['days'] }} hari</p>
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg mb-3 text-sm italic">
                                "{{ $leave['reason'] }}"</div>
                            <div class="flex gap-2">
                                <button
                                    class="flex-1 bg-green-600 text-white py-2 rounded-lg text-sm font-medium">Setuju</button>
                                <button
                                    class="flex-1 bg-red-600 text-white py-2 rounded-lg text-sm font-medium">Tolak</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        // Weekly Chart
        new Chart(document.getElementById('weeklyChart'), {
            type: 'line',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum'],
                datasets: [{
                        label: 'Hadir',
                        data: [235, 238, 240, 237, 230],
                        borderColor: '#10b981',
                        tension: 0.4
                    },
                    {
                        label: 'Terlambat',
                        data: [8, 5, 3, 6, 10],
                        borderColor: '#f97316',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Distribution Chart
        new Chart(document.getElementById('pieChart'), {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Terlambat', 'Belum Hadir'],
                datasets: [{
                    data: [198, 12, 35],
                    backgroundColor: ['#10b981', '#f97316', '#6b7280']
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</x-app-layout>
