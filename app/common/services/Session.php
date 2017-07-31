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
class Session{

    /**
     * 设置session
     * @param String $name   session name
     * @param Mixed  $data   session data
     * @param Int    $time 超时时间(秒)
     */
    public static function set($name, $data, $time=10 * 24 * 3600){
        $expire = time() + $time;
        @ini_set('session.gc_maxlifetime', $expire);

        $session_data = array();
        $session_data['data'] = $data;
        $session_data['expire'] = $expire;

        $_SESSION[$name] = $session_data;
    }

    /**
     * 读取session
     * @param  String $name  session name
     * @return Mixed
     */
    public static function get($name){
        if(isset($_SESSION[$name])){
            if($_SESSION[$name]['expire']>time()){
                return $_SESSION[$name]['data'];
            }else{
                self::clear($name);
            }
        }
        return false;
    }

    /**
     * 清除session
     * @param  String  $name  session name
     */
    public static function clear($name){
        unset($_SESSION[$name]);
    }

}
