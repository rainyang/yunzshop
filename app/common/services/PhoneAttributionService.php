<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/18
 * Time: 20:17
 */

namespace app\common\services;


class PhoneAttributionService
{
    public static function getPhoneApi($mobile)
    {
//        $url = "https://cx.shouji.360.cn/phonearea.php?number=18520632247";  //360接口
//        $url = "https://www.iteblog.com/api/mobile.php?mobile=18519101034";  //ITEBLOG接口

        return "https://cx.shouji.360.cn/phonearea.php?number=".$mobile;
    }
}