<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/22
 * Time: 上午11:52
 */

namespace app\common\services\member;


use app\backend\modules\member\models\Member;
use app\common\models\MemberShopInfo;

class MemberRelation
{
    public function createParentOfMember()
    {
        $memberInfo = new MemberShopInfo();

        //$data = $memberInfo->getTreeAllNodes();

        $data = $memberInfo->getDescendants(65);

        dd($data);
    }
}