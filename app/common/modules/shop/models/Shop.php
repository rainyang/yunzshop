<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/19
 * Time: 2:45 PM
 */

namespace app\common\modules\shop\models;

use app\common\models\AccountWechats;

/**
 * todo 商城类
 * Class Shop
 * @package app\common\modules\trade\models
 * @property int uniacid
 * @property int weid
 * @property int acid
 * @property AccountWechats account
 */
class Shop extends \app\common\models\Shop
{
    static $current;
    // todo 当前公众号对应的商城
    public static function current()
    {
        if (!isset(self::$current)) {
            self::$current = new self();
            self::$current->init();
        }
        self::$current;
    }

    private function init()
    {

    }
}
