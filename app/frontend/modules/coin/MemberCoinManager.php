<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/11
 * Time: 下午3:01
 */

namespace app\frontend\modules\coin;


use Illuminate\Container\Container;
use Yunshop\Love\Frontend\Models\MemberLove;

class MemberCoinManager extends Container
{
    public function __construct()
    {
        $this->bind('love', function ($memberCoinManager, $attributes = []) {
            return new MemberLove($attributes);
        });
    }
}