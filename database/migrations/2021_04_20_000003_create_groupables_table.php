<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groupables', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK');
            $table->unsignedInteger('group_id')->comment('權限管理群組ID');
            $table->unsignedInteger('groupable_id')->comment('管理者ID');
            $table->string('groupable_type')->comment('管理者類型');

            // 建立時間
            $table->datetime('created_at')
                ->default(DB::raw('CURRENT_TIMESTAMP'))
                ->comment('建立時間');

            // 最後更新
            $table->datetime('updated_at')
                ->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))
                ->comment('最後更新');

            $table->index('group_id');
            $table->index('groupable_id');
            $table->unique(['group_id', 'groupable_id', 'groupable_type']);
        });

        DB::statement("ALTER TABLE `" . "groupables" . "` COMMENT '權限管理群組與管理者對應表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groupables');
    }
};
