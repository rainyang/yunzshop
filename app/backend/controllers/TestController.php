<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 18/04/2017
 * Time: 15:12
 */

namespace app\backend\controllers;

use app\common\components\BaseController;
use app\common\models\Member;
use app\common\models\Order;
use app\common\models\statusFlow\ModelHasFlow;
use app\common\models\statusFlow\Flow;
use app\common\services\MessageService;
use app\frontend\modules\member\models\SubMemberModel;
use app\frontend\modules\order\services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestController extends BaseController
{
    //public $transactionActions = ['*'];
    public function a(){
        DB::select("delete from ims_migrations where migration = '2018_06_12_140403_create_status_flow_table'");
        DB::select("delete from ims_migrations where migration = '2018_06_13_140403_add_data_to_status_flow_table'");
        exit;
    }
    public function index()
    {

        if (Schema::hasTable('yz_process')) {
            Schema::dropIfExists('yz_process');
        }
        if (Schema::hasTable('yz_status')) {
            Schema::dropIfExists('yz_status');
        }
        if (Schema::hasTable('yz_flow_state')) {
            Schema::dropIfExists('yz_flow_state');

        }
        if (Schema::hasTable('yz_state')) {
            Schema::dropIfExists('yz_state');

        }
        if (Schema::hasTable('yz_flow')) {
            Schema::dropIfExists('yz_flow');

        }
        exit;
        //$statusFlow = StatusFlow::find(3);
        $order = Order::first();
        /**
         * @var Order $order
         */
        dump($order->flow());
        dump($order->flow()->flow);
//        if ($order->statusFlows->isEmpty()) {
//            $order->statusFlows()->save($statusFlow);
//
//        }
//        dump($order->statusFlows);
//        dump(ModelHasStatusFlow::get());


    }

    public function op_database()
    {
        $sub_data = array(
            'member_id' => 999,
            'uniacid' => 5,
            'group_id' => 0,
            'level_id' => 0,
        );

        SubMemberModel::insertData($sub_data);

        if (SubMemberModel::insertData($sub_data)) {
            echo 'ok';
        } else {
            echo 'ko';
        }

    }

    public function notice()
    {
        $teamDividendNotice = \Setting::get('plugin.team_dividend');

        $member = Member::getMemberById(\YunShop::app()->getMemberId());

        if ($teamDividendNotice['template_id']) {
            $message = $teamDividendNotice['team_agent'];
            $message = str_replace('[昵称]', $member->nickname, $message);
            $message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
            $message = str_replace('[团队等级]', '一级', $message);

            $msg = [
                "first" => '您好',
                "keyword1" => "成为团队代理通知",
                "keyword2" => $message,
                "remark" => "",
            ];
            echo '<pre>';
            print_r($msg);
            MessageService::notice($teamDividendNotice['template_id'], $msg, 'oNnNJwqQwIWjAoYiYfdnfiPuFV9Y');

        }
        return;
    }
}