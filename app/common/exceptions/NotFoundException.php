<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/28
 * Time: 下午4:36
 */

namespace app\common\exceptions;

use Exception;


class NotFoundException extends Exception
{
    public function getStatusCode()
    {
        return 404;
    }

    public function getHeaders()
    {
        return [];
    }
}