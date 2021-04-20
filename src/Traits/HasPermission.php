<?php

namespace Pharaoh\Permission\Traits;

use Pharaoh\Permission\Models\Group;

trait HasPermission
{
    public function getPermissions()
    {
        return $this->morphToMany(Group::class, 'groupable')
            ->first()
            ->permissions()
            ->pluck('permission_key')
            ->toArray();
    }
}
