<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\TelegramUser;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
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

        $pages = [
            [
                'title' => 'Главная страница',
                'content' => '<h1>Добро пожаловать в RostPack!</h1>
                <p>RostPack - это мощный пакет для Laravel, который предоставляет множество полезных функций для разработки веб-приложений.</p>
                
                <h2>Наши возможности:</h2>
                <ul>
                    <li>Telegram Bot API интеграция</li>
                    <li>Система блога с CRUD операциями</li>
                    <li>Система страниц с иерархической структурой</li>
                    <li>Личный кабинет с авторизацией через Telegram</li>
                    <li>SEO настройки</li>
                    <li>Аналитика и статистика</li>
                </ul>
                
                <p>Начните использовать RostPack уже сегодня!</p>',
                'excerpt' => 'Главная страница RostPack - мощного пакета для Laravel разработки.',
                'status' => 'published',
                'is_homepage' => true,
                'show_in_menu' => true,
                'menu_title' => 'Главная',
                'sort_order' => 1,
                'meta_title' => 'RostPack - Главная страница',
                'meta_description' => 'Главная страница RostPack - пакета для Laravel с Telegram интеграцией, блогом и системой страниц.',
                'meta_keywords' => 'Laravel, пакет, Telegram, блог, CMS, главная',
                'views_count' => 234,
            ],
            [
                'title' => 'О нас',
                'content' => '<h1>О компании RostPack</h1>
                <p>Мы создаем качественные решения для разработчиков Laravel.</p>
                
                <h2>Наша миссия</h2>
                <p>Предоставить разработчикам мощные и удобные инструменты для создания современных веб-приложений.</p>
                
                <h2>Наша команда</h2>
                <p>Мы - команда опытных разработчиков, которые понимают потребности современной веб-разработки.</p>
                
                <h2>Наши принципы</h2>
                <ul>
                    <li>Качество кода</li>
                    <li>Простота использования</li>
                    <li>Хорошая документация</li>
                    <li>Поддержка сообщества</li>
                </ul>',
                'excerpt' => 'Информация о компании RostPack и нашей миссии.',
                'status' => 'published',
                'is_homepage' => false,
                'show_in_menu' => true,
                'menu_title' => 'О нас',
                'sort_order' => 2,
                'meta_title' => 'О нас - RostPack',
                'meta_description' => 'Информация о компании RostPack, нашей миссии и принципах работы.',
                'meta_keywords' => 'о нас, компания, миссия, команда',
                'views_count' => 89,
            ],
            [
                'title' => 'Услуги',
                'content' => '<h1>Наши услуги</h1>
                <p>Мы предоставляем широкий спектр услуг для разработчиков Laravel.</p>
                
                <h2>Разработка пакетов</h2>
                <p>Создание кастомных пакетов для Laravel под ваши потребности.</p>
                
                <h2>Консультации</h2>
                <p>Консультации по архитектуре и лучшим практикам Laravel.</p>
                
                <h2>Техническая поддержка</h2>
                <p>Поддержка и сопровождение ваших проектов.</p>
                
                <h2>Обучение</h2>
                <p>Обучение команды работе с Laravel и нашими пакетами.</p>',
                'excerpt' => 'Список услуг, которые мы предоставляем для разработчиков Laravel.',
                'status' => 'published',
                'is_homepage' => false,
                'show_in_menu' => true,
                'menu_title' => 'Услуги',
                'sort_order' => 3,
                'meta_title' => 'Услуги - RostPack',
                'meta_description' => 'Услуги RostPack: разработка пакетов, консультации, поддержка и обучение для Laravel.',
                'meta_keywords' => 'услуги, разработка, консультации, поддержка, обучение',
                'views_count' => 67,
            ],
            [
                'title' => 'Контакты',
                'content' => '<h1>Свяжитесь с нами</h1>
                <p>Мы всегда рады новым проектам и сотрудничеству!</p>
                
                <h2>Контактная информация</h2>
                <ul>
                    <li><strong>Email:</strong> info@rostpack.com</li>
                    <li><strong>Telegram:</strong> @rostpack_support</li>
                    <li><strong>GitHub:</strong> github.com/rostpack</li>
                </ul>
                
                <h2>Время работы</h2>
                <p>Понедельник - Пятница: 9:00 - 18:00 (МСК)</p>
                
                <h2>Форма обратной связи</h2>
                <p>Используйте форму ниже для отправки сообщения:</p>
                <p><em>Форма обратной связи будет добавлена в следующих версиях.</em></p>',
                'excerpt' => 'Контактная информация и способы связи с нами.',
                'status' => 'published',
                'is_homepage' => false,
                'show_in_menu' => true,
                'menu_title' => 'Контакты',
                'sort_order' => 4,
                'meta_title' => 'Контакты - RostPack',
                'meta_description' => 'Контактная информация RostPack. Свяжитесь с нами для обсуждения вашего проекта.',
                'meta_keywords' => 'контакты, связь, email, telegram, поддержка',
                'views_count' => 45,
            ],
            [
                'title' => 'Документация',
                'content' => '<h1>Документация RostPack</h1>
                <p>Подробная документация по использованию всех возможностей пакета.</p>
                
                <h2>Быстрый старт</h2>
                <p>Начните работу с RostPack за несколько минут.</p>
                
                <h2>API Reference</h2>
                <p>Полное описание всех классов и методов пакета.</p>
                
                <h2>Примеры использования</h2>
                <p>Практические примеры для различных сценариев.</p>
                
                <h2>FAQ</h2>
                <p>Ответы на часто задаваемые вопросы.</p>',
                'excerpt' => 'Документация по использованию пакета RostPack.',
                'status' => 'published',
                'is_homepage' => false,
                'show_in_menu' => true,
                'menu_title' => 'Документация',
                'sort_order' => 5,
                'meta_title' => 'Документация - RostPack',
                'meta_description' => 'Документация по использованию пакета RostPack. API, примеры, FAQ.',
                'meta_keywords' => 'документация, API, примеры, FAQ, руководство',
                'views_count' => 123,
            ],
            [
                'title' => 'Политика конфиденциальности',
                'content' => '<h1>Политика конфиденциальности</h1>
                <p>Настоящая Политика конфиденциальности описывает, как мы собираем, используем и защищаем вашу информацию.</p>
                
                <h2>Сбор информации</h2>
                <p>Мы собираем информацию, которую вы предоставляете нам напрямую, например, при регистрации или использовании наших услуг.</p>
                
                <h2>Использование информации</h2>
                <p>Мы используем собранную информацию для предоставления, поддержки и улучшения наших услуг.</p>
                
                <h2>Защита информации</h2>
                <p>Мы принимаем разумные меры для защиты вашей информации от несанкционированного доступа.</p>
                
                <h2>Согласие</h2>
                <p>Используя наши услуги, вы соглашаетесь с настоящей Политикой конфиденциальности.</p>',
                'excerpt' => 'Политика конфиденциальности RostPack.',
                'status' => 'published',
                'is_homepage' => false,
                'show_in_menu' => false,
                'menu_title' => 'Политика конфиденциальности',
                'sort_order' => 6,
                'meta_title' => 'Политика конфиденциальности - RostPack',
                'meta_description' => 'Политика конфиденциальности RostPack. Как мы собираем и защищаем вашу информацию.',
                'meta_keywords' => 'политика, конфиденциальность, защита данных',
                'views_count' => 12,
            ],
        ];

        foreach ($pages as $pageData) {
            $pageData['slug'] = Str::slug($pageData['title']);
            $pageData['author_id'] = $user->id;
            
            Page::create($pageData);
        }
    }
}
