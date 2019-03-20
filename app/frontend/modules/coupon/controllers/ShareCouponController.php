<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/20
 * Time: 11:07
 */

namespace app\frontend\modules\coupon\controllers;

use app\common\components\ApiController;
use app\common\events\order\AfterOrderPaidImmediatelyEvent;
use app\common\exceptions\AppException;
use app\common\models\Order;
use app\frontend\models\Member;
use app\frontend\modules\coupon\models\ShoppingShareCoupon;

class ShareCouponController extends ApiController
{
    protected $set;

    protected $share_model;

    protected $member;

    public function preAction()
    {
        parent::preAction();

        $this->getData();

    }

    //分享页面
    public function share()
    {

        //event(new AfterOrderPaidImmediatelyEvent(Order::find(801)));
        //dd(1);
        //拥有推广资格的会员才可以分享
        if ($this->set['share_limit'] == 1) {
            $share_limit = $this->member->yzMember->is_agent ? 1 : 0;
        } else {
            $share_limit = 0;
        }


        if ($this->share_model->isEmpty()) {
            throw new AppException('无分享优惠卷');
        }

        $num = 0;



        $data = [
            'banner' => $this->set['banner'],
            'share_limit' => $share_limit,
            'coupon_num' => $num,
        ];


        $this->successJson('share', $data);

    }

    //领取页面
    public function receive()
    {
        $data = [
            'banner' => $this->set['banner'],
        ];

        $this->successJson('share', $data);
    }


    protected function getData()
    {

        $order_ids = explode(',', rtrim(\YunShop::request()->order_ids, ','));

        $share_model = ShoppingShareCoupon::whereIn('order_id', $order_ids)->get();

        $set = \Setting::get('coupon.shopping_share');
        array_set($set, 'banner', yz_tomedia($set['banner']));


        $this->member = Member::with(['yzMember'])->find(\YunShop::app()->getMemberId());

        $this->set = $set;

        $this->share_model = $share_model;
    }
}