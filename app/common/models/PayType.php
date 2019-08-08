<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午11:32
 */

namespace app\common\models;

/**
 * Class PayType
 * @package app\common\models
 * @property string code
 * @property string setting_key
 * @property string name
 * @property int need_password
 * @property int id
 */
class PayType extends BaseModel
{
    public $table = 'yz_pay_type';
    const UNPaid = 0;//未支付
    const WECHAT_PAY = 1;//微信
    const ALIPAY = 2;//支付宝
    const CREDIT = 3;//余额支付
    const CASH = 4;//货到付款
    const BACKEND = 5;//后台支付
    const PAY_CLOUD_WEACHAT = 6;//云收银微信
    const PAY_CLOUD_ALIPAY = 7;//云收银支付宝
    const CASH_PAY = 8;//现金支付
    const WechatApp = 9;//微信App支付
    const AlipayApp = 10;//支付宝App支付
    const STORE_PAY = 11;//门店
    const PAY_YUN_WECHAT = 12;//微信-YZ
    const WANMI_Pay = 13;//快捷
    const ANOTHER_Pay = 14;//找人代付
    const PAY_YUN_ALIPAY = 15;//支付宝-YZ
    const REMITTANCE = 16;//转账
    const COD = 17;//货到付款
    const HXQUICK = 18;//环迅快捷支付
    const HXWECHAT = 22;//环迅微商支付
    const YOP = 26;//易宝支付
    const USDTPAY = 27;//USDT支付
    const WECHAT_HJ_PAY = 28;//微信支付-HJ(汇聚)
    const ALIPAY_HJ_PAY = 29;//支付宝支付-HJ(汇聚)
    const PAY_TEAM_DEPOSIT = 30;//预存款支付

    /**
     * 查询所有分类类型
     *
     * @return mixed
     */
    public static function get_pay_type_name($id)
    {
        return self::select('name')->where('id', $id)->value('name');
    }

    public static function fetchPayName()
    {
        return self::select('name')
            ->groupBy('name')
            ->get();
    }


    public static function updateDalance($name_id,$name)
    {
         return self::where('id',$name_id)
             ->update(['name'=>$name]);
    }
    public static function fetchPayType($name)
    {
        return self::where('name', $name)
            ->get();
    }

    public static function payTypeColl()
    {
        $coll = [];
        $pay_names = PayType::fetchPayName();

        if (!$pay_names->isEmpty()) {
            foreach ($pay_names as $item) {
                $pay_types = PayType::fetchPayType($item->name);

                if (!$pay_types->isEmpty()) {
                    foreach ($pay_types as $rows) {
                        $coll[$item->name][] = $rows->id;
                    }
                }
            }
        }

        return $coll;
    }
}