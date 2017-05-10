<?php

namespace app\backend\modules\coupon\services;

use EasyWeChat\Foundation\Application;

class Message
{
    //发送客服消息
    public static function sendNotice($openid, $news)
    {
        $pay = \Setting::get('shop.pay');
        $options = [
            'app_id'  => $pay['weixin_appid'],
            'secret'  => $pay['weixin_secret'],
        ];
        $app = new Application($options);
        $app->staff->message([$news])->to($openid)->send();
    }
}