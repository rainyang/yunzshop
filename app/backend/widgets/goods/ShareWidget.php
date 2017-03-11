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

    public function run()
    {
        $share = new Share();
        if ($this->goods_id && Share::getInfo($this->goods_id)) {
            $share = Share::getInfo($this->goods_id);
        }
        return view('goods.widgets.share', [
            'share'=> $share,
        ])->render();
    }
}