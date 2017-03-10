<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/9
 * Time: 上午9:25
 */

namespace app\frontend\modules\goods\services\models;

use app\common\events\OrderGoodsPriceWascalculated;
use Illuminate\Contracts\Queue\ShouldQueue;

class RealGoodsDispatch implements ShouldQueue
{
    private $_order_goods_model;
    //todo 待实现
    public function isRealGoods()
    {
        return true;
    }
    //todo 待实现
    public function getDispatchPrice()
    {
        return 12;
    }

    public function handle(OrderGoodsPriceWascalculated $even)
    {

        $this->_order_goods_model = $even->getOrderGoodsModel();
        if (!$this->isRealGoods()) {
            return;
        }
        $this->_order_goods_model->setDispatchPrice($this->getDispatchPrice());
        //dd($this->_order_goods_model);
        return;
    }

}