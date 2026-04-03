<?php

use App\Http\Controllers\Website\ContactController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\ProgressController;
use App\Http\Controllers\Website\TowerUnitController;
use App\Http\Controllers\Website\VillaController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->name('website.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/villas', [VillaController::class, 'index'])->name('villas.index');
    Route::get('/villas/{villa}', [VillaController::class, 'show'])->name('villas.show');
    Route::get('/towers', [TowerUnitController::class, 'index'])->name('towers.index');
    Route::get('/towers/{towerUnit}', [TowerUnitController::class, 'show'])->name('towers.show');
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
});
