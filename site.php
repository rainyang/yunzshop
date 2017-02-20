<?php
/**
 * 芸众商城模块微站定义
 *
 * @url http://bbs.yunzshop.com/
 */
defined('IN_IA') or exit('Access Denied');

require_once IA_ROOT . '/addons/sz_yi/version.php';
require_once IA_ROOT . '/addons/sz_yi/defines.php';
require_once SZ_YI_INC . 'functions.php';
require_once SZ_YI_INC . 'core.php';
require_once SZ_YI_INC . 'plugin/plugin.php';
require_once SZ_YI_INC . 'plugin/plugin_model.php';
if($_GET['new']==1){
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
            $class_name = ucfirst($this->_getClassName($full_class_name));
            $full_dir = $this->_formatDir(SZ_YI_PATH."{$dir}/{$class_name}.php");
            ddump($full_dir);
            ddump(is_file($full_dir));
            if(is_file($full_dir)){
                ddump($full_dir);

                include_once $full_dir;
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
                    $dir = SZ_YI_INC;
                    break;
                case 'LeanCloud':
                    $dir = SZ_YI_INC . 'plugin/vendor/';
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
            return __CORE_PATH__.__APP_NAME__."/controller/{$controller_group_name}/{$controller_name}.php";
        }

        public function getControllerGroupName()
        {
            $controller_group_name = $this->api_name_arr[0].'/'.$this->api_name_arr[1];
            return $controller_group_name;
        }

        public function getControllerName()
        {
            $controller_name = ucfirst($this->api_name_arr[2]);

            return $controller_name;
        }

        public function getMethodName()
        {
            $method_name = isset($this->api_name_arr[3]) ? $this->api_name_arr[3] : 'index';
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
            /*ddump(array(
                //__CORE_PATH__,
                __APP_NAME__,
                $this::CONTROLLER_NAME_SPACE,
                $controller_group_name.'/'.$controller_name

            ));
            ddump(
                $controller_name
            );exit;*/
            $controller_full_name = implode('',array(
                //__CORE_PATH__,
                __APP_NAME__,
                $this::CONTROLLER_NAME_SPACE,
                $controller_group_name.'/'.$controller_name

            ));
            $controller_full_name = $this->_formatNamespace($controller_full_name);
            //ddump($controller_full_name);exit;
            return $controller_full_name;
        }
        private function _formatNamespace($namespace){
            $namespace = str_replace('/','\\',$namespace);
            return $namespace;
        }
    }
    new AutoLoader();
    new Run();
    exit;
}

class Sz_yiModuleSite extends Core
{

    public function __construct()
    {
        parent::__construct();
    }

    //插件web入口  
    public function doWebPlugin()
    {
        global $_W, $_GPC;
        require_once SZ_YI_INC . "plugin/plugin.php";
        $plugins = m('plugin')->getAll();
        $p = $_GPC['p'];
        $file = SZ_YI_PLUGIN . $p . "/web.php";
        if (!is_file($file)) {
            message('未找到插件 ' . $plugins[$p] . ' 入口方法');
        }
        require $file;
        $pluginClass = ucfirst($p) . "Web";
        $plug = new $pluginClass($p);
        $method = strtolower($_GPC['method']);
        if (empty($method)) {
            $plug->index();
            exit;
        }
        if (method_exists($plug, $method)) {
            $plug->$method();
            exit;
        }
        trigger_error('Plugin Web Method ' . $method . ' not Found!');
    }

    //插件app入口
    public function doMobilePlugin()
    {
        global $_W, $_GPC;
        require_once SZ_YI_INC . "plugin/plugin.php";
        $plugins = m('plugin')->getAll();
        $p = $_GPC['p'];
        $file = SZ_YI_PLUGIN . $p . "/mobile.php";

        if (!is_file($file)) {
            message('未找到插件 ' . $plugins[$p] . ' 入口方法');
        }
        require $file;
        $pluginClass = ucfirst($p) . "Mobile";
        $plug = new $pluginClass($p);
        $method = strtolower($_GPC['method']);
        if (empty($method)) {
            return $plug->index();
        }
        elseif (method_exists($plug, $method)) {

            return $plug->$method();
        }else{
            trigger_error('Plugin Mobile Method ' . $method . ' not Found!');
            exit;
        }
    }

    //购物车入口
    public function doMobileCart()
    {
        return $this->_exec('doMobileShop', 'cart', false);
    }

    //我的收藏入口
    public function doMobileFavorite()
    {
        return $this->_exec('doMobileShop', 'favorite', false);
    }

    //工具
    public function doMobileUtil()
    {
        return $this->_exec(__FUNCTION__, '', false);
    }

    //会员
    public function doMobileMember()
    {
        return $this->_exec(__FUNCTION__, 'center', false);
    }

    //商城
    public function doMobileShop()
    {
        return $this->_exec(__FUNCTION__, 'index', false);
    }

    //订单
    public function doMobileOrder()
    {
        return $this->_exec(__FUNCTION__, 'list', false);
    }

    //会议
    public function doMobileMeet()
    {
        return $this->_exec(__FUNCTION__, 'index', false);
    }

    //餐饮
    public function doMobileRest()
    {
        return $this->_exec(__FUNCTION__, 'index', false);
    }

    //接口
    public function doMobileApi()
    {
        return $this->_exec(__FUNCTION__, 'index', false);
    }
    //直播
    public function doMobileLive()
    {
        return $this->_exec(__FUNCTION__, 'list', false);
    }
    //订单
    //支付成功
    public function payResult($params)
    {
        return m('order')->payResult($params);
    }

    public function getAuthSet()
    {
        global $_W;
        $set = pdo_fetch('select sets from ' . tablename('sz_yi_sysset') . ' order by id asc  limit 1');
        $sets = iunserializer($set['sets']);
        if (is_array($sets)) {
            return is_array($sets['auth']) ? $sets['auth'] : array();
        }
        return array();
    }

    public function doWebAuth()
    {
        return $this->_exec('doWebSysset', 'auth', true);
    }

    public function doWebUpgrade()
    {
        return $this->_exec('doWebSysset', 'upgrade', true);
    }

    //微信管理订单
    public function doMobileWechatOrder()
    {
        return $this->_execFront('doWebOrder', 'list', false);
    }

}
