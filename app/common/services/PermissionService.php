<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 08/03/2017
 * Time: 09:39
 */

namespace app\common\services;


use app\common\models\Menu;
use app\common\models\user\UniAccountUser;
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
        /*if(!$item){
            return false;
        }*/
        if (\Yunshop::isPHPUnit()) {
            return true;
        }
        if (self::isFounder()) {
            return true;
            //todo 临时增加创始人私有管理插件权限
        } elseif (in_array($item, static::founderPermission())) {
            return false;
        }
        if (self::isOwner()) {
            return true;
        }
        if (self::isManager()) {
            return true;
        }
        if (self::checkNoPermission($item) === true) {
            return true;
        }
        return in_array($item, User::userPermissionCache());
    }

    /**
     * 检测是否存在白名单
     * @param $route
     * @return bool
     */
    public static function checkNoPermission($route)
    {
        $noPermissions = \Cache::get('noPermissions');
        if ($noPermissions === null) {
            $noPermissions = self::getNoPermissionList(\Config::get('menu'));
            \Cache::put('noPermissions', $noPermissions);
        }
        if (in_array($route, $noPermissions)) {
            return true;
        }
        return false;
    }


    public static function founderPermission()
    {
        return ['founder_plugins', 'plugins_enable', 'plugins_disable', 'plugins_manage', 'plugins_delete', 'plugins_update', 'shop_upgrade', 'supervisor'];
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
                if (isset($m['child']) && $m['child']) {
                    $noPermissions = array_merge($noPermissions, self::getNoPermissionList($m['child']));
                }
            }
        }
        return $noPermissions;
    }

    /**
     * 是否是创始人
     * @return bool
     */
    public static function isFounder()
    {
        return \YunShop::app()->role === 'founder' && \YunShop::app()->isfounder === true;
    }

    /**
     * 是否是主管理员
     * @return bool
     */
    public static function isOwner()
    {
        return \YunShop::app()->role === 'owner';
    }

    /**
     * 是否是管理员
     * @return bool
     */
    public static function isManager()
    {
        return \YunShop::app()->role === 'manager';
    }

    /**
     * 是否是操作员
     * @return bool
     */
    public static function isOperator()
    {
        return \YunShop::app()->role === 'operator';
    }
}
