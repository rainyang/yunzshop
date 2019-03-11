<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUniacidApplication extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       if (!Schema::hasTable('yz_uniacid_app')) {
            Schema::create('yz_uniacid_app', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->index()->nullable();
                $table->string('img')->comment('应用图片');
                $table->string('name', 100)->comment('应用名称');
                $table->string('kind', 100)->nullable()->comment('行业分类');
                $table->string('title', 100)->nullable()->comment('应用标题');
                $table->string('descr')->nullable()->comment('应用描述');
                $table->float('version')->nullable()->comment('应用版本');
                $table->integer('validity_time')->comment('有效期');
                
                $table->tinyInteger('type')->comment('应用类型,1服务号 2订阅号 3企业号 4小程序5 PC应用6 APP应用 7小游戏');
//
                $table->tinyInteger('status')->nullable()->default(1)->comment('应用状态 0禁用1启用');

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
        if (Schema::hasTable('yz_uniacid_app')) {

            Schema::dropIfExists('yz_uniacid_app');
        }
    }
}
