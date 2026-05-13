<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attend;
use Illuminate\Support\Facades\Storage;

class AttendenceClockController extends Controller
{


    const STATUS_IN = 'in';
    const STATUS_OUT = 'out';
    const STATUS_LATE = 'late';


    public function index() {
        return view("attendanceClock");
    }

    public function store(Request $request)
        {
            // 1. Validasi Input
            $request->validate([
                'selfie' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            // 2. Olah Foto Selfie
            $file = $request->file('selfie');
            // Simpan ke folder 'public/selfies' (pastikan sudah php artisan storage:link)
            $path = $file->store('selfies', 'public');

            // 3. Simpan ke Database
            $attend = Attend::create([
                'user_id'    => auth()->id(), // Mengambil ID user yang sedang login
                'selfie_url' => $path,
                'clock_in'   => now()->toTimeString(), // Jam masuk saat ini
                'clock_out'   => null, // Tanggal absen hari ini
                'status'      => self::STATUS_IN,
                'latitude'   => $request->latitude,
                'longitude'  => $request->longitude,
            ]);

            return response()->json([
                'message' => 'Absensi berhasil tercatat!',
                'data'    => $attend
            ], 201);
        }
    public function update(Request $request, $id)
    {
        $attend = Attend::where('user_id', auth()->id())
                        ->where('id', $id)
                        ->whereNull('clock_out') // Asumsi kolom untuk jam pulang bernama clock_out
                        ->first();

        if (!$attend) {
            return response()->json([
                'message' => 'Data absensi tidak ditemukan atau Anda sudah melakukan absen pulang.'
            ], 404);
        }
        
        $attend->update([
            'clock_out' => now()->toTimeString(),
        ]);

        return response()->json([
            'message' => 'Berhasil absen pulang. Hati-hati di jalan!',
            'data'    => $attend
        ]);
    }

    public function getAll(Request $req) {
        $search = $req->query("search");
        $lastId = $req->query("lastId");
        $limit = $req->query("limit") ?? 10; 
        
        $userId = auth()->id();

        $query = Attend::where("user_id", $userId);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('status', 'like', "%{$search}%")
                ->orWhere('clock_at', 'like', "%{$search}%");
            });
        }
        if ($lastId) {
            $query->where('id', '<', $lastId);
        }


        $data = $query->orderBy('id', 'desc') // Urutan terbaru di atas
                    ->limit($limit)
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
            'count' => $data->count(),
            'lastId' => $data->last() ? $data->last()->id : null 
        ]);
    }


}
