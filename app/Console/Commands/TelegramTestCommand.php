<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;

class TelegramTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rostpack:telegram-test 
                            {chat_id : ID чата для отправки сообщения}
                            {--message= : Текст сообщения (по умолчанию: тестовое сообщение)}
                            {--parse-mode=HTML : Режим парсинга (HTML, Markdown, MarkdownV2)}';

    /**
     * The console command description.
     */
    protected $description = 'Отправить тестовое сообщение через Telegram бота';

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
        $chatId = $this->argument('chat_id');
        $message = $this->option('message') ?: '🚀 Тестовое сообщение от RostPack бота!';
        $parseMode = $this->option('parse-mode');

        try {
            $this->info("Отправка сообщения в чат: {$chatId}");
            $this->line("Текст: {$message}");
            $this->line("Режим парсинга: {$parseMode}");

            $result = $this->telegramService->sendMessage($chatId, $message, [
                'parse_mode' => $parseMode
            ]);

            if ($result) {
                $this->info('✅ Сообщение успешно отправлено!');
                $this->line("Message ID: {$result->getMessageId()}");
                $this->line("Дата отправки: " . date('Y-m-d H:i:s', $result->getDate()));
            } else {
                $this->error('❌ Ошибка отправки сообщения!');
                $this->info('Проверьте chat_id и токен бота');
            }

            return $result ? 0 : 1;

        } catch (\Exception $e) {
            $this->error("Ошибка отправки сообщения: " . $e->getMessage());
            Log::error('Telegram test command error: ' . $e->getMessage());
            return 1;
        }
    }
}
