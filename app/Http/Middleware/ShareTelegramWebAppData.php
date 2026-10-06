<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\TelegramUser;

class ShareTelegramWebAppData
{
    public function handle(Request $request, Closure $next)
    {
        $telegramAuth = new TelegramAuth();
        $isTelegramWebApp = $telegramAuth->isTelegramWebApp($request);

        // Клиентский SDK надёжнее серверных эвристик — уважаем уже установленную сессию WebApp
        if (!$isTelegramWebApp && $request->session()->get('is_telegram_webapp')) {
            $isTelegramWebApp = true;
        }

        $request->attributes->set('isTelegramWebApp', $isTelegramWebApp);
        View::share('isTelegramWebApp', $isTelegramWebApp);
        View::share('isDevelopmentMode', app()->environment('local'));

        // Восстанавливаем авторизацию из сессии Telegram
        if ($request->session()->has('telegram_user_id') && !Auth::check()) {
            $user = TelegramUser::findByTelegramId((int) $request->session()->get('telegram_user_id'));
            if ($user) {
                Auth::login($user);
            }
        }

        // Опциональный auto-login только для разработки (явно через .env)
        if (
            !Auth::check()
            && app()->environment('local')
            && filter_var(env('TELEGRAM_DEV_AUTO_LOGIN', false), FILTER_VALIDATE_BOOLEAN)
        ) {
            $dev = config('rostpack.telegram.dev_user', []);
            $user = TelegramUser::createOrUpdateFromTelegram([
                'id' => $dev['id'] ?? 123456789,
                'first_name' => $dev['first_name'] ?? 'Test',
                'last_name' => $dev['last_name'] ?? 'User',
                'username' => $dev['username'] ?? 'testuser',
                'photo_url' => $dev['photo_url'] ?? null,
            ]);
            Auth::login($user);
            $request->session()->put('telegram_user_id', $user->telegram_id);
        }

        return $next($request);
    }
}
