<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 08/03/2017
 * Time: 09:39
 */

namespace app\common\services;


use app\common\models\Menu;
use app\common\models\user\User;

class PermissionService
{

    public static function isAuth()
    {
        return \YunShop::app()->uid;
    }

    /**
     * 检测是否有权限
     * @param $item
     * @return bool
     */
    public static function can($item)
    {
        /*
        if(!$item){
            return false;
        }
        */
        if(self::checkNoPermission($item) === true){
            return true;
        }
        if (self::isFounder()) {
            return true;
        }
        return in_array($item, User::getAllPermissions());
    }

    /**
     * 检测是否存在白名单
     * @param $route
     * @return bool
     */
    public static function checkNoPermission($route)
    {
        $noPermissions = \Cache::get('noPermissions');
        if($noPermissions === null){
            $noPermissions = self::getNoPermissionList(\Config::get('menu'));
            \Cache::put('noPermissions',$noPermissions);
        }
        if(in_array($route, $noPermissions)){
            return true;
        }
        return false;
    }

    /**
     * 获取权限白名单
     * @param $menus
     * @return array
     */
    public static function getNoPermissionList($menus)
    {
        $noPermissions = [];
        if ($menus) {
            foreach ($menus as $key => $m) {
                if (!isset($m['permit']) || (isset($m['permit']) && !$m['permit'])) {
                    $noPermissions[] = $key;
                }
                if(isset($m['child']) && $m['child']){
                     $noPermissions = array_merge($noPermissions,self::getNoPermissionList($m['child']));
                }
            }
        }
        return $noPermissions;
    }


    /**
     * 是否是创始人
     * @return mixed
     */
    public static function isFounder()
    {
        return \YunShop::app()->isfounder === true;
    }
}