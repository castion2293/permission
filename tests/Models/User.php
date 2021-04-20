<?php

namespace Pharaoh\Permission\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pharaoh\Permission\Traits\HasPermission;

class User extends Model
{
    use HasPermission;
    use HasFactory;
}
