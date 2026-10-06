<?php

namespace App\View;

use Illuminate\Support\Facades\Blade;
use App\Support\ViteHelper;
use App\Support\AssetVersion;

class BladeDirectives
{
    /**
     * Зарегистрировать Blade директивы
     */
    public static function register(): void
    {
        // Директива для подключения CSS
        Blade::directive('rostpackCss', function () {
            return "<?php echo \App\Support\ViteHelper::getCssTag(); ?>";
        });

        // Директива для подключения JS
        Blade::directive('rostpackJs', function () {
            return "<?php echo \App\Support\ViteHelper::getJsTag(); ?>";
        });

        // Директива для подключения всех ассетов
        Blade::directive('rostpackAssets', function () {
            return "<?php echo \App\Support\ViteHelper::getViteTags(); ?>";
        });

        // Директива для получения версии CSS
        Blade::directive('rostpackCssVersion', function () {
            return "<?php echo \App\Support\AssetVersion::getCssVersion(); ?>";
        });

        // Директива для получения версии JS
        Blade::directive('rostpackJsVersion', function () {
            return "<?php echo \App\Support\AssetVersion::getJsVersion(); ?>";
        });

        // Директива для проверки Vite
        Blade::directive('rostpackViteEnabled', function () {
            return "<?php echo \App\Support\ViteHelper::isViteEnabled() ? 'true' : 'false'; ?>";
        });

        // Директива для получения URL CSS
        Blade::directive('rostpackCssUrl', function () {
            return "<?php echo \App\Support\AssetVersion::getAssetUrl('css'); ?>";
        });

        // Директива для получения URL JS
        Blade::directive('rostpackJsUrl', function () {
            return "<?php echo \App\Support\AssetVersion::getAssetUrl('js'); ?>";
        });
    }
}
