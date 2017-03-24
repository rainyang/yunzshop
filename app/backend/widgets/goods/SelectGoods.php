<?php
/**
 * Created by PhpStorm.
 * User: Rui
 * Date: 2017/3/20
 * Time: 18:23
 */

namespace app\backend\widgets\goods;

use app\common\components\Widget;


class SelectWidget extends Widget
{

    public function run()
    {
        return view('goods.widgets.select', [
        ])->render();
    }
}