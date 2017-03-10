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

    /**
     * 生成后台相对Url
     *      路由   api.v1.test.index  为  app/backend/moduels/api/modules/v1/TestController   index
     * @param $route
     * @param array $params
     * @return string
     */
    public static function web($route, $params = [])
    {
        if(empty($route)){
            return $route;
        }
        $defaultParams = ['c'=>'site','a'=>'entry','m'=>'sz_yi','do'=>random(4),'route'=>$route];
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
        if(empty($route)){
            return $route;
        }
        $defaultParams = ['i'=>\YunShop::app()->uniacid,'c'=>'entry','m'=>'sz_yi','do'=>random(4),'route'=>$route];
        $params = array_merge($defaultParams, $params);

        return   '/app/index.php?'. http_build_query($params);
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
        empty($domain) && $domain = request()->getSchemeAndHttpHost();
        return $domain . self::app($route,$params);
    }
}