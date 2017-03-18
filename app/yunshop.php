<?php

use Illuminate\Support\Str;
use app\common\services\PermissionService;
use app\backend\models\Menu;

//商城根目录
define('SHOP_ROOT', dirname(__FILE__));

class YunShop
{
    private static $_req;
    private static $_app;

    public function __construct()
    {

    }

    public static function run($namespace,$modules,$controllerName, $action, $currentRoutes)
    {

        //检测命名空间
        if (!class_exists($namespace)) {
            abort(404," 不存在命名空间: " . $namespace);
        }

        //检测controller继承
        $controller = new $namespace;
        if(!$controller instanceof \app\common\components\BaseController){
            abort(404,'不存在控制器:' . $namespace);
        }

        //设置默认方法
        if (empty($action)) {
            $action = 'index';
            self::app()->action = $action;
            $currentRoutes[] = $action;
        }

        //检测方法是否存在并可执行
        if (!method_exists($namespace, $action) || !is_callable([$namespace, $action]) ) {
            abort(404,'操作方法不存在: ' . $action);
        }

        $controller->modules = $modules;
        $controller->controller = $controllerName;
        $controller->action = $action;
        $controller->route = implode('.',$currentRoutes);

        //菜单生成
        \Config::set('menu',Menu::getMenuList());

        //检测权限
        if(self::isWeb() && !PermissionService::can($controller->route)){
            abort(403,'无权限');
        }
        //设置uniacid
        Setting::$uniqueAccountId = self::app()->uniacid;
        //执行方法
        $content = $controller->$action(
            Illuminate\Http\Request::capture()
        );

        exit($content);
    }


    /**
     * Configures an object with the initial property values.
     * @param object $object the object to be configured
     * @param array $properties the property initial values given in terms of name-value pairs.
     * @return object the object itself
     */
    public static function configure($object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
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
        $currentRoutes = [];
        $modules = [];
        if ($routes) {
            $length = count($routes);
            $routeFirst = array_first($routes);
            $countRoute = count($routes);
            if($routeFirst === 'plugin'){
                $currentRoutes[] = $routeFirst;
                $namespace = 'Yunshop';
                 array_shift($routes);
                $pluginName = array_shift($routes);
                if($pluginName || plugin($pluginName)) {
                    $currentRoutes[] = $pluginName;
                    $namespace .=  '\\'.ucfirst(Str::camel($pluginName));
                    $path = base_path() . '/plugins/'. $pluginName . '/src';
                    foreach ($routes as $k => $r) {
                        $ucFirstRoute = ucfirst(Str::camel($r));
                        $controllerFile = $path . '/'  . $ucFirstRoute . 'Controller.php';
                        if (is_file($controllerFile)) {
                            $namespace .= '\\' . $ucFirstRoute . 'Controller';
                            $controllerName = $ucFirstRoute;
                            $path = $controllerFile;
                            $currentRoutes[] = $r;
                        }elseif(is_dir($path .= '/'.$r)){
                            $namespace .=  '\\'.$r;
                            $modules[] = $r;
                            $currentRoutes[] = $r;
                        }else{
                            if ($countRoute !== $k + 3) {
                                exit('no found route:' . self::request()->route);
                            }
                            $action = strpos($r, '-') === false ? $r : Str::camel($r);
                            $currentRoutes[] = $r;
                        }
                    }
                }else{
                    abort(404,'无此插件');
                }
            }else{
                foreach ($routes as $k => $r) {
                    $ucFirstRoute = ucfirst(Str::camel($r));
                    $controllerFile = $path . '/controllers/' . $ucFirstRoute . 'Controller.php';
                    if (is_file($controllerFile)) {
                        $namespace .= '\\controllers\\' . $ucFirstRoute . 'Controller';
                        $controllerName = $ucFirstRoute;
                        $path = $controllerFile;
                        $currentRoutes[] = $r;
                    } elseif (is_dir($path .= '/modules/' . $r)) {
                        $namespace .= '\\modules\\' . $r;
                        $modules[] = $r;
                        $currentRoutes[] = $r;
                    } else {
                        if ($length !== $k + 1) {
                            exit('no found route:' . self::request()->route);
                        }
                        $action = strpos($r, '-') === false ? $r : Str::camel($r);
                        $currentRoutes[] = $r;
                    }

                }
            }


        }
        //执行run
        static::run($namespace,$modules,$controllerName, $action, $currentRoutes);

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
    protected $values = [];

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

    public function get($key=null){
        if(isset($key)){
            return array_get($this->values,$key,null);
        }
        return $this->values;
    }
}

class YunRequest extends YunComponent implements ArrayAccess
{

    public function __construct()
    {
        global $_GPC;
        $this->values = $_GPC;
    }
    public function offsetUnset($offset){
        unset($this->values[$offset]);
    }
    public function offsetSet($offset, $value){
        $this->values[$offset] = $value;
    }
    public function offsetGet($offset){
        if(isset($this->values[$offset])){
            return $this->values[$offset];
        }
        return null;
    }
    public function offsetExists($offset){
        if(isset($this->values[$offset])){
            return true;
        }
        return false;
    }

}

class YunApp extends YunComponent
{
    protected $values;
    protected $routeList;

    public function __construct()
    {
        global $_W;
        $this->values = $_W;
        //$this->var = $_W;
        $this->routeList = Config::get('menu');
    }

    /**
     * 通过子路由获取交路径
     * @return mixed
     */
    public function getRoutes()
    {
        $key = 'routes-child-parent';
        $routes = \Cache::get($key);
        if($routes === null){
            $routes = $this->allRoutes();
            \Cache::put($key,$routes,36000);
        }

        return $routes;
    }

    protected function allRoutes($list = [],$parent = [])
    {
        $routes = [];
        !$list && $list = $this->routeList;
        if($list){
            foreach ($list as $k=>$v){
                $temp = $v;
                if(isset($temp['child']))  unset($temp['child']);
                if(isset($v['url'])) {
                    $routes[$v['url']] = array_merge($temp, ['parent' => $parent]);
                    if (isset($v['child']) && $v['child']) {
                        $routes = array_merge($routes,
                            $this->allRoutes($v['child'], array_merge($parent, $routes[$v['url']])));
                    }
                }
            }
        }

        return $routes;
    }

    /**
     * @todo set member id from session
     * @return int
     */
    public function getMemberId()
    {
        return session('member_id', 0);
    }



}
