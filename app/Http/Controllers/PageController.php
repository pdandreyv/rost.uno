<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Page;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Page::published()->with('author');

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
        $sortBy = $request->get('sort', 'sort_order');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortBy, ['title', 'sort_order', 'views_count', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $pages = $query->paginate(20);

        return view('rostpack.pages.index', compact('pages'));
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        // Загружаем модель по slug
        $page = Page::where('slug', $slug)->first();
        
        if (!$page) {
            abort(404, 'Страница не найдена');
        }
        
        // Увеличиваем счетчик просмотров
        $page->incrementViews();

        return view('rostpack.pages.show', compact('page'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Page::class);
        
        $parentPages = Page::published()->root()->get();
        
        return view('rostpack.pages.create', compact('parentPages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Page::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'template' => 'nullable|string|max:100',
            'status' => 'required|in:draft,published',
            'is_homepage' => 'boolean',
            'parent_id' => 'nullable|exists:pages,id',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'show_in_menu' => 'boolean',
            'menu_title' => 'nullable|string|max:255'
        ]);

        $data = $request->all();
        $data['slug'] = $this->generateSlug($request->title);
        $data['author_id'] = auth()->id();

        // Если это главная страница, снимаем флаг с других страниц
        if ($request->is_homepage) {
            Page::where('is_homepage', true)->update(['is_homepage' => false]);
        }

        Page::create($data);

        return redirect()->route('pages.index')
            ->with('success', 'Страница успешно создана!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        $this->authorize('update', $page);
        
        $parentPages = Page::published()
            ->where('id', '!=', $page->id)
            ->root()
            ->get();
        
        return view('rostpack.pages.edit', compact('page', 'parentPages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page)
    {
        $this->authorize('update', $page);

        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'template' => 'nullable|string|max:100',
            'status' => 'required|in:draft,published',
            'is_homepage' => 'boolean',
            'parent_id' => 'nullable|exists:pages,id',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'show_in_menu' => 'boolean',
            'menu_title' => 'nullable|string|max:255'
        ]);

        $data = $request->all();
        
        // Обновляем slug только если изменился заголовок
        if ($page->title !== $request->title) {
            $data['slug'] = $this->generateSlug($request->title, $page->id);
        }

        // Если это главная страница, снимаем флаг с других страниц
        if ($request->is_homepage) {
            Page::where('is_homepage', true)->where('id', '!=', $page->id)->update(['is_homepage' => false]);
        }

        $page->update($data);

        return redirect()->route('rostpack.pages.show', $page)
            ->with('success', 'Страница успешно обновлена!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $this->authorize('delete', $page);

        // Проверяем, что нет дочерних страниц
        if ($page->hasChildren()) {
            return redirect()->back()
                ->with('error', 'Нельзя удалить страницу, у которой есть дочерние страницы!');
        }

        $page->delete();

        return redirect()->route('pages.index')
            ->with('success', 'Страница успешно удалена!');
    }

    /**
     * Generate unique slug for the page.
     */
    private function generateSlug(string $title, $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Page::where('slug', $slug)->when($excludeId, function($query) use ($excludeId) {
            return $query->where('id', '!=', $excludeId);
        })->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
