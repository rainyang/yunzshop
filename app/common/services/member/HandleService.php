<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/5/21
 * Time: 下午3:45
 */

namespace app\common\services\member;

use app\common\events\member\ChangeMemberGoldEvent;

class HandleService
{
    public static function trigger($data)
    {
        event(new ChangeMemberGoldEvent($data));
    }
}