<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\SetupController;
use App\Http\Controllers\Dashboard\TowerUnitController;
use App\Http\Controllers\Dashboard\VillaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::resource('villas', VillaController::class);
    Route::resource('tower-units', TowerUnitController::class)->only(['index', 'show'])->parameters(['tower-units' => 'towerUnit']);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    Route::prefix('setup')->name('setup.')->group(function () {
        Route::get('stages', [SetupController::class, 'stages'])->name('stages');
        Route::get('statuses', [SetupController::class, 'statuses'])->name('statuses');
        Route::get('engineers', [SetupController::class, 'engineers'])->name('engineers');
    });
});
