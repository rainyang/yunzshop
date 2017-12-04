<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/7
 * Time: 下午3:00
 */

namespace app\common\services\credit;


class ConstService
{

    const OPERATOR_SHOP     = 0;  //操作者 商城

    const OPERATOR_ORDER    = -1; //操作者 订单

    const OPERATOR_MEMBER   = -2; //操作者 会员

    //类型：收入
    const TYPE_INCOME = 1;

    //类型：支出
    const TYPE_EXPENDITURE = 2;

    //充值状态 ：成功
    const STATUS_SUCCESS = 1;

    //充值状态 ：失败
    const STATUS_FAILURE = -1;

    const SOURCE_RECHARGE          = 1; //充值

    const SOURCE_CONSUME           = 2; //消费

    const SOURCE_TRANSFER          = 3; //转让

    const SOURCE_DEDUCTION         = 4; //抵扣

    const SOURCE_AWARD             = 5; //奖励

    const SOURCE_WITHDRAWAL        = 6; //提现

    const SOURCE_INCOME            = 7; //提现至～～

    const SOURCE_CANCEL_DEDUCTION  = 8; //抵扣取消回滚

    const SOURCE_CANCEL_AWARD      = 9; //奖励取消回滚

    const SOURCE_CANCEL_CONSUME    = 10; //消费取消回滚

    const SOURCE_RECHARGE_MINUS    = 11; //后台扣除

    const SOURCE_RECHARGE_CODE    = 92; //充值码充值


    protected static $title = '余额';




    public function __construct($title = '')
    {
        static::$title              = $title ?: static::$title;
    }


    public function sourceComment()
    {
        return [
            self::SOURCE_RECHARGE              => static::$title . '充值',
            self::SOURCE_CONSUME               => static::$title . '消费',
            self::SOURCE_TRANSFER              => static::$title . '转让',
            self::SOURCE_DEDUCTION             => static::$title . '抵扣',
            self::SOURCE_AWARD                 => static::$title . '奖励',
            self::SOURCE_WITHDRAWAL            => static::$title . '提现',
            self::SOURCE_INCOME                => '提现至' . static::$title,
            self::SOURCE_CANCEL_DEDUCTION      => '抵扣取消',
            self::SOURCE_CANCEL_AWARD          => '奖励取消',
            self::SOURCE_CANCEL_CONSUME        => '消费取消',
            self::SOURCE_RECHARGE_MINUS        => '后台扣除',
            self::SOURCE_RECHARGE_CODE         => '充值码充值'
        ];
    }

    public function typeComment()
    {
        return [
            self::TYPE_INCOME                   => '收入',
            self::TYPE_EXPENDITURE              => '支出'
        ];
    }

    public function operatorComment()
    {
        return [
            self::OPERATOR_SHOP                 => '商城操作',
            self::OPERATOR_ORDER               => '会员操作',
            self::OPERATOR_MEMBER               => '订单操作'
        ];
    }
}
