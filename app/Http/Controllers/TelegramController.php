<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\TelegramService;
use Telegram\Bot\Objects\Update;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    /**
     * Telegram сервис
     */
    protected TelegramService $telegramService;

    /**
     * Создать новый экземпляр контроллера
     */
    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Обработать webhook от Telegram
     */
    public function webhook(Request $request): Response
    {
        try {
            // Получаем данные от Telegram
            $input = $request->all();
            
            // Логируем входящий запрос для отладки
            Log::info('Telegram webhook received', $input);

            // Создаем объект Update из входящих данных
            $update = new Update($input);

            // Обрабатываем обновление
            $this->telegramService->handleUpdate($update);

            // Возвращаем успешный ответ
            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Ошибка обработки Telegram webhook: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response('Error', 500);
        }
    }

    /**
     * Получить информацию о боте
     */
    public function botInfo(): array
    {
        try {
            $info = $this->telegramService->getBotInfo();
            
            if (!$info) {
                return [
                    'success' => false,
                    'message' => 'Не удалось получить информацию о боте'
                ];
            }

            return [
                'success' => true,
                'data' => $info
            ];

        } catch (\Exception $e) {
            Log::error('Ошибка получения информации о боте: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Ошибка получения информации о боте'
            ];
        }
    }

    /**
     * Установить webhook
     */
    public function setWebhook(Request $request): array
    {
        try {
            $url = $request->input('url');
            
            if (!$url) {
                return [
                    'success' => false,
                    'message' => 'URL для webhook не указан'
                ];
            }

            $result = $this->telegramService->setWebhook($url);

            return [
                'success' => $result,
                'message' => $result ? 'Webhook успешно установлен' : 'Ошибка установки webhook'
            ];

        } catch (\Exception $e) {
            Log::error('Ошибка установки webhook: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Ошибка установки webhook'
            ];
        }
    }

    /**
     * Удалить webhook
     */
    public function removeWebhook(): array
    {
        try {
            $result = $this->telegramService->removeWebhook();

            return [
                'success' => $result,
                'message' => $result ? 'Webhook успешно удален' : 'Ошибка удаления webhook'
            ];

        } catch (\Exception $e) {
            Log::error('Ошибка удаления webhook: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Ошибка удаления webhook'
            ];
        }
    }

    /**
     * Отправить тестовое сообщение
     */
    public function sendTestMessage(Request $request): array
    {
        try {
            $chatId = $request->input('chat_id');
            $message = $request->input('message', 'Тестовое сообщение от RostPack бота! 🚀');

            if (!$chatId) {
                return [
                    'success' => false,
                    'message' => 'Chat ID не указан'
                ];
            }

            $result = $this->telegramService->sendMessage($chatId, $message);

            return [
                'success' => $result !== null,
                'message' => $result ? 'Сообщение успешно отправлено' : 'Ошибка отправки сообщения'
            ];

        } catch (\Exception $e) {
            Log::error('Ошибка отправки тестового сообщения: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Ошибка отправки сообщения'
            ];
        }
    }
}
