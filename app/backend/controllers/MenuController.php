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

class MenuController extends BaseController
{
    public function index()
    {
        $parent_id = 0;
        $childe_switch = $parent_id > 0 ? 1 : 0;

        $menus = Menu::getMenuAllInfo($parent_id, $childe_switch)->get()->toArray();

        if (!empty($menus)) {
            echo '<pre>';print_r($menus);
        } else {
            return $this->errorJson('菜单栏目数据错误');
        }
/*
        return view('setting.menu',[
            'set' => 1
        ]);
        */
    }

    public function add()
    {
        $menu_model = new Menu();

        $data = array(
            'parent_id' => 0,
            'item' => 'shop',
            'name' => '商城管理',
            'url' => 'http://www.shop.com',
            'url_params' => '&v=11',
            'permit' => 1,
            'menu' => 1,
            'icon' => 'icon_shop',
            'status' => 1
        );

        $menu_model->setRawAttributes($data);

        if ($menu_model->save()) {
            $this->message('菜单修改成功', Url::absoluteWeb('menu.index'));
        } else {
            $this->error('菜单修改失败');
        }

        //return view('welcome',compact('model'));
    }

    public function edit()
    {
        $id = \YunShop::request()->id;

        $menu_model = Menu::getMenuInfoById($id)->first();

        $data = array(
            'parent_id' => 2,
            'item' => 'shop',
            'name' => '商城管理',
            'url' => 'http://www.shop.com',
            'url_params' => '&v=11',
            'permit' => 1,
            'menu' => 0,
            'icon' => 'icon_shop'
        );

        $menu_model->setRawAttributes($data);

        if ($menu_model->save()) {
            $this->message('菜单修改成功', Url::absoluteWeb('menu.index'));
        } else {
            $this->error('菜单修改失败');
        }

        //return $this->render('form',[]);
    }

    public function del()
    {
        $id = \YunShop::request()->id;

        $menu_model = Menu::getMenuInfoById($id)->first();

        if (empty($menu_model)) {
            $this->error('菜单不存在');
        }

        if ($menu_model->delete()) {
            $this->message('菜单删除成功', Url::absoluteWeb('menu.index'));
        } else {
            $this->error('菜单删除失败');
        }
    }
}