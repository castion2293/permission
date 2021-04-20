<?php

namespace Pharaoh\Permission;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 合併套件migration
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes(
            [
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ],
            'permission-database'
        );

        // 合併套件設定檔
        $this->mergeConfigFrom(__DIR__ . '/../config/permission.php', 'permission');

        $this->publishes([__DIR__ . '/../config/permission.php' => config_path('permission')], 'permission-config');
    }

    public function register()
    {
        parent::register();

        $loader = AliasLoader::getInstance();
        $loader->alias('permission', 'Pharaoh\Permission\Permission');
    }
}
