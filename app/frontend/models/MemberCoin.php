<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 下午5:40
 */

namespace app\frontend\models;

abstract class MemberCoin
{
    abstract function getMaxUsablePoint();
}