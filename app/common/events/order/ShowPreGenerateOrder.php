<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/7
 * Time: 下午9:18
 */

namespace app\common\events\order;


use app\common\events\Event;
use Illuminate\Support\Collection;

class ShowPreGenerateOrder extends Event
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