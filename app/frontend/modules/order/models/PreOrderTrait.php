<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/13
 * Time: 下午4:14
 */

namespace app\frontend\modules\order\models;


use app\common\exceptions\AppException;

trait PreOrderTrait
{
    /**
     * 订单插入数据库,触发订单生成事件
     * @return mixed
     * @throws AppException
     */
    public function generate()
    {
        $this->save();

        $result = $this->push();

        if ($result === false) {

            throw new AppException('订单相关信息保存失败');
        }
        return $this->id;
    }

    /**
     * 递归格式化金额字段
     * @param $attributes
     * @return array
     */
    private function formatAmountAttributes($attributes)
    {
        // 格式化价格字段,将key中带有price,amount的属性,转为保留2位小数的字符串
        $attributes = array_combine(array_keys($attributes), array_map(function ($value, $key) {
            if (is_array($value)) {
                $value = $this->formatAmountAttributes($value);
            } else {
                if (str_contains($key, 'price') || str_contains($key, 'amount')) {
                    $value = sprintf('%.2f', $value);
                }
            }
            return $value;
        }, $attributes, array_keys($attributes)));
        return $attributes;
    }



    /**
     * 统计商品总数
     * @return int
     */
    protected function getGoodsTotal()
    {
        //累加所有商品数量
        $result = $this->orderGoods->sum(function ($aOrderGoods) {
            return $aOrderGoods->total;
        });

        return $result;
    }

    /**
     * 统计订单商品成交金额
     * @return int
     */
    protected function getOrderGoodsPrice()
    {
        return $this->goods_price = $this->orderGoods->getPrice();
    }

    /**
     * 统计订单商品原价
     * @return int
     */
    protected function getGoodsPrice()
    {
        return $this->orderGoods->getGoodsPrice();
    }
}