<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/11
 * Time: 16:41
 */

namespace app\backend\widgets\goods;


use app\common\components\Widget;
use app\common\models\goods\GoodsPointActivity;

class PointActivityWidget extends Widget
{
    public function run()
    {
        $goods_id = request()->id;
        $data = GoodsPointActivity::getDataByGoodsId($goods_id);

        return view('goods.widgets.point_activity', [
            'data' => $data
        ])->render();
    }
}