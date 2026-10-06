<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TelegramUser;
use Symfony\Component\HttpFoundation\Response;

class TelegramAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('TelegramAuth middleware called', ['url' => $request->url(), 'session_id' => $request->session()->getId()]);
        
        // Проверяем, есть ли данные авторизации Telegram в сессии
        $hasSession = $request->session()->has('telegram_user_id');
        \Log::info('Session check:', ['has_telegram_user_id' => $hasSession]);
        
        if ($hasSession) {
            $telegramUserId = $request->session()->get('telegram_user_id');
            $user = TelegramUser::findByTelegramId($telegramUserId);
            \Log::info('User lookup:', ['telegram_user_id' => $telegramUserId, 'user_found' => $user ? true : false]);
            
            if ($user) {
                Auth::login($user);
                \Log::info('User logged in via middleware:', ['user_id' => $user->id, 'telegram_id' => $user->telegram_id]);
                return $next($request);
            }
        }

        // Проверяем, является ли запрос от Telegram WebApp
        if ($this->isTelegramWebApp($request)) {
            \Log::info('Telegram WebApp detected, attempting auto-auth');
            
            // Пытаемся получить данные пользователя из Telegram WebApp
            $webAppData = $this->extractWebAppData($request);
            
            if ($webAppData && $this->validateWebAppData($webAppData)) {
                try {
                    // Создаем или обновляем пользователя
                    $user = TelegramUser::createOrUpdateFromTelegram($webAppData);
                    \Log::info('User created/updated from WebApp:', ['user' => $user->telegram_id]);
                    
                    // Авторизуем пользователя
                    Auth::login($user);
                    $request->session()->put('telegram_user_id', $user->telegram_id);
                    
                    \Log::info('User auto-authenticated via WebApp:', ['user_id' => $user->id, 'telegram_id' => $user->telegram_id]);
                    return $next($request);
                    
                } catch (\Exception $e) {
                    \Log::error('WebApp auto-auth failed:', ['error' => $e->getMessage()]);
                }
            }
        }

        \Log::info('User not authenticated, redirecting to home');
        return redirect()->route('home');
    }

    /**
     * Проверить, является ли запрос от Telegram WebApp
     */
    public function isTelegramWebApp(Request $request): bool
    {
        if ($request->header('X-Telegram-WebApp-Data') || $request->header('X-Telegram-Init-Data')) {
            return true;
        }

        if ($request->query('tgWebAppData') || $request->input('tgWebAppData') || $request->input('initData')) {
            return true;
        }

        $query = $request->query->all();
        if (isset($query['tgWebAppData']) || isset($query['tgWebAppVersion']) || isset($query['tgWebAppPlatform'])) {
            return true;
        }

        $userAgent = (string) $request->header('User-Agent', '');
        // Telegram Mini App WebView обычно содержит Telegram и не является обычным браузером
        if (
            str_contains($userAgent, 'Telegram')
            && (str_contains($userAgent, 'WebApp') || str_contains($userAgent, 'Telegram-Android') || str_contains($userAgent, 'Telegram-iOS'))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Извлечь данные пользователя из Telegram WebApp
     */
    public function extractWebAppData(Request $request): ?array
    {
        // Пытаемся получить данные из заголовка
        $webAppData = $request->header('X-Telegram-WebApp-Data');
        if ($webAppData) {
            return $this->parseWebAppData($webAppData);
        }

        // Пытаемся получить данные из параметра URL
        $initData = $request->query('tgWebAppData');
        if ($initData) {
            return $this->parseWebAppData($initData);
        }

        // Пытаемся получить данные из POST запроса
        $postData = $request->input('tgWebAppData') ?: $request->input('initData');
        if ($postData) {
            return $this->parseWebAppData($postData);
        }

        return null;
    }

    /**
     * Проверить подпись Mini App initData и вернуть пользователя.
     *
     * @see https://core.telegram.org/bots/webapps#validating-data-received-via-the-mini-app
     */
    public function validateInitData(?string $initData): ?array
    {
        if (!$initData) {
            \Log::warning('[tg-auth-v3] initData empty');
            return null;
        }

        $parsed = [];
        foreach (explode('&', $initData) as $pair) {
            if ($pair === '' || !str_contains($pair, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $pair, 2);
            $parsed[$key] = rawurldecode($value);
        }

        \Log::info('[tg-auth-v3] initData parsed', [
            'fields' => array_keys($parsed),
            'has_hash' => isset($parsed['hash']),
            'has_signature' => isset($parsed['signature']),
            'has_user' => isset($parsed['user']),
            'auth_date' => $parsed['auth_date'] ?? null,
            'now' => time(),
        ]);

        if (empty($parsed['hash']) || empty($parsed['auth_date']) || empty($parsed['user'])) {
            \Log::warning('[tg-auth-v3] initData missing required fields', [
                'fields' => array_keys($parsed),
            ]);
            return null;
        }

        if ((int) $parsed['auth_date'] < time() - 86400) {
            \Log::warning('[tg-auth-v3] initData too old', [
                'auth_date' => $parsed['auth_date'],
                'now' => time(),
            ]);
            return null;
        }

        $hash = $parsed['hash'];
        unset($parsed['hash']);
        ksort($parsed);

        $checkLines = [];
        foreach ($parsed as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            $checkLines[] = $key . '=' . $value;
        }

        $botToken = (string) config('rostpack.telegram.bot_token');
        if ($botToken === '') {
            \Log::error('[tg-auth-v3] bot token is empty');
            return null;
        }

        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);
        $computedHash = hash_hmac('sha256', implode("\n", $checkLines), $secretKey);
        $hashOk = hash_equals($computedHash, $hash);

        \Log::info('[tg-auth-v3] initData hash check', [
            'ok' => $hashOk,
            'check_fields' => array_keys($parsed),
            'token_len' => strlen($botToken),
        ]);

        if (!$hashOk) {
            return null;
        }

        $user = json_decode($parsed['user'], true);

        if (!is_array($user) || !isset($user['id'], $user['first_name'])) {
            \Log::warning('[tg-auth-v3] initData user json invalid');
            return null;
        }

        \Log::info('[tg-auth-v3] initData user ok', [
            'telegram_id' => $user['id'],
            'username' => $user['username'] ?? null,
        ]);

        return $user;
    }

    /**
     * Парсить данные Telegram WebApp
     */
    protected function parseWebAppData(string $data): ?array
    {
        try {
            // Telegram WebApp передает данные в формате URL-encoded
            parse_str($data, $parsed);
            
            // Ищем данные пользователя
            if (isset($parsed['user'])) {
                $userData = json_decode($parsed['user'], true);
                if ($userData && is_array($userData)) {
                    return $userData;
                }
            }

            // Альтернативный формат - прямые параметры
            if (isset($parsed['id']) && isset($parsed['first_name'])) {
                return [
                    'id' => $parsed['id'],
                    'first_name' => $parsed['first_name'],
                    'last_name' => $parsed['last_name'] ?? null,
                    'username' => $parsed['username'] ?? null,
                    'photo_url' => $parsed['photo_url'] ?? null,
                ];
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Failed to parse WebApp data:', ['error' => $e->getMessage(), 'data' => $data]);
            return null;
        }
    }

    /**
     * Валидировать данные Telegram WebApp
     */
    public function validateWebAppData(array $data): bool
    {
        // Проверяем наличие обязательных полей
        if (!isset($data['id']) || !isset($data['first_name'])) {
            return false;
        }

        // Проверяем, что ID является числом
        if (!is_numeric($data['id'])) {
            return false;
        }

        // Дополнительные проверки можно добавить здесь
        // Например, проверка подписи от Telegram Bot API

        return true;
    }
}
