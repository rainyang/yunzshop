<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppuserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       if (!Schema::hasTable('app_user')) {
            Schema::create('app_user', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->comment('所属公众号');
                $table->string('name', 100)->comment('用户名称');
                $table->text('app_permission');
                $table->integer('owner_uid')->comment('创建该账户的用户id');
                $table->tinyInteger('status')->nullable()->default(1)->comment('用户状态 0禁用1启用');

                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('app_user')) {

            Schema::dropIfExists('app_user');
        }
    }
}
