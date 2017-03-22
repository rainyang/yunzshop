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
use Ixudra\Curl\Facades\Curl;

class MenuController extends BaseController
{

    public function index()
    {

        $menu = new Menu();
        $menuList = $menu->getDescendants(0);

        return view('menu.index', [
            'menuList' => $menuList
        ])->render();

    }

    public function add()
    {
        $model = new Menu();

        $parentId = intval(\YunShop::request()->parent_id);
        $data = \YunShop::request()->menu;

        if ($data) {
            $model->fill($data);

            $validator = $model->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($model->save()) {
                    return $this->message('添加菜单成功', Url::absoluteWeb('menu.index'));
                } else {
                    $this->error('添加菜单失败');
                }
            }
        }

        $parentId && $model->setAttribute('parent_id',$parentId);
        $parentMenu = [0=>'请选择上级'] + $model->toSelectArray(0);

        return view('menu.form', [
            'parentMenu' => $parentMenu,
            'model' => $model
        ])->render();
    }

    public function edit()
    {
        $id = \YunShop::request()->id;
        $data = \YunShop::request()->menu;

        $model = Menu::getMenuInfoById($id);
        if(!$model){
            return $this->message('无此记录','','error');
        }

        if ($data) {
            $model->fill($data);

            $validator = $model->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($model->save()) {
                    return $this->message('菜单修改成功', Url::absoluteWeb('menu.index'));
                } else {
                    $this->error('菜单修改失败');
                }
            }
        }

        $parentMenu = [0=>'请选择上级'] + $model->toSelectArray(0);

        return view('menu.form', [
            'model' => $model,
            'parentMenu' => $parentMenu,
        ])->render();
    }

    public function del()
    {
        $id = \YunShop::request()->id;

        $model = Menu::getMenuInfoById($id);
        if (empty($model)) {
            return $this->message('菜单不存在','','error');
        }
        if($model->childs->count()>0){
            return $this->message('存在子菜单不可删除','','error');
        }

        if ($model->delete()) {
            return $this->message('菜单删除成功', Url::absoluteWeb('menu.index'));
        } else {
            $this->error('菜单删除失败');
        }
    }

    public function getRemoteUpdate()
    {
        $url = "http://test.yunzshop.com/app/index.php?i=2&c=entry&a=shop&m=sz_yi&do=FO9H&route=menu.to-list";
        $responseData = Curl::to($url)->get();

        if($responseData){
            $data = json_decode($responseData);
            if($data->data && $menu = objectArray($data->data)){
                try {
                    (new Menu())->where('id','>',0)->forceDelete();
                    foreach($menu as $v){
                        Menu::create($v);
                    }
                    //菜单生成
                    \Config::set('menu',Menu::getMenuList());

                }catch (\Exception $e){
                     throw new \Exception($e);
                }
            }
        }
        return $this->message('更新远程菜单成功');
    }

}