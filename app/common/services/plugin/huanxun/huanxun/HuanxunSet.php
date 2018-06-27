<?php

namespace app\common\services\plugin\huanxun;

use app\common\components\BaseController;

/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2018/3/19
*/
class HuanxunSet extends BaseController
{
    protected $huanxun;
        

    function __construct()
    {
        parent::__construct();

        $this->huanxun =  \Setting::get('plugin.huanxun_set');
    }

    //是否开启
    public static function whetherEnabled()
    {
        $huanxun = \Setting::get('plugin.huanxun_set');

        if (app('plugins')->isEnabled('huanxun')) {
            if ($huanxun['switch']) {
                return $huanxun['switch'];
            }
        }

        return 0;
    }
}