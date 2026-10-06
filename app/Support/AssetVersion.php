<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class AssetVersion
{
    /**
     * Получить версию CSS файла
     */
    public static function getCssVersion(): string
    {
        return self::getAssetVersion('css');
    }

    /**
     * Получить версию JS файла
     */
    public static function getJsVersion(): string
    {
        return self::getAssetVersion('js');
    }

    /**
     * Получить версию файла по типу
     */
    protected static function getAssetVersion(string $type): string
    {
        $manifestPath = public_path('build/manifest.json');
        
        if (!File::exists($manifestPath)) {
            return self::getFallbackVersion($type);
        }

        $manifest = json_decode(File::get($manifestPath), true);
        
        if (!$manifest) {
            return self::getFallbackVersion($type);
        }

        // Ищем файл по типу
        foreach ($manifest as $entry => $data) {
            if (str_contains($entry, "rostpack.{$type}")) {
                return self::extractVersionFromPath($data['file'] ?? '');
            }
        }

        return self::getFallbackVersion($type);
    }

    /**
     * Извлечь версию из пути файла
     */
    protected static function extractVersionFromPath(string $path): string
    {
        // Извлекаем хэш из имени файла (например: assets/rostpack-abc123.js)
        if (preg_match('/-([a-f0-9]{8,})\./', $path, $matches)) {
            return $matches[1];
        }

        return '1.0.0';
    }

    /**
     * Получить fallback версию
     */
    protected static function getFallbackVersion(string $type): string
    {
        $filePath = public_path("vendor/rostpack/{$type}/rostpack.{$type}");
        
        if (File::exists($filePath)) {
            return (string) File::lastModified($filePath);
        }

        return '1.0.0';
    }

    /**
     * Получить URL файла с версией
     */
    public static function getAssetUrl(string $type, bool $withVersion = true): string
    {
        $manifestPath = public_path('build/manifest.json');
        
        if (!File::exists($manifestPath)) {
            return self::getFallbackUrl($type, $withVersion);
        }

        $manifest = json_decode(File::get($manifestPath), true);
        
        if (!$manifest) {
            return self::getFallbackUrl($type, $withVersion);
        }

        // Ищем файл по типу
        foreach ($manifest as $entry => $data) {
            if (str_contains($entry, "rostpack.{$type}")) {
                return asset('build/' . $data['file']);
            }
        }

        return self::getFallbackUrl($type, $withVersion);
    }

    /**
     * Получить fallback URL
     */
    protected static function getFallbackUrl(string $type, bool $withVersion): string
    {
        $baseUrl = asset("vendor/rostpack/{$type}/rostpack.{$type}");
        
        if ($withVersion) {
            $version = self::getFallbackVersion($type);
            return $baseUrl . '?v=' . $version;
        }

        return $baseUrl;
    }

    /**
     * Проверить, нужно ли обновить кэш
     */
    public static function shouldUpdateCache(): bool
    {
        $manifestPath = public_path('build/manifest.json');
        
        if (!File::exists($manifestPath)) {
            return true;
        }

        $manifest = json_decode(File::get($manifestPath), true);
        
        if (!$manifest) {
            return true;
        }

        // Проверяем, есть ли файлы в манифесте
        foreach ($manifest as $entry => $data) {
            if (str_contains($entry, 'rostpack.')) {
                $filePath = public_path('build/' . $data['file']);
                if (!File::exists($filePath)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Получить информацию о всех ассетах
     */
    public static function getAssetsInfo(): array
    {
        $manifestPath = public_path('build/manifest.json');
        
        if (!File::exists($manifestPath)) {
            return [
                'css' => self::getFallbackUrl('css', true),
                'js' => self::getFallbackUrl('js', true),
                'version' => '1.0.0',
                'manifest_exists' => false,
            ];
        }

        $manifest = json_decode(File::get($manifestPath), true);
        
        if (!$manifest) {
            return [
                'css' => self::getFallbackUrl('css', true),
                'js' => self::getFallbackUrl('js', true),
                'version' => '1.0.0',
                'manifest_exists' => false,
            ];
        }

        $assets = [
            'css' => null,
            'js' => null,
            'version' => '1.0.0',
            'manifest_exists' => true,
        ];

        foreach ($manifest as $entry => $data) {
            if (str_contains($entry, 'rostpack.css')) {
                $assets['css'] = asset('build/' . $data['file']);
                $assets['version'] = self::extractVersionFromPath($data['file'] ?? '');
            } elseif (str_contains($entry, 'rostpack.js')) {
                $assets['js'] = asset('build/' . $data['file']);
                $assets['version'] = self::extractVersionFromPath($data['file'] ?? '');
            }
        }

        return $assets;
    }
}
