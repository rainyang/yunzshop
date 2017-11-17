<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/17
 * Time: 下午6:10
 */

namespace app\common\exceptions;

use Exception;

class ShopException extends Exception
{
    public $redirect = '';
    const UNIACID_NOT_FOUND = -2; // 公众号id不存在

    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
    }
}