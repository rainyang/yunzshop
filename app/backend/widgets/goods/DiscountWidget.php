<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 03/03/2017
 * Time: 12:19
 */

namespace app\backend\widgets\goods;


use app\common\components\Widget;
use app\backend\modules\goods\models\Discount;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\member\models\MemberGroup;

class DiscountWidget extends Widget
{

    public function run()
    {
        $discounts = new Discount();
        if ($this->goods_id && Discount::getList($this->goods_id)) {
            $discounts = Discount::getList($this->goods_id);
        }
        $levels = MemberLevel::getMemberLevelList();
        $groups = MemberGroup::getMemberGroupList();
        return $this->render('goods/discount/discount',
            [
                'discount' => $discounts,
                'levels' => $levels,
                'groups' => $groups
            ]
        );
    }
}