<?php

namespace Pharaoh\Permission;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        parent::register();

        $loader = AliasLoader::getInstance();
        $loader->alias('permission', 'Pharaoh\Permission\Permission');
    }
}
