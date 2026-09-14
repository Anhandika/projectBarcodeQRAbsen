<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MonitorController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin_sekolah'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitor', [MonitorController::class, 'index'])->name('monitor');
    Route::get('/monitor/refresh', [MonitorController::class, 'refresh'])->name('monitor.refresh');
});

Route::middleware(['auth', 'role:guru,siswa'])->group(function () {
    Route::get('/scan', [AttendanceController::class, 'scanPage'])->name('attendance.scan');
    Route::post('/attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan.store');
});
