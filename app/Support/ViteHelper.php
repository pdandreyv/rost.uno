<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class ViteHelper
{
    /**
     * Получить Vite теги для ассетов
     */
    public static function getViteTags(): string
    {
        $manifestPath = public_path('build/manifest.json');
        
        if (!File::exists($manifestPath)) {
            return self::getFallbackTags();
        }

        $manifest = json_decode(File::get($manifestPath), true);
        
        if (!$manifest) {
            return self::getFallbackTags();
        }

        $tags = [];
        
        // Ищем CSS файлы
        foreach ($manifest as $entry => $data) {
            if (str_contains($entry, 'rostpack.css')) {
                $tags[] = '<link rel="stylesheet" href="' . asset('build/' . $data['file']) . '">';
            }
        }

        // Ищем JS файлы
        foreach ($manifest as $entry => $data) {
            if (str_contains($entry, 'rostpack.js')) {
                $tags[] = '<script type="module" src="' . asset('build/' . $data['file']) . '"></script>';
            }
        }

        return implode("\n", $tags);
    }

    /**
     * Получить fallback теги
     */
    protected static function getFallbackTags(): string
    {
        $cssVersion = AssetVersion::getCssVersion();
        $jsVersion = AssetVersion::getJsVersion();
        
        return implode("\n", [
            '<link rel="stylesheet" href="' . asset("vendor/rostpack/css/rostpack.css?v={$cssVersion}") . '">',
            '<script src="' . asset("vendor/rostpack/js/rostpack.js?v={$jsVersion}") . '"></script>',
        ]);
    }

    /**
     * Получить только CSS тег
     */
    public static function getCssTag(): string
    {
        $manifestPath = public_path('build/manifest.json');
        
        if (!File::exists($manifestPath)) {
            $version = AssetVersion::getCssVersion();
            return '<link rel="stylesheet" href="' . asset("vendor/rostpack/css/rostpack.css?v={$version}") . '">';
        }

        $manifest = json_decode(File::get($manifestPath), true);
        
        if (!$manifest) {
            $version = AssetVersion::getCssVersion();
            return '<link rel="stylesheet" href="' . asset("vendor/rostpack/css/rostpack.css?v={$version}") . '">';
        }

        foreach ($manifest as $entry => $data) {
            if (str_contains($entry, 'rostpack.css')) {
                return '<link rel="stylesheet" href="' . asset('build/' . $data['file']) . '">';
            }
        }

        $version = AssetVersion::getCssVersion();
        return '<link rel="stylesheet" href="' . asset("vendor/rostpack/css/rostpack.css?v={$version}") . '">';
    }

    /**
     * Получить только JS тег
     */
    public static function getJsTag(): string
    {
        $manifestPath = public_path('build/manifest.json');
        
        if (!File::exists($manifestPath)) {
            $version = AssetVersion::getJsVersion();
            return '<script src="' . asset("vendor/rostpack/js/rostpack.js?v={$version}") . '"></script>';
        }

        $manifest = json_decode(File::get($manifestPath), true);
        
        if (!$manifest) {
            $version = AssetVersion::getJsVersion();
            return '<script src="' . asset("vendor/rostpack/js/rostpack.js?v={$version}") . '"></script>';
        }

        foreach ($manifest as $entry => $data) {
            if (str_contains($entry, 'rostpack.js')) {
                return '<script type="module" src="' . asset('build/' . $data['file']) . '"></script>';
            }
        }

        $version = AssetVersion::getJsVersion();
        return '<script src="' . asset("vendor/rostpack/js/rostpack.js?v={$version}") . '"></script>';
    }

    /**
     * Проверить, используется ли Vite
     */
    public static function isViteEnabled(): bool
    {
        $manifestPath = public_path('build/manifest.json');
        return File::exists($manifestPath);
    }

    /**
     * Получить информацию о сборке
     */
    public static function getBuildInfo(): array
    {
        $manifestPath = public_path('build/manifest.json');
        
        if (!File::exists($manifestPath)) {
            return [
                'vite_enabled' => false,
                'build_time' => null,
                'files' => [],
            ];
        }

        $manifest = json_decode(File::get($manifestPath), true);
        
        if (!$manifest) {
            return [
                'vite_enabled' => false,
                'build_time' => null,
                'files' => [],
            ];
        }

        $files = [];
        foreach ($manifest as $entry => $data) {
            if (str_contains($entry, 'rostpack.')) {
                $files[] = [
                    'entry' => $entry,
                    'file' => $data['file'],
                    'url' => asset('build/' . $data['file']),
                    'size' => File::exists(public_path('build/' . $data['file'])) 
                        ? File::size(public_path('build/' . $data['file'])) 
                        : 0,
                ];
            }
        }

        return [
            'vite_enabled' => true,
            'build_time' => File::lastModified($manifestPath),
            'files' => $files,
        ];
    }
}
