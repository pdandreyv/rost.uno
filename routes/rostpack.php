<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginController;

Route::group(['prefix' => 'rostpack', 'as' => 'rostpack.', 'middleware' => ['web', 'rostpack.webapp']], function () {
    
    // Главная перенесена на /
    Route::get('/', function () {
        return redirect()->route('home');
    })->name('index');

    // Игровое меню (футер)
    Route::get('/mine', fn () => view('rostpack.mine'))->name('mine');
    Route::get('/friends', fn () => view('rostpack.friends'))->name('friends');
    Route::get('/settings', fn () => view('rostpack.settings'))->name('settings');
    
    // Страница авторизации
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    
    // API маршрут для получения информации
    Route::get('/info', function () {
        $package = app('rostpack');
        
        return response()->json([
            'name' => 'RostPack',
            'version' => $package->getVersion(),
            'message' => $package->hello(),
            'config' => $package->getConfig()
        ]);
    })->name('info');
    
    // Telegram маршруты
    Route::group(['prefix' => 'telegram', 'as' => 'telegram.'], function () {
        
        // Webhook для получения обновлений от Telegram
        Route::post('/webhook', [TelegramController::class, 'webhook'])
            ->name('webhook')
            ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
        
        // API маршруты для управления ботом
        Route::get('/bot-info', [TelegramController::class, 'botInfo'])->name('bot-info');
        Route::post('/set-webhook', [TelegramController::class, 'setWebhook'])->name('set-webhook');
        Route::post('/remove-webhook', [TelegramController::class, 'removeWebhook'])->name('remove-webhook');
        Route::post('/send-test', [TelegramController::class, 'sendTestMessage'])->name('send-test');
        
    });
    
    // Маршруты авторизации
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        // Исключаем только CSRF для Telegram авторизации, но оставляем session
        Route::post('/telegram', [AuthController::class, 'handleTelegramAuth'])
            ->name('telegram.handle')
            ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

        // Callback виджета / Login URL: Telegram редиректит сюда с данными пользователя
        Route::get('/telegram/callback', [AuthController::class, 'handleTelegramCallback'])
            ->name('telegram.callback');
        
        // Маршрут для Telegram WebApp авторизации
        Route::post('/webapp', [AuthController::class, 'handleWebAppAuth'])
            ->name('webapp.handle')
            ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
            
        Route::get('/bot-info', [AuthController::class, 'getBotInfo'])->name('bot-info');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
    
    // Все остальные маршруты требуют авторизации через Telegram
    Route::group(['middleware' => ['telegram.auth']], function () {
        
        // Публичные маршруты для блога
        Route::group(['prefix' => 'blog', 'as' => 'blog.'], function () {
            Route::get('/', [BlogController::class, 'index'])->name('index');
            Route::get('/{post}', [BlogController::class, 'show'])->name('show');
            Route::get('/create', [BlogController::class, 'create'])->name('create');
            Route::post('/', [BlogController::class, 'store'])->name('store');
            Route::get('/{post}/edit', [BlogController::class, 'edit'])->name('edit');
            Route::put('/{post}', [BlogController::class, 'update'])->name('update');
            Route::delete('/{post}', [BlogController::class, 'destroy'])->name('destroy');
        });
        
        // Публичные маршруты для страниц
        Route::group(['prefix' => 'pages', 'as' => 'pages.'], function () {
            Route::get('/', [PageController::class, 'index'])->name('index');
            Route::get('/{page}', [PageController::class, 'show'])->name('show');
            Route::get('/create', [PageController::class, 'create'])->name('create');
            Route::post('/', [PageController::class, 'store'])->name('store');
            Route::get('/{page}/edit', [PageController::class, 'edit'])->name('edit');
            Route::put('/{page}', [PageController::class, 'update'])->name('update');
            Route::delete('/{page}', [PageController::class, 'destroy'])->name('destroy');
        });
        
        // Личный кабинет (профиль)
        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::get('/', [DashboardController::class, 'profile'])->name('index');
            Route::post('/', [DashboardController::class, 'updateProfile'])->name('update');
            Route::put('/', [DashboardController::class, 'updateProfile'])->name('update.put');
            Route::patch('/', [DashboardController::class, 'updateProfile'])->name('update.patch');
        });
        
    });
    
});
