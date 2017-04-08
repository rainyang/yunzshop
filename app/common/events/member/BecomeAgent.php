<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/4/6
 * Time: 下午9:47
 */

namespace app\common\events\member;


use app\common\events\Event;
use app\common\models\MemberShopInfo;

class BecomeAgent extends Event
{
    protected $mid;

    protected $user;

    public function __construct($mid, MemberShopInfo $model)
    {
        $this->mid = $mid;

        $this->user = $model;
    }

    public function getMid()
    {
        return $this->mid;
    }

    public function getMemberModel()
    {
        return $this->user;
    }
}