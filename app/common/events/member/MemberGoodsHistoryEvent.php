<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/3/29
 * Time: 9:44
 */

namespace app\common\events\member;


use app\common\events\Event;

class MemberGoodsHistoryEvent extends Event
{
    protected $goods_model;

    protected $mark;

    protected $mark_id;

    public function __construct($goods_model, $mark, $mark_id)
    {
        $this->goods_model = $goods_model;
        $this->mark = $mark;
        $this->mark_id = $mark_id;
    }

    public function getGoodsModel()
    {
        return $this->goods_model;
    }

    public function getMark()
    {
        return $this->mark;
    }

    public function getMarkId()
    {
        return $this->mark_id;
    }
}