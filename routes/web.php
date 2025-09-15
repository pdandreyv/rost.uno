<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Маршрут для авторизации - перенаправляем на rostpack
Route::get('/login', function () {
    return redirect()->route('rostpack.index');
})->name('login');
