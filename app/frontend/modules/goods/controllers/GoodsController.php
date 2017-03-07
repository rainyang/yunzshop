<?php
namespace app\frontend\modules\goods\controllers;

use app\common\components\BaseController;
use app\common\models\Goods;
use app\common\models\GoodsSpecItem;

/**
 * Created by PhpStorm.
 * User: Rui
 * Date: 2017/3/3
 * Time: 22:16
 */
class GoodsController extends BaseController
{
    public function getGoodsById()
    {
        $id = \YunShop::request()->goods_id;
        //$goods = new Goods();
        $goodsModel = Goods::with('hasManyParams')->with('hasManySpecs')->find($id);//->getGoodsById(2);

        foreach ($goodsModel->hasManySpecs as &$spec)
        {
            $spec['specitem'] = GoodsSpecItem::where('specid', $spec['id'])->get();
        }

        dd($goodsModel->toJson());
    }
}