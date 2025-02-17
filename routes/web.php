<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoxesController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/locations', [BoxesController::class, 'index'])->name('locations');
    Route::post('/web/boxes', [BoxesController::class, 'store'])->name('boxes.store');
    Route::put('/web/boxes/{box}', [BoxesController::class, 'edit'])->name('boxes.edit');
    Route::post('/web/boxes/{box}/toggle-status', [BoxesController::class, 'toggleStatus'])->name('boxes.toggleStatus');
    Route::delete('/web/boxes/{box}', [BoxesController::class, 'delete'])->name('boxes.delete');

    Route::get('/reservations', [BoxesController::class, 'reservations'])->name('reservations');
});

require __DIR__.'/auth.php';
