<?php
/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/3/3
 * Time: 上午10:17
 */

namespace app\backend\modules\goods\widgets;


class Discount
{

    public function run()
    {
        //逻辑与 调用model
                    $this->render('discount',[]);
    }
}