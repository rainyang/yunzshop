<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/11
 * Time: 下午3:01
 */

namespace app\frontend\modules\coin;

use Illuminate\Container\Container;

class CoinManager extends Container
{
    public function __construct()
    {
        $this->bind('MemberCoinManager', function ($coinManager, $attributes = []) {
            return new MemberCoinManager($attributes);
        });
    }
}