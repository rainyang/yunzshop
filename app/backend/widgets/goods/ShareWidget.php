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
        $share = new Share();
        if ($this->goodsId && Share::getInfo($this->goodsId)) {
            $share = Share::getInfo($this->goodsId);
        }
        return $this->render('goods/share/share',
            [
                'share'=> $share,
            ]
        );
    }
}