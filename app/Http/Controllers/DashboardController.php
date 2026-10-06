<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\BlogPost;
use App\Models\Page;
use App\Models\TelegramUser;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Статистика для пользователя
        $stats = [
            'blog_posts' => BlogPost::where('author_id', $user->id)->count(),
            'published_posts' => BlogPost::where('author_id', $user->id)->published()->count(),
            'draft_posts' => BlogPost::where('author_id', $user->id)->draft()->count(),
            'pages' => Page::where('author_id', $user->id)->count(),
            'published_pages' => Page::where('author_id', $user->id)->published()->count(),
            'draft_pages' => Page::where('author_id', $user->id)->draft()->count(),
            'total_views' => BlogPost::where('author_id', $user->id)->sum('views_count') + 
                            Page::where('author_id', $user->id)->sum('views_count')
        ];

        // Последние статьи
        $recentPosts = BlogPost::where('author_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Последние страницы
        $recentPages = Page::where('author_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        return view('rostpack.dashboard.index', compact('stats', 'recentPosts', 'recentPages'));
    }

    /**
     * Show user profile.
     */
    public function profile()
    {
        $user = Auth::user();
        
        return view('rostpack.dashboard.profile', compact('user'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'nullable|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'city' => 'nullable|string|max:191',
            'phone_number' => 'nullable|string|max:50',
            'alias' => 'nullable|string|max:191',
            'birth_date_view' => 'nullable|boolean',
            'phone_number_view' => 'nullable|boolean',
            'city_view' => 'nullable|boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only([
            'first_name', 'middle_name', 'last_name', 
            'birth_date', 'gender', 'city', 'phone_number', 'alias',
            'birth_date_view', 'phone_number_view', 'city_view'
        ]);

        // Обработка фото
        if ($request->hasFile('photo')) {
            // Удаляем старое фото
            if ($user->photo && !str_starts_with($user->photo, 'http')) {
                \Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('user-photos', 'public');
        }

        $user->update($data);

        return redirect()->route('rostpack.profile.index')
            ->with('success', 'Профиль успешно обновлен!');
    }

    /**
     * Show user's blog posts.
     */
    public function myPosts(Request $request)
    {
        $user = Auth::user();
        
        $query = BlogPost::where('author_id', $user->id);

        // Фильтр по статусу
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Поиск
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        $posts = $query->orderBy('updated_at', 'desc')->paginate(10);

        return view('rostpack.dashboard.my-posts', compact('posts'));
    }

    /**
     * Show user's pages.
     */
    public function myPages(Request $request)
    {
        $user = Auth::user();
        
        $query = Page::where('author_id', $user->id);

        // Фильтр по статусу
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Поиск
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        $pages = $query->orderBy('updated_at', 'desc')->paginate(10);

        return view('rostpack.dashboard.my-pages', compact('pages'));
    }

    /**
     * Show analytics for user's content.
     */
    public function analytics()
    {
        $user = Auth::user();
        
        // Статистика по месяцам за последний год
        $monthlyStats = BlogPost::where('author_id', $user->id)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Топ статей по просмотрам
        $topPosts = BlogPost::where('author_id', $user->id)
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        // Топ страниц по просмотрам
        $topPages = Page::where('author_id', $user->id)
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        return view('rostpack.dashboard.analytics', compact('monthlyStats', 'topPosts', 'topPages'));
    }
}
