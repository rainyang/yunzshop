<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/27
 * Time: 0:42
 */

namespace app\common\events\member;


use app\common\events\Event;
use app\common\models\MemberShopInfo;

class MemberLevelUpgradeEvent extends Event
{
    protected $memberModel;

    public function __construct(MemberShopInfo $memberModel)
    {
        $this->memberModel = $memberModel;
    }

    public function getMemberModel(){
        return $this->memberModel;
    }

}