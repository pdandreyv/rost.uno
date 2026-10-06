<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Page;
use App\Models\TelegramUser;

class PagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(TelegramUser $user): bool
    {
        return true; // Все могут просматривать список страниц
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(TelegramUser $user, Page $page): bool
    {
        // Пользователь может просматривать свои страницы или опубликованные
        return $user->id === $page->author_id || $page->isPublished();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(TelegramUser $user): bool
    {
        return true; // Все авторизованные пользователи могут создавать страницы
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(TelegramUser $user, Page $page): bool
    {
        return $user->id === $page->author_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(TelegramUser $user, Page $page): bool
    {
        return $user->id === $page->author_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(TelegramUser $user, Page $page): bool
    {
        return $user->id === $page->author_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(TelegramUser $user, Page $page): bool
    {
        return $user->id === $page->author_id;
    }
}
