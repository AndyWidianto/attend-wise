<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ 'Dashboard' }}
        </h2>
    </x-slot>
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold mb-1 dark:text-white">Dashboard Karyawan</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ $currentDate }}</p>
        </div>

        <!-- Today's Status Card -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-blue-100 mb-1">Selamat Datang Kembali</p>
                    <h2 class="text-2xl font-bold mb-4">{{ $todayStatus['name'] }}</h2>
                    <div class="flex items-center gap-2 text-blue-100">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        <span>Waktu Sekarang: <span class="font-mono" id="clock-display"></span></span>
                    </div>
                </div>
                @if ($attendToday)
                    {{-- Kondisi sudah absen masuk, tapi belum pulang --}}
                    <button id="BtnClockOut" id-attend="{{ $attendToday->id }}"
                        class="bg-white text-blue-600 px-8 py-4 rounded-xl font-bold text-center hover:bg-blue-50 transition-colors shadow-lg flex flex-col items-center group">
                        <i data-lucide="log-out" class="w-6 h-6 mb-2 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm opacity-80 font-medium">Kerja bagus!</span>
                        <span>Selesai Bertugas?</span>
                    </button>
                @else
                    {{-- Kondisi belum absen sama sekali --}}
                    <a href="/dashboard/clock"
                        class="bg-white text-blue-600 px-8 py-4 rounded-xl font-bold text-center hover:bg-blue-50 transition-colors shadow-lg flex flex-col items-center group">
                        <i data-lucide="play-circle"
                            class="w-6 h-6 mb-2 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm opacity-80 font-medium">Sudah siap?</span>
                        <span>Mulai Absensi</span>
                    </a>
                @endif
            </div>

            <div class="mt-6 pt-6 border-t border-blue-500 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-blue-100 text-sm mb-1" id="text-clock-in">Clock In</p>
                    <p class="text-xl font-bold">{{ $todayStatus['clockIn'] }}</p>
                </div>
                <div>
                    <p class="text-blue-100 text-sm mb-1">Clock Out</p>
                    <p class="text-xl font-bold" id="text-clock-out">{{ $todayStatus['clockOut'] }}</p>
                </div>
                <div>
                    <p class="text-blue-100 text-sm mb-1">Status</p>
                    <span class="inline-block bg-orange-500 px-3 py-1 rounded-full text-sm font-medium">
                        @if ($todayStatus['status'] === 'in')
                            Hadir
                        @elseif($todayStatus['status'] === 'late')
                            Terlambat
                        @elseif($todayStatus['status'] === 'permit')
                            Izin
                        @else
                            Alfa
                        @endif
                    </span>
                </div>
                <div>
                    <p class="text-blue-100 text-sm mb-1">Jam Kerja</p>
                    <p class="text-xl font-bold" id="totalWorkingHours">{{ $todayStatus['workingHours'] }}</p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
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
                        <span class="text-sm text-gray-500">Bulan Ini</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-3xl font-bold dark:text-white">{{ $stat['value'] }}</span>
                        <span class="text-gray-500">/{{ $stat['total'] }}</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">{{ $stat['label'] }}</p>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-{{ $stat['color'] }}-500 h-2 rounded-full"
                            style="width: {{ $stat['percentage'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Charts Row -->
        <div x-data="chartRow()" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold mb-1 dark:text-white">Jam Kerja Minggu Ini</h3>
                        <p class="text-sm text-gray-500">Total: 37.5 jam</p>
                    </div>
                    <i data-lucide="trending-up" class="w-5 h-5 text-green-500"></i>
                </div>
                <div class="h-[250px]">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold mb-6 dark:text-white">Distribusi Kehadiran</h3>
                <div class="h-[200px]">
                    <canvas id="attendancePie"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                            <span class="text-sm dark:text-gray-300">Hadir</span>
                        </div>
                        <span class="font-medium dark:text-white" x-text="dataAttendence[0] || 0"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-orange-500"></div>
                            <span class="text-sm dark:text-gray-300">Terlambat</span>
                        </div>
                        <span class="font-medium dark:text-white" x-text="dataAttendence[1] || 0"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-[#3b82f6]"></div>
                            <span class="text-sm dark:text-gray-300">Izin</span>
                        </div>
                        <span class="font-medium dark:text-white" x-text="dataAttendence[1] || 0"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-[#ff1717]"></div>
                            <span class="text-sm dark:text-gray-300">Alpha</span>
                        </div>
                        <span class="font-medium dark:text-white" x-text="dataAttendence[1]"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Table -->
        <div x-data="getAttendece()" x-init="init()"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-bold dark:text-white">Aktivitas Terbaru</h3>
                <a href="/history" class="text-blue-600 hover:underline text-sm font-medium">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
                        <template x-for="(item, index) in attendences" :key="item.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-6 py-4 whitespace-nowrap"
                                    x-text="new Date(item.created_at).toLocaleDateString('id-ID')"></td>
                                <td class="px-6 py-4" x-text="item.clock_in || '--:--'"></td>
                                <td class="px-6 py-4" x-text="item.clock_out || '--:--'"></td>
                                <td class="px-6 py-4"
                                    x-text="item.clock_in && item.clock_out ? formattedDuration(item.clock_in, item.clock_out) : '-'">
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': item
                                                .status === 'in',
                                            'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400': item
                                                .status === 'late',
                                            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': item
                                                .status === 'alfa',
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': item
                                                .status === 'permit'
                                        }"
                                        x-text="item.status === 'in' ? 'Hadir' : item.status === 'late' ? 'Terlambat' : item.status === 'permit' ? 'Izin' : 'Alfa'">
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="/clock"
                class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:border-blue-500 transition-colors group">
                <i data-lucide="map-pin"
                    class="w-8 h-8 text-blue-600 mb-3 group-hover:scale-110 transition-transform"></i>
                <h4 class="font-bold mb-1 dark:text-white">Presensi GPS</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">Verifikasi lokasi otomatis</p>
            </a>
            <a href="/leave"
                class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:border-blue-500 transition-colors group">
                <i data-lucide="calendar"
                    class="w-8 h-8 text-green-600 mb-3 group-hover:scale-110 transition-transform"></i>
                <h4 class="font-bold mb-1 dark:text-white">Ajukan Izin</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">Sisa cuti: 12 hari</p>
            </a>
            <a href="/dashboard/history"
                class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:border-blue-500 transition-colors group">
                <i data-lucide="clock"
                    class="w-8 h-8 text-purple-600 mb-3 group-hover:scale-110 transition-transform"></i>
                <h4 class="font-bold mb-1 dark:text-white">Riwayat</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">Log presensi lengkap</p>
            </a>
        </div>
    </div>

    @stack('scripts')
    <script>
        function getAttendece() {
            return {
                attendences: [],
                loading: false,
                limit: 3,
                init() {
                    this.getAttendeces();
                },
                async getAttendeces() {
                    this.loading = true;
                    try {
                        const res = await fetch(`/api/attendence`, {
                            method: "GET",
                            headers: {
                                "Accept": "application/json",
                                "Content-Type": "application/json",
                                "X-Requested-With": "XMLHttpRequest"
                            },
                            credentials: "include"
                        });
                        if (!res.ok) {
                            const errorData = await res.json();
                            console.error("Server Error:", errorData);
                            return;
                        }

                        const result = await res.json();
                        console.log("Data Absensi:", result);
                        this.attendences = result.data;

                    } catch (err) {
                        console.error("Network Error:", err);
                    } finally {
                        this.loading = false;
                    }
                },
                formattedDuration(clockIn, clockOut) {
                    if (!clockIn || !clockOut) return '0h 0m';

                    const start = new Date(`1970-01-01T${clockIn}Z`);
                    const end = new Date(`1970-01-01T${clockOut}Z`);
                    if (end < start) end.setDate(end.getDate() + 1);

                    const diffMs = end - start; // Selisih dalam milidetik
                    const diffHrs = Math.floor(diffMs / 3600000); // Konversi ke jam
                    const diffMins = Math.round((diffMs % 3600000) / 60000); // Konversi ke menit sisanya

                    return `${diffHrs}h ${diffMins}m`;
                }
            }
        }

        function chartRow() {
            return {
                weeklyChart: {
                    labels: [],
                    data: []
                },
                dataAttendence: [],
                attendence: [],
                totalWorking: 0,
                async init() {
                    await this.getStatAttend();
                    this.$nextTick(() => {
                        this.renderChart();
                    });
                },
                renderChart() {
                    const ctx = document.getElementById('weeklyChart');
                    if (!ctx) return;

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: this.weeklyChart.labels,
                            datasets: [{
                                label: 'Jam Kerja',
                                data: this.weeklyChart.data,
                                backgroundColor: '#3b82f6',
                                borderRadius: 8,
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                    new Chart(document.getElementById('attendancePie'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Hadir', 'Terlambat', 'Izin', 'Alpa'],
                            datasets: [{
                                data: this.dataAttendence,
                                backgroundColor: ['#10b981', '#f97316', '#3b82f6', '#ff1717'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });

                },
                async getStatAttend() {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    try {
                        const res = await fetch(`/api/attendence/stats`, {
                            method: "GET",
                            headers: {
                                "Accept": "application/json",
                                "Content-Type": "application/json",
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": csrfToken
                            },
                            credentials: "include"
                        });
                        if (!res.ok) {
                            const errorData = await res.json();
                            console.error("Server Error:", errorData);
                            return;
                        }

                        const result = await res.json();
                        this.weeklyChart = {
                            labels: result.working_hours.labels,
                            data: result.working_hours.dataset
                        }
                        const distribution = result.distribution;
                        this.dataAttendence = [
                            distribution.totalIn,
                            distribution.totalLate,
                            distribution.totalPermit,
                            distribution.totalAlpha
                        ]

                    } catch (err) {
                        console.error("Network Error:", err);
                    }
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            // Re-init Lucide Icons (Penting!)
            lucide.createIcons();
        });

        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds}`;
            document.getElementById('clock-display').textContent = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();

        const buttonAttend = document.getElementById("BtnClockOut");

        async function updateAttend() {
            const id = buttonAttend.getAttribute("id-attend");
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const res = await fetch(`/api/attendence/${id}`, {
                    method: "PATCH",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    credentials: "include"
                });
                if (!res.ok) {
                    const errorData = await res.json();
                    console.error("Server Error:", errorData);
                    return;
                }

                const result = await res.json();
                console.log("Data Absensi:", result);

            } catch (err) {
                console.error("Network Error:", err);
            }
        }
        buttonAttend.addEventListener("click", async () => {
            if (!confirm("Apakah Benar Pekerjaan anda telah selesai?")) return;
            await updateAttend()
        })
    </script>
</x-app-layout>
