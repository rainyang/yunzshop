<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 下午3:44
 */

namespace app\frontend\modules\goods\model\type;

use app\frontend\modules\goods\model\GoodsModel;

class Verify
{
    private $goods_model;
    public function __construct(GoodsModel $goods_model)
    {
        $this->goods_model = $goods_model;
    }
    //todo db goods isverify
    public static function enable(GoodsModel $goods_model){
        if($goods_model->getInitialData()['isverify'] == 2){
            return true;
        }
        return false;
    }
    public function getData(){

    }
}