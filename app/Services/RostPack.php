<?php

/**
 * RostPack Main Class
 * 
 * ВАЖНО: ВСЕ стили и JavaScript должны быть в отдельных файлах!
 * - Стили → resources/css/rostpack.css
 * - JavaScript → resources/js/rostpack.js
 * - В Blade-шаблонах только конфигурационный код (window.rostpackConfig)
 * НЕ добавлять inline стили или скрипты в HTML/Blade файлы!
 */

namespace App\Services;

class RostPack
{
    /**
     * Конфигурация пакета
     */
    protected array $config;

    /**
     * Создать новый экземпляр пакета
     */
    public function __construct()
    {
        $this->config = config('rostpack', []);
    }

    /**
     * Получить конфигурацию
     */
    public function getConfig(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->config;
        }

        return data_get($this->config, $key, $default);
    }

    /**
     * Установить конфигурацию
     */
    public function setConfig(string $key, $value): self
    {
        data_set($this->config, $key, $value);
        
        return $this;
    }

    /**
     * Пример метода пакета
     */
    public function hello(string $name = 'World'): string
    {
        return "Привет, {$name}! Это пакет Laravel от RostPack.";
    }

    /**
     * Получить версию пакета
     */
    public function getVersion(): string
    {
        return '1.0.0';
    }

    /**
     * Отобразить главную страницу
     */
    public function index()
    {
        // Получаем последние опубликованные статьи
        $latestPosts = \App\Models\BlogPost::published()
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('rostpack.index', [
            'version' => $this->getVersion(),
            'message' => $this->hello(),
            'latestPosts' => $latestPosts,
        ]);
    }
}
