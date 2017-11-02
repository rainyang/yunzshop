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
    const WechatApp = 9;//现金支付
    const AlipayApp = 10;//现金支付
    const STORE_PAY = 11;//门店


    /**
     * 查询所有分类类型
     *
     * @return mixed
     */
    public static function get_pay_type_name($id)
    {
        return self::select('name')->where('id', $id)->value('name');
    }
}