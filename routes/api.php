<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\MenuItemController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\SetupController;
use App\Http\Controllers\Api\V1\TowerSiteUpdateController;
use App\Http\Controllers\Api\V1\TowerTaskController;
use App\Http\Controllers\Api\V1\TowerUnitController;
use App\Http\Controllers\Api\V1\VillaController;
use App\Http\Controllers\Api\V1\VillaSiteUpdateController;
use App\Http\Controllers\Api\V1\VillaTaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::apiResource('villas', VillaController::class);
        Route::apiResource('villas.tasks', VillaTaskController::class)->only(['index', 'store']);
        Route::prefix('villa-tasks')->name('villa-tasks.')->group(function () {
            Route::get('{task}', [VillaTaskController::class, 'show'])->name('show');
            Route::match(['put', 'patch'], '{task}', [VillaTaskController::class, 'update'])->name('update');
            Route::delete('{task}', [VillaTaskController::class, 'destroy'])->name('destroy');
        });
        Route::apiResource('tower-units', TowerUnitController::class);
        Route::apiResource('tower-units.tasks', TowerTaskController::class)->only(['index', 'store']);
        Route::prefix('tower-tasks')->name('tower-tasks.')->group(function () {
            Route::get('{task}', [TowerTaskController::class, 'show'])->name('show');
            Route::match(['put', 'patch'], '{task}', [TowerTaskController::class, 'update'])->name('update');
            Route::delete('{task}', [TowerTaskController::class, 'destroy'])->name('destroy');
        });
        Route::apiResource('villas.updates', VillaSiteUpdateController::class)->only(['index', 'store']);
        Route::prefix('villa-updates')->name('villa-updates.')->group(function () {
            Route::get('{update}', [VillaSiteUpdateController::class, 'show'])->name('show');
            Route::delete('{update}', [VillaSiteUpdateController::class, 'destroy'])->name('destroy');
        });
        Route::apiResource('tower-units.updates', TowerSiteUpdateController::class)->only(['index', 'store']);
        Route::prefix('tower-updates')->name('tower-updates.')->group(function () {
            Route::get('{update}', [TowerSiteUpdateController::class, 'show'])->name('show');
            Route::delete('{update}', [TowerSiteUpdateController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('sales-summary', [ReportController::class, 'salesSummary'])->name('sales');
            Route::get('structural-status', [ReportController::class, 'structuralStatus'])->name('structural');
            Route::get('finishing-status', [ReportController::class, 'finishingStatus'])->name('finishing');
            Route::get('facade-status', [ReportController::class, 'facadeStatus'])->name('facade');
            Route::get('dashboard-stats', [ReportController::class, 'dashboardStats'])->name('dashboard');
        });
        Route::get('setup/stages', [SetupController::class, 'stages'])->name('setup.stages');
        Route::get('setup/statuses', [SetupController::class, 'statuses'])->name('setup.statuses');
        Route::get('setup/engineers', [SetupController::class, 'engineers'])->name('setup.engineers');
        Route::get('menu-items', [MenuItemController::class, 'index'])->name('menu-items.index');
    });
});
