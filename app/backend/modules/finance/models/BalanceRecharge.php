<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/19
 * Time: 下午3:56
 */

namespace app\backend\modules\finance\models;


use app\common\traits\ValidatorTrait;

class BalanceRecharge
{
    use ValidatorTrait;

    public function rules()
    {
        return [
            'enough'    => 'numeric',
            'give'      => 'numeric',
        ];
    }

    public function atributeNames()
    {
        return [
            'enough'    => "满足金额值",
            'give'      => "赠送金额",
        ];
    }

}
