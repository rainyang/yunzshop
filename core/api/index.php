<?php
/**
 * 网站入口页面
 *
 * PHP version 5.x.x
 *
 * @package   前台模块
 * @author    name <xxx@yunzshop.com>
 * @version   v1.0
 */
namespace api;
define("__API_ROOT__", __DIR__);
define("__BASE_ROOT__", __DIR__ . "/../../../..");
//echo phpinfo();
require_once __BASE_ROOT__ . '/framework/bootstrap.inc.php';
require_once __BASE_ROOT__ . '/addons/sz_yi/defines.php';
require_once __BASE_ROOT__ . '/addons/sz_yi/core/inc/functions.php';
require_once __BASE_ROOT__ . '/addons/sz_yi/core/inc/plugin/plugin_model.php';
require_once __BASE_ROOT__ . '/addons/sz_yi/core/inc/aes.php';
global $_W, $_GPC, $_YZ;

$_GET['api'] = ltrim($_GET['api'], '/');
spl_autoload_register(function ($class_name) {

    $name_space = substr($class_name, 0, strrpos($class_name, '\\'));

    $class_name_parts = explode('\\', $class_name);
    if ($name_space == 'model\api') {
        array_unshift($class_name_parts, __API_ROOT__ . '/..');
        $dir = implode('/', $class_name_parts);
        require $dir . '.php';

    } elseif ($name_space == 'controller\api') {

        //array_unshift($parts,__API_ROOT__.'/'.$_GET['api']);
        //$dir = implode('/',$class_name_parts);
        //require $dir.'.php';
    }
});

require_once __API_ROOT__.'/YZ.class.php';

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

        return __API_ROOT__ . "/{$controller_group_name}/{$controller_name}.php";
    }

    public function getControllerGroupName(){
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
final class Run{
    const CONTROLLER_NAME_SPACE='\\controller\\api\\';
    private $dispatch;
    public function __construct()
    {
        $this->dispatch = new Dispatcher($_GET['api']);
        $this->run();
    }
    public function run(){
        require $this->dispatch->getControllerPatch();
        $controller_full_name = $this->getControllerFullName();
        $method_name = $this->dispatch->getMethodName();
        $controller_obj = new $controller_full_name;
        $controller_obj->$method_name();
    }
    private function getControllerFullName(){
        $controller_group_name = $this->dispatch->getControllerGroupName();
        $controller_name = $this->dispatch->getControllerName();
        $controller_full_name = $this::CONTROLLER_NAME_SPACE."{$controller_group_name}\\{$controller_name}";
        return $controller_full_name;
    }
}
new Run();


//new \controller\api\orderDetail();
//$_YZ = new YZ($_W);
