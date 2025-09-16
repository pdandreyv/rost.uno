<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'RostPack') }} - Платформа для Telegram ботов</title>
    <meta name="description" content="RostPack - современная платформа для создания и управления Telegram ботами с системой блогов и страниц">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>
<body>
    <div class="welcome-container">
        <div class="bot-icon">🤖</div>
        
        <h1 class="welcome-title">RostPack</h1>
        <p class="welcome-subtitle">Современная платформа для создания и управления Telegram ботами с системой блогов и страниц</p>
        
        <div class="status-indicator">
            <div class="status-dot"></div>
            Платформа активна и готова к работе
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🤖</div>
                <div class="feature-title">Telegram боты</div>
                <div class="feature-desc">Создавайте и управляйте Telegram ботами с удобным интерфейсом</div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📝</div>
                <div class="feature-title">Система блогов</div>
                <div class="feature-desc">Публикуйте статьи и управляйте контентом через веб-интерфейс</div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📄</div>
                <div class="feature-title">Управление страницами</div>
                <div class="feature-desc">Создавайте статические страницы для вашего сайта</div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🔐</div>
                <div class="feature-title">Telegram авторизация</div>
                <div class="feature-desc">Безопасная авторизация через Telegram без паролей</div>
            </div>
        </div>
        
        <div class="cta-section">
            <a href="/rostpack" class="cta-button primary">Перейти к платформе</a>
            <a href="/rostpack/info" class="cta-button secondary">API документация</a>
        </div>
        
        <div class="welcome-footer">
            <p>Создано с ❤️ командой RostPack</p>
            <p>Версия 1.0.0 | Laravel {{ app()->version() }} | PHP {{ PHP_VERSION }}</p>
        </div>
    </div>

    <script>
        // Добавляем интерактивность
        document.addEventListener('DOMContentLoaded', function() {
            // Анимация появления карточек
            const cards = document.querySelectorAll('.feature-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('fade-in');
            });

            // Эффект при наведении на кнопки
            const buttons = document.querySelectorAll('.cta-button');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px) scale(1.02)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Плавная прокрутка для кнопок
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (this.getAttribute('href').startsWith('#')) {
                        e.preventDefault();
                        const target = document.querySelector(this.getAttribute('href'));
                        if (target) {
                            target.scrollIntoView({ behavior: 'smooth' });
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
