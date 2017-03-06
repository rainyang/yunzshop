<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/6
 * Time: 上午11:32
 */

namespace app\backend\widgets\goods;

use app\common\components\Widget;
use app\backend\modules\goods\models\Sale;
use app\common\models\Area;

class SaleWidget extends Widget
{
    public $goodsId = '';

    public function run()
    {
        $saleModel = new Sale();
        $parents = Area::getProvinces(0);
        $sale = Sale::getList($this->goodsId);
        $sale = $sale->toArray();
        if ($sale) {
            $saleModel->setRawAttributes($sale);
        }
        return $this->render('goods/sale/sale', [
            'item' => $saleModel,
            'parents' => $parents->toArray()
        ]);
    }
}

