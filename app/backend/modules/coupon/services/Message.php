<?php

namespace app\backend\modules\coupon\services;

use EasyWeChat\Foundation\Application;

class Message
{
    //发送客服消息
    /*
     * $notice可以是微信文本回复或者微信图文回复
     * 文本: $message = new Text(['content' => 'Hello']);
     * 图文:
     * $message = new News([
                    'title' => 'your_title',
                    'image' => 'your_image',
                    'description' => 'your_description',
                    'url' => 'your_url',
                ]);
     */
    public static function sendNotice($openid, $notice)
    {
        $pay = \Setting::get('shop.pay');
        $options = [
            'app_id'  => $pay['weixin_appid'],
            'secret'  => $pay['weixin_secret'],
        ];
        $app = new Application($options);
        @$app->staff->message($notice)->to($openid)->send();
    }
}