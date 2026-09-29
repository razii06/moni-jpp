<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DokumentasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobPackageController;
use Illuminate\Support\Facades\Route;

// Landing Page Monitoring (Public)
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/monitoring/job-packages/{jobPackage}', [HomeController::class, 'show'])
    ->name('public.job-packages.show');

// Breeze Authentication Routes
require __DIR__ . '/auth.php';

// Panel Admin JPP
Route::middleware(['auth', 'throttle:120,1'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dokumentasi', [DokumentasiController::class, 'index'])->name('dokumentasi.index');
    Route::post('/dokumentasi', [DokumentasiController::class, 'store'])->name('dokumentasi.store');
    Route::delete('/dokumentasi/{dokumentasi}', [DokumentasiController::class, 'destroy'])->name('dokumentasi.destroy');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Custom Job Package Routes (Wajib didefinisikan sebelum Route::resource)
    Route::get('/job-packages/export', [JobPackageController::class, 'export'])
        ->middleware('throttle:5,1')
        ->name('job-packages.export');
    Route::get('/job-packages/{jobPackage}/download/{field}', [JobPackageController::class, 'downloadDocument'])->name('job-packages.download');
    Route::delete('/job-packages/{jobPackage}/delete-document/{field}', [JobPackageController::class, 'deleteDocument'])->name('job-packages.delete-document');
    Route::patch('/job-packages/{jobPackage}/cancel', [JobPackageController::class, 'cancel'])
        ->middleware('throttle:20,1')
        ->name('job-packages.cancel');
    Route::patch('/job-packages/{jobPackage}/reactivate', [JobPackageController::class, 'reactivate'])
        ->middleware('throttle:20,1')
        ->name('job-packages.reactivate');

    // Main Resource Route (CRUD)
    Route::resource('job-packages', JobPackageController::class);
});