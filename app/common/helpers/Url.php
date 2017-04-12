<?php
namespace app\common\helpers;
/**
 * Url生成类
 *
 * User: jan
 * Date: 21/02/2017
 * Time: 18:02
 */
class Url
{
    public static function shopUrl($uri)
    {
        if(empty($uri) && self::isHttp($uri)){
            return $uri;
        }
        $domain = request()->getSchemeAndHttpHost();
        $module = request()->get('m','sz_yi');
        return $domain . '/addons/' . $module . (strpos($uri,'/') === 0 ? '':'/') . $uri;
    }

    /**
     * 生成后台相对Url
     *      路由   api.v1.test.index  为  app/backend/moduels/api/modules/v1/TestController   index
     * @param $route
     * @param array $params
     * @return string
     */
    public static function web($route, $params = [])
    {
        if(empty($route) && self::isHttp($route)){
            return $route;
        }
        $defaultParams = ['c'=>'site','a'=>'entry','m'=>'sz_yi','do'=>rand(1000,9999),'route'=>$route];
        $params = array_merge($defaultParams, $params);

        return  '/web/index.php?'. http_build_query($params);
    }

    /**
     * 生成前台相对Url
     *      路由   api.v1.test.index  为  app/frontend/moduels/api/modules/v1/TestController   index
     * @param $route
     * @param array $params
     * @return string
     */
    public static function app($route, $params = [])
    {
        if(empty($route) && self::isHttp($route)){
            return $route;
        }
        $module = request()->get('m','sz_yi');
        return   '/addons/' . $module . '/#'.$route .  ($params ? '?'.http_build_query($params) : '');
    }

    /**
     *  前端api接口相对Url
     *
     * @param $route
     * @param array $params
     * @return string
     */
    public static function api($route, $params = [])
    {
        if(empty($route) && self::isHttp($route)){
            return $route;
        }
        $defaultParams = ['i'=>\YunShop::app()->uniacid,'route'=>$route];
        $params = array_merge($defaultParams, $params);

        return   '/addons/sz_yi/api.php?'. http_build_query($params);
    }

    /**
     * 生成后台绝对地址
     *  路由   api.v1.test.index  为  app/backend/moduels/api/modules/v1/TestController   index
     *
     * @param $route
     * @param array $params
     * @param string $domain
     * @return string
     */
    public static function absoluteWeb($route, $params = [], $domain = '')
    {
        if(empty($route) && self::isHttp($route)){
            return $route;
        }
        empty($domain) && $domain = request()->getSchemeAndHttpHost();
        return $domain . self::web($route,$params);
    }

    /**
     * 生成前台绝对地址
     *      路由   api.v1.test.index  为  app/frontend/moduels/api/modules/v1/TestController   index
     * @param $route
     * @param array $params
     * @param string $domain
     * @return string
     */
    public static function absoluteApp($route, $params = [], $domain = '')
    {
        if(empty($route) && self::isHttp($route)){
            return $route;
        }
        empty($domain) && $domain = request()->getSchemeAndHttpHost();
        return $domain . self::app($route,$params);
    }

    public static function absoluteApi($route, $params = [], $domain = '')
    {
        if(empty($route) && self::isHttp($route)){
            return $route;
        }
        empty($domain) && $domain = request()->getSchemeAndHttpHost();
        return $domain . self::api($route,$params);
    }

    public static function isHttp($url)
    {
        return (strpos($url,'http://') == 0 || strpos($url,'https://') == 0);
    }
}