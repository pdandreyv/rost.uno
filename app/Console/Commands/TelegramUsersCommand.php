<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use App\Models\TelegramUser;

class TelegramUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rostpack:telegram-users 
                            {action : Действие (list|stats|find|verify)}
                            {--telegram-id= : Telegram ID для поиска}
                            {--username= : Username для поиска}
                            {--limit=50 : Лимит записей для вывода}';

    /**
     * The console command description.
     */
    protected $description = 'Управление пользователями Telegram бота';

    /**
     * Telegram сервис
     */
    protected TelegramService $telegramService;

    /**
     * Create a new command instance.
     */
    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        try {
            switch ($action) {
                case 'list':
                    return $this->listUsers();
                case 'stats':
                    return $this->showStats();
                case 'find':
                    return $this->findUser();
                case 'verify':
                    return $this->verifyUser();
                default:
                    $this->error("Неизвестное действие: {$action}");
                    $this->info('Доступные действия: list, stats, find, verify');
                    return 1;
            }
        } catch (\Exception $e) {
            $this->error("Ошибка выполнения команды: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Показать список пользователей
     */
    protected function listUsers(): int
    {
        $limit = (int) $this->option('limit');
        
        $this->info("Список пользователей (лимит: {$limit}):");
        $this->newLine();

        $users = $this->telegramService->getAllUsers($limit);

        if ($users->isEmpty()) {
            $this->warn('Пользователи не найдены');
            return 0;
        }

        $headers = ['ID', 'Telegram ID', 'Username', 'Имя', 'Город', 'Статус', 'Дата регистрации'];
        $rows = [];

        foreach ($users as $user) {
            $rows[] = [
                $user->id,
                $user->telegram_id,
                $user->telegram_username ? '@' . $user->telegram_username : '-',
                $user->display_name,
                $user->city ?: '-',
                $user->isVerified() ? '✅' : '⏳',
                $user->created_at->format('d.m.Y H:i')
            ];
        }

        $this->table($headers, $rows);
        return 0;
    }

    /**
     * Показать статистику пользователей
     */
    protected function showStats(): int
    {
        $this->info('📊 Статистика пользователей:');
        $this->newLine();

        $stats = $this->telegramService->getUserStats();

        $this->line("Всего пользователей: <fg=green>{$stats['total']}</>");
        $this->line("Подтвержденных: <fg=green>{$stats['verified']}</>");
        $this->line("Неподтвержденных: <fg=yellow>{$stats['unverified']}</>");
        $this->line("Зарегистрировано сегодня: <fg=blue>{$stats['today']}</>");

        if ($stats['total'] > 0) {
            $verifiedPercent = round(($stats['verified'] / $stats['total']) * 100, 1);
            $this->line("Процент подтвержденных: <fg=cyan>{$verifiedPercent}%</>");
        }

        return 0;
    }

    /**
     * Найти пользователя
     */
    protected function findUser(): int
    {
        $telegramId = $this->option('telegram-id');
        $username = $this->option('username');

        if (!$telegramId && !$username) {
            $this->error('Укажите --telegram-id или --username для поиска');
            return 1;
        }

        $user = null;

        if ($telegramId) {
            $user = $this->telegramService->getUserByTelegramId((int) $telegramId);
        } elseif ($username) {
            $user = $this->telegramService->getUserByTelegramUsername($username);
        }

        if (!$user) {
            $this->warn('Пользователь не найден');
            return 1;
        }

        $this->info('👤 Найденный пользователь:');
        $this->newLine();

        $this->line("ID в системе: {$user->id}");
        $this->line("Telegram ID: {$user->telegram_id}");
        $this->line("Username: " . ($user->telegram_username ? '@' . $user->telegram_username : 'Не указан'));
        $this->line("Имя: {$user->display_name}");
        $this->line("Город: " . ($user->city ?: 'Не указан'));
        $this->line("Телефон: " . ($user->phone_number ?: 'Не указан'));
        $this->line("Статус: " . ($user->isVerified() ? '✅ Подтвержден' : '⏳ Не подтвержден'));
        $this->line("Дата регистрации: " . $user->created_at->format('d.m.Y H:i:s'));
        $this->line("Последнее обновление: " . $user->updated_at->format('d.m.Y H:i:s'));

        return 0;
    }

    /**
     * Подтвердить пользователя
     */
    protected function verifyUser(): int
    {
        $telegramId = $this->option('telegram-id');
        $username = $this->option('username');

        if (!$telegramId && !$username) {
            $this->error('Укажите --telegram-id или --username для подтверждения');
            return 1;
        }

        $user = null;

        if ($telegramId) {
            $user = $this->telegramService->getUserByTelegramId((int) $telegramId);
        } elseif ($username) {
            $user = $this->telegramService->getUserByTelegramUsername($username);
        }

        if (!$user) {
            $this->warn('Пользователь не найден');
            return 1;
        }

        if ($user->isVerified()) {
            $this->warn('Пользователь уже подтвержден');
            return 0;
        }

        $user->update(['verified' => true]);

        $this->info("✅ Пользователь {$user->display_name} успешно подтвержден!");

        // Отправляем уведомление пользователю
        try {
            $this->telegramService->sendMessage(
                $user->telegram_id,
                "🎉 Поздравляем! Ваш аккаунт был подтвержден администратором."
            );
            $this->line("📱 Уведомление отправлено пользователю");
        } catch (\Exception $e) {
            $this->warn("Не удалось отправить уведомление: " . $e->getMessage());
        }

        return 0;
    }
}
