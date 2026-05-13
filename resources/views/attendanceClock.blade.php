<x-app-layout>


    <div x-data="attendanceSystem()" x-init="init()" class="max-w-4xl mx-auto space-y-6 p-4">
        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold mb-1">Presensi Digital</h1>
            <p class="text-gray-600 dark:text-gray-400">
                Verifikasi lokasi dan identitas untuk clock in/out
            </p>
        </div>

        <!-- Progress Steps -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-center border">
                <!-- Step 1 -->
                <div class="flex-1 flex items-center">
                    <div :class="(step === 'location' || locationStatus === 'inside') && !faceVerified ? 'bg-blue-600 text-white' :
                        'bg-gray-200 text-gray-500'"
                        class="w-10 h-10 rounded-full flex items-center justify-center transition-colors">1</div>
                    <div :class="locationStatus === 'inside' ? 'bg-blue-600' : 'bg-gray-200'" class="flex-1 h-1 mx-2">
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex-1 flex items-center">
                    <div :class="step === 'camera' ? 'bg-blue-600 text-white' : (locationStatus === 'inside' ?
                        'bg-gray-300 text-gray-700' : 'bg-gray-200 text-gray-500')"
                        class="w-10 h-10 rounded-full flex items-center justify-center transition-colors">2</div>
                    <div :class="faceVerified ? 'bg-blue-600' : 'bg-gray-200'" class="flex-1 h-1 mx-2"></div>
                </div>

                <!-- Step 3 -->
                <div class="flex-1 flex items-center">
                    <div :class="step === 'confirm' ? 'bg-blue-600 text-white' : (faceVerified ? 'bg-gray-300 text-gray-700' :
                        'bg-gray-200 text-gray-500')"
                        class="w-10 h-10 rounded-full flex items-center justify-center transition-colors">3</div>
                        <div :class="locationStatus === 'inside' && faceVerified && step === 'confirm' ? 'bg-blue-600' : 'bg-gray-200'" class="flex-1 h-1 mx-2"></div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mt-4 text-center">
                <p class="text-sm font-medium">Lokasi GPS</p>
                <p class="text-sm font-medium">Verifikasi Wajah</p>
                <p class="text-sm font-medium">Konfirmasi</p>
            </div>
        </div>

        <!-- Step 1: Location Verification -->
        <div x-show="step === 'location'" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                        <i data-lucide="map-pin" class="text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Verifikasi Lokasi GPS</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Pastikan Anda berada di area kantor</p>
                    </div>
                </div>

                <div x-show="locationStatus === 'checking'" class="text-center py-12">
                    <div
                        class="animate-spin inline-block w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full mb-4">
                    </div>
                    <p class="text-gray-600">Memeriksa lokasi Anda...</p>
                </div>

                <div x-show="locationStatus === 'inside'" class="space-y-6">
                    <div
                        class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 flex items-start gap-3">
                        <i data-lucide="check-circle" class="text-green-600 mt-1"></i>
                        <div>
                            <h4 class="font-medium text-green-900 dark:text-green-100">Di Dalam Area Presensi</h4>
                            <p class="text-sm text-green-700 dark:text-green-300">Lokasi Anda terverifikasi.</p>
                            <p x-text="'Jarak dari kantor: ' + distance + ' meter'" class="text-xs text-green-600 mt-2">
                            </p>
                        </div>
                    </div>

                    <!-- Mock Map -->
                    <div
                        class="bg-gray-100 dark:bg-gray-700 rounded-lg h-64 flex items-center justify-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-100 to-blue-200 opacity-50"></div>
                        <div class="relative z-10 text-center">
                            <div
                                class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                                <i data-lucide="navigation" class="text-white w-10 h-10"></i>
                            </div>
                            <p class="font-medium">Lokasi Anda Terdeteksi</p>
                        </div>
                    </div>

                    <button @click="goToStep('camera')"
                        class="w-full bg-blue-600 text-white py-4 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        Lanjut ke Verifikasi Wajah
                    </button>
                </div>

                <div x-show="locationStatus === 'outside'" class="space-y-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
                        <i data-lucide="x-circle" class="text-red-600"></i>
                        <div>
                            <h4 class="font-medium text-red-900">Di Luar Area Presensi</h4>
                            <p class="text-sm text-red-700"
                                x-text="'Jarak Anda: ' + distance + 'm. Maksimal radius 100m.'"></p>
                        </div>
                    </div>
                    <button @click="checkLocation()"
                        class="w-full bg-gray-600 text-white py-4 rounded-lg font-medium">Coba Lagi</button>
                </div>
            </div>
        </div>

        <!-- Step 2: Camera Verification -->
        <div x-show="step === 'camera'" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="camera" class="text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Verifikasi Wajah</h3>
                        <p class="text-sm text-gray-600">Ambil foto untuk validasi identitas</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div x-show="!capturedImage" class="relative">
                        <video x-ref="video" autoplay playsinline class="w-full rounded-lg bg-gray-900"
                            style="max-height: 400px; transform: scaleX(-1);"></video>
                        <div class="absolute bottom-6 left-1/2 -translate-x-1/2">
                            <button @click="capturePhoto"
                                class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg">
                                <div class="w-14 h-14 bg-blue-600 rounded-full"></div>
                            </button>
                        </div>
                    </div>

                    <div x-show="capturedImage" class="space-y-4">
                        <div class="relative">
                            <img :src="capturedImage" class="w-full rounded-lg"
                                style="max-height: 400px; object-fit: cover;">
                            <div x-show="faceVerified"
                                class="absolute top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-full flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                                <span class="font-medium text-sm">Wajah Terverifikasi</span>
                            </div>
                        </div>

                        <div x-show="faceVerified" class="flex gap-3">
                            <button @click="retakePhoto"
                                class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-medium">Foto Ulang</button>
                            <button @click="goToStep('confirm')"
                                class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-medium">Lanjutkan</button>
                        </div>

                        <div x-show="!faceVerified" class="text-center py-4">
                            <div
                                class="animate-spin inline-block w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full mb-2">
                            </div>
                            <p class="text-sm text-gray-600">Memverifikasi wajah...</p>
                        </div>
                    </div>
                </div>
                <canvas x-ref="canvas" class="hidden"></canvas>
            </div>
        </div>

        <!-- Step 3: Confirmation -->
        <div x-show="step === 'confirm'" x-cloak>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="check-circle" class="text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Konfirmasi Presensi</h3>
                        <p class="text-sm text-gray-600">Periksa detail sebelum clock in</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Waktu</span>
                            <span class="font-medium" x-text="currentTime"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lokasi</span>
                            <span class="font-medium text-green-600">Terverifikasi</span>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button @click="step = 'location'" class="flex-1 bg-gray-200 py-4 rounded-lg">Batal</button>
                        <button @click="handleClockIn" :disabled="loading"
                            class="flex-1 bg-blue-600 text-white py-4 rounded-lg flex items-center justify-center gap-2">
                            <template x-if="!loading">
                                <span>Clock In Sekarang</span>
                            </template>
                            <template x-if="loading">
                                <span>Processing...</span>
                            </template>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg flex gap-3">
                <i data-lucide="map-pin" class="text-blue-600"></i>
                <div>
                    <h4 class="font-medium text-sm">Geofencing</h4>
                    <p class="text-xs text-gray-600">Verifikasi lokasi real-time</p>
                </div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg flex gap-3">
                <i data-lucide="camera" class="text-purple-600"></i>
                <div>
                    <h4 class="font-medium text-sm">Face Recognition</h4>
                    <p class="text-xs text-gray-600">AI Identitas Wajah</p>
                </div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg flex gap-3">
                <i data-lucide="fingerprint" class="text-green-600"></i>
                <div>
                    <h4 class="font-medium text-sm">Anti-Fraud</h4>
                    <p class="text-xs text-gray-600">Deteksi manipulasi GPS</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function attendanceSystem() {
            return {
                step: 'location',
                loading: false,
                locationStatus: 'checking',
                capturedImage: null,
                faceVerified: false,
                imageBlob: null,
                distance: 0,
                currentTime: '',
                officeLocation: {
                    lat: -6.2088,
                    lng: 106.8456
                },
                geofenceRadius: 100,
                stream: null,

                init() {
                    this.checkLocation();
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                    lucide.createIcons();
                },

                updateTime() {
                    this.currentTime = new Date().toLocaleTimeString('id-ID');
                },

                async goToStep(newStep) {
                    this.step = newStep;

                    if (newStep === 'camera') {
                        await this.$nextTick();
                        try {
                            await this.startCamera();
                        } catch (err) {
                            console.error("Gagal memulai kamera:", err);
                        }
                    }

                    setTimeout(() => lucide.createIcons(), 50);
                },

                checkLocation() {
                    this.locationStatus = 'checking';
                    if ("geolocation" in navigator) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.distance = Math.round(this.calculateDistance(
                                    position.coords.latitude,
                                    position.coords.longitude,
                                    this.officeLocation.lat,
                                    this.officeLocation.lng
                                ));
                                // Mock: always true for demo as requested in original code
                                this.locationStatus = (this.distance <= this.geofenceRadius || true) ? 'inside' :
                                    'outside';
                            },
                            (error) => {
                                this.locationStatus = 'outside';
                                Swal.fire('Error', 'Gagal akses lokasi', 'error');
                            }
                        );
                    }
                },

                calculateDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371e3;
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
                },

                async startCamera() {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: "user"
                            }
                        });
                        this.stream = stream;
                        const videoElement = this.$refs.video;

                        if (videoElement) {
                            videoElement.srcObject = stream;
                            // Penting: panggil play() secara manual untuk memastikan video jalan
                            videoElement.play();
                        } else {
                            console.error("Gagal menemukan refs 'video'. Pastikan x-ref='video' sudah terpasang.");
                        }
                    } catch (err) {
                        console.error(err);
                        Swal.fire('Error', 'Kamera tidak bisa diakses', 'error');
                    }
                },

                capturePhoto() {
                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    canvas.toBlob((blob) => {
                        this.imageBlob = blob;
                    }, 'image/jpeg', 0.8);
                    this.capturedImage = canvas.toDataURL('image/png');

                    // Stop camera
                    this.stream.getTracks().forEach(track => track.stop());

                    // Mock Verification
                    setTimeout(() => {
                        this.faceVerified = true;
                        lucide.createIcons();
                    }, 1500);
                },

                retakePhoto() {
                    this.capturedImage = null;
                    this.faceVerified = false;
                    this.startCamera();
                },

                async handleClockIn() {
                    this.loading = true;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    try {
                        const dataToSend = new FormData();
                        if (!this.imageBlob) return this.step = "camera";
                        dataToSend.append("selfie", this.imageBlob);
                        dataToSend.append("latitude", this.officeLocation.lat);
                        dataToSend.append("longitude", this.officeLocation.lng);
                        const res = await fetch("/api/attendence", {
                            method: "POST",
                            body: dataToSend, // Biarkan browser yang mengatur Content-Type-nya
                            headers: {
                                "Accept": "application/json",
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
                        console.log("Data Absensi:", result.data);
                        Swal.fire('Berhasil', 'Presensi Anda telah tercatat!', 'success');
                    } catch (err) {
                        console.error("Network Error:", err);
                        Swal.fire('Error', 'Waah sepertinya system sedang sibuk coba lagi', 'error');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
