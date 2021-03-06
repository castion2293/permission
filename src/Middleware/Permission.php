<?php

namespace Pharaoh\Permission\Middleware;

use Closure;
use Pharaoh\Permission\Exceptions\PermissionException;

class Permission
{
    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @param $permissionKeys
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next, ...$permissionKeys)
    {
        $user = $request->user();

        if (empty($user)) {
            throw new \Exception('沒有認證後的管理者Model');
        }

        $permissionSettingsCollection = collect(config('permission.items'))->pluck('menu')
            ->flatten(1);

        $userPermissions = $user->getPermissions();
        foreach ($permissionKeys as $permissionKey) {
            // 檢查權限是否開啟
            $isFuncKeyOpen = $permissionSettingsCollection
                ->where('func_key', $permissionKey)
                ->where('open', true)
                ->isNotEmpty();

            if (!$isFuncKeyOpen) {
                throw new PermissionException("{$permissionKey} permission is not open");
            }

            // 檢查 user 有無權限g
            if (!in_array(intval($permissionKey), $userPermissions)) {
                throw new PermissionException("Does not has {$permissionKey} permission");
            }
        }

        return $next($request);
    }
}
