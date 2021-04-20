<?php

namespace Pharaoh\Permission\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Permission
 *
 * @see \Pharaoh\Permission\Permission
 */
class Permission extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        // 回傳 alias 的名稱
        return 'permission';
    }
}
