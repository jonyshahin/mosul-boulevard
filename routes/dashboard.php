<?php

use App\Http\Controllers\Dashboard\ContactMessageController;
use App\Http\Controllers\Dashboard\CustomerController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\InspectionRequestsPageController;
use App\Http\Controllers\Dashboard\NotificationRecipientRulesPageController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\RequestTypesPageController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\SetupController;
use App\Http\Controllers\Dashboard\TowerUnitController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\VillaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('customers', CustomerController::class);
    Route::resource('villas', VillaController::class);
    Route::resource('tower-units', TowerUnitController::class)->parameters(['tower-units' => 'towerUnit']);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    Route::prefix('setup')->name('setup.')->group(function () {
        Route::get('stages', [SetupController::class, 'stages'])->name('stages');
        Route::get('statuses', [SetupController::class, 'statuses'])->name('statuses');
        Route::get('engineers', [SetupController::class, 'engineers'])->name('engineers');
    });

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('request-types', [RequestTypesPageController::class, 'index'])
            ->name('request-types.index');
        Route::get('notification-recipient-rules', [NotificationRecipientRulesPageController::class, 'index'])
            ->name('notification-recipient-rules.index');
    });

    Route::prefix('inspection-requests')->name('inspection-requests.')->group(function () {
        Route::get('/', [InspectionRequestsPageController::class, 'index'])->name('index');
        Route::get('/create', [InspectionRequestsPageController::class, 'create'])->name('create');
        Route::get('/{id}', [InspectionRequestsPageController::class, 'show'])->whereNumber('id')->name('show');
        Route::get('/{id}/edit', [InspectionRequestsPageController::class, 'edit'])->whereNumber('id')->name('edit');
    });

    Route::resource('messages', ContactMessageController::class)->only(['index', 'show', 'destroy'])->parameters(['messages' => 'contactMessage']);
    Route::post('messages/{contactMessage}/reply', [ContactMessageController::class, 'reply'])->name('messages.reply');
});
