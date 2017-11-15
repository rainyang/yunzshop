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
use app\common\models\OrderPay;
use app\common\models\PayOrder;
use app\common\services\MessageService;
use app\common\services\WechatPay;
use app\frontend\modules\finance\controllers\IncomeController;
use app\frontend\modules\finance\services\BalanceRechargeResultService;
use app\frontend\modules\member\models\SubMemberModel;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\services\OrderService;

class TestController extends BaseController
{
    public function index()
    {
        $data = [
            'total' => [
                'amounts' => '3033.82',
                'poundage' => '0',
                'pay_way' => 'wechat',
            ],
            'withdrawal' => [
                [
                    'type' => 'Yunshop\ConsumeReturn\common\models\Log',
                    'key_name' => 'consumeReturn',
                    'type_name' => "消费返现",
                    'type_id' => '6920,6965',
                    "income" => '347.82',
                    'poundage' => '0.00',
                    'poundage_rate' => 0,
                    'servicetax' => '34.78',
                    'servicetax_rate' => 10,
                    'can' => 1,
                    'roll_out_limit' => 0,
                    'selected' => 1,
                ],
                [
                    'type' => 'Yunshop\FullReturn\common\models\Log',
                    'key_name' => 'FullReturn',
                    'type_name' => "满额返现",
                    'type_id' => '6934,6939,6940,6941,6942,6943,6944,6947,6948,6957,6958,6959,6960,6961,6962',
                    "income" => '2690.00',
                    'poundage' => '0.00',
                    'poundage_rate' => 0,
                    'servicetax' => '269.00',
                    'servicetax_rate' => 10,
                    'can' => 1,
                    'roll_out_limit' => 0,
                    'selected' => 1,
                ],
            ],

        ];
        $result = (new IncomeController())->saveWithdraw($data);
        dd($result);
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

    public function wx()
    {
        $msg = (new WechatPay())->doWithdraw(369, '3232', 100);

        dd($msg);
    }
}