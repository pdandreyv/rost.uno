<?php

namespace App\Providers;

use App\Models\BlogPost;
use App\Models\Page;
use App\Policies\BlogPostPolicy;
use App\Policies\PagePolicy;
use App\Services\RostPack;
use App\Services\TelegramService;
use App\View\BladeDirectives;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('rostpack', function () {
            return new RostPack();
        });

        $this->app->singleton(TelegramService::class, function () {
            return new TelegramService();
        });

        $this->app->alias(TelegramService::class, 'telegram');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(BlogPost::class, BlogPostPolicy::class);
        Gate::policy(Page::class, PagePolicy::class);

        BladeDirectives::register();

        View::share('isTelegramWebApp', false);
        View::share('isDevelopmentMode', app()->environment('local'));
    }
}
