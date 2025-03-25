<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SanitaryController;
use App\Http\Controllers\PrintController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

Route::get('/sanitary', [SanitaryController::class, 'sanitary'])->name('sanitary');
Route::post('new-permit', [SanitaryController::class, 'newPermit'])->name('newPermit');
Route::put('update-permit/{id}', [SanitaryController::class, 'updatePermit'])->name('updatePermit');
Route::put('/sanitary-permit/{id}/inspected', [SanitaryController::class, 'inspected'])->name('sanitaryPermit.inspected');
Route::put('/sanitary-permit/{id}/renewal', [SanitaryController::class, 'renewalPermit'])->name('sanitaryPermit.renewal');

Route::get('/sanitary/{id}/print', [PrintController::class, 'print'])->name('sanitary.print');



