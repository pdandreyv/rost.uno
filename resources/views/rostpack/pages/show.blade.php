@extends('rostpack.layouts.app')

@section('title', $page->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <article class="max-w-4xl mx-auto">
        <!-- Навигация -->
        <nav class="mb-8">
            <div class="flex items-center text-sm text-gray-500 mb-4">
                <a href="{{ route('rostpack.pages.index') }}" class="hover:text-blue-600">← Все страницы</a>
            </div>
            
            @if($page->breadcrumb && count($page->breadcrumb) > 1)
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    @foreach($page->breadcrumb as $index => $item)
                        @if($index > 0)
                            <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                        @if($index === count($page->breadcrumb) - 1)
                            <span class="text-gray-900 font-medium">{{ $item['title'] }}</span>
                        @else
                            <a href="{{ $item['url'] }}" class="hover:text-blue-600">{{ $item['title'] }}</a>
                        @endif
                    @endforeach
                </div>
            @endif
        </nav>

        <!-- Заголовок страницы -->
        <header class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $page->title }}</h1>
            
            <div class="flex items-center text-sm text-gray-500 mb-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                        <span class="text-gray-600 font-medium">
                            {{ substr($page->author->display_name, 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">{{ $page->author->display_name }}</div>
                        <div class="text-gray-500">
                            {{ $page->created_at->format('d.m.Y') }}
                        </div>
                    </div>
                </div>
                <div class="ml-auto flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    {{ $page->views_count }} просмотров
                </div>
            </div>
            
            @if($page->excerpt)
                <div class="text-lg text-gray-600 bg-gray-50 p-4 rounded-lg">
                    {{ $page->excerpt }}
                </div>
            @endif
        </header>

        <!-- Содержимое страницы -->
        <div class="prose prose-lg max-w-none">
            {!! $page->content !!}
        </div>

        <!-- Дочерние страницы -->
        @if($page->hasChildren())
            <div class="mt-12 pt-8 border-t border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Подразделы</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($page->children as $child)
                        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <a href="{{ route('rostpack.pages.show', $child) }}" 
                                   class="hover:text-blue-600 transition-colors">
                                    {{ $child->title }}
                                </a>
                            </h3>
                            @if($child->excerpt)
                                <p class="text-gray-600 mb-3">{{ $child->excerpt }}</p>
                            @endif
                            <div class="flex items-center justify-between">
                                <a href="{{ route('rostpack.pages.show', $child) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    Читать далее →
                                </a>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ $child->views_count }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Теги и мета-информация -->
        @if($page->meta_keywords)
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Теги:</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(explode(',', $page->meta_keywords) as $keyword)
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                            {{ trim($keyword) }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </article>
</div>

<style>
.prose {
    color: #374151;
    line-height: 1.75;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    color: #111827;
    font-weight: 600;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.prose h2 {
    font-size: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 0.5rem;
}

.prose h3 {
    font-size: 1.25rem;
}

.prose p {
    margin-bottom: 1.5rem;
}

.prose ul, .prose ol {
    margin-bottom: 1.5rem;
    padding-left: 1.5rem;
}

.prose li {
    margin-bottom: 0.5rem;
}

.prose code {
    background-color: #f3f4f6;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

.prose pre {
    background-color: #1f2937;
    color: #f9fafb;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin-bottom: 1.5rem;
}

.prose pre code {
    background-color: transparent;
    padding: 0;
    color: inherit;
}

.prose blockquote {
    border-left: 4px solid #3b82f6;
    padding-left: 1rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #6b7280;
}
</style>
@endsection
