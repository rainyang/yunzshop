<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzAdminUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_admin_user')) {
            Schema::create('yz_admin_user', function (Blueprint $table) {
                $table->increments('id');
                $table->string('username')->comment('用户名');
                $table->string('password')->comment('密码');
                $table->tinyInteger('status')->default(0)->comment('状态(0:有效; 1:已过期; 2:已禁用)');
                $table->string('phone', 11)->comment('手机号');
                $table->text('remarks')->nullable()->comment('备注');
                $table->integer('application_id')->nullable()->comment('平台id');
                $table->integer('power_id')->nullable()->comment('权限id');
                $table->integer('application_number')->default(0)->comment('平台数量(0:代表不允许创建)');
                $table->integer('effective_time')->default(0)->comment('有效期(0:永久有效)');
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
        Schema::dropIfExists('yz_admin_user');
    }
}
