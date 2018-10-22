<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 17:59
 */

namespace app\backend\modules\charts\modules\phone\services;


use Illuminate\Support\Facades\DB;

class PhoneAttributionService
{
    public function phoneStatistics()
    {
        $member = $this->getPhone();
        foreach ($member as $k => $item) {
                $data[$k] = $this->getPhoneApi($item['mobile']);
                $phone[] = file_get_contents($data[$k]);
        }
dd($phone);
        return json_decode($phone);
    }

    public function getPhone()
    {
        $uniacid = \YunShop::app()->uniacid;
        $member_phone = DB::select("select uid,mobile,uniacid from ims_mc_members where uniacid =$uniacid and mobile != ''");

        return $member_phone;
    }

    public function getPhoneApi($mobile)
    {
//        $url = "https://cx.shouji.360.cn/phonearea.php?number=18520632247";  //360接口
//        $url = "https://www.iteblog.com/api/mobile.php?mobile=18519101034";  //ITEBLOG接口

        return "https://cx.shouji.360.cn/phonearea.php?number=".$mobile;
    }
}