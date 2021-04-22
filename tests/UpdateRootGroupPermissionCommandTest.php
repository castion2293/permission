<?php

namespace Pharaoh\Permission\Tests;

use Illuminate\Support\Arr;
use Pharaoh\Permission\Models\Group;
use Pharaoh\Permission\Models\Permission;

class UpdateRootGroupPermissionCommandTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testUpdateRootGroupPermissionCommand()
    {
        // Arrange
        $group = Group::factory()->create(
            [
                'name' => config('permission.root_group_name')
            ]
        )->toArray();

        $permissionKeys = [1201, 1202, 1203];
        foreach ($permissionKeys as $permissionKey) {
            $permissions[] = Permission::factory()->create(
                [
                    'group_id' => $group['id'],
                    'permission_key' => $permissionKey
                ]
            )->toArray();
        }

        // Act
        $this->artisan('update:root-group-permission')
            ->expectsOutput('更新「' . config('permission.root_group_name') . '」的權限結束。');

        // Assert
        foreach ($permissions as $permission) {
            $this->assertDatabaseMissing(
                'permissions',
                [
                    'id' => Arr::get($permission, 'id'),
                    'group_id' => Arr::get($group, 'id'),
                    'permission_key' => Arr::get($permission, 'permission_key'),
                ]
            );
        }

        $newPermissions = collect(config('permission.items'))->pluck('menu')
            ->flatten(1);

        foreach ($newPermissions as $newPermission) {
            $this->assertDatabaseHas(
                'permissions',
                [
                    'group_id' => Arr::get($group, 'id'),
                    'permission_key' => Arr::get($newPermission, 'func_key'),
                ]
            );
        }
    }
}
