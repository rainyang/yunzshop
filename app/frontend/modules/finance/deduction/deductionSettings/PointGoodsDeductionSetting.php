<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 下午1:49
 */

namespace app\frontend\modules\finance\deduction\deductionSettings;

use app\frontend\models\Goods;
use app\frontend\modules\deduction\DeductionSettingInterface;

class PointGoodsDeductionSetting implements DeductionSettingInterface
{
    public function getWeight()
    {
        return 10;
    }

    /**
     * @var \app\frontend\models\goods\Sale
     */
    private $setting;

    function __construct(Goods $goods)
    {
        $this->setting = $goods->hasOneSale;

    }

    public function isEnableDeductDispatchPrice()
    {
        return \Setting::get('point.set.point_freight');
    }

    public function isDisable()
    {
        // 商品抵扣设置为0,则商品不参与抵扣
        return $this->setting->max_point_deduct === '0';
    }

    public function getFixedAmount()
    {
        return str_replace('%', '', $this->setting->max_point_deduct) ?: false;
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
        // 商品抵扣设置为空,则商品未设置独立抵扣
        if($this->setting->max_point_deduct === ''){
            return false;
        }
        if(strexists($this->setting->max_point_deduct, '%')){
            return 'GoodsPriceProportion';
        }
        return 'FixedAmount';
    }
}