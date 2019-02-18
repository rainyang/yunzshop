<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/18
 * Time: 下午3:01
 */

namespace app\common\helpers;


class DBHelper
{
    public static function tablename($table)
    {
        if (env('APP_Framework') == 'platform') {
            return "`" . env('DB_PREFIX') . $table . "`";
        } else {
            if(empty($GLOBALS['_W']['config']['db']['master'])) {
                return "`{$GLOBALS['_W']['config']['db']['tablepre']}{$table}`";
            }
            return "`{$GLOBALS['_W']['config']['db']['master']['tablepre']}{$table}`";
        }
    }
}