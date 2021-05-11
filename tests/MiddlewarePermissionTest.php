<?php

namespace Pharaoh\Permission\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Pharaoh\Permission\Exceptions\PermissionException;
use Pharaoh\Permission\Middleware\Permission;
use Pharaoh\Permission\Models\Group;
use Pharaoh\Permission\Tests\Models\User;
use Pharaoh\Permission\Models\Permission as PermissionModel;

class MiddlewarePermissionTest extends BaseTestCase
{
    use DatabaseMigrations;

    /**
     * User Model
     *
     * @var Model|User
     */
    protected Model $user;

    /**
     * permission middleware
     *
     * @var
     */
    protected $middleware;

    /**
     * request 請求
     *
     * @var Request
     */
    protected Request $request;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
        $this->user->id = 1;

        $group = Group::factory()->create()->toArray();

        DB::table('groupables')
            ->insert(
                [
                    'group_id' => $group['id'],
                    'groupable_id' => $this->user->id,
                    'groupable_type' => User::class
                ]
            );

        foreach ([1001, 1002, 1003] as $permissionKey) {
            PermissionModel::factory()->create(
                [
                    'group_id' => $group['id'],
                    'permission_key' => $permissionKey
                ]
            )->toArray();
        }

        // --- 描述 mock 的事件故事 ---
        // 在整個測試過程，建立一個程式會使用到的類別物件
        // 並且被呼叫 callApi()
        // 而且回傳了自定義的假資料
        $this->initMock(Request::class)
            ->makePartial()
            ->shouldReceive('user')
            ->andReturn($this->user);

        $this->request = \App::make(Request::class);

        $this->middleware = new Permission();
    }

    /**
     * 測試檢查一組 permissionKey
     *
     * @throws \Exception
     */
    public function testMiddlewareWithOnePermissionKey()
    {
        // Act
        $response = $this->middleware->handle(
            $this->request,
            function () {
                return response()->json(['code' => 200001], 200);
            },
            1001
        );

        $responseData = json_decode($response->getContent(), true);

        // Assert
        $code = Arr::get($responseData, 'code');
        $this->assertEquals(200001, $code);
    }

    /**
     * 測試檢查多組 permissionKey
     *
     * @throws \Exception
     */
    public function testMiddlewareWithMultiplePermissionKey()
    {
        // Act
        $response = $this->middleware->handle(
            $this->request,
            function () {
                return response()->json(['code' => 200001], 200);
            },
            1001,
            1002,
            1003
        );

        $responseData = json_decode($response->getContent(), true);

        // Assert
        $code = Arr::get($responseData, 'code');
        $this->assertEquals(200001, $code);
    }

    /**
     * 測試檢查 User 沒有該組 permissionKey
     */
    public function testMiddlewareWithoutPermissionKey()
    {
        // Arrange
        $permissionKey = 1101;

        // Except
        $this->expectException(PermissionException::class);
        $this->expectErrorMessage("Does not has {$permissionKey} permission");

        // Act
        $this->middleware->handle(
            $this->request,
            function () {
                return response()->json(['code' => 200001], 200);
            },
            $permissionKey
        );
    }

    /**
     * 測試檢查 Permission Config func_key open = false
     */
    public function testMiddlewareFuncKeyIsNotOpen()
    {
        // Arrange
        $permissionKey = 1102;

        // Expect
        $this->expectException(PermissionException::class);
        $this->expectErrorMessage("{$permissionKey} permission is not open");

        // Act
        $this->middleware->handle(
            $this->request,
            function () {
                return response()->json(['code' => 200001], 200);
            },
            $permissionKey
        );
    }
}
