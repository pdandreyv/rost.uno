<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'RostBot') }} - Умный помощник</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="welcome-container">
        <div class="bot-icon">🤖</div>
        
        <h1 class="welcome-title">RostBot</h1>
        <p class="welcome-subtitle">Ваш умный помощник для решения задач и ответов на вопросы</p>
        
        <div class="status-indicator">
            <div class="status-dot"></div>
            Система активна и готова к работе
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">💬</div>
                <div class="feature-title">Умные диалоги</div>
                <div class="feature-desc">Ведите естественные беседы с ботом на любые темы</div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <div class="feature-title">Быстрые ответы</div>
                <div class="feature-desc">Мгновенная обработка запросов и предоставление решений</div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <div class="feature-title">Точные решения</div>
                <div class="feature-desc">Получайте точные и релевантные ответы на ваши вопросы</div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <div class="feature-title">Безопасность</div>
                <div class="feature-desc">Ваши данные защищены современными методами шифрования</div>
            </div>
        </div>
        
        <div class="cta-section">
            <a href="/rostpack" class="cta-button primary">Начать работу с ботом</a>
            <a href="/rostpack/info" class="cta-button secondary">Узнать больше</a>
        </div>
        
        <div class="welcome-footer">
            <p>Создано с ❤️ командой RostPack</p>
            <p>Версия 1.0.0 | Laravel {{ app()->version() }}</p>
        </div>
    </div>
</body>
</html>
