<?php

namespace Pharaoh\Permission\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Pharaoh\Permission\Models\Group;
use Pharaoh\Permission\Models\Permission;
use Pharaoh\Permission\Tests\Models\User;

class ModelPermissionTest extends BaseTestCase
{
    use DatabaseMigrations;

    /**
     * User Model
     *
     * @var Model|User
     */
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

    /**
     * 測試 加入 管理群組
     */
    public function testUserAddGroup()
    {
        // Arrange
        $group = Group::factory()->create()->toArray();

        // Act
        $this->user->addGroup($group['id']);

        // Assert
        $this->assertDatabaseHas(
            'groupables',
            [
                'group_id' => $group['id'],
                'groupable_id' => $this->user->id,
                'groupable_type' => $this->user::class
            ]
        );
    }

    /**
     * 測試 改變 管理群組
     */
    public function testUserChangeGroup()
    {
        // Arrange
        $oldGroup = Group::factory()->create()->toArray();

        DB::table('groupables')
            ->insert(
                [
                    'group_id' => $oldGroup['id'],
                    'groupable_id' => $this->user->id,
                    'groupable_type' => User::class
                ]
            );

        $newGroup = Group::factory()->create()->toArray();

        // Act
        $this->user->addGroup($newGroup['id']);

        // Assert
        $this->assertDatabaseHas(
            'groupables',
            [
                'group_id' => $newGroup['id'],
                'groupable_id' => $this->user->id,
                'groupable_type' => $this->user::class
            ]
        );

        $this->assertDatabaseMissing(
            'groupables',
            [
                'group_id' => $oldGroup['id'],
                'groupable_id' => $this->user->id,
                'groupable_type' => $this->user::class
            ]
        );
    }

    /**
     * 測試 管理群組 改變 權限
     */
    public function testGroupChangePermission()
    {
        // Arrange
        $group = Group::factory()->create();

        $oldPermissionKeys = [1101, 1102, 1103];
        $oldPermissions = [];
        foreach ($oldPermissionKeys as $permissionKey) {
            $oldPermissions[] = Permission::factory()->create(
                [
                    'group_id' => data_get($group, 'id'),
                    'permission_key' => $permissionKey
                ]
            )->toArray();
        }

        $newPermissionsKeys = [1101, 1102, 1103, 1104, 1105];

        // Act
        $group->addPermissions($newPermissionsKeys);

        // Assert
        foreach ($oldPermissions as $oldPermission) {
            $this->assertDatabaseMissing(
                'permissions',
                [
                    'id' => Arr::get($oldPermissions, 'id'),
                    'group_id' => data_get($group, 'id'),
                    'permission_key' => Arr::get($oldPermissions, 'permission_key')
                ]
            );
        }

        foreach ($newPermissionsKeys as $newPermissionsKey) {
            $this->assertDatabaseHas(
                'permissions',
                [
                    'group_id' => data_get($group, 'id'),
                    'permission_key' => $newPermissionsKey
                ]
            );
        }
    }

    /**
     * 測試 User model 獲取所屬群組
     */
    public function testUserBelongGroup()
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
        $belongGroup = $this->user->belongGroup()->toArray();

        // Assert
        $this->assertEquals(Arr::get($group, 'id'), Arr::get($belongGroup, 'id'));
    }

    /**
     * 測試 User model 獲取所屬群組 卻無任何所屬群組
     */
    public function testUserBelongGroupWithoutGroup()
    {
        // Act
        $belongGroup = $this->user->belongGroup();

        // Assert
        $this->assertNull($belongGroup);
    }

    /**
     * 測試 刪除 Group
     */
    public function testDeleteGroup()
    {
        // Arrange
        $group = Group::factory()->create();

        $permissionKeys = [1101, 1102, 1103];
        $Permissions = [];
        foreach ($permissionKeys as $permissionKey) {
            $Permissions[] = Permission::factory()->create(
                [
                    'group_id' => data_get($group, 'id'),
                    'permission_key' => $permissionKey
                ]
            )->toArray();
        }


        // Act
        $group->deleteGroup();

        // Assert
        $this->assertDatabaseMissing(
            'groups',
            [
                'id' => $group['id']
            ]
        );

        foreach ($Permissions as $permission) {
            $this->assertDatabaseMissing('permissions', [
                'id' => $permission['id']
            ]);
        }
    }
}
