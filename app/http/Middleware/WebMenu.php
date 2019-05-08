<?php

namespace app\Http\Middleware;

use app\common\models\Menu;
use app\common\services\PermissionService;
use Closure;
use Illuminate\Support\Facades\Config;

class WebMenu
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws \app\common\exceptions\ShopException
     */
    public function handle($request, Closure $next)
    {
        //菜单生成
        $menu_array = Config::get('app.menu');
        if (!\Cache::has('db_menu')) {
            $dbMenu = Config::get($menu_array['main_menu']);//$dbMenu = Menu::getMenuList();
            \Cache::put('db_menu', $dbMenu, 3600);
        } else {
            $dbMenu = \Cache::get('db_menu');
        }

        $menuList = array_merge($dbMenu, (array)Config::get($menu_array['plugins_menu']));
        //兼容旧插件使用
        $menuList = array_merge($menuList, (array)Config::get($menu_array['old_plugin_menu']));

        $menuList['system']['child'] = array_merge($menuList['system']['child'], (array)Config::get($menu_array['founder_menu']));

        Config::set('menu', $menuList);

        $item = Menu::getCurrentItemByRoute($request->input('route'), $menuList);

        \YunShop::$currentItems = array_merge(Menu::getCurrentMenuParents($item, $menuList), [$item]);
        Config::set('currentMenuItem', $item);
        //检测权限
        if (!PermissionService::can($item)) {
            $exception = new \app\common\exceptions\ShopException('Sorry,您没有操作无权限，请联系管理员!');
            $exception->setRedirect(yzWebUrl('index.index'));
            throw $exception;
        }
        return $next($request);
    }
}
