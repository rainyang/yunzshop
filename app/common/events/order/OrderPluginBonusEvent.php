<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/9/20
 * Time: 上午10:31
 */

namespace app\common\events\order;


use app\common\events\Event;

abstract class OrderPluginBonusEvent extends Event
{
    protected $pluginBonusModel;

    public function __construct($pluginBonusModel)
    {
        $this->pluginBonusModel = $pluginBonusModel;
    }

    public function getData()
    {
        return $this->pluginBonusModel;
    }
}