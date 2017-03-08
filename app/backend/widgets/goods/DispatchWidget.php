<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 03/03/2017
 * Time: 12:19
 */

namespace app\backend\widgets\goods;


use app\common\components\Widget;
use app\backend\modules\goods\models\GoodsDispatch;
use app\backend\modules\goods\models\Dispatch;

class DispatchWidget extends Widget
{

    public function run()
    {
        $dispatch = new GoodsDispatch();
        if ($this->goods_id && GoodsDispatch::getInfo($this->goods_id)) {
            $dispatch = GoodsDispatch::getInfo($this->goods_id);
        }
        $dispatch_templates = Dispatch::getAll();
        return $this->render('goods/dispatch/goods_dispatch_info',
            [
                'dispatch' => $dispatch,
                'dispatch_templates' => $dispatch_templates
            ]
        );
    }
}