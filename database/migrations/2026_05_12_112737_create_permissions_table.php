<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke user yang mengajukan
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Detail Izin
            $table->date('date');                 // Tanggal izin
            $table->string('type');               // Jenis: 'sick', 'leave', 'permit' (Sakit, Cuti, Izin)
            $table->text('reason');               // Alasan izin
            $table->string('image_url')->nullable(); // Foto surat dokter atau bukti pendukung
            
            // Approval System
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('note')->nullable();     // Catatan dari admin (misal: alasan ditolak)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
