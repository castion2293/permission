<?php

namespace Pharaoh\Permission\Middleware;

use Closure;

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

        $userPermissions = $user->getPermissions();

        foreach ($permissionKeys as $permissionKey) {
            if (!in_array(intval($permissionKey), $userPermissions)) {
                return response()->json(
                    [
                        'code' => 403001,
                        'error' => '無該功能權限'
                    ],
                    403
                );
            }
        }

        return $next($request);
    }
}
