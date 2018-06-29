<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 18/04/2017
 * Time: 15:12
 */

namespace app\backend\controllers;

use app\common\models\Callback;
use app\common\models\Migration;
use app\frontend\modules\order\services\OrderService;
use app\common\components\BaseController;
use app\common\models\Member;
use app\common\models\Order;
use app\common\models\OrderPay;
use app\common\models\Flow;
use app\common\services\MessageService;
use app\frontend\modules\member\models\SubMemberModel;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use SuperClosure\SerializableClosure;

class TestController extends BaseController
{
    public function d()
    {
        Order::find(1432)->pay_type_id = 5;
        dd(111);
        exit;

        \Log::useDailyFiles(storage_path().'/logs/test/session.log');
        \Log::debug('1',1);
        echo 1;exit;
        if (Schema::hasTable('yz_order_pay')) {
            Schema::table('yz_order_pay', function (Blueprint $table) {
                if (Schema::hasColumn('yz_order_pay', 'pay_time')) {
                    $table->integer('pay_time')->nullable()->change();
                    $table->integer('refund_time')->nullable()->change();
                }
            });
            DB::select('update '.app('db')->getTablePrefix().'yz_order_pay set refund_time = null where refund_time = 0');
            DB::select('update '.app('db')->getTablePrefix().'yz_order_pay set pay_time = null where pay_time = 0');
        }
    }

    public function c()
    {
        if (Schema::hasTable('yz_containers')) {
            Schema::dropIfExists('yz_containers');
        }
        if (Schema::hasTable('yz_container_binds')) {
            Schema::dropIfExists('yz_container_binds');
        }
        DB::select("delete from ims_migrations where migration = '2018_06_20_103112_create_manager_table'");

    }

    //public $transactionActions = ['*'];
    public function a()
    {
        $id = Migration::where('migration','2018_06_18_140403_add_remittance_audit_to_status_flow_table')->value('id');
        Migration::where('id','>',$id)->delete();
        $this->b();
        exit;
    }

    public function b()
    {

        if (Schema::hasTable('yz_remittance_record')) {
            Schema::dropIfExists('yz_remittance_record');
        }
        if (Schema::hasTable('yz_process')) {
            Schema::dropIfExists('yz_process');
        }
        if (Schema::hasTable('yz_status')) {
            Schema::dropIfExists('yz_status');
        }
        if (Schema::hasTable('yz_flow')) {
            Schema::dropIfExists('yz_flow');

        }
        exit;
    }

    public function index()
    {
        dump(\app\frontend\models\OrderPay::find(1420));
        dump(\app\frontend\models\OrderPay::find(1420)->payType);
        exit;

        $orderPay = OrderPay::find(1432);

        /**
         * @var OrderPay $orderPay
         */
        $flow = Flow::where('code', RemittanceFlow::class)->first();
        $orderPay->flows()->save($flow);

        $a = $orderPay->currentProcess();
        dd($a);

        exit;


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