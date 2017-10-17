<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 下午1:49
 */

namespace app\frontend\modules\finance\deduction\deductionSettings;

use app\frontend\models\Goods;

class PointGoodsDeductionSetting implements \app\frontend\modules\deduction\DeductionSettingInterface
{
    private $setting;

    function __construct(Goods $goods)
    {
        $this->setting = $goods->hasOneSale;

    }

    public function isEnableDeductDispatchPrice()
    {
        return false;
    }

    public function isDisable()
    {
        // 商品抵扣设置为0
        return $this->setting->max_point_deduct === '0';
    }

    public function getFixedAmount()
    {
        return str_replace('%', '', $this->setting->max_point_deduct);
    }

    public function getPriceProportion()
    {
        if (!$this->setting->max_point_deduct) {
            return false;
        }

        return str_replace('%', '', $this->setting->max_point_deduct);
    }

    public function getDeductionType()
    {
        if(strexists($this->setting->max_point_deduct, '%')){
            return 'GoodsPriceProportion';
        }
        return 'FixedAmount';
    }
}