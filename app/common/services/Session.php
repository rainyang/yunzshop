<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/29
 * Time: 下午2:38
 */

namespace app\common\services;

/**
 * Session控制类
 */
class Session
{

    const PREFIX = 'yunzshop_';

    /**
     * 设置session
     * @param String $name session name
     * @param Mixed $data session data
     * @param Int $time 超时时间(秒)
     */
    public static function set($name, $data, $time = 10 * 24 * 3600)
    {
        $expire = time() + $time;

        $session_data = array();
        $session_data['data'] = $data;
        $session_data['expire'] = $expire;

        $_SESSION[self::PREFIX . $name] = $session_data;
    }

    /**
     * 读取session
     * @param  String $name session name
     * @return Mixed
     */
    public static function get($name)
    {
        if (isset($_SESSION[self::PREFIX . $name])) {
            if ($_SESSION[self::PREFIX . $name]['expire'] > time()) {
                return $_SESSION[self::PREFIX . $name]['data'];
            } else {
                self::clear($name);
            }
        }
        return false;
    }

    /**
     * 清除session
     * @param  String $name session name
     */
    public static function clear($name)
    {
        unset($_SESSION[self::PREFIX . $name]);
    }

    public static function put($name, $data, $time = 10 * 24 * 3600)
    {
        self::set($name, $data, $time);
    }

    public static function remove($name)
    {
        self::clear($name);
    }
    public static function has($name)
    {
        if(!isset($_SESSION[self::PREFIX . $name])){
            return false;
        }
        if($_SESSION[self::PREFIX . $name]['expire'] <= time()){
            return false;
        }
        return true;
    }
}
