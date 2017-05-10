<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLevelLimitToImsYzCoupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yz_coupon', function (Blueprint $table) {
            if (!Schema::hasColumn('yz_coupon', 'level_limit')) {
                $table->integer('level_limit')->nullable()->after('get_type')->comment('可领取该优惠券的会员等级限制(如果为3, 表示1,2,3等级可以领取; -1表示所有会员都可领取;)');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yz_coupon', function (Blueprint $table) {
            $table->dropColumn('level_limit');
        });
    }
}
