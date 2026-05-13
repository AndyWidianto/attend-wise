<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ 'History' }}
        </h2>
    </x-slot>

    <div class="space-y-6 p-6 bg-gray-50 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100"
        x-data="{
            searchQuery: '',
            filterStatus: 'all',
            selectedMonth: 'Mei 2026',
            attendanceData: [
                { date: '09 Mei 2026', day: 'Jumat', clockIn: '08:05', clockOut: '17:30', totalHours: '8h 25m', status: 'Terlambat', location: 'Kantor Pusat', notes: 'Terlambat 5 menit' },
                { date: '08 Mei 2026', day: 'Kamis', clockIn: '07:58', clockOut: '17:15', totalHours: '8h 17m', status: 'Hadir', location: 'Kantor Pusat', notes: '-' },
                { date: '07 Mei 2026', day: 'Rabu', clockIn: '08:00', clockOut: '17:00', totalHours: '8h 00m', status: 'Hadir', location: 'Kantor Pusat', notes: '-' },
                { date: '06 Mei 2026', day: 'Selasa', clockIn: '08:15', clockOut: '17:00', totalHours: '7h 45m', status: 'Terlambat', location: 'Kantor Pusat', notes: 'Terlambat 15 menit' },
                { date: '05 Mei 2026', day: 'Senin', clockIn: '07:55', clockOut: '17:30', totalHours: '8h 35m', status: 'Hadir', location: 'Kantor Pusat', notes: '-' },
                { date: '02 Mei 2026', day: 'Jumat', clockIn: '-', clockOut: '-', totalHours: '-', status: 'Cuti', location: '-', notes: 'Cuti Tahunan' },
                { date: '01 Mei 2026', day: 'Kamis', clockIn: '-', clockOut: '-', totalHours: '-', status: 'Libur', location: '-', notes: 'Hari Buruh' }
            ],
            getStatusClass(status) {
                const classes = {
                    'Hadir': 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400',
                    'Terlambat': 'bg-orange-100 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400',
                    'Cuti': 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
                    'Izin': 'bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400',
                    'Alpa': 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400',
                    'Libur': 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                };
                return classes[status] || 'bg-gray-100 text-gray-700';
            },
            get filteredData() {
                return this.attendanceData.filter(item => {
                    const matchesSearch = item.date.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        item.day.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesFilter = this.filterStatus === 'all' || item.status === this.filterStatus;
                    return matchesSearch && matchesFilter;
                });
            }
        }">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-1">Riwayat Kehadiran</h1>
                <p class="text-gray-600 dark:text-gray-400">Lihat histori presensi Anda</p>
            </div>
            <button
                class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i data-lucide="download" class="w-5 h-5"></i>
                Export Data
            </button>
        </div>

        <!-- Monthly Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold">Ringkasan Bulan Ini</h3>
                <div class="flex items-center gap-2">
                    <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                    </button>
                    <span class="font-medium px-4" x-text="selectedMonth"></span>
                    <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Total Hari</p>
                    <p class="text-2xl font-bold">22</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Hadir</p>
                    <p class="text-2xl font-bold text-green-600">18</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Terlambat</p>
                    <p class="text-2xl font-bold text-orange-600">3</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Alpa</p>
                    <p class="text-2xl font-bold text-red-600">0</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Cuti/Izin</p>
                    <p class="text-2xl font-bold text-blue-600">1</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Total Jam</p>
                    <p class="text-2xl font-bold">178h 30m</p>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                    <input type="text" placeholder="Cari tanggal atau hari..." x-model="searchQuery"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none bg-white dark:bg-gray-700" />
                </div>

                <div class="flex gap-2">
                    <button @click="filterStatus = 'all'"
                        :class="filterStatus === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700'"
                        class="px-4 py-3 rounded-lg font-medium transition-colors">
                        Semua
                    </button>
                    <button @click="filterStatus = 'Hadir'"
                        :class="filterStatus === 'Hadir' ? 'bg-green-600 text-white' : 'bg-gray-100 dark:bg-gray-700'"
                        class="px-4 py-3 rounded-lg font-medium transition-colors">
                        Hadir
                    </button>
                    <button @click="filterStatus = 'Terlambat'"
                        :class="filterStatus === 'Terlambat' ? 'bg-orange-600 text-white' : 'bg-gray-100 dark:bg-gray-700'"
                        class="px-4 py-3 rounded-lg font-medium transition-colors">
                        Terlambat
                    </button>
                </div>
            </div>
        </div>

        <!-- Attendance Table -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Tanggal</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Clock In</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Clock Out</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Total Jam</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Lokasi</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="(record, index) in filteredData" :key="index">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="font-medium" x-text="record.date"></div>
                                        <div class="text-sm text-gray-500" x-text="record.day"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                        <span x-text="record.clockIn"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                        <span x-text="record.clockOut"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium" x-text="record.totalHours"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusClass(record.status)"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium">
                                        <i data-lucide="check-circle" class="w-3 h-3"
                                            x-show="record.status === 'Hadir'"></i>
                                        <i data-lucide="alert-circle" class="w-3 h-3"
                                            x-show="record.status === 'Terlambat'"></i>
                                        <i data-lucide="calendar" class="w-3 h-3"
                                            x-show="['Cuti', 'Libur'].includes(record.status)"></i>
                                        <span x-text="record.status"></span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <template x-if="record.location !== '-'">
                                        <div class="flex items-center gap-2 text-sm">
                                            <i data-lucide="map-pin" class="w-4 h-4 text-gray-400"></i>
                                            <span x-text="record.location"></span>
                                        </div>
                                    </template>
                                    <template x-if="record.location === '-'">
                                        <span class="text-gray-400">-</span>
                                    </template>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400" x-text="record.notes">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Menampilkan <span x-text="filteredData.length"></span> dari <span
                    x-text="attendanceData.length"></span> data
            </p>
            <div class="flex gap-2">
                <button
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Previous</button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">1</button>
                <button
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">2</button>
                <button
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Next</button>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        lucide.createIcons();
        document.addEventListener('alpine:initialized', () => {
            Alpine.effect(() => {
                // Memantau perubahan filter/search
                const _ = Alpine.store('searchQuery');
                setTimeout(() => lucide.createIcons(), 10);
            });
        });

        async function getAttendece() {
            try {
                const res = await fetch(`/api/attendence`, {
                    method: "GET",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json",
                        // X-Requested-With penting agar Laravel tahu ini adalah AJAX request
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    // KRUSIAL: Agar browser mengirimkan cookie session ke server Laravel
                    credentials: "include"
                });

                // Cek jika responnya tidak oke (misal 401 Unauthorized atau 404 Not Found)
                if (!res.ok) {
                    const errorData = await res.json();
                    console.error("Server Error:", errorData);
                    return;
                }

                const result = await res.json();
                console.log("Data Absensi:", result.data);

                return result.data;

            } catch (err) {
                console.error("Network Error:", err);
            }
        }

        getAttendece();
    </script>
</x-app-layout>
