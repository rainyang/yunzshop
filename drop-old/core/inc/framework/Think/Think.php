<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Think;
require_once __CORE_PATH__.'/inc/framework/functions.php';
define('LIB_PATH',__CORE_PATH__.'/inc/framework/');
define('EXT','.php');

/**
 * ThinkPHP 引导类
 */
class Think
{
    static public function start()
    {
        spl_autoload_register('Think\Think::autoload');
        //dump(C());
        C(load_config(LIB_PATH.'/config/convention.php'));
        C(load_config(LIB_PATH.'/config/config.php'));
    }
    /**
     * 类库自动加载
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class) {
        if(false !== strpos($class,'\\')){
            $name           =   strstr($class, '\\', true);
            if(in_array($name,array('Think')) || is_dir(LIB_PATH.$name)){
                // Library目录下面的命名空间自动定位
                $path       =   LIB_PATH;
            }else{
                // 检测自定义命名空间 否则就以模块为命名空间
                $namespace  =   C('AUTOLOAD_NAMESPACE');
                $path       =   isset($namespace[$name])? dirname($namespace[$name]).'/' : APP_PATH;
            }
            $filename       =   $path . str_replace('\\', '/', $class) . EXT;
            if(is_file($filename)) {
                // Win环境下面严格区分大小写
                if (IS_WIN && false === strpos(str_replace('/', '\\', realpath($filename)), $class . EXT)){
                    return ;
                }
                include $filename;
            }
        }
        return false;
    }
    /**
     * 添加和获取页面Trace记录
     * @param string $value 变量
     * @param string $label 标签
     * @param string $level 日志级别(或者页面Trace的选项卡)
     * @param boolean $record 是否记录日志
     * @return void|array
     */
    static public function trace($value='[think]',$label='',$level='DEBUG',$record=false) {
        static $_trace =  array();
        if('[think]' === $value){ // 获取trace信息
            return $_trace;
        }else{
            $info   =   ($label?$label.':':'').print_r($value,true);
            $level  =   strtoupper($level);

            if((defined('IS_AJAX') && IS_AJAX) || !C('SHOW_PAGE_TRACE')  || $record) {
                Log::record($info,$level,$record);
            }else{
                if(!isset($_trace[$level]) || count($_trace[$level])>C('TRACE_MAX_RECORD')) {
                    $_trace[$level] =   array();
                }
                $_trace[$level][]   =   $info;
            }
        }
    }
}