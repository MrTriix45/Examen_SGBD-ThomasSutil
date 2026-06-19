<?php

// Controller
use App\Http\Controllers\AskController;
use App\Http\Controllers\IaSettingsController;
use Illuminate\Support\Facades\Route;

// Route pour la page d'accueil qui redirige vers la page de connexion
Route::get('/', function () {
    return redirect('/login');
});

// Routes protégées par authentification
Route::middleware('auth')->group(function () {
    Route::get('/ask', [AskController::class, 'index'])->name('ask.index');
    Route::post('/ask', [AskController::class, 'ask'])->name('ask.post');

    // ROUTE - IA SETTINGS
    Route::get('/iasettings', [IaSettingsController::class, 'index'])->name('ia-settings.index');
    Route::post('/iasettings', [IaSettingsController::class, 'store'])->name('ia-settings.store');
});

require __DIR__.'/settings.php';
