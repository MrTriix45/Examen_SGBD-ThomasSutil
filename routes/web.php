<?php

// Controller
use App\Http\Controllers\AskController;
use App\Http\Controllers\IaSettingsController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;

// Route pour la page d'accueil qui redirige vers la page de connexion
Route::get('/', function () {
    return redirect('/login');
});

// Routes protégées par authentification
Route::middleware('auth')->group(function () {
    // ROUTE - ASK
    Route::get('/ask', [AskController::class, 'index'])->name('ask.index');
    Route::post('/ask', [AskController::class, 'ask'])->name('ask.post');

    // ROUTE - IA SETTINGS
    Route::get('/iasettings', [IaSettingsController::class, 'index'])->name('ia-settings.index');
    Route::post('/iasettings', [IaSettingsController::class, 'store'])->name('ia-settings.store');

    // ROUTE - USER SETTINGS
    Route::resource('/user', UserController::class);

    // ROUTE - ASK STREAM
    Route::get('/ask-stream', [\App\Http\Controllers\AskStreamController::class, 'index'])->name('ask-stream.index');
    Route::post('/ask-stream', [\App\Http\Controllers\AskStreamController::class, 'stream'])->name('ask-stream.stream');
});

require __DIR__.'/settings.php';
