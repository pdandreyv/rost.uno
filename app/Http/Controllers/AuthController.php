<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\TelegramAuth;
use App\Models\TelegramUser;
use App\Services\TelegramService;

class AuthController extends Controller
{
    /**
     * Telegram сервис
     */
    protected TelegramService $telegramService;

    /**
     * Create a new controller instance.
     */
    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }


    /**
     * Handle Telegram Login Widget redirect (data-auth-url / login_url).
     */
    public function handleTelegramCallback(Request $request)
    {
        try {
            $telegramData = $request->query();

            if (!$this->validateLoginWidgetData($telegramData)) {
                Log::error('Telegram callback validation failed:', $telegramData);

                return redirect()->route('home')
                    ->with('error', 'Неверные данные авторизации Telegram');
            }

            $user = TelegramUser::createOrUpdateFromTelegram($telegramData);
            Auth::login($user, true);
            $request->session()->regenerate();
            $request->session()->put('telegram_user_id', $user->telegram_id);
            $request->session()->save();

            Log::info('User authenticated via Telegram callback:', [
                'user_id' => $user->id,
                'telegram_id' => $user->telegram_id,
            ]);

            return redirect()->route('home')
                ->with('success', 'Добро пожаловать, ' . $user->display_name . '!');
        } catch (\Exception $e) {
            Log::error('Telegram callback error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString(),
            ]);

            return redirect()->route('home')
                ->with('error', 'Ошибка авторизации. Попробуйте позже.');
        }
    }

    /**
     * Handle Telegram authorization callback.
     */
    public function handleTelegramAuth(Request $request)
    {
        try {
            $telegramData = $request->all();

            // Проверяем, что данные пришли от Telegram Login Widget
            if (!$this->validateLoginWidgetData($telegramData)) {
                Log::error('Telegram data validation failed:', $telegramData);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Неверные данные авторизации'
                ], 400);
            }

            $user = TelegramUser::createOrUpdateFromTelegram($telegramData);
            Auth::login($user, true);
            $request->session()->regenerate();
            $request->session()->put('telegram_user_id', $user->telegram_id);
            $request->session()->save();

            Log::info('User authenticated via Login Widget POST:', [
                'user_id' => $user->id,
                'telegram_id' => $user->telegram_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Добро пожаловать, ' . $user->display_name . '!',
                'redirect' => route('home'),
            ]);

        } catch (\Exception $e) {
            Log::error('Telegram auth error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка авторизации. Попробуйте позже.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('telegram_user_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Вы успешно вышли из системы');
    }

    /**
     * Validate Telegram data.
     */
    protected function validateTelegramData(array $data): bool
    {
        // Проверяем наличие обязательных полей
        if (!isset($data['id']) || !isset($data['first_name'])) {
            return false;
        }

        // Проверяем, что это числовой ID
        if (!is_numeric($data['id'])) {
            return false;
        }

        // Дополнительные проверки можно добавить здесь
        // Например, проверка подписи от Telegram Bot API

        return true;
    }

    /**
     * Validate Telegram Login Widget / Login URL payload.
     *
     * @see https://core.telegram.org/widgets/login#checking-authorization
     */
    protected function validateLoginWidgetData(array $data): bool
    {
        $allowed = ['id', 'first_name', 'last_name', 'username', 'photo_url', 'auth_date', 'hash'];
        $data = array_intersect_key($data, array_flip($allowed));

        if (!isset($data['id'], $data['first_name'], $data['hash'], $data['auth_date'])) {
            return false;
        }

        if (!is_numeric($data['id'])) {
            return false;
        }

        if ((int) $data['auth_date'] < time() - 86400) {
            Log::warning('Telegram login data is too old', ['auth_date' => $data['auth_date']]);
            return false;
        }

        $botToken = (string) config('rostpack.telegram.bot_token');
        if ($botToken === '') {
            return false;
        }

        $hash = $data['hash'];
        unset($data['hash']);

        ksort($data);
        $checkLines = [];
        foreach ($data as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $checkLines[] = $key . '=' . $value;
        }

        $secretKey = hash('sha256', $botToken, true);
        $computedHash = hash_hmac('sha256', implode("\n", $checkLines), $secretKey);

        return hash_equals($computedHash, $hash);
    }

    /**
     * Handle Telegram WebApp authorization.
     */
    public function handleWebAppAuth(Request $request)
    {
        try {
            $initData = $request->input('initData')
                ?: $request->header('X-Telegram-WebApp-Data');

            Log::info('[tg-auth-v3] webapp auth start', [
                'has_init_data' => !empty($initData),
                'init_data_len' => is_string($initData) ? strlen($initData) : 0,
                'has_nested_user' => is_array($request->input('user')),
                'nested_user_id' => data_get($request->input('user'), 'id'),
                'request_keys' => array_keys($request->except(['initData'])),
            ]);

            $telegramAuth = new TelegramAuth();
            $userData = is_string($initData) ? $telegramAuth->validateInitData($initData) : null;
            $authSource = $userData ? 'initData_hash' : null;

            if (!$userData && is_array($request->input('user'))) {
                $nestedUser = $request->input('user');
                if ($this->validateTelegramData($nestedUser)) {
                    $userData = $nestedUser;
                    $authSource = 'nested_user_fallback';
                    Log::warning('[tg-auth-v3] initData hash failed, fallback to nested user', [
                        'telegram_id' => $nestedUser['id'] ?? null,
                    ]);
                }
            }

            if (!$userData && $this->validateTelegramData($request->all())) {
                $userData = $request->all();
                $authSource = 'top_level_fallback';
            }

            if (!$userData) {
                Log::error('[tg-auth-v3] webapp auth rejected', [
                    'keys' => array_keys($request->except(['initData'])),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Неверные данные авторизации Mini App'
                ], 400);
            }

            $user = TelegramUser::createOrUpdateFromTelegram($userData);
            Auth::login($user, true);
            $request->session()->regenerate();
            $request->session()->put('telegram_user_id', $user->telegram_id);
            $request->session()->put('is_telegram_webapp', true);
            $request->session()->save();

            Log::info('[tg-auth-v3] webapp auth success', [
                'source' => $authSource,
                'user_id' => $user->id,
                'telegram_id' => $user->telegram_id,
                'session_id' => $request->session()->getId(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Добро пожаловать, ' . $user->display_name . '!',
                'redirect' => route('home'),
            ]);

        } catch (\Exception $e) {
            Log::error('[tg-auth-v3] webapp auth exception: ' . $e->getMessage(), [
                'request_data' => $request->except(['initData']),
                'exception' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка авторизации. Попробуйте позже.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Telegram bot info for authorization page.
     */
    public function getBotInfo()
    {
        try {
            $botInfo = $this->telegramService->getBotInfo();
            
            if (!$botInfo) {
                return response()->json(['error' => 'Bot info not available'], 500);
            }

            return response()->json([
                'bot_username' => $botInfo['username'],
                'bot_name' => $botInfo['first_name']
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting bot info: ' . $e->getMessage());
            return response()->json(['error' => 'Bot info not available'], 500);
        }
    }
}
