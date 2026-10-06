<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\BlogPost;
use App\Models\TelegramUser;

class BlogPostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(TelegramUser $user): bool
    {
        return true; // Все могут просматривать список статей
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(TelegramUser $user, BlogPost $blogPost): bool
    {
        // Пользователь может просматривать свои статьи или опубликованные
        return $user->id === $blogPost->author_id || $blogPost->isPublished();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(TelegramUser $user): bool
    {
        return true; // Все авторизованные пользователи могут создавать статьи
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(TelegramUser $user, BlogPost $blogPost): bool
    {
        return $user->id === $blogPost->author_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(TelegramUser $user, BlogPost $blogPost): bool
    {
        return $user->id === $blogPost->author_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(TelegramUser $user, BlogPost $blogPost): bool
    {
        return $user->id === $blogPost->author_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(TelegramUser $user, BlogPost $blogPost): bool
    {
        return $user->id === $blogPost->author_id;
    }
}
