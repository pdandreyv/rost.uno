<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;

Route::middleware(['rostpack.webapp'])->group(function () {
    Route::get('/', [IndexController::class, 'index'])->name('home');
});

Route::get('/login', function () {
    return redirect()->route('home');
})->name('login');

require __DIR__.'/rostpack.php';
