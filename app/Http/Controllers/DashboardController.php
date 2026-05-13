<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attend;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
public function index() 
{
    $user = auth()->user();

    $startOfMonth = now()->startOfMonth();
    $endOfMonth = now()->endOfMonth();

    $totalWorkingDays = 0;
    $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

    foreach ($period as $date) {
        if ($date->isWeekday()) {
            $totalWorkingDays++;
        }
    }

    $statsAttend = Attend::where("user_id", $user->id)
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth]) // Filter bulan ini
        ->selectRaw("
            count(case when status = 'in' then 1 end) as total_hadir,
            count(case when status = 'late' then 1 end) as total_telat,
            count(case when status = 'alfa' then 1 end) as total_alfa,
            count(case when status = 'permit' then 1 end) as total_izin
        ")
        ->first();

    $totalMasuk = $statsAttend->total_hadir + $statsAttend->total_telat + $statsAttend->total_ijin;
    $totalAlfa = $totalWorkingDays - $totalMasuk;
    $totalAlfa = $totalAlfa < 0 ? 0 : $totalAlfa;
    
    $realtimeAttendance = [
        [
            'initials' => 'JD',
            'name' => $user->name,
            'department' => 'IT Support',
            'clock_in' => '08:05 AM',
            'status' => 'On Time',
            'status_color' => 'green', // Menggunakan class green
        ],
        [
            'initials' => 'AS',
            'name' => 'Alice Smith',
            'department' => 'Marketing',
            'clock_in' => '08:45 AM',
            'status' => 'Late',
            'status_color' => 'orange', // Menggunakan class orange
        ],
        [
            'initials' => 'BW',
            'name' => 'Budi Waluyo',
            'department' => 'Finance',
            'clock_in' => '07:55 AM',
            'status' => 'Early',
            'status_color' => 'blue', // Menggunakan class blue
        ],
    ];

    // Data dummy untuk pengajuan cuti (pending leaves)
    $pendingLeaves = [
        [
            'employee' => 'Siti Aminah',
            'type' => 'Cuti Tahunan',
            'days' => 3,
            'reason' => 'Acara pernikahan keluarga di luar kota.',
        ],
        [
            'employee' => 'Rizky Pratama',
            'type' => 'Izin Sakit',
            'days' => 1,
            'reason' => 'Demam tinggi dan butuh istirahat total.',
        ],
    ];

    $stats = [
            [
                'label' => 'Hadir',
                'value' => $statsAttend->total_hadir,
                'total' => $totalWorkingDays,
                'icon' => 'check-circle',
                'color' => 'green',
                'percentage' => $statsAttend->total_hadir / $totalWorkingDays * 100,
            ],
            [
                'label' => 'Terlambat',
                'value' => $statsAttend->total_telat,
                'total' => $totalWorkingDays,
                'icon' => 'alert-circle',
                'color' => 'orange',
                'percentage' => $statsAttend->total_telat / $totalWorkingDays * 100,
            ],
            [
                'label' => 'Izin/Cuti',
                'value' => $statsAttend->total_izin,
                'total' => $totalWorkingDays,
                'icon' => 'calendar',
                'color' => 'blue',
                'percentage' => $statsAttend->total_izin / $totalWorkingDays * 100,
            ],
            [
                'label' => 'Alpa',
                'value' => $statsAttend->total_alfa,
                'total' => $totalWorkingDays,
                'icon' => 'x-circle',
                'color' => 'red',
                'percentage' => $statsAttend->total_alfa / $totalWorkingDays * 100,
            ],
        ];
        $currentDate = now()->translatedFormat('l, d F Y');

        $attendToday = Attend::where('user_id', $user->id)
            ->whereDate('created_at', now()->today())
            ->orderBy("id", "desc")
            ->first();
        
        $workingHours = null;

        if ($attendToday && $attendToday->clock_in && $attendToday->clock_out) {
            $in = Carbon::parse($attendToday->clock_in);
            $out = Carbon::parse($attendToday->clock_out);

            $hours = $in->diffInHours($out);
            $minutes = $in->diffInMinutes($out) % 60; // Ambil sisa menit setelah diambil jamnya

            $workingHours = "{$hours}h {$minutes}m";
        }

        $todayStatus = [
            'name' => $user->name,
            'clockIn' => $attendToday->clock_in ?? '--:--',
            'clockOut' => $attendToday->clock_out ?? '--:--',
            'status' => $attendToday->status ?? "Belum Absen",
            'workingHours' => $workingHours ?? '0h 0m',
        ];
        $recentActivity = [
            ['date' => '08 Mei 2026', 'in' => '08:05', 'out' => '17:30', 'status' => 'Terlambat', 'hours' => '8h 25m'],
            ['date' => '07 Mei 2026', 'in' => '07:58', 'out' => '17:15', 'status' => 'Hadir', 'hours' => '8h 17m'],
            ['date' => '06 Mei 2026', 'in' => '08:00', 'out' => '17:00', 'status' => 'Hadir', 'hours' => '8h 00m'],
        ];

    return view("dashboard", compact('realtimeAttendance', 'pendingLeaves', 'stats', 'currentDate', 'todayStatus', 'recentActivity', 'attendToday'));
}
}
