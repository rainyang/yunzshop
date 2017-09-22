<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/17
 * Time: 下午6:10
 */

namespace app\common\exceptions;

class PaymentException extends ShopException
{
    const PAY_PASSWORD_SETTING_CLOSED = 20001; // 商城支付密码设置未开启
    const PAY_PASSWORD_NOT_SET = 20002; // 用户未设置支付密码
    const PAY_PASSWORD_ERROR = 20003; // 支付密码错误
    public function passwordError(){
        $this->code = self::PAY_PASSWORD_ERROR;
        $this->message = '支付密码错误';
    }

    public function settingClose()
    {
        $this->code = self::PAY_PASSWORD_SETTING_CLOSED;
        $this->message = '商城支付密码设置未开启';
    }
}