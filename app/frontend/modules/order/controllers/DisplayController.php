<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:11
 */

namespace app\frontend\modules\order\controllers;

use app\frontend\modules\goods\service\GoodsService;
use app\frontend\modules\member\service\MemberService;
use app\frontend\modules\order\service\OrderService;

class DisplayController
{
    public function index(){
        //$member = Member::getMember();
        $member_model = MemberService::getCurrentMemberModel();
        $goods_group_model = GoodsService::getGoodsGroupModel([['goods_id'=>1,'total'=>2]]);
        $order_data = OrderService::getPreCreateOrder($goods_group_model,$member_model);
        dump($order_data);
    }
}