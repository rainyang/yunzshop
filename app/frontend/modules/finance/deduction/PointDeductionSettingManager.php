<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/16
 * Time: 下午1:49
 */

namespace app\frontend\modules\finance\deduction;

use app\frontend\models\Goods;
use app\frontend\modules\deduction\DeductionSettingCollection;
use app\frontend\modules\deduction\DeductionSettingManagerInterface;
use app\frontend\modules\finance\deduction\deductionSettings\PointGoodsDeductionSetting;
use app\frontend\modules\finance\deduction\deductionSettings\PointShopDeductionSetting;
use Illuminate\Container\Container;

class PointDeductionSettingManager extends Container implements DeductionSettingManagerInterface
{
    public function __construct()
    {
        /**
         * 计积分抵扣商品设置
         */
        $this->bind('goods', function (PointDeductionSettingManager $deductionSettingManager, Goods $goods) {
            return new PointGoodsDeductionSetting($goods);
        });
        /**
         * 积分抵扣商城设置
         */
        $this->bind('shop', function (PointDeductionSettingManager $deductionSettingManager, Goods $goods) {
            return new PointShopDeductionSetting($goods);
        });
    }

    /**
     * @param Goods $goods
     * @return DeductionSettingCollection
     */
    public function getDeductionSettingCollection(Goods $goods){
        $deductionSettingCollection = new DeductionSettingCollection();
        $deductionSettingCollection->push($this->make('goods',$goods));
        $deductionSettingCollection->push($this->make('shop',$goods));
        return $deductionSettingCollection;
    }
}