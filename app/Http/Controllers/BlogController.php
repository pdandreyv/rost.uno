<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\BlogPost;
use App\Models\TelegramUser;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlogPost::published()->with('author');

        // Поиск
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        // Фильтр по автору
        if ($request->has('author') && $request->author) {
            $query->where('author_id', $request->author);
        }

        // Сортировка
        $sortBy = $request->get('sort', 'published_at');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortBy, ['title', 'published_at', 'views_count', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $posts = $query->paginate(10);

        return view('rostpack.blog.index', compact('posts'));
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        // Загружаем модель вручную для отладки
        $post = BlogPost::where('slug', $slug)->first();
        
        if (!$post) {
            abort(404, 'Статья не найдена');
        }
        
        // Увеличиваем счетчик просмотров
        $post->incrementViews();

        // Получаем похожие статьи
        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where('author_id', $post->author_id)
            ->limit(3)
            ->get();

        return view('rostpack.blog.show', compact('post', 'relatedPosts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', BlogPost::class);
        
        return view('rostpack.blog.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', BlogPost::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'is_featured' => 'boolean'
        ]);

        $data = $request->all();
        $data['slug'] = $this->generateSlug($request->title);
        $data['author_id'] = auth()->id();

        // Обработка изображения
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blog-images', 'public');
        }

        // Устанавливаем дату публикации
        if ($request->status === 'published' && !$request->published_at) {
            $data['published_at'] = now();
        }

        BlogPost::create($data);

        return redirect()->route('blog.index')
            ->with('success', 'Статья успешно создана!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogPost $post)
    {
        $this->authorize('update', $post);
        
        return view('rostpack.blog.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogPost $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'is_featured' => 'boolean'
        ]);

        $data = $request->all();
        
        // Обновляем slug только если изменился заголовок
        if ($post->title !== $request->title) {
            $data['slug'] = $this->generateSlug($request->title, $post->id);
        }

        // Обработка изображения
        if ($request->hasFile('featured_image')) {
            // Удаляем старое изображение
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('blog-images', 'public');
        }

        // Устанавливаем дату публикации
        if ($request->status === 'published' && !$post->published_at) {
            $data['published_at'] = now();
        }

        $post->update($data);

        return redirect()->route('rostpack.blog.show', $post)
            ->with('success', 'Статья успешно обновлена!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $post)
    {
        $this->authorize('delete', $post);

        // Удаляем изображение
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('blog.index')
            ->with('success', 'Статья успешно удалена!');
    }

    /**
     * Generate unique slug for the post.
     */
    private function generateSlug(string $title, $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (BlogPost::where('slug', $slug)->when($excludeId, function($query) use ($excludeId) {
            return $query->where('id', '!=', $excludeId);
        })->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
