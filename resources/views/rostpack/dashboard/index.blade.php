@extends('rostpack.layouts.app')

@section('title', 'Личный кабинет')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Личный кабинет</h1>
        <p class="text-gray-600">Добро пожаловать, {{ Auth::user()->display_name }}!</p>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Всего статей</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['blog_posts'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Опубликовано</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['published_posts'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Страниц</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pages'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Просмотров</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_views']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Быстрые действия -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Быстрые действия</h3>
            <div class="space-y-3">
                <a href="{{ route('rostpack.blog.create') }}" 
                   class="block w-full text-left px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    ✏️ Создать новую статью
                </a>
                <a href="{{ route('rostpack.pages.create') }}" 
                   class="block w-full text-left px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    📄 Создать новую страницу
                </a>
                <a href="{{ route('rostpack.profile.index') }}" 
                   class="block w-full text-left px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    👤 Редактировать профиль
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Меню</h3>
            <div class="space-y-2">
                <a href="{{ route('rostpack.blog.index') }}" 
                   class="block text-blue-600 hover:text-blue-800 py-2">
                    📝 Блог ({{ $stats['blog_posts'] }})
                </a>
                <a href="{{ route('rostpack.pages.index') }}" 
                   class="block text-blue-600 hover:text-blue-800 py-2">
                    📄 Страницы ({{ $stats['pages'] }})
                </a>
                <a href="{{ route('rostpack.index') }}" 
                   class="block text-blue-600 hover:text-blue-800 py-2">
                    🏠 Главная
                </a>
            </div>
        </div>
    </div>

    <!-- Последние статьи -->
    @if($recentPosts->count() > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Последние статьи</h3>
                <a href="{{ route('rostpack.blog.index') }}" 
                   class="text-blue-600 hover:text-blue-800 text-sm">
                    Все статьи →
                </a>
            </div>
            <div class="space-y-3">
                @foreach($recentPosts as $post)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                        <div>
                            <a href="{{ route('rostpack.blog.show', $post) }}" 
                               class="text-gray-900 hover:text-blue-600 font-medium">
                                {{ $post->title }}
                            </a>
                            <div class="text-sm text-gray-500">
                                {{ $post->status === 'published' ? 'Опубликовано' : 'Черновик' }} • 
                                {{ $post->updated_at->format('d.m.Y') }}
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            {{ $post->views_count }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Последние страницы -->
    @if($recentPages->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Последние страницы</h3>
                <a href="{{ route('rostpack.pages.index') }}" 
                   class="text-blue-600 hover:text-blue-800 text-sm">
                    Все страницы →
                </a>
            </div>
            <div class="space-y-3">
                @foreach($recentPages as $page)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                        <div>
                            <a href="{{ route('rostpack.pages.show', $page) }}" 
                               class="text-gray-900 hover:text-blue-600 font-medium">
                                {{ $page->title }}
                            </a>
                            <div class="text-sm text-gray-500">
                                {{ $page->status === 'published' ? 'Опубликовано' : 'Черновик' }} • 
                                {{ $page->updated_at->format('d.m.Y') }}
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            {{ $page->views_count }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
