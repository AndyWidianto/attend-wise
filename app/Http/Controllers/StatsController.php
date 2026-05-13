<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attend;

class StatsController extends Controller
{
    public function getStatsAttend() {
    $user = auth()->user();

    $startOfWeek = now()->startOfWeek(); // Secara default dimulai dari Senin
    $endOfWeek = now()->endOfWeek();     // Berakhir di Minggu
    $startOfMonth = now()->startOfMonth();
    $endOfMonth = now()->endOfMonth();

    $weeklyData = Attend::where('user_id', $user->id)
        ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
        ->orderBy('created_at', 'asc')
        ->get()
        ->groupBy(function($date) {
            // Kelompokkan berdasarkan nama hari dalam bahasa Inggris (Monday, Tuesday, dst)
            return Carbon::parse($date->created_at)->format('l');
        });

    $statsAttend = Attend::where("user_id", $user->id)
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth]) // Filter bulan ini
        ->selectRaw("
            count(case when status = 'in' then 1 end) as total_in,
            count(case when status = 'late' then 1 end) as total_late,
            count(case when status = 'alfa' then 1 end) as total_alpha,
            count(case when status = 'permit' then 1 end) as total_permit
        ")
        ->first();

    // 3. Siapkan template mapping hari untuk memastikan urutan Senin-Minggu
    $daysMapping = [
        'Monday'    => 'Sen',
        'Tuesday'   => 'Sel',
        'Wednesday' => 'Rab',
        'Thursday'  => 'Kam',
        'Friday'    => 'Jum',
        'Saturday'  => 'Sab',
        'Sunday'    => 'Min',
    ];

    $labels = [];
    $dataset = [];

    foreach ($daysMapping as $dayName => $labelIndo) {
        $labels[] = $labelIndo;
        
        // Ambil data berdasarkan grup hari
        $dayGroup = $weeklyData->get($dayName);
        
        if ($dayGroup) {
            // Hitung total jam kerja jika ada data (menggunakan logika diff jam)
            $totalHours = $dayGroup->reduce(function($carry, $item) {
                if ($item->clock_in && $item->clock_out) {
                    $in = Carbon::parse($item->clock_in);
                    $out = Carbon::parse($item->clock_out);
                    // Hitung selisih dalam jam (decimal), misal 8.5 jam
                    return $carry + $in->diffInMinutes($out) / 60;
                }
                return $carry;
            }, 0);
            
            $dataset[] = round($totalHours, 1);
        } else {
            $dataset[] = 0; // Jika tidak ada absen di hari tersebut
        }
    }

    return response()->json([
        "working_hours" => [
            'labels' => $labels,
            'dataset' => $dataset
        ],
        "distribution" => [
            "totalIn" => $statsAttend->total_in,
            "totalLate" => $statsAttend->total_late,
            "totalAlpha" => $statsAttend->total_alpha,
            "totalPermit" => $statsAttend->total_permit
        ]
    ]);
    }
}
