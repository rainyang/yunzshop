<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/12
 * Time: 下午3:28
 */

namespace app\frontend\modules\finance\deduction;

use app\frontend\modules\coin\deduction\GoodsDeduction;

class PointGoodsDeduction implements GoodsDeduction
{
    private $goodsSale;
    function __construct($goods)
    {
        $this->goodsSale = $goods->hasOneSale;
    }

    public function getGoodsDeductionProportion()
    {
        $maxPointDeduct = $this->goodsSale->max_point_deduct;
        if ($maxPointDeduct === '0') {
            return 0;
        }
        if ($maxPointDeduct) {
            if (strexists($maxPointDeduct, '%')) {
                // todo setting
                $goods_point = floatval(str_replace('%', '', $maxPointDeduct) / 100 * $goodsPrice / $this->point_set['money']);
            } else {
                $goods_point = $maxPointDeduct * $goods_model->total / $this->point_set['money'];
            }
            return $goods_point;
        } else if ($this->point_set['money_max'] > 0 && empty($maxPointDeduct)) {
            $goods_point = $this->point_set['money_max'] / 100 * $goodsPrice / $this->point_set['money'];
            return $goods_point;
        }
    }
}