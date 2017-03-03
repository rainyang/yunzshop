<?php

//商城根目录
define('SHOP_ROOT', dirname(__FILE__));

class YunShop
{
    private static $_req;
    private static $_app;

    public function __construct()
    {

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
        $path = dirname(__FILE__) ;
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
            return new YunRequest();
        }
    }

    public static function app()
    {
        if (self::$_app !== null) {
            return self::$_app;
        } else {
            return new YunApp();
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
                    $path = $controllerFile;
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
        $content = $controller->$action(
           Illuminate\Http\Request::capture()
        );
        exit($content);
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

class YunComponent
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

    public function set($name, $value){
        $this->values[$name] = $value;
        return $this;
    }

    public function get(){
        return $this->values;
    }
}

class YunRequest extends YunComponent
{
    protected $values;

    public function __construct()
    {
        global $_GPC;
        $this->values = $_GPC;
    }
}

class YunApp extends YunComponent
{
    protected $values;

    public function __construct()
    {
        global $_W;
        $this->values = $_W;
        //$this->var = $_W;
    }

}
