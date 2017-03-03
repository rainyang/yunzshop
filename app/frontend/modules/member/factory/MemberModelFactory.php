<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/26
 * Time: 下午9:20
 */

namespace app\frontend\modules\member\model\factory;


use app\common\models\Member;
use app\frontend\modules\member\model\MemberModel;

class MemberModelFactory
{
    public function getMemberModel($member_id){
        return new MemberModel($this->getFromOrm($member_id));
    }
    private function getFromOrm($member_id){
        return Member::first();
    }
}