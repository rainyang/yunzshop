<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_containers')) {
            Schema::create('yz_containers', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name');
                $table->string('key');
                $table->text('class');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();

            });
            if (!Schema::hasTable('yz_container_binds')) {
                Schema::create('yz_container_binds', function (Blueprint $table) {
                    $table->integer('id', true);
                    $table->integer('containers_id');
                    $table->string('name');
                    $table->string('key');
                    $table->text('class');
                    $table->tinyInteger('is_shared')->default(0);
                    $table->integer('created_at')->nullable();
                    $table->integer('updated_at')->nullable();
                    $table->integer('deleted_at')->nullable();
                    $table->foreign('containers_id')
                        ->references('id')
                        ->on('yz_containers')
                        ->onDelete('cascade');
                });
            }
        }
//        collect([
//            [
//                'name' => '状态',
//                'key' => 'status',
//                'class' => \app\common\modules\status\StatusContainer::class,
//                'is_shared' => '1',
//                'binds' => [
//                    [
//                        'name' => '转账待收款',
//                        'key' => 'RemittanceWaitReceipt',
//                        'class' => \app\common\modules\payType\remittance\models\status\RemittanceWaitReceipt::class,
//                    ]
//                ]
//            ]
//        ])->each(function ($data) {
//            \app\common\models\Containers::create($data)->binds()->saveMany($data['binds']);
//        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('yz_containers')) {
            Schema::dropIfExists('yz_containers');
        }
        if (Schema::hasTable('yz_container_binds')) {
            Schema::dropIfExists('yz_container_binds');
        }
    }
}
