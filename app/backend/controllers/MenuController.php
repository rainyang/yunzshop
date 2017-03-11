<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 09/03/2017
 * Time: 11:00
 */

namespace app\backend\controllers;

use app\backend\models\Menu;
use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\helpers\PaginationHelper;

class MenuController extends BaseController
{
    private $pageSize = 20;

    public function index()
    {
        $menus = Menu::getMenuAllInfo()
                     ->paginate($this->pageSize)
                      ->toArray();

        $pager = PaginationHelper::show($menus['total'], $menus['current_page'], $this->pageSize);

        if (empty($menus)) {
            return $this->errorJson('菜单栏目数据错误');
        }

        return view('setting.menu',[
            'pager' => $pager,
            'menus' => $menus
        ])->render();

    }

    public function add()
    {
        $menu_model = new Menu();

        $data = \YunShop::request()->menu;
        $id = \YunShop::request()->id;

        if (!empty($data)) {
            $menu_model->setRawAttributes($data);

            $validator = Menu::validator($menu_model->getAttributes());

            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($menu_model->save()) {
                    return $this->message('菜单修改成功', Url::absoluteWeb('menu.index'));
                } else {
                    $this->error('菜单修改失败');
                }
            }
        }

        if (!empty($id)) {
            $menu_model = Menu::getMenuInfoById($id)->first()->toArray();
        }

        return view('setting.menu_add',[
            'menu' => $menu_model
        ])->render();
    }

    public function edit()
    {
        $id = \YunShop::request()->id;

        $menu_model = Menu::getMenuInfoById($id)->first();

        $data = \YunShop::request()->menu;

        if (!empty($data)) {
            $menu_model->setRawAttributes($data);

            $validator = Menu::validator($menu_model->getAttributes());

            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($menu_model->save()) {
                    return $this->message('菜单修改成功', Url::absoluteWeb('menu.index'));
                } else {
                    $this->error('菜单修改失败');
                }
            }
        }

        return view('setting.menu_info',[
            'menu' => $menu_model
        ])->render();
    }

    public function del()
    {
        $id = \YunShop::request()->id;

        $menu_model = Menu::getMenuInfoById($id)->first();

        if (empty($menu_model)) {
            $this->error('菜单不存在');
        }

        if ($menu_model->delete()) {
            return $this->message('菜单删除成功', Url::absoluteWeb('menu.index'));
        } else {
            $this->error('菜单删除失败');
        }
    }

    public function getJsonData()
    {
        $id = \YunShop::request()->id ? \YunShop::request()->id : 0;;

        $menu_model = Menu::getMenuAllInfo($id,1)->get();

        if (empty($menu_model)) {
            return $this->errorJson('数据不存在');
        }

        return $this->successJson('', $menu_model->toJson());
    }
}