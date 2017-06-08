<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午3:48
 */

namespace app\common\events\member;


use app\common\events\Event;
use app\backend\modules\member\models\Member;

class MemberRelationEvent extends Event
{
<<<<<<< HEAD
    protected $mid;

=======
>>>>>>> b81d83f8cad2ea730c79ff317c09610b4476387e
    protected $user;

    public function __construct(Member $model)
    {
        $this->user = $model;
    }

    public function getMemberModel()
    {
        return $this->user;
    }
}