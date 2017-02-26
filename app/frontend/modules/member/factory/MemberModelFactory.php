<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/26
 * Time: 下午9:20
 */

namespace app\frontend\modules\member\model\factory;


use app\api\model\Member;
use app\frontend\modules\member\model\MemberModel;

class MemberModelFactory
{
    public function getMemberModel(){
        return new MemberModel($this->getFromOrm());
    }
    private function getFromOrm(){
        return Member::first();
    }
}