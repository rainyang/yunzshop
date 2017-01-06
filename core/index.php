<?php
namespace api;
/**
 * 网站入口页面
 *
 * PHP version 5.x.x
 *
 * @package   前台模块
 * @author    name <xxx@yunzshop.com>
 * @version   v1.0
 */

define('IN_SYS', true);
define("__CORE_PATH__", __DIR__);
define("__VENDOR_PATH__", __DIR__."/../vendor");
define("__BASE_ROOT__", __DIR__ . "/../../..");
//var_dump(get_defined_constants());
require_once __BASE_ROOT__ . '/framework/bootstrap.inc.php';
require_once __BASE_ROOT__ . '/addons/sz_yi/defines.php';
require_once __BASE_ROOT__ . '/addons/sz_yi/core/inc/functions.php';
require_once __BASE_ROOT__ . '/addons/sz_yi/core/inc/plugin/plugin_model.php';
require_once __BASE_ROOT__ . '/addons/sz_yi/core/inc/aes.php';
require_once __BASE_ROOT__ . '/addons/sz_yi/core/inc/core.php';

$_GET['api'] = ltrim($_GET['api'], '/');
class AutoLoader
{
    public function __construct()
    {
        spl_autoload_register(array($this, 'spl_autoload_register'));
    }
    //private $namespace;
    public function spl_autoload_register($full_class_name)
    {
        $namespace = substr($full_class_name, 0, strrpos($full_class_name, '\\'));//最后一个'\'之前 是命名空间
        if(empty($namespace)){
            return false;
        }
        $dir = self::_mapNamespaceToDir($namespace);
        $class_name = $this->_getClassName($full_class_name);
        $full_dir = $this->_formatDir(__CORE_PATH__.'/'."{$dir}/{$class_name}.php");
        if(is_file($full_dir)){
            include $full_dir;
        }
    }
    private function _getClassName($full_class_name){
        $array = explode('\\',$full_class_name);
        $name = array_pop($array);
        return $name;
    }
    private static function _mapNamespaceToDir($namespace)
    {
        $dir = '';
        switch ($namespace) {
            case 'util':
                $dir = __CORE_PATH__ . '/inc/';
                break;
            case 'LeanCloud':
                $dir = __CORE_PATH__ . '/inc/plugin/vendor/';
                break;
            default:
                break;
        }
        $dir .= str_replace('\\','/', $namespace);// 明明空间中的\ 转换为目录的/
        return $dir;
    }
    private function _formatDir($dir){
        $dir = str_replace('\\','/',$dir);
        return $dir;
    }
}

//require_once __API_ROOT__ . '/controller/YZ.class.php';

final class Dispatcher
{
    private $api_name_arr;
    private $api_name;

    public function __construct($api_name)
    {
        $this->api_name = $api_name;
        $this->api_name_arr = explode('/', $api_name);

    }

    public function getControllerPatch()
    {
        $controller_group_name = $this->getControllerGroupName();
        $controller_name = $this->getControllerName();
        return __CORE_PATH__."/".__MODULE_NAME__."/controller/{$controller_group_name}/{$controller_name}.php";
    }

    public function getControllerGroupName()
    {
        $controller_group_name = $this->api_name_arr[0];
        return $controller_group_name;
    }

    public function getControllerName()
    {
        $controller_name = $this->api_name_arr[1];

        return $controller_name;
    }

    public function getMethodName()
    {
        $method_name = isset($this->api_name_arr[2]) ? $this->api_name_arr[2] : 'index';
        return $method_name;
    }
}

final class Run
{
    const CONTROLLER_NAME_SPACE = '\\controller\\';
    private $dispatch;

    public function __construct()
    {
        $this->dispatch = new Dispatcher($_GET['api']);
        $this->run();
    }

    public function run()
    {

        //require_once $this->dispatch->getControllerPatch();
        $controller_full_name = $this->_getControllerFullName();
        $method_name = $this->dispatch->getMethodName();
        $controller_obj = new $controller_full_name;
        $controller_obj->$method_name();
    }

    private function _getControllerFullName()
    {
        $controller_group_name = $this->dispatch->getControllerGroupName();
        $controller_name = $this->dispatch->getControllerName();
        $controller_full_name = implode('',array(
            //__CORE_PATH__,
            __MODULE_NAME__,
            $this::CONTROLLER_NAME_SPACE,
            $controller_group_name.'/'.$controller_name

        ));
        $controller_full_name = $this->_formatNamespace($controller_full_name);
        return $controller_full_name;
    }
    private function _formatNamespace($namespace){
        $namespace = str_replace('/','\\',$namespace);
        return $namespace;
    }
}
require_once __CORE_PATH__ . '/inc/framework/framework.php';
new AutoLoader();

new Run();

