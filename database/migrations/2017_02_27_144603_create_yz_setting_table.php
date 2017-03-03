<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yz_setting', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('unique_account_id')->comment('统一账号');
            $table->string('group')->default('shop')->comment('分组');
            $table->string('key')->comment('配置key名');
            $table->string('type')->comment('值类型');
            $table->text('value')->comment('值');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('yz_setting');
    }
}
