<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/26
 * Time: 下午1:57
 */

namespace app\frontend\modules\orderGoods\price\type;


class OrderGoodsPriceDecorator
{
    protected $orderGoodsPriceDecorator;

    public function __construct(OrderGoodsPriceDecorator $orderGoodsPriceDecorator)
    {
        $this->orderGoodsPriceDecorator = $orderGoodsPriceDecorator;
    }

    function __call($name, $arguments)
    {
        return $this->orderGoodsPriceDecorator->$name(...$arguments);
    }
}