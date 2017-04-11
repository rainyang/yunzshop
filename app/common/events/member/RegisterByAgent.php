<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/4/10
 * Time: 下午3:13
 */

namespace app\common\events\member;


use app\common\events\Event;

class RegisterByAgent extends Event
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}