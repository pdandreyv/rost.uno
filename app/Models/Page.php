<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'rost_pages';

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'template',
        'status',
        'is_homepage',
        'parent_id',
        'sort_order',
        'author_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views_count',
        'show_in_menu',
        'menu_title'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_homepage' => 'boolean',
        'show_in_menu' => 'boolean',
        'views_count' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'deleted_at'
    ];

    /**
     * Get the author that owns the page.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'author_id');
    }

    /**
     * Get the parent page.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    /**
     * Get the child pages.
     */
    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Scope a query to only include published pages.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include draft pages.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include pages that should be shown in menu.
     */
    public function scopeInMenu($query)
    {
        return $query->where('show_in_menu', true);
    }

    /**
     * Scope a query to only include root pages (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the excerpt or truncated content.
     */
    public function getExcerptAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return $this->content ? \Str::limit(strip_tags($this->content), 200) : '';
    }

    /**
     * Get the full URL for the page.
     */
    public function getUrlAttribute()
    {
        return route('rostpack.pages.show', $this->slug);
    }

    /**
     * Get the menu title or fallback to title.
     */
    public function getMenuTitleAttribute($value)
    {
        return $value ?: $this->title;
    }

    /**
     * Check if the page is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if the page is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the page is homepage.
     */
    public function isHomepage(): bool
    {
        return $this->is_homepage;
    }

    /**
     * Check if the page has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Get the breadcrumb trail.
     */
    public function getBreadcrumbAttribute()
    {
        $breadcrumb = [];
        $page = $this;

        while ($page) {
            array_unshift($breadcrumb, [
                'title' => $page->title,
                'url' => $page->url
            ]);
            $page = $page->parent;
        }

        return $breadcrumb;
    }

    /**
     * Increment views count.
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
