<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;

class TelegramWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rostpack:telegram-webhook 
                            {action : Действие (set|remove|status)}
                            {--url= : URL для webhook (только для set)}
                            {--force : Принудительно выполнить действие}';

    /**
     * The console command description.
     */
    protected $description = 'Управление webhook для Telegram бота';

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
                case 'set':
                    return $this->setWebhook();
                case 'remove':
                    return $this->removeWebhook();
                case 'status':
                    return $this->checkWebhookStatus();
                default:
                    $this->error("Неизвестное действие: {$action}");
                    $this->info('Доступные действия: set, remove, status');
                    return 1;
            }
        } catch (\Exception $e) {
            $this->error("Ошибка выполнения команды: " . $e->getMessage());
            Log::error('Telegram webhook command error: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Установить webhook
     */
    protected function setWebhook(): int
    {
        $url = $this->option('url') ?: config('rostpack.telegram.webhook_url');

        if (!$url) {
            $this->error('URL для webhook не указан!');
            $this->info('Используйте --url=URL или настройте TELEGRAM_WEBHOOK_URL в .env');
            return 1;
        }

        // Проверяем, что URL использует HTTPS
        if (!str_starts_with($url, 'https://')) {
            $this->error('Webhook URL должен использовать HTTPS!');
            return 1;
        }

        $this->info("Установка webhook: {$url}");

        if (!$this->option('force')) {
            if (!$this->confirm('Продолжить установку webhook?')) {
                $this->info('Установка webhook отменена');
                return 0;
            }
        }

        $result = $this->telegramService->setWebhook($url);

        if ($result) {
            $this->info('✅ Webhook успешно установлен!');
            $this->line("URL: {$url}");
            
            // Проверяем информацию о боте
            $botInfo = $this->telegramService->getBotInfo();
            if ($botInfo) {
                $this->line("Бот: @{$botInfo['username']} ({$botInfo['first_name']})");
            }
        } else {
            $this->error('❌ Ошибка установки webhook!');
            $this->info('Проверьте токен бота и доступность URL');
        }

        return $result ? 0 : 1;
    }

    /**
     * Удалить webhook
     */
    protected function removeWebhook(): int
    {
        $this->info('Удаление webhook...');

        if (!$this->option('force')) {
            if (!$this->confirm('Вы уверены, что хотите удалить webhook?')) {
                $this->info('Удаление webhook отменено');
                return 0;
            }
        }

        $result = $this->telegramService->removeWebhook();

        if ($result) {
            $this->info('✅ Webhook успешно удален!');
        } else {
            $this->error('❌ Ошибка удаления webhook!');
        }

        return $result ? 0 : 1;
    }

    /**
     * Проверить статус webhook
     */
    protected function checkWebhookStatus(): int
    {
        $this->info('Проверка статуса webhook...');

        try {
            $botInfo = $this->telegramService->getBotInfo();
            
            if (!$botInfo) {
                $this->error('❌ Не удалось получить информацию о боте!');
                $this->info('Проверьте токен бота в настройках');
                return 1;
            }

            $this->info('🤖 Информация о боте:');
            $this->line("Имя: {$botInfo['first_name']}");
            $this->line("Username: @{$botInfo['username']}");
            $this->line("ID: {$botInfo['id']}");
            $this->line("Может присоединяться к группам: " . ($botInfo['can_join_groups'] ? 'Да' : 'Нет'));
            $this->line("Может читать все сообщения группы: " . ($botInfo['can_read_all_group_messages'] ? 'Да' : 'Нет'));
            $this->line("Поддерживает inline запросы: " . ($botInfo['supports_inline_queries'] ? 'Да' : 'Нет'));

            // Получаем информацию о webhook
            $webhookInfo = $this->getWebhookInfo();
            
            if ($webhookInfo) {
                $this->newLine();
                $this->info('🔗 Информация о webhook:');
                $this->line("URL: {$webhookInfo['url']}");
                $this->line("Pending update count: {$webhookInfo['pending_update_count']}");
                
                if (isset($webhookInfo['last_error_date'])) {
                    $this->warn("Последняя ошибка: " . date('Y-m-d H:i:s', $webhookInfo['last_error_date']));
                    $this->warn("Описание ошибки: {$webhookInfo['last_error_message']}");
                }
                
                if (isset($webhookInfo['max_connections'])) {
                    $this->line("Максимум соединений: {$webhookInfo['max_connections']}");
                }
            } else {
                $this->warn('⚠️ Webhook не установлен или недоступен');
            }

        } catch (\Exception $e) {
            $this->error("Ошибка получения информации: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Получить информацию о webhook
     */
    protected function getWebhookInfo(): ?array
    {
        try {
            $response = $this->telegramService->getTelegram()->getWebhookInfo();
            return $response->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting webhook info: ' . $e->getMessage());
            return null;
        }
    }
}
