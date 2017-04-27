<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/28
 * Time: 上午11:32
 */

namespace app\common\models;


class PayType extends BaseModel
{
    public $table = 'yz_pay_type';
    const UnPaid = 0;//未支付
    const WECHAT_PAY = 1;//微信
    const ALIPAY = 2;//支付宝
    const CREDIT = 3;//余额支付
    public static function defaultTypeName(){
        $result = self::find(PayType::UnPaid);
        if(isset($result)){
            return $result->name;
        }
        return '数据有误';
    }
}