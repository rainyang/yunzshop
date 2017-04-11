<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/11
 * Time: 下午3:37
 */

namespace app\common\events\order;


use app\common\events\Event;
use Illuminate\Support\Collection;

class CreatingOrder extends Event
{
    private $memberCarts;
    public function __construct(Collection $memberCarts)
    {
        $this->memberCarts = $memberCarts;
    }

    public function getMemberCarts(){
        return $this->memberCarts;
    }
}