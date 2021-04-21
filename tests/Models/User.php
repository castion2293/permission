<?php

namespace Pharaoh\Permission\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Pharaoh\Permission\Traits\HasPermission;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasPermission;
    use HasFactory;
}
