<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateImsYzUsersProfileMobile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_users_profile')) {
            if (Schema::hasColumn('yz_users_profile', 'mobile')) {
                Schema::table('yz_users_profile', function (Blueprint $table) {
                    $table->dropUnique('yz_users_profile_email_unique');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yz_users_profile');
    }
}
