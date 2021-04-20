<?php

namespace Pharaoh\Permission\Traits;

use Pharaoh\Permission\Models\Group;

trait HasPermission
{
    /**
     * 獲取所有管理權限
     *
     * @return array
     */
    public function getPermissions(): array
    {
        // 確認登入者是屬於哪一個管理者群組
        $group = $this->morphToMany(Group::class, 'groupable')
            ->with('permissions')
            ->first();

        if (empty($group)) {
            return [];
        }

        return $group->permissions
            ->pluck('permission_key')
            ->toArray();
    }
}
