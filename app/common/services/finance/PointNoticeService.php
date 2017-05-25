<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/11
 * Time: 下午5:33
 */

namespace app\common\services\finance;

use EasyWeChat\Foundation\Application;
use Setting;

class PointNoticeService
{
    public static function sendNotice($openid, $news)
    {
        $pay = Setting::get('shop.pay');
        $options = [
            'app_id'  => $pay['weixin_appid'],
            'secret'  => $pay['weixin_secret'],
        ];
        $app = new Application($options);
        $app->staff->message([$news])->to($openid)->send();
    }
}