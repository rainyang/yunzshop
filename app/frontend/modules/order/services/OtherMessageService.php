<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/9 下午1:57
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\order\services;


use app\common\services\MessageService as Notice;
use app\frontend\models\Member;

class OtherMessageService
{
    private $orderModel;

    private $memberModel;


    function __construct($orderModel)
    {
        $this->orderModel = $orderModel;
        $this->memberModel = $this->getMemberModel();
    }

    public function created()
    {
        $content = '会员'.$this->getMemberName()."，订单：" . $this->orderModel->order_sn ."，订单金额：".$this->orderModel->price. "已经成功下单（未支付）。";

        $one_msg = [
            "first" => '您好',
            "keyword1" => '一级会员下单成功通知',
            "keyword2" => '一级' . $content,
            "remark" => "",
        ];
        $two_msg = [
            "first" => '您好',
            "keyword1" => '二级会员下单成功通知',
            "keyword2" => '二级' . $content,
            "remark" => "",
        ];
        $this->notice($one_msg,$two_msg);
    }

    public function paid()
    {
        $content = '会员'.$this->getMemberName()."，订单：" . $this->orderModel->order_sn ."，订单金额：".$this->orderModel->price. "已经支付。";

        $one_msg = [
            "first" => '您好',
            "keyword1" => '一级会员订单支付通知',
            "keyword2" => '一级' . $content,
            "remark" => "",
        ];
        $two_msg = [
            "first" => '您好',
            "keyword1" => '二级会员订单支付通知',
            "keyword2" => '二级' . $content,
            "remark" => "",
        ];
        $this->notice($one_msg,$two_msg);
    }

    public function sent()
    {
        $content = '会员'.$this->getMemberName()."，订单：" . $this->orderModel->order_sn ."，订单金额：".$this->orderModel->price. "已经发货。";

        $one_msg = [
            "first" => '您好',
            "keyword1" => '一级会员订单发货通知',
            "keyword2" => '一级' . $content,
            "remark" => "",
        ];
        $two_msg = [
            "first" => '您好',
            "keyword1" => '二级会员订单发货通知',
            "keyword2" => '二级' . $content,
            "remark" => "",
        ];
        $this->notice($one_msg,$two_msg);
    }

    public function received()
    {
        $content = '会员'.$this->getMemberName()."，订单：" . $this->orderModel->order_sn ."，订单金额：".$this->orderModel->price. "已经完成。";

        $one_msg = [
            "first" => '您好',
            "keyword1" => '一级会员订单完成通知',
            "keyword2" => '一级' . $content,
            "remark" => "",
        ];
        $two_msg = [
            "first" => '您好',
            "keyword1" => '二级会员订单完成通知',
            "keyword2" => '二级' . $content,
            "remark" => "",
        ];
        $this->notice($one_msg,$two_msg);
    }

    private function notice($oneMsg,$twoMsg)
    {
        if (!\Setting::get('shop.notice.other_toggle')) {
            return;
        }
        \Log::info('二级消息通知,设置通过');
        $templateId = \Setting::get('shop.notice.task');
        if (!$templateId) {
            return;
        }
        \Log::info('二级消息通知,模版ID通过');
        \Log::info('二级消息通知,模版ID通过'.$this->memberModel->yzMember->parent_id, print_r($this->memberModel->toArray(),true));
        if (isset($this->memberModel->yzMember) && $this->memberModel->yzMember->parent_id) {
            \Log::info('二级消息通知,一级消息通过');
            Notice::notice($templateId,$oneMsg,$this->memberModel->yzMember->parent_id);
        }

        $twoSuperior = $this->getMemberModel($this->memberModel->yzMember->parent_id);
        if (isset($twoSuperior->yzMember) && $twoSuperior->yzMember->parent_id) {
            \Log::info('二级消息通知,二级消息通过');
            Notice::notice($templateId,$twoMsg,$twoSuperior->yzMember->parent_id);
        }
        return;
    }

    private function getMemberName()
    {
        return $this->memberModel->realname ? $this->memberModel->realname : $this->memberModel->nickname;
    }

    private function getMemberModel($memberId = '')
    {
        $memberId = $memberId ? $memberId : $this->orderModel->uid;
        return Member::select('uid','realname','nickname')->with(['yzMember'=>function($query) {
            return $query->select('member_id','parent_id','relation');
        }])->where('uid',$memberId)->first();
    }
}
