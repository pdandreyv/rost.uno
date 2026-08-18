<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use RostPack\RostPack\Models\BlogPost;
use RostPack\RostPack\Models\Page;
use RostPack\RostPack\Models\TelegramUser;
use Illuminate\Support\Str;

class RostPackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Заполнение базы данных тестовыми данными RostPack...');

        // Создаем тестового пользователя
        $this->command->info('👤 Создание тестового пользователя...');
        $user = $this->createTestUser();

        // Создаем статьи блога
        $this->command->info('📝 Создание статей блога...');
        $this->createBlogPosts($user);

        // Создаем страницы
        $this->command->info('📄 Создание страниц...');
        $this->createPages($user);

        $this->command->info('✅ База данных успешно заполнена!');
        
        $this->command->line('');
        $this->command->line('📊 Статистика:');
        
        $blogCount = BlogPost::count();
        $pagesCount = Page::count();
        $usersCount = TelegramUser::count();
        
        $this->command->line("   📝 Статей: {$blogCount}");
        $this->command->line("   📄 Страниц: {$pagesCount}");
        $this->command->line("   👥 Пользователей: {$usersCount}");
        
        $this->command->line('');
        $this->command->line('🚀 Теперь вы можете:');
        $this->command->line('   • Посетить главную страницу: /rostpack');
        $this->command->line('   • Просмотреть блог: /rostpack/blog');
        $this->command->line('   • Просмотреть страницы: /rostpack/pages');
        $this->command->line('   • Авторизация через Telegram виджет на главной странице');
    }

    /**
     * Создать тестового пользователя
     */
    private function createTestUser()
    {
        return TelegramUser::firstOrCreate(
            ['telegram_id' => 123456789],
            [
                'first_name' => 'Тестовый',
                'last_name' => 'Пользователь',
                'telegram_username' => 'test_user',
                'city' => 'Москва',
                'verified' => true,
            ]
        );
    }

    /**
     * Создать статьи блога
     */
    private function createBlogPosts($user)
    {
        $posts = [
            [
                'title' => 'Добро пожаловать в RostPack!',
                'excerpt' => 'Знакомство с возможностями нашего пакета для Laravel разработки.',
                'content' => '<h2>Что такое RostPack?</h2><p>RostPack - это мощный пакет для Laravel, который предоставляет множество полезных функций для разработки веб-приложений.</p>',
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
                'content' => '<h2>Создание Telegram бота</h2><p>Для начала работы с Telegram ботом необходимо создать его через @BotFather.</p>',
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
                'content' => '<h2>Управление статьями</h2><p>RostPack предоставляет полноценную систему управления статьями блога.</p>',
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
                'content' => '<h2>Создание страниц</h2><p>RostPack включает мощную систему управления страницами с иерархической структурой.</p>',
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
                'content' => '<h2>Личный кабинет</h2><p>RostPack предоставляет полноценный личный кабинет для управления контентом.</p>',
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

    /**
     * Создать страницы
     */
    private function createPages($user)
    {
        $pages = [
            [
                'title' => 'Главная страница',
                'content' => '<h1>Добро пожаловать в RostPack!</h1><p>RostPack - это мощный пакет для Laravel, который предоставляет множество полезных функций для разработки веб-приложений.</p>',
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
                'content' => '<h1>О компании RostPack</h1><p>Мы создаем качественные решения для разработчиков Laravel.</p>',
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
                'content' => '<h1>Наши услуги</h1><p>Мы предоставляем широкий спектр услуг для разработчиков Laravel.</p>',
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
                'content' => '<h1>Свяжитесь с нами</h1><p>Мы всегда рады новым проектам и сотрудничеству!</p>',
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
                'content' => '<h1>Документация RostPack</h1><p>Подробная документация по использованию всех возможностей пакета.</p>',
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
                'content' => '<h1>Политика конфиденциальности</h1><p>Настоящая Политика конфиденциальности описывает, как мы собираем, используем и защищаем вашу информацию.</p>',
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