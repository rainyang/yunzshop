<?php
namespace app\backend\modules\goods\services;

/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/27
 * Time: 上午9:18
 */
class PrivilegeService
{
    /**
     * 数组转换成字符串
     * @param array $array
     * @return string
     */
    public function arrayToString($array)
    {
        if (empty($array)) {
            return $array;
        } else {
            return implode(',', $array);
        }

    }

    /**
     * 字符串转换成数组
     * @param string $string
     * @return array
     */
    public function stringToArray($string)
    {
        if (empty($string)) {
            return $string;
        } else {
            return explode(',', $string);
        }
    }
}