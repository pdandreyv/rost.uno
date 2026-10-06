@extends('rostpack.layouts.app')

@section('title', 'Страницы')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Страницы</h1>
        <p class="text-lg text-gray-600">Информационные страницы сайта</p>
    </div>

    <!-- Поиск и фильтры -->
    <div class="mb-8 bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('rostpack.pages.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Поиск по страницам..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="min-w-48">
                <select name="author" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Все авторы</option>
                    @foreach($pages->pluck('author')->unique('id') as $author)
                        <option value="{{ $author->id }}" {{ request('author') == $author->id ? 'selected' : '' }}>
                            {{ $author->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-32">
                <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="sort_order" {{ request('sort') == 'sort_order' ? 'selected' : '' }}>По порядку</option>
                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>По названию</option>
                    <option value="views_count" {{ request('sort') == 'views_count' ? 'selected' : '' }}>По просмотрам</option>
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>По дате</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Найти
            </button>
        </form>
    </div>

    <!-- Список страниц -->
    @if($pages->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pages as $page)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center text-sm text-gray-500">
                                <span>{{ $page->author->display_name }}</span>
                                <span class="mx-2">•</span>
                                <time datetime="{{ $page->created_at->format('Y-m-d') }}">
                                    {{ $page->created_at->format('d.m.Y') }}
                                </time>
                            </div>
                            @if($page->is_homepage)
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                    Главная
                                </span>
                            @endif
                        </div>
                        
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">
                            <a href="{{ route('rostpack.pages.show', $page) }}" 
                               class="hover:text-blue-600 transition-colors">
                                {{ $page->title }}
                            </a>
                        </h2>
                        
                        <p class="text-gray-600 mb-4">{{ $page->excerpt }}</p>
                        
                        <div class="flex items-center justify-between">
                            <a href="{{ route('rostpack.pages.show', $page) }}" 
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                Читать далее →
                            </a>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                {{ $page->views_count }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Пагинация -->
        <div class="mt-8">
            {{ $pages->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-500 text-lg mb-4">Страницы не найдены</div>
            <p class="text-gray-400">Попробуйте изменить параметры поиска</p>
        </div>
    @endif
</div>
@endsection
