<?php

use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\VillaController;
use Illuminate\Support\Facades\Route;

Route::name('website.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/villas', [VillaController::class, 'index'])->name('villas.index');
    Route::get('/villas/{villa}', [VillaController::class, 'show'])->name('villas.show');
});
