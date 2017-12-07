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
    //todo 可以删除此文件，验证规则已经转移到 BalanceSetController 2017-12-05 LYT:995265288
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
