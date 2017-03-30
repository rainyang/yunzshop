<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/29
 * Time: 下午5:23
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;

/*
 * 余额变动记录表
 *
 * */
class Balance extends BaseModel
{
    public $table = 'yz_balance_log';

    public $timestamps = false;



}