<?php

namespace Pharaoh\Permission\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * 覆寫序列化方法 toArray()時使用
     *
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'group_id', 'id');
    }

    /**
     * 更新管理群組權限
     *
     * @param array $permissions
     */
    public function addPermissions(array $permissions)
    {
        // 刪除該群組舊有權限
        $this->permissions()->delete();

        $permissionModels = collect($permissions)->map(
            function ($permissionKey) {
                return new Permission(
                    [
                        'group_id' => $this->id,
                        'permission_key' => $permissionKey
                    ]
                );
            }
        );

        $this->permissions()->saveMany($permissionModels);
    }
}
