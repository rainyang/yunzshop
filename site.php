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

// 载入composer的autoload文件
include __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule; //如果你不喜欢这个名称，as DB;就好

//商城根目录
define('SHOP_ROOT', dirname(__FILE__));

class YunShop
{
    private static $_req;
    private static $_app;

    public function __construct()
    {

    }

    public static function run()
    {
        /*
       * php错误调度
       */
        if (self::app()->config['setting']['development'] == 1) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }
        /**
         * 连接数据库
         */
        $database = [
            'driver' => 'mysql',
            'host' => self::app()->config['db']['master']['host'],
            'database' => self::app()->config['db']['master']['database'],
            'username' => self::app()->config['db']['master']['username'],
            'password' => self::app()->config['db']['master']['password'],
            'charset' => self::app()->config['db']['master']['charset'],
            'collation' => 'utf8_unicode_ci',
            'prefix' => self::app()->config['db']['master']['tablepre'],
        ];
        $capsule = new Capsule;
        // 创建链接
        $capsule->addConnection($database);
        // 设置全局静态可访问
        $capsule->setAsGlobal();
        // 启动Eloquent
        $capsule->bootEloquent();

        self::parseRoute();
    }

    public static function arrayMerge($a, $b)
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (is_int($k)) {
                    if (isset($res[$k])) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::arrayMerge($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    public static function getConfig()
    {

    }

    public static function getAppNamespace()
    {
        $rootName = 'app';
        if (self::isWeb()) {
            $rootName .= '\\backend';
        }
        if (self::isApp()) {
            $rootName .= '\\frontend';
        }
        return $rootName;
    }

    public static function getAppPath()
    {
        $path = dirname(__FILE__) . '/app';
        if (self::isWeb()) {
            $path .= '/backend';
        }
        if (self::isApp()) {
            $path .= '/frontend';
        }
        return $path;
    }

    public static function isWeb()
    {
        return strpos($_SERVER['PHP_SELF'], '/web/index.php') !== false ? true : false;
    }

    public static function isApp()
    {
        return strpos($_SERVER['PHP_SELF'], '/app/index.php') !== false ? true : false;
    }

    public static function request()
    {
        if (self::$_req !== null) {
            return self::$_req;
        } else {
            return new Request();
        }
    }

    public static function app()
    {
        if (self::$_app !== null) {
            return self::$_app;
        } else {
            return new App();
        }
    }

    /**
     * 解析路由
     *
     * 后台访问  /web/index.php?c=site&a=entry&m=sz_yi&do=xxx&route=module.controller.action
     * 前台      /app/index.php....
     *
     * 多字母的路由用中划线隔开 比如：
     *      TestCacheController
     *          function testClean()
     * 路由写法为   test-cache.test-clean
     *
     */
    public static function parseRoute()
    {
        $routes = explode('.', self::request()->route);

        $path = self::getAppPath();
        $namespace = self::getAppNamespace();
        $action = '';
        $controllerName = '';
        $modules = [];
        if ($routes) {
            $length = count($routes);
            foreach ($routes as $k => $r) {
                $ucFirstRoute = self::getUcfirstName($r);
                $controllerFile = $path . '/controllers/' . $ucFirstRoute . 'Controller.php';

                if (file_exists($controllerFile)) {
                    $namespace .= '\\controllers\\' . $ucFirstRoute . 'Controller';
                    $controllerName = $ucFirstRoute;
                } elseif (is_dir($path .= '/modules/' . $r)) {
                    $namespace .= '\\modules\\' . $r;
                    $modules[] = $r;
                } else {
                    if ($length !== $k + 1) {
                        exit('no found route:' . self::request()->route);
                    }
                    $action = strpos($r, '-') === false ? $r : lcfirst(self::getUcfirstName($r));
                }

            }
        }

        if (!class_exists($namespace)) {
            exit(" no exists class: " . $namespace);
        }
        if (empty($action)) {
            $action = 'index';
            self::app()->action = $action;
        }
        if (!method_exists($namespace, $action)) {
            exit('no exists method: ' . $action);
        }
        $controller = new $namespace;
        $controller->modules = $modules;
        $controller->controller = $controllerName;
        $controller->action = $action;
        $controller->$action();
    }

    public static function getUcfirstName($name)
    {
        if (strpos($name, '-')) {
            $names = explode('-', $name);
            $name = '';
            foreach ($names as $v) {
                $name .= ucfirst($v);
            }
        }
        return ucfirst($name);
    }

}

class Component
{
    protected $values;

    public function __set($name, $value)
    {
        return array_key_exists($name, $this->values)
            ? $this->values[$name] : null;
    }

    public function __get($name)
    {
        if (!array_key_exists($name, $this->values)) {
            $this->values[$name] = null;
        }
        return $this->values[$name];
    }
}

class Request extends Component
{
    protected $values;

    public function __construct()
    {
        global $_GPC;
        $this->values = $_GPC;
    }


}

class App extends Component
{
    protected $values;

    public function __construct()
    {
        global $_W;
        $this->values = $_W;
        $this->var = $_W;
    }

}


YunShop::run();

exit;


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
        } elseif (method_exists($plug, $method)) {

            return $plug->$method();
        } else {
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
