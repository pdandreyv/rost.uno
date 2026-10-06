<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Support\AssetVersion;
use App\Support\ViteHelper;

class AssetCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rostpack:assets 
                            {action : Действие (info|build|clear|version)}
                            {--type= : Тип ассета (css|js|all)}';

    /**
     * The console command description.
     */
    protected $description = 'Управление ассетами RostPack';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $type = $this->option('type') ?? 'all';

        switch ($action) {
            case 'info':
                $this->showInfo();
                break;
            case 'build':
                $this->buildAssets();
                break;
            case 'clear':
                $this->clearAssets();
                break;
            case 'version':
                $this->showVersion($type);
                break;
            default:
                $this->error("Неизвестное действие: {$action}");
                return 1;
        }

        return 0;
    }

    /**
     * Показать информацию об ассетах
     */
    protected function showInfo()
    {
        $this->info('Информация об ассетах RostPack:');
        $this->newLine();

        $buildInfo = ViteHelper::getBuildInfo();
        
        if ($buildInfo['vite_enabled']) {
            $this->info('✅ Vite включен');
            $this->info('Время сборки: ' . date('Y-m-d H:i:s', $buildInfo['build_time']));
            $this->newLine();
            
            $this->info('Файлы:');
            foreach ($buildInfo['files'] as $file) {
                $size = $this->formatBytes($file['size']);
                $this->line("  - {$file['entry']}: {$file['url']} ({$size})");
            }
        } else {
            $this->warn('⚠️ Vite отключен, используются fallback ассеты');
            $this->newLine();
            
            $cssUrl = AssetVersion::getAssetUrl('css');
            $jsUrl = AssetVersion::getAssetUrl('js');
            
            $this->line("CSS: {$cssUrl}");
            $this->line("JS: {$jsUrl}");
        }

        $this->newLine();
        $this->info('Версии:');
        $this->line("CSS: " . AssetVersion::getCssVersion());
        $this->line("JS: " . AssetVersion::getJsVersion());
    }

    /**
     * Собрать ассеты
     */
    protected function buildAssets()
    {
        $this->info('Сборка ассетов...');
        
        if (!file_exists(base_path('package.json'))) {
            $this->error('Файл package.json не найден. Сначала опубликуйте конфигурацию Vite.');
            return;
        }

        if (!file_exists(base_path('node_modules'))) {
            $this->warn('Зависимости Node.js не установлены. Устанавливаем...');
            $this->call('npm', ['install']);
        }

        $this->info('Запуск сборки...');
        $this->call('npm', ['run', 'build']);

        $this->info('✅ Сборка завершена!');
    }

    /**
     * Очистить ассеты
     */
    protected function clearAssets()
    {
        $this->info('Очистка ассетов...');
        
        $buildDir = public_path('build');
        if (is_dir($buildDir)) {
            $this->call('artisan', ['vendor:publish', '--tag=assets', '--force']);
            $this->info('✅ Ассеты очищены!');
        } else {
            $this->warn('Директория build не найдена.');
        }
    }

    /**
     * Показать версию ассетов
     */
    protected function showVersion($type)
    {
        switch ($type) {
            case 'css':
                $version = AssetVersion::getCssVersion();
                $this->line("CSS версия: {$version}");
                break;
            case 'js':
                $version = AssetVersion::getJsVersion();
                $this->line("JS версия: {$version}");
                break;
            case 'all':
            default:
                $cssVersion = AssetVersion::getCssVersion();
                $jsVersion = AssetVersion::getJsVersion();
                $this->line("CSS версия: {$cssVersion}");
                $this->line("JS версия: {$jsVersion}");
                break;
        }
    }

    /**
     * Форматировать размер файла
     */
    protected function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}
