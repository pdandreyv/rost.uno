<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogPost;
use App\Models\TelegramUser;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем тестового пользователя, если его нет
        $user = TelegramUser::firstOrCreate(
            ['telegram_id' => 123456789],
            [
                'first_name' => 'Тестовый',
                'last_name' => 'Пользователь',
                'telegram_username' => 'test_user',
                'city' => 'Москва',
                'verified' => true,
            ]
        );

        $posts = [
            [
                'title' => 'Добро пожаловать в RostPack!',
                'excerpt' => 'Знакомство с возможностями нашего пакета для Laravel разработки.',
                'content' => '<h2>Что такое RostPack?</h2>
                <p>RostPack - это мощный пакет для Laravel, который предоставляет множество полезных функций для разработки веб-приложений.</p>
                
                <h3>Основные возможности:</h3>
                <ul>
                    <li>Telegram Bot API интеграция</li>
                    <li>Система блога с CRUD операциями</li>
                    <li>Система страниц с иерархической структурой</li>
                    <li>Личный кабинет с авторизацией через Telegram</li>
                    <li>SEO настройки</li>
                    <li>Аналитика и статистика</li>
                </ul>
                
                <p>Пакет легко устанавливается и настраивается, предоставляя готовые решения для многих задач.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'is_featured' => true,
                'meta_title' => 'RostPack - Мощный пакет для Laravel',
                'meta_description' => 'Знакомство с возможностями RostPack - пакета для Laravel с Telegram интеграцией, блогом и системой страниц.',
                'meta_keywords' => 'Laravel, пакет, Telegram, блог, CMS',
                'views_count' => 156,
            ],
            [
                'title' => 'Настройка Telegram бота',
                'excerpt' => 'Пошаговое руководство по настройке Telegram бота для вашего Laravel приложения.',
                'content' => '<h2>Создание Telegram бота</h2>
                <p>Для начала работы с Telegram ботом необходимо создать его через @BotFather.</p>
                
                <h3>Шаги настройки:</h3>
                <ol>
                    <li>Найдите @BotFather в Telegram</li>
                    <li>Отправьте команду /newbot</li>
                    <li>Введите название бота</li>
                    <li>Введите username бота</li>
                    <li>Скопируйте полученный токен</li>
                </ol>
                
                <h3>Настройка в Laravel:</h3>
                <pre><code>TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_WEBHOOK_URL=https://yourdomain.com/rostpack/telegram/webhook</code></pre>
                
                <p>После настройки токена выполните команду для установки webhook:</p>
                <pre><code>php artisan rostpack:telegram-webhook set</code></pre>',
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'is_featured' => true,
                'meta_title' => 'Настройка Telegram бота в Laravel',
                'meta_description' => 'Подробное руководство по настройке Telegram бота для Laravel приложения с использованием RostPack.',
                'meta_keywords' => 'Telegram, бот, Laravel, настройка, webhook',
                'views_count' => 89,
            ],
            [
                'title' => 'Создание статей в блоге',
                'excerpt' => 'Узнайте, как создавать и управлять статьями в блоге с помощью RostPack.',
                'content' => '<h2>Управление статьями</h2>
                <p>RostPack предоставляет полноценную систему управления статьями блога.</p>
                
                <h3>Возможности:</h3>
                <ul>
                    <li>Создание, редактирование и удаление статей</li>
                    <li>Система статусов (черновик, опубликовано, архив)</li>
                    <li>Загрузка изображений</li>
                    <li>SEO настройки</li>
                    <li>Счетчик просмотров</li>
                    <li>Поиск и фильтрация</li>
                </ul>
                
                <h3>Создание статьи:</h3>
                <p>Для создания новой статьи перейдите в личный кабинет и нажмите "Создать новую статью".</p>
                
                <h3>Поля статьи:</h3>
                <ul>
                    <li><strong>Заголовок</strong> - основной заголовок статьи</li>
                    <li><strong>Краткое описание</strong> - краткое содержание для превью</li>
                    <li><strong>Содержимое</strong> - полный текст статьи</li>
                    <li><strong>Изображение</strong> - главное изображение статьи</li>
                    <li><strong>Статус</strong> - черновик или опубликовано</li>
                </ul>',
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'is_featured' => false,
                'meta_title' => 'Создание статей в блоге RostPack',
                'meta_description' => 'Руководство по созданию и управлению статьями в блоге с помощью системы RostPack.',
                'meta_keywords' => 'блог, статьи, CMS, управление контентом',
                'views_count' => 45,
            ],
            [
                'title' => 'Система страниц и меню',
                'excerpt' => 'Как создавать страницы и настраивать меню в вашем приложении.',
                'content' => '<h2>Создание страниц</h2>
                <p>RostPack включает мощную систему управления страницами с иерархической структурой.</p>
                
                <h3>Типы страниц:</h3>
                <ul>
                    <li><strong>Обычные страницы</strong> - стандартные страницы сайта</li>
                    <li><strong>Главная страница</strong> - можно назначить одну страницу как главную</li>
                    <li><strong>Родительские страницы</strong> - страницы с дочерними элементами</li>
                </ul>
                
                <h3>Настройка меню:</h3>
                <p>Каждая страница может быть настроена для отображения в меню:</p>
                <ul>
                    <li>Показывать/скрывать в меню</li>
                    <li>Настроить название в меню</li>
                    <li>Установить порядок сортировки</li>
                </ul>
                
                <h3>Шаблоны страниц:</h3>
                <p>Система поддерживает различные шаблоны для отображения страниц, что позволяет создавать уникальный дизайн для разных типов контента.</p>',
                'status' => 'published',
                'published_at' => now()->subHours(12),
                'is_featured' => false,
                'meta_title' => 'Система страниц и меню в RostPack',
                'meta_description' => 'Руководство по созданию страниц и настройке меню с помощью системы RostPack.',
                'meta_keywords' => 'страницы, меню, CMS, иерархия, шаблоны',
                'views_count' => 23,
            ],
            [
                'title' => 'Личный кабинет и аналитика',
                'excerpt' => 'Обзор возможностей личного кабинета и системы аналитики в RostPack.',
                'content' => '<h2>Личный кабинет</h2>
                <p>RostPack предоставляет полноценный личный кабинет для управления контентом.</p>
                
                <h3>Возможности личного кабинета:</h3>
                <ul>
                    <li>Управление профилем</li>
                    <li>Создание и редактирование статей</li>
                    <li>Создание и редактирование страниц</li>
                    <li>Просмотр аналитики</li>
                    <li>Статистика просмотров</li>
                </ul>
                
                <h3>Аналитика:</h3>
                <p>Система предоставляет детальную аналитику по вашему контенту:</p>
                <ul>
                    <li>Общее количество статей и страниц</li>
                    <li>Количество просмотров</li>
                    <li>Статистика по месяцам</li>
                    <li>Топ контента по просмотрам</li>
                </ul>
                
                <h3>Авторизация через Telegram:</h3>
                <p>Для входа в личный кабинет используется авторизация через Telegram, что обеспечивает безопасность и удобство.</p>',
                'status' => 'draft',
                'published_at' => null,
                'is_featured' => false,
                'meta_title' => 'Личный кабинет и аналитика RostPack',
                'meta_description' => 'Обзор возможностей личного кабинета и системы аналитики в пакете RostPack.',
                'meta_keywords' => 'личный кабинет, аналитика, статистика, Telegram',
                'views_count' => 0,
            ],
        ];

        foreach ($posts as $postData) {
            $postData['slug'] = Str::slug($postData['title']);
            $postData['author_id'] = $user->id;
            
            BlogPost::create($postData);
        }
    }
}
