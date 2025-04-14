<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\AppController::class, 'index'])->name('app');
Route::post('/guess', [App\Http\Controllers\AppController::class, 'guess'])->name('guess');