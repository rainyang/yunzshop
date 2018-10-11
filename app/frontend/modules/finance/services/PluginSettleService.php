<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/11
 * Time: 17:16
 */

namespace app\frontend\modules\finance\services;


class PluginSettleService
{
    public static function create($key)
    {
        switch ($key) {
            case 'merchant':
                $class = new \Yunshop\Merchant\services\ReturnFormatService();
                break;
            case 'commission':
                $class = new \Yunshop\Commission\services\ReturnFormatService();
                break;
            case 'areaDividend':
                $class = new \Yunshop\AreaDividend\services\ReturnFormatService();
                break;
            case 'teamDividend':
                $class = new \Yunshop\TeamDividend\services\ReturnFormatService();
                break;
            default:
                $class = null;
        }

        return $class;
    }

}