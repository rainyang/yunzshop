<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/25 下午10:32
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services\finance;


use app\common\models\Member;
use app\common\services\MessageService;
use Illuminate\Database\Eloquent\Model;

class BalanceNoticeService
{
    public static function withdrawSubmitNotice(Model $withdrawModel)
    {
        $msg       = array(
            'first' => array(
                'value' => "提现申请已经成功提交!",
                "color" => "#4a5077"
            ),
            'money' => array(
                'title' => '提现金额',
                'value' => '￥' . $withdrawModel->amounts . '元',
                "color" => "#4a5077"
            ),
            'timet' => array(
                'title' => '提现时间',
                'value' => $withdrawModel->created_at->toDateTimeString(),
                "color" => "#4a5077"
            ),
            'remark' => array(
                'value' => "\r\n请等待我们的审核并打款！",
                "color" => "#4a5077"
            )
        );
        $template_id = \Setting::get('shop.notice.withdraw_submit');
        static::notice($template_id,$msg,$withdrawModel->member_id);
    }

    public static function withdrawSuccessNotice(Model $withdrawModel)
    {
        $msg       = array(
            'first' => array(
                'value' => "恭喜您成功提现!",
                "color" => "#4a5077"
            ),
            'money' => array(
                'title' => '提现金额',
                'value' => '￥' . $withdrawModel->amounts . '元',
                "color" => "#4a5077"
            ),
            'timet' => array(
                'title' => '提现时间',
                'value' => $withdrawModel->pay_at->toDateTimeString(),
                "color" => "#4a5077"
            ),
            'remark' => array(
                'value' => "\r\n感谢您的支持！",
                "color" => "#4a5077"
            )
        );
        $template_id = \Setting::get('shop.notice.withdraw_submit');
        static::notice($template_id,$msg,$withdrawModel->member_id);
    }

    public static function withdrawFailureNotice(Model $withdrawModel)
    {
        $msg       = array(
            'first' => array(
                'value' => "抱歉，提现申请审核失败!",
                "color" => "#4a5077"
            ),
            'money' => array(
                'title' => '提现金额',
                'value' => '￥' . $withdrawModel->amounts . '元',
                "color" => "#4a5077"
            ),
            'timet' => array(
                'title' => '提现时间',
                'value' => $withdrawModel->audit_at->toDateTimeString(),
                "color" => "#4a5077"
            ),
            'remark' => array(
                'value' => "\r\n有疑问请联系客服，谢谢您的支持！",
                "color" => "#4a5077"
            )
        );
        $template_id = \Setting::get('shop.notice.withdraw_submit');
        static::notice($template_id,$msg,$withdrawModel->member_id);
    }

    public static function notice($templateId,$msg,$memberId)
    {
        if (!$templateId) {
            return ;
        }
        if (!$memberId) {
            return ;
        }
        $memberModel = Member::ofUid($memberId)->with('hasOneFans')->first();
        if (!$memberModel) {
            return ;
        }
        if (isset($memberModel->hasOneFans) && !empty($memberModel->hasOneFans->openid) && $memberModel->hasOneFans->follow) {
            MessageService::notice($templateId,$msg,$memberModel->hasOneFans->openid);
        }

    }



}
