<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 03/03/2017
 * Time: 12:19
 */

namespace app\backend\widgets;


use app\common\components\Widget;
use app\backend\modules\goods\models\Discount;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\member\models\MemberGroup;

class DiscountWidget extends Widget
{
    public $goodsId = '';

    public function run()
    {
        $discounts = Discount::getList($this->goodsId);
        $levels = MemberLevel::getMemberLevelList();
        $groups = MemberGroup::getMemberGroupList();
        return $this->render('goods/discount/discount',
            [
                'discount'=>$discounts,
                'levels' => $levels,
                'groups' => $groups
            ]
        );
    }
}