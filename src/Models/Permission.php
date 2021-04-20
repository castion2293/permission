<?php

namespace Pharaoh\Permission\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public function groups()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
}
