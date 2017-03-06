<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 03/03/2017
 * Time: 12:19
 */

namespace app\backend\widgets\goods;


use app\common\components\Widget;
use app\backend\modules\goods\models\Share;

class ShareWidget extends Widget
{
    public $goodsId = '';

    public function run()
    {
<<<<<<< HEAD
        $share = new Share();
        if ($this->goodsId && Share::getInfo($this->goodsId)) {
            $share = Share::getInfo($this->goodsId);
        }
=======
        $shareModel = new Share();
        if ($this->goodsId) {
            $shareModel = Share::getInfo($this->goodsId);
        }

>>>>>>> 25ce58a8762d4fdadcde1c263ba98759dde91c5d
        return $this->render('goods/share/share',
            [
                'share'=> $shareModel,
            ]
        );
    }
}