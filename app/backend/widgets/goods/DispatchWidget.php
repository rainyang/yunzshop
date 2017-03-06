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
use ClassesWithParents\G;

class DispatchWidget extends Widget
{
    public $goodsId = '';

    public function run()
    {
        $dispatch = new GoodsDispatch();
        if ($this->goodsId && GoodsDispatch::getInfo($this->goodsId)) {
            $dispatch = Dispatch::getInfo($this->goodsId);
        }
        return $this->render('list',
            [
                'dispatch'=> $dispatch,
            ]
        );
    }
}