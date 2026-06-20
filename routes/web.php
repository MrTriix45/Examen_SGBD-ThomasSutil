<?php

// Controller
use App\Http\Controllers\AskController;
use App\Http\Controllers\UserPreferenceIaController;
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
    Route::get('/iasettings', [UserPreferenceIaController::class, 'index'])->name('ia-settings.index');
    Route::post('/iasettings', [UserPreferenceIaController::class, 'update'])->name('ia-settings.update');

    // ROUTE - USER SETTINGS
    Route::get('/user/usage', [UserController::class, 'usage'])->name('user.usage');
    Route::resource('/user', UserController::class);

    // ROUTE - ASK STREAM
    Route::get('/ask-stream', [\App\Http\Controllers\AskStreamController::class, 'index'])->name('ask-stream.index');
    Route::post('/ask-stream', [\App\Http\Controllers\AskStreamController::class, 'stream'])->name('ask-stream.stream');
});

require __DIR__.'/settings.php';
