<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class TelegramUser extends Model implements Authenticatable
{
    use AuthenticatableTrait;
    /**
     * The table associated with the model.
     */
    protected $table = 'rost_users';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'telegram_id',
        'telegram_username',
        'first_name',
        'middle_name',
        'last_name',
        'birth_date',
        'birth_date_view',
        'gender',
        'phone_number',
        'phone_number_view',
        'photo',
        'user_type_id',
        'city',
        'city_view',
        'alias',
        'referrer_id',
        'verified',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'birth_date' => 'date',
        'birth_date_view' => 'boolean',
        'phone_number_view' => 'boolean',
        'city_view' => 'boolean',
        'verified' => 'boolean',
        'user_type_id' => 'integer',
        'referrer_id' => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the user type that owns the user.
     */
    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }

    /**
     * Get the referrer that owns the user.
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'referrer_id');
    }

    /**
     * Get the users referred by this user.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(TelegramUser::class, 'referrer_id');
    }

    /**
     * Find user by Telegram ID.
     */
    public static function findByTelegramId(int $telegramId): ?self
    {
        return static::where('telegram_id', $telegramId)->first();
    }

    /**
     * Find user by Telegram username.
     */
    public static function findByTelegramUsername(string $username): ?self
    {
        return static::where('telegram_username', $username)->first();
    }

    /**
     * Create or update user from Telegram data.
     */
    public static function createOrUpdateFromTelegram(array $telegramData): self
    {
        $telegramId = $telegramData['id'];
        $user = static::findByTelegramId($telegramId);

        $userData = [
            'telegram_id' => $telegramId,
            'telegram_username' => $telegramData['username'] ?? null,
            'first_name' => $telegramData['first_name'] ?? '',
            'last_name' => $telegramData['last_name'] ?? '',
        ];

        // Сохраняем фото из Telegram, если оно есть
        if (isset($telegramData['photo_url']) && !empty($telegramData['photo_url'])) {
            $userData['photo'] = static::saveTelegramPhoto($telegramData['photo_url'], $telegramId);
        }

        \Log::info('Creating/updating user with data:', $userData);

        if ($user) {
            $user->update($userData);
            \Log::info('User updated:', ['id' => $user->id, 'telegram_id' => $user->telegram_id]);
        } else {
            $user = static::create($userData);
            \Log::info('User created:', ['id' => $user->id, 'telegram_id' => $user->telegram_id]);
        }

        return $user;
    }

    /**
     * Save Telegram photo to local storage.
     */
    public static function saveTelegramPhoto(string $photoUrl, int $telegramId): string
    {
        try {
            // Создаем директорию для фото пользователей
            $uploadPath = public_path('storage/telegram-photos');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Генерируем имя файла
            $extension = pathinfo(parse_url($photoUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = 'telegram_' . $telegramId . '_' . time() . '.' . $extension;
            $filePath = $uploadPath . '/' . $filename;

            // Скачиваем фото
            $photoData = file_get_contents($photoUrl);
            if ($photoData === false) {
                \Log::error('Failed to download Telegram photo', ['url' => $photoUrl]);
                return '';
            }

            // Сохраняем файл
            if (file_put_contents($filePath, $photoData) === false) {
                \Log::error('Failed to save Telegram photo', ['path' => $filePath]);
                return '';
            }

            // Возвращаем относительный путь для хранения в БД
            return 'telegram-photos/' . $filename;

        } catch (\Exception $e) {
            \Log::error('Error saving Telegram photo: ' . $e->getMessage(), [
                'url' => $photoUrl,
                'telegram_id' => $telegramId
            ]);
            return '';
        }
    }

    /**
     * Get photo URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo)) {
            return null;
        }

        // Если это уже полный URL (например, из Telegram), возвращаем как есть
        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }

        // Если это локальный файл, возвращаем URL к нему
        return asset('storage/' . $this->photo);
    }

    /**
     * Get full name.
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->last_name
        ]);

        return implode(' ', $parts);
    }

    /**
     * Get display name (alias or full name).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->alias ?: $this->full_name ?: 'Неизвестный пользователь';
    }

    /**
     * Check if user is verified.
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * Check if user has Telegram username.
     */
    public function hasTelegramUsername(): bool
    {
        return !empty($this->telegram_username);
    }

    /**
     * Get Telegram mention.
     */
    public function getTelegramMentionAttribute(): string
    {
        if ($this->hasTelegramUsername()) {
            return '@' . $this->telegram_username;
        }

        return $this->display_name;
    }
}
