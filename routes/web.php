<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeathController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SanitaryController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\HealthCardController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

Route::middleware('admin')->group(function () {

    // dashboard

    // sanitary
    Route::get('/sanitary', [SanitaryController::class, 'sanitary'])->name('sanitary');
    Route::post('new-permit', [SanitaryController::class, 'newPermit'])->name('newPermit');
    Route::put('update-permit/{id}', [SanitaryController::class, 'updatePermit'])->name('updatePermit');
    Route::put('/sanitary-permit/{id}/inspected', [SanitaryController::class, 'inspected'])->name('sanitaryPermit.inspected');
    Route::put('/sanitary-permit/{id}/renewal', [SanitaryController::class, 'renewalPermit'])->name('sanitaryPermit.renewal');
    Route::get('/sanitary/print/{id}', [PrintController::class, 'print'])->name('sanitary.print');
    Route::delete('/sanitary/{id}', [SanitaryController::class, 'deleteSanitary'])->name('sanitary.delete');


    // health card
    Route::get('health-card', [HealthCardController::class, 'healthCard'])->name('healthCard');
    Route::post('new-health-card', [HealthCardController::class, 'newHealthCard'])->name('newHealthCard');
    Route::get('generate-pdf', [HealthCardController::class, 'generatePdf'])->name('generate_pdf');
    Route::put('health-cards/{id}/update', [HealthCardController::class, 'updateHealthCard'])->name('updateHealthCard');
    Route::delete('/health-card/{id}', [HealthCardController::class, 'deleteHealth'])->name('health-card.delete');

    Route::get('/reports/rhu', [PrintController::class, 'reportRhu'])->name('reports.rhu');


 
});

Route::middleware('staff')->group(function () {
    // death
    Route::get('/death-certificate', [DeathController::class, 'death'])->name('death.index');
    Route::post('/death-certificate', [DeathController::class, 'store'])->name('death.store');
    Route::put('/death-certificate/{death}', [DeathController::class, 'update'])->name('death.update');
    Route::get('/death-certificate/{id}/generate-pdf', [DeathController::class, 'generatePdf'])->name('death_generate_pdf');
    Route::delete('/death-record/{id}', [DeathController::class, 'delete'])->name('death-record.delete');
});



