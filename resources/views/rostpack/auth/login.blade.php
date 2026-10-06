@extends('rostpack.layouts.app')

@section('title', 'Авторизация - RostPack')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                Авторизация в RostPack
            </h2>
            <p class="text-sm text-gray-600">
                Войдите через Telegram для доступа к системе
            </p>
            @if(session('error'))
                <p class="mt-3 text-sm text-red-600">{{ session('error') }}</p>
            @endif
            @if(session('success'))
                <p class="mt-3 text-sm text-green-600">{{ session('success') }}</p>
            @endif
        </div>
        
        <div class="mt-6">
            <div class="js-webapp-auth-status text-center" style="display: none;">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-3 text-gray-600">Автоматический вход через Telegram...</p>
            </div>

            <div class="text-center telegram-login-widget">
                @if(config('rostpack.telegram.bot_username'))
                    <script async src="https://telegram.org/js/telegram-widget.js?22"
                            data-telegram-login="{{ ltrim(config('rostpack.telegram.bot_username'), '@') }}"
                            data-size="large"
                            data-auth-url="{{ url()->route('rostpack.auth.telegram.callback') }}"
                            data-request-access="write"></script>
                    <p class="mt-3 text-xs text-gray-500">
                        Нажмите кнопку ниже, чтобы войти через Telegram.
                    </p>
                @else
                    <p class="text-sm text-red-600">Не задан TELEGRAM_BOT_USERNAME в .env</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
