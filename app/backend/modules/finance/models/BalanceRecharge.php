<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
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
            'enough'    => 'numeric|min:0',
            'give'      => 'numeric|min:0',
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
