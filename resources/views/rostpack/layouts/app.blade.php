<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'RostPack')</title>
    
    <!-- Favicon из монеты RostPack -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/rostpack/moneta.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/rostpack/moneta.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/rostpack/moneta.png') }}">
    <meta name="msapplication-TileImage" content="{{ asset('images/rostpack/moneta.png') }}">
    <meta name="theme-color" content="#ffb43d">

    <!-- Telegram Mini App SDK: в head, до остальных скриптов -->
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <script>
        (function () {
            var tg = window.Telegram && window.Telegram.WebApp;
            if (!tg) return;
            if (typeof tg.ready === 'function') tg.ready();
            if (typeof tg.expand === 'function') tg.expand();
            if (tg.initData) {
                try { sessionStorage.setItem('tgWebAppInitData', tg.initData); } catch (e) {}
            }
        })();
    </script>
    
    <!-- Styles -->
    @rostpackCss
</head>
<body>
  <div class="main">
    <div class="header">
      <div class="profile">
        @auth
            <div class="profile-container">
                @if(Auth::user()->photo_url)
                    <div class="avatar" onclick="window.RostPack.toggleProfileMenu()"><img src="{{ Auth::user()->photo_url }}" alt="{{ Auth::user()->display_name }}"></div>
                @else
                    <div class="avatar" onclick="window.RostPack.toggleProfileMenu()">👤</div>
                @endif
                <div class="display_name">
                    {{ Auth::user()->display_name }}
                </div>
                
                <!-- Выпадающее меню -->
                <div class="profile-menu" id="profile-menu">
                    <a href="{{ route('rostpack.profile.index') }}" class="menu-item">
                        <span class="menu-icon">👤</span>
                        Профиль
                    </a>
                    @if(!$isTelegramWebApp)
                        <form method="POST" action="{{ route('rostpack.auth.logout') }}" class="menu-item-form js-browser-only-logout">
                            @csrf
                            <button type="submit" class="menu-item logout">
                                <span class="menu-icon">🚪</span>
                                Выйти
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @else
            @if(config('rostpack.telegram.bot_username'))
                <div class="telegram-login-widget">
                    <script async src="https://telegram.org/js/telegram-widget.js?22"
                            data-telegram-login="{{ ltrim(config('rostpack.telegram.bot_username'), '@') }}"
                            data-size="medium"
                            data-auth-url="{{ url()->route('rostpack.auth.telegram.callback') }}"
                            data-request-access="write"></script>
                </div>
            @else
                <button onclick="alert('Для авторизации через Telegram необходимо настроить bot_username в конфигурации пакета.')" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out">
                    Войти через Telegram
                </button>
            @endif
        @endauth
      </div>
      <div class="coins"><img src="{{ asset('images/rostpack/moneta.png') }}" alt="moneta"> <span>200</span></div>
    </div>

    <!-- Main Content -->
    <main class="px-4 pb-20">
        @yield('content')
    </main>

    <div class="footer">
      <button onclick="location.href='{{ route('rostpack.index') }}'">🏠<br>Дом</button>
      <button onclick="location.href='{{ route('rostpack.mine') }}'">⛏<br>Добыть</button>
      <button onclick="location.href='{{ route('rostpack.friends') }}'">👥<br>Друзья</button>
      <button onclick="location.href='{{ route('rostpack.settings') }}'">⚙<br>Настрой</button>
    </div>
  </div>

    <!-- Scripts -->
    <script>
        window.rostpackConfig = {
            authUrl: '{{ route("rostpack.auth.telegram.handle") }}',
            webappAuthUrl: '{{ route("rostpack.auth.webapp.handle") }}',
            indexUrl: '{{ route("home") }}',
            botUsername: '{{ config("rostpack.telegram.bot_username") }}',
            isTelegramWebApp: {{ !empty($isTelegramWebApp) ? 'true' : 'false' }},
            isDevelopmentMode: {{ !empty($isDevelopmentMode) ? 'true' : 'false' }},
            isAuthenticated: {{ Auth::check() ? 'true' : 'false' }}
        };
    </script>
    @rostpackJs
</body>
</html>