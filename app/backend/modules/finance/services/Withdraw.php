<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/11
 * Time: 下午3:43
 */

namespace app\backend\modules\finance\services;


use app\common\traits\ValidatorTrait;

class Withdraw
{
    use ValidatorTrait;


    public function rules()
    {
        return [];
    }
}