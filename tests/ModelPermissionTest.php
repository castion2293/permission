<?php

namespace Pharaoh\Permission\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Pharaoh\Permission\Models\Group;
use Pharaoh\Permission\Models\Permission;
use Pharaoh\Permission\Tests\Models\User;

class ModelPermissionTest extends BaseTestCase
{
    use DatabaseMigrations;

    protected Model $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
        $this->user->id = 1;
    }

    /**
     * 測試User model 獲取所有權限
     */
    public function testUserGetPermissions()
    {
        // Arrange
        $group = Group::factory()->create()->toArray();

        DB::table('groupables')
            ->insert(
                [
                    'group_id' => $group['id'],
                    'groupable_id' => $this->user->id,
                    'groupable_type' => User::class
                ]
            );

        $permissionKeys = [1101, 1102, 1103];
        $permissions = [];
        foreach ($permissionKeys as $permissionKey) {
            $permissions[] = Permission::factory()->create(
                [
                    'group_id' => $group['id'],
                    'permission_key' => $permissionKey
                ]
            )->toArray();
        }

        // Act
        $userPermissions = $this->user->getPermissions();

        // Assert
        $this->assertSame($permissionKeys, $userPermissions);
    }

    /**
     * 測試 User model 沒有加入任何一個群組
     */
    public function testUserGetPermissionWithoutGroup()
    {
        // Arrange

        // Act
        $userPermissions = $this->user->getPermissions();

        // Assert
        $this->assertEmpty($userPermissions);
    }

    /**
     * 測試 User model 管理群組裡沒有任何權限
     */
    public function testUserGetPermissionWithoutPermission()
    {
        // Arrange
        $group = Group::factory()->create()->toArray();

        DB::table('groupables')
            ->insert(
                [
                    'group_id' => $group['id'],
                    'groupable_id' => $this->user->id,
                    'groupable_type' => User::class
                ]
            );

        // Act
        $userPermissions = $this->user->getPermissions();

        // Assert
        $this->assertEmpty($userPermissions);
    }
}
