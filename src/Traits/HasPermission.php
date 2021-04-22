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

    /**
     * 加入至管理權限群組
     *
     * @param int $groupId
     */
    public function addGroup(int $groupId)
    {
        $this->morphToMany(Group::class, 'groupable')
            ->sync($groupId);
    }

    /**
     * 獲取所屬權限群組
     */
    public function belongGroup()
    {
        return $this->morphToMany(Group::class, 'groupable')->first();
    }
}
