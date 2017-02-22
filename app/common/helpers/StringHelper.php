<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 22/02/2017
 * Time: 15:14
 */

namespace app\common\helpers;


use Illuminate\Support\Str;

class StringHelper extends  Str
{
    /**
     *
     * @param $str
     * @return string
     */
    public static function camelToMiddleLine($str)
    {
        return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '-', $str));
    }

}