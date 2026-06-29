<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\KnockoutPredictionsController;
use App\Http\Controllers\PredictionsController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Login.Login');
})->name('login');

Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/registrar', [RegisterController::class, 'store'])->name('register.store');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/regras', function () {
        return view('rules.index');
    })->name('rules.index');

    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');

    Route::get('/palpites', [PredictionsController::class, 'index'])->name('predictions.index');
    Route::get('/palpites/placares', [PredictionsController::class, 'scores'])->name('predictions.scores');
    Route::post('/palpites', [PredictionsController::class, 'store'])->name('predictions.store');

    Route::get('/eliminatorias', [KnockoutPredictionsController::class, 'index'])->name('knockout.index');
    Route::post('/eliminatorias', [KnockoutPredictionsController::class, 'store'])->name('knockout.store');
});
