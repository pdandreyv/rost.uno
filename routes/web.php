<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Маршрут для авторизации - перенаправляем на rostpack
Route::get('/login', function () {
    return redirect()->route('rostpack.index');
})->name('login');
