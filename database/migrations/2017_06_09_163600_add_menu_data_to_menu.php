<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMenuDataToMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_menu')) {
            DB::delete('delete from ims_yz_menu where id in (97,98,107,108,109,110)');
            if(!\app\common\models\Menu::whereIn('id',[97,98,107,108,109,110])->count()){
                $result = DB::statement('INSERT INTO `ims_yz_menu` (`id`, `name`, `item`, `url`, `url_params`, `permit`, `menu`, `icon`, `parent_id`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(97, \'退换货订单\', \'refund_list_refund\', \'refund.list.refund\', \'\', 1, 1, \'fa-circle-o\', 28, 6, 1, 1492170755, 1496897315, NULL),
	(98, \'已退款\', \'refund_list_refunded\', \'refund.list.refunded\', \'\', 1, 1, \'fa-circle-o\', 28, 7, 1, 1492438967, 1496897342, NULL),
	(107, \'仅退款\', \'refund_list_refundMoney\', \'refund.list.refundMoney\', \'\', 1, 1, \'fa-circle-o\', 97, 2, 1, 1493967747, 1496904801, NULL),
	(108, \'退货退款\', \'refund_list_returnGoods\', \'refund.list.returnGoods\', \'\', 1, 1, \'fa-circle-o\', 97, 3, 1, 1493967852, 1496904881, NULL),
	(109, \'换货\', \'refund_list_exchangeGoods\', \'refund.list.exchangeGoods\', \'\', 1, 1, \'fa-circle-o\', 97, 4, 1, 1493967934, 1496904894, NULL),
	(110, \'全部\', \'refund_list_refund_all\', \'refund.list.refund\', \'\', 1, 1, \'fa-circle-o\', 97, 1, 1, 1493967747, 1496904912, NULL);
');
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
        //
    }
}
