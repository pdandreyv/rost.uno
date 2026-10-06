<?php

namespace App\Services;

use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Objects\Message;
use Illuminate\Support\Facades\Log;
use App\Models\TelegramUser;

class TelegramService
{
    /**
     * Экземпляр Telegram Bot API
     */
    protected Api $telegram;

    /**
     * Конфигурация бота
     */
    protected array $config;

    /**
     * Создать новый экземпляр Telegram сервиса
     */
    public function __construct()
    {
        $this->config = config('rostpack.telegram', []);
        
        if (empty($this->config['bot_token'])) {
            throw new \InvalidArgumentException('Telegram bot token не настроен');
        }

        $this->telegram = new Api($this->config['bot_token']);
    }

    /**
     * Обработать входящее обновление от Telegram
     */
    public function handleUpdate(Update $update): void
    {
        try {
            $message = $update->getMessage();
            
            if (!$message) {
                return;
            }

            $this->processMessage($message);
            
        } catch (\Exception $e) {
            Log::error('Ошибка обработки Telegram обновления: ' . $e->getMessage());
        }
    }

    /**
     * Обработать сообщение
     */
    protected function processMessage(Message $message): void
    {
        $text = $message->getText();
        $chatId = $message->getChat()->getId();

        if (!$text) {
            return;
        }

        // Обработка команды /start, в том числе /start login
        if ($text === '/start' || str_starts_with($text, '/start ')) {
            $this->handleStartCommand($chatId, $message);
            return;
        }

        // Обработка других команд
        if (str_starts_with($text, '/')) {
            $this->handleCommand($text, $chatId, $message);
            return;
        }

        // Обработка обычных сообщений
        $this->handleTextMessage($text, $chatId, $message);
    }

    /**
     * Обработать команду /start
     */
    protected function handleStartCommand(int $chatId, Message $message): void
    {
        // Сохраняем или обновляем пользователя
        $user = $this->saveOrUpdateUser($message->getFrom()->toArray());
        
        $firstName = $user->first_name ?: 'Пользователь';
        $lastName = $user->last_name ?: '';
        $fullName = trim($firstName . ' ' . $lastName);

        $welcomeText = "Привет, {$fullName}! 👋\n\n";
        $welcomeText .= "Добро пожаловать в бот RostPack! 🚀\n\n";
        
        if ($user->wasRecentlyCreated) {
            $welcomeText .= "🎉 Вы успешно зарегистрированы в системе!\n\n";
        } else {
            $welcomeText .= "👋 Рады видеть вас снова!\n\n";
        }
        
        $welcomeText .= "Доступные команды:\n";
        $welcomeText .= "/start - Показать это сообщение\n";
        $welcomeText .= "/help - Получить помощь\n";
        $welcomeText .= "/info - Информация о боте\n";
        $welcomeText .= "/profile - Мой профиль\n\n";
        $welcomeText .= "Чтобы войти на сайт, нажмите кнопку ниже.";

        $this->sendMessage($chatId, $welcomeText, $this->loginButtonOptions());
    }

    /**
     * Обработать команду
     */
    protected function handleCommand(string $command, int $chatId, Message $message): void
    {
        switch ($command) {
            case '/help':
                $this->sendMessage($chatId, "Помощь по командам:\n\n/start - Начать работу с ботом\n/help - Показать эту справку\n/info - Информация о боте\n/profile - Мой профиль");
                break;
                
            case '/info':
                $info = "Информация о боте:\n\n";
                $info .= "Версия: " . $this->getVersion() . "\n";
                $info .= "Разработчик: RostPack Team\n";
                $info .= "Описание: Бот для работы с пакетом RostPack";
                $this->sendMessage($chatId, $info);
                break;
                
            case '/profile':
                $this->handleProfileCommand($chatId, $message);
                break;
                
            default:
                $this->sendMessage($chatId, "Неизвестная команда. Используйте /help для получения списка команд.");
        }
    }

    /**
     * Обработать текстовое сообщение
     */
    protected function handleTextMessage(string $text, int $chatId, Message $message): void
    {
        $response = "Вы написали: \"{$text}\"\n\n";
        $response .= "Это обычное сообщение. Используйте команды для получения дополнительной информации!";
        
        $this->sendMessage($chatId, $response);
    }

    /**
     * Отправить сообщение
     */
    public function sendMessage(int $chatId, string $text, array $options = []): ?Message
    {
        try {
            $params = array_merge([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML'
            ], $options);

            return $this->telegram->sendMessage($params);
            
        } catch (\Exception $e) {
            Log::error('Ошибка отправки сообщения в Telegram: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Кнопка Login URL для завершения авторизации на сайте.
     */
    protected function loginButtonOptions(): array
    {
        $callbackUrl = $this->getAuthCallbackUrl();

        if ($callbackUrl === '' || !str_starts_with($callbackUrl, 'https://')) {
            return [];
        }

        return [
            'reply_markup' => json_encode([
                'inline_keyboard' => [[
                    [
                        'text' => 'Войти на сайт',
                        'login_url' => [
                            'url' => $callbackUrl,
                            'request_write_access' => true,
                        ],
                    ],
                ]],
            ]),
        ];
    }

    /**
     * HTTPS URL callback для Telegram Login Widget / Login URL.
     */
    protected function getAuthCallbackUrl(): string
    {
        $webhook = (string) ($this->config['webhook_url'] ?? '');
        if ($webhook !== '') {
            $parts = parse_url($webhook);
            if (!empty($parts['scheme']) && !empty($parts['host'])) {
                return $parts['scheme'] . '://' . $parts['host'] . '/rostpack/auth/telegram/callback';
            }
        }

        $appUrl = (string) config('app.url');
        if (str_starts_with($appUrl, 'https://')) {
            return rtrim($appUrl, '/') . '/rostpack/auth/telegram/callback';
        }

        return '';
    }

    /**
     * Установить webhook
     */
    public function setWebhook(string $url): bool
    {
        try {
            $response = $this->telegram->setWebhook(['url' => $url]);
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Ошибка установки webhook: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Удалить webhook
     */
    public function removeWebhook(): bool
    {
        try {
            $response = $this->telegram->removeWebhook();
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Ошибка удаления webhook: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Получить информацию о боте
     */
    public function getBotInfo(): ?array
    {
        try {
            $response = $this->telegram->getMe();
            return $response->toArray();
            
        } catch (\Exception $e) {
            Log::error('Ошибка получения информации о боте: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Получить версию пакета
     */
    protected function getVersion(): string
    {
        return '1.0.0';
    }

    /**
     * Получить экземпляр Telegram API
     */
    public function getTelegram(): Api
    {
        return $this->telegram;
    }

    /**
     * Сохранить или обновить пользователя из данных Telegram
     */
    protected function saveOrUpdateUser(array $telegramData): TelegramUser
    {
        try {
            return TelegramUser::createOrUpdateFromTelegram($telegramData);
        } catch (\Exception $e) {
            Log::error('Ошибка сохранения пользователя Telegram: ' . $e->getMessage(), [
                'telegram_data' => $telegramData
            ]);
            
            // Возвращаем пустую модель в случае ошибки
            return new TelegramUser();
        }
    }

    /**
     * Обработать команду /profile
     */
    protected function handleProfileCommand(int $chatId, Message $message): void
    {
        try {
            $user = $this->saveOrUpdateUser($message->getFrom()->toArray());
            
            $profileText = "👤 Ваш профиль:\n\n";
            $profileText .= "ID: {$user->telegram_id}\n";
            $profileText .= "Имя: {$user->display_name}\n";
            
            if ($user->hasTelegramUsername()) {
                $profileText .= "Username: @{$user->telegram_username}\n";
            }
            
            if ($user->city) {
                $profileText .= "Город: {$user->city}\n";
            }
            
            if ($user->phone_number) {
                $profileText .= "Телефон: {$user->phone_number}\n";
            }
            
            $profileText .= "Статус: " . ($user->isVerified() ? "✅ Подтвержден" : "⏳ Не подтвержден") . "\n";
            $profileText .= "Дата регистрации: " . $user->created_at->format('d.m.Y H:i') . "\n";
            
            $this->sendMessage($chatId, $profileText);
            
        } catch (\Exception $e) {
            Log::error('Ошибка получения профиля: ' . $e->getMessage());
            $this->sendMessage($chatId, "❌ Ошибка получения профиля. Попробуйте позже.");
        }
    }

    /**
     * Получить пользователя по Telegram ID
     */
    public function getUserByTelegramId(int $telegramId): ?TelegramUser
    {
        return TelegramUser::findByTelegramId($telegramId);
    }

    /**
     * Получить пользователя по Telegram username
     */
    public function getUserByTelegramUsername(string $username): ?TelegramUser
    {
        return TelegramUser::findByTelegramUsername($username);
    }

    /**
     * Получить всех пользователей
     */
    public function getAllUsers(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return TelegramUser::whereNotNull('telegram_id')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Получить статистику пользователей
     */
    public function getUserStats(): array
    {
        $totalUsers = TelegramUser::whereNotNull('telegram_id')->count();
        $verifiedUsers = TelegramUser::whereNotNull('telegram_id')->where('verified', true)->count();
        $todayUsers = TelegramUser::whereNotNull('telegram_id')
            ->whereDate('created_at', today())
            ->count();
        
        return [
            'total' => $totalUsers,
            'verified' => $verifiedUsers,
            'today' => $todayUsers,
            'unverified' => $totalUsers - $verifiedUsers
        ];
    }
}
