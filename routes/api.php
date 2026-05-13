<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendenceClockController;
use App\Http\Controllers\StatsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/attendence', [AttendenceClockController::class, 'store'])->name('create.attendence');
    Route::patch('/attendence/{id}', [AttendenceClockController::class, 'update'])->name('update.attendence');
    Route::get('/attendence', [AttendenceClockController::class, 'getAll'])->name('attendence.index');
    Route::get('/attendence/stats', [StatsController::class, 'getStatsAttend'])->name('stat.attendence');
});
