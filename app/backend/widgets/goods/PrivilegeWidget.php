<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 03/03/2017
 * Time: 12:19
 */

namespace app\backend\widgets\goods;


use app\common\components\Widget;
use app\backend\modules\goods\models\Privilege;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\member\models\MemberGroup;


class PrivilegeWidget extends Widget
{

    public function run()
    {
        $privilege = new Privilege();
        if ($this->goods_id && Privilege::getInfo($this->goods_id)) {
            $privilege = Privilege::getInfo($this->goods_id);
            $privilege->show_levels = !empty($privilege->show_levels) ? explode(',', $privilege->show_levels) : '';
            $privilege->buy_levels = !empty($privilege->buy_levels) ? explode(',', $privilege->buy_levels) : '';
            $privilege->show_groups = !empty($privilege->show_groups) ? explode(',', $privilege->show_groups) : '';
            $privilege->buy_groups = !empty($privilege->buy_groups) ? explode(',', $privilege->buy_groups) : '';
        }
        $levels = MemberLevel::getMemberLevelList();
        $groups = MemberGroup::getMemberGroupList();
        return $this->render('goods/privilege/privilege',
            [
                'privilege' => $privilege,
                'levels' => $levels,
                'groups' => $groups
            ]
        );
    }
}