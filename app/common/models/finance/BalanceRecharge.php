<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/1
 * Time: 上午11:14
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;

/*
 * 余额充值记录数据表
 *
 * */
class BalanceRecharge extends BaseModel
{
    public $table = 'yz_balance_recharge';

    //public $timestamps = false;

    protected $guarded = [''];

    /*
     * 通过主键ID获取记录
     *
     * @params int $rechargeId
     *
     * @return object
     *
     * @Autor yitian */
    public static function getRecordById($rechargeId)
    {
        return self::uniacid()->where('id',$rechargeId)->first();
    }

}