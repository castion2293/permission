<?php

namespace Pharaoh\Permission\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Pharaoh\Permission\Models\Group;
use Pharaoh\Permission\Models\Permission;

class UpdateRootGroupPermissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:root-group-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新管理者權限群組(用於新增功能)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $rootGroupName = config('permission.root_group_name');
        $rootGroup = Group::where('name', $rootGroupName)->first();
        if (empty($rootGroup)) {
            $this->error('找不到權限管理名稱為「' . $rootGroupName . '」的資料!!!');
            return 0;
        }

        DB::transaction(
            function () use ($rootGroup) {
                $permissionKeys = collect(config('permission.items'))->pluck('menu')
                    ->flatten(1)
                    ->pluck('func_key')
                    ->toArray();

                $rootGroup->addPermissions($permissionKeys);
            }
        );

        $this->info('更新「' . $rootGroupName . '」的權限結束。');

        return 0;
    }
}
