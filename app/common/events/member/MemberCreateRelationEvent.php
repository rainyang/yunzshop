<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/30
 * Time: 下午4:17
 */

namespace app\common\events\member;



use app\common\events\Event;

class MemberCreateRelationEvent extends Event
{
    protected $uid;
    protected $parent_id;

    public function __construct($uid, $parent_id)
    {
        $this->uid       = $uid;
        $this->parent_id = $parent_id;
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function getParentId()
    {
        return $this->parent_id;
    }
}