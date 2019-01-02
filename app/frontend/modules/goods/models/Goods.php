<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/2
 * Time: 18:31
 */

namespace app\frontend\modules\goods\models;


use Yunshop\SalesCommission\models\GoodsSalesCommission;

class Goods extends \app\common\models\Goods
{
    public $appends = ['status_name','estimated_commission'];

    public function getEstimatedCommissionAttribute($key)
    {
        return $this->getSalesCommission();
    }

    public function getSalesCommission()
    {
        $set = \Setting::get('plugin.sales-commission');
        if ($set['switch']) {
            $salesCommissionGoods = GoodsSalesCommission::getGoodsByGoodsId($this->id)->first();
            if ($salesCommissionGoods) {
                if ($salesCommissionGoods->has_dividend == '1') {
                    return $salesCommissionGoods->dividend_rate;
                } else {
                    return $set['default_rate'];
                }
            }
        }
    }

}