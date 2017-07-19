<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/19 下午4:41
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\traits;


trait CreateOrderSnTrait
{
    public static function createOrderSn($prefix,$field='order_sn',$numeric=FALSE)
    {
        $orderSn = createNo($prefix,$numeric);
        while (1) {
            if (!self::where($field,$orderSn)->first()) {
                break;
            }
            $orderSn = createNo($prefix,$numeric);
        }
        return $orderSn;
    }

}