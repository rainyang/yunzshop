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
    public $goodsId = '';

    public function run()
    {
        $privilege = new Privilege();
        if ($this->goodsId && Privilege::getInfo($this->goodsId)) {
            $privilege = Privilege::getInfo($this->goodsId);
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