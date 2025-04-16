<?php

use Illuminate\Support\Facades\Route;

// Página principal
Route::get('/', [App\Http\Controllers\AppController::class, 'index'])->name('app');

// Palpites
Route::post('/guess', [App\Http\Controllers\AppController::class, 'guess'])->name('guess');

// Verifica se a palavra existe
Route::post('/check-word/{word}', [App\Http\Controllers\AppController::class, 'checkWord'])->name('check-word');