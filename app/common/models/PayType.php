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
    const ONLINE = 1;//在线支付
    const CREDIT = 2;//余额支付
    public static function defaultTypeName(){
        $result = self::find(PayType::UnPaid);
        if(isset($result)){
            return $result->name;
        }
        return '数据有误';
    }
}