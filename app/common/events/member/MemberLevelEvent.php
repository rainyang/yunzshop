<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/14
 * Time: 7:15 PM
 */

namespace app\common\events\member;


use app\common\events\Event;
use app\common\models\MemberShopInfo;

abstract class MemberLevelEvent extends Event
{
    protected $memberModel;

    protected $number;

    public function __construct(MemberShopInfo $memberModel, $number)
    {
        $this->memberModel = $memberModel;
        $this->number = $number;
    }

    public function getMemberModel(){
        return $this->memberModel;
    }

    public function getNumber()
    {
        return $this->number;
    }
}